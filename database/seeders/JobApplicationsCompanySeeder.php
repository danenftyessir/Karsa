<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SupabaseService;
use Carbon\Carbon;

/**
 * JOB APPLICATIONS SEEDER FOR COMPANIES
 *
 * Requirements:
 * - >5 active applications per company
 * - ALL statuses must be represented: pending, reviewed, shortlisted, rejected, accepted
 * - Time distribution for diagrams (applications spread over time)
 */
class JobApplicationsCompanySeeder extends Seeder
{
    protected $supabase;

    public function __construct()
    {
        $this->supabase = new SupabaseService();
    }

    public function run(): void
    {
        $this->command->info('🌱 Seeding job applications for companies...');

        // Get all job postings - use DB facade
        $jobPostings = \DB::table('job_postings')->select('id', 'company_id', 'title')->get();

        if ($jobPostings->isEmpty()) {
            $this->command->warn('⚠️  No job postings found. Run JobPostingKKNSeeder first.');
            return;
        }

        // Get all students - use DB facade
        $students = \DB::table('users')->select('id')->where('user_type', 'student')->get();

        if ($students->isEmpty()) {
            $this->command->warn('⚠️  No students found. Run UserSeeder first.');
            return;
        }

        $this->command->info("📊 Found " . $jobPostings->count() . " job postings and " . $students->count() . " students");

        $totalApplications = 0;
        $statusDistribution = [
            'pending' => 0,
            'reviewed' => 0,
            'shortlisted' => 0,
            'rejected' => 0,
            'accepted' => 0,
        ];

        // Group job postings by company
        $jobsByCompany = [];
        foreach ($jobPostings as $job) {
            if (!isset($jobsByCompany[$job->company_id])) {
                $jobsByCompany[$job->company_id] = [];
            }
            $jobsByCompany[$job->company_id][] = $job;
        }

        $companyIndex = 0;
        foreach ($jobsByCompany as $companyId => $companyJobs) {
            // Reconnect every 10 companies to prevent prepared statement errors
            if ($companyIndex % 10 == 0) {
                \DB::reconnect('pgsql');
            }
            $companyIndex++;

            // Check if this company already has enough applications
            $existingApplicationsCount = \DB::table('job_applications')
                ->whereIn('job_posting_id', array_column($companyJobs, 'id'))
                ->count();

            if ($existingApplicationsCount >= 6) {
                // Skip this company if it already has enough applications
                continue;
            }

            // Ensure each company gets >5 applications distributed across its jobs
            $applicationsPerCompany = rand(6, 12) - $existingApplicationsCount;

            if ($applicationsPerCompany <= 0) {
                continue; // Already has enough
            }

            // Track status usage to ensure all statuses are used
            $companyStatusCounts = [
                'pending' => 0,
                'reviewed' => 0,
                'shortlisted' => 0,
                'rejected' => 0,
                'accepted' => 0,
            ];

            $applicationsData = [];
            $usedStudents = []; // Track to avoid duplicates per company

            for ($i = 0; $i < $applicationsPerCompany; $i++) {
                // Select random job from this company
                $job = $companyJobs[array_rand($companyJobs)];

                // Select random student who hasn't applied to this company yet
                $availableStudents = $students->filter(function($s) use ($usedStudents) {
                    return !in_array($s->id, $usedStudents);
                })->values()->all(); // Convert to array

                if (empty($availableStudents)) {
                    break; // No more available students
                }

                $student = $availableStudents[array_rand($availableStudents)];
                $usedStudents[] = $student->id;

                // Determine status - ensure all statuses are covered
                $status = $this->determineStatus($i, $applicationsPerCompany, $companyStatusCounts);
                $companyStatusCounts[$status]++;

                // Generate application with time variance
                $applicationData = $this->generateApplication($job->id, $student->id, $status);
                $applicationsData[] = $applicationData;

                $statusDistribution[$status]++;
                $totalApplications++;

                // Batch insert every 50 applications
                if (count($applicationsData) >= 50) {
                    try {
                        \DB::table('job_applications')->insert($applicationsData);
                        $applicationsData = [];
                    } catch (\Exception $e) {
                        $this->command->error("❌ Error inserting applications: " . $e->getMessage());
                        $applicationsData = [];
                    }
                }
            }

            // Insert remaining applications for this company
            if (!empty($applicationsData)) {
                try {
                    \DB::table('job_applications')->insert($applicationsData);
                } catch (\Exception $e) {
                    $this->command->error("❌ Error inserting applications: " . $e->getMessage());
                }
            }

            if ($totalApplications % 50 == 0) {
                $this->command->info("   ... created $totalApplications applications");
            }
        }

        // Update applications_count for each job posting
        $this->command->info('📊 Updating job posting statistics...');
        $updateCount = 0;
        foreach ($jobPostings as $index => $job) {
            // Reconnect every 10 iterations to prevent prepared statement errors
            if ($index % 10 == 0) {
                \DB::reconnect('pgsql');
            }

            try {
                $count = \DB::table('job_applications')->where('job_posting_id', $job->id)->count();
                \DB::table('job_postings')->where('id', $job->id)->update(['applications_count' => $count]);
                $updateCount++;

                if ($updateCount % 100 == 0) {
                    $this->command->info("   ... updated $updateCount job postings");
                }
            } catch (\Exception $e) {
                // If prepared statement error, reconnect and retry once
                if (strpos($e->getMessage(), 'prepared statement') !== false) {
                    \DB::reconnect('pgsql');
                    try {
                        $count = \DB::table('job_applications')->where('job_posting_id', $job->id)->count();
                        \DB::table('job_postings')->where('id', $job->id)->update(['applications_count' => $count]);
                        $updateCount++;
                    } catch (\Exception $retryError) {
                        $this->command->error("❌ Failed to update job posting {$job->id}: " . $retryError->getMessage());
                    }
                } else {
                    $this->command->error("❌ Failed to update job posting {$job->id}: " . $e->getMessage());
                }
            }
        }

        // Display results
        $this->command->info('');
        $this->command->info('✅ Job applications seeding completed!');
        $this->command->info("📊 Total applications created: $totalApplications");
        $this->command->newLine();
        $this->command->info('📈 Status distribution:');
        $this->command->table(
            ['Status', 'Count', 'Percentage'],
            [
                ['Pending', $statusDistribution['pending'], round($statusDistribution['pending'] / max($totalApplications, 1) * 100, 1) . '%'],
                ['Reviewed', $statusDistribution['reviewed'], round($statusDistribution['reviewed'] / max($totalApplications, 1) * 100, 1) . '%'],
                ['Shortlisted', $statusDistribution['shortlisted'], round($statusDistribution['shortlisted'] / max($totalApplications, 1) * 100, 1) . '%'],
                ['Rejected', $statusDistribution['rejected'], round($statusDistribution['rejected'] / max($totalApplications, 1) * 100, 1) . '%'],
                ['Accepted', $statusDistribution['accepted'], round($statusDistribution['accepted'] / max($totalApplications, 1) * 100, 1) . '%'],
            ]
        );

        // Verify all statuses have data
        $allStatusesUsed = true;
        foreach ($statusDistribution as $status => $count) {
            if ($count == 0) {
                $this->command->error("⚠️  WARNING: Status '$status' has no applications!");
                $allStatusesUsed = false;
            }
        }

        if ($allStatusesUsed) {
            $this->command->info('✅ All 5 statuses are represented!');
        }
    }

    /**
     * Determine status ensuring all statuses are used
     */
    private function determineStatus($index, $total, $statusCounts): string
    {
        $allStatuses = ['pending', 'reviewed', 'shortlisted', 'rejected', 'accepted'];

        // For first 5 applications, ensure each status is used at least once
        if ($index < 5) {
            return $allStatuses[$index];
        }

        // After that, distribute randomly with weights
        $weights = [
            'pending' => 25,      // 25%
            'reviewed' => 20,     // 20%
            'shortlisted' => 20,  // 20%
            'rejected' => 20,     // 20%
            'accepted' => 15,     // 15%
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $status => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return 'pending';
    }

    /**
     * Generate application data with proper timestamps
     */
    private function generateApplication($jobPostingId, $userId, $status): array
    {
        // Create applications spread over last 90 days for time-based diagrams
        $appliedAt = Carbon::now()->subDays(rand(1, 90));

        $coverLetters = [
            'Saya sangat tertarik dengan posisi ini dan yakin dapat berkontribusi dengan baik.',
            'Dengan pengalaman dan keterampilan saya, saya siap memberikan kontribusi terbaik.',
            'Saya memiliki passion yang kuat di bidang ini dan ingin bergabung dengan tim Anda.',
            'Background pendidikan saya sangat relevan dengan posisi yang ditawarkan.',
            'Saya excited untuk dapat berkontribusi dalam program ini.',
        ];

        $data = [
            'job_posting_id' => $jobPostingId,
            'user_id' => $userId,
            'status' => $status,
            'cover_letter' => $coverLetters[array_rand($coverLetters)],
            'resume_path' => null,
            'answers' => null,
            'notes' => null,
            'viewed_at' => null,
            'responded_at' => null,
            'created_at' => $appliedAt->toDateTimeString(),
            'updated_at' => $appliedAt->toDateTimeString(),
        ];

        // Add timestamps based on status
        switch ($status) {
            case 'reviewed':
                $viewedAt = $appliedAt->copy()->addDays(rand(1, 3));
                $data['viewed_at'] = $viewedAt->toDateTimeString();
                $data['updated_at'] = $viewedAt->toDateTimeString();
                $data['notes'] = 'Kandidat menarik, perlu review lebih lanjut.';
                break;

            case 'shortlisted':
                $viewedAt = $appliedAt->copy()->addDays(rand(1, 3));
                $respondedAt = $viewedAt->copy()->addDays(rand(1, 2));
                $data['viewed_at'] = $viewedAt->toDateTimeString();
                $data['responded_at'] = $respondedAt->toDateTimeString();
                $data['updated_at'] = $respondedAt->toDateTimeString();
                $data['notes'] = 'Kandidat shortlisted untuk tahap interview.';
                break;

            case 'rejected':
                $viewedAt = $appliedAt->copy()->addDays(rand(1, 5));
                $respondedAt = $viewedAt->copy()->addDays(rand(1, 3));
                $data['viewed_at'] = $viewedAt->toDateTimeString();
                $data['responded_at'] = $respondedAt->toDateTimeString();
                $data['updated_at'] = $respondedAt->toDateTimeString();
                $data['notes'] = 'Terima kasih atas aplikasinya. Saat ini kami memilih kandidat lain yang lebih sesuai.';
                break;

            case 'accepted':
                $viewedAt = $appliedAt->copy()->addDays(rand(1, 4));
                $respondedAt = $viewedAt->copy()->addDays(rand(2, 5));
                $data['viewed_at'] = $viewedAt->toDateTimeString();
                $data['responded_at'] = $respondedAt->toDateTimeString();
                $data['updated_at'] = $respondedAt->toDateTimeString();
                $data['notes'] = 'Selamat! Aplikasi Anda diterima. Silakan tunggu informasi selanjutnya.';
                break;

            case 'pending':
            default:
                // No additional timestamps for pending
                break;
        }

        return $data;
    }
}
