<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Command untuk upload gambar problem images ke Supabase Storage
 * melalui Supabase REST API (bukan S3-compatible API)
 *
 * Usage:
 * php artisan supabase:upload-problem-images
 * php artisan supabase:upload-problem-images --check
 * php artisan supabase:upload-problem-images --rebuild-db
 *
 * Gambar yang diupload akan disimpan di bucket "karsa-storage" dengan path: problems/FILENAME
 */
class UploadProblemImagesToSupabase extends Command
{
    protected $signature = 'supabase:upload-problem-images
                            {--check : Hanya cek status gambar di storage, tidak upload}
                            {--rebuild-db : Rebuild database image records berdasarkan file yang ada}';

    protected $description = 'Upload problem images local ke Supabase Storage';

    protected $projectId;
    protected $serviceKey;
    protected $bucketName;
    protected $baseUrl;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        // Load config
        $this->projectId = config('services.supabase.project_id');
        $this->serviceKey = config('services.supabase.service_key');
        $this->bucketName = config('services.supabase.bucket', 'karsa-storage');
        $this->baseUrl = "https://{$this->projectId}.supabase.co/storage/v1";

        // Validasi konfigurasi
        if (empty($this->projectId) || empty($this->serviceKey)) {
            $this->error('❌ Konfigurasi Supabase tidak lengkap!');
            $this->line('   Pastikan SUPABASE_PROJECT_ID dan SUPABASE_SERVICE_KEY sudah di-set di .env');
            return 1;
        }

        // Cek apakah bucket accessible
        if (!$this->checkBucketAccess()) {
            $this->error('❌ Bucket tidak accessible. Pastikan bucket "karsa-storage" sudah dibuat di Supabase Dashboard.');
            return 1;
        }

        if ($this->option('check')) {
            return $this->checkImagesInStorage();
        }

        if ($this->option('rebuild-db')) {
            return $this->rebuildDatabaseRecords();
        }

        return $this->uploadImages();
    }

    /**
     * Cek apakah bucket accessible
     */
    protected function checkBucketAccess(): bool
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->serviceKey,
                ])
                ->get("{$this->baseUrl}/bucket/{$this->bucketName}");

            return $response->successful();
        } catch (\Exception $e) {
            $this->error('Error checking bucket: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload gambar-gambar ke Supabase Storage
     */
    protected function uploadImages(): int
    {
        $localPath = storage_path('app/public/problems');
        $supabaseFolder = 'problems';

        if (!File::exists($localPath)) {
            $this->warn("⚠️  Folder {$localPath} tidak ditemukan!");
            $this->line('   💡 Buat folder storage/app/public/problems dan masukkan gambar di dalamnya');
            return 1;
        }

        $files = File::files($localPath);
        $imageFiles = array_filter($files, function($file) {
            $ext = strtolower($file->getExtension());
            return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
        });

        if (empty($imageFiles)) {
            $this->warn("⚠️  Tidak ada file gambar di folder {$localPath}");
            $this->line('   💡 Masukkan file gambar (jpg, png, webp, gif) ke folder tersebut');
            return 1;
        }

        $this->info("🖼️  Mulai upload " . count($imageFiles) . " gambar ke Supabase Storage...");
        $this->newLine();

        $bar = $this->output->createProgressBar(count($imageFiles));
        $bar->start();

        $uploaded = 0;
        $skipped = 0;
        $errors = 0;
        $failedFiles = [];

        foreach ($imageFiles as $file) {
            $fileName = $file->getFilename();
            $supabasePath = "{$supabaseFolder}/{$fileName}";

            // Encode path untuk URL
            $encodedPath = $this->encodePath($supabasePath);

            try {
                // Cek apakah file sudah ada di Supabase
                $existsResponse = Http::timeout(10)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->serviceKey,
                    ])
                    ->head("{$this->baseUrl}/object/{$this->bucketName}/{$encodedPath}");

                if ($existsResponse->successful()) {
                    // File sudah ada, skip
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Upload file ke Supabase
                $fileContent = File::get($file->getPathname());
                $mimeType = $this->getMimeType($file->getExtension());

                $response = Http::timeout(60)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->serviceKey,
                        'Content-Type' => $mimeType,
                        'x-upsert' => 'true',
                    ])
                    ->withBody($fileContent, $mimeType)
                    ->post("{$this->baseUrl}/object/{$this->bucketName}/{$encodedPath}");

                if ($response->successful()) {
                    $uploaded++;
                    Log::info("✅ Uploaded: {$fileName}");
                } else {
                    $errors++;
                    $failedFiles[] = $fileName;
                    Log::error("❌ Failed to upload {$fileName}: " . $response->body());
                }

            } catch (\Exception $e) {
                $errors++;
                $failedFiles[] = $fileName;
                Log::error("❌ Exception uploading {$fileName}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("📊 Hasil Upload:");
        $this->info("   ✅ Berhasil diupload: {$uploaded}");
        $this->info("   ⏭️  Sudah ada (di-skip): {$skipped}");

        if ($errors > 0) {
            $this->error("   ❌ Gagal: {$errors}");
            $this->line("   File yang gagal:");
            foreach ($failedFiles as $file) {
                $this->line("      - {$file}");
            }
        }

        if ($uploaded > 0) {
            $this->newLine();
            $this->info("✨ Gambar sekarang bisa dilihat di browse-problems!");
            $this->line("   URL pattern: https://{$this->projectId}.supabase.co/storage/v1/object/public/{$this->bucketName}/problems/FILENAME");
        }

        return $errors > 0 ? 1 : 0;
    }

    /**
     * Cek gambar yang ada di storage
     */
    protected function checkImagesInStorage(): int
    {
        $this->info("🔍 Cek gambar di Supabase Storage...");
        $this->newLine();

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->serviceKey,
                ])
                ->get("{$this->baseUrl}/object/list/{$this->bucketName}?prefix=problems/");

            if ($response->successful()) {
                $files = $response->json();
                $this->info("📦 Ditemukan " . count($files) . " file di folder problems/");
                $this->newLine();

                foreach ($files as $index => $file) {
                    $this->line("   " . ($index + 1) . ". " . $file['name']);
                }

                return 0;
            } else {
                $this->error("❌ Gagal mengambil daftar file: " . $response->status());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Rebuild database records berdasarkan file yang ada
     */
    protected function rebuildDatabaseRecords(): int
    {
        $this->info("🔄 Rebuild database records...");
        $this->newLine();

        try {
            // Ambil daftar file dari Supabase
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->serviceKey,
                ])
                ->get("{$this->baseUrl}/object/list/{$this->bucketName}?prefix=problems/");

            if (!$response->successful()) {
                $this->error("❌ Gagal mengambil daftar file dari Supabase");
                return 1;
            }

            $files = $response->json();
            $this->info("📦 Ditemukan " . count($files) . " file di Supabase Storage");

            if (count($files) === 0) {
                $this->warn("⚠️  Tidak ada file di Supabase Storage. Jalankan tanpa --rebuild-db untuk upload file terlebih dahulu.");
                return 1;
            }

            // Cek apakah ada problem_images di database
            $existingCount = \DB::connection('pgsql')
                ->table('problem_images')
                ->count();

            $this->info("📋 Records di database: {$existingCount}");

            // Generate list path untuk ditampilkan
            $filePaths = array_map(function($f) {
                return $f['name'];
            }, $files);

            $this->newLine();
            $this->info("📁 File yang tersedia:");
            foreach (array_slice($filePaths, 0, 20) as $path) {
                $this->line("   - {$path}");
            }

            if (count($filePaths) > 20) {
                $this->line("   ... dan " . (count($filePaths) - 20) . " file lainnya");
            }

            $this->newLine();
            $this->info("✅ Untuk assign gambar ke problems, jalankan:");
            $this->line("   php artisan db:seed --class=ProblemImagesSeeder");
            $this->line("");
            $this->line("   Atau jika ingin reset semua gambar:");
            $this->line("   php artisan db:seed --class=ProblemImagesSeeder --fresh");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Encode path untuk URL
     */
    protected function encodePath(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', $path)));
    }

    /**
     * Get MIME type dari extension
     */
    protected function getMimeType(string $extension): string
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}
