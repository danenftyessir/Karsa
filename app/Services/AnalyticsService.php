<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Problem;
use App\Models\Project;
use Carbon\Carbon;

/**
 * service untuk analytics dan statistik
 * 
 * PERBAIKAN BUG:
 * - ubah 'reviewed' menjadi 'under_review' untuk konsistensi status
 */
class AnalyticsService
{
    /**
     * get dashboard statistics untuk institution
     */
    public function getInstitutionStats($institutionId)
    {
        $stats = [
            'problems' => $this->getProblemStats($institutionId),
            'applications' => $this->getApplicationStats($institutionId),
            'projects' => $this->getProjectStats($institutionId),
        ];

        return $stats;
    }

    /**
     * alias untuk getInstitutionStats()
     * digunakan di DashboardController
     */
    public function getInstitutionAnalytics($institutionId)
    {
        return $this->getInstitutionStats($institutionId);
    }

    /**
     * statistik problems
     */
    private function getProblemStats($institutionId)
    {
        $problems = Problem::where('institution_id', $institutionId);

        $total = $problems->count();
        $open = (clone $problems)->where('status', 'open')->count();
        $closed = (clone $problems)->where('status', 'closed')->count();
        $completed = (clone $problems)->where('status', 'completed')->count();

        // rata-rata aplikasi per problem
        $totalApplications = Application::whereHas('problem', function($q) use ($institutionId) {
            $q->where('institution_id', $institutionId);
        })->count();
        $avgApplications = $total > 0 ? $totalApplications / $total : 0;

        // total views (PERBAIKAN: kolom database adalah 'views_count' bukan 'view_count')
        $totalViews = Problem::where('institution_id', $institutionId)->sum('views_count');
        $avgViewsPerProblem = $total > 0 ? $totalViews / $total : 0;

        // hitung growth dari bulan lalu
        $lastMonthTotal = Problem::where('institution_id', $institutionId)
                                ->where('created_at', '<', Carbon::now()->startOfMonth())
                                ->count();
        
        $growth = $lastMonthTotal > 0 
            ? (($total - $lastMonthTotal) / $lastMonthTotal) * 100 
            : 0;

        return [
            'total' => $total,
            'open' => $open,
            'closed' => $closed,
            'completed' => $completed,
            'average_applications' => round($avgApplications, 1),
            'total_views' => $totalViews,
            'avg_views_per_problem' => round($avgViewsPerProblem, 1),
            'growth' => round($growth, 1),
        ];
    }

    /**
     * statistik applications
     * 
     * PERBAIKAN: gunakan 'under_review' bukan 'reviewed'
     */
    private function getApplicationStats($institutionId)
    {
        $applications = Application::whereHas('problem', function($q) use ($institutionId) {
            $q->where('institution_id', $institutionId);
        });

        $total = $applications->count();
        $pending = (clone $applications)->where('status', 'pending')->count();
        
        // PERBAIKAN: gunakan 'under_review' bukan 'reviewed'
        $underReview = (clone $applications)->where('status', 'under_review')->count();
        
        $accepted = (clone $applications)->where('status', 'accepted')->count();
        $rejected = (clone $applications)->where('status', 'rejected')->count();

        // acceptance rate
        $acceptanceRate = $total > 0 ? ($accepted / $total) * 100 : 0;

        // hitung growth dari bulan lalu
        $lastMonthTotal = Application::whereHas('problem', function($q) use ($institutionId) {
                                    $q->where('institution_id', $institutionId);
                                })
                                ->where('created_at', '<', Carbon::now()->startOfMonth())
                                ->count();
        
        $growth = $lastMonthTotal > 0 
            ? (($total - $lastMonthTotal) / $lastMonthTotal) * 100 
            : 0;

        return [
            'total' => $total,
            'pending' => $pending,
            'under_review' => $underReview, // PERBAIKAN
            'accepted' => $accepted,
            'rejected' => $rejected,
            'acceptance_rate' => round($acceptanceRate, 1),
            'growth' => round($growth, 1),
        ];
    }

    /**
     * statistik projects
     */
    private function getProjectStats($institutionId)
    {
        $projects = Project::where('institution_id', $institutionId);

        $total = $projects->count();
        $planning = (clone $projects)->where('status', 'planning')->count();
        $active = (clone $projects)->where('status', 'active')->count();
        $review = (clone $projects)->where('status', 'review')->count();
        $completed = (clone $projects)->where('status', 'completed')->count();

        // rata-rata progress dari semua project aktif
        $activeProjects = Project::where('institution_id', $institutionId)
                                 ->where('status', 'active')
                                 ->get();
        
        $avgProgress = 0;
        if ($activeProjects->count() > 0) {
            $totalProgress = $activeProjects->sum('progress_percentage');
            $avgProgress = $totalProgress / $activeProjects->count();
        }

        // rata-rata completion time
        $completedProjects = Project::where('institution_id', $institutionId)
                                   ->where('status', 'completed')
                                   ->whereNotNull('actual_end_date')
                                   ->get();

        $avgCompletionDays = 0;
        if ($completedProjects->count() > 0) {
            $totalDays = 0;
            foreach ($completedProjects as $project) {
                if ($project->start_date && $project->actual_end_date) {
                    $totalDays += $project->start_date->diffInDays($project->actual_end_date);
                }
            }
            $avgCompletionDays = $completedProjects->count() > 0 ? $totalDays / $completedProjects->count() : 0;
        }

        // completion rate
        $completionRate = $total > 0 ? ($completed / $total) * 100 : 0;

        // hitung growth dari bulan lalu
        $lastMonthTotal = Project::where('institution_id', $institutionId)
                                ->where('created_at', '<', Carbon::now()->startOfMonth())
                                ->count();
        
        $growth = $lastMonthTotal > 0 
            ? (($total - $lastMonthTotal) / $lastMonthTotal) * 100 
            : 0;

        return [
            'total' => $total,
            'planning' => $planning,
            'active' => $active,
            'review' => $review,
            'completed' => $completed,
            'avg_progress' => round($avgProgress, 1),
            'avg_completion_days' => round($avgCompletionDays, 1),
            'completion_rate' => round($completionRate, 1),
            'growth' => round($growth, 1),
        ];
    }

    /**
     * get top problems berdasarkan jumlah aplikasi
     * 
     * PERBAIKAN: tabel applications tidak ada soft deletes
     */
    public function getTopProblems($institutionId, $limit = 5)
    {
        return Problem::where('problems.institution_id', $institutionId)
            ->selectRaw('problems.*, (SELECT COUNT(*) FROM applications WHERE applications.problem_id = problems.id) as total_applications')
            ->orderByDesc('total_applications')
            ->limit($limit)
            ->get();
    }

    /**
     * get time series data untuk chart
     */
    public function getTimeSeriesData($institutionId, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        $data = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            $applications = Application::whereHas('problem', function($q) use ($institutionId) {
                                        $q->where('institution_id', $institutionId);
                                    })
                                    ->whereDate('created_at', $date)
                                    ->count();
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'applications' => $applications,
            ];
        }

        return $data;
    }

    /**
     * get application funnel data
     */
    public function getApplicationFunnel($institutionId)
    {
        $total = Application::whereHas('problem', function($q) use ($institutionId) {
            $q->where('institution_id', $institutionId);
        })->count();

        $pending = Application::whereHas('problem', function($q) use ($institutionId) {
            $q->where('institution_id', $institutionId);
        })->where('status', 'pending')->count();

        // PERBAIKAN: gunakan 'under_review'
        $underReview = Application::whereHas('problem', function($q) use ($institutionId) {
            $q->where('institution_id', $institutionId);
        })->where('status', 'under_review')->count();

        $accepted = Application::whereHas('problem', function($q) use ($institutionId) {
            $q->where('institution_id', $institutionId);
        })->where('status', 'accepted')->count();

        $rejected = Application::whereHas('problem', function($q) use ($institutionId) {
            $q->where('institution_id', $institutionId);
        })->where('status', 'rejected')->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'under_review' => $underReview, // PERBAIKAN
            'accepted' => $accepted,
            'rejected' => $rejected,
            'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 1) : 0,
            'under_review_percentage' => $total > 0 ? round(($underReview / $total) * 100, 1) : 0, // PERBAIKAN
            'accepted_percentage' => $total > 0 ? round(($accepted / $total) * 100, 1) : 0,
            'rejected_percentage' => $total > 0 ? round(($rejected / $total) * 100, 1) : 0,
        ];
    }
}