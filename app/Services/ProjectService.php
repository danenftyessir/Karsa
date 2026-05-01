<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Application;
use App\Models\ProjectMilestone;
use App\Models\ProjectReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * service untuk mengelola operasi project
 * berisi business logic untuk project management
 * 
 * path: app/Services/ProjectService.php
 */
class ProjectService
{
    /**
     * buat project baru dari application yang accepted
     */
    public function createFromApplication(Application $application)
    {
        DB::beginTransaction();
        
        try {
            $problem = $application->problem;
            
            // buat project
            $project = Project::create([
                'application_id' => $application->id,
                'student_id' => $application->student_id,
                'problem_id' => $application->problem_id,
                'institution_id' => $problem->institution_id,
                'title' => $problem->title,
                'description' => $problem->description,
                'status' => 'active',
                'start_date' => $problem->start_date,
                'end_date' => $problem->end_date,
                'progress_percentage' => 0,
            ]);

            // buat default milestones
            $this->createDefaultMilestones($project);

            DB::commit();
            
            return $project;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * buat default milestones untuk project baru
     */
    protected function createDefaultMilestones(Project $project)
    {
        $duration = $project->start_date->diffInMonths($project->end_date);
        
        $milestones = [
            [
                'title' => 'Orientasi dan Persiapan',
                'description' => 'Pengenalan lokasi, koordinasi dengan instansi, dan persiapan program',
                'order' => 1,
                'target_date' => $project->start_date->copy()->addWeeks(1),
            ],
            [
                'title' => 'Pelaksanaan Program Utama',
                'description' => 'Implementasi kegiatan sesuai rencana kerja',
                'order' => 2,
                'target_date' => $project->start_date->copy()->addMonths(floor($duration * 0.7)),
            ],
            [
                'title' => 'Monitoring dan Evaluasi',
                'description' => 'Evaluasi progress dan penyesuaian program jika diperlukan',
                'order' => 3,
                'target_date' => $project->start_date->copy()->addMonths(floor($duration * 0.85)),
            ],
            [
                'title' => 'Pelaporan dan Penutupan',
                'description' => 'Penyusunan laporan akhir dan serah terima program',
                'order' => 4,
                'target_date' => $project->end_date->copy()->subWeek(),
            ],
        ];

        foreach ($milestones as $milestone) {
            ProjectMilestone::create(array_merge($milestone, [
                'project_id' => $project->id,
                'status' => 'pending',
                'progress_percentage' => 0,
            ]));
        }
    }

    /**
     * update progress milestone
     */
    public function updateMilestoneProgress($milestoneId, $progress, $notes = null)
    {
        $milestone = ProjectMilestone::findOrFail($milestoneId);
        
        $milestone->update([
            'progress_percentage' => $progress,
            'notes' => $notes,
            'status' => $progress == 100 ? 'completed' : 'in_progress',
            'completed_at' => $progress == 100 ? now() : null,
        ]);

        // update project progress
        $milestone->project->updateProgress();

        return $milestone;
    }

    /**
     * submit progress report
     */
    public function submitReport($projectId, $data, $documentFile = null, $photoFiles = null)
    {
        DB::beginTransaction();
        
        try {
            // upload document jika ada
            if ($documentFile) {
                $data['document_path'] = $documentFile->store('reports/documents', 'public');
            }

            // upload photos jika ada
            if ($photoFiles) {
                $photoPaths = [];
                foreach ($photoFiles as $photo) {
                    $photoPaths[] = $photo->store('reports/photos', 'public');
                }
                $data['photos'] = $photoPaths;
            }

            $report = ProjectReport::create($data);

            DB::commit();
            
            return $report;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * submit final report
     */
    public function submitFinalReport($projectId, $data, $reportFile = null)
    {
        DB::beginTransaction();
        
        try {
            $project = Project::findOrFail($projectId);

            // upload final report
            if ($reportFile) {
                $reportPath = $reportFile->store('projects/final-reports', 'public');
                
                $project->update([
                    'final_report_path' => $reportPath,
                    'final_report_summary' => $data['summary'],
                    'submitted_at' => now(),
                    'impact_metrics' => $data['impact_metrics'] ?? null,
                ]);
            }

            // buat final report entry
            ProjectReport::create([
                'project_id' => $projectId,
                'student_id' => $project->student_id,
                'type' => 'final',
                'title' => 'Laporan Akhir - ' . $project->title,
                'summary' => $data['summary'],
                'activities' => $data['activities'] ?? '',
                'period_start' => $project->actual_start_date ?? $project->start_date,
                'period_end' => now(),
                'document_path' => $reportPath ?? null,
                'status' => 'pending',
            ]);

            DB::commit();
            
            return $project;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * complete project
     */
    public function completeProject($projectId)
    {
        $project = Project::findOrFail($projectId);
        
        $project->update([
            'status' => 'completed',
            'actual_end_date' => now(),
            'progress_percentage' => 100,
        ]);

        return $project;
    }

    /**
     * get project statistics untuk student
     */
    public function getStudentStats($studentId)
    {
        $projects = Project::where('student_id', $studentId)->get();

        return [
            'total' => $projects->count(),
            'active' => $projects->where('status', 'active')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
            'total_reports' => ProjectReport::whereIn('project_id', $projects->pluck('id'))->count(),
            'pending_milestones' => ProjectMilestone::whereIn('project_id', $projects->pluck('id'))
                                                    ->where('status', '!=', 'completed')
                                                    ->count(),
        ];
    }

    /**
     * calculate total impact dari completed projects
     */
    public function calculateTotalImpact($studentId)
    {
        $projects = Project::where('student_id', $studentId)
                          ->completed()
                          ->get();

        $totalBeneficiaries = 0;
        $totalActivities = 0;

        foreach ($projects as $project) {
            if ($project->impact_metrics) {
                $totalBeneficiaries += $project->impact_metrics['beneficiaries'] ?? 0;
                $totalActivities += $project->impact_metrics['activities'] ?? 0;
            }
        }

        return [
            'beneficiaries' => $totalBeneficiaries,
            'activities' => $totalActivities,
        ];
    }
}