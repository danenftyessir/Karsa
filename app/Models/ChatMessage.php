<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model ChatMessage
 *
 * Represents an individual message in a chat conversation
 */
class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'context',
        'metadata',
        'confidence_score',
        'token_usage',
        'flagged',
        'flag_reason',
    ];

    protected $casts = [
        'context' => 'array',
        'metadata' => 'array',
        'confidence_score' => 'decimal:2',
        'token_usage' => 'integer',
        'flagged' => 'boolean',
    ];

    /**
     * Relationship to Conversation
     */
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * Relationship to Document References
     */
    public function documentReferences()
    {
        return $this->hasMany(ChatDocumentReference::class, 'message_id');
    }

    /**
     * Scope for user messages
     */
    public function scopeUserMessages($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Scope for assistant messages
     */
    public function scopeAssistantMessages($query)
    {
        return $query->where('role', 'assistant');
    }

    /**
     * Scope for flagged messages
     */
    public function scopeFlagged($query)
    {
        return $query->where('flagged', true);
    }

    /**
     * Check if message is from user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if message is from assistant
     */
    public function isAssistant(): bool
    {
        return $this->role === 'assistant';
    }

    /**
     * Get formatted timestamp
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('H:i');
    }
}
