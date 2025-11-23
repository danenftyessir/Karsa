<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * JobApplicationController - Manage Job Applications
 *
 * IMPLEMENTED: Semua operasi CRUD langsung ke Supabase PostgreSQL
 * TIDAK ADA data yang disimpan di local database
 */
class JobApplicationController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Display all applications for company (Kanban view)
     * IMPLEMENTED: Data dari Supabase PostgreSQL
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('home')
                ->with('error', 'profil perusahaan tidak ditemukan');
        }

        // IMPLEMENTED: Ambil data applications dari Supabase
        $jobPostingId = $request->get('job_posting_id');
        $status = $request->get('status');
        $search = $request->get('search');

        $applicationsQuery = $company->jobApplications()
            ->with(['user.student', 'jobPosting']);

        // Apply filters
        if ($jobPostingId) {
            $applicationsQuery->where('job_posting_id', $jobPostingId);
        }

        if ($status) {
            $applicationsQuery->where('status', $status);
        }

        if ($search) {
            $applicationsQuery->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        $applications = $applicationsQuery->orderBy('job_applications.created_at', 'desc')->get();

        // Group by status for Kanban view
        $applicationsByStatus = [
            'new' => $applications->where('status', JobApplication::STATUS_NEW)->values(),
            'reviewing' => $applications->where('status', JobApplication::STATUS_REVIEWING)->values(),
            'shortlisted' => $applications->where('status', JobApplication::STATUS_SHORTLISTED)->values(),
            'interview' => $applications->where('status', JobApplication::STATUS_INTERVIEW)->values(),
            'offer' => $applications->where('status', JobApplication::STATUS_OFFER)->values(),
            'hired' => $applications->where('status', JobApplication::STATUS_HIRED)->values(),
            'rejected' => $applications->where('status', JobApplication::STATUS_REJECTED)->values(),
        ];

        // Get job postings for filter dropdown
        $jobPostings = $company->jobPostings()
            ->active()
            ->orderBy('title')
            ->get();

        // Statistics
        $stats = [
            'total' => $applications->count(),
            'new' => $applications->where('status', JobApplication::STATUS_NEW)->count(),
            'shortlisted' => $applications->where('status', JobApplication::STATUS_SHORTLISTED)->count(),
            'hired' => $applications->where('status', JobApplication::STATUS_HIRED)->count(),
        ];

        return view('company.applications.index', compact(
            'company',
            'applicationsByStatus',
            'jobPostings',
            'stats'
        ));
    }

    /**
     * Display single application detail
     * IMPLEMENTED: Data dari Supabase PostgreSQL
     */
    public function show($id)
    {
        $user = Auth::user();
        $company = $user->company;

        // IMPLEMENTED: Ambil data application dari Supabase
        $application = JobApplication::with(['user.student', 'jobPosting', 'reviewer'])
            ->whereHas('jobPosting', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->where('id', $id)
            ->firstOrFail();

        // Mark as reviewed if not yet reviewed
        if (!$application->is_reviewed) {
            $application->update([
                'reviewed_at' => now(),
                'reviewed_by' => $user->id,
            ]);
        }

        return view('company.applications.show', compact('company', 'application'));
    }

    /**
     * Update application status
     * IMPLEMENTED: Update langsung ke Supabase PostgreSQL
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,reviewing,shortlisted,interview,offer,rejected,hired',
            'notes' => 'nullable|string|max:1000',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $company = $user->company;

        // IMPLEMENTED: Update status di Supabase PostgreSQL
        $application = JobApplication::whereHas('jobPosting', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->where('id', $id)
            ->firstOrFail();

        try {
            $application->updateStatus($validated['status'], $user->id, $validated['notes'] ?? null);

            if ($validated['status'] === JobApplication::STATUS_REJECTED && isset($validated['rejection_reason'])) {
                $application->update(['rejection_reason' => $validated['rejection_reason']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Application status updated successfully',
                'application' => $application->fresh(['user', 'jobPosting']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Shortlist application
     * IMPLEMENTED: Update langsung ke Supabase PostgreSQL
     */
    public function shortlist($id)
    {
        $user = Auth::user();
        $company = $user->company;

        $application = JobApplication::whereHas('jobPosting', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->where('id', $id)
            ->firstOrFail();

        $application->shortlist($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Application shortlisted successfully',
        ]);
    }

    /**
     * Reject application
     * IMPLEMENTED: Update langsung ke Supabase PostgreSQL
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $company = $user->company;

        $application = JobApplication::whereHas('jobPosting', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->where('id', $id)
            ->firstOrFail();

        try {
            $application->reject($user->id, $validated['reason'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Application rejected successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Hire applicant
     * IMPLEMENTED: Update langsung ke Supabase PostgreSQL
     */
    public function hire($id)
    {
        $user = Auth::user();
        $company = $user->company;

        $application = JobApplication::whereHas('jobPosting', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->where('id', $id)
            ->firstOrFail();

        $application->hire($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Applicant hired successfully',
        ]);
    }

    /**
     * Bulk update applications status
     * IMPLEMENTED: Bulk update langsung ke Supabase PostgreSQL
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'required|integer|exists:job_applications,id',
            'status' => 'required|in:new,reviewing,shortlisted,interview,offer,rejected,hired',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $company = $user->company;

        // IMPLEMENTED: Bulk update di Supabase PostgreSQL
        $applications = JobApplication::whereHas('jobPosting', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->whereIn('id', $validated['application_ids'])
            ->get();

        $successCount = 0;
        $errors = [];

        foreach ($applications as $application) {
            try {
                $application->updateStatus($validated['status'], $user->id, $validated['notes'] ?? null);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Application ID {$application->id}: " . $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            return response()->json([
                'success' => false,
                'message' => "$successCount applications updated, " . count($errors) . " failed",
                'errors' => $errors,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $successCount . ' applications updated successfully',
        ]);
    }

    /**
     * Export applications to CSV
     * IMPLEMENTED: Data dari Supabase PostgreSQL
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $jobPostingId = $request->get('job_posting_id');

        $applicationsQuery = $company->jobApplications()
            ->with(['user.student', 'jobPosting']);

        if ($jobPostingId) {
            $applicationsQuery->where('job_posting_id', $jobPostingId);
        }

        $applications = $applicationsQuery->orderBy('job_applications.created_at', 'desc')->get();

        $filename = 'applications_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($applications) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Application ID',
                'Job Title',
                'Applicant Name',
                'Email',
                'Status',
                'Rating',
                'Applied Date',
                'Reviewed Date',
            ]);

            foreach ($applications as $application) {
                fputcsv($file, [
                    $application->id,
                    $application->jobPosting->title ?? 'N/A',
                    $application->user->name ?? 'N/A',
                    $application->user->email ?? 'N/A',
                    $application->status_label,
                    $application->rating ?? 'N/A',
                    $application->created_at->format('Y-m-d H:i:s'),
                    $application->reviewed_at ? $application->reviewed_at->format('Y-m-d H:i:s') : 'Not Reviewed',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Add rating to application
     * IMPLEMENTED: Update langsung ke Supabase PostgreSQL
     */
    public function addRating(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $user = Auth::user();
        $company = $user->company;

        $application = JobApplication::whereHas('jobPosting', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->where('id', $id)
            ->firstOrFail();

        $application->addRating($validated['rating'], $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Rating added successfully',
        ]);
    }

    /**
     * Add notes to application
     * IMPLEMENTED: Update langsung ke Supabase PostgreSQL
     */
    public function addNotes(Request $request, $id)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $company = $user->company;

        $application = JobApplication::whereHas('jobPosting', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->where('id', $id)
            ->firstOrFail();

        $application->addNotes($validated['notes']);

        return response()->json([
            'success' => true,
            'message' => 'Notes added successfully',
        ]);
    }
}
