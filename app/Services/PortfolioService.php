<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Project;
use App\Models\Review;
use Illuminate\Support\Str;

/**
 * service untuk mengelola data portfolio mahasiswa
 * 
 * path: app/Services/PortfolioService.php
 */
class PortfolioService
{
    /**
     * ambil data portfolio lengkap untuk student
     */
    public function getPortfolioData($studentId)
    {
        $student = Student::with(['user', 'university'])->findOrFail($studentId);

        // ambil SEMUA completed projects (untuk private view, tampilkan semua)
        // user bisa toggle visibility untuk menampilkan/menyembunyikan di public profile
        $completedProjects = Project::with([
            'problem.institution',
            'problem.province',
            'problem.regency',
            'institutionReview'
        ])
        ->where('student_id', $studentId)
        ->where('status', 'completed')
        // REMOVED: ->portfolioVisible() untuk private view
        ->orderBy('actual_end_date', 'desc')
        ->get();

        // ambil reviews
        $reviews = Review::where('type', 'institution_to_student')
            ->whereIn('project_id', $completedProjects->pluck('id'))
            ->get();

        // hitung total impact beneficiaries
        $totalImpactBeneficiaries = $this->calculateTotalImpactBeneficiaries($completedProjects);

        // hitung statistik
        $statistics = [
            'completed_projects' => $completedProjects->count(),
            'sdgs_addressed' => $this->countUniqueSDGs($completedProjects),
            'total_impact_beneficiaries' => $totalImpactBeneficiaries,
            'positive_reviews' => $reviews->where('rating', '>=', 4)->count(),
            'average_rating' => $reviews->isEmpty() ? 0 : round($reviews->avg('rating'), 1),
        ];

        // generate skills dari completed projects
        $skills = $this->generateSkillsFromProjects($completedProjects);

        return [
            'student' => $student,
            'completedProjects' => $completedProjects,
            'completed_projects' => $completedProjects, // backward compatibility
            'reviews' => $reviews,
            'statistics' => $statistics,
            'skills' => $skills,
        ];
    }

    /**
     * ambil public portfolio berdasarkan username
     */
    public function getPublicPortfolio($username)
    {
        // cari student berdasarkan username
        $student = Student::whereHas('user', function($query) use ($username) {
            $query->where('username', $username);
        })
        ->with(['user', 'university'])
        ->firstOrFail();

        // ambil completed projects yang visible
        $completedProjects = Project::with([
            'problem.institution',
            'problem.province',
            'problem.regency',
            'institutionReview'
        ])
        ->where('student_id', $student->id)
        ->where('status', 'completed')
        ->portfolioVisible()
        ->orderBy('actual_end_date', 'desc')
        ->get();

        // ambil reviews
        $reviews = Review::where('type', 'institution_to_student')
            ->whereIn('project_id', $completedProjects->pluck('id'))
            ->get();

        // hitung total impact beneficiaries
        $totalImpactBeneficiaries = $this->calculateTotalImpactBeneficiaries($completedProjects);

        // hitung statistik
        $statistics = [
            'completed_projects' => $completedProjects->count(),
            'sdgs_addressed' => $this->countUniqueSDGs($completedProjects),
            'total_impact_beneficiaries' => $totalImpactBeneficiaries,
            'positive_reviews' => $reviews->where('rating', '>=', 4)->count(),
            'average_rating' => $reviews->isEmpty() ? 0 : round($reviews->avg('rating'), 1),
        ];

        // generate skills
        $skills = $this->generateSkillsFromProjects($completedProjects);

        return [
            'student' => $student,
            'completedProjects' => $completedProjects,
            'completed_projects' => $completedProjects, // backward compatibility
            'reviews' => $reviews,
            'statistics' => $statistics,
            'skills' => $skills,
        ];
    }

    /**
     * generate portfolio slug untuk student
     */
    public function generatePortfolioSlug(Student $student)
    {
        return $student->user->username;
    }

    /**
     * toggle visibility proyek di portfolio
     */
    public function toggleProjectVisibility($projectId)
    {
        $project = Project::findOrFail($projectId);
        $project->is_portfolio_visible = !$project->is_portfolio_visible;
        $project->save();

        return $project;
    }

    /**
     * hitung jumlah unique SDGs dari completed projects
     */
    private function countUniqueSDGs($completedProjects)
    {
        $sdgs = [];
        
        foreach ($completedProjects as $project) {
            if ($project->problem && $project->problem->sdg_categories) {
                $categories = $project->problem->sdg_categories;
                
                // pastikan dalam bentuk array
                if (is_string($categories)) {
                    $categories = json_decode($categories, true) ?? [];
                }
                
                if (is_array($categories)) {
                    $sdgs = array_merge($sdgs, $categories);
                }
            }
        }
        
        return count(array_unique($sdgs));
    }

    /**
     * hitung total impact beneficiaries dari completed projects
     */
    private function calculateTotalImpactBeneficiaries($completedProjects)
    {
        $totalBeneficiaries = 0;

        foreach ($completedProjects as $project) {
            if ($project->impact_metrics) {
                $impactMetrics = $project->impact_metrics;
                
                // pastikan dalam bentuk array
                if (is_string($impactMetrics)) {
                    $impactMetrics = json_decode($impactMetrics, true) ?? [];
                }
                
                if (is_array($impactMetrics) && isset($impactMetrics['beneficiaries'])) {
                    $totalBeneficiaries += intval($impactMetrics['beneficiaries']);
                }
            }
        }

        return $totalBeneficiaries;
    }

    /**
     * generate skills dari completed projects
     */
    private function generateSkillsFromProjects($completedProjects)
    {
        $skills = [];
        
        foreach ($completedProjects as $project) {
            if ($project->problem && $project->problem->required_skills) {
                $requiredSkills = $project->problem->required_skills;
                
                // pastikan dalam bentuk array
                if (is_string($requiredSkills)) {
                    $requiredSkills = json_decode($requiredSkills, true) ?? [];
                }
                
                if (is_array($requiredSkills)) {
                    $skills = array_merge($skills, $requiredSkills);
                }
            }
        }
        
        return array_unique($skills);
    }
}