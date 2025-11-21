<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRegisterRequest;
use App\Http\Requests\InstitutionRegisterRequest;
use App\Http\Requests\CompanyRegisterRequest;
use App\Models\User;
use App\Models\Student;
use App\Models\Institution;
use App\Models\Company;
use App\Models\University;
use App\Models\Province;
use App\Models\Regency;
use App\Services\SupabaseStorageService;
use App\Services\SupabaseService;
use App\Mail\InstitutionRegistered;
use App\Jobs\ValidateInstitutionDocumentsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
     * tampilkan form registrasi company
     */
    public function showCompanyForm()
    {
        // ambil data provinces untuk dropdown
        $provinces = Province::orderBy('name', 'asc')->get();

        return view('auth.company-register', compact('provinces'));
    }

    /**
     * alias untuk showCompanyForm
     */
    public function showCompanyRegistrationForm()
    {
        return $this->showCompanyForm();
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
                    'message' => 'Registrasi Berhasil! Selamat Datang Di Karsa.',
                    'redirect_url' => route('student.dashboard') // PERBAIKAN: gunakan redirect_url bukan redirect
                ], 200);
            }

            // redirect ke dashboard student dengan pesan sukses
            return redirect()
                ->route('student.dashboard')
                ->with('success', 'Selamat Datang Di Karsa! Akun Anda Berhasil Dibuat. Silakan Lengkapi Profil Dan Mulai Mencari Proyek KKN Yang Sesuai Dengan Minat Anda.');

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
                }
            }

            // upload KTP menggunakan SupabaseStorageService
            if ($request->hasFile('ktp')) {
                try {
                    $file = $request->file('ktp');
                    $uploadedPath = $this->storageService->uploadKTP($file, $institution->id);

                    if ($uploadedPath) {
                        $institution->update(['ktp_path' => $uploadedPath]);
                        Log::info('KTP uploaded successfully', [
                            'institution_id' => $institution->id,
                            'path' => $uploadedPath
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error uploading KTP: ' . $e->getMessage());
                }
            }

            // upload NPWP menggunakan SupabaseStorageService
            if ($request->hasFile('npwp')) {
                try {
                    $file = $request->file('npwp');
                    $uploadedPath = $this->storageService->uploadNPWP($file, $institution->id);

                    if ($uploadedPath) {
                        $institution->update(['npwp_path' => $uploadedPath]);
                        Log::info('NPWP uploaded successfully', [
                            'institution_id' => $institution->id,
                            'path' => $uploadedPath
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error uploading NPWP: ' . $e->getMessage());
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

            // Kirim email notifikasi registrasi
            try {
                Mail::to($institution->email)->send(new InstitutionRegistered($institution));
                Log::info('Registration email sent successfully to: ' . $institution->email);
            } catch (\Exception $e) {
                Log::error('Failed to send registration email: ' . $e->getMessage());
                // Tidak throw exception, registrasi tetap berhasil meski email gagal
            }

            // Dispatch AI validation job (async background process)
            try {
                ValidateInstitutionDocumentsJob::dispatch($institution)
                    ->onQueue('validations')
                    ->delay(now()->addSeconds(30)); // Delay 30 detik untuk memastikan files fully uploaded

                Log::info('✅ AI validation job dispatched', [
                    'institution_id' => $institution->id
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Failed to dispatch AI validation job: ' . $e->getMessage());
                // Registrasi tetap berhasil, admin akan manual review
            }

            // TIDAK auto-login user - biarkan menunggu verifikasi AI
            // User akan login manual setelah akun diverifikasi

            // cek apakah request adalah AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ajuan registrasi berhasil! Cek email berkala',
                    'data' => [
                        'institution' => [
                            'id' => $institution->id,
                            'name' => $institution->name,
                            'email' => $institution->email,
                            'verification_status' => $institution->verification_status ?? 'pending_ai_validation',
                        ],
                        'redirect_url' => route('home')
                    ]
                ], 201);
            }

            // redirect ke homepage dengan pesan sukses
            return redirect()
                ->route('home')
                ->with('success', 'Ajuan registrasi berhasil! Cek email berkala untuk info verifikasi.');

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

    /**
     * proses registrasi company
     * WAJIB: Semua data disimpan langsung ke Supabase PostgreSQL
     * WAJIB: Logo disimpan di Supabase Storage
     */
    public function registerCompany(CompanyRegisterRequest $request)
    {
        try {
            Log::info('Company registration attempt', [
                'email' => $request->email,
                'company_name' => $request->company_name,
            ]);

            // gunakan database transaction untuk memastikan atomicity
            DB::beginTransaction();
            Log::info('Transaction started');

            // buat user account
            Log::info('Attempting to create user', [
                'name' => $request->company_name,
                'email' => $request->email,
                'username' => $request->username,
            ]);

            $user = User::create([
                'name' => $request->company_name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'user_type' => 'company',
                'is_active' => true,
            ]);

            Log::info('Company user created successfully', ['user_id' => $user->id]);

            // buat company profile dengan data awal
            // PENTING: Data ini akan disimpan di Supabase melalui model Company
            $companyData = [
                'user_id' => $user->id,
                'name' => $request->company_name,
                'industry' => $request->industry,
                'description' => $request->description,
                'website' => $request->website,
                'address' => $request->address,
                'city' => $request->city,
                'province_id' => $request->province_id,
                'phone' => $request->phone,
                'founded_year' => $request->founded_year,
                'employee_count' => $this->getEmployeeCountFromSize($request->company_size),
                'verification_status' => 'pending_verification',
                'logo' => null, // akan diupdate jika ada logo
            ];

            $company = Company::create($companyData);

            Log::info('Company profile created', ['company_id' => $company->id]);

            // upload logo menggunakan SupabaseStorageService
            // WAJIB: Logo harus disimpan di Supabase Storage, BUKAN local storage
            if ($request->hasFile('logo')) {
                try {
                    $file = $request->file('logo');

                    // gunakan method uploadCompanyLogo dari SupabaseStorageService
                    $uploadedPath = $this->storageService->uploadCompanyLogo($file, $company->id);

                    if ($uploadedPath) {
                        // update company profile dengan path logo yang baru
                        $company->update(['logo' => $uploadedPath]);
                        Log::info('Company logo uploaded to Supabase Storage', [
                            'company_id' => $company->id,
                            'path' => $uploadedPath
                        ]);
                    } else {
                        Log::warning('Failed to upload company logo to Supabase for company ID: ' . $company->id);
                    }
                } catch (\Exception $e) {
                    Log::error('Error uploading company logo to Supabase: ' . $e->getMessage());
                    // tidak perlu throw exception, logo bersifat opsional
                }
            }

            DB::commit();

            // picu event bahwa user baru telah terdaftar (untuk kirim email verifikasi)
            event(new Registered($user));

            Log::info('Company registered successfully', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'email' => $user->email,
                'company_name' => $company->name
            ]);

            // auto-login user setelah registrasi berhasil
            Auth::login($user);

            // refresh session untuk memuat data terbaru
            $user = $user->fresh(['company']);
            Auth::setUser($user);

            // cek apakah request adalah AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi Berhasil! Selamat Datang Di Lapangku.',
                    'redirect_url' => route('company.dashboard')
                ], 200);
            }

            // redirect ke dashboard company dengan pesan sukses
            return redirect()
                ->route('company.dashboard')
                ->with('success', 'Selamat Datang Di Lapangku! Akun Perusahaan Anda Berhasil Dibuat. Mulai Posting Lowongan Dan Temukan Talent Terbaik.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Company registration failed: ' . $e->getMessage());
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
     * helper method untuk convert company size ke employee count
     */
    private function getEmployeeCountFromSize(string $size): ?int
    {
        return match($size) {
            '1-10' => 10,
            '11-50' => 50,
            '51-200' => 200,
            '201-500' => 500,
            '501-1000' => 1000,
            '1000+' => 1000,
            default => null,
        };
    }
}