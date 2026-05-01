<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * buat notifikasi baru
     */
    public function create($userId, $type, $title, $message, $data = null, $actionUrl = null)
    {
        try {
            // pastikan action_url tidak mengarah ke endpoint API atau invalid URL
            if ($actionUrl) {
                $invalidPaths = [
                    '/notifications/latest',
                    '/notifications/getLatest',
                    '/api/',
                ];

                foreach ($invalidPaths as $invalid) {
                    if (str_contains($actionUrl, $invalid)) {
                        Log::warning('Invalid action_url detected, setting to null', [
                            'action_url' => $actionUrl,
                            'type' => $type
                        ]);
                        $actionUrl = null;
                        break;
                    }
                }

                // jika action_url hanya '#' atau kosong, set null
                if (in_array($actionUrl, ['#', '', ' '])) {
                    $actionUrl = null;
                }
            }

            return Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'action_url' => $actionUrl,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('gagal membuat notifikasi: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * alias untuk create() - untuk backward compatibility
     */
    public function createNotification($userId, $type, $title, $message, $actionUrl = null, $data = null)
    {
        return $this->create($userId, $type, $title, $message, $data, $actionUrl);
    }
    
    /**
     * kirim notifikasi friend request
     */
    public function sendFriendRequestNotification($receiverId, $requesterName, $requesterId)
    {
        try {
            return $this->createNotification(
                $receiverId,
                'friend_request',
                'Permintaan Pertemanan Baru',
                "{$requesterName} ingin terhubung dengan Anda",
                route('student.friends.profile', $requesterId),
                [
                    'requester_id' => $requesterId,
                    'requester_name' => $requesterName
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending friend request notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * kirim notifikasi friend request accepted
     */
    public function sendFriendAcceptedNotification($requesterId, $accepterName, $accepterId)
    {
        try {
            return $this->createNotification(
                $requesterId,
                'friend_accepted',
                'Permintaan Pertemanan Diterima',
                "{$accepterName} telah menerima permintaan pertemanan Anda",
                route('student.friends.profile', $accepterId),
                [
                    'accepter_id' => $accepterId,
                    'accepter_name' => $accepterName
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending friend accepted notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * kirim notifikasi friend project activity
     * (ketika teman apply atau complete proyek)
     */
    public function sendFriendActivityNotification($userId, $friendName, $friendId, $activity, $projectId = null)
    {
        try {
            $messages = [
                'project_applied' => "{$friendName} telah melamar proyek baru",
                'project_completed' => "{$friendName} telah menyelesaikan sebuah proyek",
                'project_started' => "{$friendName} memulai proyek baru"
            ];

            $url = $projectId 
                ? route('student.problem.detail', $projectId)
                : route('student.friends.profile', $friendId);

            return $this->createNotification(
                $userId,
                'friend_activity',
                'Aktivitas Teman',
                $messages[$activity] ?? "{$friendName} melakukan aktivitas baru",
                $url,
                [
                    'friend_id' => $friendId,
                    'friend_name' => $friendName,
                    'activity_type' => $activity,
                    'project_id' => $projectId
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending friend activity notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * kirim notifikasi mutual connection
     * (ketika teman dari teman bergabung)
     */
    public function sendMutualConnectionNotification($userId, $newFriendName, $mutualFriendName, $newFriendId)
    {
        try {
            return $this->createNotification(
                $userId,
                'mutual_connection',
                'Koneksi Bersama',
                "{$newFriendName} dan {$mutualFriendName} telah terhubung. Mungkin Anda juga mengenal {$newFriendName}?",
                route('student.friends.profile', $newFriendId),
                [
                    'new_friend_id' => $newFriendId,
                    'new_friend_name' => $newFriendName,
                    'mutual_friend_name' => $mutualFriendName
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending mutual connection notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * batch send notifications untuk friend suggestions
     * (kirim ke users yang mungkin tertarik dengan user baru)
     */
    public function sendFriendSuggestionNotifications($userId, $userName, $suggestedUserIds)
    {
        try {
            $count = 0;
            foreach ($suggestedUserIds as $suggestedId) {
                $result = $this->createNotification(
                    $suggestedId,
                    'friend_suggestion',
                    'Rekomendasi Koneksi',
                    "Anda mungkin mengenal {$userName}. Kirim permintaan pertemanan?",
                    route('student.friends.profile', $userId),
                    [
                        'suggested_friend_id' => $userId,
                        'suggested_friend_name' => $userName
                    ]
                );
                if ($result) $count++;
            }
            return $count;
        } catch (\Exception $e) {
            Log::error('Error sending friend suggestion notifications: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * cleanup old friend notifications
     * (hapus notifikasi friend request yang sudah expired)
     */
    public function cleanupOldFriendNotifications($days = 30)
    {
        try {
            $deleted = Notification::whereIn('type', [
                    'friend_request',
                    'friend_suggestion',
                    'mutual_connection'
                ])
                ->where('created_at', '<', now()->subDays($days))
                ->where('is_read', true)
                ->delete();

            Log::info("Cleaned up {$deleted} old friend notifications");
            return $deleted;
        } catch (\Exception $e) {
            Log::error('Error cleaning up friend notifications: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * get unread friend notifications count
     */
    public function getUnreadFriendNotificationsCount($userId)
    {
        try {
            return Notification::where('user_id', $userId)
                ->whereIn('type', [
                    'friend_request',
                    'friend_accepted',
                    'friend_activity',
                    'mutual_connection',
                    'friend_suggestion'
                ])
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            Log::error('Error getting unread friend notifications count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * mark all friend notifications as read
     */
    public function markAllFriendNotificationsAsRead($userId)
    {
        try {
            $updated = Notification::where('user_id', $userId)
                ->whereIn('type', [
                    'friend_request',
                    'friend_accepted',
                    'friend_activity',
                    'mutual_connection',
                    'friend_suggestion'
                ])
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return $updated;
        } catch (\Exception $e) {
            Log::error('Error marking friend notifications as read: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * notifikasi ketika mahasiswa submit aplikasi
     */
    public function applicationSubmitted($application)
    {
        $institution = $application->problem->institution;
        
        return $this->create(
            $institution->user_id,
            'application_submitted',
            'Aplikasi Baru Diterima',
            "{$application->student->user->name} telah mengajukan aplikasi untuk proyek {$application->problem->title}",
            [
                'application_id' => $application->id,
                'student_name' => $application->student->user->name,
                'problem_title' => $application->problem->title,
            ],
            route('institution.applications.show', $application->id)
        );
    }

    /**
     * notifikasi ketika aplikasi diterima
     */
    public function applicationAccepted($application)
    {
        return $this->create(
            $application->student->user_id,
            'application_accepted',
            'Aplikasi Diterima! 🎉',
            "Selamat! Aplikasi Anda untuk proyek {$application->problem->title} telah diterima oleh {$application->problem->institution->name}",
            [
                'application_id' => $application->id,
                'problem_title' => $application->problem->title,
                'institution_name' => $application->problem->institution->name,
            ],
            route('student.applications.show', $application->id)
        );
    }

    /**
     * notifikasi ketika aplikasi ditolak
     */
    public function applicationRejected($application, $feedback = null)
    {
        $message = "Aplikasi Anda untuk proyek {$application->problem->title} belum dapat diterima saat ini.";
        if ($feedback) {
            $message .= " Feedback: {$feedback}";
        }
        
        return $this->create(
            $application->student->user_id,
            'application_rejected',
            'Aplikasi Ditolak',
            $message,
            [
                'application_id' => $application->id,
                'problem_title' => $application->problem->title,
                'feedback' => $feedback,
            ],
            route('student.applications.show', $application->id)
        );
    }

    /**
     * notifikasi ketika laporan disubmit
     */
    public function reportSubmitted($report, $project)
    {
        return $this->create(
            $project->institution->user_id,
            'report_submitted',
            'Laporan Baru Disubmit',
            "{$project->student->user->name} telah mengupload laporan untuk proyek {$project->problem->title}",
            [
                'report_id' => $report->id,
                'project_id' => $project->id,
                'student_name' => $project->student->user->name,
            ],
            route('institution.projects.show', $project->id)
        );
    }

    /**
     * notifikasi ketika proyek dimulai
     */
    public function projectStarted($project)
    {
        // notifikasi untuk mahasiswa
        $this->create(
            $project->student->user_id,
            'project_started',
            'Proyek Dimulai! 🚀',
            "Proyek {$project->problem->title} telah resmi dimulai. Selamat bekerja!",
            [
                'project_id' => $project->id,
                'problem_title' => $project->problem->title,
            ],
            route('student.projects.show', $project->id)
        );

        // notifikasi untuk instansi
        return $this->create(
            $project->institution->user_id,
            'project_started',
            'Proyek Dimulai',
            "Proyek {$project->problem->title} dengan mahasiswa {$project->student->user->name} telah dimulai",
            [
                'project_id' => $project->id,
                'problem_title' => $project->problem->title,
                'student_name' => $project->student->user->name,
            ],
            route('institution.projects.show', $project->id)
        );
    }

    /**
     * notifikasi ketika review diterima
     */
    public function reviewReceived($review, $reviewer)
    {
        return $this->create(
            $review->reviewee_id,
            'review_received',
            'Review Diterima',
            "Anda menerima review dari {$reviewer->name}",
            [
                'review_id' => $review->id,
                'rating' => $review->rating,
            ],
            route('student.portfolio')
        );
    }

    /**
     * dapatkan notifikasi terbaru
     */
    public function getLatest($userId, $limit = 5)
    {
        return Notification::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'action_url' => $notification->action_url,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->toIso8601String(),
                ];
            });
    }

    /**
     * hitung jumlah notifikasi yang belum dibaca
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->count();
    }

    /**
     * tandai semua notifikasi sebagai sudah dibaca
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}