<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * model untuk tabel project_milestones
 * mengelola milestone/tahapan proyek
 * 
 * path: app/Models/ProjectMilestone.php
 */
class ProjectMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'order',
        'target_date',
        'completed_at',
        'status',
        'progress_percentage',
        'notes',
        'deliverables',
    ];

    protected $casts = [
        'target_date' => 'date',
        'completed_at' => 'date',
        'deliverables' => 'array',
    ];

    /**
     * relasi ke project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * scope untuk filter milestone pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * scope untuk filter milestone in progress
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * scope untuk filter milestone completed
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * scope untuk filter milestone overdue
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
                     ->where('target_date', '<', now());
    }

    /**
     * mark milestone as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress_percentage' => 100,
        ]);

        // update project progress
        $this->project->updateProgress();
    }

    /**
     * cek apakah milestone overdue
     */
    public function getIsOverdueAttribute()
    {
        if ($this->status === 'completed') {
            return false;
        }
        
        return now()->isAfter($this->target_date);
    }

    /**
     * hitung days remaining
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->status === 'completed') {
            return 0;
        }
        
        $days = now()->diffInDays($this->target_date, false);
        return max(0, $days);
    }

    /**
     * get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'gray',
            'in_progress' => 'blue',
            'completed' => 'green',
            'delayed' => 'red',
            default => 'gray',
        };
    }
}