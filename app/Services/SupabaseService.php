<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * SupabaseService - Service untuk handle SEMUA operasi database dan storage ke Supabase
 *
 * PENTING: SEMUA data CRUD WAJIB langsung ke Supabase PostgreSQL
 * PENTING: SEMUA file foto/gambar WAJIB disimpan di Supabase Storage
 * TIDAK ADA data yang disimpan di local database/storage
 */
class SupabaseService
{
    protected $supabaseUrl;
    protected $supabaseKey;
    protected $supabaseServiceKey;
    protected $supabaseStorageUrl;

    public function __construct()
    {
        $this->supabaseUrl = config('services.supabase.url');
        $this->supabaseKey = config('services.supabase.anon_key');
        $this->supabaseServiceKey = config('services.supabase.service_key');
        $this->supabaseStorageUrl = config('services.supabase.storage_url');
    }

    // ========================================================================
    // DATABASE OPERATIONS - DIRECT TO SUPABASE POSTGRESQL
    // ========================================================================

    /**
     * Select data dari Supabase table
     */
    public function select($table, $columns = '*', $conditions = [])
    {
        try {
            $query = DB::connection('pgsql')->table($table)->select($columns);

            foreach ($conditions as $column => $value) {
                if (is_array($value)) {
                    $query->whereIn($column, $value);
                } else {
                    $query->where($column, $value);
                }
            }

            return $query->get();
        } catch (\Exception $e) {
            Log::error("Supabase select error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Insert data ke Supabase table
     */
    public function insert($table, $data)
    {
        try {
            return DB::connection('pgsql')->table($table)->insertGetId($data);
        } catch (\Exception $e) {
            Log::error("Supabase insert error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update data di Supabase table
     */
    public function update($table, $id, $data)
    {
        try {
            return DB::connection('pgsql')->table($table)
                ->where('id', $id)
                ->update($data);
        } catch (\Exception $e) {
            Log::error("Supabase update error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete data dari Supabase table
     */
    public function delete($table, $id)
    {
        try {
            return DB::connection('pgsql')->table($table)
                ->where('id', $id)
                ->delete();
        } catch (\Exception $e) {
            Log::error("Supabase delete error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Find single record dari Supabase table
     */
    public function find($table, $id)
    {
        try {
            return DB::connection('pgsql')->table($table)
                ->where('id', $id)
                ->first();
        } catch (\Exception $e) {
            Log::error("Supabase find error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Query builder untuk query kompleks ke Supabase
     */
    public function query($table)
    {
        return DB::connection('pgsql')->table($table);
    }

    // ========================================================================
    // STORAGE OPERATIONS - DIRECT TO SUPABASE STORAGE
    // ========================================================================

    /**
     * Upload file ke Supabase Storage
     * WAJIB: Semua foto/gambar HARUS disimpan di Supabase Storage
     *
     * @param string $bucket Nama bucket (company_logos, job_images, talent_avatars, etc)
     * @param string $filePath Path file di bucket
     * @param mixed $file File content atau UploadedFile
     * @return string Public URL dari file yang diupload
     */
    public function uploadFile($bucket, $filePath, $file)
    {
        try {
            // Upload ke Supabase Storage menggunakan Laravel Storage facade
            $disk = Storage::disk('supabase');
            $fullPath = "{$bucket}/{$filePath}";

            if (is_string($file)) {
                // Jika file adalah string (file content)
                $disk->put($fullPath, $file);
            } else {
                // Jika file adalah UploadedFile
                $disk->putFileAs($bucket, $file, basename($filePath));
            }

            // Return public URL
            return $this->getPublicUrl($bucket, $filePath);
        } catch (\Exception $e) {
            Log::error("Supabase upload file error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete file dari Supabase Storage
     */
    public function deleteFile($bucket, $filePath)
    {
        try {
            $disk = Storage::disk('supabase');
            $fullPath = "{$bucket}/{$filePath}";

            // Try to delete directly without checking existence
            // This avoids issues with exists() method on some storage drivers
            try {
                return $disk->delete($fullPath);
            } catch (\Exception $deleteException) {
                // File might not exist, which is fine
                Log::info("File not found or already deleted: {$fullPath}");
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Supabase delete file error: " . $e->getMessage());
            // Don't throw error for delete failures - just log it
            return false;
        }
    }

    /**
     * Get public URL dari file di Supabase Storage
     */
    public function getPublicUrl($bucket, $filePath)
    {
        $supabaseUrl = env('SUPABASE_URL');
        return "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filePath}";
    }

    /**
     * Generate signed URL untuk private files
     */
    public function getSignedUrl($bucket, $filePath, $expiresIn = 3600)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->supabaseServiceKey,
                'apikey' => $this->supabaseKey,
            ])->post("{$this->supabaseUrl}/storage/v1/object/sign/{$bucket}/{$filePath}", [
                'expiresIn' => $expiresIn,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return "{$this->supabaseUrl}/storage/v1{$data['signedURL']}";
            }

            throw new \Exception("Failed to generate signed URL: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Supabase signed URL error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * List files dalam bucket
     */
    public function listFiles($bucket, $prefix = '')
    {
        try {
            $disk = Storage::disk('supabase');
            $path = $prefix ? "{$bucket}/{$prefix}" : $bucket;
            return $disk->files($path);
        } catch (\Exception $e) {
            Log::error("Supabase list files error: " . $e->getMessage());
            throw $e;
        }
    }

    // ========================================================================
    // HELPER METHODS
    // ========================================================================

    /**
     * Execute raw SQL query ke Supabase PostgreSQL
     */
    public function raw($sql, $bindings = [])
    {
        try {
            return DB::connection('pgsql')->select($sql, $bindings);
        } catch (\Exception $e) {
            Log::error("Supabase raw query error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        DB::connection('pgsql')->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        DB::connection('pgsql')->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        DB::connection('pgsql')->rollBack();
    }

    /**
     * Check if Supabase connection is alive
     */
    public function healthCheck()
    {
        try {
            DB::connection('pgsql')->getPdo();
            return true;
        } catch (\Exception $e) {
            Log::error("Supabase health check failed: " . $e->getMessage());
            return false;
        }
    }
}
