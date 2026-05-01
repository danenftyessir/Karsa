<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\ProjectReport;
use App\Services\ProjectService;
use App\Services\PortfolioService;
use App\Services\AIProjectSuggestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * controller untuk mengelola proyek mahasiswa
 * 
 * path: app/Http/Controllers/Student/MyProjectsController.php
 */
class MyProjectsController extends Controller
{
    protected $projectService;
    protected $portfolioService;
    protected $aiSuggestionService;

    public function __construct(
        ProjectService $projectService,
        PortfolioService $portfolioService,
        AIProjectSuggestionService $aiSuggestionService
    ) {
        $this->projectService = $projectService;
        $this->portfolioService = $portfolioService;
        $this->aiSuggestionService = $aiSuggestionService;
    }

    /**
     * tampilkan halaman my projects
     */
    public function index(Request $request)
    {
        $student = Auth::user()->student;

        $query = Project::where('student_id', $student->id)
                       ->with(['problem.images', 'institution', 'milestones', 'reports']);

        // filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'completed') {
                $query->completed();
            } else {
                $query->where('status', $request->status);
            }
        }

        // sort
        if ($request->filled('sort')) {
            if ($request->sort === 'oldest') {
                $query->oldest();
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $projects = $query->paginate(6);

        // statistik
        $stats = $this->projectService->getStudentStats($student->id);

        // generate AI suggestions untuk setiap project
        $aiSuggestions = [];
        foreach ($projects as $project) {
            $aiSuggestions[$project->id] = $this->aiSuggestionService->generateProjectSuggestion($project);
        }

        return view('student.projects.index', compact('projects', 'stats', 'aiSuggestions'));
    }

    /**
     * tampilkan detail proyek
     */
    public function show($id)
    {
        $student = Auth::user()->student;

        $project = Project::where('id', $id)
                         ->where('student_id', $student->id)
                         ->with([
                             'problem.images',
                             'institution',
                             'milestones',
                             'reports' => function($query) {
                                 $query->latest()->limit(5);
                             }
                         ])
                         ->firstOrFail();

        // get team members yang bekerja di problem yang sama
        $teamMembers = $this->getTeamMembers($student, $project);

        return view('student.projects.show', compact('project', 'teamMembers'));
    }

    /**
     * update milestone progress
     */
    public function updateMilestone(Request $request, $milestoneId)
    {
        $student = Auth::user()->student;

        $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        try {
            // Security check: pastikan milestone milik proyek student ini
            $milestone = ProjectMilestone::findOrFail($milestoneId);
            $project = Project::where('id', $milestone->project_id)
                             ->where('student_id', $student->id)
                             ->firstOrFail();

            $milestone = $this->projectService->updateMilestoneProgress(
                $milestoneId,
                $request->progress_percentage,
                $request->notes
            );

            // Update project progress
            $project->updateProgress();

            return response()->json([
                'success' => true,
                'message' => 'Progress milestone berhasil diupdate',
                'milestone' => $milestone,
                'project_progress' => $project->fresh()->progress_percentage,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update milestone: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * tampilkan form create report
     */
    public function createReport($projectId)
    {
        $student = Auth::user()->student;
        
        $project = Project::where('id', $projectId)
                         ->where('student_id', $student->id)
                         ->firstOrFail();

        return view('student.projects.create-report', compact('project'));
    }

    /**
     * submit progress report
     */
    public function storeReport(Request $request, $projectId)
    {
        $student = Auth::user()->student;
        
        $project = Project::where('id', $projectId)
                         ->where('student_id', $student->id)
                         ->firstOrFail();

        $request->validate([
            'type' => 'required|in:weekly,monthly',
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'activities' => 'required|string',
            'challenges' => 'nullable|string',
            'next_plans' => 'nullable|string',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $data = $request->only([
                'type', 'title', 'summary', 'activities', 
                'challenges', 'next_plans', 'period_start', 'period_end'
            ]);
            
            $data['project_id'] = $project->id;
            $data['student_id'] = $student->id;

            $report = $this->projectService->submitReport(
                $project->id,
                $data,
                $request->file('document'),
                $request->file('photos')
            );

            return redirect()
                ->route('student.projects.show', $project->id)
                ->with('success', 'Laporan progress berhasil dikirim');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal mengirim laporan: ' . $e->getMessage());
        }
    }

    /**
     * tampilkan form final report
     */
    public function createFinalReport($projectId)
    {
        $student = Auth::user()->student;
        
        $project = Project::where('id', $projectId)
                         ->where('student_id', $student->id)
                         ->active()
                         ->firstOrFail();

        return view('student.projects.create-final-report', compact('project'));
    }

    /**
     * submit final report
     */
    public function storeFinalReport(Request $request, $projectId)
    {
        $student = Auth::user()->student;
        
        $project = Project::where('id', $projectId)
                         ->where('student_id', $student->id)
                         ->active()
                         ->firstOrFail();

        $request->validate([
            'summary' => 'required|string',
            'activities' => 'required|string',
            'final_report' => 'required|file|mimes:pdf|max:20480',
            'beneficiaries' => 'nullable|integer|min:0',
            'activities_count' => 'nullable|integer|min:0',
        ]);

        try {
            $data = [
                'summary' => $request->summary,
                'activities' => $request->activities,
                'impact_metrics' => [
                    'beneficiaries' => $request->beneficiaries ?? 0,
                    'activities' => $request->activities_count ?? 0,
                ],
            ];

            $this->projectService->submitFinalReport(
                $project->id,
                $data,
                $request->file('final_report')
            );

            // complete project
            $this->projectService->completeProject($project->id);

            return redirect()
                ->route('student.projects.show', $project->id)
                ->with('success', 'Laporan akhir berhasil dikirim. Proyek telah selesai!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal mengirim laporan akhir: ' . $e->getMessage());
        }
    }

    /**
     * download report document
     */
    public function downloadReport($reportId)
    {
        $student = Auth::user()->student;

        $report = ProjectReport::where('id', $reportId)
                              ->where('student_id', $student->id)
                              ->firstOrFail();

        if (!$report->document_path) {
            abort(404, 'Dokumen tidak ditemukan');
        }

        return response()->download(
            storage_path('app/public/' . $report->document_path)
        );
    }

    /**
     * toggle portfolio visibility untuk proyek
     */
    public function togglePortfolioVisibility($projectId)
    {
        $student = Auth::user()->student;

        $project = Project::where('id', $projectId)
                         ->where('student_id', $student->id)
                         ->where('status', 'completed') // hanya completed projects
                         ->firstOrFail();

        try {
            $project = $this->portfolioService->toggleProjectVisibility($projectId);

            return response()->json([
                'success' => true,
                'message' => $project->is_portfolio_visible
                    ? 'Proyek berhasil ditambahkan ke portfolio'
                    : 'Proyek berhasil dihapus dari portfolio',
                'is_visible' => $project->is_portfolio_visible,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah visibility portfolio: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * get team members yang bekerja pada problem yang sama dengan proyek ini
     *
     * @param \App\Models\Student $student
     * @param \App\Models\Project|null $project
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTeamMembers($student, $project = null)
    {
        // jika tidak ada project spesifik, return empty collection
        if (!$project) {
            return collect([]);
        }

        // cari mahasiswa lain yang bekerja di problem yang sama
        return \App\Models\Student::select('id', 'user_id', 'university_id', 'profile_photo_path', 'first_name', 'last_name')
            ->whereHas('projects', function($query) use ($project) {
                $query->where('problem_id', $project->problem_id)
                      ->whereIn('status', ['active', 'completed']); // hanya yang aktif atau selesai
            })
            ->where('id', '!=', $student->id) // exclude diri sendiri
            ->with(['user:id,name', 'university:id,name'])
            ->limit(6)
            ->get();
    }
}
