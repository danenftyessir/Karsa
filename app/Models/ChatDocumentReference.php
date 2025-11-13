<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ChatDocumentReference
 *
 * Links chat messages to source documents from the knowledge repository
 */
class ChatDocumentReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'document_id',
        'relevance_score',
        'excerpt',
        'metadata',
    ];

    protected $casts = [
        'relevance_score' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Relationship to Message
     */
    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    /**
     * Relationship to Document
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Scope for high relevance documents
     */
    public function scopeHighRelevance($query, $threshold = 0.7)
    {
        return $query->where('relevance_score', '>=', $threshold);
    }

    /**
     * Get excerpt with ellipsis if too long
     */
    public function getShortExcerptAttribute()
    {
        if (!$this->excerpt) {
            return null;
        }

        return strlen($this->excerpt) > 200
            ? substr($this->excerpt, 0, 200) . '...'
            : $this->excerpt;
    }
}
