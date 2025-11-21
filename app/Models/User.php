<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * model user
 *
 * model utama untuk autentikasi user di sistem
 * support multi-type user: student, institution, admin
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * attributes yang dapat diisi mass assignment
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'user_type',
        'is_active',
        'email_verified_at',
        'email_verification_token',
    ];

    /**
     * attributes yang harus disembunyikan saat serialisasi
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
    ];

    /**
     * attributes yang di-cast ke tipe data tertentu
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * relasi ke student (one to one)
     * 
     * setiap user dengan user_type = 'student' memiliki 1 data student
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * relasi ke institution (one to one)
     *
     * setiap user dengan user_type = 'institution' memiliki 1 data institution
     */
    public function institution()
    {
        return $this->hasOne(Institution::class);
    }

    /**
     * relasi ke company (one to one)
     *
     * setiap user dengan user_type = 'company' memiliki 1 data company
     */
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    /**
     * relasi ke notifikasi
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * cek apakah user adalah student
     */
    public function isStudent(): bool
    {
        return $this->user_type === 'student';
    }

    /**
     * cek apakah user adalah institution
     */
    public function isInstitution(): bool
    {
        return $this->user_type === 'institution';
    }

    /**
     * cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * cek apakah user adalah company
     */
    public function isCompany(): bool
    {
        return $this->user_type === 'company';
    }

    /**
     * get profile data berdasarkan user type
     */
    public function getProfileAttribute()
    {
        if ($this->isStudent()) {
            return $this->student;
        } elseif ($this->isInstitution()) {
            return $this->institution;
        } elseif ($this->isCompany()) {
            return $this->company;
        }

        return null;
    }

    /**
     * get profile photo URL
     * PERBAIKAN BUG: sekarang menggunakan Supabase URL yang benar
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->isStudent()) {
            // gunakan accessor dari Student model yang sudah di-fix
            // Student model sudah handle fallback ke ui-avatars jika tidak ada foto
            return $this->student?->profile_photo_url
                ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=200&background=4F46E5&color=ffffff';
        } elseif ($this->isInstitution()) {
            // gunakan accessor dari Institution model
            // Institution model sudah handle fallback ke ui-avatars jika tidak ada logo
            return $this->institution?->logo_url
                ?? 'https://ui-avatars.com/api/?name=' . urlencode(substr($this->name, 0, 1)) . '&size=200&background=10B981&color=ffffff';
        }

        // default avatar untuk user type lain (admin)
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=200&background=6366F1&color=ffffff';
    }

    /**
     * get unread notifications count
     */
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->notifications()
            ->where('is_read', false)
            ->count();
    }
}