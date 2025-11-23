<?php

namespace Database\Seeders;

use App\Models\Friend;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * seeder untuk membuat data dummy pertemanan antar mahasiswa
 * 
 * data dummy:
 * - 50+ pertemanan yang sudah accepted
 * - 20+ pending friend requests
 * - distribusi pertemanan antar universitas
 * 
 * jalankan: php artisan db:seed --class=FriendSeeder
 */
class FriendSeeder extends Seeder
{
    /**
     * jalankan database seeds
     */
    public function run(): void
    {
        \DB::reconnect('pgsql');

        $students = Student::all();
        
        if ($students->count() < 2) {
            $this->command->warn('Jumlah mahasiswa kurang dari 2, skip friend seeder');
            return;
        }

        $this->command->info('Mulai seeding friends...');
        
        // array untuk tracking pertemanan yang sudah dibuat (avoid duplicate)
        $createdFriendships = [];
        
        // buat accepted friendships (50+ pertemanan)
        $acceptedCount = 0;
        $targetAccepted = 50;
        
        while ($acceptedCount < $targetAccepted) {
            $requester = $students->random();
            $receiver = $students->where('id', '!=', $requester->id)->random();
            
            // buat unique key untuk tracking
            $friendshipKey = $this->getFriendshipKey($requester->id, $receiver->id);
            
            // skip jika sudah ada
            if (in_array($friendshipKey, $createdFriendships)) {
                continue;
            }
            
            Friend::create([
                'requester_id' => $requester->id,
                'receiver_id' => $receiver->id,
                'status' => 'accepted',
                'responded_at' => now()->subDays(rand(1, 30))
            ]);
            
            $createdFriendships[] = $friendshipKey;
            $acceptedCount++;
        }
        
        $this->command->info("[OK] {$acceptedCount} accepted friendships dibuat");
        
        // buat pending friend requests (20 permintaan)
        $pendingCount = 0;
        $targetPending = 20;
        
        while ($pendingCount < $targetPending) {
            $requester = $students->random();
            $receiver = $students->where('id', '!=', $requester->id)->random();
            
            $friendshipKey = $this->getFriendshipKey($requester->id, $receiver->id);
            
            if (in_array($friendshipKey, $createdFriendships)) {
                continue;
            }
            
            Friend::create([
                'requester_id' => $requester->id,
                'receiver_id' => $receiver->id,
                'status' => 'pending',
                'message' => $this->getRandomMessage()
            ]);
            
            $createdFriendships[] = $friendshipKey;
            $pendingCount++;
        }
        
        $this->command->info("[OK] {$pendingCount} pending friend requests dibuat");
        
        // buat beberapa rejected requests untuk variasi (5 requests)
        $rejectedCount = 0;
        $targetRejected = 5;
        
        while ($rejectedCount < $targetRejected) {
            $requester = $students->random();
            $receiver = $students->where('id', '!=', $requester->id)->random();
            
            $friendshipKey = $this->getFriendshipKey($requester->id, $receiver->id);
            
            if (in_array($friendshipKey, $createdFriendships)) {
                continue;
            }
            
            Friend::create([
                'requester_id' => $requester->id,
                'receiver_id' => $receiver->id,
                'status' => 'rejected',
                'responded_at' => now()->subDays(rand(1, 10))
            ]);
            
            $createdFriendships[] = $friendshipKey;
            $rejectedCount++;
        }
        
        $this->command->info("[OK] {$rejectedCount} rejected requests dibuat");
        
        // buat beberapa mahasiswa dengan banyak koneksi (power users)
        $this->createPowerUsers($students, $createdFriendships, 5);
        
        $totalFriendships = Friend::count();
        $this->command->info("[SUCCESS] Total {$totalFriendships} friendships berhasil dibuat!");
    }
    
    /**
     * helper untuk membuat unique key dari friendship
     */
    private function getFriendshipKey($id1, $id2)
    {
        // sort ids agar key konsisten regardless of order
        $ids = [$id1, $id2];
        sort($ids);
        return implode('-', $ids);
    }
    
    /**
     * helper untuk mendapatkan random message
     */
    private function getRandomMessage()
    {
        $messages = [
            'Hai! Saya tertarik dengan proyek KKN yang Anda kerjakan. Mari terhubung!',
            'Halo, saya lihat kita dari jurusan yang sama. Boleh berteman?',
            'Hi! Tertarik untuk berkolaborasi di proyek KKN bersama?',
            'Halo! Saya melihat kita punya minat yang sama di bidang SDG. Mari terhubung!',
            'Hai, saya juga sedang mengerjakan proyek serupa. Mungkin kita bisa sharing pengalaman?',
            null, // beberapa request tanpa message
            null,
            null
        ];
        
        return $messages[array_rand($messages)];
    }
    
    /**
     * buat beberapa power users dengan banyak koneksi
     */
    private function createPowerUsers($students, &$createdFriendships, $count)
    {
        $this->command->info('Membuat power users dengan banyak koneksi...');
        
        for ($i = 0; $i < $count; $i++) {
            $powerUser = $students->random();
            $targetConnections = rand(15, 25);
            $connections = 0;
            
            while ($connections < $targetConnections) {
                $friend = $students->where('id', '!=', $powerUser->id)->random();
                $friendshipKey = $this->getFriendshipKey($powerUser->id, $friend->id);
                
                if (in_array($friendshipKey, $createdFriendships)) {
                    continue;
                }
                
                Friend::create([
                    'requester_id' => $powerUser->id,
                    'receiver_id' => $friend->id,
                    'status' => 'accepted',
                    'responded_at' => now()->subDays(rand(1, 60))
                ]);
                
                $createdFriendships[] = $friendshipKey;
                $connections++;
            }
            
            $userNumber = $i + 1;
            $this->command->info("  [OK] Power user #{$userNumber} dibuat dengan {$connections} koneksi");
        }
    }
}