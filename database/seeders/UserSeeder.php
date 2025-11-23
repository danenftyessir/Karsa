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
            try {
                // Check if user already exists
                $existingUser = $this->supabase->select('users', ['id'], ['email' => $user['email']]);
                if (!empty($existingUser)) {
                    $skippedCompany++;
                    continue;
                }

                $this->supabase->insert('users', $user);
                $insertedCompany++;

                // Flush connection every 10 inserts to avoid prepared statement limit
                if (($index + 1) % 10 == 0) {
                    \DB::reconnect('pgsql');
                }
            } catch (\Exception $e) {
                $this->command->error("❌ Failed to insert user: {$user['email']} - " . $e->getMessage());
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
            // Company lapangan yang realistis untuk program KKN
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
