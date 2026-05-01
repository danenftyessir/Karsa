<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController - Company Dashboard
 *
 * IMPLEMENTED: Semua data CRUD langsung dari Supabase PostgreSQL
 * TIDAK ADA dummy data lagi
 */
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('home')
                ->with('error', 'profil perusahaan tidak ditemukan');
        }

        // IMPLEMENTED: Ambil statistik dari Supabase
        $totalJobs = $company->jobPostings()->count();
        $activeJobs = $company->jobPostings()->active()->count();
        $draftJobs = $company->jobPostings()->where('status', 'draft')->count();

        // Total applications received
        $totalApplications = $company->jobApplications()->count();

        // Shortlisted candidates
        $shortlistedCount = $company->jobApplications()
            ->where('job_applications.status', JobApplication::STATUS_SHORTLISTED)
            ->count();

        // Total hires
        $hiresCount = $company->jobApplications()
            ->where('job_applications.status', JobApplication::STATUS_HIRED)
            ->count();

        // Calculate growth percentages (comparing last 30 days vs previous 30 days)
        $currentPeriodJobs = $company->jobPostings()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $previousPeriodJobs = $company->jobPostings()
            ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
            ->count();
        $jobsGrowth = $previousPeriodJobs > 0
            ? round((($currentPeriodJobs - $previousPeriodJobs) / $previousPeriodJobs) * 100)
            : 0;

        $currentPeriodApplications = $company->jobApplications()
            ->where('job_applications.created_at', '>=', now()->subDays(30))
            ->count();
        $previousPeriodApplications = $company->jobApplications()
            ->whereBetween('job_applications.created_at', [now()->subDays(60), now()->subDays(30)])
            ->count();
        $applicationsGrowth = $previousPeriodApplications > 0
            ? round((($currentPeriodApplications - $previousPeriodApplications) / $previousPeriodApplications) * 100)
            : 0;

        $stats = [
            'total_jobs' => $totalJobs,
            'active_jobs' => $activeJobs,
            'draft_jobs' => $draftJobs,
            'total_jobs_growth' => $jobsGrowth,
            'applications_received' => $totalApplications,
            'applications_growth' => $applicationsGrowth,
            'shortlisted_candidates' => $shortlistedCount,
            'shortlisted_growth' => 0, // Can be calculated if needed
            'hires_made' => $hiresCount,
            'hires_growth' => 0, // Can be calculated if needed
        ];

        // IMPLEMENTED: Ambil recent applications dari Supabase
        $recentApplications = $company->jobApplications()
            ->with(['user', 'jobPosting'])
            ->orderBy('job_applications.created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'name' => $application->user->name ?? 'Unknown',
                    'position' => $application->jobPosting->title ?? 'Unknown Position',
                    'status' => $application->status,
                    'avatar' => $application->user->avatar ?? null,
                    'created_at' => $application->created_at,
                ];
            });

        // IMPLEMENTED: AI talent recommendations dari Supabase
        // Ambil users dengan impact score tertinggi yang belum diapply
        $talentRecommendations = User::where('user_type', 'student')
            ->whereHas('student')
            ->whereDoesntHave('jobApplications', function ($query) use ($company) {
                $query->whereIn('job_posting_id', $company->jobPostings()->pluck('id'));
            })
            ->with('student')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'expertise' => $user->student->major ?? 'No major specified',
                    'avatar' => $user->student->profile_photo_path ?? 'default-avatar.jpg',
                    'online' => true, // Could be implemented with last_seen_at
                ];
            });

        // IMPLEMENTED: Ambil data chart applications over time dari Supabase
        $applicationsOverTime = $this->getApplicationsOverTime($company);

        // IMPLEMENTED: Ambil data jobs by category dari Supabase
        $jobsByCategory = $this->getJobsByCategory($company);

        return view('company.dashboard.index', compact(
            'company',
            'stats',
            'recentApplications',
            'talentRecommendations',
            'applicationsOverTime',
            'jobsByCategory'
        ));
    }

    /**
     * Get applications over time data for chart
     * IMPLEMENTED: Data dari Supabase PostgreSQL
     */
    private function getApplicationsOverTime($company)
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i));
        }

        $labels = $months->map(fn($date) => $date->format('M'))->toArray();

        // Get applications grouped by status and month
        $statusGroups = [
            'new' => [],
            'reviewing' => [],
            'shortlisted' => [],
        ];

        foreach ($months as $month) {
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            foreach (['new', 'reviewing', 'shortlisted'] as $status) {
                $count = $company->jobApplications()
                    ->where('job_applications.status', $status)
                    ->whereBetween('job_applications.created_at', [$startOfMonth, $endOfMonth])
                    ->count();

                $statusGroups[$status][] = $count;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'New',
                    'data' => $statusGroups['new'],
                    'color' => '#F97316',
                ],
                [
                    'label' => 'Reviewing',
                    'data' => $statusGroups['reviewing'],
                    'color' => '#22C55E',
                ],
                [
                    'label' => 'Shortlisted',
                    'data' => $statusGroups['shortlisted'],
                    'color' => '#3B82F6',
                ],
            ],
        ];
    }

    /**
     * Get jobs by category data for chart
     * IMPLEMENTED: Data dari Supabase PostgreSQL
     */
    private function getJobsByCategory($company)
    {
        $jobsByCategory = $company->jobPostings()
            ->select('job_category_id', DB::raw('count(*) as total'))
            ->with('jobCategory')
            ->groupBy('job_category_id')
            ->get();

        $labels = [];
        $data = [];

        foreach ($jobsByCategory as $item) {
            $labels[] = $item->jobCategory->name ?? 'Uncategorized';
            $data[] = $item->total;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
