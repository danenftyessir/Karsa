<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * controller untuk browse problems dengan sistem filter yang telah diperbaiki
 * menggunakan PostgreSQL JSON operators untuk filter SDG yang akurat
 */
class BrowseProblemsController extends Controller
{
    /**
     * tampilkan halaman browse problems dengan filter yang telah di-refactor
     */
    public function index(Request $request)
    {
        // query base dengan eager loading
        $query = Problem::query()
            ->select([
                'id', 'institution_id', 'province_id', 'regency_id', 'title', 
                'description', 'status', 'application_deadline', 'required_students',
                'difficulty_level', 'duration_months', 'sdg_categories', 'is_featured',
                'is_urgent', 'views_count', 'created_at'
            ])
            ->where('status', 'open');

        // filter 1: pencarian teks (title dan description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        // filter 2: lokasi - province
        if ($request->filled('province_id')) {
            $query->where('province_id', $request->province_id);
        }

        // filter 3: lokasi - regency (hanya jika province dipilih)
        if ($request->filled('regency_id')) {
            $query->where('regency_id', $request->regency_id);
        }

        // filter 4: tingkat kesulitan
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // ✅ FILTER SDG BARU - REFACTORED DARI NOL
        // menggunakan PostgreSQL JSON contains operator (@>) untuk akurasi sempurna
        // mendukung multiple selection (user bisa pilih lebih dari 1 SDG)
        if ($request->filled('sdg_categories')) {
            $sdgCategories = $request->sdg_categories;
            
            // pastikan input adalah array
            if (!is_array($sdgCategories)) {
                $sdgCategories = [$sdgCategories];
            }
            
            // convert semua ke integer untuk keamanan
            $sdgCategories = array_map('intval', array_filter($sdgCategories));
            
            if (!empty($sdgCategories)) {
                // gunakan whereJsonContains untuk setiap SDG yang dipilih
                // dengan OR logic - problem akan muncul jika punya SALAH SATU dari SDG yang dipilih
                $query->where(function($q) use ($sdgCategories) {
                    foreach ($sdgCategories as $sdg) {
                        $q->orWhereJsonContains('sdg_categories', $sdg);
                    }
                });
            }
        }

        // filter 5: durasi proyek (dalam bulan)
        if ($request->filled('duration')) {
            $duration = $request->duration;
            
            switch ($duration) {
                case '1-2':
                    $query->whereBetween('duration_months', [1, 2]);
                    break;
                case '3-4':
                    $query->whereBetween('duration_months', [3, 4]);
                    break;
                case '5-6':
                    $query->whereBetween('duration_months', [5, 6]);
                    break;
                case '6+':
                    $query->where('duration_months', '>', 6);
                    break;
            }
        }

        // filter 6: status urgent/featured
        if ($request->filled('is_urgent') && $request->is_urgent == '1') {
            $query->where('is_urgent', true);
        }

        if ($request->filled('is_featured') && $request->is_featured == '1') {
            $query->where('is_featured', true);
        }

        // sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'deadline':
                // urutkan berdasarkan deadline terdekat
                $query->orderBy('application_deadline', 'asc');
                break;
            case 'popular':
                // urutkan berdasarkan views terbanyak
                $query->orderBy('views_count', 'desc');
                break;
            case 'oldest':
                // urutkan berdasarkan paling lama dibuat
                $query->orderBy('created_at', 'asc');
                break;
            case 'latest':
            default:
                // urutkan berdasarkan paling baru dibuat (default)
                $query->orderBy('created_at', 'desc');
                break;
        }

        // hitung total results sebelum pagination
        $totalProblems = (clone $query)->count();

        // eager load relationships untuk performa
        $query->with([
            'institution:id,name,type,logo_path',
            'province:id,name',
            'regency:id,name,province_id',
            'images' => function($imageQuery) {
                $imageQuery->select('id', 'problem_id', 'image_path', 'order')
                          ->orderBy('order')
                          ->limit(1);
            }
        ]);

        // get ALL problems for map view (before pagination)
        $allProblems = (clone $query)->get();

        // pagination dengan query string preserved
        $problems = $query->paginate(12)->withQueryString();

        // data untuk dropdown filter
        $provinces = Province::orderBy('name')->get(['id', 'name']);

        // regencies hanya dimuat jika province dipilih
        $regencies = [];
        if ($request->filled('province_id')) {
            $regencies = Regency::where('province_id', $request->province_id)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        // total SDG categories (1-17)
        $sdgCategories = 17;

        return view('student.browse-problems.index', compact(
            'problems',
            'allProblems',
            'provinces',
            'regencies',
            'totalProblems',
            'sdgCategories'
        ));
    }

    /**
     * tampilkan detail problem
     */
    public function show($id)
    {
        $problem = Problem::with([
            'institution',
            'province',
            'regency',
            'images' => function($query) {
                $query->orderBy('order');
            }
        ])->findOrFail($id);

        // increment views count
        $problem->increment('views_count');

        // cek apakah user sudah apply
        $hasApplied = false;
        if (Auth::check() && Auth::user()->isStudent() && Auth::user()->student) {
            $hasApplied = Auth::user()->student->applications()
                ->where('problem_id', $problem->id)
                ->exists();
        }

        // cek apakah user sudah wishlist
        $isWishlisted = false;
        if (Auth::check() && Auth::user()->isStudent() && Auth::user()->student) {
            $isWishlisted = Auth::user()->student->hasWishlisted($problem->id);
        }

        // ✅ SIMILAR PROBLEMS - REFACTORED
        // cari problem serupa berdasarkan lokasi ATAU SDG categories
        $similarProblems = Problem::where('id', '!=', $problem->id)
            ->where('status', 'open')
            ->where(function($query) use ($problem) {
                // cari problem di province yang sama
                $query->where('province_id', $problem->province_id);
                
                // ATAU problem dengan SDG category yang overlap
                $sdgCategories = $problem->sdg_categories;
                
                // convert ke array jika perlu
                if (is_string($sdgCategories)) {
                    $sdgCategories = json_decode($sdgCategories, true) ?? [];
                }
                
                // pastikan array dan tidak kosong
                if (is_array($sdgCategories) && count($sdgCategories) > 0) {
                    $query->orWhere(function($q) use ($sdgCategories) {
                        foreach ($sdgCategories as $sdg) {
                            $q->orWhereJsonContains('sdg_categories', $sdg);
                        }
                    });
                }
            })
            ->with([
                'institution',
                'province',
                'regency',
                'images' => function($query) {
                    $query->orderBy('order')->limit(1);
                }
            ])
            ->limit(3)
            ->get();

        return view('student.browse-problems.detail', compact(
            'problem',
            'hasApplied',
            'isWishlisted',
            'similarProblems'
        ));
    }

    /**
     * get regencies by province (API endpoint untuk filter dinamis)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegencies(Request $request)
    {
        $provinceId = $request->province_id;
        
        if (!$provinceId) {
            return response()->json([]);
        }

        $regencies = Regency::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($regencies);
    }
}