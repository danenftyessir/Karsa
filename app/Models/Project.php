<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * model untuk tabel projects
 * mengelola proyek KKN yang sedang/sudah dikerjakan mahasiswa
 * 
 * path: app/Models/Project.php
 */
class Project extends Model
{
    use HasFactory, SoftDeletes;

    // ✅ PERBAIKAN: hapus 'completed_at' dari fillable karena column tidak ada di migration
    protected $fillable = [
        'application_id',
        'student_id',
        'problem_id',
        'institution_id',
        'title',
        'description',
        'status',
        'start_date',
        'end_date',
        'actual_start_date',
        'actual_end_date',
        'progress_percentage',
        'final_report_path',
        'final_report_summary',
        'submitted_at',
        'rating',
        'institution_review',
        'reviewed_at',
        'impact_metrics',
        'is_portfolio_visible',
        'is_featured',
        'role_in_team',
        // Certificate fields
        'certificate_path',
        'certificate_number',
        'certificate_generated_at',
    ];

    // ✅ PERBAIKAN: hapus 'completed_at' dari casts
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'certificate_generated_at' => 'datetime',
        'impact_metrics' => 'array',
        'is_portfolio_visible' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * relasi ke application
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * relasi ke student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * relasi ke problem
     */
    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }

    /**
     * relasi ke institution
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * relasi ke milestones
     */
    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('order');
    }

    /**
     * relasi ke reports
     */
    public function reports()
    {
        return $this->hasMany(ProjectReport::class);
    }

    /**
     * relasi ke review dari institution
     */
    public function institutionReview()
    {
        return $this->hasOne(Review::class)->where('type', 'institution_to_student');
    }

    /**
     * scope untuk filter by status
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * scope untuk ongoing projects
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    /**
     * scope untuk completed projects
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * scope untuk active projects
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * scope untuk portfolio visible projects
     */
    public function scopePortfolioVisible($query)
    {
        return $query->where('is_portfolio_visible', true);
    }

    /**
     * update progress percentage berdasarkan milestones
     */
    public function updateProgress()
    {
        $totalMilestones = $this->milestones()->count();
        
        if ($totalMilestones === 0) {
            return;
        }

        $completedMilestones = $this->milestones()->completed()->count();
        $progressPercentage = round(($completedMilestones / $totalMilestones) * 100);

        $this->update(['progress_percentage' => $progressPercentage]);
    }

    /**
     * mark project as completed
     * ✅ PERBAIKAN: hapus field completed_at, gunakan actual_end_date saja
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'actual_end_date' => now(),
            'progress_percentage' => 100,
        ]);
    }

    /**
     * submit final report
     */
    public function submitFinalReport($filePath, $summary)
    {
        $this->update([
            'final_report_path' => $filePath,
            'final_report_summary' => $summary,
            'submitted_at' => now(),
        ]);
    }

    /**
     * set institution review and rating
     */
    public function setReview($rating, $review)
    {
        $this->update([
            'rating' => $rating,
            'institution_review' => $review,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * cek apakah project overdue
     */
    public function getIsOverdueAttribute()
    {
        if ($this->status === 'completed' || $this->status === 'cancelled') {
            return false;
        }
        
        return now()->isAfter($this->end_date);
    }

    /**
     * hitung days remaining
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->status === 'completed' || $this->status === 'cancelled') {
            return 0;
        }
        
        $days = now()->diffInDays($this->end_date, false);
        return max(0, ceil($days));
    }

    /**
     * get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'blue',
            'on_hold' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * get status label in indonesian
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'active' => 'Aktif',
            'on_hold' => 'Ditunda',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };
    }

    /**
     * get project duration in months
     */
    public function getDurationInMonthsAttribute()
    {
        return $this->start_date->diffInMonths($this->end_date);
    }

    /**
     * calculate completion percentage
     */
    public function calculateCompletionPercentage()
    {
        $totalDays = $this->start_date->diffInDays($this->end_date);
        $elapsedDays = $this->start_date->diffInDays(now());
        
        if ($totalDays <= 0) {
            return 100;
        }
        
        return min(100, round(($elapsedDays / $totalDays) * 100));
    }
}