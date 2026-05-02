<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Command untuk test akses publik ke Supabase Storage
 * Menggunakan alternatif method untuk hindari cURL certificate error
 *
 * Usage: php artisan supabase:test-public-url
 */
class TestSupabasePublicUrl extends Command
{
    protected $signature = 'supabase:test-public-url {--fix : Set bucket menjadi public}';

    protected $description = 'Test akses publik ke Supabase Storage bucket';

    public function handle(): int
    {
        $this->info('🧪 Testing Supabase Storage Public Access...');
        $this->newLine();

        $projectId = config('services.supabase.project_id');
        $serviceKey = config('services.supabase.service_key');
        $bucketName = config('services.supabase.bucket', 'karsa-storage');

        $testPath = 'problems/masalah-desa-1.jpg';
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $testPath)));

        $publicUrl = "https://{$projectId}.supabase.co/storage/v1/object/public/{$bucketName}/{$encodedPath}";

        $this->info("📋 Test Configuration:");
        $this->line("   Project ID: {$projectId}");
        $this->line("   Bucket Name: {$bucketName}");
        $this->line("   Test File: {$testPath}");
        $this->newLine();

        $this->info("🔗 Public URL: {$publicUrl}");
        $this->newLine();

        // Check bucket status
        $this->info('🔍 Checking bucket status...');

        $bucketStatusUrl = "https://{$projectId}.supabase.co/storage/v1/bucket/{$bucketName}";
        $bucketResponse = $this->httpGet($bucketStatusUrl, $serviceKey);

        if ($bucketResponse === false) {
            $this->error("❌ Gagal mengambil status bucket");
            return 1;
        }

        $bucketData = json_decode($bucketResponse, true);

        if (isset($bucketData['public'])) {
            $this->info("   Bucket Public: " . ($bucketData['public'] ? '✅ Ya' : '❌ Tidak'));

            if (!$bucketData['public']) {
                $this->newLine();
                $this->error("❌ BUCKET TIDAK PUBLIC! Ini penyebab gambar tidak bisa diakses.");
                $this->newLine();

                if ($this->option('fix')) {
                    return $this->makeBucketPublic($projectId, $serviceKey, $bucketName);
                }

                $this->info("💡 SOLUSI:");
                $this->line("   1. Buka Supabase Dashboard: https://supabase.com/dashboard");
                $this->line("   2. Pilih project");
                $this->line("   3. Menu: Storage → {$bucketName} → Settings/Configuration");
                $this->line("   4. Aktifkan 'Public bucket'");
                $this->newLine();
                $this->line("   ATAU jalankan: php artisan supabase:test-public-url --fix");
                return 1;
            }
        }

        $this->newLine();
        $this->info('🌐 Testing file access...');

        // Test file access
        $fileResponse = $this->httpHead($publicUrl);

        $this->newLine();

        if ($fileResponse['status'] === 200) {
            $this->info("✅ SUCCESS! File accessible!");
            $this->info("   Status: 200 OK");
            $this->info("   Content-Type: " . ($fileResponse['headers']['content-type'] ?? 'N/A'));
            $this->newLine();
            $this->info("✨ Gambar akan tampil di browse-problems!");
            return 0;
        }

        if ($fileResponse['status'] === 404) {
            $this->error("❌ 404 Not Found - File tidak ada di bucket");
            $this->line("   Pastikan file sudah diupload ke Supabase Storage");
            return 1;
        }

        if ($fileResponse['status'] === 400) {
            $this->error("❌ 400 Bad Request");
            $this->line("   Bucket tidak ditemukan atau URL tidak valid");
            return 1;
        }

        $this->error("❌ Status: " . $fileResponse['status']);
        return 1;
    }

    /**
     * Set bucket menjadi public
     */
    protected function makeBucketPublic(string $projectId, string $serviceKey, string $bucketName): int
    {
        $this->info("🔧 Mengubah bucket menjadi public...");
        $this->newLine();

        $url = "https://{$projectId}.supabase.co/storage/v1/bucket/{$bucketName}";

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode(['public' => true]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $serviceKey,
                'Content-Type: application/json',
                'apikey: ' . $serviceKey,
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->error("❌ cURL Error: {$error}");
            return 1;
        }

        $this->line("   HTTP Status: {$httpCode}");
        $this->line("   Response: {$response}");

        if ($httpCode === 200 || $httpCode === 201) {
            $this->newLine();
            $this->info("✅ SUCCESS! Bucket sekarang PUBLIC!");
            $this->info("✨ Gambar akan tampil di browse-problems!");
            return 0;
        }

        $this->error("❌ Gagal mengubah bucket: {$response}");
        return 1;
    }

    /**
     * HTTP GET request dengan SSL bypass
     */
    protected function httpGet(string $url, ?string $bearerToken = null): string|false
    {
        $ch = curl_init();
        $headers = ['Accept: application/json'];
        if ($bearerToken) {
            $headers[] = 'Authorization: Bearer ' . $bearerToken;
            $headers[] = 'apikey: ' . $bearerToken;
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        return $error ? false : $response;
    }

    /**
     * HTTP HEAD request dengan SSL bypass
     */
    protected function httpHead(string $url): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['status' => 0, 'error' => $error, 'headers' => []];
        }

        $headers = [];
        if ($headerSize > 0 && $response) {
            $headerLines = explode("\r\n", substr($response, 0, $headerSize));
            foreach ($headerLines as $line) {
                if (strpos($line, ':') !== false) {
                    [$key, $value] = explode(':', $line, 2);
                    $headers[strtolower(trim($key))] = trim($value);
                }
            }
        }

        return ['status' => $httpCode, 'headers' => $headers];
    }
}