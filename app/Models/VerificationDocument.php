<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationDocument extends Model
{
    protected $fillable = [
        'institution_id',
        'document_type',
        'file_url',
        'file_name',
        'file_size',
        'mime_type',
        'ai_verification_id',
        'ai_status',
        'ai_score',
        'ai_confidence',
        'ai_flags',
        'ai_extracted_data',
        'ai_reasoning',
        'ai_processed_at',
        'human_reviewed',
        'human_status',
        'human_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'ai_score' => 'float',
        'ai_confidence' => 'float',
        'ai_flags' => 'array',
        'ai_extracted_data' => 'array',
        'ai_processed_at' => 'datetime',
        'human_reviewed' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Document types
     */
    const TYPE_OFFICIAL_LETTER = 'official_letter';
    const TYPE_LOGO = 'logo';
    const TYPE_PIC_IDENTITY = 'pic_identity';
    const TYPE_NPWP = 'npwp';

    /**
     * AI statuses
     */
    const STATUS_APPROVED = 'approved';
    const STATUS_NEEDS_REVIEW = 'needs_review';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the institution that owns the document
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the reviewer (user) if human reviewed
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if document is approved by AI
     */
    public function isApproved(): bool
    {
        return $this->ai_status === self::STATUS_APPROVED;
    }

    /**
     * Check if document needs human review
     */
    public function needsReview(): bool
    {
        return $this->ai_status === self::STATUS_NEEDS_REVIEW;
    }

    /**
     * Check if document is rejected
     */
    public function isRejected(): bool
    {
        return $this->ai_status === self::STATUS_REJECTED;
    }
}
