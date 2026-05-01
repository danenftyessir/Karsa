<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Project;
use App\Services\ReviewService;

/**
 * controller untuk manajemen review mahasiswa oleh instansi
 */
class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * tampilkan daftar review yang telah diberikan
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;

        // FIXED: tambah eager loading untuk reviewee.student dan reviewee.student.university
        $query = Review::with([
            'reviewee.student.university', // eager load user -> student -> university
            'project.problem'
        ])
        ->where('type', 'institution_to_student')
        ->whereHas('project', function($q) use ($institution) {
            $q->where('institution_id', $institution->id);
        });

        // filter berdasarkan rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // search berdasarkan nama mahasiswa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('reviewee', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // sorting
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            default:
                $query->latest();
        }

        $reviews = $query->paginate(15)->withQueryString();

        // OPTIMIZED: statistik review - 1 query instead of 4
        // Menggunakan single query dengan agregasi untuk performa lebih baik
        $statsQuery = Review::query()
            ->join('projects', 'reviews.project_id', '=', 'projects.id')
            ->where('reviews.type', 'institution_to_student')
            ->where('projects.institution_id', $institution->id)
            ->selectRaw('
                COUNT(*) as total,
                COALESCE(AVG(reviews.rating), 0) as average_rating,
                SUM(CASE WHEN reviews.rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN reviews.rating = 4 THEN 1 ELSE 0 END) as four_star
            ')
            ->first();

        $stats = [
            'total' => $statsQuery->total ?? 0,
            'average_rating' => round($statsQuery->average_rating ?? 0, 1),
            'five_star' => $statsQuery->five_star ?? 0,
            'four_star' => $statsQuery->four_star ?? 0,
        ];

        return view('institution.reviews.index', compact('reviews', 'stats'));
    }

    /**
     * tampilkan form create review untuk proyek yang selesai
     */
    public function create($projectId)
    {
        $institution = auth()->user()->institution;

        $project = Project::with([
            'student.user',
            'student.university',
            'problem'
        ])
        ->where('institution_id', $institution->id)
        ->findOrFail($projectId);

        // validasi: hanya bisa review jika proyek sudah selesai
        if ($project->status !== 'completed') {
            return redirect()->route('institution.projects.show', $project->id)
                           ->with('error', 'Hanya dapat memberikan review untuk proyek yang sudah selesai.');
        }

        // cek apakah sudah pernah review
        $existingReview = Review::where('project_id', $project->id)
            ->where('type', 'institution_to_student')
            ->first();

        if ($existingReview) {
            return redirect()->route('institution.projects.show', $project->id)
                           ->with('error', 'Anda sudah memberikan review untuk proyek ini.');
        }

        return view('institution.reviews.create', compact('project'));
    }

    /**
     * simpan review baru
     */
    public function store(Request $request)
    {
        $institution = auth()->user()->institution;
        $projectId = $request->input('project_id');

        $project = Project::where('institution_id', $institution->id)->findOrFail($projectId);

        // validasi
        if ($project->status !== 'completed') {
            return back()->with('error', 'Hanya dapat memberikan review untuk proyek yang sudah selesai.');
        }

        // cek apakah sudah review
        $existingReview = Review::where('project_id', $project->id)
            ->where('type', 'institution_to_student')
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan review untuk proyek ini.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
            'strengths' => 'nullable|string|max:500',
            'improvements' => 'nullable|string|max:500',
            'would_collaborate_again' => 'boolean',
        ]);

        try {
            // gunakan service untuk handle logic
            $review = $this->reviewService->createInstitutionReview(
                $project,
                $validated['rating'],
                $validated['review'],
                $validated['strengths'] ?? null,
                $validated['improvements'] ?? null,
                $validated['would_collaborate_again'] ?? false
            );

            return redirect()->route('institution.projects.show', $project->id)
                           ->with('success', 'Review berhasil diberikan!');

        } catch (\Exception $e) {
            return back()->withInput()
                       ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * tampilkan detail review
     */
    public function show($id)
    {
        $institution = auth()->user()->institution;

        // FIXED: tambah eager loading lengkap
        $review = Review::with([
            'reviewee.student.university',
            'project.problem'
        ])
        ->where('type', 'institution_to_student')
        ->whereHas('project', function($q) use ($institution) {
            $q->where('institution_id', $institution->id);
        })
        ->findOrFail($id);

        return view('institution.reviews.show', compact('review'));
    }

    /**
     * edit review yang sudah diberikan
     */
    public function edit($id)
    {
        $institution = auth()->user()->institution;

        // FIXED: tambah eager loading lengkap
        $review = Review::with([
            'reviewee.student.university',
            'project.problem'
        ])
        ->where('type', 'institution_to_student')
        ->whereHas('project', function($q) use ($institution) {
            $q->where('institution_id', $institution->id);
        })
        ->findOrFail($id);

        // hanya bisa edit dalam 30 hari
        if ($review->created_at->addDays(30)->isPast()) {
            return redirect()->route('institution.reviews.show', $review->id)
                           ->with('error', 'Review tidak dapat diedit setelah 30 hari.');
        }

        return view('institution.reviews.edit', compact('review'));
    }

    /**
     * update review yang sudah diberikan
     */
    public function update(Request $request, $id)
    {
        $institution = auth()->user()->institution;

        $review = Review::where('type', 'institution_to_student')
            ->whereHas('project', function($q) use ($institution) {
                $q->where('institution_id', $institution->id);
            })
            ->findOrFail($id);

        // hanya bisa edit dalam 30 hari
        if ($review->created_at->addDays(30)->isPast()) {
            return back()->with('error', 'Review tidak dapat diedit setelah 30 hari.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
            'strengths' => 'nullable|string|max:500',
            'improvements' => 'nullable|string|max:500',
        ]);

        try {
            $review->update([
                'rating' => $validated['rating'],
                'review_text' => $validated['review'],
                'strengths' => $validated['strengths'] ?? null,
                'improvements' => $validated['improvements'] ?? null,
            ]);

            return redirect()->route('institution.reviews.show', $review->id)
                           ->with('success', 'Review berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()
                       ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}