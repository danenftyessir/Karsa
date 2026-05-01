<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * relasi ke user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * scope untuk notifikasi yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * scope untuk notifikasi yang sudah dibaca
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * scope untuk notifikasi berdasarkan tipe
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * scope untuk notifikasi terbaru
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * tandai sebagai sudah dibaca
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * tandai sebagai belum dibaca
     */
    public function markAsUnread()
    {
        if ($this->is_read) {
            $this->update([
                'is_read' => false,
                'read_at' => null,
            ]);
        }
    }

    /**
     * FIXED: accessor untuk action_url dengan validasi
     * pastikan action_url tidak mengarah ke endpoint yang invalid
     */
    public function getActionUrlAttribute($value)
    {
        // jika null, return null
        if (!$value) {
            return null;
        }
        
        // list invalid paths yang tidak boleh dijadikan action_url
        $invalidPaths = [
            '/notifications/latest',
            '/notifications/getLatest',
            '/api/notifications',
        ];
        
        // cek apakah value mengandung invalid path
        foreach ($invalidPaths as $invalid) {
            if (str_contains($value, $invalid)) {
                return null; // return null jika invalid
            }
        }
        
        // cek jika hanya '#' atau kosong
        if (in_array($value, ['#', '', ' '])) {
            return null;
        }
        
        return $value;
    }

    /**
     * dapatkan icon berdasarkan tipe notifikasi
     */
    public function getIconAttribute()
    {
        $icons = [
            'application_submitted' => '📝',
            'application_accepted' => '✅',
            'application_rejected' => '❌',
            'project_started' => '🚀',
            'project_milestone' => '🎯',
            'report_submitted' => '📄',
            'report_approved' => '✔️',
            'report_rejected' => '✖️',
            'review_received' => '⭐',
            'problem_published' => '📢',
            'problem_closed' => '🔒',
            'message_received' => '💬',
            'deadline_reminder' => '⏰',
            'general' => 'ℹ️',
        ];

        return $icons[$this->type] ?? 'ℹ️';
    }

    /**
     * dapatkan warna badge berdasarkan tipe
     */
    public function getBadgeColorAttribute()
    {
        $colors = [
            'application_submitted' => 'blue',
            'application_accepted' => 'green',
            'application_rejected' => 'red',
            'project_started' => 'purple',
            'project_milestone' => 'indigo',
            'report_submitted' => 'yellow',
            'report_approved' => 'green',
            'report_rejected' => 'red',
            'review_received' => 'yellow',
            'problem_published' => 'blue',
            'problem_closed' => 'gray',
            'message_received' => 'pink',
            'deadline_reminder' => 'orange',
            'general' => 'gray',
        ];

        return $colors[$this->type] ?? 'gray';
    }

    /**
     * format waktu yang user friendly
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}