<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Problem;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * controller untuk manajemen problems oleh institution
 */
class ProblemController extends Controller
{
    /**
     * tampilkan daftar problems yang dibuat institution
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;
        
        $query = Problem::where('institution_id', $institution->id)
                       ->with(['province', 'regency', 'images']);
        
        // filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // sorting
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title':
                $query->orderBy('title');
                break;
            case 'applications':
                $query->orderBy('applications_count', 'desc');
                break;
            case 'views':
                $query->orderBy('views_count', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $problems = $query->paginate(12)->withQueryString();
        
        // statistik
        $stats = [
            'total' => Problem::where('institution_id', $institution->id)->count(),
            'draft' => Problem::where('institution_id', $institution->id)->where('status', 'draft')->count(),
            'open' => Problem::where('institution_id', $institution->id)->where('status', 'open')->count(),
            'in_progress' => Problem::where('institution_id', $institution->id)->where('status', 'in_progress')->count(),
            'completed' => Problem::where('institution_id', $institution->id)->where('status', 'completed')->count(),
            'closed' => Problem::where('institution_id', $institution->id)->where('status', 'closed')->count(),
        ];
        
        return view('institution.problems.index', compact('problems', 'stats'));
    }
    
    /**
     * tampilkan form create problem
     */
    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        return view('institution.problems.create', compact('provinces'));
    }
    
    /**
     * simpan problem baru
     * FIX: handle array conversion untuk required_skills dan sdg_categories
     */
    public function store(Request $request)
    {
        try {
            Log::info('Problem Store - Request Data', [
                'all_data' => $request->except(['images']),
                'has_images' => $request->hasFile('images')
            ]);

            // preprocessing: convert string ke array untuk required_skills jika perlu
            $requestData = $request->all();
            
            // handle required_skills: jika string dengan koma, split jadi array
            if (isset($requestData['required_skills'])) {
                if (is_string($requestData['required_skills'])) {
                    $requestData['required_skills'] = array_filter(
                        array_map('trim', explode(',', $requestData['required_skills']))
                    );
                } elseif (!is_array($requestData['required_skills'])) {
                    $requestData['required_skills'] = [];
                }
                // remove empty values
                $requestData['required_skills'] = array_values(array_filter($requestData['required_skills']));
            }

            // handle required_majors: sama seperti required_skills
            if (isset($requestData['required_majors'])) {
                if (is_string($requestData['required_majors']) && !empty($requestData['required_majors'])) {
                    $requestData['required_majors'] = array_filter(
                        array_map('trim', explode(',', $requestData['required_majors']))
                    );
                } elseif (!is_array($requestData['required_majors'])) {
                    $requestData['required_majors'] = [];
                }
                $requestData['required_majors'] = array_values(array_filter($requestData['required_majors']));
            }

            // handle deliverables: clean up array
            if (isset($requestData['deliverables']) && is_array($requestData['deliverables'])) {
                $requestData['deliverables'] = array_values(array_filter($requestData['deliverables']));
            }

            // handle facilities_provided: clean up array
            if (isset($requestData['facilities_provided']) && is_array($requestData['facilities_provided'])) {
                $requestData['facilities_provided'] = array_values(array_filter($requestData['facilities_provided']));
            }

            // handle sdg_categories: convert ke integer array
            if (isset($requestData['sdg_categories']) && is_array($requestData['sdg_categories'])) {
                $requestData['sdg_categories'] = array_values(array_map('intval', $requestData['sdg_categories']));
            }

            // replace request dengan data yang sudah diprocess
            $request->merge($requestData);

            Log::info('Problem Store - After Preprocessing', [
                'required_skills' => $requestData['required_skills'] ?? null,
                'required_majors' => $requestData['required_majors'] ?? null,
                'sdg_categories' => $requestData['sdg_categories'] ?? null,
            ]);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'background' => 'nullable|string',
                'objectives' => 'nullable|string',
                'scope' => 'nullable|string',
                'province_id' => 'required|exists:provinces,id',
                'regency_id' => 'required|exists:regencies,id',
                'village' => 'nullable|string|max:255',
                'detailed_location' => 'nullable|string',
                'sdg_categories' => 'required|array|min:1',
                'sdg_categories.*' => 'integer|between:1,17',
                'required_students' => 'required|integer|min:1',
                'required_skills' => 'required|array|min:1',
                'required_majors' => 'nullable|array',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after:start_date',
                'application_deadline' => 'required|date|after_or_equal:today|before:start_date',
                'duration_months' => 'required|integer|min:1',
                'difficulty_level' => 'required|in:beginner,intermediate,advanced',
                'status' => 'required|in:draft,open',
                'expected_outcomes' => 'nullable|string',
                'deliverables' => 'nullable|array',
                'facilities_provided' => 'nullable|array',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            ], [
                'start_date.after_or_equal' => 'Tanggal mulai proyek tidak boleh di masa lalu.',
                'application_deadline.after_or_equal' => 'Deadline aplikasi tidak boleh di masa lalu.',
                'application_deadline.before' => 'Deadline aplikasi harus sebelum tanggal mulai proyek.',
                'end_date.after' => 'Tanggal selesai proyek harus setelah tanggal mulai.',
            ]);

            Log::info('Problem Store - Validation Passed', ['validated' => $validated]);

            DB::beginTransaction();

            $validated['institution_id'] = auth()->user()->institution->id;
            
            $problem = Problem::create($validated);

            Log::info('Problem Store - Problem Created', ['problem_id' => $problem->id]);
            
            // upload images jika ada (FIX: gunakan disk supabase dengan fallback)
            if ($request->hasFile('images')) {
                // Log disk configuration untuk debugging
                Log::info('Problem Store - Disk Configuration', [
                    'default_disk' => config('filesystems.default'),
                    'supabase_endpoint' => config('filesystems.disks.supabase.endpoint'),
                    'supabase_bucket' => config('filesystems.disks.supabase.bucket'),
                    'supabase_region' => config('filesystems.disks.supabase.region'),
                ]);

                foreach ($request->file('images') as $index => $image) {
                    try {
                        Log::info('Problem Store - Attempting Image Upload', [
                            'index' => $index,
                            'original_name' => $image->getClientOriginalName(),
                            'mime_type' => $image->getMimeType(),
                            'size' => $image->getSize(),
                            'problem_id' => $problem->id
                        ]);

                        // Try upload to Supabase
                        try {
                            Log::info('Problem Store - Starting Supabase Upload', [
                                'file' => $image->getClientOriginalName(),
                                'size' => $image->getSize(),
                                'mime' => $image->getMimeType(),
                                'bucket' => config('filesystems.disks.supabase.bucket'),
                                'endpoint' => config('filesystems.disks.supabase.endpoint')
                            ]);

                            $path = $image->store('problems', 'supabase');

                            if (!$path) {
                                throw new \Exception("Store method returned false/empty path");
                            }

                            Log::info('Problem Store - Image Stored to Supabase Successfully', [
                                'path' => $path,
                                'problem_id' => $problem->id,
                                'url' => supabase_url($path)
                            ]);

                        } catch (\Exception $supabaseException) {
                            Log::error('Problem Store - Supabase Upload Failed', [
                                'error' => $supabaseException->getMessage(),
                                'file' => $image->getClientOriginalName(),
                                'trace' => $supabaseException->getTraceAsString()
                            ]);

                            // Di production, jangan fallback ke local storage (ephemeral)
                            if (app()->environment('production')) {
                                throw new \Exception("Gagal upload gambar ke Supabase: " . $supabaseException->getMessage());
                            }

                            // Fallback ke public disk HANYA di local/development
                            Log::warning('Problem Store - Using Public Disk Fallback (Development Only)', [
                                'file' => $image->getClientOriginalName()
                            ]);

                            $path = $image->store('problems', 'public');

                            if (!$path) {
                                throw new \Exception("Fallback upload juga gagal. Error: " . $supabaseException->getMessage());
                            }

                            Log::info('Problem Store - Image Stored to Public Disk (Fallback)', [
                                'path' => $path
                            ]);
                        }

                        $problem->images()->create([
                            'problem_id' => $problem->id,
                            'image_path' => $path,
                            'order' => $index + 1,
                            'is_cover' => $index === 0, // image pertama jadi cover
                        ]);

                        Log::info('Problem Store - Image Record Created', [
                            'path' => $path,
                            'order' => $index + 1
                        ]);

                    } catch (\Exception $imgException) {
                        Log::error('Problem Store - Image Upload Exception', [
                            'index' => $index,
                            'error' => $imgException->getMessage(),
                            'trace' => $imgException->getTraceAsString()
                        ]);
                        throw new \Exception("Error upload gambar ke-" . ($index + 1) . ": " . $imgException->getMessage());
                    }
                }
            }

            DB::commit();

            Log::info('Problem Store - Success', ['problem_id' => $problem->id]);
            
            return redirect()
                ->route('institution.problems.show', $problem->id)
                ->with('success', 'Problem Berhasil Dibuat!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Problem Store - Validation Error', [
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Problem Store - Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat menyimpan problem: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * tampilkan detail problem
     */
    public function show($id)
    {
        $problem = Problem::where('institution_id', auth()->user()->institution->id)
                         ->with(['province', 'regency', 'images', 'applications'])
                         ->findOrFail($id);
        
        return view('institution.problems.show', compact('problem'));
    }
    
    /**
     * tampilkan form edit problem
     */
    public function edit($id)
    {
        $problem = Problem::where('institution_id', auth()->user()->institution->id)
                         ->with(['images'])
                         ->findOrFail($id);
        
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::where('province_id', $problem->province_id)
                           ->orderBy('name')
                           ->get();
        
        return view('institution.problems.edit', compact('problem', 'provinces', 'regencies'));
    }
    
    /**
     * update problem
     * FIX: handle array conversion dan validation lengkap seperti store
     */
    public function update(Request $request, $id)
    {
        // fetch problem dengan authorization check
        $problem = Problem::where('institution_id', auth()->user()->institution->id)
                         ->findOrFail($id);

        try {
            Log::info('Problem Update - Request Data', [
                'problem_id' => $problem->id,
                'all_data' => $request->except(['images', 'delete_images']),
                'has_images' => $request->hasFile('images')
            ]);

            // preprocessing: convert string ke array untuk required_skills jika perlu (sama seperti store)
            $requestData = $request->all();

            // handle required_skills: jika string dengan koma, split jadi array
            if (isset($requestData['required_skills'])) {
                if (is_string($requestData['required_skills'])) {
                    $requestData['required_skills'] = array_filter(
                        array_map('trim', explode(',', $requestData['required_skills']))
                    );
                } elseif (!is_array($requestData['required_skills'])) {
                    $requestData['required_skills'] = [];
                }
                // remove empty values
                $requestData['required_skills'] = array_values(array_filter($requestData['required_skills']));
            }

            // handle required_majors: sama seperti required_skills
            if (isset($requestData['required_majors'])) {
                if (is_string($requestData['required_majors']) && !empty($requestData['required_majors'])) {
                    $requestData['required_majors'] = array_filter(
                        array_map('trim', explode(',', $requestData['required_majors']))
                    );
                } elseif (!is_array($requestData['required_majors'])) {
                    $requestData['required_majors'] = [];
                }
                $requestData['required_majors'] = array_values(array_filter($requestData['required_majors']));
            }

            // handle deliverables: clean up array
            if (isset($requestData['deliverables']) && is_array($requestData['deliverables'])) {
                $requestData['deliverables'] = array_values(array_filter($requestData['deliverables']));
            }

            // handle facilities_provided: clean up array
            if (isset($requestData['facilities_provided']) && is_array($requestData['facilities_provided'])) {
                $requestData['facilities_provided'] = array_values(array_filter($requestData['facilities_provided']));
            }

            // handle sdg_categories: convert ke integer array
            if (isset($requestData['sdg_categories']) && is_array($requestData['sdg_categories'])) {
                $requestData['sdg_categories'] = array_values(array_map('intval', $requestData['sdg_categories']));
            }

            // replace request dengan data yang sudah diprocess
            $request->merge($requestData);

            Log::info('Problem Update - After Preprocessing', [
                'required_skills' => $requestData['required_skills'] ?? null,
                'required_majors' => $requestData['required_majors'] ?? null,
                'sdg_categories' => $requestData['sdg_categories'] ?? null,
            ]);

            // validation lengkap sesuai dengan form edit
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'background' => 'nullable|string',
                'objectives' => 'nullable|string',
                'scope' => 'nullable|string',
                'province_id' => 'required|exists:provinces,id',
                'regency_id' => 'required|exists:regencies,id',
                'village' => 'nullable|string|max:255',
                'detailed_location' => 'nullable|string',
                'sdg_categories' => 'required|array|min:1',
                'sdg_categories.*' => 'integer|between:1,17',
                'required_students' => 'required|integer|min:1',
                'required_skills' => 'required|array|min:1',
                'required_majors' => 'nullable|array',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after:start_date',
                'application_deadline' => 'required|date|after_or_equal:today|before:start_date',
                'duration_months' => 'required|integer|min:1',
                'difficulty_level' => 'required|in:beginner,intermediate,advanced',
                'status' => 'required|in:draft,open,closed',
                'expected_outcomes' => 'nullable|string',
                'deliverables' => 'nullable|array',
                'facilities_provided' => 'nullable|array',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
                'delete_images' => 'nullable|array',
                'delete_images.*' => 'exists:problem_images,id',
            ], [
                'start_date.after_or_equal' => 'Tanggal mulai proyek tidak boleh di masa lalu.',
                'application_deadline.after_or_equal' => 'Deadline aplikasi tidak boleh di masa lalu.',
                'application_deadline.before' => 'Deadline aplikasi harus sebelum tanggal mulai proyek.',
                'end_date.after' => 'Tanggal selesai proyek harus setelah tanggal mulai.',
            ]);

            Log::info('Problem Update - Validation Passed', ['validated_keys' => array_keys($validated)]);

            DB::beginTransaction();

            // Update data problem (exclude images dan delete_images)
            $problem->update($request->except(['_token', '_method', 'images', 'delete_images']));

            // --- Logika untuk Gambar ---

            // 1. Hapus Gambar yang Ditandai untuk Dihapus
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $imageId) {
                    $imageToDelete = $problem->images()->find($imageId);
                    if ($imageToDelete) {
                        $imagePath = $imageToDelete->image_path;

                        // Coba hapus dari public disk dulu (untuk file lama)
                        try {
                            if (Storage::disk('public')->exists($imagePath)) {
                                Storage::disk('public')->delete($imagePath);
                                Log::info('Problem Update - Image Deleted from Public Disk', [
                                    'image_id' => $imageId,
                                    'path' => $imagePath
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::warning('Problem Update - Failed to delete from public disk', [
                                'error' => $e->getMessage()
                            ]);
                        }

                        // Coba hapus dari supabase disk (untuk file baru)
                        try {
                            if (Storage::disk('supabase')->exists($imagePath)) {
                                Storage::disk('supabase')->delete($imagePath);
                                Log::info('Problem Update - Image Deleted from Supabase Disk', [
                                    'image_id' => $imageId,
                                    'path' => $imagePath
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::warning('Problem Update - Failed to delete from supabase disk', [
                                'error' => $e->getMessage()
                            ]);
                        }

                        // Hapus dari database
                        $imageToDelete->delete();
                        Log::info('Problem Update - Image Record Deleted', ['image_id' => $imageId]);
                    }
                }
            }

            // 2. Upload Gambar Baru
            if ($request->hasFile('images')) {
                // Refresh relation setelah delete dan hitung existing images
                $problem->load('images');
                $currentImageCount = $problem->images->count();

                // Log disk configuration untuk debugging
                Log::info('Problem Update - Disk Configuration', [
                    'default_disk' => config('filesystems.default'),
                    'supabase_endpoint' => config('filesystems.disks.supabase.endpoint'),
                    'supabase_bucket' => config('filesystems.disks.supabase.bucket'),
                    'supabase_region' => config('filesystems.disks.supabase.region'),
                ]);

                foreach ($request->file('images') as $index => $image) {
                    try {
                        Log::info('Problem Update - Attempting Image Upload', [
                            'index' => $index,
                            'original_name' => $image->getClientOriginalName(),
                            'mime_type' => $image->getMimeType(),
                            'size' => $image->getSize(),
                            'problem_id' => $problem->id
                        ]);

                        // Try upload to Supabase
                        try {
                            Log::info('Problem Update - Starting Supabase Upload', [
                                'file' => $image->getClientOriginalName(),
                                'size' => $image->getSize(),
                                'mime' => $image->getMimeType(),
                                'bucket' => config('filesystems.disks.supabase.bucket'),
                                'endpoint' => config('filesystems.disks.supabase.endpoint')
                            ]);

                            $path = $image->store('problems', 'supabase');

                            if (!$path) {
                                throw new \Exception("Store method returned false/empty path");
                            }

                            Log::info('Problem Update - Image Stored to Supabase Successfully', [
                                'path' => $path,
                                'problem_id' => $problem->id,
                                'url' => supabase_url($path)
                            ]);

                        } catch (\Exception $supabaseException) {
                            Log::error('Problem Update - Supabase Upload Failed', [
                                'error' => $supabaseException->getMessage(),
                                'file' => $image->getClientOriginalName(),
                                'trace' => $supabaseException->getTraceAsString()
                            ]);

                            // Di production, jangan fallback ke local storage (ephemeral)
                            if (app()->environment('production')) {
                                throw new \Exception("Gagal upload gambar ke Supabase: " . $supabaseException->getMessage());
                            }

                            // Fallback ke public disk HANYA di local/development
                            Log::warning('Problem Update - Using Public Disk Fallback (Development Only)', [
                                'file' => $image->getClientOriginalName()
                            ]);

                            $path = $image->store('problems', 'public');

                            if (!$path) {
                                throw new \Exception("Fallback upload juga gagal. Error: " . $supabaseException->getMessage());
                            }

                            Log::info('Problem Update - Image Stored to Public Disk (Fallback)', [
                                'path' => $path
                            ]);
                        }

                        $problem->images()->create([
                            'problem_id' => $problem->id,
                            'image_path' => $path,
                            'order' => $currentImageCount + $index + 1,
                            'is_cover' => ($currentImageCount === 0 && $index === 0)
                        ]);

                        Log::info('Problem Update - Image Record Created', [
                            'path' => $path,
                            'order' => $currentImageCount + $index + 1
                        ]);

                    } catch (\Exception $imgException) {
                        Log::error('Problem Update - Image Upload Exception', [
                            'index' => $index,
                            'error' => $imgException->getMessage(),
                            'trace' => $imgException->getTraceAsString()
                        ]);
                        throw new \Exception("Error upload gambar ke-" . ($index + 1) . ": " . $imgException->getMessage());
                    }
                }

                // Refresh relation lagi setelah upload
                $problem->load('images');

                // Jika ada gambar baru diupload dan belum ada cover, set gambar pertama yang baru sebagai cover
                if ($problem->images()->where('is_cover', true)->doesntExist()) {
                    $firstImage = $problem->images()->orderBy('order')->first();
                    if ($firstImage) {
                        $firstImage->update(['is_cover' => true]);
                    }
                }
            }

            // Jika tidak ada gambar baru diupload dan semua gambar lama dihapus, pastikan tidak ada cover yang tersisa
            if ($problem->images()->where('is_cover', true)->doesntExist() && $problem->images()->count() > 0) {
                $firstImage = $problem->images()->orderBy('order')->first();
                if ($firstImage) {
                    $firstImage->update(['is_cover' => true]);
                }
            }

            DB::commit();

            Log::info('Problem Update - Success', ['problem_id' => $problem->id]);

            return redirect()->route('institution.problems.show', $problem->id)->with('success', 'Problem berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Problem Update - Validation Error', [
                'problem_id' => $problem->id,
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Problem Update - Exception', [
                'problem_id' => $problem->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat memperbarui problem: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * hapus problem
     * FIX: support multi-disk deletion (public dan supabase)
     */
    public function destroy($id)
    {
        $problem = Problem::where('institution_id', auth()->user()->institution->id)
                         ->findOrFail($id);

        // cek apakah ada aplikasi yang sudah diterima
        if ($problem->applications()->where('status', 'accepted')->exists()) {
            return back()->with('error', 'Tidak Dapat Menghapus Problem yang Sudah Memiliki Aplikasi Diterima!');
        }

        // hapus semua gambar (support public dan supabase disk)
        foreach ($problem->images as $image) {
            $imagePath = $image->image_path;

            // Coba hapus dari public disk
            try {
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            } catch (\Exception $e) {
                Log::warning('Problem Destroy - Failed to delete from public disk', [
                    'error' => $e->getMessage()
                ]);
            }

            // Coba hapus dari supabase disk
            try {
                if (Storage::disk('supabase')->exists($imagePath)) {
                    Storage::disk('supabase')->delete($imagePath);
                }
            } catch (\Exception $e) {
                Log::warning('Problem Destroy - Failed to delete from supabase disk', [
                    'error' => $e->getMessage()
                ]);
            }

            $image->delete();
        }

        $problem->delete();

        return redirect()
            ->route('institution.problems.index')
            ->with('success', 'Problem Berhasil Dihapus!');
    }

    /**
     * API endpoint untuk mendapatkan regencies berdasarkan province
     * digunakan untuk dynamic dropdown
     */
    public function getRegencies($provinceId)
    {
        $regencies = Regency::where('province_id', $provinceId)
                           ->orderBy('name')
                           ->get(['id', 'name']);
        
        return response()->json($regencies);
    }
}