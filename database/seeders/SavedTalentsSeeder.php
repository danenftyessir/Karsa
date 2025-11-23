<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SupabaseService;
use Carbon\Carbon;

/**
 * SAVED TALENTS SEEDER FOR COMPANIES
 *
 * Requirements:
 * - >5 saved talents per company
 * - ALL categories must be represented: Baru, Dihubungi, Interview, Ditawari, Ditolak
 * - No category can be empty
 */
class SavedTalentsSeeder extends Seeder
{
    protected $supabase;

    // All 5 talent categories - MUST all be used
    private $categories = [
        'Baru',        // New/Fresh talent
        'Dihubungi',   // Contacted
        'Interview',   // Interviewed
        'Ditawari',    // Offered
        'Ditolak',     // Rejected
    ];

    public function __construct()
    {
        $this->supabase = new SupabaseService();
    }

    public function run(): void
    {
        $this->command->info('🌱 Seeding saved talents for companies...');

        // Get all companies - use DB facade
        $companies = \DB::table('companies')->select('id', 'name')->get();

        if ($companies->isEmpty()) {
            $this->command->warn('⚠️  No companies found. Run CompanySeeder first.');
            return;
        }

        // Get all students - use DB facade
        $students = \DB::table('users')->select('id')->where('user_type', 'student')->get();

        if ($students->isEmpty()) {
            $this->command->warn('⚠️  No students found. Run UserSeeder first.');
            return;
        }

        $this->command->info("📊 Found " . $companies->count() . " companies and " . $students->count() . " students");

        $totalSavedTalents = 0;
        $categoryDistribution = [
            'Baru' => 0,
            'Dihubungi' => 0,
            'Interview' => 0,
            'Ditawari' => 0,
            'Ditolak' => 0,
        ];

        $companyIndex = 0;
        foreach ($companies as $company) {
            // Reconnect every 10 companies to prevent prepared statement errors
            if ($companyIndex % 10 == 0) {
                \DB::reconnect('pgsql');
            }
            $companyIndex++;

            // Check if this company already has enough saved talents
            $existingSavedCount = \DB::table('saved_talents')->where('company_id', $company->id)->count();

            if ($existingSavedCount >= 6) {
                // Skip this company if it already has enough saved talents
                continue;
            }

            // Ensure each company saves >5 talents (6-10 talents)
            $talentsToSave = rand(6, 10) - $existingSavedCount;

            if ($talentsToSave <= 0) {
                continue; // Already has enough
            }

            $savedTalentsData = [];
            $usedStudents = []; // Track to avoid duplicates per company
            $companyCategoryCounts = array_fill_keys($this->categories, 0);

            for ($i = 0; $i < $talentsToSave; $i++) {
                // Select random student who hasn't been saved by this company yet
                $availableStudents = $students->filter(function($s) use ($usedStudents) {
                    return !in_array($s->id, $usedStudents);
                })->values()->all(); // Convert to array

                if (empty($availableStudents)) {
                    break; // No more available students
                }

                $student = $availableStudents[array_rand($availableStudents)];
                $usedStudents[] = $student->id;

                // Determine category - ensure all categories are covered
                $category = $this->determineCategory($i, $talentsToSave, $companyCategoryCounts);
                $companyCategoryCounts[$category]++;

                // Generate saved talent data
                $savedTalent = $this->generateSavedTalent($company->id, $student->id, $category);
                $savedTalentsData[] = $savedTalent;

                $categoryDistribution[$category]++;
                $totalSavedTalents++;
            }

            // Insert saved talents for this company
            if (!empty($savedTalentsData)) {
                try {
                    \DB::table('saved_talents')->insert($savedTalentsData);
                } catch (\Exception $e) {
                    $this->command->error("❌ Error inserting saved talents for company {$company->name}: " . $e->getMessage());
                }
            }

            if ($totalSavedTalents % 50 == 0) {
                $this->command->info("   ... saved $totalSavedTalents talents");
            }
        }

        // Display results
        $this->command->info('');
        $this->command->info('✅ Saved talents seeding completed!');
        $this->command->info("📊 Total saved talents: $totalSavedTalents");
        $this->command->newLine();
        $this->command->info('📈 Category distribution:');
        $this->command->table(
            ['Category', 'Count', 'Percentage'],
            [
                ['Baru', $categoryDistribution['Baru'], round($categoryDistribution['Baru'] / max($totalSavedTalents, 1) * 100, 1) . '%'],
                ['Dihubungi', $categoryDistribution['Dihubungi'], round($categoryDistribution['Dihubungi'] / max($totalSavedTalents, 1) * 100, 1) . '%'],
                ['Interview', $categoryDistribution['Interview'], round($categoryDistribution['Interview'] / max($totalSavedTalents, 1) * 100, 1) . '%'],
                ['Ditawari', $categoryDistribution['Ditawari'], round($categoryDistribution['Ditawari'] / max($totalSavedTalents, 1) * 100, 1) . '%'],
                ['Ditolak', $categoryDistribution['Ditolak'], round($categoryDistribution['Ditolak'] / max($totalSavedTalents, 1) * 100, 1) . '%'],
            ]
        );

        // Verify all categories have data
        $allCategoriesUsed = true;
        foreach ($categoryDistribution as $category => $count) {
            if ($count == 0) {
                $this->command->error("⚠️  WARNING: Category '$category' has no saved talents!");
                $allCategoriesUsed = false;
            }
        }

        if ($allCategoriesUsed) {
            $this->command->info('✅ All 5 categories are represented!');
        }
    }

    /**
     * Determine category ensuring all categories are used
     */
    private function determineCategory($index, $total, $categoryCounts): string
    {
        // For first 5 saves, ensure each category is used at least once
        if ($index < 5) {
            return $this->categories[$index];
        }

        // After that, distribute with realistic weights
        $weights = [
            'Baru' => 30,        // 30% - Most common (fresh saves)
            'Dihubungi' => 25,   // 25% - Contacted
            'Interview' => 20,   // 20% - Interview scheduled/done
            'Ditawari' => 15,    // 15% - Made an offer
            'Ditolak' => 10,     // 10% - Rejected (least common)
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $category => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $category;
            }
        }

        return 'Baru';
    }

    /**
     * Generate saved talent data
     */
    private function generateSavedTalent($companyId, $userId, $category): array
    {
        // Generate realistic notes based on category
        $notes = $this->generateNotes($category);

        // Saved at different times (last 60 days)
        $savedAt = Carbon::now()->subDays(rand(1, 60));

        return [
            'company_id' => $companyId,
            'user_id' => $userId,
            'category' => $category,
            'notes' => $notes,
            'saved_at' => $savedAt->toDateTimeString(),
            'created_at' => $savedAt->toDateTimeString(),
            'updated_at' => $savedAt->toDateTimeString(),
        ];
    }

    /**
     * Generate realistic notes based on category
     */
    private function generateNotes($category): string
    {
        $notesByCategory = [
            'Baru' => [
                'Profil menarik, perlu review lebih lanjut.',
                'Kandidat potensial untuk posisi internship.',
                'Background pendidikan sesuai dengan kebutuhan kami.',
                'Portfolio menunjukkan skill yang relevan.',
                'Saved untuk review team.',
            ],
            'Dihubungi' => [
                'Sudah dihubungi via email untuk screening awal.',
                'WhatsApp sent, menunggu response.',
                'Email dan LinkedIn message sent.',
                'Follow up scheduled untuk minggu depan.',
                'Kandidat merespon positif, tertarik untuk lanjut.',
            ],
            'Interview' => [
                'Interview scheduled untuk tanggal 15.',
                'Interview selesai, kandidat sangat impressive.',
                'Technical test passed, siap interview HR.',
                'Interview round 1 completed, proceed to round 2.',
                'Kandidat menunjukkan antusiasme tinggi saat interview.',
            ],
            'Ditawari' => [
                'Offer letter sent, menunggu konfirmasi.',
                'Sudah ditawari posisi internship, negosiasi salary.',
                'Offer accepted, onboarding scheduled.',
                'Kandidat request waktu untuk pertimbangan.',
                'Offer extended dengan benefit package.',
            ],
            'Ditolak' => [
                'Kandidat tidak sesuai dengan requirement teknis.',
                'Memilih kandidat lain yang lebih experienced.',
                'Kandidat menolak offer kami.',
                'Tidak lanjut karena availability tidak match.',
                'Skill set belum sesuai dengan kebutuhan posisi.',
            ],
        ];

        $categoryNotes = $notesByCategory[$category] ?? ['No notes.'];
        return $categoryNotes[array_rand($categoryNotes)];
    }
}
