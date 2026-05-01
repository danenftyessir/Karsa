<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * model untuk documents dengan accessor dan scope yang telah di-refactor
 * 
 * @property int $id
 * @property int|null $project_id
 * @property int $uploaded_by
 * @property string $title
 * @property string|null $description
 * @property string $file_path
 * @property string $file_type
 * @property int $file_size
 * @property array|null $categories
 * @property array|null $tags
 * @property string|null $author_name
 * @property string|null $institution_name
 * @property string|null $university_name
 * @property int|null $year
 * @property int|null $province_id
 * @property int|null $regency_id
 * @property int $download_count
 * @property int $view_count
 * @property int $citation_count
 * @property bool $is_public
 * @property bool $is_featured
 * @property string $status
 * @property \Carbon\Carbon|null $approved_at
 */
class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'uploaded_by',
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'categories',
        'tags',
        'author_name',
        'institution_name',
        'university_name',
        'year',
        'province_id',
        'regency_id',
        'download_count',
        'view_count',
        'citation_count',
        'is_public',
        'is_featured',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'categories' => 'array',
        'tags' => 'array',
        'file_size' => 'integer',
        'year' => 'integer',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'citation_count' => 'integer',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // ===========================================
    // RELATIONSHIPS
    // ===========================================

    /**
     * relasi ke project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * relasi ke user yang upload
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
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

    // ===========================================
    // ACCESSORS & MUTATORS
    // ===========================================

    /**
     * ✅ ACCESSOR: get categories labels dalam bahasa indonesia
     * menggunakan helper function sdg_label() untuk konsistensi
     * 
     * usage: $document->categories_labels
     * return: ['Tanpa Kemiskinan', 'Pendidikan Berkualitas']
     */
    public function getCategoriesLabelsAttribute(): array
    {
        if (!$this->categories || !is_array($this->categories)) {
            return [];
        }
        
        return array_map(function($category) {
            return sdg_label($category);
        }, $this->categories);
    }

    /**
     * ✅ ACCESSOR: get primary category label
     * berguna untuk display kategori utama
     */
    public function getPrimaryCategoryLabelAttribute(): string
    {
        if (!$this->categories || !is_array($this->categories) || empty($this->categories)) {
            return 'Tidak Ada Kategori';
        }
        
        return sdg_label($this->categories[0]);
    }

    /**
     * accessor: get file size dalam format human readable
     */
    public function getFileSizeFormattedAttribute(): string
    {
        return format_file_size($this->file_size);
    }

    /**
     * accessor: get file URL dari supabase
     */
    public function getFileUrlAttribute(): string
    {
        return document_url($this->file_path);
    }

    /**
     * accessor: get file extension
     */
    public function getFileExtensionAttribute(): string
    {
        return strtoupper($this->file_type);
    }

    /**
     * accessor: cek apakah document baru (kurang dari 7 hari)
     */
    public function getIsNewAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * accessor: get stats summary
     */
    public function getStatsAttribute(): array
    {
        return [
            'downloads' => $this->download_count ?? 0,
            'views' => $this->view_count ?? 0,
            'citations' => $this->citation_count ?? 0,
        ];
    }

    // ===========================================
    // QUERY SCOPES
    // ===========================================

    /**
     * ✅ SCOPE: filter by categories (SDG)
     * mendukung single atau multiple categories
     * menggunakan whereJsonContains untuk akurasi sempurna
     * 
     * usage: Document::byCategories([1, 4])->get()
     */
    public function scopeByCategories($query, $categories)
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
                $q->orWhereJsonContains('categories', $category);
            }
        });
    }

    /**
     * scope: filter dokumen yang dipublikasikan
     */
    public function scopePublished($query)
    {
        return $query->where('is_public', true)
                    ->where('status', 'approved');
    }

    /**
     * scope: filter dokumen featured
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->where('is_public', true)
                    ->where('status', 'approved');
    }

    /**
     * scope: filter by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * scope: filter by file type
     */
    public function scopeFileType($query, string $fileType)
    {
        return $query->where('file_type', $fileType);
    }

    /**
     * scope: filter by year
     */
    public function scopeYear($query, int $year)
    {
        return $query->where('year', $year);
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
     * scope: search by keyword
     * mencari di title, description, author_name, institution_name, university_name
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('title', 'ILIKE', "%{$keyword}%")
              ->orWhere('description', 'ILIKE', "%{$keyword}%")
              ->orWhere('author_name', 'ILIKE', "%{$keyword}%")
              ->orWhere('institution_name', 'ILIKE', "%{$keyword}%")
              ->orWhere('university_name', 'ILIKE', "%{$keyword}%");
        });
    }

    /**
     * scope: dokumen terpopuler (berdasarkan downloads)
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->where('is_public', true)
                    ->where('status', 'approved')
                    ->orderBy('download_count', 'desc')
                    ->limit($limit);
    }

    /**
     * scope: dokumen terbaru
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->where('is_public', true)
                    ->where('status', 'approved')
                    ->latest()
                    ->limit($limit);
    }

    // ===========================================
    // METHODS
    // ===========================================

    /**
     * increment download count
     */
    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }

    /**
     * increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * increment citation count
     */
    public function incrementCitations(): void
    {
        $this->increment('citation_count');
    }

    /**
     * approve dokumen
     */
    public function approve(): bool
    {
        $this->status = 'approved';
        $this->approved_at = now();
        return $this->save();
    }

    /**
     * reject dokumen
     */
    public function reject(): bool
    {
        $this->status = 'rejected';
        return $this->save();
    }

    /**
     * set as featured
     */
    public function setFeatured(bool $featured = true): bool
    {
        $this->is_featured = $featured;
        return $this->save();
    }

    /**
     * set visibility
     */
    public function setPublic(bool $public = true): bool
    {
        $this->is_public = $public;
        return $this->save();
    }
}