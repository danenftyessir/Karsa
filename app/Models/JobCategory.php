<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * JobCategory Model
 *
 * PENTING: Model ini menggunakan Supabase PostgreSQL sebagai database
 * SEMUA operasi CRUD langsung ke Supabase, BUKAN local database
 */
class JobCategory extends Model
{
    use HasFactory;

    // Specify connection to Supabase PostgreSQL
    protected $connection = 'pgsql';
    protected $table = 'job_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
    ];

    // ========================================================================
    // RELATIONSHIPS
    // ========================================================================

    /**
     * Job category has many job postings
     */
    public function jobPostings()
    {
        return $this->hasMany(JobPosting::class);
    }

    // ========================================================================
    // SCOPES
    // ========================================================================

    /**
     * Scope: Filter by slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Scope: Search by name
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where('name', 'ILIKE', "%{$keyword}%");
    }

    // ========================================================================
    // ACCESSORS & MUTATORS
    // ========================================================================

    /**
     * Get job postings count for this category
     */
    public function getJobPostingsCountAttribute()
    {
        return $this->jobPostings()->count();
    }

    /**
     * Get active job postings count for this category
     */
    public function getActiveJobPostingsCountAttribute()
    {
        return $this->jobPostings()->active()->count();
    }
}
