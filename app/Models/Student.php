<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * model untuk tabel students
 *
 * representasi mahasiswa yang menggunakan platform KKN-GO
 */
class Student extends Model
{
    use HasFactory;

    // PENTING: Specify connection ke Supabase PostgreSQL
    protected $connection = 'pgsql';

    /**
     * attributes yang dapat diisi mass assignment
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'university_id',
        'major',
        'nim',
        'semester',
        'phone',
        'profile_photo_path',
        'bio',
        'stories',
        'skills',
        'interests',
        'portfolio_visible',
    ];

    /**
     * attributes yang di-cast ke tipe data tertentu
     */
    protected $casts = [
        'skills' => 'array',
        'stories' => 'array',
        'interests' => 'array',
        'portfolio_visible' => 'boolean',
    ];

    /**
     * relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * relasi ke university
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * relasi ke applications
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * relasi ke wishlist
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * relasi ke projects
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * relasi ke project reports
     */
    public function projectReports()
    {
        return $this->hasMany(ProjectReport::class);
    }

    /**
     * cek apakah student sudah wishlist problem tertentu
     */
    public function hasWishlisted($problemId)
    {
        return $this->wishlists()->where('problem_id', $problemId)->exists();
    }

    /**
     * cek apakah student sudah apply ke problem tertentu
     */
    public function hasApplied($problemId)
    {
        return $this->applications()->where('problem_id', $problemId)->exists();
    }

    /**
     * get active projects count
     */
    public function getActiveProjectsCountAttribute()
    {
        return $this->projects()
            ->whereIn('status', ['active', 'in_progress'])
            ->count();
    }

    /**
     * get completed projects count
     */
    public function getCompletedProjectsCountAttribute()
    {
        return $this->projects()
            ->where('status', 'completed')
            ->count();
    }

    /**
     * get average rating dari semua reviews
     */
    public function getAverageRatingAttribute()
    {
        $reviews = Review::where('reviewee_type', 'student')
                        ->where('reviewee_id', $this->id)
                        ->where('is_public', true)
                        ->get();

        return $reviews->isEmpty() ? 0 : round($reviews->avg('rating'), 1);
    }

    /**
     * get full name
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * get whatsapp number - alias untuk field phone
     */
    public function getWhatsappAttribute()
    {
        return $this->phone;
    }

    /**
     * get whatsapp_number - alias untuk field phone
     */
    public function getWhatsappNumberAttribute()
    {
        return $this->phone;
    }

    /**
     * get profile photo URL
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            // cek apakah path sudah berupa URL lengkap
            if (str_starts_with($this->profile_photo_path, 'http')) {
                return $this->profile_photo_path;
            }
            
            // cek apakah ini adalah path dari Supabase (tidak mengandung 'public/')
            if (!str_starts_with($this->profile_photo_path, 'public/')) {
                // ini adalah path Supabase, gunakan SupabaseStorageService untuk generate URL
                $storageService = app(\App\Services\SupabaseStorageService::class);
                return $storageService->getPublicUrl($this->profile_photo_path);
            }
            
            // fallback ke local storage jika path mengandung 'public/'
            return asset('storage/' . str_replace('public/', '', $this->profile_photo_path));
        }
        
        // default avatar
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&size=200&background=4F46E5&color=ffffff';
    }

    /**
     * get total SDGs addressed dari completed projects
     */
    public function getTotalSdgsAddressedAttribute()
    {
        $completedProjects = $this->projects()
            ->where('status', 'completed')
            ->with('problem')
            ->get();

        $sdgs = [];
        foreach ($completedProjects as $project) {
            if ($project->problem && $project->problem->sdg_categories) {
                $categories = $project->problem->sdg_categories;

                // handle jika masih string JSON
                if (is_string($categories)) {
                    $categories = json_decode($categories, true) ?? [];
                }

                if (is_array($categories)) {
                    $sdgs = array_merge($sdgs, $categories);
                }
            }
        }

        return count(array_unique($sdgs));
    }

    /**
     * relasi untuk permintaan pertemanan yang diterima (pending)
     */
    public function pendingFriendRequests()
    {
        return Friend::where('receiver_id', $this->id)
            ->where('status', 'pending')
            ->with('requester.user', 'requester.university')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * relasi untuk permintaan pertemanan yang dikirim
     */
    public function sentFriendRequests()
    {
        return Friend::where('requester_id', $this->id)
            ->with('receiver.user', 'receiver.university')
            ->orderBy('created_at', 'desc');
    }

    /**
     * get jumlah teman yang sudah diterima
     */
    public function friendsCount()
    {
        return Friend::where(function($query) {
                $query->where('requester_id', $this->id)
                      ->orWhere('receiver_id', $this->id);
            })
            ->where('status', 'accepted')
            ->count();
    }

    /**
     * get daftar teman yang sudah diterima
     */
    public function friends()
    {
        $friendships = Friend::where(function($query) {
                $query->where('requester_id', $this->id)
                      ->orWhere('receiver_id', $this->id);
            })
            ->where('status', 'accepted')
            ->with('requester.user', 'requester.university', 'receiver.user', 'receiver.university')
            ->get();

        // ekstrak student objects dari friendships
        return $friendships->map(function($friendship) {
            return $friendship->requester_id === $this->id
                ? $friendship->receiver
                : $friendship->requester;
        });
    }

    /**
     * cek status pertemanan dengan student lain
     */
    public function friendshipStatusWith($studentId)
    {
        // cek apakah sudah berteman
        $friendship = Friend::where(function($query) use ($studentId) {
                $query->where(function($q) use ($studentId) {
                    $q->where('requester_id', $this->id)
                      ->where('receiver_id', $studentId);
                })->orWhere(function($q) use ($studentId) {
                    $q->where('requester_id', $studentId)
                      ->where('receiver_id', $this->id);
                });
            })
            ->first();

        if (!$friendship) {
            return 'none'; // tidak ada relasi
        }

        if ($friendship->status === 'accepted') {
            return 'friends';
        }

        if ($friendship->status === 'pending') {
            if ($friendship->requester_id === $this->id) {
                return 'request_sent'; // kita yang kirim request
            } else {
                return 'request_received'; // kita yang terima request
            }
        }

        return 'none';
    }

    /**
     * cek apakah ada pending request dengan student lain
     */
    public function hasPendingRequestWith($studentId)
    {
        return Friend::where(function($query) use ($studentId) {
                $query->where(function($q) use ($studentId) {
                    $q->where('requester_id', $this->id)
                      ->where('receiver_id', $studentId);
                })->orWhere(function($q) use ($studentId) {
                    $q->where('requester_id', $studentId)
                      ->where('receiver_id', $this->id);
                });
            })
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * cek apakah sudah berteman dengan student lain
     */
    public function isFriendWith($studentId)
    {
        return Friend::where(function($query) use ($studentId) {
                $query->where(function($q) use ($studentId) {
                    $q->where('requester_id', $this->id)
                      ->where('receiver_id', $studentId);
                })->orWhere(function($q) use ($studentId) {
                    $q->where('requester_id', $studentId)
                      ->where('receiver_id', $this->id);
                });
            })
            ->where('status', 'accepted')
            ->exists();
    }

    /**
     * suggested friends berdasarkan universitas dan jurusan yang sama
     */
    public function suggestedFriends($limit = 5)
    {
        // get IDs dari teman yang sudah ada
        $existingFriendIds = $this->friends()->pluck('id')->toArray();

        // get IDs dari pending requests (baik yang dikirim maupun diterima)
        $pendingIds = Friend::where(function($query) {
                $query->where('requester_id', $this->id)
                      ->orWhere('receiver_id', $this->id);
            })
            ->where('status', 'pending')
            ->get()
            ->map(function($friendship) {
                return $friendship->requester_id === $this->id
                    ? $friendship->receiver_id
                    : $friendship->requester_id;
            })
            ->toArray();

        // combine IDs yang harus di-exclude
        $excludeIds = array_merge($existingFriendIds, $pendingIds, [$this->id]);

        // cari students dengan universitas atau jurusan yang sama
        return Student::where('id', '!=', $this->id)
            ->whereNotIn('id', $excludeIds)
            ->where(function($query) {
                $query->where('university_id', $this->university_id)
                      ->orWhere('major', 'like', '%' . $this->major . '%');
            })
            ->with('user', 'university')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}