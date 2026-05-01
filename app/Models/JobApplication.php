<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * JobApplication Model
 *
 * PENTING: Model ini menggunakan Supabase PostgreSQL sebagai database
 * SEMUA operasi CRUD langsung ke Supabase, BUKAN local database
 */
class JobApplication extends Model
{
    use HasFactory;

    // Specify connection to Supabase PostgreSQL
    protected $connection = 'pgsql';
    protected $table = 'job_applications';

    protected $fillable = [
        'job_posting_id',
        'user_id',
        'status',
        'cover_letter',
        'resume_url',
        'portfolio_url',
        'expected_salary',
        'available_from',
        'notes',
        'rating',
        'reviewed_at',
        'reviewed_by',
        'interview_scheduled_at',
        'offer_extended_at',
        'hired_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'expected_salary' => 'decimal:2',
        'available_from' => 'date',
        'rating' => 'integer',
        'reviewed_at' => 'datetime',
        'interview_scheduled_at' => 'datetime',
        'offer_extended_at' => 'datetime',
        'hired_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Status constants
    const STATUS_NEW = 'new';
    const STATUS_REVIEWING = 'reviewing';
    const STATUS_SHORTLISTED = 'shortlisted';
    const STATUS_INTERVIEW = 'interview';
    const STATUS_OFFER = 'offer';
    const STATUS_REJECTED = 'rejected';
    const STATUS_HIRED = 'hired';

    // ========================================================================
    // RELATIONSHIPS
    // ========================================================================

    /**
     * Application belongs to a job posting
     */
    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Application belongs to a user (applicant)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Application reviewed by a user
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ========================================================================
    // SCOPES
    // ========================================================================

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Only new applications
     */
    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    /**
     * Scope: Only reviewing applications
     */
    public function scopeReviewing($query)
    {
        return $query->where('status', self::STATUS_REVIEWING);
    }

    /**
     * Scope: Only shortlisted applications
     */
    public function scopeShortlisted($query)
    {
        return $query->where('status', self::STATUS_SHORTLISTED);
    }

    /**
     * Scope: Only interview stage applications
     */
    public function scopeInterview($query)
    {
        return $query->where('status', self::STATUS_INTERVIEW);
    }

    /**
     * Scope: Only offer stage applications
     */
    public function scopeOffer($query)
    {
        return $query->where('status', self::STATUS_OFFER);
    }

    /**
     * Scope: Only rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope: Only hired applications
     */
    public function scopeHired($query)
    {
        return $query->where('status', self::STATUS_HIRED);
    }

    /**
     * Scope: Filter by job posting
     */
    public function scopeByJobPosting($query, $jobPostingId)
    {
        return $query->where('job_posting_id', $jobPostingId);
    }

    /**
     * Scope: Filter by applicant
     */
    public function scopeByApplicant($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Only reviewed applications
     */
    public function scopeReviewed($query)
    {
        return $query->whereNotNull('reviewed_at');
    }

    /**
     * Scope: Only unreviewed applications
     */
    public function scopeUnreviewed($query)
    {
        return $query->whereNull('reviewed_at');
    }

    // ========================================================================
    // ACCESSORS & MUTATORS
    // ========================================================================

    /**
     * Check if application is new
     */
    public function getIsNewAttribute()
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * Check if application is reviewed
     */
    public function getIsReviewedAttribute()
    {
        return !is_null($this->reviewed_at);
    }

    /**
     * Check if application is shortlisted
     */
    public function getIsShortlistedAttribute()
    {
        return $this->status === self::STATUS_SHORTLISTED;
    }

    /**
     * Check if application is rejected
     */
    public function getIsRejectedAttribute()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if application is hired
     */
    public function getIsHiredAttribute()
    {
        return $this->status === self::STATUS_HIRED;
    }

    /**
     * Get days since application submitted
     */
    public function getDaysSinceAppliedAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            self::STATUS_NEW => 'bg-blue-100 text-blue-800',
            self::STATUS_REVIEWING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_SHORTLISTED => 'bg-purple-100 text-purple-800',
            self::STATUS_INTERVIEW => 'bg-indigo-100 text-indigo-800',
            self::STATUS_OFFER => 'bg-green-100 text-green-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
            self::STATUS_HIRED => 'bg-emerald-100 text-emerald-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status label for UI
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_NEW => 'New',
            self::STATUS_REVIEWING => 'Reviewing',
            self::STATUS_SHORTLISTED => 'Shortlisted',
            self::STATUS_INTERVIEW => 'Interview',
            self::STATUS_OFFER => 'Offer Extended',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_HIRED => 'Hired',
            default => ucfirst($this->status),
        };
    }

    // ========================================================================
    // METHODS
    // ========================================================================

    /**
     * Update application status
     */
    public function updateStatus($status, $reviewerId = null, $notes = null)
    {
        $data = [
            'status' => $status,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
        ];

        if ($notes) {
            $data['notes'] = $notes;
        }

        // Set specific timestamps based on status
        if ($status === self::STATUS_INTERVIEW) {
            $data['interview_scheduled_at'] = now();
        } elseif ($status === self::STATUS_OFFER) {
            $data['offer_extended_at'] = now();
        } elseif ($status === self::STATUS_HIRED) {
            $data['hired_at'] = now();
        } elseif ($status === self::STATUS_REJECTED) {
            $data['rejected_at'] = now();
        }

        $this->update($data);
    }

    /**
     * Mark as shortlisted
     */
    public function shortlist($reviewerId = null)
    {
        $this->updateStatus(self::STATUS_SHORTLISTED, $reviewerId);
    }

    /**
     * Mark as rejected
     */
    public function reject($reviewerId = null, $reason = null)
    {
        $this->updateStatus(self::STATUS_REJECTED, $reviewerId);
        if ($reason) {
            $this->update(['rejection_reason' => $reason]);
        }
    }

    /**
     * Mark as hired
     */
    public function hire($reviewerId = null)
    {
        $this->updateStatus(self::STATUS_HIRED, $reviewerId);
    }

    /**
     * Add rating
     */
    public function addRating($rating, $reviewerId = null)
    {
        $this->update([
            'rating' => $rating,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
        ]);
    }

    /**
     * Add internal notes
     */
    public function addNotes($notes)
    {
        $this->update(['notes' => $notes]);
    }
}
