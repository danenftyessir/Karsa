<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk memberikan saran AI proaktif pada proyek mahasiswa
 *
 * path: app/Services/AIProjectSuggestionService.php
 */
class AIProjectSuggestionService
{
    private $anthropicApiKey;
    private $model = 'claude-3-5-sonnet-20241022';

    public function __construct()
    {
        $this->anthropicApiKey = config('services.anthropic.api_key');
    }

    /**
     * Generate saran AI berdasarkan status dan progress proyek
     */
    public function generateProjectSuggestion(Project $project): string
    {
        try {
            // Jika proyek completed, return congratulation message
            if ($project->status === 'completed') {
                return "Proyek telah selesai. Pertimbangkan untuk menambahkan ke portfolio agar meningkatkan kredibilitas profesional Anda.";
            }

            // Jika proyek on hold
            if ($project->status === 'on_hold') {
                return "Proyek dalam status tertunda. Koordinasikan dengan institusi untuk menentukan langkah lanjutan atau update status proyek.";
            }

            // Load milestones untuk context
            $totalMilestones = $project->milestones()->count();
            $completedMilestones = $project->milestones()->where('is_completed', true)->count();
            $latestReport = $project->reports()->latest()->first();
            $daysUntilDeadline = $project->days_remaining;

            // Build context untuk AI
            $context = $this->buildProjectContext($project, $totalMilestones, $completedMilestones, $latestReport);

            // Call Anthropic API
            $suggestion = $this->callAnthropicAPI($context);

            return $suggestion ?: $this->getFallbackSuggestion($project, $totalMilestones, $completedMilestones, $latestReport);

        } catch (\Exception $e) {
            Log::error('AI Project Suggestion Error: ' . $e->getMessage());
            return $this->getFallbackSuggestion($project, 0, 0, null);
        }
    }

    /**
     * Build context untuk AI berdasarkan data proyek
     */
    private function buildProjectContext(Project $project, int $totalMilestones, int $completedMilestones, $latestReport): string
    {
        $daysRemaining = $project->days_remaining;
        $isOverdue = $project->is_overdue;
        $progress = $project->progress_percentage;

        $context = "Proyek KKN dengan detail berikut:\n";
        $context .= "- Status: {$project->status}\n";
        $context .= "- Progress: {$progress}%\n";
        $context .= "- Total Milestone: {$totalMilestones}\n";
        $context .= "- Milestone Selesai: {$completedMilestones}\n";
        $context .= "- Hari Tersisa: " . ($isOverdue ? "Overdue" : "{$daysRemaining} hari") . "\n";
        $context .= "- Laporan Terakhir: " . ($latestReport ? $latestReport->created_at->diffForHumans() : "Belum ada") . "\n";

        return $context;
    }

    /**
     * Call Anthropic Claude API untuk generate suggestion
     */
    private function callAnthropicAPI(string $context): ?string
    {
        if (!$this->anthropicApiKey) {
            return null;
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->anthropicApiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(15)->post('https://api.anthropic.com/v1/messages', [
            'model' => $this->model,
            'max_tokens' => 200,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Anda adalah konsultan manajemen proyek untuk platform KKN profesional. Berikan 1 saran strategis dan spesifik dalam Bahasa Indonesia formal untuk mahasiswa berdasarkan data proyek berikut:\n\n{$context}\n\nPersyaratan:\n- JANGAN gunakan emoji\n- Maksimal 25 kata\n- Fokus pada actionable next steps yang konkret\n- Gunakan bahasa profesional dan terukur\n- Berikan insight yang valuable, bukan sekedar motivasi umum\n- Sebutkan angka/metrik spesifik jika relevan\n\nContoh baik: \"Dengan 3 milestone tersisa dan deadline 15 hari, prioritaskan penyelesaian milestone dokumentasi untuk menjaga momentum.\"\nContoh buruk: \"Terus semangat mengerjakan proyek ya!\""
                ]
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $suggestion = $data['content'][0]['text'] ?? null;

            // Clean up the suggestion
            if ($suggestion) {
                $suggestion = trim($suggestion);
                // Remove any emojis that might slip through
                $suggestion = preg_replace('/[\x{1F300}-\x{1F9FF}]/u', '', $suggestion);
                // Pastikan tidak terlalu panjang
                if (strlen($suggestion) > 250) {
                    $suggestion = substr($suggestion, 0, 250) . '...';
                }
                return $suggestion;
            }
        }

        return null;
    }

    /**
     * Get fallback suggestion jika AI tidak available
     */
    private function getFallbackSuggestion(Project $project, int $totalMilestones, int $completedMilestones, $latestReport): string
    {
        $progress = $project->progress_percentage;
        $isOverdue = $project->is_overdue;
        $daysRemaining = $project->days_remaining;
        $remainingMilestones = $totalMilestones - $completedMilestones;
        $daysSinceLastReport = $latestReport ? now()->diffInDays($latestReport->created_at) : null;

        // Overdue - critical
        if ($isOverdue) {
            $overdueBy = abs($daysRemaining);
            return "Proyek telah melewati deadline {$overdueBy} hari. Koordinasi segera dengan institusi untuk extension atau percepatan penyelesaian.";
        }

        // Critical deadline proximity dengan progress rendah
        if ($daysRemaining <= 7 && $progress < 80) {
            return "Tersisa {$daysRemaining} hari dengan progress {$progress}%. Fokuskan pada {$remainingMilestones} milestone kritis untuk mencapai minimum viable completion.";
        }

        // Laporan overdue
        if ($daysSinceLastReport && $daysSinceLastReport > 14 && $progress < 100) {
            return "Tidak ada laporan sejak {$daysSinceLastReport} hari yang lalu. Submit progress report untuk menjaga transparansi dengan institusi.";
        }

        // Progress sangat rendah dengan banyak milestone
        if ($progress < 20 && $totalMilestones > 0) {
            return "Progress {$progress}% dengan {$totalMilestones} milestone terdaftar. Mulai eksekusi milestone pertama dan dokumentasikan baseline metrics.";
        }

        // Progress sedang - perlu acceleration
        if ($progress >= 20 && $progress < 50 && $remainingMilestones > 0) {
            $avgDaysPerMilestone = $remainingMilestones > 0 ? ceil($daysRemaining / $remainingMilestones) : 0;
            return "Progress {$progress}% pada tengah proyek. Alokasikan rata-rata {$avgDaysPerMilestone} hari per milestone untuk mengejar target completion.";
        }

        // Progress tinggi - preparation phase
        if ($progress >= 50 && $progress < 80 && $daysRemaining > 7) {
            return "Progress {$progress}% menunjukkan trajectory baik. Mulai compile dokumentasi dan prepare preliminary impact assessment report.";
        }

        // Almost done - finalization
        if ($progress >= 80 && $progress < 100) {
            return "Progress {$progress}% mendekati completion. Prioritaskan quality assurance, finalisasi deliverables, dan prepare final report submission.";
        }

        // Default - steady progress
        return "Maintain current momentum dengan {$progress}% completion. Ensure regular milestone updates dan periodic stakeholder communication.";
    }

    /**
     * Generate batch suggestions untuk multiple projects
     */
    public function generateBatchSuggestions(array $projects): array
    {
        $suggestions = [];

        foreach ($projects as $project) {
            $suggestions[$project->id] = $this->generateProjectSuggestion($project);
        }

        return $suggestions;
    }
}
