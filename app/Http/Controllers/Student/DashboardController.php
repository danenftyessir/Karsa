<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Application;
use App\Models\Problem;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * controller untuk dashboard mahasiswa
 * menampilkan ringkasan aktivitas, recommendations, dan quick access
 * 
 * path: app/Http/Controllers/Student/DashboardController.php
 */
class DashboardController extends Controller
{
    /**
     * tampilkan dashboard mahasiswa dengan data lengkap
     */
    public function index()
    {
        $student = Auth::user()->student;

        // statistik utama untuk cards di atas
        $stats = [
            'active_projects' => Project::where('student_id', $student->id)
                                       ->where('status', 'active')
                                       ->count(),
            'total_applications' => Application::where('student_id', $student->id)->count(),
            'pending_applications' => Application::where('student_id', $student->id)
                                                 ->where('status', 'pending')
                                                 ->count(),
            'completed_projects' => Project::where('student_id', $student->id)
                                          ->where('status', 'completed')
                                          ->count(),
        ];

        // proyek aktif dengan progress dan milestone untuk section "Proyek Aktif"
        $activeProjects = Project::where('student_id', $student->id)
                                ->where('status', 'active')
                                ->with([
                                    'problem',
                                    'institution',
                                    'milestones' => function($query) {
                                        $query->orderBy('target_date');
                                    }
                                ])
                                ->latest()
                                ->take(3)
                                ->get();

        // aplikasi terbaru untuk section "Aplikasi Terbaru"
        $recentApplications = Application::where('student_id', $student->id)
                                        ->with([
                                            'problem.institution',
                                            'problem.province',
                                            'problem.regency'
                                        ])
                                        ->latest()
                                        ->take(5)
                                        ->get();

        // rekomendasi proyek berdasarkan jurusan dan skills untuk sidebar
        // gunakan whereRaw untuk comparison dengan subquery
        $recommendedProblems = Problem::where('status', 'open')
                                     ->where('application_deadline', '>=', Carbon::now())
                                     ->with([
                                         'institution',
                                         'province',
                                         'regency',
                                         'images' => function($query) {
                                             $query->where('is_primary', true);
                                         }
                                     ])
                                     ->when($student->major, function($query) use ($student) {
                                         // filter berdasarkan jurusan jika ada
                                         $query->where(function($q) use ($student) {
                                             $q->whereJsonContains('required_majors', $student->major)
                                               ->orWhereNull('required_majors');
                                         });
                                     })
                                     ->when($student->skills, function($query) use ($student) {
                                         // filter berdasarkan skills jika ada
                                         $skills = json_decode($student->skills, true);
                                         if ($skills && count($skills) > 0) {
                                             $query->where(function($q) use ($skills) {
                                                 foreach($skills as $skill) {
                                                     $q->orWhereJsonContains('required_skills', $skill);
                                                 }
                                             });
                                         }
                                     })
                                     ->withCount('applications')
                                     // filter problems yang masih punya slot kosong
                                     ->whereRaw('(SELECT COUNT(*) FROM applications WHERE applications.problem_id = problems.id) < problems.required_students')
                                     ->inRandomOrder()
                                     ->take(4)
                                     ->get();

        // notifikasi yang belum dibaca
        $unreadNotifications = Notification::where('user_id', Auth::id())
                                          ->whereNull('read_at')
                                          ->latest()
                                          ->take(5)
                                          ->get();

        // milestone yang akan datang dari active projects
        $upcomingMilestones = collect();
        foreach ($activeProjects as $project) {
            $milestones = $project->milestones()
                                 ->where('status', '!=', 'completed')
                                 ->where('target_date', '>=', Carbon::now())
                                 ->orderBy('target_date')
                                 ->take(3)
                                 ->get();
            $upcomingMilestones = $upcomingMilestones->merge($milestones);
        }
        $upcomingMilestones = $upcomingMilestones->sortBy('target_date')->take(5);

        // kelengkapan profil untuk mengingatkan user melengkapi data
        $profileCompletion = $this->calculateProfileCompletion($student);

        return view('student.dashboard.index', compact(
            'student',
            'stats',
            'activeProjects',
            'recentApplications',
            'recommendedProblems',
            'unreadNotifications',
            'upcomingMilestones',
            'profileCompletion'
        ));
    }

    /**
     * hitung persentase kelengkapan profil mahasiswa
     * 
     * @param \App\Models\Student $student
     * @return array
     */
    private function calculateProfileCompletion($student)
    {
        // field-field yang dicek untuk kelengkapan profil
        $fields = [
            'profile_photo' => $student->profile_photo_path ? 1 : 0,
            'bio' => $student->bio ? 1 : 0,
            'skills' => $student->skills ? 1 : 0,
            'whatsapp' => $student->whatsapp_number ? 1 : 0,
            'semester' => $student->semester ? 1 : 0,
        ];

        $completed = array_sum($fields);
        $total = count($fields);
        $percentage = ($completed / $total) * 100;

        return [
            'percentage' => round($percentage),
            'fields' => $fields,
            'is_complete' => $percentage == 100,
            'missing_fields' => array_keys(array_filter($fields, fn($val) => $val === 0))
        ];
    }
}