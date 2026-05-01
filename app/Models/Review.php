<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * model untuk tabel reviews
 * mengelola review dari institusi ke mahasiswa dan sebaliknya
 * 
 * path: app/Models/Review.php
 */
class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'reviewer_id',
        'reviewee_id',
        'type',
        'rating',
        'professionalism_rating',
        'communication_rating',
        'quality_rating',
        'timeliness_rating',
        'review_text',
        'strengths',
        'improvements',
        'is_public',
        'is_featured',
        'response',
        'responded_at',
    ];

    protected $casts = [
        'rating' => 'float',
        'professionalism_rating' => 'integer',
        'communication_rating' => 'integer',
        'quality_rating' => 'integer',
        'timeliness_rating' => 'integer',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * relasi ke project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * relasi ke reviewer (user yang memberi review)
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * relasi ke reviewee (user yang menerima review)
     */
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    /**
     * scope untuk public reviews
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * scope untuk featured reviews
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * scope untuk filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * scope untuk reviews yang diberikan institution ke student
     */
    public function scopeToStudent($query, $studentUserId)
    {
        return $query->where('type', 'institution_to_student')
                     ->where('reviewee_id', $studentUserId);
    }

    /**
     * scope untuk reviews yang diberikan student ke institution
     */
    public function scopeToInstitution($query, $institutionUserId)
    {
        return $query->where('type', 'student_to_institution')
                     ->where('reviewee_id', $institutionUserId);
    }

    /**
     * scope untuk filter by project
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * hitung average rating dari detail ratings
     * untuk institution_to_student reviews
     */
    public function calculateDetailedRating()
    {
        if ($this->type !== 'institution_to_student') {
            return $this->rating;
        }

        $ratings = [
            $this->professionalism_rating,
            $this->communication_rating,
            $this->quality_rating,
            $this->timeliness_rating,
        ];

        $filteredRatings = array_filter($ratings);
        
        return !empty($filteredRatings) 
            ? round(array_sum($filteredRatings) / count($filteredRatings), 1)
            : 0;
    }

    /**
     * add response to review
     */
    public function addResponse($responseText)
    {
        $this->update([
            'response' => $responseText,
            'responded_at' => now(),
        ]);
    }

    /**
     * get review type label
     */
    public function getTypeLabel()
    {
        return match($this->type) {
            'institution_to_student' => 'Review dari Instansi',
            'student_to_institution' => 'Review dari Mahasiswa',
            default => 'Review',
        };
    }

    /**
     * get rating badge color
     */
    public function getRatingColor()
    {
        if ($this->rating >= 4.5) {
            return 'green';
        } elseif ($this->rating >= 3.5) {
            return 'blue';
        } elseif ($this->rating >= 2.5) {
            return 'yellow';
        } else {
            return 'red';
        }
    }

    /**
     * get rating description
     */
    public function getRatingDescription()
    {
        if ($this->rating >= 4.5) {
            return 'Excellent';
        } elseif ($this->rating >= 3.5) {
            return 'Good';
        } elseif ($this->rating >= 2.5) {
            return 'Fair';
        } else {
            return 'Needs Improvement';
        }
    }
}