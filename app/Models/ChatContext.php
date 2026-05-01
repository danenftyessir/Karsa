<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ChatContext
 *
 * Stores the context and indexed documents for a conversation
 */
class ChatContext extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'indexed_documents',
        'filters_applied',
        'knowledge_base_summary',
        'indexed_doc_count',
    ];

    protected $casts = [
        'indexed_documents' => 'array',
        'filters_applied' => 'array',
        'indexed_doc_count' => 'integer',
    ];

    /**
     * Relationship to Conversation
     */
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * Get all indexed documents
     */
    public function getDocuments()
    {
        if (!$this->indexed_documents) {
            return collect();
        }

        return Document::whereIn('id', $this->indexed_documents)->get();
    }

    /**
     * Add document to indexed list
     */
    public function addDocument($documentId)
    {
        $indexed = $this->indexed_documents ?? [];

        if (!in_array($documentId, $indexed)) {
            $indexed[] = $documentId;
            $this->update([
                'indexed_documents' => $indexed,
                'indexed_doc_count' => count($indexed),
            ]);
        }
    }

    /**
     * Check if document is indexed
     */
    public function hasDocument($documentId): bool
    {
        return in_array($documentId, $this->indexed_documents ?? []);
    }
}
