<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\Student;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * controller untuk mengelola fitur pertemanan antar mahasiswa
 * 
 * fitur:
 * - mencari teman
 * - mengirim friend request
 * - accept/reject request
 * - unfriend
 * - lihat network statistics
 * 
 * path: app/Http/Controllers/Student/FriendController.php
 */
class FriendController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * tampilkan halaman daftar teman dan pencarian
     */
    public function index(Request $request)
    {
        $student = Auth::user()->student;
        
        // statistik network
        $stats = [
            'total_friends' => $student->friendsCount(),
            'pending_requests' => $student->pendingFriendRequests()->count(),
            'sent_requests' => $student->sentFriendRequests()->pending()->count(),
        ];

        // daftar teman
        $friends = $student->friends();
        
        // filter pencarian teman
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $friends = $friends->filter(function($friend) use ($search) {
                $fullName = strtolower($friend->user->name);
                $university = strtolower($friend->university->name ?? '');
                return str_contains($fullName, $search) || str_contains($university, $search);
            });
        }

        // pending requests yang diterima
        $pendingRequests = $student->pendingFriendRequests();

        // rekomendasi teman
        $suggestions = $student->suggestedFriends(8);

        return view('student.friends.index', compact(
            'student',
            'friends',
            'pendingRequests',
            'suggestions',
            'stats'
        ));
    }

    /**
     * halaman pencarian teman
     */
    public function search(Request $request)
    {
        $student = Auth::user()->student;
        $query = Student::with('user', 'university');

        // exclude diri sendiri
        $query->where('id', '!=', $student->id);

        // filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // filter universitas
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->university_id);
        }

        // filter jurusan
        if ($request->filled('major')) {
            $query->where('major', 'like', "%{$request->major}%");
        }

        // filter skills
        if ($request->filled('skills')) {
            $skills = is_array($request->skills) ? $request->skills : [$request->skills];
            $query->where(function($q) use ($skills) {
                foreach ($skills as $skill) {
                    $q->orWhereJsonContains('skills', $skill);
                }
            });
        }

        $results = $query->paginate(20);

        // tambahkan status pertemanan untuk setiap hasil
        foreach ($results as $result) {
            $result->friendship_status = $student->friendshipStatusWith($result->id);
        }

        // untuk ajax request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'results' => $results
            ]);
        }

        return view('student.friends.search', compact('results', 'student'));
    }

    /**
     * kirim friend request
     */
    public function sendRequest(Request $request, $studentId)
    {
        try {
            $currentStudent = Auth::user()->student;
            
            // validasi
            if ($currentStudent->id == $studentId) {
                return back()->with('error', 'Anda tidak dapat mengirim permintaan ke diri sendiri');
            }

            $targetStudent = Student::findOrFail($studentId);

            // cek apakah sudah ada request sebelumnya
            if ($currentStudent->hasPendingRequestWith($studentId)) {
                return back()->with('error', 'Permintaan pertemanan sudah dikirim sebelumnya');
            }

            if ($currentStudent->isFriendWith($studentId)) {
                return back()->with('error', 'Anda sudah berteman dengan mahasiswa ini');
            }

            // buat friend request
            $friendship = Friend::create([
                'requester_id' => $currentStudent->id,
                'receiver_id' => $targetStudent->id,
                'status' => 'pending',
                'message' => $request->message
            ]);

            // kirim notifikasi
            $this->notificationService->createNotification(
                $targetStudent->user_id,
                'friend_request',
                'Permintaan Pertemanan Baru',
                "{$currentStudent->user->first_name} {$currentStudent->user->last_name} ingin berteman dengan Anda",
                route('student.friends.index')
            );

            return back()->with('success', 'Permintaan pertemanan berhasil dikirim');

        } catch (\Exception $e) {
            Log::error('Error sending friend request: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengirim permintaan');
        }
    }

    /**
     * terima friend request
     */
    public function acceptRequest($friendshipId)
    {
        try {
            $currentStudent = Auth::user()->student;
            
            $friendship = Friend::findOrFail($friendshipId);

            // validasi bahwa request ditujukan ke user ini
            if ($friendship->receiver_id !== $currentStudent->id) {
                return back()->with('error', 'Permintaan tidak valid');
            }

            if ($friendship->status !== 'pending') {
                return back()->with('error', 'Permintaan sudah diproses sebelumnya');
            }

            // update status
            $friendship->update([
                'status' => 'accepted',
                'responded_at' => now()
            ]);

            // kirim notifikasi ke requester
            $this->notificationService->createNotification(
                $friendship->requester->user_id,
                'friend_accepted',
                'Permintaan Pertemanan Diterima',
                "{$currentStudent->user->first_name} {$currentStudent->user->last_name} menerima permintaan pertemanan Anda",
                route('student.friends.index')
            );

            return back()->with('success', 'Permintaan pertemanan berhasil diterima');

        } catch (\Exception $e) {
            Log::error('Error accepting friend request: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menerima permintaan');
        }
    }

    /**
     * tolak friend request
     */
    public function rejectRequest($friendshipId)
    {
        try {
            $currentStudent = Auth::user()->student;
            
            $friendship = Friend::findOrFail($friendshipId);

            // validasi
            if ($friendship->receiver_id !== $currentStudent->id) {
                return back()->with('error', 'Permintaan tidak valid');
            }

            if ($friendship->status !== 'pending') {
                return back()->with('error', 'Permintaan sudah diproses sebelumnya');
            }

            // update status
            $friendship->update([
                'status' => 'rejected',
                'responded_at' => now()
            ]);

            return back()->with('success', 'Permintaan pertemanan ditolak');

        } catch (\Exception $e) {
            Log::error('Error rejecting friend request: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menolak permintaan');
        }
    }

    /**
     * batalkan friend request yang sudah dikirim
     */
    public function cancelRequest($friendshipId)
    {
        try {
            $currentStudent = Auth::user()->student;
            
            $friendship = Friend::findOrFail($friendshipId);

            // validasi
            if ($friendship->requester_id !== $currentStudent->id) {
                return back()->with('error', 'Permintaan tidak valid');
            }

            if ($friendship->status !== 'pending') {
                return back()->with('error', 'Permintaan sudah diproses');
            }

            $friendship->delete();

            return back()->with('success', 'Permintaan pertemanan dibatalkan');

        } catch (\Exception $e) {
            Log::error('Error canceling friend request: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membatalkan permintaan');
        }
    }

    /**
     * unfriend (hapus pertemanan)
     */
    public function unfriend($friendshipId)
    {
        try {
            $currentStudent = Auth::user()->student;
            
            $friendship = Friend::findOrFail($friendshipId);

            // validasi bahwa user adalah bagian dari pertemanan ini
            if ($friendship->requester_id !== $currentStudent->id && 
                $friendship->receiver_id !== $currentStudent->id) {
                return back()->with('error', 'Aksi tidak valid');
            }

            if ($friendship->status !== 'accepted') {
                return back()->with('error', 'Anda tidak berteman dengan mahasiswa ini');
            }

            $friendship->delete();

            return back()->with('success', 'Pertemanan berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Error unfriending: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus pertemanan');
        }
    }

    /**
     * lihat profil publik teman
     */
    public function showProfile($studentId)
    {
        // redirect ke public profile view
        $student = Student::with('user')->findOrFail($studentId);

        // redirect ke public profile dengan username
        return redirect()->route('profile.public', $student->user->username);
    }

    /**
     * helper untuk mendapatkan mutual friends
     */
    private function getMutualFriends($currentStudent, $targetStudent)
    {
        $currentFriendIds = $currentStudent->friends()->pluck('id')->toArray();
        $targetFriendIds = $targetStudent->friends()->pluck('id')->toArray();

        $mutualIds = array_intersect($currentFriendIds, $targetFriendIds);

        return Student::with('user', 'university')
                      ->whereIn('id', $mutualIds)
                      ->limit(6)
                      ->get();
    }
    /**
     * get activity feed dari teman-teman
     */
    public function getActivities(Request $request)
    {
        $currentStudent = Auth::user()->student;
        $page = $request->get('page', 1);
        $perPage = 10;
        
        // dapatkan list teman
        $friendIds = $currentStudent->friends()->pluck('id')->toArray();
        
        if (empty($friendIds)) {
            return response()->json([
                'success' => true,
                'activities' => [],
                'message' => 'Belum ada teman untuk menampilkan aktivitas'
            ]);
        }
        
        $activities = [];
        
        // aktivitas: friend baru
        $newFriends = Friend::whereIn('requester_id', $friendIds)
                        ->orWhereIn('receiver_id', $friendIds)
                        ->where('status', 'accepted')
                        ->orderBy('responded_at', 'desc')
                        ->limit(5)
                        ->get();
        
        foreach ($newFriends as $friendship) {
            $friend = $friendship->getFriend($currentStudent->id);
            $otherFriend = $friendship->getFriend($friend->id);
            
            $activities[] = [
                'type' => 'friend',
                'user_name' => $friend->user->first_name . ' ' . $friend->user->last_name,
                'user_photo' => $friend->user->profile_photo_url,
                'user_url' => route('student.friends.profile', $friend->id),
                'action' => 'terhubung dengan ' . $otherFriend->user->first_name . ' ' . $otherFriend->user->last_name,
                'timestamp' => $friendship->responded_at->diffForHumans(),
                'raw_timestamp' => $friendship->responded_at,
            ];
        }
        
        // aktivitas: aplikasi proyek baru
        $applications = \App\Models\Application::whereIn('student_id', $friendIds)
                        ->with(['student.user', 'problem'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
        
        foreach ($applications as $application) {
            $activities[] = [
                'type' => 'project',
                'user_name' => $application->student->user->first_name . ' ' . $application->student->user->last_name,
                'user_photo' => $application->student->user->profile_photo_url,
                'user_url' => route('student.friends.profile', $application->student_id),
                'action' => 'melamar proyek baru',
                'timestamp' => $application->created_at->diffForHumans(),
                'raw_timestamp' => $application->created_at,
                'preview' => [
                    'title' => $application->problem->title,
                    'description' => $application->problem->description,
                    'image' => $application->problem->cover_image
                        ? Storage::url($application->problem->cover_image)
                        : asset('placeholder-project.jpg')
                ]
            ];
        }
        
        // aktivitas: proyek selesai
        $completedProjects = \App\Models\Project::whereIn('student_id', $friendIds)
                            ->where('status', 'completed')
                            ->with(['student.user', 'problem'])
                            ->orderBy('updated_at', 'desc')
                            ->limit(10)
                            ->get();
        
        foreach ($completedProjects as $project) {
            $activities[] = [
                'type' => 'achievement',
                'user_name' => $project->student->user->first_name . ' ' . $project->student->user->last_name,
                'user_photo' => $project->student->user->profile_photo_url,
                'user_url' => route('student.friends.profile', $project->student_id),
                'action' => 'menyelesaikan proyek',
                'timestamp' => $project->updated_at->diffForHumans(),
                'raw_timestamp' => $project->updated_at,
                'preview' => [
                    'title' => $project->problem->title,
                    'description' => 'Proyek KKN berhasil diselesaikan',
                    'image' => $project->problem->cover_image
                        ? Storage::url($project->problem->cover_image)
                        : asset('placeholder-project.jpg')
                ]
            ];
        }
        
        // sort activities by timestamp
        usort($activities, function($a, $b) {
            return $b['raw_timestamp'] <=> $a['raw_timestamp'];
        });
        
        // pagination
        $offset = ($page - 1) * $perPage;
        $paginatedActivities = array_slice($activities, $offset, $perPage);
        
        // remove raw_timestamp untuk response
        foreach ($paginatedActivities as &$activity) {
            unset($activity['raw_timestamp']);
        }
        
        return response()->json([
            'success' => true,
            'activities' => $paginatedActivities,
            'has_more' => count($activities) > ($offset + $perPage),
            'total' => count($activities)
        ]);
    }

    /**
     * get activity statistics
     */
    public function getActivityStats()
    {
        $currentStudent = Auth::user()->student;
        $friendIds = $currentStudent->friends()->pluck('id')->toArray();
        
        if (empty($friendIds)) {
            return response()->json([
                'success' => true,
                'stats' => [
                    'active_friends' => 0,
                    'projects_this_week' => 0,
                    'new_connections' => 0
                ]
            ]);
        }
        
        $stats = [
            // teman yang aktif (punya aktivitas dalam 7 hari terakhir)
            'active_friends' => \App\Models\Application::whereIn('student_id', $friendIds)
                ->where('created_at', '>=', now()->subDays(7))
                ->distinct('student_id')
                ->count(),
            
            // proyek baru dalam seminggu
            'projects_this_week' => \App\Models\Application::whereIn('student_id', $friendIds)
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            
            // koneksi baru dalam sebulan
            'new_connections' => Friend::where(function($query) use ($currentStudent) {
                    $query->where('requester_id', $currentStudent->id)
                        ->orWhere('receiver_id', $currentStudent->id);
                })
                ->where('status', 'accepted')
                ->where('responded_at', '>=', now()->subMonth())
                ->count()
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * get trending connections
     * (mahasiswa yang banyak terhubung dengan teman-teman kita)
     */
    public function getTrendingConnections()
    {
        $currentStudent = Auth::user()->student;
        $friendIds = $currentStudent->friends()->pluck('id')->toArray();
        
        if (empty($friendIds)) {
            return response()->json([
                'success' => true,
                'trending' => []
            ]);
        }
        
        // cari mutual friends yang belum terhubung
        $mutualFriendsCount = [];
        
        foreach ($friendIds as $friendId) {
            $friend = Student::find($friendId);
            if (!$friend) continue;
            
            $friendOfFriends = $friend->friends()->pluck('id')->toArray();
            
            foreach ($friendOfFriends as $fofId) {
                // skip jika itu diri sendiri atau sudah teman
                if ($fofId === $currentStudent->id || in_array($fofId, $friendIds)) {
                    continue;
                }
                
                if (!isset($mutualFriendsCount[$fofId])) {
                    $mutualFriendsCount[$fofId] = 0;
                }
                $mutualFriendsCount[$fofId]++;
            }
        }
        
        // sort by mutual friends count
        arsort($mutualFriendsCount);
        
        // ambil top 5
        $trendingIds = array_slice(array_keys($mutualFriendsCount), 0, 5);
        
        $trending = Student::with('user', 'university')
            ->whereIn('id', $trendingIds)
            ->get()
            ->map(function($student) use ($mutualFriendsCount) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->first_name . ' ' . $student->user->last_name,
                    'major' => $student->major,
                    'university' => $student->university->name,
                    'photo' => $student->user->profile_photo_url,
                    'mutual_friends' => $mutualFriendsCount[$student->id],
                    'profile_url' => route('student.friends.profile', $student->id)
                ];
            });
        
        return response()->json([
            'success' => true,
            'trending' => $trending
        ]);
    }

    /**
     * get network growth statistics
     */
    public function getNetworkGrowth()
    {
        $currentStudent = Auth::user()->student;
        
        // hitung pertumbuhan network per bulan (6 bulan terakhir)
        $growth = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $startDate = now()->subMonths($i)->startOfMonth();
            $endDate = now()->subMonths($i)->endOfMonth();
            
            $count = Friend::where(function($query) use ($currentStudent) {
                    $query->where('requester_id', $currentStudent->id)
                        ->orWhere('receiver_id', $currentStudent->id);
                })
                ->where('status', 'accepted')
                ->whereBetween('responded_at', [$startDate, $endDate])
                ->count();
            
            $growth[] = [
                'month' => $startDate->format('M Y'),
                'connections' => $count
            ];
        }
        
        return response()->json([
            'success' => true,
            'growth' => $growth
        ]);
    }
}

