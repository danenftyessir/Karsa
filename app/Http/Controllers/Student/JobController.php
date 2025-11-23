<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\SavedJob;
use App\Models\JobAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * JobController - Browse Job Listings untuk Student
 *
 * menampilkan lowongan kerja/magang dari company
 * data langsung dari supabase postgresql
 */
class JobController extends Controller
{
    /**
     * halaman utama browse jobs
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // query job postings yang aktif
        $query = JobPosting::with(['company', 'jobCategory'])
            ->active();

        // filter by search keyword
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // filter by job type
        if ($request->filled('job_type')) {
            $query->byType($request->job_type);
        }

        // filter by location
        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        // filter by category
        if ($request->filled('category')) {
            $query->where('job_category_id', $request->category);
        }

        // filter by salary range
        if ($request->filled('salary_min')) {
            $query->where('salary_min', '>=', $request->salary_min);
        }

        // filter by sdg alignment
        if ($request->filled('sdg')) {
            $sdgIds = is_array($request->sdg) ? $request->sdg : [$request->sdg];
            foreach ($sdgIds as $sdgId) {
                $query->whereJsonContains('sdg_alignment', (int) $sdgId);
            }
        }

        // sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'salary_high':
                $query->orderBy('salary_max', 'desc');
                break;
            case 'salary_low':
                $query->orderBy('salary_min', 'asc');
                break;
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            default:
                $query->orderBy('published_at', 'desc');
        }

        $jobs = $query->paginate(12);

        // ambil applied job ids untuk user
        $appliedJobIds = JobApplication::where('user_id', $user->id)
            ->pluck('job_posting_id')
            ->toArray();

        // ambil categories untuk filter
        $categories = JobCategory::orderBy('name')->get();

        // job types untuk filter
        $jobTypes = [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Magang',
            'freelance' => 'Freelance',
        ];

        // sdg options untuk filter
        $sdgOptions = [
            ['id' => 1, 'name' => 'No Poverty'],
            ['id' => 4, 'name' => 'Quality Education'],
            ['id' => 8, 'name' => 'Decent Work And Economic Growth'],
            ['id' => 9, 'name' => 'Industry, Innovation And Infrastructure'],
            ['id' => 10, 'name' => 'Reduced Inequalities'],
            ['id' => 11, 'name' => 'Sustainable Cities And Communities'],
            ['id' => 12, 'name' => 'Responsible Consumption And Production'],
            ['id' => 13, 'name' => 'Climate Action'],
        ];

        // statistics
        $totalJobs = JobPosting::active()->count();
        $totalCompanies = JobPosting::active()->distinct('company_id')->count('company_id');

        // ambil saved job ids
        $savedJobIds = SavedJob::where('user_id', $user->id)
            ->pluck('job_posting_id')
            ->toArray();

        return view('student.jobs.index', compact(
            'jobs',
            'appliedJobIds',
            'savedJobIds',
            'categories',
            'jobTypes',
            'sdgOptions',
            'totalJobs',
            'totalCompanies'
        ));
    }

    /**
     * detail job posting
     */
    public function show($id)
    {
        $user = Auth::user();

        $job = JobPosting::with(['company', 'jobCategory'])
            ->findOrFail($id);

        // increment views
        $job->incrementViews();

        // cek apakah sudah apply
        $hasApplied = JobApplication::where('user_id', $user->id)
            ->where('job_posting_id', $id)
            ->exists();

        // ambil existing application jika ada
        $application = JobApplication::where('user_id', $user->id)
            ->where('job_posting_id', $id)
            ->first();

        // similar jobs
        $similarJobs = JobPosting::with(['company'])
            ->active()
            ->where('id', '!=', $id)
            ->where(function ($query) use ($job) {
                $query->where('job_category_id', $job->job_category_id)
                    ->orWhere('company_id', $job->company_id);
            })
            ->limit(4)
            ->get();

        return view('student.jobs.show', compact(
            'job',
            'hasApplied',
            'application',
            'similarJobs'
        ));
    }

    /**
     * apply ke job posting
     */
    public function apply(Request $request, $id)
    {
        $user = Auth::user();

        $job = JobPosting::active()->findOrFail($id);

        // cek apakah sudah apply
        $existingApplication = JobApplication::where('user_id', $user->id)
            ->where('job_posting_id', $id)
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'Anda sudah melamar ke lowongan ini');
        }

        $validated = $request->validate([
            'cover_letter' => 'nullable|string|max:2000',
        ]);

        // buat application
        JobApplication::create([
            'job_posting_id' => $id,
            'user_id' => $user->id,
            'status' => 'pending', // default status sesuai migration
            'cover_letter' => $validated['cover_letter'] ?? null,
        ]);

        // increment applications count
        $job->increment('applications_count');

        return redirect()->route('student.jobs.show', $id)
            ->with('success', 'Lamaran berhasil dikirim!');
    }

    /**
     * withdraw application
     */
    public function withdraw($id)
    {
        $user = Auth::user();

        $application = JobApplication::where('user_id', $user->id)
            ->where('job_posting_id', $id)
            ->where('status', 'new')
            ->firstOrFail();

        $application->delete();

        // decrement applications count
        JobPosting::where('id', $id)->decrement('applications_count');

        return back()->with('success', 'Lamaran berhasil dibatalkan');
    }

    // ========================================================================
    // SAVED JOBS / BOOKMARK
    // ========================================================================

    /**
     * halaman saved jobs
     */
    public function saved(Request $request)
    {
        $user = Auth::user();

        $query = SavedJob::with(['jobPosting.company'])
            ->byUser($user->id);

        $savedJobs = $query->orderBy('created_at', 'desc')->paginate(12);

        // statistik
        $totalSaved = SavedJob::byUser($user->id)->count();
        $expiringCount = SavedJob::byUser($user->id)
            ->whereHas('jobPosting', function ($q) {
                $q->whereNotNull('expires_at')
                    ->where('expires_at', '<=', now()->addDays(7));
            })
            ->count();

        return view('student.jobs.saved', compact(
            'savedJobs',
            'totalSaved',
            'expiringCount'
        ));
    }

    /**
     * toggle save/unsave job
     */
    public function toggleSave(Request $request, $id)
    {
        $user = Auth::user();

        $savedJob = SavedJob::where('user_id', $user->id)
            ->where('job_posting_id', $id)
            ->first();

        if ($savedJob) {
            $savedJob->delete();
            return response()->json([
                'success' => true,
                'action' => 'unsaved',
                'message' => 'Lowongan dihapus dari bookmark'
            ]);
        }

        SavedJob::create([
            'user_id' => $user->id,
            'job_posting_id' => $id,
            'folder' => $request->input('folder'),
            'saved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'action' => 'saved',
            'message' => 'Lowongan ditambahkan ke bookmark'
        ]);
    }

    /**
     * update saved job (folder, notes, reminder)
     */
    public function updateSaved(Request $request, $id)
    {
        $user = Auth::user();

        $savedJob = SavedJob::where('user_id', $user->id)
            ->where('job_posting_id', $id)
            ->firstOrFail();

        $savedJob->update([
            'folder' => $request->input('folder'),
            'notes' => $request->input('notes'),
            'reminder_at' => $request->input('reminder_at'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bookmark berhasil diupdate'
        ]);
    }

    // ========================================================================
    // JOB COMPARISON
    // ========================================================================

    /**
     * compare jobs side by side
     */
    public function compare(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $ids = array_filter($ids, 'is_numeric');

        if (count($ids) < 2 || count($ids) > 3) {
            return redirect()->route('student.jobs.index')
                ->with('error', 'Pilih 2-3 lowongan untuk dibandingkan');
        }

        $jobs = JobPosting::with(['company', 'jobCategory'])
            ->whereIn('id', $ids)
            ->get();

        return view('student.jobs.compare', compact('jobs'));
    }

    // ========================================================================
    // QUICK APPLY
    // ========================================================================

    /**
     * quick apply dengan data profile
     */
    public function quickApply(Request $request, $id)
    {
        $user = Auth::user();
        $profile = $user->profile;

        $job = JobPosting::active()->findOrFail($id);

        // cek apakah sudah apply
        if (JobApplication::where('user_id', $user->id)->where('job_posting_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melamar ke lowongan ini'
            ], 400);
        }

        // buat application dengan data dari profile
        JobApplication::create([
            'job_posting_id' => $id,
            'user_id' => $user->id,
            'status' => 'new',
            'cover_letter' => $request->input('cover_letter', $user->default_cover_letter ?? null),
            'resume_url' => $profile->resume_url ?? null,
            'portfolio_url' => route('student.profile.public', $user->username ?? $user->id),
            'applied_at' => now(),
        ]);

        $job->increment('applications_count');

        return response()->json([
            'success' => true,
            'message' => 'Lamaran berhasil dikirim!'
        ]);
    }

    // ========================================================================
    // JOB ALERTS
    // ========================================================================

    /**
     * halaman job alerts
     * NOTE: Job alerts feature belum diimplementasi - table job_alerts tidak ada
     */
    public function alerts()
    {
        $user = Auth::user();

        // TODO: Implement job alerts feature
        // Sementara return empty collection
        $alerts = collect([]);

        return view('student.jobs.alerts', compact('alerts'));
    }

    /**
     * create job alert
     */
    public function storeAlert(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'keywords' => 'nullable|string|max:255',
            'job_types' => 'nullable|array',
            'locations' => 'nullable|array',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'skills' => 'nullable|array',
            'frequency' => 'required|in:instant,daily,weekly',
        ]);

        JobAlert::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'keywords' => $validated['keywords'] ?? null,
            'job_types' => $validated['job_types'] ?? [],
            'locations' => $validated['locations'] ?? [],
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'skills' => $validated['skills'] ?? [],
            'frequency' => $validated['frequency'],
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job alert berhasil dibuat'
        ]);
    }

    /**
     * toggle alert active status
     */
    public function toggleAlert($id)
    {
        $user = Auth::user();

        $alert = JobAlert::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $alert->update(['is_active' => !$alert->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $alert->is_active,
            'message' => $alert->is_active ? 'Alert diaktifkan' : 'Alert dinonaktifkan'
        ]);
    }

    /**
     * delete job alert
     */
    public function destroyAlert($id)
    {
        $user = Auth::user();

        JobAlert::where('user_id', $user->id)
            ->where('id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job alert berhasil dihapus'
        ]);
    }
}
