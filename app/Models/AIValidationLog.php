<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AIValidationLog Model
 *
 * Log untuk setiap validasi AI yang dilakukan pada institution
 * Menyimpan score, details, dan metadata dari proses validasi
 */
class AIValidationLog extends Model
{
    /**
     * Table name
     */
    protected $table = 'ai_validation_logs';

    /**
     * Fillable fields
     */
    protected $fillable = [
        'institution_id',
        'validation_type',
        'is_passed',
        'score',
        'confidence',
        'details',
        'issues',
        'recommendation',
        'api_used',
        'processing_time',
    ];

    /**
     * Cast attributes to specific types
     */
    protected $casts = [
        'is_passed' => 'boolean',
        'score' => 'decimal:2',
        'confidence' => 'decimal:2',
        'details' => 'array',
        'issues' => 'array',
        'processing_time' => 'integer',
    ];

    /**
     * Relationship to Institution
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Scope untuk filter by validation type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('validation_type', $type);
    }

    /**
     * Scope untuk filter by institution
     */
    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    /**
     * Scope untuk filter by API used
     */
    public function scopeByAPI($query, string $api)
    {
        return $query->where('api_used', $api);
    }
}
