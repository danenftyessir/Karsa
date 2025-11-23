<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model University
 */
class University extends Model
{
    use HasFactory;

    // PENTING: Specify connection ke Supabase PostgreSQL
    protected $connection = 'pgsql';

    protected $fillable = [
        'name',
        'code',
        'province_id',
        'regency_id',
        'type', // negeri/swasta
        'accreditation',
    ];

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
     * relasi ke students
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}