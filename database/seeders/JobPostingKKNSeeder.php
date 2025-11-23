<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SupabaseService;
use Illuminate\Support\Str;

/**
 * JOB POSTING KKN SEEDER
 *
 * Seed realistic field-based KKN job postings dengan requirements:
 * - Skills dari portfolio
 * - SDG alignment
 * - Project evidence
 * - Impact-driven criteria
 *
 * Berdasarkan PDF: KUMPUL IDE KKNGO
 * Sub-tema: Teknologi, Kesehatan, Pendidikan, Lingkungan, Sosial Budaya
 */
class JobPostingKKNSeeder extends Seeder
{
    protected $supabase;

    public function __construct()
    {
        $this->supabase = new SupabaseService();
    }

    public function run(): void
    {
        $this->command->info('🌱 Seeding realistic KKN-based job postings...');

        // Get all companies - use DB facade
        $companies = \DB::table('companies')->select('id', 'name', 'industry')->get();

        // Get all job categories - use DB facade
        $categories = \DB::table('job_categories')->select('id', 'name', 'slug')->get();
        $categoriesMap = collect($categories)->keyBy('slug')->toArray();

        $jobCount = 0;
        $skippedCount = 0;
        foreach ($companies as $index => $company) {
            // Reconnect every 20 iterations
            if ($index % 20 == 0) {
                \DB::reconnect('pgsql');
            }

            // Check existing job count for this company - use DB facade
            $existingJobs = \DB::table('job_postings')
                ->select('id', 'job_category_id')
                ->where('company_id', $company->id)
                ->get();
            $existingJobCount = count($existingJobs);

            // Skip if company already has enough jobs
            if ($existingJobCount >= 4) {
                $skippedCount++;
                continue;
            }

            // Create jobs only if needed (to reach 4-6 total, ensuring >3 requirement)
            $targetJobs = rand(4, 6);
            $jobsToCreate = max(0, $targetJobs - $existingJobCount);

            // Track used categories to ensure >3 different categories
            $usedCategoryIds = collect($existingJobs)->pluck('job_category_id')->unique()->toArray();

            for ($i = 0; $i < $jobsToCreate; $i++) {
                $job = $this->generateKKNJob($company, $categoriesMap, $usedCategoryIds);
                if ($job) {
                    try {
                        \DB::table('job_postings')->insert($job);
                        $usedCategoryIds[] = $job['job_category_id'];
                        $jobCount++;

                        if ($jobCount % 20 == 0) {
                            $this->command->info("📝 Created $jobCount job postings...");
                        }
                    } catch (\Exception $e) {
                        $this->command->error("❌ Failed to insert job: {$job['title']} - " . $e->getMessage());
                    }
                }
            }
        }

        if ($skippedCount > 0) {
            $this->command->warn("⚠️  Skipped $skippedCount companies that already have enough jobs");
        }

        $this->command->info('');
        $this->command->info('🎉 JobPostingKKNSeeder completed!');
        $this->command->info("📊 Total job postings: $jobCount");
    }

    private function generateKKNJob($company, $categoriesMap, $usedCategoryIds = []): ?array
    {
        // Select random KKN job template, prioritizing unused categories
        $jobTemplates = $this->getKKNJobTemplates();

        // Shuffle templates to randomize
        shuffle($jobTemplates);

        // Try to find a template with an unused category first
        $template = null;
        foreach ($jobTemplates as $t) {
            $cat = $categoriesMap[$t['category_slug']] ?? null;
            if ($cat && !in_array($cat->id, $usedCategoryIds)) {
                $template = $t;
                break;
            }
        }

        // If all categories are used, just pick random
        if (!$template) {
            $template = $jobTemplates[array_rand($jobTemplates)];
        }

        // Get category
        $category = $categoriesMap[$template['category_slug']] ?? null;
        if (!$category) {
            return null;
        }

        $title = $template['title'];
        $slug = Str::slug($title . '-' . $company->name . '-' . rand(100, 999));

        // Random location di Indonesia
        $locations = [
            'Jakarta Utara', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Pusat',
            'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Medan', 'Makassar', 'Palembang',
            'Depok', 'Tangerang', 'Bekasi', 'Bogor', 'Cirebon', 'Malang', 'Solo', 'Denpasar',
            'Desa Karanganyar, Jawa Barat', 'Desa Cijambe, Jawa Barat', 'Desa Cigadung, Bandung',
            'Kelurahan Antapani, Bandung', 'Kecamatan Coblong, Bandung', 'Remote/Daring',
        ];

        $jobType = $template['job_type'] ?? 'Internship';
        $salaryMin = $template['salary_min'] ?? 0;
        $salaryMax = $template['salary_max'] ?? 0;

        return [
            'company_id' => $company->id,
            'job_category_id' => $category->id,
            'title' => $title,
            'slug' => $slug,
            'department' => $template['department'] ?? 'Community Development',
            'location' => $locations[array_rand($locations)],
            'job_type' => $jobType,
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMax,
            'salary_currency' => 'IDR',
            'salary_period' => $template['salary_period'] ?? 'monthly',
            'description' => $template['description'],
            'responsibilities' => $template['responsibilities'],
            'qualifications' => $template['qualifications'],
            'benefits' => $template['benefits'] ?? "- Sertifikat KKN digital\n- Pengalaman lapangan nyata\n- Networking dengan profesional\n- Impact measurement report\n- Rekomendasi dari instansi",
            'skills' => json_encode($template['skills']),
            'sdg_alignment' => json_encode($template['sdg_goals']),
            'impact_metrics' => $template['impact_metrics'] ?? null,
            'success_criteria' => $template['success_criteria'] ?? null,
            'status' => $this->randomStatus(),
            'allow_guest_applications' => false,
            'views_count' => rand(0, 500),
            'applications_count' => rand(0, 50),
            'published_at' => now()->subDays(rand(1, 90))->toDateTimeString(),
            'expires_at' => now()->addDays(rand(30, 120))->toDateTimeString(),
            'created_at' => now()->subDays(rand(1, 90))->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];
    }

    private function randomStatus(): string
    {
        $statuses = ['draft', 'posted', 'posted', 'posted', 'posted', 'closed'];
        return $statuses[array_rand($statuses)];
    }

    private function getKKNJobTemplates(): array
    {
        return [
            // ===============================================
            // TEKNOLOGI & DIGITAL TRANSFORMATION
            // ===============================================
            [
                'category_slug' => 'digital-transformation',
                'title' => 'Field Officer - Digitalisasi UMKM Desa',
                'job_type' => 'Internship',
                'salary_min' => 2000000,
                'salary_max' => 3500000,
                'salary_period' => 'monthly',
                'department' => 'Digital Transformation',
                'description' => "Program KKN untuk mendampingi UMKM desa dalam transformasi digital. Anda akan bekerja langsung dengan pelaku UMKM untuk onboarding ke marketplace, pembuatan konten digital, dan edukasi digital marketing.\n\nProgram ini sejalan dengan SDG 8 (Decent Work & Economic Growth) dan SDG 9 (Industry, Innovation & Infrastructure).",
                'responsibilities' => "- Melakukan survei dan pemetaan UMKM di wilayah penugasan\n- Mendampingi UMKM untuk onboarding ke marketplace (Tokopedia, Shopee, Bukalapak)\n- Membantu pembuatan konten produk (foto, video, copywriting)\n- Memberikan pelatihan digital marketing dasar kepada pelaku UMKM\n- Membuat laporan progress mingguan dengan impact metrics\n- Dokumentasi best practices dan success stories",
                'qualifications' => "WAJIB:\n- Mahasiswa aktif minimal semester 5\n- Portofolio menunjukkan pengalaman e-commerce/digital marketing\n- Skill fotografi produk dan video editing dasar\n- Mampu mengoperasikan Google Suite dan social media platforms\n- Bersedia turun ke lapangan dan mobile\n- Komunikatif dan sabar dalam mengajar\n\nDINILAI PLUS:\n- Pernah mengikuti proyek KKN/pengabdian masyarakat sebelumnya\n- Portfolio website development atau content creation\n- Pengalaman mengelola social media bisnis\n- Impact score tinggi di platform Karsa",
                'skills' => ['Digital Marketing', 'Content Creation', 'E-Commerce', 'Community Training', 'Google Workspace', 'Social Media Management'],
                'sdg_goals' => [8, 9, 10],
                'impact_metrics' => 'Target: 20 UMKM ter-onboard ke marketplace, 50 produk ter-dokumentasi, 5 UMKM mendapat penjualan online pertama',
                'success_criteria' => 'Minimal 15 UMKM berhasil memiliki toko online aktif dengan minimal 3 produk terdokumentasi dengan baik',
            ],

            [
                'category_slug' => 'web-development',
                'title' => 'Web Developer Volunteer - Website Desa/Pemerintahan',
                'job_type' => 'Internship',
                'salary_min' => 3000000,
                'salary_max' => 5000000,
                'salary_period' => 'project',
                'department' => 'Technology',
                'description' => "Membuat website untuk instansi pemerintah desa/kelurahan dengan fokus pada transparansi, pelayanan publik, dan informasi desa. Website harus responsive, accessible, dan mudah dikelola oleh perangkat desa.",
                'responsibilities' => "- Requirement gathering dengan perangkat desa/kelurahan\n- Design dan develop website dengan CMS yang user-friendly\n- Implement fitur: profil desa, layanan publik, transparansi anggaran, pengumuman\n- Training kepada admin desa untuk pengelolaan konten\n- Dokumentasi teknis dan user manual\n- Maintenance support selama 3 bulan",
                'qualifications' => "WAJIB:\n- Portfolio minimal 2 website yang pernah dibuat\n- Skill: HTML, CSS, JavaScript, PHP/Laravel atau WordPress\n- Pengalaman dengan responsive design dan accessibility standards\n- Mampu membuat dokumentasi teknis yang jelas\n\nDINILAI PLUS:\n- Pernah mengembangkan website pemerintah/NGO\n- Pengalaman dengan Laravel Livewire atau modern JS framework\n- Portfolio menunjukkan perhatian pada UI/UX dan accessibility\n- Pernah memberikan training/workshop web development",
                'skills' => ['Laravel', 'PHP', 'JavaScript', 'HTML/CSS', 'WordPress', 'UI/UX Design', 'Web Accessibility', 'Technical Documentation'],
                'sdg_goals' => [9, 16],
                'impact_metrics' => 'Target: 1 website desa live dengan minimal 20 halaman konten, 3 perangkat desa terlatih mengelola website',
                'success_criteria' => 'Website dapat diakses publik, memenuhi standar accessibility WCAG 2.1 Level A, dan admin desa mampu update konten mandiri',
            ],

            [
                'category_slug' => 'mobile-app-development',
                'title' => 'Mobile App Developer - Aplikasi Sosial/Edukasi',
                'job_type' => 'Contract',
                'salary_min' => 4000000,
                'salary_max' => 7000000,
                'salary_period' => 'project',
                'department' => 'Technology',
                'description' => "Develop mobile application untuk solusi masalah sosial di bidang edukasi, kesehatan, atau lingkungan. Aplikasi harus user-friendly, offline-capable, dan measurable impact.",
                'responsibilities' => "- Research kebutuhan pengguna melalui survei lapangan\n- Design dan develop mobile app (Android/iOS)\n- Implement analytics untuk track usage dan impact\n- User testing dengan target beneficiaries\n- Deploy ke Play Store/App Store\n- Create user guide dan tutorial video",
                'qualifications' => "WAJIB:\n- Portfolio minimal 1 aplikasi mobile yang sudah live di store\n- Skill: Flutter/React Native atau native Android/iOS\n- Pengalaman dengan Firebase atau backend integration\n- Kemampuan UI/UX design untuk mobile\n\nDINILAI PLUS:\n- Aplikasi portfolio menunjukkan social impact\n- Pengalaman offline-first app development\n- Portfolio menunjukkan perhatian pada accessibility\n- Pernah melakukan user research/testing",
                'skills' => ['Flutter', 'React Native', 'Firebase', 'Mobile UI/UX', 'API Integration', 'User Testing', 'Analytics Implementation'],
                'sdg_goals' => [3, 4, 9],
                'impact_metrics' => 'Target: Aplikasi dengan minimal 100 downloads dalam 2 bulan, retention rate >30%, measurable impact indicators',
                'success_criteria' => 'Aplikasi live di store dengan rating minimal 4.0, memiliki active users, dan terbukti menyelesaikan masalah sosial tertentu',
            ],

            // ===============================================
            // KESEHATAN & WELLBEING
            // ===============================================
            [
                'category_slug' => 'public-health',
                'title' => 'Community Health Worker - Posyandu & Edukasi Gizi',
                'job_type' => 'Internship',
                'salary_min' => 1500000,
                'salary_max' => 2500000,
                'salary_period' => 'monthly',
                'department' => 'Public Health',
                'description' => "Program KKN kesehatan masyarakat untuk mendampingi Posyandu, edukasi gizi balita dan ibu hamil, serta kampanye kesehatan preventif di desa/kelurahan. Fokus pada pencegahan stunting dan peningkatan kesadaran gizi.",
                'responsibilities' => "- Membantu kegiatan Posyandu bulanan (penimbangan, imunisasi, konseling)\n- Edukasi gizi kepada ibu hamil dan ibu menyusui\n- Kampanye PHBS (Perilaku Hidup Bersih dan Sehat) ke rumah-rumah\n- Pendataan status gizi balita dan ibu hamil\n- Membuat materi edukasi visual (poster, infografis, video)\n- Laporan monitoring dan evaluasi program",
                'qualifications' => "WAJIB:\n- Mahasiswa Kesehatan Masyarakat, Gizi, Keperawatan, atau Kedokteran\n- Pemahaman dasar tentang stunting, gizi balita, dan kesehatan ibu-anak\n- Skill komunikasi dan edukasi kesehatan kepada masyarakat awam\n- Empati tinggi dan sabar dalam berinteraksi dengan ibu-ibu\n\nDINILAI PLUS:\n- Pengalaman di Posyandu/Puskesmas sebelumnya\n- Portfolio kampanye kesehatan atau edukasi masyarakat\n- Skill desain untuk membuat materi edukasi visual\n- Sertifikat pelatihan kesehatan masyarakat",
                'skills' => ['Public Health', 'Nutrition Education', 'Community Outreach', 'Health Counseling', 'Data Collection', 'Visual Communication'],
                'sdg_goals' => [2, 3],
                'impact_metrics' => 'Target: 50 balita terdata status gizinya, 30 ibu mendapat konseling gizi, 100 rumah dikunjungi untuk kampanye PHBS',
                'success_criteria' => 'Minimal 80% balita yang terdata memiliki status gizi tercatat dengan benar, dan minimal 20 ibu menunjukkan peningkatan pengetahuan gizi',
            ],

            [
                'category_slug' => 'health-technology',
                'title' => 'HealthTech Developer - Sistem Informasi Kesehatan Desa',
                'job_type' => 'Contract',
                'salary_min' => 3500000,
                'salary_max' => 6000000,
                'salary_period' => 'project',
                'department' => 'Health Technology',
                'description' => "Develop sistem informasi kesehatan untuk Posyandu/Puskesmas: pencatatan balita, jadwal imunisasi, reminder ibu hamil, dan dashboard monitoring stunting. Sistem harus simple, mobile-friendly, dan bisa digunakan oleh kader Posyandu.",
                'responsibilities' => "- Analisis kebutuhan sistem dengan Puskesmas dan kader Posyandu\n- Design database untuk data kesehatan balita dan ibu hamil\n- Develop web application dengan dashboard monitoring\n- Implement reminder system (WhatsApp/SMS) untuk jadwal Posyandu\n- Training kader Posyandu untuk input data\n- Dokumentasi dan handover kepada Puskesmas",
                'qualifications' => "WAJIB:\n- Portfolio web application dengan database management\n- Skill: Laravel/PHP, MySQL/PostgreSQL, Bootstrap/Tailwind CSS\n- Pemahaman tentang data privacy dan keamanan data kesehatan\n- Mampu membuat UI yang sederhana untuk user non-tech\n\nDINILAI PLUS:\n- Pengalaman healthcare/medical system\n- Skill integration dengan WhatsApp API atau SMS gateway\n- Portfolio dashboard/data visualization\n- Pemahaman tentang standar data kesehatan Indonesia",
                'skills' => ['Laravel', 'PHP', 'MySQL', 'Dashboard Development', 'Data Privacy', 'API Integration', 'User Training'],
                'sdg_goals' => [3, 9],
                'impact_metrics' => 'Sistem live dengan minimal 100 data balita terinput, 50 reminder terkirim otomatis, dan 5 kader terlatih menggunakan sistem',
                'success_criteria' => 'Sistem operasional di minimal 1 Posyandu dengan data akurat dan kader mampu input data mandiri',
            ],

            // ===============================================
            // PENDIDIKAN & LITERASI
            // ===============================================
            [
                'category_slug' => 'teaching-tutoring',
                'title' => 'Volunteer Teacher - Mengajar di Daerah Terpencil',
                'job_type' => 'Internship',
                'salary_min' => 1500000,
                'salary_max' => 2500000,
                'salary_period' => 'monthly',
                'department' => 'Education',
                'description' => "Program mengajar di SD/SMP daerah terpencil atau kurang mampu. Fokus pada peningkatan literasi, numerasi, dan motivasi belajar siswa. Mirip dengan Indonesia Mengajar.",
                'responsibilities' => "- Mengajar di kelas (matematika, bahasa Indonesia, IPA, atau bahasa Inggris)\n- Membuat lesson plan kreatif dan engaging\n- Bimbingan belajar setelah sekolah untuk siswa yang tertinggal\n- Membuat media pembelajaran interaktif\n- Mengorganisir kegiatan ekstrakurikuler (literasi, seni, olahraga)\n- Laporan perkembangan belajar siswa",
                'qualifications' => "WAJIB:\n- Mahasiswa semester 5 ke atas dari jurusan apapun\n- Passion dalam mengajar dan berinteraksi dengan anak-anak\n- Kreatif dalam membuat metode pembelajaran\n- Sabar, empati, dan adaptif dengan lingkungan baru\n- Bersedia tinggal di lokasi penugasan (jika diperlukan)\n\nDINILAI PLUS:\n- Pengalaman mengajar/mentoring sebelumnya\n- Portfolio kegiatan sosial atau volunteer\n- Skill public speaking atau storytelling\n- Kemampuan bahasa daerah setempat",
                'skills' => ['Teaching', 'Lesson Planning', 'Educational Content Creation', 'Child Psychology', 'Classroom Management', 'Public Speaking'],
                'sdg_goals' => [4, 10],
                'impact_metrics' => 'Target: Mengajar minimal 30 siswa, minimal 20 siswa menunjukkan peningkatan nilai, membuat 10 lesson plan kreatif',
                'success_criteria' => 'Siswa menunjukkan peningkatan motivasi belajar dan nilai akademik, serta feedback positif dari guru dan sekolah',
            ],

            [
                'category_slug' => 'educational-technology',
                'title' => 'EdTech Developer - Platform Pembelajaran Desa/Sekolah',
                'job_type' => 'Contract',
                'salary_min' => 4000000,
                'salary_max' => 7000000,
                'salary_period' => 'project',
                'department' => 'Educational Technology',
                'description' => "Membuat platform e-learning sederhana untuk sekolah atau komunitas belajar desa. Platform harus bisa diakses offline, mobile-friendly, dan mudah digunakan oleh guru dan siswa dengan literasi digital terbatas.",
                'responsibilities' => "- Requirement gathering dengan guru dan siswa\n- Design dan develop e-learning platform (web/mobile)\n- Implement fitur: video lessons, quiz, assignment, discussion forum\n- Upload konten pembelajaran awal (minimal 20 materi)\n- Training guru untuk mengelola platform dan membuat konten\n- Monitoring usage dan feedback collection",
                'qualifications' => "WAJIB:\n- Portfolio e-learning atau educational platform\n- Skill: Laravel/Next.js, video streaming, quiz system\n- Pengalaman dengan offline-first design (PWA/caching)\n- Kemampuan UI/UX untuk platform edukasi\n\nDINILAI PLUS:\n- Pengalaman develop Learning Management System (LMS)\n- Portfolio menunjukkan perhatian pada accessibility\n- Skill video editing untuk membuat tutorial\n- Pemahaman tentang pedagogi online learning",
                'skills' => ['Laravel', 'Next.js', 'LMS Development', 'Video Streaming', 'Progressive Web App', 'Educational UX', 'Content Management'],
                'sdg_goals' => [4, 9],
                'impact_metrics' => 'Platform live dengan minimal 20 materi, 50 siswa terdaftar, 10 guru terlatih membuat konten',
                'success_criteria' => 'Platform digunakan aktif oleh minimal 30 siswa dengan engagement rate >50% dan feedback positif dari guru',
            ],

            [
                'category_slug' => 'literacy-programs',
                'title' => 'Literacy Program Coordinator - Perpustakaan Desa/Taman Bacaan',
                'job_type' => 'Internship',
                'salary_min' => 1500000,
                'salary_max' => 2000000,
                'salary_period' => 'monthly',
                'department' => 'Community Education',
                'description' => "Mengelola dan mengaktifkan perpustakaan desa atau mendirikan taman bacaan masyarakat. Fokus pada peningkatan minat baca anak dan literasi digital masyarakat.",
                'responsibilities' => "- Menata dan mengkatalogkan buku di perpustakaan desa\n- Mengorganisir kegiatan reading club untuk anak-anak\n- Mengadakan storytelling session rutin\n- Membuat program literasi digital (internet sehat, hoax detection)\n- Kerjasama dengan sekolah untuk kunjungan perpustakaan\n- Laporan statistik peminjaman dan aktivitas perpustakaan",
                'qualifications' => "WAJIB:\n- Gemar membaca dan passionate tentang literasi\n- Skill organizing dan event management\n- Kreatif dalam membuat kegiatan literasi yang menarik\n- Komunikatif dan bisa berinteraksi dengan anak-anak\n\nDINILAI PLUS:\n- Pengalaman di perpustakaan atau taman bacaan\n- Skill storytelling atau public speaking\n- Portfolio kegiatan literasi atau edukasi\n- Kemampuan desain untuk membuat poster promosi",
                'skills' => ['Library Management', 'Event Organization', 'Storytelling', 'Digital Literacy Education', 'Community Engagement', 'Content Curation'],
                'sdg_goals' => [4, 10],
                'impact_metrics' => 'Target: 200 buku terkatalog, 50 anak aktif mengikuti reading club, 10 kegiatan literasi terselenggara',
                'success_criteria' => 'Peningkatan jumlah peminjaman buku minimal 50%, dan minimal 30 anak rutin mengikuti kegiatan literasi',
            ],

            // ===============================================
            // LINGKUNGAN & SUSTAINABILITY
            // ===============================================
            [
                'category_slug' => 'waste-management',
                'title' => 'Waste Management Officer - Bank Sampah & Kampanye Zero Waste',
                'job_type' => 'Internship',
                'salary_min' => 1500000,
                'salary_max' => 2500000,
                'salary_period' => 'monthly',
                'department' => 'Environmental',
                'description' => "Mendirikan atau mengaktifkan bank sampah di desa/kelurahan, edukasi pemilahan sampah, dan kampanye reduce-reuse-recycle. Fokus pada perubahan perilaku masyarakat terhadap sampah.",
                'responsibilities' => "- Sosialisasi bank sampah dan pemilahan sampah ke warga\n- Membantu operasional bank sampah (timbang, catat, kelola)\n- Kampanye reduce-reuse-recycle door-to-door\n- Membuat materi edukasi tentang bahaya sampah plastik\n- Mengorganisir kegiatan eco-enzyme atau composting workshop\n- Laporan impact: jumlah sampah tertangani, warga partisipan",
                'qualifications' => "WAJIB:\n- Passionate tentang lingkungan dan sustainability\n- Komunikatif dan persuasif dalam mengedukasi warga\n- Teliti dalam pencatatan dan administrasi bank sampah\n- Tidak takut kotor dan bersedia hands-on dengan sampah\n\nDINILAI PLUS:\n- Pengalaman di komunitas lingkungan atau zero waste\n- Portfolio kampanye lingkungan atau sustainability\n- Skill fotografi/videografi untuk dokumentasi\n- Pemahaman tentang circular economy",
                'skills' => ['Waste Management', 'Community Education', 'Environmental Advocacy', 'Data Recording', 'Event Organization', 'Sustainability Practices'],
                'sdg_goals' => [11, 12, 13],
                'impact_metrics' => 'Target: 50 rumah aktif memilah sampah, 100kg sampah per bulan masuk bank sampah, 5 kegiatan edukasi lingkungan',
                'success_criteria' => 'Bank sampah operasional dengan minimal 30 nasabah aktif dan peningkatan kesadaran pemilahan sampah di masyarakat',
            ],

            [
                'category_slug' => 'environmental-conservation',
                'title' => 'Conservation Volunteer - Reboisasi & Restorasi Ekosistem',
                'job_type' => 'Internship',
                'salary_min' => 1000000,
                'salary_max' => 2000000,
                'salary_period' => 'monthly',
                'department' => 'Environmental',
                'description' => "Program konservasi lingkungan: penanaman pohon, restorasi hutan mangrove, atau konservasi sungai. Melibatkan masyarakat lokal dalam upaya pelestarian alam.",
                'responsibilities' => "- Koordinasi kegiatan penanaman pohon dengan masyarakat\n- Monitoring pertumbuhan pohon yang sudah ditanam\n- Edukasi pentingnya konservasi dan keanekaragaman hayati\n- Dokumentasi biodiversity lokal (flora dan fauna)\n- Membuat peta lokasi penanaman dan species distribution\n- Laporan impact: jumlah pohon, tingkat survival rate, partisipasi warga",
                'qualifications' => "WAJIB:\n- Mahasiswa Kehutanan, Biologi, Lingkungan, atau passionate environmentalist\n- Fisik kuat dan siap bekerja outdoor\n- Mampu mobilisasi dan mengajak masyarakat\n- Teliti dalam monitoring dan pendataan\n\nDINILAI PLUS:\n- Pengalaman di organisasi lingkungan atau conservation project\n- Skill identifikasi species (flora/fauna)\n- Portfolio dokumentasi alam atau environmental content\n- Kemampuan GIS atau mapping",
                'skills' => ['Environmental Conservation', 'Reforestation', 'Community Mobilization', 'Biodiversity Monitoring', 'Data Collection', 'GIS Mapping'],
                'sdg_goals' => [13, 15],
                'impact_metrics' => 'Target: 500 pohon ditanam dengan survival rate >70%, 100 warga terlibat, biodiversity mapping 1 area',
                'success_criteria' => 'Pohon yang ditanam tumbuh dengan baik, masyarakat aktif merawat, dan ada dokumentasi biodiversity yang lengkap',
            ],

            // ===============================================
            // SOSIAL & PEMBERDAYAAN MASYARAKAT
            // ===============================================
            [
                'category_slug' => 'umkm-empowerment',
                'title' => 'UMKM Mentor - Branding & Business Development',
                'job_type' => 'Internship',
                'salary_min' => 2000000,
                'salary_max' => 3500000,
                'salary_period' => 'monthly',
                'department' => 'Economic Empowerment',
                'description' => "Mendampingi UMKM lokal untuk mengembangkan branding, packaging, dan strategi bisnis. Membantu UMKM naik kelas dari produk rumahan menjadi produk yang marketable.",
                'responsibilities' => "- Assessment bisnis UMKM (produk, target market, kompetitor)\n- Membantu redesign packaging dan branding UMKM\n- Membuat business plan dan pricing strategy\n- Training digital marketing dan social media untuk UMKM\n- Membantu akses ke marketplace atau koperasi\n- Monitoring sales dan feedback pelanggan",
                'qualifications' => "WAJIB:\n- Portfolio branding atau business development projects\n- Skill: business analysis, marketing strategy, branding design\n- Komunikatif dan persuasif dalam mentoring UMKM\n- Sabar dan empati terhadap pelaku usaha kecil\n\nDINILAI PLUS:\n- Pengalaman berbisnis sendiri atau di startup\n- Portfolio packaging design atau brand identity\n- Skill desain grafis (Canva, Photoshop, Illustrator)\n- Pemahaman tentang marketplace dan e-commerce",
                'skills' => ['Business Development', 'Branding', 'Marketing Strategy', 'Packaging Design', 'Digital Marketing', 'Mentoring', 'Financial Planning'],
                'sdg_goals' => [1, 8, 10],
                'impact_metrics' => 'Target: 10 UMKM ter-mentoring, 5 UMKM rebranding packaging, 3 UMKM masuk marketplace, peningkatan omzet rata-rata 30%',
                'success_criteria' => 'UMKM menunjukkan peningkatan branding dan sales, serta mampu mengelola bisnis lebih terstruktur',
            ],

            [
                'category_slug' => 'community-development',
                'title' => 'Community Organizer - Pemberdayaan Karang Taruna/PKK',
                'job_type' => 'Internship',
                'salary_min' => 1500000,
                'salary_max' => 2500000,
                'salary_period' => 'monthly',
                'department' => 'Community Development',
                'description' => "Mengaktifkan dan memberdayakan Karang Taruna, PKK, atau organisasi masyarakat lainnya melalui program-program produktif: skill training, wirausaha sosial, atau kegiatan kemasyarakatan.",
                'responsibilities' => "- Mapping potensi dan kebutuhan komunitas\n- Fasilitasi pembentukan program kerja Karang Taruna/PKK\n- Mengorganisir skill training (handicraft, kuliner, digital skills)\n- Membantu akses modal usaha atau koperasi\n- Membuat kegiatan sosial untuk engagement komunitas\n- Dokumentasi dan laporan impact program",
                'qualifications' => "WAJIB:\n- Leadership dan organizational skills\n- Komunikatif dan mampu memfasilitasi diskusi kelompok\n- Kreatif dalam membuat program komunitas\n- Empati dan pemahaman tentang dinamika masyarakat\n\nDINILAI PLUS:\n- Pengalaman di organisasi kemahasiswaan atau community\n- Portfolio program sosial atau pemberdayaan masyarakat\n- Skill training/workshop facilitation\n- Jaringan dengan lembaga pemberi bantuan atau CSR",
                'skills' => ['Community Organizing', 'Facilitation', 'Program Management', 'Training & Development', 'Stakeholder Engagement', 'Impact Measurement'],
                'sdg_goals' => [1, 5, 10, 11],
                'impact_metrics' => 'Target: 30 anggota aktif Karang Taruna/PKK, 5 skill training terselenggara, 10 peserta mulai usaha baru',
                'success_criteria' => 'Organisasi masyarakat aktif dengan program yang berkelanjutan dan partisipasi warga yang meningkat',
            ],

            [
                'category_slug' => 'agriculture-food-security',
                'title' => 'AgriTech Field Officer - Urban Farming & Ketahanan Pangan',
                'job_type' => 'Internship',
                'salary_min' => 1500000,
                'salary_max' => 2500000,
                'salary_period' => 'monthly',
                'department' => 'Agriculture',
                'description' => "Program urban farming atau pertanian berkelanjutan di perkotaan/desa untuk meningkatkan ketahanan pangan. Edukasi hidroponik, vertical farming, atau organic farming.",
                'responsibilities' => "- Sosialisasi urban farming/hydroponics kepada warga\n- Workshop pembuatan instalasi hidroponik sederhana\n- Monitoring pertumbuhan tanaman dan troubleshooting\n- Edukasi organic farming dan kompos organik\n- Membuat panduan/SOP urban farming yang mudah diikuti\n- Dokumentasi harvest dan impact measurement",
                'qualifications' => "WAJIB:\n- Pemahaman dasar tentang pertanian atau hidroponik\n- Hands-on dan tidak takut kotor dengan tanah/pupuk\n- Sabar dalam mengajar dan troubleshooting\n- Kreatif dalam membuat solusi pertanian sederhana\n\nDINILAI PLUS:\n- Pengalaman urban farming atau berkebun sendiri\n- Portfolio pertanian atau sustainability project\n- Skill videografi untuk membuat tutorial\n- Pemahaman tentang AgriTech atau smart farming",
                'skills' => ['Urban Farming', 'Hydroponics', 'Organic Farming', 'Agriculture Training', 'Troubleshooting', 'Documentation'],
                'sdg_goals' => [2, 11, 12, 13],
                'impact_metrics' => 'Target: 20 rumah memulai urban farming, 50kg hasil panen total, 5 workshop terselenggara',
                'success_criteria' => 'Warga mampu urban farming mandiri, ada hasil panen yang bisa dikonsumsi atau dijual, dan awareness food security meningkat',
            ],

            // ===============================================
            // DATA & RESEARCH
            // ===============================================
            [
                'category_slug' => 'research-policy',
                'title' => 'Research Assistant - Riset Sosial & Policy Brief',
                'job_type' => 'Internship',
                'salary_min' => 2000000,
                'salary_max' => 3500000,
                'salary_period' => 'monthly',
                'department' => 'Research & Policy',
                'description' => "Melakukan riset sosial di lapangan untuk evidence-based policy recommendations kepada pemerintah desa/kota. Fokus pada isu kemiskinan, pendidikan, kesehatan, atau lingkungan.",
                'responsibilities' => "- Design methodology riset (survey, interview, FGD)\n- Pengumpulan data kuantitatif dan kualitatif di lapangan\n- Analisis data menggunakan tools statistik\n- Menulis policy brief dan recommendations\n- Presentasi hasil riset kepada stakeholder\n- Publikasi hasil riset (laporan, artikel, infografis)",
                'qualifications' => "WAJIB:\n- Mahasiswa/lulusan social science, statistics, atau public policy\n- Skill: research methodology, data analysis, academic writing\n- Pengalaman dengan tools: Excel, SPSS, R, atau Python\n- Analytical thinking dan attention to detail\n\nDINILAI PLUS:\n- Portfolio riset atau publikasi sebelumnya\n- Skill data visualization (Tableau, Power BI)\n- Pengalaman qualitative research (interview, FGD)\n- Pemahaman tentang policy-making process",
                'skills' => ['Research Methodology', 'Data Collection', 'Statistical Analysis', 'Policy Writing', 'Data Visualization', 'Stakeholder Presentation'],
                'sdg_goals' => [16, 17],
                'impact_metrics' => 'Target: 1 policy brief komprehensif, minimal 100 responden survey, 5 FGD terselenggara, presentasi ke pemda',
                'success_criteria' => 'Riset menghasilkan insights yang actionable, policy brief diterima positif oleh pemda, dan ada follow-up implementation',
            ],
        ];
    }
}
