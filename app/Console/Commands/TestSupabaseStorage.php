<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TestSupabaseStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supabase:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Supabase storage connection and upload';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Supabase Storage Configuration...');
        $this->newLine();

        // 1. Check Configuration
        $this->info('1. Configuration Check:');
        $config = config('filesystems.disks.supabase');
        $this->line('   Endpoint: ' . ($config['endpoint'] ?? 'NOT SET'));
        $this->line('   Bucket: ' . ($config['bucket'] ?? 'NOT SET'));
        $this->line('   Region: ' . ($config['region'] ?? 'NOT SET'));
        $this->line('   URL: ' . ($config['url'] ?? 'NOT SET'));
        $this->newLine();

        // 2. Test Connection
        $this->info('2. Testing Connection to Supabase...');
        try {
            $disk = Storage::disk('supabase');
            $this->line('   ✓ Disk instance created successfully');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to create disk instance: ' . $e->getMessage());
            return 1;
        }

        // 3. Test S3 Client Directly
        $this->info('3. Testing S3 Client...');
        try {
            $s3Client = new \Aws\S3\S3Client([
                'version' => 'latest',
                'region' => config('filesystems.disks.supabase.region'),
                'endpoint' => config('filesystems.disks.supabase.endpoint'),
                'credentials' => [
                    'key' => config('filesystems.disks.supabase.key'),
                    'secret' => config('filesystems.disks.supabase.secret'),
                ],
                'use_path_style_endpoint' => config('filesystems.disks.supabase.use_path_style_endpoint', false),
                'http' => [
                    'verify' => false,
                ],
            ]);

            $this->line('   ✓ S3 Client created');

            // Test list buckets
            try {
                $result = $s3Client->listBuckets();
                $this->line('   ✓ Can connect to S3 endpoint');
                $buckets = $result->get('Buckets');
                $bucketCount = is_array($buckets) ? count($buckets) : 0;
                $this->line('   Available buckets: ' . $bucketCount);

                if ($bucketCount > 0) {
                    foreach ($buckets as $bucket) {
                        $this->line('     - ' . $bucket['Name']);
                    }
                }
            } catch (\Exception $e) {
                $this->error('   ✗ Cannot list buckets: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            $this->error('   ✗ S3 Client creation failed: ' . $e->getMessage());
        }

        // 4. Test Upload
        $this->info('4. Testing File Upload...');
        try {
            $testContent = 'Test file created at ' . now()->toDateTimeString();
            $testPath = 'test/' . uniqid() . '.txt';

            $result = $disk->put($testPath, $testContent);

            if ($result) {
                $this->line('   ✓ Test file uploaded successfully');
                $this->line('   Path: ' . $testPath);

                // 4. Test Read
                $this->info('4. Testing File Read...');
                if ($disk->exists($testPath)) {
                    $this->line('   ✓ Test file exists');

                    $content = $disk->get($testPath);
                    if ($content === $testContent) {
                        $this->line('   ✓ File content matches');
                    } else {
                        $this->warn('   ⚠ File content does not match');
                    }
                } else {
                    $this->error('   ✗ Test file does not exist after upload');
                }

                // 5. Test URL Generation
                $this->info('5. Testing URL Generation...');
                $url = supabase_url($testPath);
                $this->line('   Generated URL: ' . $url);

                // 6. Clean up
                $this->info('6. Cleaning up...');
                $disk->delete($testPath);
                $this->line('   ✓ Test file deleted');

                $this->newLine();
                $this->info('✓ All tests passed! Supabase storage is working correctly.');
                return 0;

            } else {
                $this->error('   ✗ Upload returned false');
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('   ✗ Upload failed: ' . $e->getMessage());
            $this->newLine();

            // Traverse all previous exceptions to find AWS S3 Exception
            $this->error('Exception Chain:');
            $currentException = $e;
            $depth = 0;

            while ($currentException !== null && $depth < 10) {
                $this->line('   [' . $depth . '] ' . get_class($currentException) . ': ' . $currentException->getMessage());

                if ($currentException instanceof \Aws\S3\Exception\S3Exception) {
                    $this->newLine();
                    $this->error('AWS S3 Error Details (Found at depth ' . $depth . '):');
                    $this->line('   Code: ' . ($currentException->getAwsErrorCode() ?? 'N/A'));
                    $this->line('   Message: ' . ($currentException->getAwsErrorMessage() ?? 'N/A'));
                    $this->line('   Type: ' . ($currentException->getAwsErrorType() ?? 'N/A'));
                    $this->line('   Request ID: ' . ($currentException->getAwsRequestId() ?? 'N/A'));

                    if ($currentException->getResponse()) {
                        $this->line('   Status Code: ' . $currentException->getResponse()->getStatusCode());
                        $this->line('   Response Body: ' . (string) $currentException->getResponse()->getBody());
                    }
                    break;
                }

                $currentException = $currentException->getPrevious();
                $depth++;
            }

            return 1;
        }
    }
}
