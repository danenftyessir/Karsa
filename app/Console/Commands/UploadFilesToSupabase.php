<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/**
 * command untuk upload files dari local storage ke supabase
 * 
 * jalankan:
 * php artisan upload:supabase --type=documents
 * php artisan upload:supabase --type=problems
 * php artisan upload:supabase --type=all
 */
class UploadFilesToSupabase extends Command
{
    protected $signature = 'upload:supabase {--type=all : Type file yang diupload (documents|problems|all)}';
    
    protected $description = 'Upload files dari local storage ke Supabase storage';

    public function handle()
    {
        $type = $this->option('type');
        
        $this->info("🚀 Memulai upload files ke Supabase...");
        $this->info("Type: {$type}");
        
        // cek koneksi ke supabase
        try {
            Storage::disk('supabase')->exists('test.txt');
            $this->info("✅ Koneksi ke Supabase berhasil");
        } catch (\Exception $e) {
            $this->error("❌ Koneksi ke Supabase gagal: " . $e->getMessage());
            $this->info("💡 Pastikan konfigurasi Supabase di .env sudah benar:");
            $this->info("   - SUPABASE_URL");
            $this->info("   - SUPABASE_ACCESS_KEY_ID");
            $this->info("   - SUPABASE_SECRET_ACCESS_KEY");
            $this->info("   - SUPABASE_BUCKET");
            return 1;
        }
        
        $this->newLine();

        if ($type === 'all' || $type === 'documents') {
            $this->uploadDocuments();
        }

        if ($type === 'all' || $type === 'problems') {
            $this->uploadProblemImages();
        }

        $this->newLine();
        $this->info("✅ Upload selesai!");
        return 0;
    }

    /**
     * upload dokumen PDF ke supabase
     */
    protected function uploadDocuments()
    {
        $this->info("📄 Uploading documents...");
        
        $localPath = storage_path('app/public/documents/reports');
        
        if (!File::exists($localPath)) {
            $this->warn("⚠️  Folder {$localPath} tidak ditemukan!");
            $this->info("💡 Buat folder terlebih dahulu dan masukkan file PDF di dalamnya");
            return;
        }

        $files = File::files($localPath);
        
        if (empty($files)) {
            $this->warn("⚠️  Tidak ada file di folder documents/reports");
            $this->info("💡 Masukkan file PDF ke folder: {$localPath}");
            return;
        }

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $uploaded = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $extension = strtolower($file->getExtension());
            
            // hanya upload PDF, DOC, DOCX
            if (!in_array($extension, ['pdf', 'doc', 'docx'])) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                $fileContent = File::get($file->getPathname());
                $supabasePath = 'documents/reports/' . $fileName;

                // cek apakah file sudah ada di supabase
                $exists = Storage::disk('supabase')->exists($supabasePath);
                
                if ($exists) {
                    // update file yang sudah ada
                    Storage::disk('supabase')->put($supabasePath, $fileContent, 'public');
                } else {
                    // upload file baru
                    Storage::disk('supabase')->put($supabasePath, $fileContent, 'public');
                }
                
                $uploaded++;
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("❌ Error upload {$fileName}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Berhasil upload {$uploaded} dokumen");
        if ($skipped > 0) {
            $this->info("⏭️  Dilewati {$skipped} file (bukan PDF/DOC/DOCX)");
        }
        if ($errors > 0) {
            $this->warn("⚠️  {$errors} file gagal diupload");
        }
    }

    /**
     * upload gambar problems ke supabase
     */
    protected function uploadProblemImages()
    {
        $this->info("🖼️  Uploading problem images...");
        
        $localPath = storage_path('app/public/problems');
        
        if (!File::exists($localPath)) {
            $this->warn("⚠️  Folder {$localPath} tidak ditemukan!");
            $this->info("💡 Buat folder terlebih dahulu dan masukkan gambar di dalamnya");
            return;
        }

        $files = File::files($localPath);
        
        if (empty($files)) {
            $this->warn("⚠️  Tidak ada file di folder problems");
            $this->info("💡 Masukkan file gambar ke folder: {$localPath}");
            return;
        }

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $uploaded = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $extension = strtolower($file->getExtension());
            
            // hanya upload gambar
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                $fileContent = File::get($file->getPathname());
                $supabasePath = 'problems/' . $fileName;

                // cek apakah file sudah ada di supabase
                $exists = Storage::disk('supabase')->exists($supabasePath);
                
                if ($exists) {
                    // update file yang sudah ada
                    Storage::disk('supabase')->put($supabasePath, $fileContent, 'public');
                } else {
                    // upload file baru
                    Storage::disk('supabase')->put($supabasePath, $fileContent, 'public');
                }
                
                $uploaded++;
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("❌ Error upload {$fileName}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Berhasil upload {$uploaded} gambar");
        if ($skipped > 0) {
            $this->info("⏭️  Dilewati {$skipped} file (bukan gambar)");
        }
        if ($errors > 0) {
            $this->warn("⚠️  {$errors} file gagal diupload");
        }
    }
}