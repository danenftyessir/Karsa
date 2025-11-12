<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Institution
 * 
 * representasi institusi/lembaga yang menerbitkan masalah KKN
 */
class Institution extends Model
{
    use HasFactory;

    /**
     * attributes yang dapat diisi mass assignment
     */
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'address',
        'province_id',
        'regency_id',
        'email',
        'phone',
        'logo_path',
        'pic_name',
        'pic_position',
        'pic_phone',
        'verification_document_path',
        'website',
        'description',
        // Legacy verification
        'is_verified',
        'verified_at',
        // AI Verification
        'verification_status',
        'verification_score',
        'verification_confidence',
        'verified_by',
        'rejection_reason',
    ];

    /**
     * attributes yang di-cast ke tipe data tertentu
     */
    protected $casts = [
        // Legacy
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        // AI Verification
        'verification_score' => 'float',
        'verification_confidence' => 'float',
    ];

    /**
     * relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
     * relasi ke problems
     */
    public function problems()
    {
        return $this->hasMany(Problem::class);
    }

    /**
     * relasi ke projects
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * relasi ke verification_documents
     */
    public function verificationDocuments()
    {
        return $this->hasMany(VerificationDocument::class);
    }

    /**
     * relasi ke verifier (user who verified)
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * get full address
     */
    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->address,
            $this->regency?->name,
            $this->province?->name,
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * get logo URL
     * PERBAIKAN BUG: sekarang support Supabase storage
     */
    public function getLogoUrl(): string
    {
        if ($this->logo_path) {
            // cek apakah path sudah berupa URL lengkap
            if (str_starts_with($this->logo_path, 'http')) {
                return $this->logo_path;
            }
            
            // cek apakah ini adalah path dari Supabase (tidak mengandung 'public/')
            if (!str_starts_with($this->logo_path, 'public/')) {
                // ini adalah path Supabase, gunakan SupabaseStorageService
                $storageService = app(\App\Services\SupabaseStorageService::class);
                return $storageService->getPublicUrl($this->logo_path);
            }
            
            // fallback ke local storage untuk backward compatibility
            return asset('storage/' . str_replace('public/', '', $this->logo_path));
        }
        
        // default logo dengan initial institusi
        $initial = strtoupper(substr($this->name, 0, 1));
        return 'https://ui-avatars.com/api/?name=' . urlencode($initial) . '&size=200&background=10B981&color=ffffff';
    }

    /**
     * accessor untuk logo_url
     */
    public function getLogoUrlAttribute(): string
    {
        return $this->getLogoUrl();
    }

    /**
     * get verification document URL
     */
    public function getVerificationDocumentUrl(): ?string
    {
        if ($this->verification_document_path) {
            // cek apakah path sudah berupa URL lengkap
            if (str_starts_with($this->verification_document_path, 'http')) {
                return $this->verification_document_path;
            }
            
            // cek apakah ini adalah path dari Supabase
            if (!str_starts_with($this->verification_document_path, 'public/')) {
                $storageService = app(\App\Services\SupabaseStorageService::class);
                return $storageService->getPublicUrl($this->verification_document_path);
            }
            
            // fallback ke local storage
            return asset('storage/' . str_replace('public/', '', $this->verification_document_path));
        }
        
        return null;
    }

    /**
     * cek apakah institusi sudah diverifikasi
     */
    public function isVerified(): bool
    {
        return $this->is_verified === true;
    }

    /**
     * get total problems count
     */
    public function getTotalProblemsAttribute()
    {
        return $this->problems()->count();
    }

    /**
     * get active problems count
     */
    public function getActiveProblemsAttribute()
    {
        return $this->problems()->where('status', 'open')->count();
    }

    /**
     * get total projects count
     */
    public function getTotalProjectsAttribute()
    {
        return $this->projects()->count();
    }

    /**
     * get completed projects count
     */
    public function getCompletedProjectsAttribute()
    {
        return $this->projects()->where('status', 'completed')->count();
    }

    /**
     * Verification status constants
     */
    const STATUS_PENDING_VERIFICATION = 'pending_verification';
    const STATUS_NEEDS_REVIEW = 'needs_review';
    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_REJECTED = 'rejected';

    /**
     * Check if institution is pending verification
     */
    public function isPendingVerification(): bool
    {
        return $this->verification_status === self::STATUS_PENDING_VERIFICATION;
    }

    /**
     * Check if institution needs review
     */
    public function needsReview(): bool
    {
        return $this->verification_status === self::STATUS_NEEDS_REVIEW;
    }

    /**
     * Check if institution is active
     */
    public function isActive(): bool
    {
        return $this->verification_status === self::STATUS_ACTIVE;
    }

    /**
     * Check if institution is rejected
     */
    public function isRejected(): bool
    {
        return $this->verification_status === self::STATUS_REJECTED;
    }

    /**
     * Get verification status badge color
     */
    public function getVerificationStatusBadgeColor(): string
    {
        return match($this->verification_status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_PENDING_VERIFICATION => 'warning',
            self::STATUS_NEEDS_REVIEW => 'info',
            self::STATUS_SUSPENDED => 'secondary',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get verification status label
     */
    public function getVerificationStatusLabel(): string
    {
        return match($this->verification_status) {
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_PENDING_VERIFICATION => 'Menunggu Verifikasi',
            self::STATUS_NEEDS_REVIEW => 'Perlu Review Manual',
            self::STATUS_SUSPENDED => 'Ditangguhkan',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Tidak Diketahui',
        };
    }
}