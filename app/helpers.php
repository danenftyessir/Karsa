<?php

/**
 * file helper functions global untuk aplikasi Karsa
 * berisi fungsi-fungsi utility yang sering digunakan
 * 
 * path: app/helpers.php
 * autoload via composer.json -> "files": ["app/helpers.php"]
 */

// ===============================================
// SDG CATEGORIES HELPERS
// ===============================================

if (!function_exists('sdg_label')) {
    /**
     * helper function untuk mapping SDG integer ke label bahasa indonesia
     * single source of truth untuk seluruh aplikasi
     * 
     * @param int|string $sdgNumber nomor SDG (1-17)
     * @return string label SDG dalam bahasa indonesia
     * 
     * usage: 
     * sdg_label(1) // returns "Tanpa Kemiskinan"
     * sdg_label(4) // returns "Pendidikan Berkualitas"
     */
    function sdg_label($sdgNumber): string
    {
        $sdgLabels = [
            1 => 'Tanpa Kemiskinan',
            2 => 'Tanpa Kelaparan',
            3 => 'Kehidupan Sehat Dan Sejahtera',
            4 => 'Pendidikan Berkualitas',
            5 => 'Kesetaraan Gender',
            6 => 'Air Bersih Dan Sanitasi',
            7 => 'Energi Bersih Dan Terjangkau',
            8 => 'Pekerjaan Layak Dan Pertumbuhan Ekonomi',
            9 => 'Industri, Inovasi Dan Infrastruktur',
            10 => 'Berkurangnya Kesenjangan',
            11 => 'Kota Dan Komunitas Berkelanjutan',
            12 => 'Konsumsi Dan Produksi Bertanggung Jawab',
            13 => 'Penanganan Perubahan Iklim',
            14 => 'Ekosistem Laut',
            15 => 'Ekosistem Daratan',
            16 => 'Perdamaian, Keadilan Dan Kelembagaan Yang Kuat',
            17 => 'Kemitraan Untuk Mencapai Tujuan',
        ];
        
        // convert ke integer
        $sdgNumber = (int) $sdgNumber;
        
        return $sdgLabels[$sdgNumber] ?? 'SDG ' . $sdgNumber;
    }
}

if (!function_exists('sdg_categories_array')) {
    /**
     * helper function untuk mendapatkan semua kategori SDG
     * digunakan untuk dropdown, form select, dan filter
     * 
     * @return array array dengan key integer (1-17) dan value label indonesia
     * 
     * usage:
     * $categories = sdg_categories_array();
     * foreach ($categories as $value => $label) {
     *     echo "<option value='{$value}'>{$label}</option>";
     * }
     */
    function sdg_categories_array(): array
    {
        return [
            1 => 'Tanpa Kemiskinan',
            2 => 'Tanpa Kelaparan',
            3 => 'Kehidupan Sehat Dan Sejahtera',
            4 => 'Pendidikan Berkualitas',
            5 => 'Kesetaraan Gender',
            6 => 'Air Bersih Dan Sanitasi',
            7 => 'Energi Bersih Dan Terjangkau',
            8 => 'Pekerjaan Layak Dan Pertumbuhan Ekonomi',
            9 => 'Industri, Inovasi Dan Infrastruktur',
            10 => 'Berkurangnya Kesenjangan',
            11 => 'Kota Dan Komunitas Berkelanjutan',
            12 => 'Konsumsi Dan Produksi Bertanggung Jawab',
            13 => 'Penanganan Perubahan Iklim',
            14 => 'Ekosistem Laut',
            15 => 'Ekosistem Daratan',
            16 => 'Perdamaian, Keadilan Dan Kelembagaan Yang Kuat',
            17 => 'Kemitraan Untuk Mencapai Tujuan',
        ];
    }
}

if (!function_exists('sdg_color')) {
    /**
     * helper function untuk mendapatkan warna SDG sesuai standar UN
     * 
     * @param int $sdgNumber nomor SDG (1-17)
     * @return string hex color code
     * 
     * usage:
     * <div style="background-color: {{ sdg_color(1) }}">...</div>
     */
    function sdg_color(int $sdgNumber): string
    {
        $colors = [
            1 => '#E5243B',  // no poverty - merah
            2 => '#DDA63A',  // zero hunger - kuning
            3 => '#4C9F38',  // good health - hijau
            4 => '#C5192D',  // quality education - merah gelap
            5 => '#FF3A21',  // gender equality - oranye
            6 => '#26BDE2',  // clean water - biru muda
            7 => '#FCC30B',  // affordable energy - kuning cerah
            8 => '#A21942',  // decent work - ungu gelap
            9 => '#FD6925',  // industry innovation - oranye
            10 => '#DD1367', // reduced inequalities - pink
            11 => '#FD9D24', // sustainable cities - oranye terang
            12 => '#BF8B2E', // responsible consumption - coklat
            13 => '#3F7E44', // climate action - hijau gelap
            14 => '#0A97D9', // life below water - biru
            15 => '#56C02B', // life on land - hijau terang
            16 => '#00689D', // peace justice - biru tua
            17 => '#19486A', // partnerships - biru navy
        ];
        
        return $colors[$sdgNumber] ?? '#666666';
    }
}

// ===============================================
// FILE & STORAGE HELPERS
// ===============================================

if (!function_exists('supabase_url')) {
    /**
     * generate URL publik untuk file di Supabase Storage atau local storage
     *
     * @param string|null $path path file di bucket
     * @return string URL publik file
     */
    function supabase_url(?string $path): string
    {
        if (!$path) {
            return '';
        }

        $projectId = config('services.supabase.project_id');
        $bucket = config('services.supabase.bucket', 'karsa-storage');

        // Prioritas 1: Cek local storage dulu untuk backward compatibility
        // File path yang ada di public disk biasanya relative path
        $publicStoragePath = storage_path('app/public/' . $path);
        if (file_exists($publicStoragePath)) {
            // Return URL local storage
            return url('/storage/' . $path);
        }

        // Prioritas 2: Jika Supabase dikonfigurasi, gunakan Supabase Storage
        if (!empty($projectId)) {
            // hilangkan slash di awal jika ada
            $path = ltrim($path, '/');

            // encode path jika perlu (handle spasi dan karakter khusus)
            $encodedPath = implode('/', array_map('rawurlencode', explode('/', $path)));

            // ✅ format: https://PROJECT_ID.supabase.co/storage/v1/object/public/BUCKET_NAME/PATH
            return "https://{$projectId}.supabase.co/storage/v1/object/public/{$bucket}/{$encodedPath}";
        }

        // Fallback: Jika tidak ada yang tersedia, return empty string
        \Illuminate\Support\Facades\Log::warning('⚠️ Supabase project_id tidak dikonfigurasi dan file tidak ditemukan di local storage', ['path' => $path]);
        return '';
    }
}

if (!function_exists('document_url')) {
    /**
     * generate URL untuk mengakses dokumen
     * alias untuk supabase_url() khusus dokumen
     * 
     * @param string|null $path path file dokumen di bucket
     * @return string URL publik dokumen
     */
    function document_url(?string $path): string
    {
        return supabase_url($path);
    }
}

if (!function_exists('format_file_size')) {
    /**
     * format file size dari bytes ke human readable format
     * 
     * @param int $bytes ukuran file dalam bytes
     * @return string ukuran file yang mudah dibaca
     * 
     * usage:
     * format_file_size(1024) // "1 KB"
     * format_file_size(1048576) // "1 MB"
     */
    function format_file_size(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

// ===============================================
// DATE & TIME HELPERS
// ===============================================

if (!function_exists('time_ago')) {
    /**
     * konversi timestamp ke format "time ago" dalam bahasa indonesia
     * 
     * @param \Carbon\Carbon|string $time waktu yang akan dikonversi
     * @return string format waktu relatif
     * 
     * usage:
     * time_ago($post->created_at) // "2 jam yang lalu"
     */
    function time_ago($time): string
    {
        $time = \Carbon\Carbon::parse($time);
        $now = \Carbon\Carbon::now();
        
        $diff = $time->diff($now);
        
        if ($diff->y > 0) {
            return $diff->y . ' tahun yang lalu';
        } elseif ($diff->m > 0) {
            return $diff->m . ' bulan yang lalu';
        } elseif ($diff->d > 0) {
            return $diff->d . ' hari yang lalu';
        } elseif ($diff->h > 0) {
            return $diff->h . ' jam yang lalu';
        } elseif ($diff->i > 0) {
            return $diff->i . ' menit yang lalu';
        } else {
            return 'baru saja';
        }
    }
}

if (!function_exists('format_date_indonesian')) {
    /**
     * format tanggal ke bahasa indonesia
     * 
     * @param \Carbon\Carbon|string $date tanggal yang akan diformat
     * @param string $format format output ('short', 'medium', 'long')
     * @return string tanggal dalam format indonesia
     * 
     * usage:
     * format_date_indonesian($date, 'short') // "15 Jan 2026"
     * format_date_indonesian($date, 'long')  // "15 Januari 2026"
     */
    function format_date_indonesian($date, string $format = 'medium'): string
    {
        $date = \Carbon\Carbon::parse($date);
        
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $monthsShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        
        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        
        switch ($format) {
            case 'short':
                return $date->day . ' ' . $monthsShort[$date->month] . ' ' . $date->year;
            case 'long':
                return $days[$date->englishDayOfWeek] . ', ' . $date->day . ' ' . 
                       $months[$date->month] . ' ' . $date->year;
            case 'medium':
            default:
                return $date->day . ' ' . $months[$date->month] . ' ' . $date->year;
        }
    }
}

// ===============================================
// STATUS & BADGE HELPERS
// ===============================================

if (!function_exists('status_badge_color')) {
    /**
     * get Tailwind CSS classes untuk badge berdasarkan status
     * 
     * @param string $status status value
     * @param string $type tipe entity ('application', 'project', 'problem', 'document')
     * @return string Tailwind CSS classes
     * 
     * usage:
     * <span class="{{ status_badge_color('accepted', 'application') }}">Diterima</span>
     */
    function status_badge_color(string $status, string $type = 'application'): string
    {
        $colors = [
            'application' => [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'under_review' => 'bg-blue-100 text-blue-800',
                'accepted' => 'bg-green-100 text-green-800',
                'rejected' => 'bg-red-100 text-red-800',
            ],
            'project' => [
                'active' => 'bg-green-100 text-green-800',
                'completed' => 'bg-blue-100 text-blue-800',
                'on_hold' => 'bg-yellow-100 text-yellow-800',
                'cancelled' => 'bg-red-100 text-red-800',
            ],
            'problem' => [
                'draft' => 'bg-gray-100 text-gray-800',
                'open' => 'bg-green-100 text-green-800',
                'in_progress' => 'bg-blue-100 text-blue-800',
                'completed' => 'bg-purple-100 text-purple-800',
                'closed' => 'bg-red-100 text-red-800',
            ],
            'document' => [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'approved' => 'bg-green-100 text-green-800',
                'rejected' => 'bg-red-100 text-red-800',
            ],
        ];
        
        return $colors[$type][$status] ?? 'bg-gray-100 text-gray-800';
    }
}

if (!function_exists('status_label')) {
    /**
     * get label bahasa indonesia untuk status
     * 
     * @param string $status status value
     * @param string $type tipe entity
     * @return string label dalam bahasa indonesia
     */
    function status_label(string $status, string $type = 'application'): string
    {
        $labels = [
            'application' => [
                'pending' => 'Menunggu',
                'under_review' => 'Sedang Ditinjau',
                'accepted' => 'Diterima',
                'rejected' => 'Ditolak',
            ],
            'project' => [
                'active' => 'Aktif',
                'completed' => 'Selesai',
                'on_hold' => 'Ditunda',
                'cancelled' => 'Dibatalkan',
            ],
            'problem' => [
                'draft' => 'Draft',
                'open' => 'Terbuka',
                'in_progress' => 'Berjalan',
                'completed' => 'Selesai',
                'closed' => 'Ditutup',
            ],
            'document' => [
                'pending' => 'Menunggu Persetujuan',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
            ],
        ];
        
        return $labels[$type][$status] ?? ucfirst($status);
    }
}

// ===============================================
// DIFFICULTY LEVEL HELPERS
// ===============================================

if (!function_exists('difficulty_label')) {
    /**
     * get label bahasa indonesia untuk difficulty level
     * 
     * @param string $difficulty difficulty level (beginner, intermediate, advanced)
     * @return string label dalam bahasa indonesia
     */
    function difficulty_label(string $difficulty): string
    {
        $labels = [
            'beginner' => 'Pemula',
            'intermediate' => 'Menengah',
            'advanced' => 'Lanjutan',
        ];
        
        return $labels[$difficulty] ?? ucfirst($difficulty);
    }
}

if (!function_exists('difficulty_color')) {
    /**
     * get Tailwind CSS classes untuk difficulty badge
     * 
     * @param string $difficulty difficulty level
     * @return string Tailwind CSS classes
     */
    function difficulty_color(string $difficulty): string
    {
        $colors = [
            'beginner' => 'bg-green-100 text-green-800',
            'intermediate' => 'bg-yellow-100 text-yellow-800',
            'advanced' => 'bg-red-100 text-red-800',
        ];
        
        return $colors[$difficulty] ?? 'bg-gray-100 text-gray-800';
    }
}

// ===============================================
// USER PROFILE HELPERS
// ===============================================

if (!function_exists('get_initials')) {
    /**
     * get inisial nama user (maksimal 2 huruf)
     *
     * @param string $name nama lengkap user
     * @return string inisial nama dalam uppercase
     *
     * usage:
     * get_initials('Andi Susanto') // "AS"
     * get_initials('John') // "J"
     */
    function get_initials(string $name): string
    {
        $words = explode(" ", trim($name));
        $initials = "";

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= mb_substr($word, 0, 1);
            }
        }

        // ambil maksimal 2 huruf pertama
        return strtoupper(mb_substr($initials, 0, 2));
    }
}

// ===============================================
// NUMBER FORMATTING HELPERS
// ===============================================

if (!function_exists('format_number_indonesian')) {
    /**
     * format angka ke format indonesia (separator titik dan koma)
     * 
     * @param float|int $number angka yang akan diformat
     * @param int $decimals jumlah desimal
     * @return string angka terformat
     * 
     * usage:
     * format_number_indonesian(1000000) // "1.000.000"
     */
    function format_number_indonesian($number, int $decimals = 0): string
    {
        return number_format($number, $decimals, ',', '.');
    }
}

if (!function_exists('short_number')) {
    /**
     * format angka ke format pendek (1K, 1M, 1B)
     * 
     * @param int $number angka yang akan diformat
     * @return string angka terformat pendek
     * 
     * usage:
     * short_number(1500) // "1.5K"
     * short_number(1500000) // "1.5M"
     */
    function short_number(int $number): string
    {
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . 'B';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        
        return (string) $number;
    }
}