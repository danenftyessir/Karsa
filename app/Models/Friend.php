<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * model untuk mengelola pertemanan antar mahasiswa
 * 
 * relasi:
 * - requester (mahasiswa yang mengirim permintaan)
 * - receiver (mahasiswa yang menerima permintaan)
 * 
 * path: app/Models/Friend.php
 */
class Friend extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'receiver_id',
        'status',
        'message',
        'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * relasi ke student yang mengirim permintaan
     */
    public function requester()
    {
        return $this->belongsTo(Student::class, 'requester_id');
    }

    /**
     * relasi ke student yang menerima permintaan
     */
    public function receiver()
    {
        return $this->belongsTo(Student::class, 'receiver_id');
    }

    /**
     * scope untuk mendapatkan pertemanan yang sudah diterima
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * scope untuk mendapatkan permintaan yang pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * scope untuk mendapatkan semua pertemanan user tertentu
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('requester_id', $userId)
              ->orWhere('receiver_id', $userId);
        });
    }

    /**
     * helper method untuk mendapatkan teman dari perspektif user tertentu
     */
    public function getFriend($currentUserId)
    {
        return $this->requester_id === $currentUserId 
            ? $this->receiver 
            : $this->requester;
    }

    /**
     * cek apakah permintaan dikirim oleh user tertentu
     */
    public function isSentBy($userId)
    {
        return $this->requester_id === $userId;
    }

    /**
     * cek apakah permintaan diterima oleh user tertentu
     */
    public function isReceivedBy($userId)
    {
        return $this->receiver_id === $userId;
    }
}