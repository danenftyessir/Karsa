<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Problem;
use App\Models\Institution;
use App\Models\Province;
use App\Models\Regency;
use Carbon\Carbon;

/**
 * seeder untuk problems dengan format SDG categories yang benar
 * ✅ FULL CODE dengan SEMUA template SDG 1-17
 * ✅ sdg_categories disimpan sebagai ARRAY LANGSUNG, bukan JSON string
 * 
 * jalankan: php artisan db:seed --class=ProblemsSeeder
 * 
 * TIDAK PERLU lagi menjalankan php artisan fix:double-encoded-json setelah seeding!
 */
class ProblemsSeeder extends Seeder
{
    /**
     * jalankan database seeds
     */
    public function run(): void
    {
        $institutions = Institution::all();
        $provinces = Province::all();
        
        if ($institutions->isEmpty() || $provinces->isEmpty()) {
            $this->command->error('Harap jalankan InstitutionsSeeder dan ProvincesSeeder terlebih dahulu!');
            return;
        }

        // array template problems dengan SDG categories yang benar
        $problemsTemplates = $this->getProblemTemplates();

        $this->command->info('Membuat ' . count($problemsTemplates) . ' problems...');

        // create problems untuk setiap template
        foreach ($problemsTemplates as $index => $template) {
            // Reconnect every 5 iterations to prevent prepared statement errors
            if ($index % 5 == 0) {
                \DB::reconnect('pgsql');
            }

            // pilih institution random
            $institution = $institutions->random();

            // pilih province random
            $province = $provinces->random();

            // ambil regency dari province tersebut
            $regency = Regency::where('province_id', $province->id)->inRandomOrder()->first();
            
            if (!$regency) {
                continue; // skip jika tidak ada regency
            }

            // generate dates
            $startDate = Carbon::now()->addDays(rand(30, 60));
            $durationMonths = rand(1, 2);
            $endDate = $startDate->copy()->addMonths($durationMonths);
            $applicationDeadline = $startDate->copy()->subDays(rand(7, 14));

            // ✅ PENTING: sdg_categories langsung pass ARRAY, JANGAN json_encode!
            Problem::create([
                'institution_id' => $institution->id,
                'title' => $template['title'],
                'description' => $template['description'],
                'background' => $template['background'],
                'objectives' => $template['objectives'],
                'scope' => $template['scope'],
                'province_id' => $province->id,
                'regency_id' => $regency->id,
                'village' => 'Desa ' . fake()->city(),
                'detailed_location' => fake()->address(),
                
                // ✅ BENAR: langsung pass array of integers
                'sdg_categories' => $template['sdg_categories'],
                'required_skills' => $template['required_skills'],
                'required_majors' => ['Semua Jurusan'],
                
                'required_students' => rand(2, 5),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'application_deadline' => $applicationDeadline,
                'duration_months' => $durationMonths,
                'difficulty_level' => $template['difficulty_level'],
                'status' => 'open',
                'expected_outcomes' => 'Hasil yang terukur dan berdampak positif bagi masyarakat.',
                'deliverables' => [
                    'Laporan kegiatan',
                    'Dokumentasi foto/video',
                    'Laporan akhir proyek'
                ],
                'facilities_provided' => [
                    'Tempat pelaksanaan kegiatan',
                    'Koordinasi dengan pihak terkait',
                    'Surat tugas resmi'
                ],
                'is_featured' => fake()->boolean(20),
                'is_urgent' => fake()->boolean(10),
            ]);

            // progress indicator
            if (($index + 1) % 10 == 0) {
                $this->command->info('Progress: ' . ($index + 1) . '/' . count($problemsTemplates));
            }
        }

        $this->command->info('✅ Problems seeder berhasil dijalankan!');
        $this->command->info('Total problems: ' . Problem::count());
    }

    /**
     * get problem templates - FULL 85+ templates untuk SDG 1-17
     */
    protected function getProblemTemplates(): array
    {
        return [
            // =============================================
            // SDG 1: TANPA KEMISKINAN (5 templates)
            // =============================================
            [
                'title' => 'Pemberdayaan Ekonomi Masyarakat Desa',
                'description' => 'Program untuk meningkatkan perekonomian masyarakat desa melalui pelatihan kewirausahaan dan akses modal usaha.',
                'background' => 'Tingkat kemiskinan di desa masih tinggi, perlu program pemberdayaan yang berkelanjutan.',
                'objectives' => 'Meningkatkan pendapatan dan kesejahteraan masyarakat desa.',
                'scope' => 'Pelatihan kewirausahaan, pendampingan UMKM, dan fasilitasi akses modal.',
                'sdg_categories' => [1, 8],
                'required_skills' => ['Ekonomi', 'Manajemen', 'Kewirausahaan', 'Akuntansi'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Bank Sampah Untuk Kesejahteraan',
                'description' => 'Pendirian dan pengelolaan bank sampah sebagai sumber penghasilan tambahan masyarakat.',
                'background' => 'Sampah yang belum terkelola dapat menjadi sumber ekonomi baru.',
                'objectives' => 'Mengurangi kemiskinan sambil menjaga kebersihan lingkungan.',
                'scope' => 'Pembentukan bank sampah, pelatihan sortir, dan pemasaran hasil daur ulang.',
                'sdg_categories' => [1, 11, 12],
                'required_skills' => ['Ekonomi', 'Lingkungan', 'Manajemen', 'Marketing'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Koperasi Simpan Pinjam Desa',
                'description' => 'Pembentukan koperasi simpan pinjam untuk membantu akses modal usaha mikro.',
                'background' => 'Masyarakat kesulitan akses ke lembaga keuangan formal.',
                'objectives' => 'Menyediakan akses keuangan yang mudah dan terjangkau.',
                'scope' => 'Pembentukan koperasi, pelatihan manajemen keuangan, dan operasional.',
                'sdg_categories' => [1, 8, 17],
                'required_skills' => ['Ekonomi', 'Akuntansi', 'Manajemen Koperasi', 'Hukum'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Program Beasiswa Anak Tidak Mampu',
                'description' => 'Fasilitasi akses pendidikan untuk anak-anak dari keluarga kurang mampu.',
                'background' => 'Banyak anak putus sekolah karena keterbatasan ekonomi.',
                'objectives' => 'Memutus rantai kemiskinan melalui pendidikan.',
                'scope' => 'Pendataan anak kurang mampu, penggalangan dana, dan distribusi beasiswa.',
                'sdg_categories' => [1, 4],
                'required_skills' => ['Pendidikan', 'Sosial', 'Administrasi', 'Fundraising'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Pelatihan Keterampilan Warga Miskin',
                'description' => 'Program pelatihan keterampilan praktis untuk mengurangi pengangguran.',
                'background' => 'Tingkat pengangguran tinggi akibat kurangnya keterampilan.',
                'objectives' => 'Meningkatkan employability dan mengurangi kemiskinan.',
                'scope' => 'Pelatihan menjahit, otomotif, elektronik, dan keterampilan lainnya.',
                'sdg_categories' => [1, 8, 4],
                'required_skills' => ['Pendidikan', 'Teknik', 'Manajemen Pelatihan'],
                'difficulty_level' => 'intermediate',
            ],

            // =============================================
            // SDG 2: TANPA KELAPARAN (5 templates)
            // =============================================
            [
                'title' => 'Optimalisasi Lahan Pertanian',
                'description' => 'Program peningkatan produktivitas pertanian melalui teknologi modern dan pendampingan.',
                'background' => 'Produktivitas pertanian masih rendah karena kurangnya akses teknologi.',
                'objectives' => 'Meningkatkan hasil panen dan ketahanan pangan.',
                'scope' => 'Pendampingan teknis, penyuluhan, dan introduksi teknologi pertanian.',
                'sdg_categories' => [2, 12],
                'required_skills' => ['Pertanian', 'Agronomi', 'Penyuluhan', 'Teknologi Pertanian'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Hidroponik Dan Pertanian Modern',
                'description' => 'Pengembangan sistem hidroponik untuk pertanian perkotaan.',
                'background' => 'Lahan terbatas di perkotaan namun kebutuhan pangan terus meningkat.',
                'objectives' => 'Menciptakan ketahanan pangan di lingkungan urban.',
                'scope' => 'Instalasi sistem hidroponik, pelatihan pemeliharaan, dan pemasaran hasil.',
                'sdg_categories' => [2, 11, 9],
                'required_skills' => ['Pertanian', 'Teknologi', 'Biologi', 'Marketing'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Diversifikasi Pangan Lokal',
                'description' => 'Program untuk meningkatkan konsumsi dan produksi pangan lokal.',
                'background' => 'Ketergantungan pada beras terlalu tinggi, perlu diversifikasi.',
                'objectives' => 'Meningkatkan keberagaman pangan dan ketahanan pangan.',
                'scope' => 'Sosialisasi pangan lokal, pelatihan pengolahan, dan pemasaran.',
                'sdg_categories' => [2, 12],
                'required_skills' => ['Gizi', 'Pertanian', 'Pengolahan Pangan', 'Komunikasi'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Kebun Sayur Komunitas',
                'description' => 'Pembentukan kebun sayur bersama untuk memenuhi kebutuhan sayuran warga.',
                'background' => 'Harga sayuran mahal dan akses terbatas.',
                'objectives' => 'Menyediakan sayuran segar dengan harga terjangkau.',
                'scope' => 'Pembukaan lahan, penanaman sayuran, dan sistem bagi hasil.',
                'sdg_categories' => [2, 11],
                'required_skills' => ['Pertanian', 'Hortikultura', 'Sosial', 'Manajemen'],
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Bank Pangan Desa',
                'description' => 'Pembentukan lumbung pangan untuk mengantisipasi krisis pangan.',
                'background' => 'Ketahanan pangan masih rentan saat paceklik.',
                'objectives' => 'Memastikan ketersediaan pangan sepanjang tahun.',
                'scope' => 'Pembangunan gudang, sistem simpan pinjam pangan, dan manajemen stok.',
                'sdg_categories' => [2, 1],
                'required_skills' => ['Pertanian', 'Manajemen', 'Logistik', 'Sosial'],
                'difficulty_level' => 'intermediate',
            ],

            // =============================================
            // SDG 3: KEHIDUPAN SEHAT DAN SEJAHTERA (5 templates)
            // =============================================
            [
                'title' => 'Posyandu Digital Desa',
                'description' => 'Revitalisasi posyandu dengan sistem digital untuk layanan kesehatan ibu dan anak.',
                'background' => 'Posyandu kurang aktif dan data tidak terintegrasi.',
                'objectives' => 'Meningkatkan cakupan layanan kesehatan dasar.',
                'scope' => 'Pelatihan kader, implementasi aplikasi, dan monitoring digital.',
                'sdg_categories' => [3, 9],
                'required_skills' => ['Kesehatan Masyarakat', 'Teknologi Informasi', 'Gizi', 'Manajemen'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Edukasi Kesehatan Dan Sanitasi',
                'description' => 'Program sosialisasi perilaku hidup bersih dan sehat (PHBS).',
                'background' => 'Tingkat kesadaran masyarakat tentang PHBS masih rendah.',
                'objectives' => 'Meningkatkan kesadaran dan praktik hidup sehat.',
                'scope' => 'Penyuluhan, demonstrasi, dan monitoring perilaku hidup sehat.',
                'sdg_categories' => [3, 6],
                'required_skills' => ['Kesehatan Masyarakat', 'Komunikasi', 'Penyuluhan', 'Sosiologi'],
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Program Lansia Sehat',
                'description' => 'Layanan kesehatan dan senam khusus untuk lansia.',
                'background' => 'Populasi lansia meningkat namun layanan kesehatan khusus kurang.',
                'objectives' => 'Meningkatkan kualitas hidup lansia.',
                'scope' => 'Senam rutin, pemeriksaan kesehatan, dan kegiatan sosial lansia.',
                'sdg_categories' => [3, 10],
                'required_skills' => ['Kesehatan Masyarakat', 'Fisioterapi', 'Psikologi', 'Sosial'],
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Gerakan Hidup Sehat',
                'description' => 'Kampanye gaya hidup sehat melalui olahraga dan pola makan bergizi.',
                'background' => 'Penyakit tidak menular meningkat akibat gaya hidup tidak sehat.',
                'objectives' => 'Meningkatkan kesadaran hidup sehat dan menurunkan penyakit.',
                'scope' => 'Senam massal, konseling gizi, dan pemeriksaan kesehatan gratis.',
                'sdg_categories' => [3, 4],
                'required_skills' => ['Kesehatan Masyarakat', 'Gizi', 'Olahraga', 'Komunikasi'],
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Pencegahan Stunting Melalui Edukasi Gizi',
                'description' => 'Program pencegahan stunting melalui edukasi gizi ibu dan anak.',
                'background' => 'Angka stunting masih tinggi akibat kurangnya pengetahuan gizi.',
                'objectives' => 'Menurunkan prevalensi stunting di desa.',
                'scope' => 'Edukasi gizi, pemberian makanan tambahan, dan monitoring pertumbuhan.',
                'sdg_categories' => [2, 3],
                'required_skills' => ['Gizi', 'Kesehatan Masyarakat', 'Pendidikan', 'Komunikasi'],
                'difficulty_level' => 'intermediate',
            ],

            // =============================================
            // SDG 4: PENDIDIKAN BERKUALITAS (5 templates)
            // =============================================
            [
                'title' => 'Bimbingan Belajar Gratis',
                'description' => 'Program bimbingan belajar gratis untuk siswa kurang mampu.',
                'background' => 'Banyak siswa kesulitan belajar karena kurangnya bimbingan.',
                'objectives' => 'Meningkatkan prestasi akademik siswa.',
                'scope' => 'Les mata pelajaran, bimbingan PR, dan motivasi belajar.',
                'sdg_categories' => [4, 1],
                'required_skills' => ['Pendidikan', 'Matematika', 'IPA', 'Bahasa'],
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Literasi Digital Untuk Anak Desa',
                'description' => 'Pelatihan penggunaan teknologi untuk pembelajaran.',
                'background' => 'Anak-anak desa kurang familiar dengan teknologi pembelajaran.',
                'objectives' => 'Meningkatkan literasi digital anak desa.',
                'scope' => 'Pelatihan komputer, aplikasi pembelajaran, dan internet sehat.',
                'sdg_categories' => [4, 10, 9],
                'required_skills' => ['Teknologi Pendidikan', 'Informatika', 'Pendidikan'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Taman Baca Masyarakat',
                'description' => 'Pembangunan dan pengelolaan taman baca untuk meningkatkan literasi.',
                'background' => 'Akses buku dan minat baca masih rendah.',
                'objectives' => 'Meningkatkan minat baca dan literasi masyarakat.',
                'scope' => 'Pengadaan buku, penataan ruang, dan program membaca.',
                'sdg_categories' => [4, 10],
                'required_skills' => ['Perpustakaan', 'Pendidikan', 'Manajemen', 'Komunikasi'],
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Pelatihan Guru PAUD',
                'description' => 'Pelatihan metode pembelajaran modern untuk guru PAUD.',
                'background' => 'Kualitas pendidikan anak usia dini masih perlu ditingkatkan.',
                'objectives' => 'Meningkatkan kompetensi guru PAUD.',
                'scope' => 'Workshop metode pembelajaran, alat peraga, dan psikologi anak.',
                'sdg_categories' => [4, 5],
                'required_skills' => ['Pendidikan', 'Psikologi Anak', 'Manajemen Kelas'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Sekolah Inklusi Ramah Disabilitas',
                'description' => 'Program inklusi pendidikan untuk anak berkebutuhan khusus.',
                'background' => 'Anak disabilitas kesulitan akses pendidikan.',
                'objectives' => 'Mewujudkan pendidikan inklusif dan ramah disabilitas.',
                'scope' => 'Adaptasi fasilitas, pelatihan guru, dan pendampingan siswa.',
                'sdg_categories' => [4, 10],
                'required_skills' => ['Pendidikan Khusus', 'Psikologi', 'Terapi', 'Arsitektur'],
                'difficulty_level' => 'advanced',
            ],

            // =============================================
            // SDG 5: KESETARAAN GENDER (5 templates)
            // =============================================
            [
                'title' => 'Pemberdayaan Perempuan Desa',
                'description' => 'Program pemberdayaan ekonomi perempuan melalui keterampilan dan UMKM.',
                'background' => 'Perempuan desa kurang berperan dalam ekonomi keluarga.',
                'objectives' => 'Meningkatkan kemandirian ekonomi perempuan.',
                'scope' => 'Pelatihan keterampilan, pendampingan usaha, dan akses modal.',
                'sdg_categories' => [5, 8],
                'required_skills' => ['Gender Studies', 'Ekonomi', 'Kewirausahaan', 'Sosial'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Pencegahan Kekerasan Terhadap Perempuan',
                'description' => 'Kampanye dan pendampingan pencegahan kekerasan berbasis gender.',
                'background' => 'Kasus kekerasan terhadap perempuan masih tinggi.',
                'objectives' => 'Menurunkan angka kekerasan dan meningkatkan kesadaran gender.',
                'scope' => 'Sosialisasi, pendampingan korban, dan pembentukan support group.',
                'sdg_categories' => [5, 16],
                'required_skills' => ['Hukum', 'Psikologi', 'Sosial', 'Konseling'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Kepemimpinan Perempuan Dalam Desa',
                'description' => 'Program pengembangan kapasitas kepemimpinan perempuan.',
                'background' => 'Partisipasi perempuan dalam pengambilan keputusan masih rendah.',
                'objectives' => 'Meningkatkan keterwakilan perempuan dalam kepemimpinan desa.',
                'scope' => 'Pelatihan kepemimpinan, mentoring, dan advokasi kebijakan.',
                'sdg_categories' => [5, 16],
                'required_skills' => ['Gender Studies', 'Kepemimpinan', 'Politik', 'Komunikasi'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Perempuan Dan Teknologi',
                'description' => 'Program pelatihan teknologi informasi khusus untuk perempuan desa.',
                'background' => 'Kesenjangan gender dalam akses teknologi masih tinggi.',
                'objectives' => 'Meningkatkan literasi digital perempuan desa.',
                'scope' => 'Pelatihan komputer, media sosial untuk bisnis, dan aplikasi produktivitas.',
                'sdg_categories' => [5, 9],
                'required_skills' => ['Teknologi Informasi', 'Pendidikan', 'Gender Studies'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Kesehatan Reproduksi Perempuan',
                'description' => 'Edukasi kesehatan reproduksi dan keluarga berencana.',
                'background' => 'Pengetahuan kesehatan reproduksi masih rendah.',
                'objectives' => 'Meningkatkan kesadaran dan akses layanan kesehatan reproduksi.',
                'scope' => 'Penyuluhan, konseling, dan rujukan layanan kesehatan.',
                'sdg_categories' => [5, 3],
                'required_skills' => ['Kebidanan', 'Kesehatan Masyarakat', 'Konseling'],
                'difficulty_level' => 'intermediate',
            ],

            // =============================================
            // SDG 6: AIR BERSIH DAN SANITASI (5 templates)
            // =============================================
            [
                'title' => 'Akses Air Bersih Dan Sanitasi',
                'description' => 'Pembangunan infrastruktur air bersih dan fasilitas sanitasi.',
                'background' => 'Akses air bersih masih terbatas di banyak desa.',
                'objectives' => 'Menyediakan akses air bersih yang layak untuk seluruh warga.',
                'scope' => 'Pembangunan sumur bor, instalasi pipa, dan fasilitas MCK.',
                'sdg_categories' => [6, 3],
                'required_skills' => ['Teknik Sipil', 'Kesehatan Lingkungan', 'Manajemen Proyek'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Sistem Filtrasi Air Sederhana',
                'description' => 'Program pembuatan dan distribusi alat filtrasi air untuk rumah tangga.',
                'background' => 'Kualitas air sumur masih kurang baik.',
                'objectives' => 'Meningkatkan akses air bersih melalui teknologi filtrasi.',
                'scope' => 'Pelatihan pembuatan filter, distribusi alat, dan monitoring kualitas.',
                'sdg_categories' => [6, 3],
                'required_skills' => ['Teknik Lingkungan', 'Kesehatan', 'Teknologi'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Sanitasi Total Berbasis Masyarakat',
                'description' => 'Program STBM untuk mengubah perilaku sanitasi masyarakat.',
                'background' => 'Masih banyak warga yang belum memiliki jamban sehat.',
                'objectives' => 'Mewujudkan desa bebas buang air besar sembarangan.',
                'scope' => 'Kampanye sanitasi, pembangunan jamban, dan monitoring.',
                'sdg_categories' => [6, 3],
                'required_skills' => ['Kesehatan Lingkungan', 'Teknik Sipil', 'Komunikasi'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Konservasi Mata Air Desa',
                'description' => 'Program pelestarian dan pengelolaan mata air sebagai sumber air bersih.',
                'background' => 'Mata air mengalami penurunan debit akibat kerusakan lingkungan.',
                'objectives' => 'Melestarikan mata air untuk ketersediaan air jangka panjang.',
                'scope' => 'Reboisasi, pembuatan biopori, dan pengelolaan tata air.',
                'sdg_categories' => [6, 15],
                'required_skills' => ['Kehutanan', 'Hidrologi', 'Lingkungan', 'Sosial'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Pengelolaan Limbah Rumah Tangga',
                'description' => 'Sistem pengelolaan limbah cair rumah tangga yang ramah lingkungan.',
                'background' => 'Limbah rumah tangga mencemari sumber air.',
                'objectives' => 'Mengurangi pencemaran air dari limbah rumah tangga.',
                'scope' => 'Pembuatan bak resapan, biofilter, dan edukasi pengelolaan limbah.',
                'sdg_categories' => [6, 11],
                'required_skills' => ['Teknik Lingkungan', 'Kesehatan', 'Teknik Sipil'],
                'difficulty_level' => 'intermediate',
            ],

            // =============================================
            // SDG 7: ENERGI BERSIH DAN TERJANGKAU (5 templates)
            // =============================================
            [
                'title' => 'Energi Terbarukan Desa',
                'description' => 'Implementasi energi terbarukan untuk listrik desa.',
                'background' => 'Ketergantungan pada energi fosil masih tinggi.',
                'objectives' => 'Menyediakan energi bersih dan terjangkau.',
                'scope' => 'Instalasi panel surya, turbin angin, atau mikrohidro.',
                'sdg_categories' => [7, 13],
                'required_skills' => ['Teknik Elektro', 'Energi Terbarukan', 'Manajemen Proyek'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Kompor Biogas Rumah Tangga',
                'description' => 'Program pembuatan biogas dari limbah ternak untuk memasak.',
                'background' => 'Biaya LPG mahal dan limbah ternak belum dimanfaatkan.',
                'objectives' => 'Menyediakan energi alternatif yang murah dan ramah lingkungan.',
                'scope' => 'Pembuatan digester biogas, pelatihan, dan pendampingan.',
                'sdg_categories' => [7, 12],
                'required_skills' => ['Teknik Pertanian', 'Energi', 'Lingkungan'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Lampu Tenaga Surya',
                'description' => 'Instalasi lampu tenaga surya untuk penerangan jalan dan fasilitas umum.',
                'background' => 'Penerangan jalan masih minim di desa.',
                'objectives' => 'Meningkatkan keamanan dan kenyamanan melalui penerangan.',
                'scope' => 'Instalasi solar street light dan pelatihan perawatan.',
                'sdg_categories' => [7, 11],
                'required_skills' => ['Teknik Elektro', 'Teknik Sipil', 'Manajemen'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Hemat Energi Rumah Tangga',
                'description' => 'Kampanye efisiensi energi dan penggunaan peralatan hemat listrik.',
                'background' => 'Konsumsi energi rumah tangga tidak efisien.',
                'objectives' => 'Mengurangi konsumsi energi dan biaya listrik.',
                'scope' => 'Sosialisasi, audit energi rumah, dan pendampingan.',
                'sdg_categories' => [7, 13],
                'required_skills' => ['Teknik Elektro', 'Komunikasi', 'Manajemen Energi'],
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Pembangkit Listrik Mikrohidro',
                'description' => 'Pembangunan pembangkit listrik tenaga air skala kecil.',
                'background' => 'Desa memiliki potensi air tapi belum ada listrik PLN.',
                'objectives' => 'Menyediakan listrik dari sumber terbarukan.',
                'scope' => 'Survey potensi, pembangunan, dan pelatihan operator.',
                'sdg_categories' => [7, 9],
                'required_skills' => ['Teknik Elektro', 'Teknik Sipil', 'Hidrologi'],
                'difficulty_level' => 'advanced',
            ],

            // =============================================
            // SDG 8: PEKERJAAN LAYAK DAN PERTUMBUHAN EKONOMI (5 templates)
            // =============================================
            [
                'title' => 'Kewirausahaan Digital',
                'description' => 'Pelatihan bisnis online dan pemasaran digital untuk UMKM.',
                'background' => 'UMKM belum optimal memanfaatkan platform digital.',
                'objectives' => 'Meningkatkan penjualan UMKM melalui platform digital.',
                'scope' => 'Pelatihan e-commerce, social media marketing, dan marketplace.',
                'sdg_categories' => [8, 9],
                'required_skills' => ['Marketing Digital', 'E-Commerce', 'Teknologi', 'Bisnis'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Job Fair Dan Pelatihan Kerja',
                'description' => 'Bursa kerja dan pelatihan untuk meningkatkan ketenagakerjaan.',
                'background' => 'Tingkat pengangguran masih tinggi di desa.',
                'objectives' => 'Menghubungkan pencari kerja dengan pemberi kerja.',
                'scope' => 'Job fair, pelatihan keterampilan, dan pendampingan karir.',
                'sdg_categories' => [8],
                'required_skills' => ['SDM', 'Manajemen', 'Komunikasi', 'Pelatihan'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Sertifikasi Produk UMKM',
                'description' => 'Pendampingan UMKM untuk mendapatkan sertifikasi produk.',
                'background' => 'Produk UMKM sulit bersaing karena belum bersertifikat.',
                'objectives' => 'Meningkatkan daya saing produk UMKM.',
                'scope' => 'Pelatihan standar produksi, pendampingan sertifikasi, dan branding.',
                'sdg_categories' => [8, 9],
                'required_skills' => ['Manajemen Mutu', 'Bisnis', 'Hukum', 'Marketing'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Wisata Desa Berkelanjutan',
                'description' => 'Pengembangan desa wisata berbasis komunitas.',
                'background' => 'Potensi wisata desa belum dikembangkan optimal.',
                'objectives' => 'Meningkatkan pendapatan masyarakat dari sektor pariwisata.',
                'scope' => 'Pelatihan pemandu wisata, paket wisata, dan promosi.',
                'sdg_categories' => [8, 11],
                'required_skills' => ['Pariwisata', 'Marketing', 'Manajemen', 'Komunikasi'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Pekerja Migran Aman',
                'description' => 'Edukasi dan pendampingan calon pekerja migran.',
                'background' => 'Banyak kasus penipuan dan eksploitasi pekerja migran.',
                'objectives' => 'Melindungi hak-hak pekerja migran.',
                'scope' => 'Sosialisasi, verifikasi PPTKIS, dan pendampingan hukum.',
                'sdg_categories' => [8, 16],
                'required_skills' => ['Hukum', 'Sosial', 'Komunikasi', 'Advokasi'],
                'difficulty_level' => 'advanced',
            ],

            // =============================================
            // SDG 9: INDUSTRI, INOVASI DAN INFRASTRUKTUR (5 templates)
            // =============================================
            [
                'title' => 'Smart Village Teknologi',
                'description' => 'Implementasi teknologi IoT untuk smart village.',
                'background' => 'Desa perlu adopsi teknologi untuk efisiensi pengelolaan.',
                'objectives' => 'Mewujudkan desa cerdas berbasis teknologi.',
                'scope' => 'Instalasi sensor, dashboard monitoring, dan pelatihan.',
                'sdg_categories' => [11, 9],
                'required_skills' => ['Teknologi Informasi', 'IoT', 'Data Science', 'Manajemen'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Digitalisasi Administrasi Desa',
                'description' => 'Sistem informasi desa untuk administrasi digital.',
                'background' => 'Administrasi desa masih manual dan tidak efisien.',
                'objectives' => 'Meningkatkan efisiensi pelayanan administrasi.',
                'scope' => 'Implementasi aplikasi SID, pelatihan, dan digitalisasi data.',
                'sdg_categories' => [17, 9],
                'required_skills' => ['Teknologi Informasi', 'Administrasi', 'Manajemen Data'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Inkubasi Bisnis Desa',
                'description' => 'Program inkubator untuk startup dan inovasi di desa.',
                'background' => 'Ide bisnis inovatif kurang mendapat dukungan.',
                'objectives' => 'Mendorong inovasi dan kewirausahaan di desa.',
                'scope' => 'Mentoring, pendanaan awal, dan networking.',
                'sdg_categories' => [17, 8],
                'required_skills' => ['Bisnis', 'Kewirausahaan', 'Mentoring', 'Teknologi'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Infrastruktur Jalan Desa',
                'description' => 'Perbaikan dan pemeliharaan jalan untuk akses ekonomi.',
                'background' => 'Kondisi jalan buruk menghambat aktivitas ekonomi.',
                'objectives' => 'Meningkatkan konektivitas dan akses pasar.',
                'scope' => 'Survei kerusakan, perbaikan jalan, dan pembangunan jembatan.',
                'sdg_categories' => [9, 11],
                'required_skills' => ['Teknik Sipil', 'Manajemen Proyek', 'Survey'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Akses Perbankan Untuk Masyarakat Miskin',
                'description' => 'Program inklusi keuangan untuk masyarakat unbankable.',
                'background' => 'Masyarakat miskin sulit akses layanan perbankan.',
                'objectives' => 'Meningkatkan akses keuangan formal.',
                'scope' => 'Pembukaan rekening, edukasi literasi keuangan, dan mobile banking.',
                'sdg_categories' => [1, 9],
                'required_skills' => ['Keuangan', 'Perbankan', 'Pendidikan', 'Teknologi'],
                'difficulty_level' => 'intermediate',
            ],

            // =============================================
            // SDG 10: BERKURANGNYA KESENJANGAN (3 templates)
            // =============================================
            [
                'title' => 'Program Bantuan Sosial Terpadu',
                'description' => 'Sistem distribusi bantuan sosial yang tepat sasaran.',
                'background' => 'Penyaluran bantuan sosial belum optimal.',
                'objectives' => 'Memastikan bantuan sampai ke yang berhak.',
                'scope' => 'Pendataan, verifikasi, dan monitoring distribusi.',
                'sdg_categories' => [1, 10],
                'required_skills' => ['Sosial', 'Administrasi', 'Data', 'Manajemen'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Layanan Hukum Gratis Masyarakat',
                'description' => 'Bantuan hukum untuk masyarakat tidak mampu.',
                'background' => 'Akses keadilan masih sulit bagi masyarakat miskin.',
                'objectives' => 'Menjamin akses keadilan bagi semua.',
                'scope' => 'Konsultasi hukum, pendampingan, dan edukasi hak hukum.',
                'sdg_categories' => [16, 10],
                'required_skills' => ['Hukum', 'Advokasi', 'Komunikasi', 'Sosial'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Fasilitasi Penyandang Disabilitas',
                'description' => 'Program aksesibilitas dan inklusi penyandang disabilitas.',
                'background' => 'Penyandang disabilitas menghadapi banyak hambatan.',
                'objectives' => 'Mewujudkan desa ramah disabilitas.',
                'scope' => 'Adaptasi fasilitas publik, pelatihan keterampilan, dan advokasi.',
                'sdg_categories' => [10, 11],
                'required_skills' => ['Sosial', 'Arsitektur', 'Pendidikan Khusus', 'Advokasi'],
                'difficulty_level' => 'advanced',
            ],

            // =============================================
            // SDG 11: KOTA DAN KOMUNITAS BERKELANJUTAN (5 templates)
            // =============================================
            [
                'title' => 'Pengelolaan Sampah Ramah Lingkungan',
                'description' => 'Sistem pengelolaan sampah terpadu 3R (Reduce, Reuse, Recycle).',
                'background' => 'Sampah menumpuk dan mencemari lingkungan.',
                'objectives' => 'Mengurangi timbunan sampah dan pencemaran.',
                'scope' => 'Bank sampah, composting, dan pemilahan sampah.',
                'sdg_categories' => [11, 12],
                'required_skills' => ['Lingkungan', 'Teknik', 'Sosial', 'Manajemen'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Penataan Permukiman Kumuh',
                'description' => 'Program peningkatan kualitas permukiman kumuh.',
                'background' => 'Permukiman kumuh tidak layak huni.',
                'objectives' => 'Mewujudkan hunian yang layak dan sehat.',
                'scope' => 'Perbaikan rumah, drainase, dan fasilitas umum.',
                'sdg_categories' => [11, 1],
                'required_skills' => ['Arsitektur', 'Teknik Sipil', 'Perencanaan Kota', 'Sosial'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Taman Hijau Dan Ruang Terbuka Publik',
                'description' => 'Pembangunan taman dan ruang publik untuk warga.',
                'background' => 'Kurangnya ruang terbuka hijau di permukiman.',
                'objectives' => 'Menyediakan ruang publik yang nyaman.',
                'scope' => 'Perencanaan, pembangunan, dan pengelolaan taman.',
                'sdg_categories' => [11, 13],
                'required_skills' => ['Arsitektur Lanskap', 'Perencanaan', 'Pertamanan'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Sistem Transportasi Ramah Lingkungan',
                'description' => 'Promosi transportasi umum dan sepeda sebagai mobilitas hijau.',
                'background' => 'Polusi kendaraan bermotor meningkat.',
                'objectives' => 'Mengurangi emisi dari transportasi.',
                'scope' => 'Jalur sepeda, bike sharing, dan kampanye transportasi publik.',
                'sdg_categories' => [11, 13],
                'required_skills' => ['Transportasi', 'Perencanaan Kota', 'Lingkungan'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Mitigasi Bencana Berbasis Masyarakat',
                'description' => 'Peningkatan kapasitas masyarakat menghadapi bencana.',
                'background' => 'Desa rawan bencana tapi kesiapsiagaan rendah.',
                'objectives' => 'Meningkatkan ketahanan terhadap bencana.',
                'scope' => 'Pelatihan tanggap darurat, early warning system, dan drill.',
                'sdg_categories' => [13, 11],
                'required_skills' => ['Kebencanaan', 'Komunikasi', 'Manajemen Krisis'],
                'difficulty_level' => 'advanced',
            ],

            // =============================================
            // SDG 12: KONSUMSI DAN PRODUKSI BERTANGGUNG JAWAB (5 templates)
            // =============================================
            [
                'title' => 'Produk Lokal Berkelanjutan',
                'description' => 'Promosi dan pengembangan produk lokal yang berkelanjutan.',
                'background' => 'Produk lokal kalah bersaing dengan produk impor.',
                'objectives' => 'Meningkatkan konsumsi produk lokal.',
                'scope' => 'Branding, packaging, dan kampanye cinta produk lokal.',
                'sdg_categories' => [12, 8],
                'required_skills' => ['Marketing', 'Desain', 'Bisnis', 'Komunikasi'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Kampanye Zero Waste',
                'description' => 'Gerakan gaya hidup minim sampah di komunitas.',
                'background' => 'Produksi sampah terus meningkat.',
                'objectives' => 'Mengurangi sampah dari sumbernya.',
                'scope' => 'Edukasi, workshop zero waste lifestyle, dan kampanye.',
                'sdg_categories' => [12, 11],
                'required_skills' => ['Lingkungan', 'Komunikasi', 'Pendidikan', 'Sosial'],
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Pusat Daur Ulang Komunitas',
                'description' => 'Pembangunan pusat daur ulang sampah menjadi produk bernilai.',
                'background' => 'Sampah belum dimanfaatkan secara ekonomis.',
                'objectives' => 'Mengubah sampah menjadi produk bernilai ekonomi.',
                'scope' => 'Pelatihan daur ulang, produksi, dan pemasaran.',
                'sdg_categories' => [11, 12],
                'required_skills' => ['Lingkungan', 'Desain Produk', 'Marketing', 'Manajemen'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Packaging Ramah Lingkungan',
                'description' => 'Penggunaan kemasan ramah lingkungan untuk produk UMKM.',
                'background' => 'Kemasan plastik mencemari lingkungan.',
                'objectives' => 'Mengurangi penggunaan plastik sekali pakai.',
                'scope' => 'Pelatihan, penyediaan kemasan alternatif, dan advokasi.',
                'sdg_categories' => [12, 8],
                'required_skills' => ['Desain', 'Lingkungan', 'Bisnis', 'Teknologi'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Fashion Berkelanjutan',
                'description' => 'Program fashion dari bahan daur ulang dan ramah lingkungan.',
                'background' => 'Industri fashion berkontribusi pada pencemaran.',
                'objectives' => 'Mengembangkan fashion yang sustainable.',
                'scope' => 'Pelatihan desain, produksi, dan pemasaran fashion berkelanjutan.',
                'sdg_categories' => [12, 13],
                'required_skills' => ['Desain Fashion', 'Marketing', 'Lingkungan', 'Bisnis'],
                'difficulty_level' => 'advanced',
            ],

            // =============================================
            // SDG 13: PENANGANAN PERUBAHAN IKLIM (5 templates)
            // =============================================
            [
                'title' => 'Kampung Iklim Pintar',
                'description' => 'Program desa tangguh iklim dengan adaptasi dan mitigasi.',
                'background' => 'Perubahan iklim berdampak pada kehidupan masyarakat.',
                'objectives' => 'Meningkatkan ketahanan terhadap perubahan iklim.',
                'scope' => 'Penanaman pohon, konservasi air, dan edukasi iklim.',
                'sdg_categories' => [13, 7],
                'required_skills' => ['Lingkungan', 'Klimatologi', 'Pertanian', 'Komunikasi'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Penghijauan Dan Konservasi Lingkungan',
                'description' => 'Program penanaman pohon dan pelestarian hutan.',
                'background' => 'Deforestasi menyebabkan perubahan iklim.',
                'objectives' => 'Meningkatkan tutupan hijau dan carbon sequestration.',
                'scope' => 'Penanaman pohon, nursery, dan monitoring pertumbuhan.',
                'sdg_categories' => [13, 15],
                'required_skills' => ['Kehutanan', 'Lingkungan', 'Biologi', 'Sosial'],
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Monitoring Kualitas Udara',
                'description' => 'Pemantauan kualitas udara dengan sensor IoT.',
                'background' => 'Polusi udara meningkat di perkotaan.',
                'objectives' => 'Menyediakan data kualitas udara real-time.',
                'scope' => 'Instalasi sensor, dashboard monitoring, dan edukasi.',
                'sdg_categories' => [13, 3],
                'required_skills' => ['Teknik Lingkungan', 'IoT', 'Data Science', 'Kesehatan'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Adaptasi Perubahan Iklim Pertanian',
                'description' => 'Strategi adaptasi pertanian menghadapi perubahan iklim.',
                'background' => 'Pola tanam terganggu akibat perubahan iklim.',
                'objectives' => 'Membantu petani beradaptasi dengan perubahan iklim.',
                'scope' => 'Pola tanam adaptif, varietas tahan iklim, dan asuransi pertanian.',
                'sdg_categories' => [13, 2],
                'required_skills' => ['Pertanian', 'Klimatologi', 'Ekonomi', 'Penyuluhan'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Pengurangan Emisi Gas Rumah Kaca',
                'description' => 'Program pengurangan emisi melalui efisiensi energi.',
                'background' => 'Emisi GRK terus meningkat.',
                'objectives' => 'Mengurangi jejak karbon komunitas.',
                'scope' => 'Audit karbon, efisiensi energi, dan carbon offset.',
                'sdg_categories' => [13, 7],
                'required_skills' => ['Lingkungan', 'Energi', 'Data', 'Manajemen'],
                'difficulty_level' => 'advanced',
            ],

            // =============================================
            // SDG 14: EKOSISTEM LAUT (2 templates)
            // =============================================
            [
                'title' => 'Konservasi Terumbu Karang',
                'description' => 'Program pelestarian dan rehabilitasi terumbu karang.',
                'background' => 'Terumbu karang rusak akibat aktivitas manusia.',
                'objectives' => 'Memulihkan ekosistem terumbu karang.',
                'scope' => 'Transplantasi karang, monitoring, dan edukasi nelayan.',
                'sdg_categories' => [14],
                'required_skills' => ['Kelautan', 'Biologi Laut', 'Diving', 'Konservasi'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Pengelolaan Sampah Laut',
                'description' => 'Program pembersihan dan pencegahan sampah di laut.',
                'background' => 'Sampah plastik mencemari laut.',
                'objectives' => 'Mengurangi sampah laut dan melindungi biota.',
                'scope' => 'Beach clean up, edukasi, dan pengurangan plastik.',
                'sdg_categories' => [14, 12],
                'required_skills' => ['Lingkungan', 'Kelautan', 'Sosial', 'Komunikasi'],
                'difficulty_level' => 'beginner',
            ],

            // =============================================
            // SDG 15: EKOSISTEM DARATAN (2 templates)
            // =============================================
            [
                'title' => 'Rehabilitasi Lahan Kritis',
                'description' => 'Program pemulihan lahan kritis melalui reboisasi.',
                'background' => 'Lahan kritis menyebabkan erosi dan banjir.',
                'objectives' => 'Memulihkan fungsi lahan dan mencegah bencana.',
                'scope' => 'Penanaman, pemeliharaan, dan monitoring vegetasi.',
                'sdg_categories' => [15, 13],
                'required_skills' => ['Kehutanan', 'Lingkungan', 'Teknik Sipil', 'Sosial'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Perlindungan Satwa Liar',
                'description' => 'Program konservasi satwa liar dan habitatnya.',
                'background' => 'Populasi satwa liar menurun drastis.',
                'objectives' => 'Melindungi keanekaragaman hayati.',
                'scope' => 'Patroli, monitoring, dan edukasi konservasi.',
                'sdg_categories' => [15],
                'required_skills' => ['Biologi', 'Konservasi', 'Kehutanan', 'Hukum'],
                'difficulty_level' => 'advanced',
            ],

            // =============================================
            // SDG 16: PERDAMAIAN, KEADILAN DAN KELEMBAGAAN YANG KUAT (5 templates)
            // =============================================
            [
                'title' => 'Transparansi Dana Desa',
                'description' => 'Sistem transparansi pengelolaan anggaran desa.',
                'background' => 'Pengelolaan dana desa kurang transparan.',
                'objectives' => 'Meningkatkan akuntabilitas pemerintah desa.',
                'scope' => 'Portal transparansi, papan informasi, dan musyawarah.',
                'sdg_categories' => [16, 17],
                'required_skills' => ['Akuntansi', 'Teknologi Informasi', 'Hukum', 'Komunikasi'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Pencegahan Korupsi Desa',
                'description' => 'Program edukasi anti korupsi dan pengawasan partisipatif.',
                'background' => 'Kasus korupsi di desa masih terjadi.',
                'objectives' => 'Mencegah korupsi melalui transparansi dan partisipasi.',
                'scope' => 'Kampanye anti korupsi, pelatihan, dan pengawasan masyarakat.',
                'sdg_categories' => [16, 17],
                'required_skills' => ['Hukum', 'Akuntansi', 'Komunikasi', 'Advokasi'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Mediasi Konflik Komunitas',
                'description' => 'Program mediasi dan resolusi konflik berbasis komunitas.',
                'background' => 'Konflik horizontal masih sering terjadi.',
                'objectives' => 'Menyelesaikan konflik secara damai.',
                'scope' => 'Pelatihan mediator, fasilitasi dialog, dan kesepakatan damai.',
                'sdg_categories' => [16, 11],
                'required_skills' => ['Hukum', 'Psikologi', 'Komunikasi', 'Sosiologi'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Penguatan Kelembagaan Desa',
                'description' => 'Capacity building untuk perangkat dan lembaga desa.',
                'background' => 'Kapasitas kelembagaan desa masih lemah.',
                'objectives' => 'Meningkatkan kinerja pemerintahan desa.',
                'scope' => 'Pelatihan administrasi, manajemen, dan kepemimpinan.',
                'sdg_categories' => [16],
                'required_skills' => ['Administrasi Publik', 'Manajemen', 'Hukum', 'Kepemimpinan'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Revitalisasi Seni Dan Budaya Lokal',
                'description' => 'Pelestarian dan pengembangan seni budaya tradisional.',
                'background' => 'Seni budaya lokal mulai terlupakan.',
                'objectives' => 'Melestarikan identitas budaya lokal.',
                'scope' => 'Pelatihan seni, pertunjukan, dan dokumentasi.',
                'sdg_categories' => [4, 11],
                'required_skills' => ['Seni', 'Budaya', 'Pendidikan', 'Dokumentasi'],
                'difficulty_level' => 'beginner',
            ],

            // =============================================
            // SDG 17: KEMITRAAN UNTUK MENCAPAI TUJUAN (5 templates)
            // =============================================
            [
                'title' => 'Kemitraan Universitas-Desa',
                'description' => 'Program kolaborasi berkelanjutan universitas dengan desa.',
                'background' => 'Hasil penelitian universitas tidak sampai ke masyarakat.',
                'objectives' => 'Menjembatani akademisi dengan praktik di lapangan.',
                'scope' => 'Riset partisipatif, pengabdian, dan transfer teknologi.',
                'sdg_categories' => [17, 4],
                'required_skills' => ['Manajemen', 'Penelitian', 'Komunikasi', 'Multidisiplin'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Forum Multi Stakeholder Desa',
                'description' => 'Pembentukan forum kolaborasi berbagai pemangku kepentingan.',
                'background' => 'Koordinasi antar stakeholder masih lemah.',
                'objectives' => 'Meningkatkan sinergi pembangunan desa.',
                'scope' => 'Pembentukan forum, pertemuan rutin, dan kolaborasi program.',
                'sdg_categories' => [17, 16],
                'required_skills' => ['Manajemen', 'Komunikasi', 'Fasilitasi', 'Networking'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Platform Kolaborasi Digital',
                'description' => 'Sistem digital untuk kolaborasi dan berbagi sumber daya.',
                'background' => 'Koordinasi program masih konvensional.',
                'objectives' => 'Memudahkan kolaborasi melalui platform digital.',
                'scope' => 'Pengembangan platform, onboarding, dan pengelolaan.',
                'sdg_categories' => [17, 9],
                'required_skills' => ['Teknologi Informasi', 'Manajemen', 'Desain', 'Komunikasi'],
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Pendampingan Berkelanjutan',
                'description' => 'Program mentoring jangka panjang untuk keberlanjutan program.',
                'background' => 'Program KKN sering berhenti setelah mahasiswa pulang.',
                'objectives' => 'Memastikan keberlanjutan program pasca KKN.',
                'scope' => 'Mentoring online, monitoring, dan dukungan teknis.',
                'sdg_categories' => [17],
                'required_skills' => ['Manajemen Program', 'Mentoring', 'Komunikasi', 'Monitoring'],
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Database Kolaboratif',
                'description' => 'Sistem database terpadu untuk berbagi data dan informasi.',
                'background' => 'Data pembangunan desa tersebar dan tidak terintegrasi.',
                'objectives' => 'Menyediakan data terpadu untuk perencanaan.',
                'scope' => 'Pengembangan database, digitalisasi data, dan pelatihan.',
                'sdg_categories' => [17, 9],
                'required_skills' => ['Data Science', 'Teknologi Informasi', 'Statistik', 'Manajemen'],
                'difficulty_level' => 'advanced',
            ],
        ];
    }
}