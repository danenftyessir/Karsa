<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Institution;
use App\Models\University;
use App\Models\Province;
use App\Models\Regency;

/**
 * seeder untuk data dummy users, students, institutions, universities
 * UPDATED: menggunakan data provinces & regencies dari database (dinamis dari BPS)
 * 
 * path: database/seeders/DummyDataSeeder.php
 * jalankan: php artisan db:seed --class=DummyDataSeeder
 */
class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "memulai seeding data dummy...\n\n";
        
        $this->cleanOldData();
        $this->seedUniversities();
        $this->seedStudents();
        $this->seedInstitutions();
        
        echo "\n";
        echo "==========================================\n";
        echo "seeding data dummy selesai!\n";
        echo "==========================================\n";
        echo "\n";
        echo "statistik data:\n";
        echo "  - universities  : " . University::count() . "\n";
        echo "  - students      : " . Student::count() . "\n";
        echo "  - institutions  : " . Institution::count() . "\n";
        echo "\n";
        echo "akun testing (semua password: password123):\n";
        echo "\n";
        echo "mahasiswa pertama:\n";
        $firstStudent = Student::with('user', 'university')->first();
        if ($firstStudent) {
            echo "  nama     : " . $firstStudent->first_name . " " . $firstStudent->last_name . "\n";
            echo "  email    : " . $firstStudent->user->email . "\n";
            echo "  username : " . $firstStudent->user->username . "\n";
            echo "  kampus   : " . $firstStudent->university->name . "\n";
        }
        echo "\n";
        echo "instansi pertama:\n";
        $firstInst = Institution::with('user')->first();
        if ($firstInst) {
            echo "  nama     : " . $firstInst->name . "\n";
            echo "  email    : " . $firstInst->user->email . "\n";
            echo "  username : " . $firstInst->user->username . "\n";
            echo "  jenis    : " . $firstInst->type . "\n";
        }
        echo "\n";
    }

    /**
     * hapus data lama untuk clean install
     */
    private function cleanOldData(): void
    {
        echo "membersihkan data lama...\n";
        
        DB::table('students')->delete();
        DB::table('institutions')->delete();
        DB::table('universities')->delete();
        DB::table('users')->whereIn('user_type', ['student', 'institution'])->delete();
        
        echo "  -> data lama berhasil dihapus\n";
    }

    /**
     * seeding universities (120+ universitas dari seluruh Indonesia)
     */
    private function seedUniversities(): void
    {
        echo "seeding universities...\n";
        
        // ambil semua provinces untuk distribusi
        $allProvinces = Province::all();
        
        if ($allProvinces->isEmpty()) {
            echo "  ERROR: Tidak ada data provinces!\n";
            echo "  Jalankan ProvincesRegenciesSeeder terlebih dahulu!\n";
            return;
        }
        
        echo "  -> tersedia " . $allProvinces->count() . " provinsi dari BPS\n";
        
        // helper function untuk mendapatkan location
        $getLocation = function($provinceName = null) use ($allProvinces) {
            if ($provinceName) {
                // cari province berdasarkan nama
                $province = $allProvinces->first(function($p) use ($provinceName) {
                    return stripos($p->name, $provinceName) !== false;
                });
                
                if (!$province) {
                    $province = $allProvinces->random();
                }
            } else {
                // random province
                $province = $allProvinces->random();
            }
            
            // ambil regency dari province tersebut
            $regencies = Regency::where('province_id', $province->id)->get();
            
            if ($regencies->isEmpty()) {
                // fallback jika tidak ada regency
                return [
                    'province_id' => $province->id,
                    'regency_id' => null // akan dihandle oleh database
                ];
            }
            
            $regency = $regencies->random();
            
            return [
                'province_id' => $province->id,
                'regency_id' => $regency->id
            ];
        };
        
        // daftar universitas dengan hint lokasi (opsional)
        $universitiesData = [
            ['name' => 'Universitas Indonesia', 'code' => 'UI', 'province_hint' => 'Jakarta', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Institut Teknologi Bandung', 'code' => 'ITB', 'province_hint' => 'Jawa Barat', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Institut Pertanian Bogor', 'code' => 'IPB', 'province_hint' => 'Jawa Barat', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Padjadjaran', 'code' => 'UNPAD', 'province_hint' => 'Jawa Barat', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Diponegoro', 'code' => 'UNDIP', 'province_hint' => 'Jawa Tengah', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Sebelas Maret', 'code' => 'UNS', 'province_hint' => 'Jawa Tengah', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Gadjah Mada', 'code' => 'UGM', 'province_hint' => 'Yogyakarta', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Airlangga', 'code' => 'UNAIR', 'province_hint' => 'Jawa Timur', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Brawijaya', 'code' => 'UB', 'province_hint' => 'Jawa Timur', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Institut Teknologi Sepuluh Nopember', 'code' => 'ITS', 'province_hint' => 'Jawa Timur', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Udayana', 'code' => 'UNUD', 'province_hint' => 'Bali', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Trunojoyo Madura', 'code' => 'UTM', 'province_hint' => 'Jawa Timur', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Teuku Umar', 'code' => 'UTU', 'province_hint' => 'Aceh', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Terbuka', 'code' => 'UT', 'province_hint' => 'Jakarta', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Tanjungpura', 'code' => 'UNTAN', 'province_hint' => 'Kalimantan Barat', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Syiah Kuala', 'code' => 'UNSYIAH', 'province_hint' => 'Aceh', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Sumatera Utara', 'code' => 'USU', 'province_hint' => 'Sumatera Utara', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Sultan Ageng Tirtayasa', 'code' => 'UNTIRTA', 'province_hint' => 'Banten', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Sulawesi Tenggara', 'code' => 'USULTRA', 'province_hint' => null, 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Sulawesi Barat', 'code' => 'UNSULBAR', 'province_hint' => null, 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Sembilanbelas November', 'code' => 'UNS19', 'province_hint' => 'Jawa Timur', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Samudra', 'code' => 'UNSAM', 'province_hint' => 'Aceh', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Sains Al Quran', 'code' => 'UNSIQ', 'province_hint' => 'Jawa Tengah', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Pendidikan Muhammadiyah Sorong', 'code' => 'UNIMUDA', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Pendidikan Indonesia', 'code' => 'UPI', 'province_hint' => 'Jawa Barat', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Pendidikan Ganesha', 'code' => 'UNDIKSHA', 'province_hint' => 'Bali', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Pembangunan Nasional Veteran Jawa Timur', 'code' => 'UPNVJT', 'province_hint' => 'Jawa Timur', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Pattimura', 'code' => 'UNPATTI', 'province_hint' => 'Maluku', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Pasifik Morotai', 'code' => 'UNIPAS', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'C'],
            ['name' => 'Universitas Pancasakti Tegal', 'code' => 'UPS', 'province_hint' => 'Jawa Tengah', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Palangka Raya', 'code' => 'UPR', 'province_hint' => 'Kalimantan Tengah', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Nurul Huda', 'code' => 'UNUHA', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Noor Huda Mustofa', 'code' => 'UNHM', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'C'],
            ['name' => 'Universitas Negeri Yogyakarta', 'code' => 'UNY', 'province_hint' => 'Yogyakarta', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Negeri Surabaya', 'code' => 'UNESA', 'province_hint' => 'Jawa Timur', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Negeri Semarang', 'code' => 'UNNES', 'province_hint' => 'Jawa Tengah', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Negeri Padang', 'code' => 'UNP', 'province_hint' => 'Sumatera Barat', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Negeri Medan', 'code' => 'UNIMED', 'province_hint' => 'Sumatera Utara', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Negeri Manado', 'code' => 'UNIMA', 'province_hint' => null, 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Negeri Malang', 'code' => 'UM', 'province_hint' => 'Jawa Timur', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Negeri Makassar', 'code' => 'UNM', 'province_hint' => 'Sulawesi Selatan', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Negeri Jakarta', 'code' => 'UNJ', 'province_hint' => 'Jakarta', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Negeri Gorontalo', 'code' => 'UNG', 'province_hint' => null, 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Muslim Nusantara Al Washliyah', 'code' => 'UMNAW', 'province_hint' => 'Sumatera Utara', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Muslim Indonesia', 'code' => 'UMI', 'province_hint' => 'Sulawesi Selatan', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Musamus', 'code' => 'UNMUS', 'province_hint' => null, 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Muria Kudus', 'code' => 'UMK', 'province_hint' => 'Jawa Tengah', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Mulawarman', 'code' => 'UNMUL', 'province_hint' => 'Kalimantan Timur', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Muhammadiyah Sinjai', 'code' => 'UMSINJAI', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Muhammadiyah Purworejo', 'code' => 'UMPWR', 'province_hint' => 'Jawa Tengah', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Muhammadiyah Ponorogo', 'code' => 'UMPO', 'province_hint' => 'Jawa Timur', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Muhammadiyah Palangkaraya', 'code' => 'UMPALANGKA', 'province_hint' => 'Kalimantan Tengah', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Muhammadiyah Kolaka', 'code' => 'UMKOLAKA', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Muhammadiyah Buton', 'code' => 'UMBUTON', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Muhammadiyah Makassar', 'code' => 'UNISMUH', 'province_hint' => 'Sulawesi Selatan', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Mataram', 'code' => 'UNRAM', 'province_hint' => 'Nusa Tenggara Barat', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Malikussaleh', 'code' => 'UNIMAL', 'province_hint' => 'Aceh', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Madura', 'code' => 'UNIRA', 'province_hint' => 'Jawa Timur', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Timor', 'code' => 'UNIMOR', 'province_hint' => 'Nusa Tenggara Timur', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Lampung', 'code' => 'UNILA', 'province_hint' => 'Lampung', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Labuhan Batu', 'code' => 'UNLABUHAN', 'province_hint' => 'Sumatera Utara', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Kuningan', 'code' => 'UNIKU', 'province_hint' => 'Jawa Barat', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Kristen Indonesia Toraja', 'code' => 'UKITORAJA', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Kristen Indonesia Maluku', 'code' => 'UKIM', 'province_hint' => 'Maluku', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Komputer Indonesia', 'code' => 'UNIKOM', 'province_hint' => 'Jawa Barat', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Khairun', 'code' => 'UNKHAIR', 'province_hint' => null, 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Jambi', 'code' => 'UNJA', 'province_hint' => 'Jambi', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Islam Malang', 'code' => 'UIM', 'province_hint' => 'Jawa Timur', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Islam Tirtayasa', 'code' => 'UIT', 'province_hint' => 'Banten', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Ibn Khaldun Bogor', 'code' => 'UIKA', 'province_hint' => 'Jawa Barat', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Hasanuddin', 'code' => 'UNHAS', 'province_hint' => 'Sulawesi Selatan', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Halu Oleo', 'code' => 'UHO', 'province_hint' => null, 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Famika', 'code' => 'UFAMIKA', 'province_hint' => 'Jawa Tengah', 'type' => 'swasta', 'accreditation' => 'C'],
            ['name' => 'Universitas Esa Unggul', 'code' => 'UEU', 'province_hint' => 'Jakarta', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Darma Agung', 'code' => 'UDA', 'province_hint' => 'Sumatera Utara', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Boyolali', 'code' => 'UBY', 'province_hint' => 'Jawa Tengah', 'type' => 'swasta', 'accreditation' => 'C'],
            ['name' => 'Universitas Borneo Tarakan', 'code' => 'UBT', 'province_hint' => 'Kalimantan Utara', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Atma Jaya Makassar', 'code' => 'UAJM', 'province_hint' => 'Sulawesi Selatan', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Andalas', 'code' => 'UNAND', 'province_hint' => 'Sumatera Barat', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Almarisah Madani', 'code' => 'UAM', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'C'],
            ['name' => 'Universitas Al Azhar Indonesia', 'code' => 'UAI', 'province_hint' => 'Jakarta', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Al Asyariah Mandar', 'code' => 'UNASMAN', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Abulyatama', 'code' => 'UNAYA', 'province_hint' => 'Aceh', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas 17 Agustus 1945 Samarinda', 'code' => 'UNTAG', 'province_hint' => 'Kalimantan Timur', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Tadulako', 'code' => 'UNTAD', 'province_hint' => 'Sulawesi Tengah', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Bima Internasional MFH', 'code' => 'UBI', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'C'],
            ['name' => 'Universitas Nahdatul Watan Mataram', 'code' => 'UNWAM', 'province_hint' => 'Nusa Tenggara Barat', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Islam Negeri Tulung Agung', 'code' => 'UINTA', 'province_hint' => 'Jawa Timur', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Islam Negeri Sultan Syarif Kasim Riau', 'code' => 'UIN SUSKA', 'province_hint' => 'Riau', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Bangka Belitung', 'code' => 'UBB', 'province_hint' => null, 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Islam Negeri Sayyid Ali Rahmatullah Tulungagung', 'code' => 'UIN SATU', 'province_hint' => 'Jawa Timur', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Universitas Jenderal Soedirman', 'code' => 'UNSOED', 'province_hint' => 'Jawa Tengah', 'type' => 'negeri', 'accreditation' => 'A'],         
            ['name' => 'Institut Teknologi Sumatera', 'code' => 'ITERA', 'province_hint' => 'Lampung', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Institut Teknologi Kalimantan', 'code' => 'ITK', 'province_hint' => 'Kalimantan Timur', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Institut Teknologi Dan Sains Nahdlatul Ulama Pekalongan', 'code' => 'ITSNUPKL', 'province_hint' => 'Jawa Tengah', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Institut Teknologi Dan Bisnis Maritim Balik Diwa Makassar', 'code' => 'ITBM', 'province_hint' => 'Sulawesi Selatan', 'type' => 'swasta', 'accreditation' => 'C'],
            ['name' => 'Institut Seni Budaya Indonesia Bandung', 'code' => 'ISBI BDG', 'province_hint' => 'Jawa Barat', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Institut Seni Budaya Indonesia Aceh', 'code' => 'ISBI ACEH', 'province_hint' => 'Aceh', 'type' => 'negeri', 'accreditation' => 'B'],
            ['name' => 'Institut Pangeran Dharma Kusuma Indramayu', 'code' => 'IPDKI', 'province_hint' => 'Jawa Barat', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Institut Agama Hindu Negeri Tampung Penyang Palangka Raya', 'code' => 'IAHN', 'province_hint' => 'Kalimantan Tengah', 'type' => 'negeri', 'accreditation' => 'B'],           
            ['name' => 'Sekolah Tinggi Keguruan Dan Ilmu Pendidikan Al Hikmah Surabaya', 'code' => 'STKIP ALHIK', 'province_hint' => 'Jawa Timur', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Sekolah Tinggi Ilmu Manajemen Indonesia', 'code' => 'STIMI', 'province_hint' => 'Jakarta', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Sekolah Tinggi Ilmu Kesehatan Salewangan Maros', 'code' => 'STIKES MAROS', 'province_hint' => 'Sulawesi Selatan', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Sekolah Tinggi Ilmu Kesehatan Makassar', 'code' => 'STIKMA', 'province_hint' => 'Sulawesi Selatan', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Sekolah Tinggi Ilmu Ekonomi Makassar', 'code' => 'STIEM', 'province_hint' => 'Sulawesi Selatan', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Politeknik Bumi Akpelni', 'code' => 'POLBOM', 'province_hint' => null, 'type' => 'swasta', 'accreditation' => 'B'],        
            ['name' => 'Universitas Islam Negeri Syarif Hidayatullah Jakarta', 'code' => 'UIN JKT', 'province_hint' => 'Jakarta', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Islam Negeri Sunan Gunung Djati', 'code' => 'UIN SGD', 'province_hint' => 'Jawa Barat', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Islam Negeri Walisongo', 'code' => 'UIN WALISONGO', 'province_hint' => 'Jawa Tengah', 'type' => 'negeri', 'accreditation' => 'A'],
            ['name' => 'Universitas Islam Negeri Sunan Kalijaga', 'code' => 'UIN SUKA', 'province_hint' => 'Yogyakarta', 'type' => 'negeri', 'accreditation' => 'A'],           
            ['name' => 'Universitas Trisakti', 'code' => 'USAKTI', 'province_hint' => 'Jakarta', 'type' => 'swasta', 'accreditation' => 'A'],
            ['name' => 'Universitas Mercu Buana', 'code' => 'UMB', 'province_hint' => 'Jakarta', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Pasundan', 'code' => 'UNPAS', 'province_hint' => 'Jawa Barat', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Islam Indonesia', 'code' => 'UII', 'province_hint' => 'Yogyakarta', 'type' => 'swasta', 'accreditation' => 'A'],
            ['name' => 'Universitas Islam Sultan Agung', 'code' => 'UNISSULA', 'province_hint' => 'Jawa Tengah', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Muhammadiyah Semarang', 'code' => 'UNIMUS', 'province_hint' => 'Jawa Tengah', 'type' => 'swasta', 'accreditation' => 'B'],
            ['name' => 'Universitas Muhammadiyah Tangerang', 'code' => 'UMT', 'province_hint' => 'Banten', 'type' => 'swasta', 'accreditation' => 'B'],
        ];
        
        $createdCount = 0;
        
        foreach ($universitiesData as $univData) {
            // dapatkan lokasi berdasarkan hint
            $location = $getLocation($univData['province_hint'] ?? null);
            
            // skip jika tidak dapat location
            if (!$location || !$location['province_id']) {
                continue;
            }
            
            University::create([
                'name' => $univData['name'],
                'code' => $univData['code'],
                'province_id' => $location['province_id'],
                'regency_id' => $location['regency_id'],
                'type' => $univData['type'],
                'accreditation' => $univData['accreditation'],
            ]);
            
            $createdCount++;
        }
        
        echo "  -> {$createdCount} universities berhasil dibuat\n";
    }

    /**
     * seeding students dummy (700 students untuk 120+ universitas - LEBIH BANYAK UNTUK KKN)
     */
    private function seedStudents(): void
    {
        echo "seeding students...\n";

        $firstNames = [
            'Andi', 'Budi', 'Citra', 'Devi', 'Eko', 'Farah', 'Gilang', 'Hana', 'Indra', 'Joko',
            'Kiki', 'Lisa', 'Maya', 'Nanda', 'Olivia', 'Putra', 'Qori', 'Rina', 'Sari', 'Tika',
            'Umar', 'Vera', 'Wawan', 'Xena', 'Yudi', 'Zara', 'Adam', 'Bella', 'Candra', 'Diana',
            'Erlangga', 'Fitri', 'Galih', 'Hesti', 'Ilham', 'Julia', 'Kevin', 'Laila', 'Mukti', 'Nisa',
            'Oscar', 'Pandu', 'Qina', 'Rafi', 'Sinta', 'Teguh', 'Ulfa', 'Vino', 'Wulan', 'Yusuf',
            'Agus', 'Bayu', 'Clara', 'Dimas', 'Elsa', 'Faisal', 'Gita', 'Hadi', 'Intan', 'Jihan',
            'Kurnia', 'Lina', 'Malik', 'Nadia', 'Omar', 'Putri', 'Qomar', 'Reza', 'Silvi', 'Toni',
            'Umi', 'Vina', 'Wahyu', 'Xaviera', 'Yoga', 'Zahra', 'Arif', 'Bunga', 'Cahya', 'Desi',
            'Eka', 'Fadli', 'Gandi', 'Hilda', 'Irfan', 'Jasmine', 'Krisna', 'Luki', 'Mira', 'Novi',
            'Oki', 'Prima', 'Qori', 'Rizki', 'Sinta', 'Tito', 'Uli', 'Vira', 'Widi', 'Yanto'
        ];

        $lastNames = [
            'Pratama', 'Santoso', 'Putri', 'Wijaya', 'Kusuma', 'Permata', 'Saputra', 'Lestari',
            'Sari', 'Nugroho', 'Wibowo', 'Rahayu', 'Susanto', 'Hartono', 'Setiawan', 'Anggraini',
            'Firmansyah', 'Hidayat', 'Ramadhan', 'Suryanto', 'Maharani', 'Nurfadilah', 'Maulana',
            'Syahputra', 'Syahputri', 'Rachman', 'Hakim', 'Adiputra', 'Salsabila', 'Rizqullah',
            'Azhari', 'Azzahra', 'Firdaus', 'Aisyah', 'Hasanah', 'Kamila', 'Nabila', 'Zahira'
        ];

        $majors = [
            'Teknik Informatika', 'Sistem Informasi', 'Ilmu Komunikasi', 'Manajemen', 'Akuntansi',
            'Teknik Sipil', 'Arsitektur', 'Psikologi', 'Hukum', 'Kedokteran',
            'Farmasi', 'Kesehatan Masyarakat', 'Gizi', 'Keperawatan', 'Kebidanan',
            'Pendidikan Bahasa Inggris', 'Pendidikan Matematika', 'Pendidikan Guru SD',
            'Ekonomi Pembangunan', 'Ilmu Pemerintahan', 'Hubungan Internasional',
            'Sosiologi', 'Antropologi', 'Sastra Indonesia', 'Desain Komunikasi Visual',
            'Teknik Elektro', 'Teknik Mesin', 'Teknik Industri', 'Agroteknologi', 'Agribisnis'
        ];

        $universities = University::all();

        if ($universities->isEmpty()) {
            echo "  ERROR: Tidak ada universities!\n";
            return;
        }

        // buat 700 students dengan distribusi merata ke semua universitas
        for ($i = 0; $i < 700; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            // Tambahkan index $i untuk memastikan username unique
            $username = strtolower($firstName . $lastName . ($i + 1000));
            $university = $universities->random();

            // generate email domain dari kode universitas
            $emailDomain = $this->getUniversityEmailDomain($university->code);
            $email = $username . '@' . $emailDomain;
            
            // buat user
            $user = User::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make('password123'),
                'user_type' => 'student',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // buat student
            Student::create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'university_id' => $university->id,
                'major' => $majors[array_rand($majors)],
                'nim' => rand(1000000000, 9999999999),
                'semester' => rand(1, 8),
                'phone' => '+628' . rand(1000000000, 9999999999),
            ]);
        }
        
        echo "  -> " . Student::count() . " students berhasil dibuat\n";
    }

    /**
     * seeding institutions dummy (80 institutions untuk lebih realistis)
     * DISTRIBUSI OTOMATIS ke semua provinsi dari database
     * FIXED: nama institusi yang credible dan realistis
     */
    private function seedInstitutions(): void
    {
        echo "seeding institutions...\n";
        
        // definisi institusi dengan nama yang credible
        $institutionsData = [
            // dinas kesehatan
            ['name' => 'Dinas Kesehatan Kabupaten Bogor', 'type' => 'Dinas Kesehatan', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Dr. H. Bambang Suryanto, M.Kes', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Kesehatan Kota Bandung', 'type' => 'Dinas Kesehatan', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Dr. Siti Nurhaliza, S.Km, M.Kes', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Kesehatan Kabupaten Semarang', 'type' => 'Dinas Kesehatan', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Dr. Ahmad Fauzi, Sp.PD', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Kesehatan Kota Surabaya', 'type' => 'Dinas Kesehatan', 'province_hint' => 'Jawa Timur', 'pic_name' => 'dr. Rita Kusuma, M.P.H', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Kesehatan Kabupaten Malang', 'type' => 'Dinas Kesehatan', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Dr. Hendra Wijaya, M.Kes', 'pic_position' => 'Kepala Dinas'],
            
            // dinas pendidikan
            ['name' => 'Dinas Pendidikan Kabupaten Bekasi', 'type' => 'Dinas Pendidikan', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Drs. H. Eko Prasetyo, M.Pd', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Pendidikan Kota Yogyakarta', 'type' => 'Dinas Pendidikan', 'province_hint' => 'Yogyakarta', 'pic_name' => 'Prof. Dr. Dewi Lestari, M.Pd', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Pendidikan Kabupaten Klaten', 'type' => 'Dinas Pendidikan', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Drs. Joko Santoso, M.M', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Pendidikan Kota Medan', 'type' => 'Dinas Pendidikan', 'province_hint' => 'Sumatera Utara', 'pic_name' => 'Dr. Maya Anggraini, S.Pd, M.Pd', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Pendidikan Kabupaten Tangerang', 'type' => 'Dinas Pendidikan', 'province_hint' => 'Banten', 'pic_name' => 'H. Budi Hermawan, S.Pd, M.M', 'pic_position' => 'Kepala Dinas'],
            
            // dinas pertanian
            ['name' => 'Dinas Pertanian Kabupaten Karawang', 'type' => 'Dinas Pertanian', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Ir. H. Agus Hartono, M.P', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Pertanian Dan Pangan Kota Semarang', 'type' => 'Dinas Pertanian', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Ir. Nina Rahayu, M.Si', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Pertanian Kabupaten Gresik', 'type' => 'Dinas Pertanian', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Ir. Dedi Kurniawan, M.M', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Ketahanan Pangan Dan Pertanian Kabupaten Garut', 'type' => 'Dinas Pertanian', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Dr. Ir. Ratna Wijaya, M.Agr', 'pic_position' => 'Kepala Dinas'],
            
            // dinas pariwisata
            ['name' => 'Dinas Pariwisata Dan Kebudayaan Kabupaten Badung', 'type' => 'Dinas Pariwisata', 'province_hint' => 'Bali', 'pic_name' => 'I Made Adi Putra, S.ST.Par, M.M', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Pariwisata Kota Yogyakarta', 'type' => 'Dinas Pariwisata', 'province_hint' => 'Yogyakarta', 'pic_name' => 'Dra. Sinta Permata, M.Par', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Pariwisata Kabupaten Lombok Barat', 'type' => 'Dinas Pariwisata', 'province_hint' => 'Nusa Tenggara Barat', 'pic_name' => 'H. Rizki Ramadhan, S.E, M.M', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Kebudayaan Dan Pariwisata Kota Bandung', 'type' => 'Dinas Pariwisata', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Dr. Lina Marlina, S.Sn, M.Sn', 'pic_position' => 'Kepala Dinas'],
            
            // dinas sosial
            ['name' => 'Dinas Sosial Kabupaten Bantul', 'type' => 'Dinas Sosial', 'province_hint' => 'Yogyakarta', 'pic_name' => 'Drs. H. Hadi Suryanto, M.Si', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Sosial Kota Surakarta', 'type' => 'Dinas Sosial', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Hj. Fitri Handayani, S.Sos, M.A.P', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Sosial Pemberdayaan Perempuan Dan Perlindungan Anak Kabupaten Sidoarjo', 'type' => 'Dinas Sosial', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Dra. Diana Putri, M.M', 'pic_position' => 'Kepala Dinas'],
            
            // dinas lingkungan hidup
            ['name' => 'Dinas Lingkungan Hidup Kabupaten Bogor', 'type' => 'Dinas Lingkungan Hidup', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Ir. Wahyu Nugroho, M.T', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Lingkungan Hidup Kota Semarang', 'type' => 'Dinas Lingkungan Hidup', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Dr. Ir. Ahmad Yani, M.Sc', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Lingkungan Hidup Dan Kehutanan Kabupaten Banyuwangi', 'type' => 'Dinas Lingkungan Hidup', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Ir. Sari Kusuma, M.T', 'pic_position' => 'Kepala Dinas'],
            
            // puskesmas
            ['name' => 'Puskesmas Cibinong', 'type' => 'Puskesmas', 'province_hint' => 'Jawa Barat', 'pic_name' => 'dr. Rina Anggraini', 'pic_position' => 'Kepala Puskesmas'],
            ['name' => 'Puskesmas Depok Jaya', 'type' => 'Puskesmas', 'province_hint' => 'Jawa Barat', 'pic_name' => 'dr. Teguh Santoso, M.Kes', 'pic_position' => 'Kepala Puskesmas'],
            ['name' => 'Puskesmas Mlati I', 'type' => 'Puskesmas', 'province_hint' => 'Yogyakarta', 'pic_name' => 'dr. Ulfa Rahayu', 'pic_position' => 'Kepala Puskesmas'],
            ['name' => 'Puskesmas Tlogosari Kulon', 'type' => 'Puskesmas', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'dr. Vina Lestari, M.P.H', 'pic_position' => 'Kepala Puskesmas'],
            ['name' => 'Puskesmas Kenjeran', 'type' => 'Puskesmas', 'province_hint' => 'Jawa Timur', 'pic_name' => 'dr. Indra Wijaya', 'pic_position' => 'Kepala Puskesmas'],
            
            // pemerintah desa
            ['name' => 'Pemerintah Desa Cikarang Barat', 'type' => 'Pemerintah Desa', 'province_hint' => 'Jawa Barat', 'pic_name' => 'H. Ahmad Fauzi, S.Sos', 'pic_position' => 'Kepala Desa'],
            ['name' => 'Pemerintah Desa Wanasari', 'type' => 'Pemerintah Desa', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Drs. Budi Santoso', 'pic_position' => 'Kepala Desa'],
            ['name' => 'Pemerintah Desa Karangploso', 'type' => 'Pemerintah Desa', 'province_hint' => 'Jawa Timur', 'pic_name' => 'H. Eko Prasetyo, S.Pd', 'pic_position' => 'Kepala Desa'],
            ['name' => 'Pemerintah Desa Cangkringan', 'type' => 'Pemerintah Desa', 'province_hint' => 'Yogyakarta', 'pic_name' => 'Ir. Hadi Suryanto', 'pic_position' => 'Kepala Desa'],
            ['name' => 'Pemerintah Desa Pandaan', 'type' => 'Pemerintah Desa', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Drs. Joko Widodo', 'pic_position' => 'Kepala Desa'],
            
            // kelurahan
            ['name' => 'Kelurahan Kebayoran Baru', 'type' => 'Kelurahan', 'province_hint' => 'Jakarta', 'pic_name' => 'H. Bambang Setiawan, S.IP', 'pic_position' => 'Lurah'],
            ['name' => 'Kelurahan Menteng', 'type' => 'Kelurahan', 'province_hint' => 'Jakarta', 'pic_name' => 'Hj. Dewi Lestari, S.Sos', 'pic_position' => 'Lurah'],
            ['name' => 'Kelurahan Cicadas', 'type' => 'Kelurahan', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Drs. Rizki Ramadhan', 'pic_position' => 'Lurah'],
            ['name' => 'Kelurahan Gumuruh', 'type' => 'Kelurahan', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Hj. Nina Rahayu, S.Sos, M.Si', 'pic_position' => 'Lurah'],
            
            // kecamatan
            ['name' => 'Kecamatan Ciputat', 'type' => 'Kecamatan', 'province_hint' => 'Banten', 'pic_name' => 'Drs. H. Agus Hartono, M.M', 'pic_position' => 'Camat'],
            ['name' => 'Kecamatan Pamulang', 'type' => 'Kecamatan', 'province_hint' => 'Banten', 'pic_name' => 'H. Dedi Kurniawan, S.IP, M.Si', 'pic_position' => 'Camat'],
            ['name' => 'Kecamatan Ngaliyan', 'type' => 'Kecamatan', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Drs. Hendra Kusuma', 'pic_position' => 'Camat'],
            ['name' => 'Kecamatan Lowokwaru', 'type' => 'Kecamatan', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Dr. Ir. Ratna Wijaya, M.T', 'pic_position' => 'Camat'],
            
            // ngo & yayasan
            ['name' => 'Yayasan Dharma Bhakti Nusantara', 'type' => 'NGO', 'province_hint' => 'Jakarta', 'pic_name' => 'Prof. Dr. Maya Anggraini, M.A', 'pic_position' => 'Ketua Yayasan'],
            ['name' => 'Yayasan Peduli Indonesia Sejahtera', 'type' => 'NGO', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Dr. H. Ahmad Yani, S.H, M.H', 'pic_position' => 'Direktur Eksekutif'],
            ['name' => 'Lembaga Pengembangan Masyarakat Desa', 'type' => 'NGO', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Dra. Siti Nurhaliza, M.Pd', 'pic_position' => 'Direktur Program'],
            ['name' => 'Yayasan Anak Bangsa Mandiri', 'type' => 'NGO', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Dr. Fitri Handayani, S.Psi, M.Psi', 'pic_position' => 'Ketua Yayasan'],
            ['name' => 'Lembaga Swadaya Masyarakat Cinta Alam', 'type' => 'NGO', 'province_hint' => 'Yogyakarta', 'pic_name' => 'Ir. Wahyu Nugroho, M.T', 'pic_position' => 'Koordinator Wilayah'],
            
            // dinas perindustrian & perdagangan
            ['name' => 'Dinas Perindustrian Dan Perdagangan Kabupaten Bandung', 'type' => 'Dinas Perindustrian', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Ir. H. Budi Hermawan, M.M', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Perdagangan Kota Surabaya', 'type' => 'Dinas Perdagangan', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Drs. Joko Santoso, M.E', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Koperasi Usaha Kecil Dan Menengah Kota Yogyakarta', 'type' => 'Dinas Koperasi', 'province_hint' => 'Yogyakarta', 'pic_name' => 'Dra. Diana Putri, M.M', 'pic_position' => 'Kepala Dinas'],
            
            // bpbd
            ['name' => 'Badan Penanggulangan Bencana Daerah Kabupaten Bantul', 'type' => 'BPBD', 'province_hint' => 'Yogyakarta', 'pic_name' => 'Drs. H. Hadi Suryanto, M.AP', 'pic_position' => 'Kepala Pelaksana'],
            ['name' => 'Badan Penanggulangan Bencana Daerah Kabupaten Garut', 'type' => 'BPBD', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Ir. Rizki Ramadhan, M.T', 'pic_position' => 'Kepala Pelaksana'],
            ['name' => 'Badan Penanggulangan Bencana Daerah Kota Semarang', 'type' => 'BPBD', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Dr. Ir. Eko Prasetyo, M.Sc', 'pic_position' => 'Kepala Pelaksana'],
            
            // posyandu
            ['name' => 'Posyandu Melati Desa Cibitung', 'type' => 'Posyandu', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Hj. Siti Aminah, A.Md.Keb', 'pic_position' => 'Koordinator Posyandu'],
            ['name' => 'Posyandu Mawar Kelurahan Bugangan', 'type' => 'Posyandu', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Lina Marlina, S.ST', 'pic_position' => 'Ketua Posyandu'],
            ['name' => 'Posyandu Teratai Desa Karangploso', 'type' => 'Posyandu', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Ulfa Rahayu, A.Md.Keb', 'pic_position' => 'Koordinator Posyandu'],
            
            // tambahan institusi dari berbagai daerah
            ['name' => 'Dinas Pemberdayaan Masyarakat Dan Desa Kabupaten Sleman', 'type' => 'Dinas Pemberdayaan', 'province_hint' => 'Yogyakarta', 'pic_name' => 'Drs. H. Teguh Santoso, M.Si', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Komunikasi Dan Informatika Kota Bandung', 'type' => 'Dinas Komunikasi', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Dr. Vina Lestari, S.Kom, M.T', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Perikanan Kabupaten Pekalongan', 'type' => 'Dinas Perikanan', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Ir. H. Indra Wijaya, M.Si', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Perhubungan Kabupaten Pasuruan', 'type' => 'Dinas Perhubungan', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Ir. Ahmad Fauzi, M.T', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Peternakan Dan Kesehatan Hewan Kabupaten Sukabumi', 'type' => 'Dinas Peternakan', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Drh. H. Bambang Setiawan, M.Si', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Perpustakaan Dan Kearsipan Kota Solo', 'type' => 'Dinas Perpustakaan', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Dra. Hj. Rita Kusuma, M.Hum', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Perumahan Dan Kawasan Permukiman Kabupaten Sidoarjo', 'type' => 'Dinas Perumahan', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Ir. Dedi Kurniawan, M.T', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Penanaman Modal Dan Pelayanan Terpadu Satu Pintu Kabupaten Bekasi', 'type' => 'Dinas Penanaman Modal', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Drs. H. Hendra Kusuma, M.M', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Kelautan Dan Perikanan Kabupaten Jepara', 'type' => 'Dinas Kelautan', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Ir. Nina Rahayu, M.P', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Kepemudaan Olahraga Dan Pariwisata Kabupaten Banyumas', 'type' => 'Dinas Kepemudaan', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Drs. Wahyu Nugroho, M.Or', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Dinas Perkebunan Kabupaten Cianjur', 'type' => 'Dinas Perkebunan', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Ir. H. Joko Santoso, M.P', 'pic_position' => 'Kepala Dinas'],
            ['name' => 'Badan Pengelolaan Keuangan Dan Aset Daerah Kabupaten Karanganyar', 'type' => 'BPKAD', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Dr. H. Agus Hartono, S.E, M.M, Ak', 'pic_position' => 'Kepala Badan'],
            ['name' => 'Badan Kepegawaian Dan Pengembangan SDM Kabupaten Bojonegoro', 'type' => 'BKD', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Drs. Ahmad Yani, M.AP', 'pic_position' => 'Kepala Badan'],
            ['name' => 'Puskesmas Rawat Inap Kecamatan Pasar Minggu', 'type' => 'Puskesmas', 'province_hint' => 'Jakarta', 'pic_name' => 'dr. Sinta Permata, M.Kes', 'pic_position' => 'Kepala Puskesmas'],
            ['name' => 'Puskesmas Ciracas', 'type' => 'Puskesmas', 'province_hint' => 'Jakarta', 'pic_name' => 'dr. Maya Anggraini', 'pic_position' => 'Kepala Puskesmas'],
            ['name' => 'Yayasan Sosial Cahaya Harapan', 'type' => 'Yayasan Sosial', 'province_hint' => 'Jawa Barat', 'pic_name' => 'Dr. H. Budi Hermawan, S.Sos, M.Si', 'pic_position' => 'Ketua Yayasan'],
            ['name' => 'Lembaga Pemberdayaan Perempuan Dan Anak', 'type' => 'NGO', 'province_hint' => 'Jawa Timur', 'pic_name' => 'Hj. Fitri Handayani, S.Psi, M.A', 'pic_position' => 'Direktur Eksekutif'],
            ['name' => 'Yayasan Pendidikan Islam Al Ikhlas', 'type' => 'Lembaga Pendidikan', 'province_hint' => 'Jakarta', 'pic_name' => 'Prof. Dr. H. Hadi Suryanto, M.Pd.I', 'pic_position' => 'Ketua Yayasan'],
            ['name' => 'Yayasan Kesejahteraan Sosial Nurul Iman', 'type' => 'Yayasan Sosial', 'province_hint' => 'Jawa Tengah', 'pic_name' => 'Hj. Diana Putri, S.Ag, M.Si', 'pic_position' => 'Direktur'],
            ['name' => 'Lembaga Kajian Dan Pengembangan Sumberdaya Manusia', 'type' => 'NGO', 'province_hint' => 'Yogyakarta', 'pic_name' => 'Dr. Rizki Ramadhan, S.Sos, M.A', 'pic_position' => 'Direktur Eksekutif'],
            ['name' => 'Pemerintah Desa Sumbersari', 'type' => 'Pemerintah Desa', 'province_hint' => 'Jawa Timur', 'pic_name' => 'H. Teguh Santoso, S.Pd', 'pic_position' => 'Kepala Desa'],
            ['name' => 'Kelurahan Tegal Parang', 'type' => 'Kelurahan', 'province_hint' => 'Jakarta', 'pic_name' => 'Drs. H. Indra Wijaya', 'pic_position' => 'Lurah'],
            ['name' => 'Kecamatan Kasihan', 'type' => 'Kecamatan', 'province_hint' => 'Yogyakarta', 'pic_name' => 'Drs. H. Eko Prasetyo, M.M', 'pic_position' => 'Camat'],
        ];
        
        $provinces = Province::all();
        
        if ($provinces->isEmpty()) {
            echo "  ERROR: Tidak ada data provinces!\n";
            echo "  Jalankan ProvincesRegenciesSeeder terlebih dahulu!\n";
            return;
        }
        
        echo "  -> tersedia " . $provinces->count() . " provinsi dari BPS\n";
        
        $createdCount = 0;
        
        foreach ($institutionsData as $instData) {
            // cari province berdasarkan hint
            $province = null;
            if (isset($instData['province_hint'])) {
                $province = $provinces->first(function($p) use ($instData) {
                    return stripos($p->name, $instData['province_hint']) !== false;
                });
            }
            
            // fallback ke random province jika tidak ditemukan
            if (!$province) {
                $province = $provinces->random();
            }
            
            // ambil regency dari province tersebut
            $regencies = Regency::where('province_id', $province->id)->get();
            
            if ($regencies->isEmpty()) {
                // skip jika tidak ada regency
                continue;
            }
            
            $regency = $regencies->random();
            
            // generate username dan email yang bersih dari nama institusi
            $cleanName = preg_replace('/[^a-zA-Z0-9\s]/', '', $instData['name']); // hapus karakter spesial
            $cleanName = strtolower(str_replace(' ', '', $cleanName));
            $username = substr($cleanName, 0, 30) . rand(1, 99); // batasi panjang username
            $email = $username . '@' . strtolower(str_replace(' ', '', $instData['type'])) . '.go.id';
            
            // ambil nama jalan yang sesuai dengan kabupaten/kota
            $streetNames = [
                'Jl. Merdeka',
                'Jl. Sudirman',
                'Jl. Ahmad Yani',
                'Jl. Diponegoro',
                'Jl. Gajah Mada',
                'Jl. Veteran',
                'Jl. Pemuda',
                'Jl. RA Kartini',
                'Jl. Cut Nyak Dien',
                'Jl. Soekarno Hatta'
            ];
            
            $street = $streetNames[array_rand($streetNames)];
            $address = $street . ' No. ' . rand(1, 200) . ', ' . $regency->name . ', ' . $province->name;
            
            // buat user
            $user = User::create([
                'name' => ucwords($instData['name']),
                'email' => $email,
                'username' => $username,
                'password' => Hash::make('password123'),
                'user_type' => 'institution',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // buat institution
            Institution::create([
                'user_id' => $user->id,
                'name' => ucwords($instData['name']),
                'type' => $instData['type'],
                'province_id' => $province->id,
                'regency_id' => $regency->id,
                'email' => $email,
                'address' => $address,
                'phone' => '+622' . rand(100000000, 999999999),
                'pic_name' => $instData['pic_name'],
                'pic_position' => $instData['pic_position'],
                'description' => 'Instansi Yang Bergerak Di Bidang ' . $instData['type'] . ' Dengan Fokus Pemberdayaan Masyarakat Dan Peningkatan Kualitas Hidup Masyarakat.',
                'is_verified' => true,
            ]);
            
            $createdCount++;
        }
        
        echo "  -> {$createdCount} institutions berhasil dibuat\n";
    }

    /**
     * generate email domain dari kode universitas
     */
    private function getUniversityEmailDomain(string $code): string
    {
        // mapping khusus untuk beberapa universitas besar
        return match($code) {
            'UI' => 'ui.ac.id',
            'ITB' => 'itb.ac.id',
            'IPB' => 'apps.ipb.ac.id',
            'UGM' => 'mail.ugm.ac.id',
            'UNPAD' => 'unpad.ac.id',
            'UNDIP' => 'students.undip.ac.id',
            'UNS' => 'student.uns.ac.id',
            'UNAIR' => 'unair.ac.id',
            'UB' => 'student.ub.ac.id',
            'ITS' => 'its.ac.id',
            'UNUD' => 'unud.ac.id',
            'UTM' => 'trunojoyo.ac.id',
            'UTU' => 'utu.ac.id',
            'UT' => 'ut.ac.id',
            'UNTAN' => 'untan.ac.id',
            'UNSYIAH' => 'unsyiah.ac.id',
            'USU' => 'usu.ac.id',
            'UNTIRTA' => 'untirta.ac.id',
            'USULTRA' => 'usultra.ac.id',
            'UNSULBAR' => 'unsulbar.ac.id',
            'UNS19' => 'unusa.ac.id',
            'UNSAM' => 'unsam.ac.id',
            'UNSIQ' => 'unsiq.ac.id',
            'UNIMUDA' => 'unimuda.ac.id',
            'UPI' => 'upi.edu',
            'UNDIKSHA' => 'undiksha.ac.id',
            'UPNVJT' => 'upnjatim.ac.id',
            'UNPATTI' => 'unpatti.ac.id',
            'UNIPAS' => 'unipas.ac.id',
            'UPS' => 'upstegal.ac.id',
            'UPR' => 'upr.ac.id',
            'UNUHA' => 'unuha.ac.id',
            'UNHM' => 'unhm.ac.id',
            'UNY' => 'uny.ac.id',
            'UNESA' => 'unesa.ac.id',
            'UNNES' => 'students.unnes.ac.id',
            'UNP' => 'unp.ac.id',
            'UNIMED' => 'unimed.ac.id',
            'UNIMA' => 'unima.ac.id',
            'UM' => 'um.ac.id',
            'UNM' => 'unm.ac.id',
            'UNJ' => 'unj.ac.id',
            'UNG' => 'ung.ac.id',
            'UMNAW' => 'umnaw.ac.id',
            'UMI' => 'umi.ac.id',
            'UNMUS' => 'unmus.ac.id',
            'UMK' => 'umk.ac.id',
            'UNMUL' => 'unmul.ac.id',
            'UMSINJAI' => 'umsinjai.ac.id',
            'UMPWR' => 'umpwr.ac.id',
            'UMPO' => 'umpo.ac.id',
            'UMPALANGKA' => 'umpalangka.ac.id',
            'UMKOLAKA' => 'umkolaka.ac.id',
            'UMBUTON' => 'umbuton.ac.id',
            'UNISMUH' => 'unismuh.ac.id',
            'UNRAM' => 'unram.ac.id',
            'UNIMAL' => 'unimal.ac.id',
            'UNIRA' => 'unira.ac.id',
            'UNIMOR' => 'unimor.ac.id',
            'UNILA' => 'unila.ac.id',
            'UNLABUHAN' => 'unlabuhan.ac.id',
            'UNIKU' => 'uniku.ac.id',
            'UKITORAJA' => 'ukitoraja.ac.id',
            'UKIM' => 'ukim.ac.id',
            'UNIKOM' => 'unikom.ac.id',
            'UNKHAIR' => 'unkhair.ac.id',
            'UNJA' => 'unja.ac.id',
            'UIM' => 'uim.ac.id',
            'UIT' => 'uit.ac.id',
            'UIKA' => 'uika-bogor.ac.id',
            'UNHAS' => 'unhas.ac.id',
            'UHO' => 'uho.ac.id',
            'UFAMIKA' => 'ufamika.ac.id',
            'UEU' => 'esaunggul.ac.id',
            'UDA' => 'darmaagung.ac.id',
            'UBY' => 'boyolali.ac.id',
            'UBT' => 'borneo.ac.id',
            'UAJM' => 'uajm.ac.id',
            'UNAND' => 'unand.ac.id',
            'UAM' => 'almarisah.ac.id',
            'UAI' => 'uai.ac.id',
            'UNASMAN' => 'unasman.ac.id',
            'UNAYA' => 'unaya.ac.id',
            'UNTAG' => 'untag-smd.ac.id',
            'UNTAD' => 'untad.ac.id',
            'UBI' => 'bimainter.ac.id',
            'UNWAM' => 'unwam.ac.id',
            'UINTA' => 'uinta.ac.id',
            'UIN SUSKA' => 'uin-suska.ac.id',
            'UNSOED' => 'unsoed.ac.id',
            'UBB' => 'ubb.ac.id',
            'UIN SATU' => 'uinsatu.ac.id',      
            'ITERA' => 'itera.ac.id',
            'ITK' => 'itk.ac.id',
            'ITSNUPKL' => 'itsnupekalongan.ac.id',
            'ITBM' => 'itbm.ac.id',
            'ISBI BDG' => 'isbi.ac.id',
            'ISBI ACEH' => 'isbiaceh.ac.id',
            'IPDKI' => 'ipdki.ac.id',
            'IAHN' => 'iahn.ac.id',
            'STKIP ALHIK' => 'stkipalhikmah.ac.id',
            'STIMI' => 'stimi.ac.id',
            'STIKES MAROS' => 'stikesmaros.ac.id',
            'STIKMA' => 'stikma.ac.id',
            'STIEM' => 'stiem.ac.id',
            'POLBOM' => 'polbombana.ac.id',        
            'UIN JKT' => 'uinjkt.ac.id',
            'UIN SGD' => 'uinsgd.ac.id',
            'UIN WALISONGO' => 'walisongo.ac.id',
            'UIN SUKA' => 'uin-suka.ac.id',           
            'USAKTI' => 'trisakti.ac.id',
            'UMB' => 'mercubuana.ac.id',
            'UNPAS' => 'unpas.ac.id',
            'UII' => 'uii.ac.id',
            'UNISSULA' => 'unissula.ac.id',
            'UNIMUS' => 'unimus.ac.id',
            'UMT' => 'umt.ac.id',
            
            // fallback untuk kode yang tidak terdaftar - generate dari kode
            default => strtolower($code) . '.ac.id',
        };
    }
}