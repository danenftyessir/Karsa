<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Services\ProjectService;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * controller untuk mengelola proyek yang sedang berjalan
 * instansi dapat monitor progress, manage milestones, dan berikan feedback
 */
class ProjectManagementController extends Controller
{
    protected $projectService;
    protected $certificateService;

    public function __construct(ProjectService $projectService, CertificateService $certificateService)
    {
        $this->projectService = $projectService;
        $this->certificateService = $certificateService;
    }

    /**
     * tampilkan daftar semua proyek
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;

        $query = Project::with(['student.user', 'student.university', 'problem', 'milestones'])
                       ->where('institution_id', $institution->id);

        // filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // filter berdasarkan problem
        if ($request->filled('problem_id')) {
            $query->where('problem_id', $request->problem_id);
        }

        // search berdasarkan nama mahasiswa atau judul
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhereHas('student.user', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(15);

        // hitung jumlah project berdasarkan status untuk summary cards
        $baseQuery = Project::where('institution_id', $institution->id);

        // statistik untuk summary cards
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'planning' => 0, // fix: tambahkan key 'planning' dengan value 0 karena tidak ada status planning di database
            'active' => (clone $baseQuery)->where('status', 'active')->count(),
            'review' => 0, // fix: tambahkan key 'review' dengan value 0 karena tidak ada status review di database
            'on_hold' => (clone $baseQuery)->where('status', 'on_hold')->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
        ];

        // daftar problems untuk filter dropdown
        $problems = $institution->problems()->orderBy('title')->get(['id', 'title']);

        return view('institution.projects.index', compact('projects', 'stats', 'problems'));
    }

    /**
     * tampilkan detail proyek untuk monitoring
     */
    public function show($id)
    {
        $institution = auth()->user()->institution;

        $project = Project::with([
            'student.user',
            'student.university',
            'problem.images',
            'problem.province',
            'problem.regency',
            'milestones',
            'reports.student.user'
        ])
        ->where('institution_id', $institution->id)
        ->findOrFail($id);

        // hitung progress
        $totalMilestones = $project->milestones()->count();
        $completedMilestones = $project->milestones()->where('status', 'completed')->count();
        $progressPercentage = $totalMilestones > 0 ? ($completedMilestones / $totalMilestones) * 100 : 0;

        return view('institution.projects.show', compact('project', 'progressPercentage'));
    }

    /**
     * tampilkan halaman manage proyek (milestone, feedback, dll)
     */
    public function manage($id)
    {
        $institution = auth()->user()->institution;

        $project = Project::with([
            'student.user',
            'student.university',
            'problem',
            'milestones',
            'reports'
        ])
        ->where('institution_id', $institution->id)
        ->findOrFail($id);

        return view('institution.projects.manage', compact('project'));
    }

    /**
     * update status proyek
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $institution = auth()->user()->institution;

            $project = Project::where('institution_id', $institution->id)->findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:active,on_hold,completed,cancelled',
                'notes' => 'nullable|string|max:500',
            ]);

            // DEBUG: Log status change
            Log::info("📊 Status Update Request", [
                'project_id' => $project->id,
                'old_status' => $project->status,
                'new_status' => $validated['status'],
                'has_certificate' => !empty($project->certificate_path)
            ]);

            // jika status berubah menjadi completed, set actual_end_date
            $data = ['status' => $validated['status']];
            $isNewlyCompleted = false;

            if ($validated['status'] === 'completed' && $project->status !== 'completed') {
                $data['actual_end_date'] = now();
                $data['completed_at'] = now();
                $data['progress_percentage'] = 100; // set progress ke 100% saat completed
                $isNewlyCompleted = true;
                Log::info("✅ Project marked as newly completed - will generate certificate");
            } else {
                Log::info("⏭️ Skipping certificate generation", [
                    'reason' => $validated['status'] === 'completed' ? 'Already completed' : 'Status not completed'
                ]);
            }

            $project->update($data);

            // 🎓 AUTO-GENERATE SERTIFIKAT saat proyek selesai
            if ($isNewlyCompleted) {
                try {
                    Log::info("🎓 Generating certificate for project {$project->id}");
                    $result = $this->certificateService->generateCertificate($project);

                    if ($result['success']) {
                        Log::info("✅ Certificate generated successfully: {$result['number']}");
                    } else {
                        Log::warning("⚠️ Certificate generation failed: {$result['message']}");
                    }
                } catch (\Exception $e) {
                    Log::error("❌ Certificate generation exception: " . $e->getMessage());
                    Log::error($e->getTraceAsString());
                    // Jangan failed update status meskipun sertifikat gagal dibuat
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'status proyek berhasil diubah',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error updating project status: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * tambah milestone baru
     */
    public function addMilestone(Request $request, $id)
    {
        $institution = auth()->user()->institution;

        $project = Project::where('institution_id', $institution->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_date' => 'required|date',
            'deliverables' => 'nullable|string',
        ]);

        // hitung order
        $maxOrder = $project->milestones()->max('order') ?? 0;

        $milestone = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'target_date' => $validated['target_date'],
            'deliverables' => isset($validated['deliverables']) && $validated['deliverables'] ? explode(',', $validated['deliverables']) : null,
            'order' => $maxOrder + 1,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('institution.projects.manage', $project->id)
            ->with('success', 'Milestone berhasil ditambahkan');
    }

    /**
     * get single milestone data
     */
    public function getMilestone($id, $milestoneId)
    {
        $institution = auth()->user()->institution;

        $project = Project::where('institution_id', $institution->id)->findOrFail($id);
        $milestone = $project->milestones()->findOrFail($milestoneId);

        return response()->json([
            'success' => true,
            'milestone' => $milestone,
        ]);
    }

    /**
     * update milestone
     */
    public function updateMilestone(Request $request, $id, $milestoneId)
    {
        $institution = auth()->user()->institution;

        $project = Project::where('institution_id', $institution->id)->findOrFail($id);
        $milestone = $project->milestones()->findOrFail($milestoneId);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_date' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,completed,delayed',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        // jika status berubah menjadi completed
        if (isset($validated['status']) && $validated['status'] === 'completed' && $milestone->status !== 'completed') {
            $validated['completed_at'] = now();
            $validated['progress_percentage'] = 100;
        }

        $milestone->update($validated);

        // update progress proyek
        $this->projectService->updateProjectProgress($project->id);

        return response()->json([
            'success' => true,
            'message' => 'milestone berhasil diupdate',
            'milestone' => $milestone->fresh(),
        ]);
    }

    /**
     * delete milestone
     */
    public function deleteMilestone($id, $milestoneId)
    {
        $institution = auth()->user()->institution;

        $project = Project::where('institution_id', $institution->id)->findOrFail($id);
        $milestone = $project->milestones()->findOrFail($milestoneId);

        // tidak bisa delete milestone yang sudah completed
        if ($milestone->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'tidak dapat menghapus milestone yang sudah selesai',
            ], 400);
        }

        $milestone->delete();

        // update progress proyek
        $this->projectService->updateProjectProgress($project->id);

        return response()->json([
            'success' => true,
            'message' => 'milestone berhasil dihapus',
        ]);
    }

    /**
     * berikan feedback untuk report
     */
    public function giveFeedback(Request $request, $id, $reportId)
    {
        $institution = auth()->user()->institution;

        $project = Project::where('institution_id', $institution->id)->findOrFail($id);
        $report = $project->reports()->findOrFail($reportId);

        $validated = $request->validate([
            'feedback' => 'required|string|max:1000',
            'status' => 'required|in:approved,revision_needed',
        ]);

        $report->update([
            'institution_feedback' => $validated['feedback'],
            'status' => $validated['status'],
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'feedback berhasil diberikan',
        ]);
    }

    /**
     * approve final report
     */
    public function approveFinalReport(Request $request, $id)
    {
        $institution = auth()->user()->institution;

        $project = Project::where('institution_id', $institution->id)->findOrFail($id);

        // pastikan project sudah completed
        if ($project->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'proyek belum selesai',
            ], 400);
        }

        $validated = $request->validate([
            'feedback' => 'nullable|string|max:1000',
        ]);

        $project->update([
            'final_report_approved' => true,
            'final_report_feedback' => $validated['feedback'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'laporan akhir berhasil disetujui',
        ]);
    }
    /**
     * approve report dari mahasiswa
     */
    public function approveReport(Request $request, $id, $reportId)
    {
        $institution = auth()->user()->institution;

        $project = Project::where('institution_id', $institution->id)->findOrFail($id);
        $report = $project->reports()->findOrFail($reportId);

        $validated = $request->validate([
            'feedback' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status' => 'approved',
            'institution_feedback' => $validated['feedback'] ?? 'Laporan disetujui',
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('institution.projects.manage', $project->id)
            ->with('success', 'Laporan berhasil disetujui');
    }

    /**
     * reject report / minta revisi dari mahasiswa
     */
    public function rejectReport(Request $request, $id, $reportId)
    {
        $institution = auth()->user()->institution;

        $project = Project::where('institution_id', $institution->id)->findOrFail($id);
        $report = $project->reports()->findOrFail($reportId);

        $validated = $request->validate([
            'feedback' => 'required|string|max:1000',
        ]);

        $report->update([
            'status' => 'revision_needed',
            'institution_feedback' => $validated['feedback'],
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('institution.projects.manage', $project->id)
            ->with('success', 'Feedback revisi berhasil dikirim');
    }
}