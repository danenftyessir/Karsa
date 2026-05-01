<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SupabaseService;

class JobCategorySeeder extends Seeder
{
    protected $supabase;

    public function __construct()
    {
        $this->supabase = new SupabaseService();
    }

    /**
     * Run the database seeds.
     *
     * Kategori ini disesuaikan dengan tema KKN:
     * - Teknologi (SDG 9)
     * - Kesehatan (SDG 3)
     * - Pendidikan (SDG 4)
     * - Lingkungan (SDG 13, 14, 15)
     * - Sosial Budaya (SDG 1, 2, 10, 11)
     */
    public function run(): void
    {
        $categories = [
            // ============================================
            // TEKNOLOGI & INOVASI (SDG 9)
            // ============================================
            [
                'name' => 'Digital Transformation',
                'slug' => 'digital-transformation',
                'description' => 'Program transformasi digital untuk desa/komunitas, termasuk digitalisasi UMKM, smart village, dan teknologi tepat guna',
                'icon' => 'cpu',
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Pengembangan website untuk instansi pemerintah, UMKM, desa, dan organisasi sosial',
                'icon' => 'code',
            ],
            [
                'name' => 'Mobile App Development',
                'slug' => 'mobile-app-development',
                'description' => 'Pembuatan aplikasi mobile untuk solusi masalah sosial, edukasi, kesehatan, dan pemberdayaan masyarakat',
                'icon' => 'smartphone',
            ],
            [
                'name' => 'Data Analytics & AI',
                'slug' => 'data-analytics-ai',
                'description' => 'Analisis data untuk policy making, riset sosial, dan machine learning untuk prediksi masalah sosial',
                'icon' => 'bar-chart-2',
            ],
            [
                'name' => 'IoT & Smart Systems',
                'slug' => 'iot-smart-systems',
                'description' => 'Internet of Things untuk pertanian pintar, monitoring lingkungan, dan smart city solutions',
                'icon' => 'wifi',
            ],

            // ============================================
            // KESEHATAN (SDG 3)
            // ============================================
            [
                'name' => 'Public Health',
                'slug' => 'public-health',
                'description' => 'Program kesehatan masyarakat: posyandu, vaksinasi, edukasi gizi, dan pencegahan penyakit',
                'icon' => 'heart',
            ],
            [
                'name' => 'Health Technology',
                'slug' => 'health-technology',
                'description' => 'Teknologi kesehatan: telemedicine, health monitoring apps, dan digitalisasi rekam medis',
                'icon' => 'activity',
            ],
            [
                'name' => 'Mental Health & Wellbeing',
                'slug' => 'mental-health-wellbeing',
                'description' => 'Program kesehatan mental: konseling, peer support, dan awareness campaign',
                'icon' => 'smile',
            ],
            [
                'name' => 'Sanitation & Hygiene',
                'slug' => 'sanitation-hygiene',
                'description' => 'Program sanitasi dan kebersihan: air bersih, toilet sehat, dan edukasi PHBS',
                'icon' => 'droplet',
            ],

            // ============================================
            // PENDIDIKAN (SDG 4)
            // ============================================
            [
                'name' => 'Educational Technology',
                'slug' => 'educational-technology',
                'description' => 'E-learning, platform edukasi digital, dan tools pembelajaran interaktif',
                'icon' => 'monitor',
            ],
            [
                'name' => 'Teaching & Tutoring',
                'slug' => 'teaching-tutoring',
                'description' => 'Mengajar di sekolah/komunitas, les gratis, dan bimbingan belajar untuk anak kurang mampu',
                'icon' => 'book-open',
            ],
            [
                'name' => 'Literacy Programs',
                'slug' => 'literacy-programs',
                'description' => 'Program literasi: baca tulis, literasi digital, dan perpustakaan desa',
                'icon' => 'book',
            ],
            [
                'name' => 'Vocational Training',
                'slug' => 'vocational-training',
                'description' => 'Pelatihan keterampilan: coding bootcamp, digital marketing, desain grafis untuk UMKM',
                'icon' => 'briefcase',
            ],
            [
                'name' => 'Early Childhood Education',
                'slug' => 'early-childhood-education',
                'description' => 'PAUD, TK, dan program stimulasi tumbuh kembang anak usia dini',
                'icon' => 'baby',
            ],

            // ============================================
            // LINGKUNGAN (SDG 13, 14, 15)
            // ============================================
            [
                'name' => 'Environmental Conservation',
                'slug' => 'environmental-conservation',
                'description' => 'Konservasi alam: reboisasi, perlindungan satwa, dan restorasi ekosistem',
                'icon' => 'tree',
            ],
            [
                'name' => 'Waste Management',
                'slug' => 'waste-management',
                'description' => 'Pengelolaan sampah: bank sampah, composting, dan reduce-reuse-recycle programs',
                'icon' => 'trash-2',
            ],
            [
                'name' => 'Renewable Energy',
                'slug' => 'renewable-energy',
                'description' => 'Energi terbarukan: solar panel, biogas, dan edukasi hemat energi',
                'icon' => 'zap',
            ],
            [
                'name' => 'Climate Action',
                'slug' => 'climate-action',
                'description' => 'Aksi iklim: carbon footprint tracking, climate awareness, dan adaptasi perubahan iklim',
                'icon' => 'cloud-rain',
            ],
            [
                'name' => 'Water & Ocean Conservation',
                'slug' => 'water-ocean-conservation',
                'description' => 'Konservasi air dan laut: bersih-bersih pantai, coral reef restoration, dan clean water access',
                'icon' => 'waves',
            ],

            // ============================================
            // SOSIAL & BUDAYA (SDG 1, 2, 10, 11)
            // ============================================
            [
                'name' => 'Community Development',
                'slug' => 'community-development',
                'description' => 'Pemberdayaan masyarakat: pengembangan desa, pembinaan karang taruna, dan community organizing',
                'icon' => 'users',
            ],
            [
                'name' => 'UMKM Empowerment',
                'slug' => 'umkm-empowerment',
                'description' => 'Pemberdayaan UMKM: digitalisasi, branding, marketplace onboarding, dan business development',
                'icon' => 'shopping-bag',
            ],
            [
                'name' => 'Agriculture & Food Security',
                'slug' => 'agriculture-food-security',
                'description' => 'Pertanian berkelanjutan: organic farming, urban farming, dan ketahanan pangan',
                'icon' => 'leaf',
            ],
            [
                'name' => 'Poverty Alleviation',
                'slug' => 'poverty-alleviation',
                'description' => 'Pengentasan kemiskinan: bantuan modal usaha, skill training, dan social assistance',
                'icon' => 'heart-handshake',
            ],
            [
                'name' => 'Cultural Preservation',
                'slug' => 'cultural-preservation',
                'description' => 'Pelestarian budaya: dokumentasi tradisi, revitalisasi seni lokal, dan cultural heritage mapping',
                'icon' => 'landmark',
            ],
            [
                'name' => 'Women & Youth Empowerment',
                'slug' => 'women-youth-empowerment',
                'description' => 'Pemberdayaan perempuan dan pemuda: leadership training, entrepreneurship, dan gender equality',
                'icon' => 'award',
            ],
            [
                'name' => 'Disaster Management',
                'slug' => 'disaster-management',
                'description' => 'Manajemen bencana: early warning system, disaster preparedness, dan community resilience',
                'icon' => 'alert-triangle',
            ],

            // ============================================
            // DESIGN & CREATIVE
            // ============================================
            [
                'name' => 'UI/UX Design',
                'slug' => 'ui-ux-design',
                'description' => 'Desain antarmuka dan pengalaman pengguna untuk aplikasi sosial, website pemerintah, dan platform edukasi',
                'icon' => 'palette',
            ],
            [
                'name' => 'Graphic Design & Branding',
                'slug' => 'graphic-design-branding',
                'description' => 'Desain grafis untuk kampanye sosial, branding UMKM, dan konten edukasi visual',
                'icon' => 'image',
            ],
            [
                'name' => 'Content Creation & Media',
                'slug' => 'content-creation-media',
                'description' => 'Pembuatan konten: video edukasi, podcast, infografis, dan social media campaign',
                'icon' => 'video',
            ],

            // ============================================
            // PROJECT MANAGEMENT & RESEARCH
            // ============================================
            [
                'name' => 'Project Management',
                'slug' => 'project-management',
                'description' => 'Manajemen proyek sosial, koordinasi program KKN, dan monitoring evaluasi',
                'icon' => 'clipboard',
            ],
            [
                'name' => 'Research & Policy',
                'slug' => 'research-policy',
                'description' => 'Riset sosial, policy brief, dan evidence-based recommendations untuk pemerintah',
                'icon' => 'file-text',
            ],

            // ============================================
            // MARKETING & COMMUNICATION
            // ============================================
            [
                'name' => 'Digital Marketing',
                'slug' => 'digital-marketing',
                'description' => 'Marketing digital untuk UMKM, kampanye sosial, dan promosi produk lokal',
                'icon' => 'megaphone',
            ],
            [
                'name' => 'Public Relations',
                'slug' => 'public-relations',
                'description' => 'Hubungan masyarakat, komunikasi publik, dan stakeholder engagement',
                'icon' => 'radio',
            ],
        ];

        $insertedCount = 0;
        $skippedCount = 0;

        foreach ($categories as $index => $category) {
            // Reconnect every 10 iterations
            if ($index % 10 == 0) {
                \DB::reconnect('pgsql');
            }

            try {
                // Check if category already exists - use DB facade
                $existingCount = \DB::table('job_categories')->where('slug', $category['slug'])->count();

                if ($existingCount == 0) {
                    // Insert if not exists
                    \DB::table('job_categories')->insert($category);
                    $insertedCount++;
                } else {
                    $skippedCount++;
                }
            } catch (\Exception $e) {
                // Skip duplicates
                if (strpos($e->getMessage(), 'duplicate key') !== false || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $skippedCount++;
                } else {
                    $this->command->error("❌ Failed to insert category: {$category['name']} - " . $e->getMessage());
                }
            }
        }

        $this->command->info('');
        if ($insertedCount > 0) {
            $this->command->info("✅ Inserted $insertedCount new categories");
        }
        if ($skippedCount > 0) {
            $this->command->warn("⚠️  Skipped $skippedCount existing categories");
        }

        $this->command->info('✅ Job categories seeding completed! Total categories: ' . count($categories));
        $this->command->info('📊 Breakdown:');
        $this->command->info('   - Teknologi & Inovasi: 5 categories');
        $this->command->info('   - Kesehatan: 4 categories');
        $this->command->info('   - Pendidikan: 5 categories');
        $this->command->info('   - Lingkungan: 5 categories');
        $this->command->info('   - Sosial & Budaya: 7 categories');
        $this->command->info('   - Design & Creative: 3 categories');
        $this->command->info('   - Project Management: 2 categories');
        $this->command->info('   - Marketing & Communication: 2 categories');
    }
}
