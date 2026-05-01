<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BpsApiService;

/**
 * command untuk testing BPS API connection
 * 
 * path: app/Console/Commands/TestBpsApiCommand.php
 * jalankan: php artisan bps:test
 */
class TestBpsApiCommand extends Command
{
    /**
     * signature command
     *
     * @var string
     */
    protected $signature = 'bps:test 
                            {--clear-cache : Clear BPS API cache}
                            {--province= : Test specific province by code}';

    /**
     * deskripsi command
     *
     * @var string
     */
    protected $description = 'Test koneksi dan data BPS API';

    /**
     * BPS API service
     */
    private $bpsService;

    /**
     * constructor
     */
    public function __construct(BpsApiService $bpsService)
    {
        parent::__construct();
        $this->bpsService = $bpsService;
    }

    /**
     * execute command
     */
    public function handle()
    {
        $this->info('==============================================');
        $this->info('  TESTING BPS API CONNECTION');
        $this->info('==============================================');
        $this->newLine();

        // clear cache jika diminta
        if ($this->option('clear-cache')) {
            $this->info('🗑️  Clearing BPS API cache...');
            $this->bpsService->clearCache();
            $this->info('✅ Cache cleared!');
            $this->newLine();
        }

        // test koneksi
        $this->testConnection();

        // test provinces
        $this->testProvinces();

        // test regencies jika diminta
        if ($provinceCode = $this->option('province')) {
            $this->testRegencies($provinceCode);
        } else {
            // test beberapa provinsi sample
            $this->testRegencies('31'); // DKI Jakarta
            $this->testRegencies('32'); // Jawa Barat
            $this->testRegencies('33'); // Jawa Tengah
        }

        $this->newLine();
        $this->info('==============================================');
        $this->info('  TESTING SELESAI');
        $this->info('==============================================');

        return 0;
    }

    /**
     * test koneksi ke BPS API
     */
    private function testConnection()
    {
        $this->info('🔌 Testing connection to BPS API...');
        
        $result = $this->bpsService->testConnection();

        if ($result['success']) {
            $this->info('✅ Connection successful!');
            $this->line("   Status Code: {$result['status_code']}");
            $this->line("   Response Time: {$result['response_time']}");
            $this->line("   Has Data: " . ($result['has_data'] ? 'Yes' : 'No'));
            $this->line("   Data Count: {$result['data_count']}");
        } else {
            $this->error('❌ Connection failed!');
            $this->error("   Error: {$result['error']}");
        }

        $this->newLine();
    }

    /**
     * test ambil data provinces
     */
    private function testProvinces()
    {
        $this->info('📍 Testing provinces data...');

        try {
            $provinces = $this->bpsService->getProvinces();
            
            $this->info("✅ Successfully retrieved {count($provinces)} provinces");
            
            // tampilkan sample 5 provinsi pertama
            $this->newLine();
            $this->line('Sample data (first 5 provinces):');
            
            $headers = ['ID', 'Code', 'Name'];
            $rows = [];
            
            foreach (array_slice($provinces, 0, 5) as $province) {
                $rows[] = [
                    $province['id'],
                    $province['code'],
                    $province['name']
                ];
            }
            
            $this->table($headers, $rows);

        } catch (\Exception $e) {
            $this->error('❌ Failed to retrieve provinces');
            $this->error("   Error: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * test ambil data regencies untuk provinsi tertentu
     */
    private function testRegencies(string $provinceCode)
    {
        $this->info("🏙️  Testing regencies for province code: {$provinceCode}...");

        try {
            $regencies = $this->bpsService->getRegencies($provinceCode);
            
            $this->info("✅ Successfully retrieved " . count($regencies) . " regencies");
            
            // tampilkan sample 5 kabupaten pertama
            if (count($regencies) > 0) {
                $this->line('Sample data (first 5 regencies):');
                
                $headers = ['ID', 'Province ID', 'Code', 'Name'];
                $rows = [];
                
                foreach (array_slice($regencies, 0, 5) as $regency) {
                    $rows[] = [
                        $regency['id'],
                        $regency['province_id'],
                        $regency['code'],
                        $regency['name']
                    ];
                }
                
                $this->table($headers, $rows);
            } else {
                $this->warn('⚠️  No regencies data received for province ' . $provinceCode);
            }

        } catch (\Exception $e) {
            $this->error("❌ Failed to retrieve regencies for province {$provinceCode}");
            $this->error("   Error: {$e->getMessage()}");
        }

        $this->newLine();
    }
}