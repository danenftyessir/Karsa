<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Institution;

class ProfileController extends Controller
{
    protected $storageService;

    public function __construct(SupabaseStorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * tampilkan halaman profil institution
     */
    public function index()
    {
        $institution = auth()->user()->institution;
        $user = auth()->user();
        
        // hitung statistik untuk dashboard profil
        $stats = [
            'total_problems' => $institution->problems()->count(),
            'active_problems' => $institution->problems()->where('status', 'open')->count(),
            'completed_problems' => $institution->problems()->where('status', 'completed')->count(),
            'total_projects' => $institution->projects()->count(),
            'active_projects' => $institution->projects()->where('status', 'active')->count(),
            'completed_projects' => $institution->projects()->where('status', 'completed')->count(),
        ];
        
        return view('institution.profile.index', compact('institution', 'user', 'stats'));
    }

    /**
     * tampilkan form edit profil
     */
    public function edit()
    {
        $institution = auth()->user()->institution;
        $user = auth()->user();
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::where('province_id', $institution->province_id)->orderBy('name')->get();
        
        // definisi tipe institusi
        $institutionTypes = [
            'pemerintah_desa' => 'pemerintah desa',
            'dinas' => 'dinas pemerintahan',
            'ngo' => 'NGO / lembaga swadaya masyarakat',
            'puskesmas' => 'puskesmas',
            'sekolah' => 'sekolah',
            'perguruan_tinggi' => 'perguruan tinggi',
            'lainnya' => 'lainnya'
        ];
        
        return view('institution.profile.edit', compact('institution', 'user', 'provinces', 'regencies', 'institutionTypes'));
    }

    /**
     * update profil institution
     */
    public function update(Request $request)
    {
        try {
            $institution = auth()->user()->institution;
            $user = auth()->user();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string',
                'province_id' => 'required|exists:provinces,id',
                'regency_id' => 'required|exists:regencies,id',
                'address' => 'required|string',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'pic_name' => 'required|string|max:255',
                'pic_position' => 'required|string|max:255',
                'description' => 'nullable|string',
                'website' => 'nullable|url',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // handle upload logo jika ada
            $logoPath = $institution->logo_path;
            
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                
                // hapus logo lama jika ada
                if ($logoPath) {
                    $this->storageService->delete($logoPath);
                }
                
                // upload logo baru menggunakan SupabaseStorageService
                $uploadedPath = $this->storageService->uploadInstitutionLogo($file, $institution->id);
                
                // jika upload berhasil, gunakan path baru
                if ($uploadedPath) {
                    $logoPath = $uploadedPath;
                    Log::info("Logo berhasil diupload untuk institution ID {$institution->id}");
                } else {
                    // tetap gunakan logo lama jika upload gagal
                    $logoPath = $institution->logo_path;
                    Log::warning("Gagal upload logo untuk institution ID {$institution->id}, menggunakan logo lama");
                }
            }

            // update data institution
            $institution->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'province_id' => $validated['province_id'],
                'regency_id' => $validated['regency_id'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'pic_name' => $validated['pic_name'],
                'pic_position' => $validated['pic_position'],
                'description' => $validated['description'] ?? null,
                'website' => $validated['website'] ?? null,
                'logo_path' => $logoPath,
            ]);

            // update user email jika berubah
            if ($user->email !== $validated['email']) {
                $user->update([
                    'email' => $validated['email'],
                    'email_verified_at' => null, // reset verifikasi email
                ]);
            }
            
            Log::info("Profil berhasil diupdate untuk institution ID {$institution->id}");

            return redirect()->route('institution.profile.index')
                            ->with('success', 'Profil Berhasil Diperbarui!');
                            
        } catch (\Exception $e) {
            Log::error("Error saat update profil institution: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui profil. Silakan coba lagi.');
        }
    }

    /**
     * update password
     */
    public function updatePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            $user = auth()->user();

            // cek password lama
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password Lama Tidak Sesuai']);
            }

            // update password
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
            
            Log::info("Password berhasil diupdate untuk user ID {$user->id}");

            return back()->with('success', 'Password Berhasil Diperbarui!');
            
        } catch (\Exception $e) {
            Log::error("Error saat update password: " . $e->getMessage());
            
            return back()->with('error', 'Terjadi kesalahan saat memperbarui password. Silakan coba lagi.');
        }
    }

    /**
     * tampilkan profil publik institution
     */
    public function showPublic($id)
    {
        // untuk saat ini gunakan id, nanti bisa diubah ke slug
        $institution = Institution::with(['user', 'province', 'regency', 'problems' => function($q) {
            $q->where('status', 'open')->orWhere('status', 'completed');
        }])->findOrFail($id);
        
        // hitung statistik untuk profil publik
        $stats = [
            'total_problems' => $institution->problems()->count(),
            'active_problems' => $institution->problems()->where('status', 'open')->count(),
            'completed_problems' => $institution->problems()->where('status', 'completed')->count(),
            'total_projects' => $institution->projects()->count(),
            'active_projects' => $institution->projects()->where('status', 'active')->count(),
            'completed_projects' => $institution->projects()->where('status', 'completed')->count(),
        ];
        
        return view('institution.profile.public', compact('institution', 'stats'));
    }
}