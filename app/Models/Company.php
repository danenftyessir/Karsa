<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// model untuk data perusahaan/company yang akan merekrut mahasiswa
class Company extends Model
{
    use HasFactory;

    // PENTING: Specify connection ke Supabase PostgreSQL
    protected $connection = 'pgsql';

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

    // relasi ke job postings (lowongan kerja)
    // IMPLEMENTED: Data langsung dari Supabase PostgreSQL
    public function jobPostings()
    {
        return $this->hasMany(JobPosting::class);
    }

    // relasi ke job applications melalui job postings
    // IMPLEMENTED: Data langsung dari Supabase PostgreSQL
    public function jobApplications()
    {
        return $this->hasManyThrough(JobApplication::class, JobPosting::class);
    }

    // relasi ke saved talents (many-to-many via pivot table)
    // IMPLEMENTED: Data langsung dari Supabase PostgreSQL
    public function savedTalents()
    {
        return $this->belongsToMany(
            User::class,
            'saved_talents',
            'company_id',
            'user_id'
        )->withPivot('category', 'notes', 'saved_at')
            ->withTimestamps();
    }
}
