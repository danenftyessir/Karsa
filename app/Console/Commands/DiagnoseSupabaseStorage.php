<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseStorageService;
use App\Models\Student;
use App\Models\Institution;
use Illuminate\Support\Facades\Http;

/**
 * Command untuk diagnose Supabase Storage connectivity dan configuration
 *
 * Usage: php artisan supabase:diagnose
 */
class DiagnoseSupabaseStorage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'supabase:diagnose
                            {--check-files : Check if profile photos exist in Supabase}
                            {--limit=10 : Number of records to check}';

    /**
     * The console command description.
     */
    protected $description = 'Diagnose Supabase Storage connectivity and configuration';

    protected $storageService;

    public function __construct(SupabaseStorageService $storageService)
    {
        parent::__construct();
        $this->storageService = $storageService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Diagnosing Supabase Storage Configuration...');
        $this->newLine();

        // 1. Check Environment Variables
        $this->checkEnvironment();
        $this->newLine();

        // 2. Check Supabase Connectivity
        $this->checkConnectivity();
        $this->newLine();

        // 3. Check Bucket Access
        $this->checkBucketAccess();
        $this->newLine();

        // 4. Optionally check if files exist
        if ($this->option('check-files')) {
            $this->checkProfilePhotos();
            $this->newLine();
        }

        $this->info('✅ Diagnosis complete!');

        return 0;
    }

    /**
     * Check environment variables
     */
    protected function checkEnvironment()
    {
        $this->info('📋 Checking Environment Variables:');

        $requiredVars = [
            'SUPABASE_PROJECT_ID' => config('services.supabase.project_id'),
            'SUPABASE_URL' => config('services.supabase.url'),
            'SUPABASE_SERVICE_KEY' => config('services.supabase.service_key'),
            'SUPABASE_BUCKET' => config('services.supabase.bucket'),
        ];

        $allConfigured = true;

        foreach ($requiredVars as $name => $value) {
            if (empty($value)) {
                $this->error("  ❌ {$name}: NOT SET");
                $allConfigured = false;
            } else {
                $display = $name === 'SUPABASE_SERVICE_KEY'
                    ? substr($value, 0, 20) . '...'
                    : $value;
                $this->info("  ✅ {$name}: {$display}");
            }
        }

        if (!$allConfigured) {
            $this->error('  ⚠️  Some environment variables are missing!');
            $this->warn('  💡 Please check your .env file and ensure all SUPABASE_* variables are set.');
        }
    }

    /**
     * Check Supabase connectivity
     */
    protected function checkConnectivity()
    {
        $this->info('🌐 Checking Supabase Connectivity:');

        $projectId = config('services.supabase.project_id');
        $serviceKey = config('services.supabase.service_key');

        if (empty($projectId) || empty($serviceKey)) {
            $this->error('  ❌ Cannot check connectivity - missing credentials');
            return;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $serviceKey,
                ])
                ->get("https://{$projectId}.supabase.co/storage/v1/bucket");

            if ($response->successful()) {
                $buckets = $response->json();
                $this->info('  ✅ Connected to Supabase successfully!');
                $this->info('  📦 Found ' . count($buckets) . ' bucket(s)');

                foreach ($buckets as $bucket) {
                    $this->line("     - {$bucket['name']} (Public: " . ($bucket['public'] ? 'Yes' : 'No') . ")");
                }
            } else {
                $this->error('  ❌ Failed to connect to Supabase');
                $this->error('     Status: ' . $response->status());
                $this->error('     Body: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('  ❌ Connection error: ' . $e->getMessage());
        }
    }

    /**
     * Check bucket access
     */
    protected function checkBucketAccess()
    {
        $this->info('🪣 Checking Bucket Access:');

        $projectId = config('services.supabase.project_id');
        $serviceKey = config('services.supabase.service_key');
        $bucketName = config('services.supabase.bucket');

        if (empty($projectId) || empty($serviceKey) || empty($bucketName)) {
            $this->error('  ❌ Cannot check bucket - missing configuration');
            return;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $serviceKey,
                ])
                ->get("https://{$projectId}.supabase.co/storage/v1/bucket/{$bucketName}");

            if ($response->successful()) {
                $bucket = $response->json();
                $this->info("  ✅ Bucket '{$bucketName}' is accessible!");
                $this->info('     Public: ' . ($bucket['public'] ? 'Yes' : 'No'));

                if (!$bucket['public']) {
                    $this->warn('  ⚠️  WARNING: Bucket is NOT public!');
                    $this->warn('     Files in this bucket cannot be accessed via public URLs.');
                    $this->warn('     💡 Go to Supabase Dashboard → Storage → ' . $bucketName . ' → Settings');
                    $this->warn('        and enable "Public bucket" to fix 404 errors.');
                }
            } else {
                $this->error("  ❌ Bucket '{$bucketName}' not found or not accessible");
                $this->error('     Status: ' . $response->status());

                if ($response->status() === 404) {
                    $this->warn('  💡 The bucket name in your .env might be incorrect.');
                    $this->warn('     Check the bucket name in Supabase Dashboard → Storage');
                }
            }
        } catch (\Exception $e) {
            $this->error('  ❌ Error checking bucket: ' . $e->getMessage());
        }
    }

    /**
     * Check if profile photos exist in Supabase
     */
    protected function checkProfilePhotos()
    {
        $this->info('🖼️  Checking Profile Photos:');

        $limit = (int) $this->option('limit');

        // Check student photos
        $students = Student::whereNotNull('profile_photo_path')
            ->limit($limit)
            ->get();

        $this->info('  📸 Checking ' . $students->count() . ' student profile photos...');

        $found = 0;
        $notFound = 0;

        foreach ($students as $student) {
            $url = $student->profile_photo_url;

            // Try to fetch the URL
            try {
                $response = Http::timeout(5)->head($url);

                if ($response->successful()) {
                    $found++;
                } else {
                    $notFound++;
                    $this->warn("     ❌ Not found: {$student->profile_photo_path}");
                    $this->line("        URL: {$url}");
                }
            } catch (\Exception $e) {
                $notFound++;
                $this->warn("     ❌ Error checking: {$student->profile_photo_path}");
            }
        }

        $this->newLine();
        $this->info("  📊 Results:");
        $this->info("     ✅ Found: {$found}");
        $this->error("     ❌ Not Found: {$notFound}");

        if ($notFound > 0) {
            $this->newLine();
            $this->warn('  ⚠️  Some photos are not accessible!');
            $this->warn('     Possible causes:');
            $this->warn('     1. Bucket is not public (see warning above)');
            $this->warn('     2. Files were never uploaded to Supabase');
            $this->warn('     3. Files were deleted from Supabase');
            $this->warn('     4. Incorrect bucket name in .env');
        }
    }
}
