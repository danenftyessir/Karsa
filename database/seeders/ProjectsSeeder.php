<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\ProjectReport;
use App\Models\Document;
use App\Models\Review;
use App\Models\Application;
use App\Models\Student;
use Carbon\Carbon;

/**
 * seeder untuk membuat data projects, milestones, reports, documents, dan reviews
 *
 * FIXED: batch insert untuk menghindari prepared statement error di PostgreSQL
 * FIXED: gunakan file PDF yang benar-benar ada di Supabase
 *
 * path: database/seeders/ProjectsSeeder.php
 * jalankan: php artisan db:seed --class=ProjectsSeeder
 */
class ProjectsSeeder extends Seeder
{
    /**
     * Daftar file PDF yang ada di Supabase folder documents/reports/
     */
    private array $existingPdfs = [
        '3341b-laporan_kkn_hasbi_mudzaki_fix-1-.pdf',
        'aaLAPORAN-PROGRAM-KERJA-KKN.pdf',
        'bc4f599c360deae829ef0952f9200a4f.pdf',
        'd5460592f2ee74a2f9f5910138d650e6.pdf',
        'f3f3ec539ee2d963e804d3a964b3290f.pdf',
        'KKN_III.D.3_REG.96_2022.pdf',
        'LAPORAN AKHIR KKN .pdf',
        'laporan akhir KKN PPM OK.pdf',
        'LAPORAN KELOMPOK KKN 1077fix.pdf',
        'LAPORAN KKN DEMAPESA.pdf',
        'LAPORAN KKN KELOMPOK 2250.pdf',
        'LAPORAN KKN_1.A.2_REG.119_2024.pdf',
        'LAPORAN KKN.pdf',
        'laporan_3460160906115724.pdf',
        'laporan_akhir_201_35_2.pdf',
        'laporan_akhir_3011_45_5.pdf',
        'laporan-kelompok.pdf',
        'Laporan-KKN-2019.pdf',
        'Laporan-Tugas-Akhir-KKN-156.pdf',
    ];

    /**
     * run seeder
     */
    public function run(): void
    {
        \DB::reconnect('pgsql');

        $this->command->info('🚀 Membuat projects dari accepted applications...');

        // disable query log untuk performa
        DB::connection()->disableQueryLog();
        
        // ambil aplikasi yang diterima dengan eager loading
        $acceptedApplications = Application::with([
            'problem',
            'problem.institution',
            'problem.institution.user',
            'problem.province',
            'problem.regency',
            'student',
            'student.user',
            'student.university'
        ])
        ->where('status', 'accepted')
        ->get();

        if ($acceptedApplications->isEmpty()) {
            $this->command->warn('Tidak ada aplikasi dengan status accepted. Jalankan ApplicationsSeeder terlebih dahulu.');
            return;
        }

        $this->command->info("📊 Ditemukan {$acceptedApplications->count()} accepted applications");

        $projectsData = [];
        $milestonesData = [];
        $reportsData = [];
        $documentsData = [];
        $reviewsData = [];
        
        $batchCounter = 0; // counter untuk batch
        $projectIndexInBatch = 0; // counter untuk index dalam batch
        
        foreach ($acceptedApplications as $application) {
            $problem = $application->problem;

            // tentukan status project (75% completed, 25% active) - BANYAK YANG SUDAH SELESAI
            $isCompleted = rand(1, 100) <= 75;
            $status = $isCompleted ? 'completed' : 'active';
            
            // tentukan progress
            $progress = $isCompleted ? 100 : rand(20, 90);
            
            // tentukan tanggal
            $startDate = Carbon::now()->subMonths(rand(1, 6));
            $endDate = $startDate->copy()->addMonths(rand(2, 4));
            
            $now = Carbon::now();
            
            // data project
            $projectData = [
                'application_id' => $application->id,
                'student_id' => $application->student_id,
                'problem_id' => $application->problem_id,
                'institution_id' => $problem->institution_id,
                'title' => $problem->title,
                'description' => $problem->description,
                'status' => $status,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'progress_percentage' => $progress,
                'actual_end_date' => $isCompleted ? $endDate->copy()->addDays(rand(1, 5)) : null,
                'role_in_team' => $this->getRandomRole(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            
            $projectsData[] = $projectData;
            $projectIndexInBatch++;
            
            // data milestones untuk project ini
            // gunakan application_id sebagai reference karena itu unique
            $milestones = [
                [
                    'title' => 'Survei Dan Pemetaan Masalah',
                    'description' => 'Melakukan survei lapangan dan identifikasi masalah utama',
                    'target_date' => $startDate->copy()->addWeeks(1),
                    'status' => 'completed',
                ],
                [
                    'title' => 'Perencanaan Program',
                    'description' => 'Menyusun rencana program dan strategi pelaksanaan',
                    'target_date' => $startDate->copy()->addWeeks(2),
                    'status' => $progress >= 40 ? 'completed' : 'in_progress',
                ],
                [
                    'title' => 'Pelaksanaan Kegiatan Utama',
                    'description' => 'Implementasi program sesuai rencana yang telah disusun',
                    'target_date' => $startDate->copy()->addWeeks(6),
                    'status' => $progress >= 70 ? 'completed' : 'pending',
                ],
                [
                    'title' => 'Evaluasi Dan Dokumentasi',
                    'description' => 'Evaluasi hasil kegiatan dan penyusunan laporan akhir',
                    'target_date' => $endDate,
                    'status' => $status === 'completed' ? 'completed' : 'pending',
                ],
            ];

            foreach ($milestones as $index => $milestone) {
                $milestonesData[] = [
                    'application_id' => $application->id, // gunakan application_id sebagai reference
                    'title' => $milestone['title'],
                    'description' => $milestone['description'],
                    'order' => $index,
                    'target_date' => $milestone['target_date'],
                    'status' => $milestone['status'],
                    'progress_percentage' => $milestone['status'] === 'completed' ? 100 : ($milestone['status'] === 'in_progress' ? rand(30, 70) : 0),
                    'completed_at' => $milestone['status'] === 'completed' ? $milestone['target_date']->copy()->subDays(rand(0, 3)) : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            
            // data progress reports
            $reportCount = rand(2, 4);
            for ($i = 0; $i < $reportCount; $i++) {
                $type = $i === 0 ? 'weekly' : ($i === $reportCount - 1 ? 'final' : 'monthly');
                
                $reportsData[] = [
                    'application_id' => $application->id,
                    'student_id' => $application->student_id,
                    'type' => $type,
                    'title' => $this->getReportTitle($type, $i + 1),
                    'summary' => 'Ringkasan kegiatan minggu/bulan ini untuk project ' . $problem->title,
                    'activities' => 'Kegiatan yang telah dilaksanakan: sosialisasi program, koordinasi dengan masyarakat, dan pelaksanaan kegiatan lapangan.',
                    'challenges' => rand(1, 100) <= 70 ? 'Cuaca yang kurang mendukung dan keterbatasan akses transportasi.' : null,
                    'next_plans' => 'Melanjutkan kegiatan sesuai rencana dan melakukan evaluasi berkala.',
                    'period_start' => $startDate->copy()->addWeeks($i * ($type === 'weekly' ? 1 : 4)),
                    'period_end' => $startDate->copy()->addWeeks(($i + 1) * ($type === 'weekly' ? 1 : 4)),
                    'document_path' => null,
                    'photos' => json_encode([]),
                    'status' => ['pending', 'reviewed', 'approved'][rand(0, 2)],
                    'institution_feedback' => rand(1, 100) <= 50 ? 'Laporan sudah baik, lanjutkan kegiatan dengan semangat!' : null,
                    'reviewed_at' => rand(1, 100) <= 50 ? $now->copy()->subDays(rand(1, 7)) : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            
            // jika completed, buat review dan document
            if ($isCompleted) {
                // review data - dari institusi ke mahasiswa
                $reviewsData[] = [
                    'application_id' => $application->id,
                    'project_id' => 0, // akan diupdate nanti
                    'reviewer_id' => $problem->institution->user_id, // institusi yang review
                    'reviewee_id' => $application->student->user_id, // mahasiswa yang direview
                    'type' => 'institution_to_student',
                    'rating' => rand(4, 5),
                    'professionalism_rating' => rand(4, 5),
                    'communication_rating' => rand(4, 5),
                    'quality_rating' => rand(4, 5),
                    'timeliness_rating' => rand(3, 5),
                    'review_text' => 'Mahasiswa menunjukkan dedikasi tinggi dan berhasil menyelesaikan program dengan baik. Sangat membantu masyarakat setempat.',
                    'strengths' => 'Sangat kooperatif dan cepat beradaptasi dengan lingkungan.',
                    'improvements' => null,
                    'is_public' => true,
                    'is_featured' => false,
                    'created_at' => $endDate->copy()->addDays(rand(1, 7)),
                    'updated_at' => $endDate->copy()->addDays(rand(1, 7)),
                ];
                
                // ✅ PENTING: Ambil random PDF dari daftar file yang ada di Supabase
                $randomPdf = $this->existingPdfs[array_rand($this->existingPdfs)];

                // document data
                $documentsData[] = [
                    'application_id' => $application->id,
                    'title' => 'Laporan Akhir KKN - ' . $problem->title,
                    'description' => 'Laporan lengkap pelaksanaan program KKN di ' . $problem->regency->name . ' dengan fokus pada ' . $problem->title,
                    'file_path' => 'documents/reports/' . $randomPdf, // ✅ Gunakan file yang benar-benar ada
                    'file_size' => rand(500000, 2000000),
                    'file_type' => 'application/pdf',
                    'uploaded_by' => $application->student->user_id,
                    'categories' => json_encode([$problem->sdg_categories[0] ?? 'community_development']),
                    'tags' => json_encode(['KKN', 'community development', $problem->regency->name]),
                    'author_name' => $application->student->first_name . ' ' . $application->student->last_name,
                    'institution_name' => $problem->institution->name,
                    'university_name' => $application->student->university->name,
                    'province_id' => $problem->province_id,
                    'regency_id' => $problem->regency_id,
                    'year' => $endDate->year,
                    'download_count' => rand(0, 50),
                    'view_count' => rand(0, 100),
                    'citation_count' => rand(0, 10),
                    'is_public' => true,
                    'is_featured' => rand(1, 100) <= 20, // 20% featured
                    'status' => 'approved',
                    'approved_at' => $endDate->copy()->addDays(rand(7, 14)),
                    'created_at' => $endDate->copy()->addDays(rand(3, 10)),
                    'updated_at' => $endDate->copy()->addDays(rand(3, 10)),
                ];
            }
            
            // batch insert setiap 20 projects untuk menghindari memory issues
            if (count($projectsData) >= 20) {
                $this->batchInsertData($projectsData, $milestonesData, $reportsData, $documentsData, $reviewsData);
                
                // reset arrays dan counter
                $projectsData = [];
                $milestonesData = [];
                $reportsData = [];
                $documentsData = [];
                $reviewsData = [];
                $projectIndexInBatch = 0;
                $batchCounter++;
            }
        }

        // insert sisa data
        if (!empty($projectsData)) {
            $this->batchInsertData($projectsData, $milestonesData, $reportsData, $documentsData, $reviewsData);
        }

        // enable query log kembali
        DB::connection()->enableQueryLog();
        
        $this->command->newLine();
        $this->command->info('✅ Projects seeder selesai!');
        $this->command->info('📊 Total created:');
        $this->command->info('   - Projects: ' . Project::count());
        $this->command->info('   - Milestones: ' . ProjectMilestone::count());
        $this->command->info('   - Reports: ' . ProjectReport::count());
        $this->command->info('   - Documents: ' . Document::whereNotNull('project_id')->count());
        $this->command->info('   - Reviews: ' . Review::count());
    }

    /**
     * batch insert data dengan mapping project_id yang benar
     */
    private function batchInsertData($projectsData, $milestonesData, $reportsData, $documentsData, $reviewsData)
    {
        // insert projects terlebih dahulu
        foreach (array_chunk($projectsData, 50) as $chunk) {
            DB::table('projects')->insert($chunk);
            
            // clear prepared statements
            try {
                DB::connection()->getPdo()->exec('DEALLOCATE ALL');
            } catch (\Exception $e) {
                // ignore error jika tidak ada prepared statements
            }
        }
        
        // ambil semua project ids yang baru saja diinsert berdasarkan application_id
        $applicationIds = array_column($projectsData, 'application_id');
        $insertedProjects = DB::table('projects')
            ->whereIn('application_id', $applicationIds)
            ->pluck('id', 'application_id')
            ->toArray();
        
        // update project_id di milestones menggunakan application_id sebagai reference
        if (!empty($milestonesData)) {
            foreach ($milestonesData as &$milestone) {
                $appId = $milestone['application_id'];
                if (isset($insertedProjects[$appId])) {
                    $milestone['project_id'] = $insertedProjects[$appId];
                }
                unset($milestone['application_id']); // hapus application_id karena tidak ada di schema
            }
            unset($milestone);
            
            foreach (array_chunk($milestonesData, 100) as $chunk) {
                DB::table('project_milestones')->insert($chunk);
                try {
                    DB::connection()->getPdo()->exec('DEALLOCATE ALL');
                } catch (\Exception $e) {
                    // ignore
                }
            }
        }
        
        // update project_id di reports menggunakan application_id sebagai reference
        if (!empty($reportsData)) {
            foreach ($reportsData as &$report) {
                $appId = $report['application_id'];
                if (isset($insertedProjects[$appId])) {
                    $report['project_id'] = $insertedProjects[$appId];
                }
                unset($report['application_id']); // hapus application_id
            }
            unset($report);
            
            foreach (array_chunk($reportsData, 100) as $chunk) {
                DB::table('project_reports')->insert($chunk);
                try {
                    DB::connection()->getPdo()->exec('DEALLOCATE ALL');
                } catch (\Exception $e) {
                    // ignore
                }
            }
        }
        
        // update project_id di documents menggunakan application_id sebagai reference
        if (!empty($documentsData)) {
            foreach ($documentsData as &$document) {
                $appId = $document['application_id'];
                if (isset($insertedProjects[$appId])) {
                    $document['project_id'] = $insertedProjects[$appId];
                }
                unset($document['application_id']); // hapus application_id
            }
            unset($document);
            
            foreach (array_chunk($documentsData, 100) as $chunk) {
                DB::table('documents')->insert($chunk);
                try {
                    DB::connection()->getPdo()->exec('DEALLOCATE ALL');
                } catch (\Exception $e) {
                    // ignore
                }
            }
        }
        
        // update project_id di reviews menggunakan application_id sebagai reference
        if (!empty($reviewsData)) {
            foreach ($reviewsData as &$review) {
                $appId = $review['application_id'];
                if (isset($insertedProjects[$appId])) {
                    $review['project_id'] = $insertedProjects[$appId];
                }
                unset($review['application_id']); // hapus application_id
            }
            unset($review);
            
            foreach (array_chunk($reviewsData, 100) as $chunk) {
                DB::table('reviews')->insert($chunk);
                try {
                    DB::connection()->getPdo()->exec('DEALLOCATE ALL');
                } catch (\Exception $e) {
                    // ignore
                }
            }
        }
    }

    /**
     * dapatkan random role in team
     */
    protected function getRandomRole(): string
    {
        $roles = [
            'Project Leader',
            'Field Coordinator',
            'Documentation Specialist',
            'Community Liaison',
            'Technical Advisor',
            'Research Analyst',
            'Team Member',
        ];

        return $roles[array_rand($roles)];
    }

    /**
     * dapatkan judul report berdasarkan tipe
     */
    protected function getReportTitle(string $type, int $number): string
    {
        $titles = [
            'weekly' => "Laporan Mingguan #{$number}",
            'monthly' => "Laporan Bulanan #{$number}",
            'final' => 'Laporan Akhir Pelaksanaan Program',
        ];

        return $titles[$type] ?? "Laporan #{$number}";
    }
}