<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Application;
use App\Models\Student;
use App\Models\Problem;
use Carbon\Carbon;

/**
 * seeder untuk data dummy applications
 * fixed: prepared statement error dengan optimasi query
 * 
 * jalankan: php artisan db:seed --class=ApplicationsSeeder
 */
class ApplicationsSeeder extends Seeder
{
    /**
     * jalankan database seeds
     */
    public function run(): void
    {
        \DB::reconnect('pgsql');

        // disable query log untuk performa lebih baik
        DB::connection()->disableQueryLog();
        
        // ambil semua students dan problems yang tersedia
        $students = Student::all();
        $problems = Problem::where('status', 'open')->get();

        if ($students->isEmpty() || $problems->isEmpty()) {
            $this->command->warn('Tidak ada students atau problems. Jalankan DummyDataSeeder terlebih dahulu.');
            return;
        }

        $this->command->info('🎯 Target: minimal 25-30 accepted applications untuk projects');
        $this->command->info("📊 Available: {$students->count()} students, {$problems->count()} problems");

        // data motivasi untuk variasi
        $motivations = [
            'Saya sangat tertarik dengan proyek ini karena sesuai dengan bidang studi saya dan ingin berkontribusi untuk masyarakat.',
            'Proyek ini sangat menarik dan sesuai dengan passion saya di bidang pemberdayaan masyarakat.',
            'Saya tertarik untuk mengaplikasikan ilmu yang telah saya pelajari di kampus untuk membantu menyelesaikan masalah nyata.',
            'Sebagai mahasiswa yang peduli dengan sustainable development, saya ingin berkontribusi dalam proyek ini.',
            'Saya memiliki keterampilan yang relevan dan pengalaman organisasi yang dapat membantu kesuksesan proyek ini.',
            'Program KKN ini merupakan kesempatan emas untuk mengimplementasikan teori yang saya pelajari.',
            'Dengan latar belakang pendidikan saya, saya optimis dapat membantu menyelesaikan permasalahan yang ada.',
            'Saya memiliki komitmen tinggi untuk terlibat aktif dalam program ini.',
        ];

        $coverLetters = [
            'Dengan hormat, saya mengajukan diri untuk bergabung dalam proyek ini.',
            'Kepada Yth. Tim Seleksi, saya tertarik untuk berpartisipasi dalam proyek ini.',
            'Salam hormat, melalui surat ini saya bermaksud mengajukan aplikasi untuk program KKN.',
            'Yth. Panitia Seleksi, saya sangat tertarik untuk menjadi bagian dari proyek ini.',
        ];

        // hapus applications lama
        Application::truncate();

        $this->command->info('📝 Phase 1: Creating accepted applications...');
        
        // phase 1: buat accepted applications dulu (untuk projects)
        $acceptedCount = 0;
        $targetAccepted = max(30, ceil($students->count() * 0.5)); // minimal 30 atau 50% dari students
        $acceptedData = [];
        
        // shuffle students untuk distribusi acak
        $shuffledStudents = $students->shuffle();
        $studentIndex = 0;
        
        foreach ($problems as $problem) {
            $studentsNeeded = $problem->required_students;
            
            for ($i = 0; $i < $studentsNeeded && $acceptedCount < $targetAccepted; $i++) {
                if ($studentIndex >= $shuffledStudents->count()) {
                    break; // habis students
                }
                
                $student = $shuffledStudents[$studentIndex];
                $studentIndex++;
                
                $appliedAt = Carbon::now()->subDays(rand(30, 60));
                $reviewedAt = $appliedAt->copy()->addDays(rand(2, 7));
                $acceptedAt = $reviewedAt->copy()->addDays(rand(1, 3));
                
                $acceptedData[] = [
                    'student_id' => $student->id,
                    'problem_id' => $problem->id,
                    'status' => 'accepted',
                    'cover_letter' => $coverLetters[array_rand($coverLetters)],
                    'motivation' => $motivations[array_rand($motivations)],
                    'applied_at' => $appliedAt,
                    'reviewed_at' => $reviewedAt,
                    'accepted_at' => $acceptedAt,
                    'rejected_at' => null,
                    'feedback' => 'Selamat! Aplikasi Anda diterima. Kami terkesan dengan motivasi dan kualifikasi Anda.',
                    'created_at' => $appliedAt,
                    'updated_at' => $acceptedAt,
                ];
                
                $acceptedCount++;
                
                // batch insert setiap 50 records untuk performa
                if (count($acceptedData) >= 50) {
                    DB::table('applications')->insert($acceptedData);
                    $acceptedData = [];
                }
            }
        }
        
        // insert sisa accepted data
        if (!empty($acceptedData)) {
            DB::table('applications')->insert($acceptedData);
        }
        
        $this->command->info("✅ Created {$acceptedCount} accepted applications");

        $this->command->info('📝 Phase 2: Creating other applications...');
        
        // phase 2: buat aplikasi dengan status lain (pending, reviewed, rejected)
        $otherApplicationsData = [];
        $otherCount = 0;
        $targetOther = min(100, $students->count() * 2); // maksimal 100 atau 2x jumlah students
        
        $availableStudents = $students->whereNotIn('id', collect($shuffledStudents)->slice(0, $studentIndex)->pluck('id'));
        
        foreach ($availableStudents as $student) {
            if ($otherCount >= $targetOther) break;
            
            // setiap student bisa apply ke 1-3 problems
            $numApplications = rand(1, min(3, $problems->count()));
            $selectedProblems = $problems->random($numApplications);
            
            foreach ($selectedProblems as $problem) {
                $status = $this->randomStatus();
                
                $appliedAt = Carbon::now()->subDays(rand(5, 45));
                $reviewedAt = in_array($status, ['reviewed', 'rejected']) 
                    ? $appliedAt->copy()->addDays(rand(1, 5)) 
                    : null;
                $rejectedAt = $status === 'rejected' 
                    ? $reviewedAt->copy()->addDays(rand(1, 2)) 
                    : null;
                
                $feedback = match($status) {
                    'reviewed' => 'Aplikasi Anda sedang dalam proses review lebih lanjut.',
                    'rejected' => 'Terima kasih atas minat Anda. Saat ini kami memilih kandidat yang lebih sesuai dengan kebutuhan proyek.',
                    default => null,
                };
                
                $otherApplicationsData[] = [
                    'student_id' => $student->id,
                    'problem_id' => $problem->id,
                    'status' => $status,
                    'cover_letter' => $coverLetters[array_rand($coverLetters)],
                    'motivation' => $motivations[array_rand($motivations)],
                    'applied_at' => $appliedAt,
                    'reviewed_at' => $reviewedAt,
                    'accepted_at' => null,
                    'rejected_at' => $rejectedAt,
                    'feedback' => $feedback,
                    'created_at' => $appliedAt,
                    'updated_at' => $rejectedAt ?? $reviewedAt ?? $appliedAt,
                ];
                
                $otherCount++;
                
                // batch insert setiap 50 records
                if (count($otherApplicationsData) >= 50) {
                    try {
                        DB::table('applications')->insert($otherApplicationsData);
                        $otherApplicationsData = [];
                    } catch (\Exception $e) {
                        // skip jika duplicate
                        $otherApplicationsData = [];
                    }
                }
            }
        }
        
        // insert sisa data
        if (!empty($otherApplicationsData)) {
            try {
                DB::table('applications')->insert($otherApplicationsData);
            } catch (\Exception $e) {
                // skip jika duplicate
            }
        }
        
        $this->command->info("✅ Created {$otherCount} other applications");

        // update applications_count di problems
        $this->command->info('📊 Updating problem statistics...');
        foreach ($problems as $problem) {
            $count = Application::where('problem_id', $problem->id)->count();
            $problem->update(['applications_count' => $count]);
        }

        // enable query log kembali
        DB::connection()->enableQueryLog();

        // tampilkan statistik final
        $totalCreated = $acceptedCount + $otherCount;
        $this->command->newLine();
        $this->command->info("✅ {$totalCreated} applications berhasil dibuat!");
        $this->command->newLine();
        $this->command->info('📊 Status distribusi:');
        $this->command->table(
            ['Status', 'Count', 'Percentage'],
            [
                ['Pending', Application::where('status', 'pending')->count(), round(Application::where('status', 'pending')->count() / max($totalCreated, 1) * 100, 1) . '%'],
                ['Reviewed', Application::where('status', 'reviewed')->count(), round(Application::where('status', 'reviewed')->count() / max($totalCreated, 1) * 100, 1) . '%'],
                ['Accepted', Application::where('status', 'accepted')->count(), round(Application::where('status', 'accepted')->count() / max($totalCreated, 1) * 100, 1) . '%'],
                ['Rejected', Application::where('status', 'rejected')->count(), round(Application::where('status', 'rejected')->count() / max($totalCreated, 1) * 100, 1) . '%'],
            ]
        );
        
        $finalAcceptedCount = Application::where('status', 'accepted')->count();
        if ($finalAcceptedCount >= 25) {
            $this->command->info("\n🎉 Success! {$finalAcceptedCount} accepted applications (target: 25+)");
        } else {
            $this->command->warn("\n⚠️ Warning: Only {$finalAcceptedCount} accepted applications (target: 25+)");
        }
    }

    /**
     * generate random status (untuk other applications)
     */
    private function randomStatus(): string
    {
        $statuses = ['pending', 'reviewed', 'rejected'];
        $weights = [40, 30, 30]; // persentase
        
        $rand = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'pending';
    }
}