<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * model untuk problems dengan accessor dan scope yang telah di-refactor
 * 
 * @property int $id
 * @property int $institution_id
 * @property string $title
 * @property string $description
 * @property string|null $background
 * @property string|null $objectives
 * @property string|null $scope
 * @property int $province_id
 * @property int $regency_id
 * @property string|null $village
 * @property string|null $detailed_location
 * @property array $sdg_categories
 * @property int $required_students
 * @property array $required_skills
 * @property array|null $required_majors
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property \Carbon\Carbon $application_deadline
 * @property int $duration_months
 * @property string $difficulty_level
 * @property string $status
 * @property string|null $expected_outcomes
 * @property array|null $deliverables
 * @property array|null $facilities_provided
 * @property int $views_count
 * @property int $applications_count
 * @property int $accepted_students
 * @property bool $is_featured
 * @property bool $is_urgent
 */
class Problem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institution_id',
        'title',
        'description',
        'background',
        'objectives',
        'scope',
        'province_id',
        'regency_id',
        'village',
        'detailed_location',
        'sdg_categories',
        'required_students',
        'required_skills',
        'required_majors',
        'start_date',
        'end_date',
        'application_deadline',
        'duration_months',
        'difficulty_level',
        'status',
        'expected_outcomes',
        'deliverables',
        'facilities_provided',
        'views_count',
        'applications_count',
        'accepted_students',
        'is_featured',
        'is_urgent',
    ];

    protected $casts = [
        'sdg_categories' => 'array',
        'required_skills' => 'array',
        'required_majors' => 'array',
        'deliverables' => 'array',
        'facilities_provided' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'application_deadline' => 'date',
        'is_featured' => 'boolean',
        'is_urgent' => 'boolean',
    ];

    // ===========================================
    // RELATIONSHIPS
    // ===========================================

    /**
     * relasi ke institution
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * relasi ke province
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * relasi ke regency
     */
    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    /**
     * relasi ke problem images
     */
    public function images()
    {
        return $this->hasMany(ProblemImage::class);
    }

    /**
     * relasi ke applications
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * relasi ke projects
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // ===========================================
    // ACCESSORS & MUTATORS
    // ===========================================

    /**
     * ACCESSOR: get SDG categories labels dalam bahasa indonesia
     * menggunakan helper function sdg_label() untuk konsistensi
     * 
     * usage: $problem->sdg_categories_labels
     * return: ['Tanpa Kemiskinan', 'Tanpa Kelaparan']
     */
    public function getSdgCategoriesLabelsAttribute(): array
    {
        if (!$this->sdg_categories || !is_array($this->sdg_categories)) {
            return [];
        }
        
        return array_map(function($sdgNumber) {
            return sdg_label($sdgNumber);
        }, $this->sdg_categories);
    }

    /**
     * ✅ ACCESSOR: get first SDG category label
     * berguna untuk display primary category
     * 
     * usage: $problem->primary_sdg_label
     * return: 'Tanpa Kemiskinan'
     */
    public function getPrimarySdgLabelAttribute(): string
    {
        if (!$this->sdg_categories || !is_array($this->sdg_categories) || empty($this->sdg_categories)) {
            return 'Tidak Ada Kategori';
        }
        
        return sdg_label($this->sdg_categories[0]);
    }

    /**
     * accessor: cek apakah problem sudah expired (deadline lewat)
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->application_deadline && $this->application_deadline->isPast();
    }

    /**
     * accessor: hitung sisa hari deadline
     */
    public function getDaysUntilDeadlineAttribute(): int
    {
        if (!$this->application_deadline) {
            return 0;
        }
        
        return max(0, now()->diffInDays($this->application_deadline, false));
    }

    /**
     * accessor: get cover/thumbnail image
     */
    public function getCoverImageAttribute()
    {
        // gunakan relationship yang sudah di-load untuk performance
        if ($this->relationLoaded('images')) {
            return $this->images->where('is_cover', true)->first()
                ?? $this->images->sortBy('order')->first();
        }

        // fallback jika images belum di-load
        return $this->images()->where('is_cover', true)->first()
            ?? $this->images()->orderBy('order')->first();
    }

    // ===========================================
    // QUERY SCOPES
    // ===========================================

    /**
     * ✅ SCOPE: filter by SDG categories
     * mendukung single atau multiple categories
     * menggunakan whereJsonContains untuk akurasi sempurna
     * 
     * usage: Problem::bySdgCategories([1, 4])->get()
     */
    public function scopeBySdgCategories($query, $categories)
    {
        // pastikan input adalah array
        if (!is_array($categories)) {
            $categories = [$categories];
        }
        
        // convert ke integer
        $categories = array_map('intval', array_filter($categories));
        
        if (empty($categories)) {
            return $query;
        }
        
        // gunakan whereJsonContains untuk setiap kategori
        // dengan OR logic
        return $query->where(function($q) use ($categories) {
            foreach ($categories as $category) {
                $q->orWhereJsonContains('sdg_categories', $category);
            }
        });
    }

    /**
     * scope: filter by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * scope: problem yang sedang open (status open dan belum expired)
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
                    ->where('application_deadline', '>=', now());
    }

    /**
     * scope: filter by difficulty level
     */
    public function scopeDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    /**
     * scope: filter by province
     */
    public function scopeByProvince($query, int $provinceId)
    {
        return $query->where('province_id', $provinceId);
    }

    /**
     * scope: filter by regency
     */
    public function scopeByRegency($query, int $regencyId)
    {
        return $query->where('regency_id', $regencyId);
    }

    /**
     * scope: filter by duration range
     */
    public function scopeByDuration($query, int $minMonths, int $maxMonths = null)
    {
        if ($maxMonths) {
            return $query->whereBetween('duration_months', [$minMonths, $maxMonths]);
        }
        
        return $query->where('duration_months', '>=', $minMonths);
    }

    /**
     * scope: featured problems
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * scope: urgent problems
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * scope: problems yang mendekati deadline
     */
    public function scopeDeadlineSoon($query, int $days = 7)
    {
        return $query->where('status', 'open')
                    ->whereBetween('application_deadline', [
                        now(),
                        now()->addDays($days)
                    ]);
    }

    /**
     * scope: search by keyword (title dan description)
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('title', 'ILIKE', "%{$keyword}%")
              ->orWhere('description', 'ILIKE', "%{$keyword}%");
        });
    }

    // ===========================================
    // METHODS
    // ===========================================

    /**
     * increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * increment applications count
     */
    public function incrementApplications(): void
    {
        $this->increment('applications_count');
    }

    /**
     * increment accepted students count
     */
    public function incrementAcceptedStudents(): void
    {
        $this->increment('accepted_students');
    }

    /**
     * cek apakah problem masih menerima aplikasi
     */
    public function isAcceptingApplications(): bool
    {
        return $this->status === 'open' 
            && $this->application_deadline 
            && $this->application_deadline->isFuture()
            && $this->accepted_students < $this->required_students;
    }

    /**
     * cek apakah problem sudah penuh
     */
    public function isFull(): bool
    {
        return $this->accepted_students >= $this->required_students;
    }
}