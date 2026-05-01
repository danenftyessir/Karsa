<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\BpsApiService;

/**
 * seeder untuk data provinces dan regencies dari BPS API
 * menggunakan BpsApiService untuk modularitas
 * 
 * jalankan: php artisan db:seed --class=ProvincesRegenciesSeeder
 */
class ProvincesRegenciesSeeder extends Seeder
{
    /**
     * BPS API service
     */
    private $bpsService;

    /**
     * constructor
     */
    public function __construct(BpsApiService $bpsService)
    {
        $this->bpsService = $bpsService;
    }

    /**
     * seed data provinces dan regencies indonesia
     */
    public function run(): void
    {
        $this->command->info('🌏 Mengambil data provinsi dan kabupaten dari BPS API...');
        $this->command->newLine();

        try {
            // hapus data lama jika ada
            $this->cleanOldData();

            // test koneksi terlebih dahulu
            if (!$this->testBpsConnection()) {
                throw new \Exception('Koneksi ke BPS API gagal');
            }

            // ambil dan seed provinces
            $this->seedProvinces();

            // ambil dan seed regencies
            $this->seedRegencies();

            // tampilkan summary
            $this->showSummary();

        } catch (\Exception $e) {
            $this->command->error('❌ Error: ' . $e->getMessage());
            Log::error('BPS Seeder Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // fallback ke data minimal jika API gagal
            $this->command->warn('⚠️  Menggunakan data fallback minimal...');
            $this->seedFallbackData();
            $this->showSummary();
        }
    }

    /**
     * bersihkan data lama
     */
    private function cleanOldData(): void
    {
        $this->command->info('🗑️  Membersihkan data lama...');
        DB::table('regencies')->delete();
        DB::table('provinces')->delete();
        $this->command->info('   ✓ Data lama dihapus');
        $this->command->newLine();
    }

    /**
     * test koneksi ke BPS API
     */
    private function testBpsConnection(): bool
    {
        $this->command->info('🔌 Mengecek koneksi ke BPS API...');
        
        $result = $this->bpsService->testConnection();

        if ($result['success']) {
            $this->command->info('   ✓ Koneksi berhasil');
            $this->command->line("   Response time: {$result['response_time']}");
            return true;
        } else {
            $this->command->error('   ✗ Koneksi gagal: ' . ($result['error'] ?? 'Unknown error'));
            return false;
        }

        $this->command->newLine();
    }

    /**
     * seed provinces dari BPS API
     */
    private function seedProvinces(): void
    {
        $this->command->info('📍 Mengambil data provinsi...');

        $provinces = $this->bpsService->getProvinces();

        if (empty($provinces)) {
            throw new \Exception('Tidak ada data provinsi dari BPS API');
        }

        // insert ke database dengan batch
        foreach (array_chunk($provinces, 50) as $chunk) {
            DB::table('provinces')->insert($chunk);
        }
        
        $count = count($provinces);
        $this->command->info("   ✓ {$count} provinsi berhasil disimpan");
        $this->command->newLine();
    }

    /**
     * seed regencies dari BPS API untuk setiap provinsi
     */
    private function seedRegencies(): void
    {
        $this->command->info('🏙️  Mengambil data kabupaten/kota...');

        $provinces = DB::table('provinces')->get();
        $totalRegencies = 0;
        $successCount = 0;
        $failCount = 0;

        $progressBar = $this->command->getOutput()->createProgressBar(count($provinces));
        $progressBar->start();

        foreach ($provinces as $province) {
            try {
                $regencies = $this->bpsService->getRegencies($province->code);

                if (!empty($regencies)) {
                    // insert dengan batch
                    foreach (array_chunk($regencies, 100) as $chunk) {
                        DB::table('regencies')->insert($chunk);
                    }
                    
                    $totalRegencies += count($regencies);
                    $successCount++;
                }

                $progressBar->advance();

                // delay kecil untuk menghindari rate limit
                usleep(100000); // 0.1 detik

            } catch (\Exception $e) {
                $failCount++;
                Log::warning("Failed to fetch regencies for province {$province->code}", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        $progressBar->finish();
        $this->command->newLine();
        
        $this->command->info("   ✓ {$totalRegencies} kabupaten/kota berhasil disimpan");
        $this->command->info("   ✓ {$successCount} provinsi berhasil diproses");
        
        if ($failCount > 0) {
            $this->command->warn("   ⚠️  {$failCount} provinsi gagal diproses");
        }
        
        $this->command->newLine();
    }

    /**
     * tampilkan summary hasil seeding
     */
    private function showSummary(): void
    {
        $provinceCount = DB::table('provinces')->count();
        $regencyCount = DB::table('regencies')->count();

        $this->command->newLine();
        $this->command->info('==============================================');
        $this->command->info('  SUMMARY');
        $this->command->info('==============================================');
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Provinsi', $provinceCount],
                ['Kabupaten/Kota', $regencyCount],
            ]
        );
        $this->command->info('==============================================');
        $this->command->newLine();
    }

    /**
     * fallback data jika BPS API gagal
     * minimal data untuk testing
     */
    private function seedFallbackData(): void
    {
        $this->command->info('📦 Menggunakan data fallback...');

        // data provinces minimal (5 provinsi populer)
        $provinces = [
            ['id' => 31, 'code' => '31', 'name' => 'DKI Jakarta'],
            ['id' => 32, 'code' => '32', 'name' => 'Jawa Barat'],
            ['id' => 33, 'code' => '33', 'name' => 'Jawa Tengah'],
            ['id' => 34, 'code' => '34', 'name' => 'DI Yogyakarta'],
            ['id' => 35, 'code' => '35', 'name' => 'Jawa Timur'],
        ];

        DB::table('provinces')->insert($provinces);

        // data regencies minimal untuk 5 provinsi
        $regencies = [
            // DKI Jakarta
            ['id' => 3171, 'province_id' => 31, 'code' => '3171', 'name' => 'Kota Jakarta Selatan'],
            ['id' => 3172, 'province_id' => 31, 'code' => '3172', 'name' => 'Kota Jakarta Timur'],
            ['id' => 3173, 'province_id' => 31, 'code' => '3173', 'name' => 'Kota Jakarta Pusat'],
            ['id' => 3174, 'province_id' => 31, 'code' => '3174', 'name' => 'Kota Jakarta Barat'],
            ['id' => 3175, 'province_id' => 31, 'code' => '3175', 'name' => 'Kota Jakarta Utara'],
            
            // Jawa Barat (sample)
            ['id' => 3201, 'province_id' => 32, 'code' => '3201', 'name' => 'Kab. Bogor'],
            ['id' => 3204, 'province_id' => 32, 'code' => '3204', 'name' => 'Kab. Bandung'],
            ['id' => 3273, 'province_id' => 32, 'code' => '3273', 'name' => 'Kota Bandung'],
            ['id' => 3275, 'province_id' => 32, 'code' => '3275', 'name' => 'Kota Bekasi'],
            ['id' => 3276, 'province_id' => 32, 'code' => '3276', 'name' => 'Kota Depok'],
            
            // Jawa Tengah (sample)
            ['id' => 3301, 'province_id' => 33, 'code' => '3301', 'name' => 'Kab. Cilacap'],
            ['id' => 3310, 'province_id' => 33, 'code' => '3310', 'name' => 'Kab. Klaten'],
            ['id' => 3374, 'province_id' => 33, 'code' => '3374', 'name' => 'Kota Semarang'],
            ['id' => 3372, 'province_id' => 33, 'code' => '3372', 'name' => 'Kota Surakarta'],
            
            // DI Yogyakarta
            ['id' => 3401, 'province_id' => 34, 'code' => '3401', 'name' => 'Kab. Kulon Progo'],
            ['id' => 3402, 'province_id' => 34, 'code' => '3402', 'name' => 'Kab. Bantul'],
            ['id' => 3403, 'province_id' => 34, 'code' => '3403', 'name' => 'Kab. Gunung Kidul'],
            ['id' => 3404, 'province_id' => 34, 'code' => '3404', 'name' => 'Kab. Sleman'],
            ['id' => 3471, 'province_id' => 34, 'code' => '3471', 'name' => 'Kota Yogyakarta'],
            
            // Jawa Timur (sample)
            ['id' => 3501, 'province_id' => 35, 'code' => '3501', 'name' => 'Kab. Pacitan'],
            ['id' => 3578, 'province_id' => 35, 'code' => '3578', 'name' => 'Kota Surabaya'],
            ['id' => 3579, 'province_id' => 35, 'code' => '3579', 'name' => 'Kota Malang'],
        ];

        DB::table('regencies')->insert($regencies);

        $this->command->info('   ✓ Fallback data berhasil disimpan');
    }
}