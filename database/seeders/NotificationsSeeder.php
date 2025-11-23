<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use App\Models\Student;
use App\Models\Institution;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NotificationsSeeder extends Seeder
{
    /**
     * jalankan database seeds untuk notifikasi
     */
    public function run(): void
    {
        \DB::reconnect('pgsql');

        // ambil beberapa user untuk dijadikan penerima notifikasi
        $students = Student::with('user')->limit(10)->get();
        $institutions = Institution::with('user')->limit(5)->get();
        
        $notificationTypes = [
            'application_submitted' => [
                'title' => 'Aplikasi Baru Diterima',
                'messages' => [
                    'Ahmad Rizki telah mengajukan aplikasi untuk proyek Pengolahan Sampah Organik',
                    'Siti Nurhaliza mengajukan diri untuk Pendidikan Literasi Digital',
                    'Budi Santoso apply ke proyek Pemberdayaan UMKM Desa',
                ],
            ],
            'application_accepted' => [
                'title' => 'Aplikasi Diterima! 🎉',
                'messages' => [
                    'Selamat! Aplikasi Anda untuk proyek Pengolahan Sampah Organik telah diterima',
                    'Aplikasi Anda diterima untuk proyek Pendidikan Literasi Digital',
                    'Anda berhasil diterima di proyek Pemberdayaan UMKM',
                ],
            ],
            'application_rejected' => [
                'title' => 'Aplikasi Ditolak',
                'messages' => [
                    'Aplikasi Anda untuk proyek Renovasi Balai Desa belum dapat diterima saat ini',
                    'Mohon maaf, aplikasi untuk Pelatihan Kewirausahaan tidak dapat diterima',
                ],
            ],
            'project_started' => [
                'title' => 'Proyek Dimulai 🚀',
                'messages' => [
                    'Proyek Pengolahan Sampah Organik telah dimulai. Selamat bekerja!',
                    'Proyek Pendidikan Literasi Digital dengan Dinas Pendidikan telah dimulai',
                ],
            ],
            'project_milestone' => [
                'title' => 'Milestone Baru',
                'messages' => [
                    'Milestone baru: Survei Awal Masyarakat',
                    'Milestone ditambahkan: Pembuatan Proposal Lengkap',
                    'Milestone baru: Pelaksanaan Workshop Perdana',
                ],
            ],
            'report_submitted' => [
                'title' => 'Laporan Baru',
                'messages' => [
                    'Ahmad Rizki telah mengirim laporan mingguan',
                    'Laporan bulanan dari Siti Nurhaliza telah diterima',
                    'Laporan progress dari tim Budi Santoso sudah disubmit',
                ],
            ],
            'report_approved' => [
                'title' => 'Laporan Disetujui ✅',
                'messages' => [
                    'Laporan mingguan Anda telah disetujui!',
                    'Laporan bulanan telah direview dan disetujui',
                ],
            ],
            'deadline_reminder' => [
                'title' => 'Pengingat Deadline ⏰',
                'messages' => [
                    'Deadline pengajuan aplikasi Proyek Sanitasi akan berakhir dalam 3 hari',
                    'Reminder: Laporan akhir harus dikumpulkan dalam 5 hari',
                    'Jangan lupa! Deadline milestone pertama tinggal 2 hari lagi',
                ],
            ],
            'review_received' => [
                'title' => 'Review Diterima ⭐',
                'messages' => [
                    'Anda mendapat review bintang 5 dari Dinas Kesehatan!',
                    'Pemerintah Desa Sukamaju memberikan review positif untuk proyek Anda',
                ],
            ],
            'problem_published' => [
                'title' => 'Masalah Baru Dipublikasikan 📢',
                'messages' => [
                    'Proyek baru: Pembangunan Taman Bacaan Masyarakat di Desa Mekar',
                    'Masalah baru tersedia: Peningkatan Kualitas Air Bersih',
                ],
            ],
        ];

        // buat notifikasi untuk mahasiswa
        foreach ($students as $index => $student) {
            // setiap mahasiswa dapat 3-5 notifikasi random
            $notifCount = rand(3, 5);
            
            for ($i = 0; $i < $notifCount; $i++) {
                $typeKey = array_rand($notificationTypes);
                $typeData = $notificationTypes[$typeKey];
                $message = $typeData['messages'][array_rand($typeData['messages'])];
                
                // tentukan apakah notifikasi sudah dibaca (70% sudah dibaca)
                $isRead = rand(1, 100) <= 70;
                $createdAt = Carbon::now()->subDays(rand(0, 30));
                $readAt = $isRead ? $createdAt->copy()->addHours(rand(1, 48)) : null;
                
                Notification::create([
                    'user_id' => $student->user_id,
                    'type' => $typeKey,
                    'title' => $typeData['title'],
                    'message' => $message,
                    'data' => json_encode([
                        'generated' => true,
                        'index' => $i,
                    ]),
                    'action_url' => $this->getActionUrl($typeKey, 'student'),
                    'is_read' => $isRead,
                    'read_at' => $readAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        // buat notifikasi untuk instansi
        foreach ($institutions as $index => $institution) {
            // setiap instansi dapat 2-4 notifikasi random
            $notifCount = rand(2, 4);
            
            for ($i = 0; $i < $notifCount; $i++) {
                // instansi lebih banyak terima notifikasi application_submitted dan report_submitted
                $institutionTypes = ['application_submitted', 'report_submitted', 'project_started'];
                $typeKey = $institutionTypes[array_rand($institutionTypes)];
                $typeData = $notificationTypes[$typeKey];
                $message = $typeData['messages'][array_rand($typeData['messages'])];
                
                $isRead = rand(1, 100) <= 60;
                $createdAt = Carbon::now()->subDays(rand(0, 15));
                $readAt = $isRead ? $createdAt->copy()->addHours(rand(1, 24)) : null;
                
                Notification::create([
                    'user_id' => $institution->user_id,
                    'type' => $typeKey,
                    'title' => $typeData['title'],
                    'message' => $message,
                    'data' => json_encode([
                        'generated' => true,
                        'index' => $i,
                    ]),
                    'action_url' => $this->getActionUrl($typeKey, 'institution'),
                    'is_read' => $isRead,
                    'read_at' => $readAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        $this->command->info('✅ notifikasi dummy berhasil dibuat!');
    }

    /**
     * dapatkan action URL berdasarkan tipe notifikasi dan user type
     */
    private function getActionUrl(string $type, string $userType): ?string
    {
        $urls = [
            'student' => [
                'application_submitted' => '/student/applications',
                'application_accepted' => '/student/applications',
                'application_rejected' => '/student/applications',
                'project_started' => '/student/projects',
                'project_milestone' => '/student/projects',
                'report_approved' => '/student/projects',
                'deadline_reminder' => '/student/dashboard',
                'review_received' => '/student/portfolio',
                'problem_published' => '/student/browse-problems',
            ],
            'institution' => [
                'application_submitted' => '/institution/applications',
                'report_submitted' => '/institution/projects',
                'project_started' => '/institution/projects',
            ],
        ];

        return $urls[$userType][$type] ?? null;
    }
}