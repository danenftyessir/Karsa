<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Services\SupabaseService;

class UserSeeder extends Seeder
{
    protected $supabase;

    public function __construct()
    {
        $this->supabase = new SupabaseService();
    }

    /**
     * Run the database seeds.
     *
     * Create users untuk:
     * 1. Company users (user_type = 'company') - FOKUS HANYA COMPANY
     *
     * Note: Student dan Institution users sudah dibuat di DummyDataSeeder.php
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding company users only...');

        // ============================================
        // COMPANY USERS
        // ============================================
        $companyUsers = $this->generateCompanyUsers();
        $insertedCompany = 0;
        $skippedCompany = 0;
        foreach ($companyUsers as $index => $user) {
            // Reconnect every 10 iterations to avoid prepared statement limit
            if ($index % 10 == 0) {
                \DB::reconnect('pgsql');
            }

            try {
                // Use DB facade instead of Supabase Service for better reliability
                $existingCount = \DB::table('users')->where('email', $user['email'])->count();
                if ($existingCount > 0) {
                    $skippedCompany++;
                    continue;
                }

                \DB::table('users')->insert($user);
                $insertedCompany++;

            } catch (\Exception $e) {
                // Skip if duplicate key error
                if (strpos($e->getMessage(), 'duplicate key') !== false || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $skippedCompany++;
                } else {
                    $this->command->error("❌ Failed to insert user: {$user['email']} - " . $e->getMessage());
                }
            }
        }

        if ($skippedCompany > 0) {
            $this->command->warn("⚠️  Skipped $skippedCompany existing company users");
        }
        $this->command->info("✅ Company users seeded: $insertedCompany new users");

        \DB::reconnect('pgsql'); // Final reconnect
        $this->command->info("🎉 Total company users created: $insertedCompany");
    }

    /**
     * Generate company users - Company lapangan yang realistis untuk KKN
     */
    private function generateCompanyUsers(): array
    {
        $users = [];
        $companyNames = [
            // Original 50 companies
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

            // Additional 60+ companies (Total >110 companies) - MUST MATCH CompanySeeder.php
            'Koperasi Nelayan Pantai Selatan', 'Koperasi Pengrajin Anyaman Mendong', 'Koperasi Peternak Sapi Potong',
            'Pabrik Gula Merah Banyumas', 'Pabrik Teh Celup Bogor', 'Pabrik Kopi Bubuk Toraja',
            'CV Furnitur Kayu Mahoni', 'CV Batik Cap Pekalongan', 'CV Bordir Tradisional Kudus',
            'UD Sandal Jepit Bogor', 'UD Kaos Sablon Yogyakarta', 'UD Batik Printing Surabaya',
            'Peternakan Bebek Petelur Brebes', 'Peternakan Ikan Lele Depok', 'Peternakan Kelinci Sukabumi',
            'Perkebunan Kakao Sulawesi', 'Perkebunan Cengkeh Maluku', 'Perkebunan Vanili Bali',
            'Desa Wisata Umbul Ponggok', 'Desa Wisata Kampoeng Batik Laweyan', 'Desa Wisata Sade Lombok',
            'Balai Latihan Kerja Jawa Timur', 'Balai Benih Sayuran Lembang', 'Balai Pengembangan UMKM',
            'BUMDES Karya Bersama', 'BUMDES Usaha Maju', 'BUMDES Tani Sejahtera',
            'Industri Rumahan Abon Sapi', 'Industri Rumahan Kue Kering', 'Industri Rumahan Asinan Jakarta',
            'Bengkel Motor Jaya Abadi', 'Bengkel Mobil Mandiri', 'Toko Elektronik Sumber Makmur',
            'Pabrik Keramik Dinoyo', 'Pabrik Batu Alam Tulungagung', 'Pabrik Marmer Majalengka',
            'Sentra Kerajinan Kulit Garut', 'Sentra Batik Tulis Madura', 'Sentra Gerabah Kasongan',
            'Kelompok Tani Organik Nusantara', 'Kelompok Ternak Ayam Kampung', 'Kelompok Nelayan Tradisional',
            'Pabrik Roti Manis Bandung', 'Pabrik Coklat Premium Bali', 'Pabrik Keripik Pedas Madiun',
            'LPK Komputer Nusantara', 'LPK Bahasa Inggris Global', 'LPK Tata Boga Indonesia',
            'Toko Kerajinan Tangan Bali', 'Toko Batik Nusantara', 'Restoran Sunda Khas Priangan',
            'Koperasi Pengrajin Wayang Kulit', 'Koperasi Petani Sayur Organik', 'Koperasi Produsen Susu Kambing',
            'Pabrik Minuman Tradisional', 'Pabrik Jamu Herbal Jawa', 'Pabrik Minyak Kelapa VCO',
            'CV Kontraktor Bangunan', 'CV Advertising Kreatif', 'CV Event Organizer Profesional',
            'UD Percetakan Modern', 'UD Fotokopi Digital', 'UD Alat Tulis Kantor',
            'Peternakan Burung Puyuh', 'Peternakan Domba Garut', 'Peternakan Udang Vaname',
            'Perkebunan Jeruk Pontianak', 'Perkebunan Salak Pondoh', 'Perkebunan Durian Medan',
            'Desa Wisata Dieng Plateau', 'Desa Wisata Kete Kesu Toraja', 'Desa Wisata Osing Banyuwangi',
            'Balai Pelatihan Teknologi', 'Balai Pengembangan Perikanan', 'Balai Penelitian Pertanian',
            'BUMDES Wisata Alam', 'BUMDES Energi Terbarukan', 'BUMDES Keuangan Mikro',
        ];

        for ($i = 0; $i < count($companyNames); $i++) {
            $companyName = $companyNames[$i];
            $slug = str_replace(' ', '', strtolower($companyName));

            $users[] = [
                'name' => $companyName,
                'username' => 'company_' . substr($slug, 0, 20) . '_' . $i,
                'email' => 'hr@' . substr($slug, 0, 30) . '.co.id',
                'password' => Hash::make('password123'),
                'user_type' => 'company',
                'email_verified_at' => now()->toDateTimeString(),
                'created_at' => now()->subDays(rand(1, 365))->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        return $users;
    }
}
