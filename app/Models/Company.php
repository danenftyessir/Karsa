<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// model untuk data perusahaan/company yang akan merekrut mahasiswa
class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'industry',
        'description',
        'website',
        'logo',
        'address',
        'city',
        'province_id',
        'phone',
        'employee_count',
        'founded_year',
        'verification_status',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relasi ke province
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    // TO DO: relasi ke job postings (lowongan kerja)
    // public function jobPostings()
    // {
    //     return $this->hasMany(JobPosting::class);
    // }

    // TO DO: relasi ke job applications
    // public function jobApplications()
    // {
    //     return $this->hasManyThrough(JobApplication::class, JobPosting::class);
    // }
}
