<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRegisterRequest;
use App\Http\Requests\InstitutionRegisterRequest;
use App\Models\User;
use App\Models\Student;
use App\Models\Institution;
use App\Models\University;
use App\Models\Province;
use App\Models\Regency;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;

/**
 * RegisterController
 * 
 * handle registrasi untuk student dan institution
 */
class RegisterController extends Controller
{
    protected $storageService;

    /**
     * constructor - inject SupabaseStorageService
     */
    public function __construct(SupabaseStorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * tampilkan halaman pilihan registrasi
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * alias untuk showRegistrationForm
     */
    public function showRegisterForm()
    {
        return $this->showRegistrationForm();
    }

    /**
     * tampilkan form registrasi student
     */
    public function showStudentForm()
    {
        // ambil data universities untuk dropdown
        $universities = University::orderBy('name', 'asc')->get();
        
        return view('auth.student-register', compact('universities'));
    }

    /**
     * alias untuk showStudentForm
     */
    public function showStudentRegisterForm()
    {
        return $this->showStudentForm();
    }

    /**
     * tampilkan form registrasi institution
     */
    public function showInstitutionForm()
    {
        // ambil data provinces untuk dropdown
        $provinces = Province::orderBy('name', 'asc')->get();
        
        // siapkan regencies collection kosong untuk kondisi awal
        $regencies = collect();
        
        // jika ada old input province_id (ketika validasi gagal), load regencies-nya
        if (old('province_id')) {
            $regencies = Regency::where('province_id', old('province_id'))
                               ->orderBy('name', 'asc')
                               ->get();
        }

        return view('auth.institution-register', compact('provinces', 'regencies'));
    }

    /**
     * alias untuk showInstitutionForm
     */
    public function showInstitutionRegisterForm()
    {
        return $this->showInstitutionForm();
    }

    /**
     * proses registrasi student
     * PERBAIKAN BUG: gunakan Supabase untuk upload foto dan refresh session setelah registrasi
     */
    public function registerStudent(StudentRegisterRequest $request)
    {
        try {
            // log incoming data untuk debug
            Log::info('Student registration attempt', [
                'email' => $request->email,
                'username' => $request->username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]);

            // gunakan database transaction untuk memastikan atomicity
            DB::beginTransaction();
            
            // buat user account
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'user_type' => 'student',
                'is_active' => true,
            ]);
            
            Log::info('User created successfully', ['user_id' => $user->id]);
            
            // buat student profile dengan data awal
            $student = Student::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'nim' => $request->nim,
                'university_id' => $request->university_id,
                'major' => $request->major,
                'semester' => $request->semester,
                'phone' => $request->whatsapp_number,
                'profile_photo_path' => null, // akan diupdate jika ada foto
            ]);
            
            Log::info('Student profile created', ['student_id' => $student->id]);
            
            // upload foto menggunakan SupabaseStorageService
            if ($request->hasFile('profile_photo')) {
                try {
                    $file = $request->file('profile_photo');
                    
                    // gunakan method uploadProfilePhoto dari SupabaseStorageService
                    $uploadedPath = $this->storageService->uploadProfilePhoto($file, $student->id);
                    
                    if ($uploadedPath) {
                        // update student profile dengan path foto yang baru
                        $student->update(['profile_photo_path' => $uploadedPath]);
                        Log::info('Profile photo uploaded successfully', [
                            'student_id' => $student->id,
                            'path' => $uploadedPath
                        ]);
                    } else {
                        Log::warning('Failed to upload profile photo for student ID: ' . $student->id);
                    }
                } catch (\Exception $e) {
                    Log::error('Error uploading profile photo: ' . $e->getMessage());
                    // tidak perlu throw exception, foto profil bersifat opsional
                }
            }
            
            DB::commit();
            
            // picu event bahwa user baru telah terdaftar (untuk kirim email verifikasi)
            event(new Registered($user));

            // log successful registration
            Log::info('Student registered successfully', [
                'user_id' => $user->id,
                'student_id' => $student->id,
                'email' => $user->email,
                'username' => $user->username
            ]);

            // auto-login user setelah registrasi berhasil
            Auth::login($user);
            
            // refresh session untuk memuat data terbaru termasuk foto profil
            // fresh() akan reload model dari database dengan semua relasinya
            $user = $user->fresh(['student']);
            Auth::setUser($user);

            // cek apakah request adalah AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi Berhasil! Selamat Datang Di KKN-GO.',
                    'redirect_url' => route('student.dashboard') // PERBAIKAN: gunakan redirect_url bukan redirect
                ], 200);
            }

            // redirect ke dashboard student dengan pesan sukses
            return redirect()
                ->route('student.dashboard')
                ->with('success', 'Selamat Datang Di KKN-GO! Akun Anda Berhasil Dibuat. Silakan Lengkapi Profil Dan Mulai Mencari Proyek KKN Yang Sesuai Dengan Minat Anda.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Student registration failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // cek apakah request adalah AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi Kesalahan Saat Registrasi. Silakan Coba Lagi.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
                ], 500);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }

    /**
     * proses registrasi institution
     */
    public function registerInstitution(InstitutionRegisterRequest $request)
    {
        try {
            // gunakan database transaction untuk memastikan atomicity
            DB::beginTransaction();
            
            // buat user account
            $user = User::create([
                'name' => $request->institution_name,
                'email' => $request->official_email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'user_type' => 'institution',
                'is_active' => true,
            ]);
            
            // buat institution profile dengan data awal
            $institution = Institution::create([
                'user_id' => $user->id,
                'name' => $request->institution_name,
                'type' => $request->institution_type,
                'address' => $request->address,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'email' => $request->official_email,
                'phone' => $request->phone_number,
                'logo_path' => null, // akan diupdate jika ada logo
                'pic_name' => $request->pic_name,
                'pic_position' => $request->pic_position,
                'verification_document_path' => null, // akan diupdate jika ada dokumen
                'website' => $request->website,
                'description' => $request->description,
                'is_verified' => false, // akan diverifikasi oleh admin
            ]);
            
            // upload logo menggunakan SupabaseStorageService
            if ($request->hasFile('logo')) {
                try {
                    $file = $request->file('logo');
                    $uploadedPath = $this->storageService->uploadInstitutionLogo($file, $institution->id);
                    
                    if ($uploadedPath) {
                        $institution->update(['logo_path' => $uploadedPath]);
                        Log::info('Institution logo uploaded successfully', [
                            'institution_id' => $institution->id,
                            'path' => $uploadedPath
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error uploading institution logo: ' . $e->getMessage());
                    // tidak perlu throw exception, logo bersifat opsional
                }
            }
            
            // upload dokumen verifikasi menggunakan SupabaseStorageService
            if ($request->hasFile('verification_document')) {
                try {
                    $file = $request->file('verification_document');
                    $uploadedPath = $this->storageService->uploadVerificationDocument($file, $institution->id);
                    
                    if ($uploadedPath) {
                        $institution->update(['verification_document_path' => $uploadedPath]);
                        Log::info('Verification document uploaded successfully', [
                            'institution_id' => $institution->id,
                            'path' => $uploadedPath
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error uploading verification document: ' . $e->getMessage());
                    // tidak perlu throw exception, dokumen verifikasi bersifat required di validasi
                }
            }
            
            DB::commit();
            
            // picu event bahwa user baru telah terdaftar
            event(new Registered($user));

            // log successful registration
            Log::info('Institution registered successfully', [
                'user_id' => $user->id,
                'institution_id' => $institution->id,
                'email' => $user->email,
                'username' => $user->username
            ]);

            // auto-login user setelah registrasi berhasil
            Auth::login($user);
            
            // refresh session untuk memuat data terbaru termasuk logo
            $user = $user->fresh(['institution']);
            Auth::setUser($user);

            // cek apakah request adalah AJAX
            if ($request->expectsJson() || $request->ajax()) {
                // buat token untuk API authentication
                $token = $user->createToken('api-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi Berhasil! Akun Instansi Anda Sedang Menunggu Verifikasi Admin.',
                    'data' => [
                        'token' => $token,
                        'institution' => [
                            'id' => $institution->id,
                            'name' => $institution->name,
                            'email' => $institution->email,
                            'verification_status' => $institution->verification_status ?? 'pending_verification',
                        ],
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'user_type' => $user->user_type,
                        ],
                        'redirect_url' => route('institution.dashboard')
                    ]
                ], 201);
            }

            // redirect ke dashboard institution dengan pesan sukses
            return redirect()
                ->route('institution.dashboard')
                ->with('success', 'Selamat Datang Di KKN-GO! Akun Instansi Anda Berhasil Dibuat Dan Sedang Menunggu Verifikasi Admin. Anda Akan Menerima Notifikasi Email Setelah Akun Diverifikasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Institution registration failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // cek apakah request adalah AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi Kesalahan Saat Registrasi. Silakan Coba Lagi.',
                    'errors' => $e->getMessage() 
                ], 500);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.');
        }
    }
}