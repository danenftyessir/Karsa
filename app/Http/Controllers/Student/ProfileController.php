<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStudentProfileRequest;
use App\Http\Requests\UpdateStudentPasswordRequest;
use App\Services\SupabaseStorageService;
use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * controller untuk mengelola profil mahasiswa (termasuk portfolio)
 * 
 * path: app/Http/Controllers/Student/ProfileController.php
 */
class ProfileController extends Controller
{
    protected $storageService;
    protected $portfolioService;

    public function __construct(
        SupabaseStorageService $storageService,
        PortfolioService $portfolioService
    ) {
        $this->storageService = $storageService;
        $this->portfolioService = $portfolioService;

        // Note: Middleware sudah diterapkan di routes/web.php
        // - Private routes (index, edit, update, dll) dilindungi oleh middleware auth & check.user.type:student di route group
        // - Public route (publicView) tidak memerlukan auth dan didefinisikan terpisah di luar route group
    }

    /**
     * tampilkan halaman profil mahasiswa (private view)
     * menampilkan info pribadi + portfolio
     */
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        // hitung statistik untuk profil
        $stats = [
            'total_projects' => $student->projects()->count(),
            'active_projects' => $student->projects()->where('status', 'active')->count(),
            'completed_projects' => $student->projects()->where('status', 'completed')->count(),
            'total_applications' => $student->applications()->count(),
            'pending_applications' => $student->applications()->where('status', 'pending')->count(),
        ];

        // ambil data portfolio
        $portfolioData = $this->portfolioService->getPortfolioData($student->id);

        return view('student.profile.index', array_merge(
            compact('user', 'student', 'stats'),
            $portfolioData
        ));
    }

    /**
     * tampilkan public profile/portfolio (dapat diakses siapa saja)
     */
    public function publicView($username)
    {
        try {
            $portfolioData = $this->portfolioService->getPublicPortfolio($username);
            
            return view('student.profile.public', $portfolioData);
        } catch (\Exception $e) {
            Log::error("Error loading public profile for username {$username}: " . $e->getMessage());
            abort(404, 'Profil tidak ditemukan');
        }
    }

    /**
     * tampilkan halaman edit profil
     */
    public function edit()
    {
        $user = Auth::user();
        $student = $user->student;
        
        // ambil data universitas untuk dropdown
        $universities = \App\Models\University::orderBy('name')->get();

        return view('student.profile.edit', compact('user', 'student', 'universities'));
    }

    /**
     * update profil mahasiswa
     */
    public function update(UpdateStudentProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $student = $user->student;
            
            // update data user
            $user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'username' => $request->username ?? $user->username,
            ]);

            // handle upload foto profil jika ada
            $profilePhotoPath = $student->profile_photo_path;

            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');

                // hapus foto lama jika ada
                if ($profilePhotoPath) {
                    $this->storageService->delete($profilePhotoPath);
                }

                $uploadedPath = $this->storageService->uploadProfilePhoto($file, $student->id);

                // jika upload berhasil, gunakan path baru. Jika gagal, tetap gunakan foto lama
                if ($uploadedPath) {
                    $profilePhotoPath = $uploadedPath;
                    Log::info("Foto profil berhasil diupload untuk student ID {$student->id}");
                } else {
                    // tetap gunakan foto lama jika upload gagal
                    $profilePhotoPath = $student->profile_photo_path;
                    Log::warning("Gagal upload foto profil untuk student ID {$student->id}, menggunakan foto lama");
                }
            }

            // filter stories yang kosong
            $stories = $request->stories ?? [];
            if (is_array($stories)) {
                $stories = array_values(array_filter($stories, function($story) {
                    return !empty(trim($story));
                }));
            }

            // filter skills yang kosong
            $skills = $request->skills ?? [];
            if (is_array($skills)) {
                $skills = array_values(array_filter($skills, function($skill) {
                    return !empty(trim($skill));
                }));
            }

            // filter interests yang kosong
            $interests = $request->interests ?? [];
            if (is_array($interests)) {
                $interests = array_values(array_filter($interests, function($interest) {
                    return !empty(trim($interest));
                }));
            }

            $student->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'nim' => $request->nim,
                'university_id' => $request->university_id,
                'major' => $request->major,
                'semester' => $request->semester,
                'phone' => $request->whatsapp_number, // field database adalah 'phone'
                'profile_photo_path' => $profilePhotoPath,
                'bio' => $request->bio,
                'stories' => $stories, // cerita & pengalaman mahasiswa (array)
                'skills' => $skills, // skills mahasiswa (array)
                'interests' => $interests, // interests mahasiswa (array)
            ]);
            
            Log::info("Profil berhasil diupdate untuk student ID {$student->id}");
            
            return redirect()->route('student.profile.index')
                ->with('success', 'Profil berhasil diperbarui!');
                
        } catch (\Exception $e) {
            Log::error("Error saat update profil student: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            Log::error("Request data: " . json_encode($request->except(['profile_photo'])));

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage());
        }
    }

    /**
     * update password student
     */
    public function updatePassword(UpdateStudentPasswordRequest $request)
    {
        try {
            $user = Auth::user();            
            $user->password = Hash::make($request->password);
            $user->save();
            
            Log::info("Password berhasil diupdate untuk user ID {$user->id}");
            
            // logout user setelah ganti password
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('success', 'Password Berhasil Diperbarui! Silakan Login Dengan Password Baru Anda.');
                
        } catch (\Exception $e) {
            Log::error("Error saat update password: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return back()->with('error', 'Terjadi Kesalahan Saat Memperbarui Password. Silakan Coba Lagi.');
        }
    }

    /**
     * toggle project visibility di portfolio
     */
    public function toggleProjectVisibility(Request $request, $projectId)
    {
        $student = Auth::user()->student;
        
        // pastikan project milik student ini
        $project = \App\Models\Project::where('id', $projectId)
                                      ->where('student_id', $student->id)
                                      ->firstOrFail();

        try {
            $updatedProject = $this->portfolioService->toggleProjectVisibility($projectId);

            return response()->json([
                'success' => true,
                'message' => $updatedProject->is_portfolio_visible 
                    ? 'Proyek ditampilkan di portfolio' 
                    : 'Proyek disembunyikan dari portfolio',
                'is_visible' => $updatedProject->is_portfolio_visible,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah visibility: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * get share link untuk portfolio
     */
    public function getShareLink()
    {
        $student = Auth::user()->student;
        $username = $this->portfolioService->generatePortfolioSlug($student);

        $shareUrl = route('profile.public', $username);

        return response()->json([
            'success' => true,
            'url' => $shareUrl,
            'username' => $username,
        ]);
    }

    // ========================================================================
    // PROFILE COMPLETENESS
    // ========================================================================

    /**
     * hitung persentase kelengkapan profil
     */
    public function getCompleteness()
    {
        $user = Auth::user();
        $student = $user->student;

        $fields = [
            'first_name' => ['weight' => 10, 'filled' => !empty($student->first_name)],
            'last_name' => ['weight' => 5, 'filled' => !empty($student->last_name)],
            'profile_photo' => ['weight' => 10, 'filled' => !empty($student->profile_photo_path)],
            'university' => ['weight' => 10, 'filled' => !empty($student->university_id)],
            'major' => ['weight' => 10, 'filled' => !empty($student->major)],
            'semester' => ['weight' => 5, 'filled' => !empty($student->semester)],
            'nim' => ['weight' => 5, 'filled' => !empty($student->nim)],
            'phone' => ['weight' => 10, 'filled' => !empty($student->phone)],
            'bio' => ['weight' => 10, 'filled' => !empty($student->bio)],
            'skills' => ['weight' => 15, 'filled' => !empty($student->skills) && count($student->skills) > 0],
            'interests' => ['weight' => 5, 'filled' => !empty($student->interests) && count($student->interests) > 0],
            'stories' => ['weight' => 5, 'filled' => !empty($student->stories) && count($student->stories) > 0],
        ];

        $totalWeight = 0;
        $filledWeight = 0;
        $missing = [];

        foreach ($fields as $name => $field) {
            $totalWeight += $field['weight'];
            if ($field['filled']) {
                $filledWeight += $field['weight'];
            } else {
                $missing[] = $name;
            }
        }

        $percentage = $totalWeight > 0 ? round(($filledWeight / $totalWeight) * 100) : 0;

        return response()->json([
            'success' => true,
            'percentage' => $percentage,
            'missing' => $missing,
            'suggestions' => $this->getCompletionSuggestions($missing),
        ]);
    }

    /**
     * dapatkan saran untuk melengkapi profil
     */
    private function getCompletionSuggestions($missing)
    {
        $suggestions = [
            'profile_photo' => 'Tambahkan foto profil untuk kesan pertama yang baik',
            'bio' => 'Tulis bio singkat yang menjelaskan siapa Anda',
            'skills' => 'Tambahkan skill untuk menarik perekrut',
            'phone' => 'Tambahkan nomor telepon agar mudah dihubungi',
            'university' => 'Pilih universitas Anda',
            'major' => 'Isi program studi Anda',
        ];

        return array_intersect_key($suggestions, array_flip($missing));
    }

    // ========================================================================
    // SHARE PROFILE WITH QR CODE
    // ========================================================================

    /**
     * generate QR code untuk profile
     */
    public function generateQR()
    {
        $user = Auth::user();
        $student = $user->student;
        $username = $this->portfolioService->generatePortfolioSlug($student);
        $profileUrl = route('profile.public', $username);

        // generate QR code sebagai data URL (menggunakan library sederhana atau API)
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($profileUrl);

        return response()->json([
            'success' => true,
            'profile_url' => $profileUrl,
            'qr_url' => $qrApiUrl,
            'username' => $username,
        ]);
    }

    // ========================================================================
    // EXPORT PROFILE TO PDF
    // ========================================================================

    /**
     * export profile ke PDF
     */
    public function exportPDF()
    {
        $user = Auth::user();
        $student = $user->student;

        // ambil data portfolio
        $portfolioData = $this->portfolioService->getPortfolioData($student->id);

        // render view ke HTML
        $html = view('student.profile.pdf', array_merge(
            compact('user', 'student'),
            $portfolioData
        ))->render();

        // untuk saat ini return HTML, nanti bisa integrasikan dengan dompdf atau snappy
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="profile-' . $student->first_name . '.html"');
    }
}