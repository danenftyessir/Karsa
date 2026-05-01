<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SavedJob Model
 *
 * menyimpan job yang di-bookmark oleh mahasiswa
 * data langsung dari supabase postgresql
 */
class SavedJob extends Model
{
    use HasFactory;

    protected $connection = 'pgsql';
    protected $table = 'saved_jobs';

    protected $fillable = [
        'user_id',
        'job_posting_id',
        'folder',
        'notes',
        'reminder_at',
        'saved_at',
    ];

    protected $casts = [
        'reminder_at' => 'datetime',
        'saved_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * relasi ke job posting
     */
    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * scope by folder
     */
    public function scopeInFolder($query, $folder)
    {
        return $query->where('folder', $folder);
    }

    /**
     * scope by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
