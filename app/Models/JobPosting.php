<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * JobPosting Model
 *
 * PENTING: Model ini menggunakan Supabase PostgreSQL sebagai database
 * SEMUA operasi CRUD langsung ke Supabase, BUKAN local database
 */
class JobPosting extends Model
{
    use HasFactory, SoftDeletes;

    // Specify connection to Supabase PostgreSQL
    protected $connection = 'pgsql';
    protected $table = 'job_postings';

    protected $fillable = [
        'company_id',
        'job_category_id',
        'title',
        'slug',
        'department',
        'location',
        'job_type',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_period',
        'description',
        'responsibilities',
        'qualifications',
        'benefits',
        'skills',
        'sdg_alignment',
        'impact_metrics',
        'success_criteria',
        'status',
        'allow_guest_applications',
        'views_count',
        'applications_count',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'skills' => 'array', // JSON to array
        'sdg_alignment' => 'array', // JSON to array
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'allow_guest_applications' => 'boolean',
        'views_count' => 'integer',
        'applications_count' => 'integer',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ========================================================================
    // RELATIONSHIPS
    // ========================================================================

    /**
     * Job posting belongs to a company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Job posting belongs to a category
     */
    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    /**
     * Job posting has many applications
     */
    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Job posting has many skills (if using pivot table)
     */
    public function jobPostingSkills()
    {
        return $this->hasMany(JobPostingSkill::class);
    }

    // ========================================================================
    // SCOPES
    // ========================================================================

    /**
     * Scope: Only get published job postings
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'posted')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope: Only get active job postings (not expired)
     */
    public function scopeActive($query)
    {
        return $query->published()
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope: Filter by company
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope: Filter by job type (support single value or array)
     */
    public function scopeByType($query, $type)
    {
        if (is_array($type)) {
            return $query->whereIn('job_type', $type);
        }
        return $query->where('job_type', $type);
    }

    /**
     * Scope: Filter by location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'LIKE', "%{$location}%");
    }

    /**
     * Scope: Search by title or description
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'ILIKE', "%{$keyword}%")
                ->orWhere('description', 'ILIKE', "%{$keyword}%")
                ->orWhere('department', 'ILIKE', "%{$keyword}%");
        });
    }

    // ========================================================================
    // ACCESSORS & MUTATORS
    // ========================================================================

    /**
     * Get salary range formatted
     */
    public function getSalaryRangeAttribute()
    {
        if (!$this->salary_min && !$this->salary_max) {
            return 'Negotiable';
        }

        $currency = $this->salary_currency ?? 'USD';
        $symbol = $currency === 'USD' ? '$' : $currency;

        if ($this->salary_min && $this->salary_max) {
            return "{$symbol}{$this->salary_min} - {$symbol}{$this->salary_max}";
        }

        if ($this->salary_min) {
            return "From {$symbol}{$this->salary_min}";
        }

        return "Up to {$symbol}{$this->salary_max}";
    }

    /**
     * Check if job posting is expired
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Check if job posting is active
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'posted'
            && $this->published_at
            && $this->published_at->isPast()
            && !$this->is_expired;
    }

    /**
     * Get days since published
     */
    public function getDaysSincePublishedAttribute()
    {
        if (!$this->published_at) {
            return null;
        }

        return $this->published_at->diffInDays(now());
    }

    /**
     * Get share URL
     */
    public function getShareUrlAttribute()
    {
        return url("/jobs/{$this->slug}-{$this->id}");
    }

    // ========================================================================
    // METHODS
    // ========================================================================

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Publish job posting
     */
    public function publish()
    {
        $this->update([
            'status' => 'posted',
            'published_at' => now(),
        ]);
    }

    /**
     * Close job posting
     */
    public function close()
    {
        $this->update([
            'status' => 'closed',
        ]);
    }

    /**
     * Archive job posting
     */
    public function archive()
    {
        $this->update([
            'status' => 'archived',
        ]);
    }

    /**
     * Get applications by status
     */
    public function getApplicationsByStatus($status)
    {
        return $this->jobApplications()->where('status', $status)->get();
    }

    /**
     * Get applications count by status
     */
    public function getApplicationsCountByStatus($status)
    {
        return $this->jobApplications()->where('status', $status)->count();
    }
}
