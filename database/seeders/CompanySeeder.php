<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SupabaseService;
use App\Models\Province;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * COMPANY SEEDER - FOKUS LAPANGAN REALISTIS INDONESIA
 *
 * Membuat company dengan karakteristik:
 * 1. Fokus lapangan (pertanian, manufaktur, UMKM, koperasi)
 * 2. Bukan tech company
 * 3. Data sangat lengkap dan detail
 * 4. Banyak job postings per company (5-10 postings)
 * 5. Realistis untuk program KKN mahasiswa
 */
class CompanySeeder extends Seeder
{
    protected $supabase;

    public function __construct()
    {
        $this->supabase = new SupabaseService();
    }

    public function run(): void
    {
        $this->command->info('🌱 Seeding Field-Based Companies (Perusahaan Lapangan)...');

        $companies = $this->getCompaniesData();
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($companies as $index => $companyData) {
            try {
                // Generate email sama seperti di UserSeeder
                $slug = str_replace(' ', '', strtolower($companyData['name']));
                $userEmail = 'hr@' . substr($slug, 0, 30) . '.co.id';

                $users = $this->supabase->select('users', ['id'], ['email' => $userEmail]);

                if ($users->isEmpty()) {
                    $this->command->warn("⚠️  User not found: $userEmail - Skipping...");
                    $skippedCount++;
                    continue;
                }

                $userId = $users->first()->id;

                // Check if company already exists
                $existingCompany = $this->supabase->select('companies', ['id'], ['user_id' => $userId]);
                if (!empty($existingCompany)) {
                    $skippedCount++;
                    continue;
                }

                // Get random province
                $provinces = Province::all();
                $province = $provinces->where('name', 'LIKE', '%' . $companyData['province_hint'] . '%')->first();
                if (!$province) {
                    $province = $provinces->random();
                }

                // Create company
                $company = [
                    'user_id' => $userId,
                    'name' => $companyData['name'],
                    'industry' => $companyData['industry'],
                    'description' => $companyData['description'],
                    'website' => $companyData['website'],
                    'logo' => null,
                    'address' => $companyData['address'],
                    'city' => $companyData['city'],
                    'province_id' => $province->id,
                    'phone' => '+628' . rand(1000000000, 9999999999),
                    'employee_count' => $companyData['employee_count'],
                    'founded_year' => $companyData['founded_year'],
                    'verification_status' => 'verified',
                    'verified_at' => now()->subMonths(rand(1, 6))->toDateTimeString(),
                    'created_at' => now()->subYears(rand(1, 3))->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];

                $this->supabase->insert('companies', $company);
                $createdCount++;

                // Reconnect setiap 10 companies
                if ($createdCount % 10 == 0) {
                    \DB::reconnect('pgsql');
                    $this->command->info("   ... created $createdCount companies");
                }

            } catch (\Exception $e) {
                $this->command->error("❌ Failed to create company: {$companyData['name']} - " . $e->getMessage());
                $skippedCount++;
            }
        }

        \DB::reconnect('pgsql');
        $this->command->info("✅ Company seeding completed!");
        $this->command->info("📊 Total: $createdCount created, $skippedCount skipped");
    }

    /**
     * Nama companies - HARUS SAMA dengan UserSeeder.php
     */
    private function getCompanyNames(): array
    {
        return [
            'Koperasi Tani Makmur Jaya', 'Koperasi Susu Sapi Perah Bandung', 'Koperasi Produsen Tempe Tahu Indonesia',
            'Pabrik Tahu Sumedang Asli', 'Pabrik Kerupuk Udang Sidoarjo', 'Pabrik Batik Tulis Solo',
            'CV Mebel Jati Jepara', 'CV Kerajinan Perak Kotagede', 'CV Anyaman Bambu Tasikmalaya',
            'UD Konveksi Garmen Tanah Abang', 'UD Sepatu Kulit Cibaduyut', 'UD Tas Kulit Magetan',
            'Peternakan Ayam Broiler Sentosa', 'Peternakan Sapi Perah Lembang', 'Peternakan Kambing Etawa',
            'Perkebunan Kopi Arabika Gayo', 'Perkebunan Teh Puncak', 'Perkebunan Kelapa Sawit Riau',
            'Desa Wisata Penglipuran Bali', 'Desa Wisata Wae Rebo NTT', 'Desa Wisata Kampung Naga',
            'Balai Benih Ikan Sukabumi', 'Balai Benih Tanaman Bogor', 'Balai Pelatihan Pertanian',
            'BUMDES Berkah Mandiri', 'BUMDES Sejahtera Makmur', 'BUMDES Maju Jaya',
            'Industri Rumahan Keripik Singkong', 'Industri Rumahan Dodol Garut', 'Industri Rumahan Emping Melinjo',
            'Bengkel Las Besi Berkah', 'Bengkel Bubut Logam Jaya', 'Toko Bahan Bangunan Sumber Rejeki',
            'Pabrik Genteng Press Jatiwangi', 'Pabrik Batu Bata Merah Cirebon', 'Pabrik Paving Block',
            'Sentra Industri Sepatu Mojokerto', 'Sentra Kerajinan Rotan Cirebon', 'Sentra Tenun Ikat NTT',
            'Kelompok Tani Subur Makmur', 'Kelompok Tani Berkah Tani', 'Kelompok Nelayan Mina Bahari',
            'Pabrik Gula Aren Cianjur', 'Pabrik Kecap Benteng Tangerang', 'Pabrik Sambal Pecel Madiun',
            'Pusat Pelatihan Menjahit Griya Mode', 'Balai Pelatihan Kerja Mandiri', 'LPK Otomotif Harapan Bangsa',
            'Toko Oleh-Oleh Khas Malang', 'Toko Pia Khas Semarang', 'Restoran Seafood Jimbaran',
        ];
    }

    /**
     * Data companies yang realistis untuk KKN lapangan
     */
    private function getCompaniesData(): array
    {
        $companyNames = $this->getCompanyNames();
        $companies = [];

        // Mapping industry berdasarkan kata kunci di nama
        $industryMap = [
            'Koperasi' => 'Koperasi & Agribisnis',
            'Pabrik' => 'Manufaktur & Industri',
            'CV' => 'Usaha Kecil Menengah',
            'UD' => 'Usaha Dagang',
            'Peternakan' => 'Peternakan',
            'Perkebunan' => 'Perkebunan',
            'Desa Wisata' => 'Pariwisata Desa',
            'Balai' => 'Lembaga Pemerintah',
            'BUMDES' => 'Badan Usaha Milik Desa',
            'Industri Rumahan' => 'Industri Rumahan',
            'Bengkel' => 'Jasa & Bengkel',
            'Toko' => 'Perdagangan',
            'Sentra' => 'Sentra Industri',
            'Kelompok' => 'Kelompok Usaha Bersama',
            'Pusat Pelatihan' => 'Pendidikan & Pelatihan',
            'LPK' => 'Lembaga Pelatihan Kerja',
            'Restoran' => 'Kuliner & Restoran',
        ];

        // Province hints berdasarkan nama
        $provinceHints = [
            'Bandung' => 'Jawa Barat', 'Sumedang' => 'Jawa Barat', 'Cibaduyut' => 'Jawa Barat',
            'Garut' => 'Jawa Barat', 'Tasikmalaya' => 'Jawa Barat', 'Sukabumi' => 'Jawa Barat',
            'Bogor' => 'Jawa Barat', 'Jatiwangi' => 'Jawa Barat', 'Cirebon' => 'Jawa Barat',
            'Cianjur' => 'Jawa Barat', 'Tangerang' => 'Banten', 'Jakarta' => 'Jakarta',
            'Tanah Abang' => 'Jakarta', 'Sidoarjo' => 'Jawa Timur', 'Solo' => 'Jawa Tengah',
            'Jepara' => 'Jawa Tengah', 'Kotagede' => 'Yogyakarta', 'Magetan' => 'Jawa Timur',
            'Sentosa' => 'Jawa Tengah', 'Lembang' => 'Jawa Barat', 'Etawa' => 'Jawa Tengah',
            'Gayo' => 'Aceh', 'Puncak' => 'Jawa Barat', 'Riau' => 'Riau',
            'Penglipuran' => 'Bali', 'Bali' => 'Bali', 'Wae Rebo' => 'Nusa Tenggara Timur',
            'NTT' => 'Nusa Tenggara Timur', 'Naga' => 'Jawa Barat', 'Mojokerto' => 'Jawa Timur',
            'Madiun' => 'Jawa Timur', 'Malang' => 'Jawa Timur', 'Semarang' => 'Jawa Tengah',
            'Jimbaran' => 'Bali',
        ];

        foreach ($companyNames as $index => $name) {
            // Tentukan industry
            $industry = 'Umum';
            foreach ($industryMap as $keyword => $industryName) {
                if (stripos($name, $keyword) !== false) {
                    $industry = $industryName;
                    break;
                }
            }

            // Tentukan province hint
            $provinceHint = 'Jawa Barat'; // default
            foreach ($provinceHints as $keyword => $province) {
                if (stripos($name, $keyword) !== false) {
                    $provinceHint = $province;
                    break;
                }
            }

            // Tentukan city berdasarkan nama
            $city = $this->extractCityFromName($name);

            // Generate description
            $description = $this->generateDescription($name, $industry);

            $companies[] = [
                'name' => $name,
                'industry' => $industry,
                'description' => $description,
                'website' => 'https://' . strtolower(str_replace([' ', '-'], '', $name)) . '.co.id',
                'address' => 'Jl. Industri No. ' . rand(10, 200),
                'city' => $city,
                'province_hint' => $provinceHint,
                'employee_count' => $this->getRandomEmployeeCount($industry),
                'founded_year' => rand(1995, 2020),
            ];
        }

        return $companies;
    }

    private function extractCityFromName($name): string
    {
        $cities = [
            'Bandung', 'Sumedang', 'Sidoarjo', 'Solo', 'Jepara', 'Yogyakarta', 'Kotagede',
            'Tanah Abang', 'Magetan', 'Garut', 'Tasikmalaya', 'Sukabumi', 'Bogor',
            'Tangerang', 'Jakarta', 'Cirebon', 'Cianjur', 'Malang', 'Semarang',
            'Mojokerto', 'Madiun', 'Jimbaran', 'Bali', 'Lembang', 'Puncak',
        ];

        foreach ($cities as $city) {
            if (stripos($name, $city) !== false) {
                return $city;
            }
        }

        return 'Bandung'; // default
    }

    private function generateDescription($name, $industry): string
    {
        $templates = [
            'Koperasi & Agribisnis' => "Koperasi yang bergerak dalam bidang {field} dengan fokus pemberdayaan anggota dan peningkatan kesejahteraan petani lokal. Menerapkan sistem kemitraan berkelanjutan dengan hasil produksi berkualitas tinggi.",
            'Manufaktur & Industri' => "Pabrik manufaktur yang memproduksi {product} berkualitas tinggi dengan standar produksi modern. Melayani pasar lokal dan regional dengan kapasitas produksi yang memadai dan tenaga kerja terampil.",
            'Usaha Kecil Menengah' => "Usaha kecil menengah yang bergerak di bidang {field} dengan produk berkualitas dan pelayanan terbaik. Mempekerjakan tenaga kerja lokal dan berkontribusi pada ekonomi daerah.",
            'Peternakan' => "Peternakan modern yang mengelola budidaya {animal} dengan sistem pemeliharaan terbaik. Menghasilkan produk berkualitas tinggi untuk memenuhi kebutuhan pasar domestik.",
            'Perkebunan' => "Perkebunan {crop} dengan luas lahan yang memadai dan sistem budidaya berkelanjutan. Memproduksi hasil perkebunan premium untuk pasar lokal dan ekspor.",
            'Pariwisata Desa' => "Desa wisata yang menawarkan pengalaman budaya dan tradisi lokal yang autentik. Melibatkan masyarakat dalam pengelolaan pariwisata dengan prinsip pemberdayaan ekonomi lokal.",
            'Badan Usaha Milik Desa' => "BUMDES yang mengelola berbagai unit usaha untuk kesejahteraan warga desa. Fokus pada pengembangan ekonomi lokal dan pemberdayaan masyarakat desa.",
            'Industri Rumahan' => "Industri rumahan yang memproduksi {product} dengan resep tradisional dan kualitas terjaga. Melibatkan ibu rumah tangga dan tenaga kerja lokal dengan sistem kemitraan.",
            'Jasa & Bengkel' => "Bengkel yang menyediakan jasa {service} dengan tenaga ahli berpengalaman. Melayani kebutuhan industri dan masyarakat umum dengan kualitas terjamin.",
            'Perdagangan' => "Toko yang menjual {product} lengkap dengan harga kompetitif. Melayani konsumen ritel dan grosir dengan pelayanan terbaik.",
            'Sentra Industri' => "Sentra industri yang menampung para pengrajin {product} dengan sistem kemitraan dan pemasaran bersama. Menghasilkan produk berkualitas untuk pasar domestik dan ekspor.",
            'Kelompok Usaha Bersama' => "Kelompok usaha bersama yang mengelola {field} dengan sistem koperasi. Memberdayakan anggota dan meningkatkan kesejahteraan bersama.",
            'Pendidikan & Pelatihan' => "Lembaga pelatihan yang menyelenggarakan program {training} untuk meningkatkan keterampilan dan kompetensi peserta. Memiliki instruktur berpengalaman dan fasilitas lengkap.",
            'Kuliner & Restoran' => "Restoran yang menyajikan {food} dengan cita rasa khas dan bahan berkualitas. Menawarkan pengalaman kuliner yang berkesan dengan pelayanan ramah.",
        ];

        $template = $templates[$industry] ?? "Usaha yang bergerak di bidang {field} dengan komitmen pada kualitas dan pelayanan terbaik untuk pelanggan.";

        // Replace placeholders
        $description = str_replace(
            ['{field}', '{product}', '{animal}', '{crop}', '{service}', '{training}', '{food}'],
            [$this->getFieldFromName($name), $this->getProductFromName($name), $this->getAnimalFromName($name),
             $this->getCropFromName($name), $this->getServiceFromName($name), $this->getTrainingFromName($name),
             $this->getFoodFromName($name)],
            $template
        );

        return $description;
    }

    private function getFieldFromName($name): string {
        if (stripos($name, 'Tani') !== false) return 'pertanian dan agribisnis';
        if (stripos($name, 'Susu') !== false) return 'peternakan sapi perah';
        if (stripos($name, 'Tempe') !== false || stripos($name, 'Tahu') !== false) return 'produksi tempe dan tahu';
        return 'usaha produktif';
    }

    private function getProductFromName($name): string {
        if (stripos($name, 'Tahu') !== false) return 'tahu';
        if (stripos($name, 'Kerupuk') !== false) return 'kerupuk';
        if (stripos($name, 'Batik') !== false) return 'batik';
        if (stripos($name, 'Mebel') !== false) return 'mebel jati';
        if (stripos($name, 'Perak') !== false) return 'kerajinan perak';
        if (stripos($name, 'Bambu') !== false) return 'anyaman bambu';
        if (stripos($name, 'Sepatu') !== false) return 'sepatu kulit';
        if (stripos($name, 'Tas') !== false) return 'tas kulit';
        if (stripos($name, 'Keripik') !== false) return 'keripik singkong';
        if (stripos($name, 'Dodol') !== false) return 'dodol';
        if (stripos($name, 'Emping') !== false) return 'emping melinjo';
        if (stripos($name, 'Genteng') !== false) return 'genteng press';
        if (stripos($name, 'Bata') !== false) return 'batu bata merah';
        if (stripos($name, 'Paving') !== false) return 'paving block';
        if (stripos($name, 'Rotan') !== false) return 'kerajinan rotan';
        if (stripos($name, 'Tenun') !== false) return 'kain tenun ikat';
        if (stripos($name, 'Gula Aren') !== false) return 'gula aren organik';
        if (stripos($name, 'Kecap') !== false) return 'kecap';
        if (stripos($name, 'Sambal') !== false) return 'sambal pecel';
        if (stripos($name, 'Oleh-Oleh') !== false) return 'oleh-oleh khas daerah';
        if (stripos($name, 'Pia') !== false) return 'pia';
        return 'produk berkualitas';
    }

    private function getAnimalFromName($name): string {
        if (stripos($name, 'Ayam') !== false) return 'ayam broiler';
        if (stripos($name, 'Sapi') !== false) return 'sapi perah';
        if (stripos($name, 'Kambing') !== false) return 'kambing etawa';
        return 'ternak';
    }

    private function getCropFromName($name): string {
        if (stripos($name, 'Kopi') !== false) return 'kopi arabika';
        if (stripos($name, 'Teh') !== false) return 'teh hijau dan hitam';
        if (stripos($name, 'Sawit') !== false) return 'kelapa sawit';
        return 'tanaman produktif';
    }

    private function getServiceFromName($name): string {
        if (stripos($name, 'Las') !== false) return 'las dan fabrikasi besi';
        if (stripos($name, 'Bubut') !== false) return 'bubut logam presisi';
        return 'jasa teknik';
    }

    private function getTrainingFromName($name): string {
        if (stripos($name, 'Menjahit') !== false) return 'menjahit dan desain busana';
        if (stripos($name, 'Kerja') !== false) return 'vokasi dan keterampilan kerja';
        if (stripos($name, 'Otomotif') !== false) return 'mekanik otomotif';
        if (stripos($name, 'Pertanian') !== false) return 'budidaya pertanian modern';
        return 'keterampilan praktis';
    }

    private function getFoodFromName($name): string {
        if (stripos($name, 'Seafood') !== false) return 'seafood segar';
        return 'hidangan lezat';
    }

    private function getRandomEmployeeCount($industry): string {
        $ranges = [
            'Koperasi & Agribisnis' => ['51-100', '101-200', '201-500'],
            'Manufaktur & Industri' => ['51-100', '101-200'],
            'Peternakan' => ['21-50', '51-100'],
            'Perkebunan' => ['101-200', '201-500'],
            'Badan Usaha Milik Desa' => ['11-20', '21-50'],
            'Industri Rumahan' => ['11-20', '21-50'],
        ];

        $range = $ranges[$industry] ?? ['21-50', '51-100'];
        return $range[array_rand($range)];
    }
}
