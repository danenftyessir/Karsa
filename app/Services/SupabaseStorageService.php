<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * service untuk handle file operations dengan Supabase Storage
 * menggunakan Supabase REST API dengan fallback ke local storage
 * 
 * dokumentasi: https://supabase.com/docs/reference/javascript/storage-from-upload
 */
class SupabaseStorageService
{
    protected $projectId;
    protected $serviceKey;
    protected $bucketName;
    protected $baseUrl;
    protected $useSupabase;

    public function __construct()
    {
        // Use new config location
        $this->projectId = config('services.supabase.project_id');
        $this->serviceKey = config('services.supabase.service_key');
        $this->bucketName = config('services.supabase.bucket', 'kkngo-storage');
        $this->baseUrl = "https://{$this->projectId}.supabase.co/storage/v1";

        // cek apakah supabase dikonfigurasi dengan benar
        $this->useSupabase = !empty($this->projectId) && !empty($this->serviceKey);

        // PRODUCTION: Supabase MUST be configured
        if (config('app.env') === 'production' && !$this->useSupabase) {
            throw new \Exception('Supabase configuration is REQUIRED in production! Check SUPABASE_PROJECT_ID and SUPABASE_SERVICE_KEY in .env');
        }

        // LOCAL: Allow fallback to local storage with warning
        if (config('app.env') === 'local' && !$this->useSupabase) {
            Log::warning("⚠️ Supabase config tidak lengkap - akan menggunakan local storage!", [
                'project_id' => $this->projectId ? '✅' : '❌',
                'service_key' => $this->serviceKey ? '✅' : '❌',
                'bucket' => $this->bucketName,
            ]);
        }
    }

    /**
     * upload file ke supabase storage dengan fallback ke local
     * 
     * @param UploadedFile $file file yang akan diupload
     * @param string $path path tujuan di bucket (contoh: problems/file.jpg)
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadFile(UploadedFile $file, string $path)
    {
        // jika supabase tidak dikonfigurasi, gunakan local storage
        if (!$this->useSupabase) {
            return $this->uploadToLocal($file, $path);
        }

        try {
            // baca file content
            $fileContent = file_get_contents($file->getRealPath());
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            Log::info("📤 Uploading to Supabase", [
                'path' => $path,
                'mime' => $mimeType,
                'size' => number_format($fileSize) . ' bytes',
                'bucket' => $this->bucketName,
            ]);

            // encode path untuk URL
            $encodedPath = $this->encodePath($path);

            // upload ke supabase menggunakan POST method
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->serviceKey,
                    'Content-Type' => $mimeType,
                    'x-upsert' => 'true', // allow overwrite jika file sudah ada
                ])
                ->withBody($fileContent, $mimeType)
                ->post("{$this->baseUrl}/object/{$this->bucketName}/{$encodedPath}");

            // check response
            if ($response->successful()) {
                $publicUrl = $this->getPublicUrl($path);
                
                Log::info("✅ Upload SUCCESS to Supabase", [
                    'path' => $path,
                    'status' => $response->status(),
                    'public_url' => $publicUrl,
                ]);
                
                return $path; // return path yang akan disimpan di database
            }

            // jika gagal, log error dan coba local storage
            Log::error("❌ Upload FAILED to Supabase", [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            
            // fallback ke local storage
            Log::info("⚠️ Falling back to local storage");
            return $this->uploadToLocal($file, $path);

        } catch (\Exception $e) {
            Log::error("❌ Upload EXCEPTION to Supabase", [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            
            // fallback ke local storage
            Log::info("⚠️ Falling back to local storage");
            return $this->uploadToLocal($file, $path);
        }
    }

    /**
     * upload file ke local storage sebagai fallback
     * 
     * @param UploadedFile $file file yang akan diupload
     * @param string $path path tujuan
     * @return string|false path file atau false jika gagal
     */
    protected function uploadToLocal(UploadedFile $file, string $path)
    {
        try {
            Log::info("📁 Uploading to local storage", ['path' => $path]);
            
            // simpan ke storage/app/public/
            $storedPath = $file->storeAs(
                dirname($path), 
                basename($path), 
                'public'
            );
            
            if ($storedPath) {
                Log::info("✅ Upload SUCCESS to local storage", [
                    'path' => $storedPath,
                    'url' => Storage::disk('public')->url($storedPath)
                ]);
                return $storedPath;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error("❌ Upload FAILED to local storage", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * encode path untuk URL (handle spasi dan karakter khusus)
     */
    protected function encodePath(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', $path)));
    }

    /**
     * get public URL untuk file di Supabase
     *
     * @param string $path path file di bucket
     * @return string public URL
     */
    public function getPublicUrl(string $path): string
    {
        // validasi: jika path kosong, return placeholder
        if (empty($path)) {
            Log::debug("⚠️ getPublicUrl: Empty path provided");
            return $this->getPlaceholderAvatar();
        }

        if (!$this->useSupabase) {
            Log::debug("⚠️ getPublicUrl: Supabase not configured, falling back to local storage", [
                'path' => $path
            ]);

            // cek apakah path mengandung 'public/' prefix (local storage)
            if (str_starts_with($path, 'public/')) {
                return asset('storage/' . str_replace('public/', '', $path));
            }

            // Check if file exists in local storage without 'public/' prefix
            // Files uploaded locally are stored as: storage/app/public/institutions/...
            // But path in DB is: institutions/...
            if (Storage::disk('public')->exists($path)) {
                Log::debug("✅ getPublicUrl: File found in local storage", ['path' => $path]);
                return asset('storage/' . $path);
            }

            // File not found - return placeholder
            Log::warning("⚠️ getPublicUrl: File not found in local storage", [
                'path' => $path,
                'checked_location' => storage_path('app/public/' . $path)
            ]);

            return $this->getPlaceholderAvatar();
        }

        // hilangkan slash di awal jika ada
        $path = ltrim($path, '/');

        // encode path untuk URL
        $encodedPath = $this->encodePath($path);

        $url = "https://{$this->projectId}.supabase.co/storage/v1/object/public/{$this->bucketName}/{$encodedPath}";

        if (config('app.debug')) {
            Log::debug("✅ getPublicUrl: Generated Supabase URL", [
                'path' => $path,
                'bucket' => $this->bucketName,
                'url' => $url,
            ]);
        }

        return $url;
    }

    /**
     * get placeholder avatar URL
     *
     * @return string placeholder avatar URL
     */
    protected function getPlaceholderAvatar(): string
    {
        return 'https://ui-avatars.com/api/?name=User&size=200&background=6366F1&color=ffffff';
    }

    /**
     * cek apakah file exists di bucket
     * 
     * @param string $path path file
     * @return bool true jika exists, false jika tidak
     */
    public function exists(string $path): bool
    {
        if (!$this->useSupabase) {
            return Storage::disk('public')->exists($path);
        }

        try {
            $encodedPath = $this->encodePath($path);
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->serviceKey,
                ])
                ->head("{$this->baseUrl}/object/{$this->bucketName}/{$encodedPath}");

            return $response->successful();

        } catch (\Exception $e) {
            Log::error("❌ Check file exists EXCEPTION", [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * hapus file dari supabase storage
     * 
     * @param string $path path file yang akan dihapus
     * @return bool true jika berhasil, false jika gagal
     */
    public function delete(string $path): bool
    {
        if (!$this->useSupabase) {
            return Storage::disk('public')->delete($path);
        }

        try {
            Log::info("🗑️ Deleting file from Supabase", ['path' => $path]);

            $encodedPath = $this->encodePath($path);

            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->serviceKey,
                ])
                ->delete("{$this->baseUrl}/object/{$this->bucketName}/{$encodedPath}");

            if ($response->successful()) {
                Log::info("✅ Delete SUCCESS", ['path' => $path]);
                return true;
            }

            Log::error("❌ Delete FAILED", [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            
            return false;

        } catch (\Exception $e) {
            Log::error("❌ Delete EXCEPTION", [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * upload gambar problem ke supabase
     * 
     * @param UploadedFile $file file gambar
     * @param int $problemId ID problem
     * @param bool $isCover apakah ini cover image
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadProblemImage(UploadedFile $file, int $problemId, bool $isCover = false)
    {
        $extension = $file->getClientOriginalExtension();
        $prefix = $isCover ? 'cover' : 'img';
        $filename = "problem-{$problemId}-{$prefix}-" . time() . '.' . $extension;
        $path = 'problems/' . $filename;

        return $this->uploadFile($file, $path);
    }

    /**
     * upload foto profil mahasiswa ke supabase
     * 
     * @param UploadedFile $file file foto
     * @param int $studentId ID mahasiswa
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadProfilePhoto(UploadedFile $file, int $studentId)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = "student-{$studentId}-profile-" . time() . '.' . $extension;
        $path = 'students/profiles/' . $filename;

        return $this->uploadFile($file, $path);
    }

    /**
     * upload logo institusi ke supabase
     *
     * @param UploadedFile $file file logo
     * @param int $institutionId ID institusi
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadInstitutionLogo(UploadedFile $file, int $institutionId)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = "institution-{$institutionId}-logo-" . time() . '.' . $extension;
        $path = 'institutions/logos/' . $filename;

        return $this->uploadFile($file, $path);
    }

    /**
     * upload logo company ke supabase
     * WAJIB: Logo company disimpan di Supabase Storage, BUKAN local storage
     *
     * @param UploadedFile $file file logo
     * @param int $companyId ID company
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadCompanyLogo(UploadedFile $file, int $companyId)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = "company-{$companyId}-logo-" . time() . '.' . $extension;
        $path = 'companies/logos/' . $filename;

        return $this->uploadFile($file, $path);
    }

    /**
     * upload verification document untuk institusi ke supabase
     *
     * @param UploadedFile $file file verification document
     * @param int $institutionId ID institusi
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadVerificationDocument(UploadedFile $file, int $institutionId)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = "institution-{$institutionId}-verification-" . time() . '.' . $extension;
        $path = 'institutions/verifications/' . $filename;

        return $this->uploadFile($file, $path);
    }

    /**
     * upload KTP penanggung jawab institusi ke supabase
     *
     * @param UploadedFile $file file KTP
     * @param int $institutionId ID institusi
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadKTP(UploadedFile $file, int $institutionId)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = "institution-{$institutionId}-ktp-" . time() . '.' . $extension;
        $path = 'institutions/ktp/' . $filename;

        return $this->uploadFile($file, $path);
    }

    /**
     * upload NPWP institusi ke supabase
     *
     * @param UploadedFile $file file NPWP
     * @param int $institutionId ID institusi
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadNPWP(UploadedFile $file, int $institutionId)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = "institution-{$institutionId}-npwp-" . time() . '.' . $extension;
        $path = 'institutions/npwp/' . $filename;

        return $this->uploadFile($file, $path);
    }

    /**
     * upload document ke supabase
     *
     * @param UploadedFile $file file document
     * @param string $category kategori document (reports, certificates, dll)
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadDocument(UploadedFile $file, string $category = 'documents/reports')
    {
        $extension = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = $originalName . '-' . time() . '.' . $extension;
        $path = $category . '/' . $filename;

        return $this->uploadFile($file, $path);
    }

    /**
     * upload proposal application ke supabase
     * 
     * @param UploadedFile $file file proposal
     * @param int $applicationId ID aplikasi
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadProposal(UploadedFile $file, int $applicationId)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = "application-{$applicationId}-proposal-" . time() . '.' . $extension;
        $path = 'proposals/' . $filename;

        return $this->uploadFile($file, $path);
    }

    /**
     * upload project report ke supabase
     * 
     * @param UploadedFile $file file report
     * @param int $projectId ID project
     * @param string $reportType tipe report (progress, final)
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadProjectReport(UploadedFile $file, int $projectId, string $reportType = 'progress')
    {
        $extension = $file->getClientOriginalExtension();
        $filename = "project-{$projectId}-{$reportType}-report-" . time() . '.' . $extension;
        $path = 'reports/' . $filename;

        return $this->uploadFile($file, $path);
    }

    /**
     * upload certificate dari local file path ke supabase
     * Method ini berbeda karena menerima file path, bukan UploadedFile
     *
     * @param string $localFilePath path ke file lokal yang akan diupload
     * @param int $studentId ID student pemilik sertifikat
     * @return string|false path file yang berhasil diupload atau false jika gagal
     */
    public function uploadCertificate(string $localFilePath, int $studentId)
    {
        if (!file_exists($localFilePath)) {
            Log::error("❌ Certificate file not found", ['path' => $localFilePath]);
            return false;
        }

        $filename = "student-{$studentId}-certificate-" . time() . '.png';
        $path = 'certificates/' . $filename;

        // jika supabase tidak dikonfigurasi, gunakan local storage
        if (!$this->useSupabase) {
            try {
                $destination = storage_path('app/public/' . $path);
                $directory = dirname($destination);

                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                if (copy($localFilePath, $destination)) {
                    Log::info("✅ Certificate saved to local storage", ['path' => $path]);
                    return $path;
                }

                return false;
            } catch (\Exception $e) {
                Log::error("❌ Failed to save certificate to local", ['error' => $e->getMessage()]);
                return false;
            }
        }

        try {
            // baca file content
            $fileContent = file_get_contents($localFilePath);
            $mimeType = 'image/png';
            $fileSize = filesize($localFilePath);

            Log::info("📤 Uploading certificate to Supabase", [
                'path' => $path,
                'size' => number_format($fileSize) . ' bytes',
            ]);

            // encode path untuk URL
            $encodedPath = $this->encodePath($path);

            // upload ke supabase
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->serviceKey,
                    'Content-Type' => $mimeType,
                    'x-upsert' => 'true',
                ])
                ->withBody($fileContent, $mimeType)
                ->post("{$this->baseUrl}/object/{$this->bucketName}/{$encodedPath}");

            if ($response->successful()) {
                Log::info("✅ Certificate upload SUCCESS", ['path' => $path]);
                return $path;
            }

            Log::error("❌ Certificate upload FAILED", [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error("❌ Certificate upload EXCEPTION", [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}