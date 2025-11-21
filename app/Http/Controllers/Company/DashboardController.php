<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// controller untuk halaman dashboard company
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        // TO DO: ambil data dari database ketika model JobPosting dan JobApplication sudah dibuat
        // statistik dummy untuk tampilan awal
        $stats = [
            'total_jobs' => 124,
            'total_jobs_growth' => 12,
            'applications_received' => 8945,
            'applications_growth' => 8,
            'shortlisted_candidates' => 1203,
            'shortlisted_growth' => 15,
            'hires_made' => 78,
            'hires_growth' => 5,
        ];

        // TO DO: ambil recent applications dari database
        $recentApplications = [
            [
                'name' => 'Alice Johnson',
                'position' => 'Senior Software Engineer',
                'status' => 'shortlisted',
                'avatar' => null,
            ],
            [
                'name' => 'Bob Williams',
                'position' => 'Product Designer',
                'status' => 'reviewing',
                'avatar' => null,
            ],
            [
                'name' => 'Charlie Davis',
                'position' => 'Data Scientist',
                'status' => 'new',
                'avatar' => null,
            ],
            [
                'name' => 'Diana Miller',
                'position' => 'HR Specialist',
                'status' => 'new',
                'avatar' => null,
            ],
            [
                'name' => 'Eve Brown',
                'position' => 'Marketing Manager',
                'status' => 'reviewing',
                'avatar' => null,
            ],
        ];

        // TO DO: ambil AI talent recommendations dari database dengan algoritma matching
        $talentRecommendations = [
            [
                'name' => 'Sophia Lee',
                'expertise' => 'Product Management',
                'avatar' => 'profile_13523136.jpg',
                'online' => true,
            ],
            [
                'name' => 'Liam Chen',
                'expertise' => 'Full-stack Development',
                'avatar' => 'profile_13523155.jpg',
                'online' => true,
            ],
            [
                'name' => 'Olivia Garcia',
                'expertise' => 'UI/UX Design',
                'avatar' => 'profile_18223127.jpg',
                'online' => true,
            ],
            [
                'name' => 'Noah Martinez',
                'expertise' => 'Data Science',
                'avatar' => 'profile_13523136.jpg',
                'online' => true,
            ],
            [
                'name' => 'Emma Wilson',
                'expertise' => 'Digital Marketing',
                'avatar' => 'profile_13523155.jpg',
                'online' => true,
            ],
        ];

        // TO DO: ambil data chart dari database
        $applicationsOverTime = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [
                [
                    'label' => 'New',
                    'data' => [65, 80, 95, 110, 140, 180],
                    'color' => '#F97316',
                ],
                [
                    'label' => 'Reviewing',
                    'data' => [45, 60, 75, 90, 100, 120],
                    'color' => '#22C55E',
                ],
                [
                    'label' => 'Shortlisted',
                    'data' => [30, 45, 60, 75, 85, 95],
                    'color' => '#3B82F6',
                ],
            ],
        ];

        // TO DO: ambil data jobs by category dari database
        $jobsByCategory = [
            'labels' => ['Engineering', 'Marketing', 'Design', 'HR', 'Sales'],
            'data' => [45, 28, 22, 15, 35],
        ];

        return view('company.dashboard.index', compact(
            'company',
            'stats',
            'recentApplications',
            'talentRecommendations',
            'applicationsOverTime',
            'jobsByCategory'
        ));
    }
}
