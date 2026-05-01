<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\User;
use App\Models\Province;
use App\Models\Regency;
use Carbon\Carbon;

/**
 * Seeder untuk documents - Hybrid Version (Best of Both Worlds)
 *
 * Menggabungkan:
 * - File path yang benar untuk download
 * - Metadata lengkap untuk filter
 * - Jumlah dokumen cukup untuk testing
 *
 * jalankan: php artisan db:seed --class=DocumentsSeeder
 */
class DocumentsSeeder extends Seeder
{
    /**
     * Daftar file PDF yang ada di Supabase folder documents/reports/
     */
    private array $existingPdfs = [
        '3341b-laporan_kkn_hasbi_mudzaki_fix-1-.pdf',
        'aaLAPORAN-PROGRAM-KERJA-KKN.pdf',
        'bc4f599c360deae829ef0952f9200a4f.pdf',
        'd5460592f2ee74a2f9f5910138d650e6.pdf',
        'f3f3ec539ee2d963e804d3a964b3290f.pdf',
        'KKN_III.D.3_REG.96_2022.pdf',
        'LAPORAN AKHIR KKN .pdf',
        'laporan akhir KKN PPM OK.pdf',
        'LAPORAN KELOMPOK KKN 1077fix.pdf',
        'LAPORAN KKN DEMAPESA.pdf',
        'LAPORAN KKN KELOMPOK 2250.pdf',
        'LAPORAN KKN_1.A.2_REG.119_2024.pdf',
        'LAPORAN KKN.pdf',
        'laporan_3460160906115724.pdf',
        'laporan_akhir_201_35_2.pdf',
        'laporan_akhir_3011_45_5.pdf',
        'laporan-kelompok.pdf',
        'Laporan-KKN-2019.pdf',
        'Laporan-Tugas-Akhir-KKN-156.pdf',
    ];

    /**
     * Template documents dengan metadata lengkap
     */
    private array $documentsTemplates = [
        [
            'title' => 'Laporan KKN Pengembangan UMKM Di Desa Sukamaju',
            'description' => 'Dokumentasi lengkap program pengembangan UMKM yang meningkatkan pendapatan masyarakat desa hingga 40%.',
            'categories' => [8, 9], // Decent Work, Industry Innovation
            'tags' => ['UMKM', 'Kewirausahaan', 'Ekonomi Desa', 'Pemberdayaan'],
            'author_name' => 'Tim KKN ITB 2024',
            'institution_name' => 'Pemerintah Desa Sukamaju',
            'university_name' => 'Institut Teknologi Bandung',
            'year' => 2024,
        ],
        [
            'title' => 'Program Sanitasi Dan Air Bersih Di Desa Mekar',
            'description' => 'Hasil implementasi sistem sanitasi dan penyediaan air bersih untuk 500 keluarga.',
            'categories' => [6, 3], // Clean Water, Good Health
            'tags' => ['Sanitasi', 'Air Bersih', 'Kesehatan', 'Infrastruktur'],
            'author_name' => 'Tim KKN UGM 2024',
            'institution_name' => 'Dinas Kesehatan Kabupaten',
            'university_name' => 'Universitas Gadjah Mada',
            'year' => 2024,
        ],
        [
            'title' => 'Implementasi Energi Terbarukan Di Desa Nusantara',
            'description' => 'Studi kelayakan dan implementasi panel surya untuk listrik desa.',
            'categories' => [7, 13], // Affordable Energy, Climate Action
            'tags' => ['Energi Terbarukan', 'Panel Surya', 'Listrik Desa', 'Lingkungan'],
            'author_name' => 'Tim KKN UI 2024',
            'institution_name' => 'Pemerintah Desa Nusantara',
            'university_name' => 'Universitas Indonesia',
            'year' => 2023,
        ],
        [
            'title' => 'Pendidikan Literasi Digital Untuk Anak-Anak Desa',
            'description' => 'Program pelatihan komputer dan internet untuk siswa SD dan SMP di daerah terpencil.',
            'categories' => [4, 10], // Quality Education, Reduced Inequalities
            'tags' => ['Pendidikan', 'Literasi Digital', 'Teknologi', 'Anak-anak'],
            'author_name' => 'Tim KKN Unpad 2024',
            'institution_name' => 'Dinas Pendidikan Kabupaten',
            'university_name' => 'Universitas Padjadjaran',
            'year' => 2024,
        ],
        [
            'title' => 'Pengelolaan Sampah Organik Menjadi Kompos',
            'description' => 'Panduan praktis pengelolaan sampah organik menjadi kompos bernilai ekonomi.',
            'categories' => [11, 12], // Sustainable Cities, Responsible Consumption
            'tags' => ['Sampah', 'Kompos', 'Daur Ulang', 'Ekonomi Sirkular'],
            'author_name' => 'Tim KKN ITS 2024',
            'institution_name' => 'Dinas Lingkungan Hidup',
            'university_name' => 'Institut Teknologi Sepuluh Nopember',
            'year' => 2023,
        ],
        [
            'title' => 'Pemberdayaan Perempuan Melalui Pelatihan Keterampilan',
            'description' => 'Dokumentasi program pelatihan menjahit dan kerajinan untuk perempuan desa.',
            'categories' => [5, 8], // Gender Equality, Decent Work
            'tags' => ['Pemberdayaan Perempuan', 'Pelatihan', 'Keterampilan', 'UMKM'],
            'author_name' => 'Tim KKN Unair 2024',
            'institution_name' => 'Dinas Pemberdayaan Perempuan',
            'university_name' => 'Universitas Airlangga',
            'year' => 2024,
        ],
        [
            'title' => 'Peningkatan Hasil Pertanian Melalui Teknologi Hidroponik',
            'description' => 'Implementasi sistem hidroponik untuk meningkatkan produktivitas pertanian.',
            'categories' => [2, 9], // Zero Hunger, Industry Innovation
            'tags' => ['Pertanian', 'Hidroponik', 'Teknologi', 'Ketahanan Pangan'],
            'author_name' => 'Tim KKN IPB 2024',
            'institution_name' => 'Dinas Pertanian Kabupaten',
            'university_name' => 'Institut Pertanian Bogor',
            'year' => 2023,
        ],
        [
            'title' => 'Posyandu Digital: Modernisasi Pelayanan Kesehatan Ibu Dan Anak',
            'description' => 'Implementasi sistem digital untuk posyandu yang meningkatkan efisiensi layanan.',
            'categories' => [3, 4], // Good Health, Quality Education
            'tags' => ['Posyandu', 'Kesehatan', 'Digital', 'Ibu dan Anak'],
            'author_name' => 'Tim KKN Undip 2024',
            'institution_name' => 'Puskesmas Kecamatan',
            'university_name' => 'Universitas Diponegoro',
            'year' => 2024,
        ],
        [
            'title' => 'Pengembangan Desa Wisata Berbasis Kearifan Lokal',
            'description' => 'Strategi pengembangan desa wisata yang berkelanjutan dengan nilai budaya lokal.',
            'categories' => [8, 11], // Decent Work, Sustainable Cities
            'tags' => ['Desa Wisata', 'Pariwisata', 'Budaya Lokal', 'Ekonomi Kreatif'],
            'author_name' => 'Tim KKN UNS 2024',
            'institution_name' => 'Dinas Pariwisata Kabupaten',
            'university_name' => 'Universitas Sebelas Maret',
            'year' => 2023,
        ],
        [
            'title' => 'Bank Sampah Digital: Inovasi Pengelolaan Sampah Berbasis Aplikasi',
            'description' => 'Pengembangan aplikasi mobile untuk manajemen bank sampah yang efisien.',
            'categories' => [11, 13], // Sustainable Cities, Climate Action
            'tags' => ['Bank Sampah', 'Aplikasi', 'Teknologi', 'Lingkungan'],
            'author_name' => 'Tim KKN Telkom University 2024',
            'institution_name' => 'Kelompok Bank Sampah',
            'university_name' => 'Telkom University',
            'year' => 2024,
        ],
        [
            'title' => 'Program Rehabilitasi Lingkungan Pesisir',
            'description' => 'Penanaman mangrove dan pembersihan sampah plastik di wilayah pesisir.',
            'categories' => [14, 13], // Life Below Water, Climate Action
            'tags' => ['Mangrove', 'Pesisir', 'Lingkungan', 'Konservasi'],
            'author_name' => 'Tim KKN Unhas 2023',
            'institution_name' => 'Dinas Kelautan dan Perikanan',
            'university_name' => 'Universitas Hasanuddin',
            'year' => 2023,
        ],
        [
            'title' => 'Pembangunan Infrastruktur Jalan Desa',
            'description' => 'Proyek perbaikan jalan desa untuk meningkatkan aksesibilitas dan ekonomi lokal.',
            'categories' => [9, 11], // Industry Innovation, Sustainable Cities
            'tags' => ['Infrastruktur', 'Jalan Desa', 'Pembangunan', 'Aksesibilitas'],
            'author_name' => 'Tim KKN UB 2023',
            'institution_name' => 'Dinas Pekerjaan Umum',
            'university_name' => 'Universitas Brawijaya',
            'year' => 2023,
        ],
        [
            'title' => 'Gerakan Literasi Perpustakaan Desa',
            'description' => 'Pembentukan perpustakaan desa dan program membaca untuk meningkatkan minat baca.',
            'categories' => [4, 10], // Quality Education, Reduced Inequalities
            'tags' => ['Literasi', 'Perpustakaan', 'Pendidikan', 'Membaca'],
            'author_name' => 'Tim KKN UPI 2024',
            'institution_name' => 'Perpustakaan Daerah',
            'university_name' => 'Universitas Pendidikan Indonesia',
            'year' => 2024,
        ],
        [
            'title' => 'Pelatihan Kewirausahaan Digital Pemuda Desa',
            'description' => 'Program pelatihan e-commerce dan digital marketing untuk pemuda desa.',
            'categories' => [8, 9], // Decent Work, Industry Innovation
            'tags' => ['Kewirausahaan', 'Digital Marketing', 'E-commerce', 'Pemuda'],
            'author_name' => 'Tim KKN Binus 2024',
            'institution_name' => 'Karang Taruna Desa',
            'university_name' => 'Binus University',
            'year' => 2024,
        ],
        [
            'title' => 'Program Pencegahan Stunting Balita',
            'description' => 'Edukasi gizi dan pem berian makanan tambahan untuk pencegahan stunting.',
            'categories' => [2, 3], // Zero Hunger, Good Health
            'tags' => ['Stunting', 'Gizi', 'Balita', 'Kesehatan'],
            'author_name' => 'Tim KKN UNS 2023',
            'institution_name' => 'Puskesmas Desa',
            'university_name' => 'Universitas Sebelas Maret',
            'year' => 2023,
        ],
        [
            'title' => 'Diversifikasi Hasil Pertanian Lokal',
            'description' => 'Pengolahan hasil pertanian menjadi produk bernilai tambah tinggi.',
            'categories' => [2, 12], // Zero Hunger, Responsible Consumption
            'tags' => ['Pertanian', 'Diversifikasi', 'Produk Olahan', 'UMKM'],
            'author_name' => 'Tim KKN UNY 2024',
            'institution_name' => 'Kelompok Tani Desa',
            'university_name' => 'Universitas Negeri Yogyakarta',
            'year' => 2024,
        ],
        [
            'title' => 'Sistem Irigasi Tetes Untuk Efisiensi Air',
            'description' => 'Implementasi teknologi irigasi tetes untuk menghemat penggunaan air pertanian.',
            'categories' => [6, 15], // Clean Water, Life on Land
            'tags' => ['Irigasi', 'Efisiensi Air', 'Pertanian', 'Teknologi'],
            'author_name' => 'Tim KKN UGM 2023',
            'institution_name' => 'Dinas Pertanian',
            'university_name' => 'Universitas Gadjah Mada',
            'year' => 2023,
        ],
        [
            'title' => 'Pemberantasan Buta Aksara Di Desa Terpencil',
            'description' => 'Program pembelajaran membaca dan menulis untuk warga dewasa buta aksara.',
            'categories' => [4, 10], // Quality Education, Reduced Inequalities
            'tags' => ['Buta Aksara', 'Pendidikan', 'Literasi', 'Dewasa'],
            'author_name' => 'Tim KKN UM 2024',
            'institution_name' => 'PKBM Desa',
            'university_name' => 'Universitas Negeri Malang',
            'year' => 2024,
        ],
        [
            'title' => 'Pengembangan Budidaya Ikan Air Tawar',
            'description' => 'Pelatihan dan pendampingan budidaya ikan lele dan nila untuk ketahanan pangan.',
            'categories' => [2, 8], // Zero Hunger, Decent Work
            'tags' => ['Budidaya Ikan', 'Perikanan', 'Ketahanan Pangan', 'UMKM'],
            'author_name' => 'Tim KKN Unsoed 2023',
            'institution_name' => 'Dinas Perikanan',
            'university_name' => 'Universitas Jenderal Soedirman',
            'year' => 2023,
        ],
    ];

    /**
     * jalankan database seeds
     */
    public function run(): void
    {
        \DB::reconnect('pgsql');

        // Ambil users untuk uploaded_by
        $users = User::whereHas('student')->orWhereHas('institution')->get();

        if ($users->isEmpty()) {
            $this->command->error('❌ Tidak ada user! Jalankan UsersSeeder terlebih dahulu.');
            return;
        }

        // Ambil provinces untuk data lokasi
        $provinces = Province::all();

        if ($provinces->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada province, dokumen akan dibuat tanpa lokasi.');
        }

        $this->command->info('📄 Membuat ' . count($this->documentsTemplates) . ' dokumen...');

        foreach ($this->documentsTemplates as $template) {
            $uploader = $users->random();
            $province = $provinces->isNotEmpty() ? $provinces->random() : null;
            $regency = null;

            if ($province) {
                $regency = Regency::where('province_id', $province->id)->inRandomOrder()->first();
            }

            // ✅ PENTING: Ambil random PDF dari daftar file yang ada di Supabase
            $randomPdf = $this->existingPdfs[array_rand($this->existingPdfs)];
            $filePath = 'documents/reports/' . $randomPdf;

            // Buat document
            Document::create([
                'uploaded_by' => $uploader->id,
                'title' => $template['title'],
                'description' => $template['description'],
                'file_path' => $filePath, // ✅ Path lengkap dengan folder
                'file_type' => 'pdf',
                'file_size' => rand(500000, 5000000), // 500KB - 5MB
                'categories' => $template['categories'], // SDG categories spesifik
                'tags' => $template['tags'],
                'author_name' => $template['author_name'],
                'institution_name' => $template['institution_name'],
                'university_name' => $template['university_name'],
                'year' => $template['year'],
                'province_id' => $province?->id,
                'regency_id' => $regency?->id,
                'download_count' => rand(0, 100),
                'view_count' => rand(0, 500),
                'citation_count' => rand(0, 20),
                'is_public' => true,
                'is_featured' => fake()->boolean(30), // 30% chance
                'status' => 'approved',
                'approved_at' => Carbon::now(),
            ]);

            $this->command->info("  ✓ {$template['title']}");
        }

        $this->command->info('✅ Berhasil membuat ' . count($this->documentsTemplates) . ' dokumen!');
    }
}
