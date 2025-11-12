<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * tampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * proses login
     */
    public function login(Request $request)
    {
        // validasi input
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'email atau username wajib diisi',
            'password.required' => 'password wajib diisi',
        ]);

        // rate limiting
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "terlalu banyak percobaan login. coba lagi dalam {$seconds} detik.",
            ]);
        }

        $loginInput = $request->input('email');
        $passwordInput = $request->input('password');
        
        Log::info('=== LOGIN ATTEMPT ===', [
            'input' => $loginInput,
            'ip' => $request->ip(),
        ]);

        // cek user
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($field, $loginInput)->first();
        
        if (!$user) {
            Log::warning('User not found', ['input' => $loginInput]);
            RateLimiter::hit($throttleKey, 60);
            
            throw ValidationException::withMessages([
                'email' => 'email atau password salah.',
            ]);
        }

        // cek password
        if (!Hash::check($passwordInput, $user->password)) {
            Log::warning('Incorrect password', ['user_id' => $user->id]);
            RateLimiter::hit($throttleKey, 60);
            
            throw ValidationException::withMessages([
                'email' => 'email atau password salah.',
            ]);
        }

        Log::info('Password correct', ['user_id' => $user->id]);

        // PERBAIKAN: Manual login untuk debugging
        $credentials = [
            $field => $loginInput,
            'password' => $passwordInput,
        ];
        
        $remember = $request->filled('remember');

        Log::info('Attempting Auth::attempt', [
            'field' => $field,
            'remember' => $remember,
            'session_driver' => config('session.driver'),
        ]);

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            
            // PENTING: Regenerate session untuk keamanan
            $request->session()->regenerate();
            
            // CRITICAL FIX: Save session secara manual
            // Karena di Railway middleware tidak auto-save
            session()->save();
            
            Log::info('Auth::attempt SUCCESS', [
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
            ]);

            // PERBAIKAN: Cek apakah session tersimpan
            if (config('session.driver') === 'database') {
                $sessionCount = \DB::table('sessions')
                    ->where('user_id', Auth::id())
                    ->count();
                    
                Log::info('Sessions in database after manual save', [
                    'count' => $sessionCount,
                ]);
                
                if ($sessionCount === 0) {
                    Log::error('Session still not saved after manual save!');
                }
            }

            return $this->authenticated($request, Auth::user());
        }

        Log::error('Auth::attempt FAILED despite correct password', [
            'user_id' => $user->id,
            'session_driver' => config('session.driver'),
            'session_config' => [
                'cookie' => config('session.cookie'),
                'domain' => config('session.domain'),
                'secure' => config('session.secure'),
                'same_site' => config('session.same_site'),
            ],
        ]);

        RateLimiter::hit($throttleKey, 60);

        throw ValidationException::withMessages([
            'email' => 'email atau password salah.',
        ]);
    }

    /**
     * redirect setelah login berhasil
     */
    protected function authenticated(Request $request, $user)
    {
        Log::info('=== AUTHENTICATED ===', [
            'user_id' => $user->id,
            'auth_check' => Auth::check(),
            'session_id' => session()->getId(),
        ]);

        // TEMPORARY: Skip email verification dan is_active check untuk debugging
        // Hapus comment ini setelah login berhasil

        /*
        if (isset($user->is_active) && !$user->is_active) {
            Log::warning('Account inactive', ['user_id' => $user->id]);
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'akun anda telah dinonaktifkan.']);
        }

        if (!$user->email_verified_at) {
            Log::warning('Email not verified', ['user_id' => $user->id]);
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'email anda belum diverifikasi.']);
        }
        */

        // redirect berdasarkan user type
        $route = null;

        switch ($user->user_type) {
            case 'student':
                $route = route('student.dashboard');
                Log::info('Redirecting to student dashboard', [
                    'user_id' => $user->id,
                    'route' => $route,
                ]);
                break;

            case 'institution':
                $route = route('institution.dashboard');
                Log::info('Redirecting to institution dashboard', [
                    'user_id' => $user->id,
                    'route' => $route,
                ]);
                break;

            default:
                Log::error('Invalid user type', [
                    'user_id' => $user->id,
                    'user_type' => $user->user_type,
                ]);
                Auth::logout();

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tipe user tidak valid.'
                    ], 400);
                }

                return redirect()->route('login')
                    ->withErrors(['email' => 'tipe user tidak valid.']);
        }

        $request->session()->save();

        Log::info('Session saved, redirecting', [
            'user_id' => $user->id,
            'redirect_to' => $route,
        ]);

        // cek apakah request adalah AJAX/API
        if ($request->expectsJson() || $request->ajax()) {
            // buat token untuk API authentication
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'user_type' => $user->user_type,
                    ],
                    'redirect_url' => $route
                ]
            ], 200);
        }

        return redirect()->intended($route);
    }

    /**
     * logout user
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();
        $userType = Auth::user()->user_type ?? null;

        Log::info('User logging out', [
            'user_id' => $userId,
            'user_type' => $userType,
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'anda berhasil logout');
    }
}