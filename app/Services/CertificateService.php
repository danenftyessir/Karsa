<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Service untuk auto-generate sertifikat KKN
 *
 * Features:
 * - Auto-generate sertifikat saat proyek completed
 * - Upload ke Supabase Storage
 * - Nomor sertifikat otomatis dengan format: 001/KKN/MHS/KARSA/XI/2024
 *
 * path: app/Services/CertificateService.php
 */
class CertificateService
{
    protected $supabaseService;

    public function __construct(SupabaseStorageService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Generate sertifikat untuk proyek yang completed
     *
     * @param Project $project
     * @return array ['success' => bool, 'path' => string, 'number' => string]
     */
    public function generateCertificate(Project $project)
    {
        try {
            // Load relasi yang dibutuhkan
            $project->load(['student.user', 'institution']);

            // Validasi: pastikan project sudah completed
            if ($project->status !== 'completed') {
                throw new \Exception('Project belum completed');
            }

            // Validasi: cek apakah sudah punya sertifikat
            if ($project->certificate_path) {
                Log::info("Project {$project->id} sudah punya sertifikat: {$project->certificate_path}");
                return [
                    'success' => true,
                    'path' => $project->certificate_path,
                    'number' => $project->certificate_number,
                    'message' => 'Sertifikat sudah ada'
                ];
            }

            // Generate nomor sertifikat
            $certificateNumber = $this->generateCertificateNumber();
            $certificateYear = now()->year;

            // Prepare data untuk sertifikat
            $certificateData = [
                'no_sertifikat' => $certificateNumber,       // "001"
                'thn_sertifikat' => $certificateYear,        // "2024"
                'nama_penerima' => $project->student->user->name,
                'nim_penerima' => $project->student->nim,
                'thn_laksana' => $project->start_date->format('Y'),
                'tgl_laksana' => $this->formatDateRange($project->start_date, $project->actual_end_date),
                'kades' => $project->institution->pic_name ?? $project->institution->name,
            ];

            // Generate image sertifikat
            $imagePath = $this->createCertificateImage($certificateData);

            if (!$imagePath) {
                throw new \Exception('Gagal membuat gambar sertifikat');
            }

            // Upload ke Supabase
            $uploadedPath = $this->supabaseService->uploadCertificate($imagePath, $project->student->id);

            // Hapus file lokal temporary
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Update project dengan info sertifikat
            $fullCertificateNumber = "{$certificateNumber}/KKN/MHS/KARSA/XI/{$certificateYear}";
            $project->update([
                'certificate_path' => $uploadedPath,
                'certificate_number' => $fullCertificateNumber,
                'certificate_generated_at' => now(),
            ]);

            Log::info("Sertifikat berhasil dibuat untuk project {$project->id}: {$uploadedPath}");

            return [
                'success' => true,
                'path' => $uploadedPath,
                'number' => $fullCertificateNumber,
                'message' => 'Sertifikat berhasil dibuat'
            ];

        } catch (\Exception $e) {
            Log::error("Error generating certificate for project {$project->id}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate nomor sertifikat otomatis (3 digit)
     * Format: 001, 002, 003, dst
     */
    private function generateCertificateNumber()
    {
        // Ambil nomor terakhir dari database
        $lastProject = Project::whereNotNull('certificate_number')
                            ->orderBy('certificate_generated_at', 'desc')
                            ->first();

        if (!$lastProject || !$lastProject->certificate_number) {
            return '001';
        }

        // Extract nomor dari format "001/KKN/MHS/KARSA/XI/2024"
        $parts = explode('/', $lastProject->certificate_number);
        $lastNumber = intval($parts[0]);

        // Increment dan format 3 digit
        $newNumber = $lastNumber + 1;
        return str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Format date range untuk tampilan di sertifikat
     * Contoh: "1 Januari - 31 Maret 2024"
     */
    private function formatDateRange($startDate, $endDate)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $start = $startDate->format('j') . ' ' . $months[$startDate->format('n')];
        $end = $endDate->format('j') . ' ' . $months[$endDate->format('n')] . ' ' . $endDate->format('Y');

        return "{$start} - {$end}";
    }

    /**
     * Create certificate image menggunakan GD
     */
    private function createCertificateImage($data)
    {
        // Path ke template dan fonts
        $templatePath = public_path('assets/certificate/template.png');
        $fontRegular = public_path('assets/certificate/fonts/Poppins-Regular.ttf');
        $fontScript = public_path('assets/certificate/fonts/PinyonScript-Regular.ttf');

        // Validasi file exists
        if (!file_exists($templatePath)) {
            throw new \Exception('Template sertifikat tidak ditemukan');
        }
        if (!file_exists($fontRegular)) {
            throw new \Exception('Font Poppins tidak ditemukan');
        }
        if (!file_exists($fontScript)) {
            throw new \Exception('Font PinyonScript tidak ditemukan');
        }

        // Load template image
        $image = imagecreatefrompng($templatePath);
        if (!$image) {
            throw new \Exception('Gagal load template image');
        }

        // Konfigurasi posisi teks (sesuai dengan kode asli)
        $textConfig = [
            'no_sertifikat' => [
                'text' => $data['no_sertifikat'],
                'x' => 647,
                'y' => 437,
                'size' => 30,
                'font' => $fontRegular,
                'color' => [30, 54, 83]
            ],
            'thn_sertifikat' => [
                'text' => $data['thn_sertifikat'],
                'x' => 1239,
                'y' => 437,
                'size' => 30,
                'font' => $fontRegular,
                'color' => [30, 54, 83]
            ],
            'nama_penerima' => [
                'text' => $data['nama_penerima'],
                'x' => 'CENTER',
                'y' => 760,
                'size' => 90,
                'font' => $fontScript,
                'color' => [225, 167, 48]  // Gold color
            ],
            'nim_penerima' => [
                'text' => $data['nim_penerima'],
                'x' => 930,
                'y' => 859,
                'size' => 36,
                'font' => $fontRegular,
                'color' => [30, 54, 83]
            ],
            'thn_laksana' => [
                'text' => $data['thn_laksana'],
                'x' => 1420,
                'y' => 934,
                'size' => 23,
                'font' => $fontRegular,
                'color' => [0, 0, 0]
            ],
            'tgl_laksana' => [
                'text' => $data['tgl_laksana'],
                'x' => 730,
                'y' => 977,
                'size' => 23,
                'font' => $fontRegular,
                'color' => [0, 0, 0]
            ],
            'kades' => [
                'text' => $data['kades'],
                'x' => 320,
                'y' => 1160,
                'size' => 30,
                'font' => $fontRegular,
                'color' => [30, 54, 83]
            ]
        ];

        // Loop untuk menulis semua teks
        foreach ($textConfig as $key => $config) {
            $color = imagecolorallocate($image, $config['color'][0], $config['color'][1], $config['color'][2]);

            // Hitung posisi X
            if (strtoupper($config['x']) === 'CENTER') {
                $bbox = imagettfbbox($config['size'], 0, $config['font'], $config['text']);
                $textWidth = $bbox[2] - $bbox[0];
                $imageWidth = imagesx($image);
                $posX = intval(($imageWidth - $textWidth) / 2);
            } else {
                $posX = intval($config['x']);
            }

            // Tulis teks ke image
            imagettftext(
                $image,
                $config['size'],
                0,
                $posX,
                $config['y'],
                $color,
                $config['font'],
                $config['text']
            );
        }

        // Save ke temporary file
        $tempPath = storage_path('app/temp_certificate_' . uniqid() . '.png');
        imagepng($image, $tempPath);
        imagedestroy($image);

        return $tempPath;
    }

    /**
     * Get URL sertifikat untuk ditampilkan
     */
    public function getCertificateUrl(Project $project)
    {
        if (!$project->certificate_path) {
            return null;
        }

        return $this->supabaseService->getPublicUrl($project->certificate_path);
    }
}
