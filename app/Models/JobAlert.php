<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * JobAlert Model
 *
 * menyimpan kriteria alert untuk job baru
 * data langsung dari supabase postgresql
 */
class JobAlert extends Model
{
    use HasFactory;

    protected $connection = 'pgsql';
    protected $table = 'job_alerts';

    protected $fillable = [
        'user_id',
        'name',
        'keywords',
        'job_types',
        'locations',
        'salary_min',
        'salary_max',
        'skills',
        'frequency',
        'is_active',
        'last_sent_at',
    ];

    protected $casts = [
        'job_types' => 'array',
        'locations' => 'array',
        'skills' => 'array',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    /**
     * relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * scope active alerts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * scope by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * scope by frequency
     */
    public function scopeByFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }
}
