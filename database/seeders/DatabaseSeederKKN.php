<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DATABASE SEEDER untuk Karsa SYSTEM
 *
 * Master seeder yang menjalankan semua seeders dengan urutan yang benar
 * untuk sistem Lapangku dengan fokus KKN field-based jobs
 */
class DatabaseSeederKKN extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🚀 ============================================');
        $this->command->info('🚀   LAPANGKU Karsa DATABASE SEEDER');
        $this->command->info('🚀 ============================================');
        $this->command->info('');

        // ============================================
        // STEP 1: Job Categories
        // ============================================
        $this->command->info('📂 STEP 1/4: Seeding Job Categories...');
        $this->call(JobCategorySeeder::class);
        $this->command->info('');

        // ============================================
        // STEP 2: Users (Company, Students, Institutions)
        // ============================================
        $this->command->info('👥 STEP 2/4: Seeding Users...');
        $this->call(UserSeeder::class);
        $this->command->info('');

        // ============================================
        // STEP 3: Companies (Real Indonesian Companies)
        // ============================================
        $this->command->info('🏢 STEP 3/4: Seeding Real Indonesian Companies...');
        $this->call(CompanySeederRealIndonesia::class);
        $this->command->info('');

        // ============================================
        // STEP 4: Job Postings (KKN Field-Based Jobs)
        // ============================================
        $this->command->info('💼 STEP 4/4: Seeding KKN-Based Job Postings...');
        $this->call(JobPostingKKNSeeder::class);
        $this->command->info('');

        // ============================================
        // SUCCESS SUMMARY
        // ============================================
        $this->command->info('');
        $this->command->info('✅ ============================================');
        $this->command->info('✅   DATABASE SEEDING COMPLETED!');
        $this->command->info('✅ ============================================');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   ✓ Job Categories: 33 categories (SDG-aligned)');
        $this->command->info('   ✓ Users: 270 users');
        $this->command->info('      - 50 Company users');
        $this->command->info('      - 200 Student/Talent users');
        $this->command->info('      - 20 Institution users');
        $this->command->info('   ✓ Companies: 30 real Indonesian companies');
        $this->command->info('   ✓ Job Postings: 150-300 KKN field-based jobs');
        $this->command->info('');
        $this->command->info('🎯 Next Steps:');
        $this->command->info('   1. Run: php artisan db:seed --class=DatabaseSeederKKN');
        $this->command->info('   2. (Optional) Seed job applications and saved talents');
        $this->command->info('   3. Verify data in Supabase dashboard');
        $this->command->info('   4. Test the Lapangku platform with real data');
        $this->command->info('');
        $this->command->info('🌟 All data seeded to SUPABASE (not local database)');
        $this->command->info('');
    }
}
