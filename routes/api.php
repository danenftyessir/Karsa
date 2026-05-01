<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ValidationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Student\BrowseProblemsController;
use App\Http\Controllers\API\DocumentVerificationController;
use App\Models\Regency;
use App\Models\Province;

/*
|--------------------------------------------------------------------------
| API Routes - KKN-GO Platform
|--------------------------------------------------------------------------
|
| file ini berisi semua API routes untuk aplikasi KKN-GO
| terutama untuk AJAX calls, validasi form, dan data loading
|
| CATATAN: Semua routes di sini otomatis mendapat prefix '/api/'
| Contoh: Route::get('/test') → URL: /api/test
|
*/

/*
|--------------------------------------------------------------------------
| Public API Routes (Tidak Perlu Authentication)
|--------------------------------------------------------------------------
*/

// ========================================
// AUTHENTICATION ROUTES (No CSRF Required)
// ========================================

// Register Student
// POST /api/register/student
Route::post('/register/student', [RegisterController::class, 'registerStudent'])
     ->name('api.register.student');

// Register Institution
// POST /api/register/institution
Route::post('/register/institution', [RegisterController::class, 'registerInstitution'])
     ->name('api.register.institution');

// Login
// POST /api/login
Route::post('/login', [LoginController::class, 'login'])
     ->name('api.login');

Route::prefix('public')->name('api.public.')->group(function () {

    // validasi step by step untuk form registrasi
    // endpoint ini digunakan untuk validasi real-time saat user mengisi form

    // validasi step registrasi student
    // POST /api/public/validate/student/step
    Route::post('/validate/student/step', [ValidationController::class, 'validateStudentStep'])
         ->name('validate.student.step');

    // validasi step registrasi institution
    // POST /api/public/validate/institution/step
    Route::post('/validate/institution/step', [ValidationController::class, 'validateInstitutionStep'])
         ->name('validate.institution.step');

    // get regencies berdasarkan province (untuk dropdown dinamis)
    // GET /api/public/regencies/{provinceId}
    Route::get('/regencies/{provinceId}', function ($provinceId) {
        try {
            $regencies = Regency::where('province_id', $provinceId)
                               ->orderBy('name', 'asc')
                               ->get(['id', 'name']);
            
            return response()->json($regencies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data kabupaten/kota',
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('regencies');
    
    // get all provinces (untuk dropdown)
    // GET /api/public/provinces
    Route::get('/provinces', function () {
        try {
            $provinces = Province::orderBy('name', 'asc')
                                ->get(['id', 'name', 'code']);
            
            return response()->json($provinces);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data provinsi',
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('provinces');
    
});

/*
|--------------------------------------------------------------------------
| Backward Compatibility Routes
|--------------------------------------------------------------------------
| Routes ini untuk backward compatibility dengan kode lama
| yang mungkin masih menggunakan path tanpa prefix 'public'
*/

// get regencies (tanpa prefix public)
// GET /api/regencies/{provinceId}
Route::get('/regencies/{provinceId}', function ($provinceId) {
    try {
        $regencies = Regency::where('province_id', $provinceId)
                           ->orderBy('name', 'asc')
                           ->get(['id', 'name']);
        
        return response()->json($regencies);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Gagal mengambil data kabupaten/kota',
            'message' => $e->getMessage()
        ], 500);
    }
});

// get provinces (tanpa prefix public)
// GET /api/provinces
Route::get('/provinces', function () {
    try {
        $provinces = Province::orderBy('name', 'asc')
                            ->get(['id', 'name', 'code']);
        
        return response()->json($provinces);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Gagal mengambil data provinsi',
            'message' => $e->getMessage()
        ], 500);
    }
});

/*
|--------------------------------------------------------------------------
| Authenticated API Routes (Perlu Login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // get current user info
    // GET /api/user
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    });

    // ========================================
    // AI DOCUMENT VERIFICATION ROUTES (Feature 1)
    // ========================================

    // Upload verification documents
    // POST /api/institutions/{id}/documents
    Route::post('/institutions/{id}/documents', [DocumentVerificationController::class, 'uploadDocuments']);

    // Trigger AI verification
    // POST /api/ai/verify-documents
    Route::post('/ai/verify-documents', [DocumentVerificationController::class, 'verifyDocuments']);

    // Get verification status
    // GET /api/institutions/{id}/verification-status
    Route::get('/institutions/{id}/verification-status', [DocumentVerificationController::class, 'getVerificationStatus']);

    // Get detailed verification result
    // GET /api/ai/verification/{verificationId}
    Route::get('/ai/verification/{verificationId}', [DocumentVerificationController::class, 'getVerificationResult']);

});

/*
|--------------------------------------------------------------------------
| Development/Testing Routes (Optional)
|--------------------------------------------------------------------------
| Routes ini hanya untuk development dan testing
| HAPUS atau DISABLE di production!
*/

// health check endpoint
// GET /api/health
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toDateTimeString(),
        'app' => config('app.name'),
        'env' => config('app.env'),
    ]);
});

// test database connection
// GET /api/test-db
Route::get('/test-db', function () {
    try {
        \DB::connection()->getPdo();
        return response()->json([
            'status' => 'ok',
            'message' => 'Database connection successful',
            'driver' => config('database.default'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database connection failed',
            'error' => $e->getMessage(),
        ], 500);
    }
});