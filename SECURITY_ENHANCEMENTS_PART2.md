# Security Enhancements Part 2 (Enhancement 4-12)

*This is continuation of Spesifikasi.md - To be merged into main specification*

---

### Enhancement 4: Comprehensive Rate Limiting & API Abuse Prevention

#### 🎯 Objective
Prevent API abuse, brute force attacks, and DDoS by implementing multi-tier rate limiting based on user subscription and endpoint sensitivity.

#### 📋 Implementation

**4.1. Global Rate Limiting Configuration**

```php
// bootstrap/app.php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware): void {

        // Standard API rate limit
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many requests. Please try again later.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429);
                });
        });

        // AI Endpoints - Strict limits (expensive operations)
        RateLimiter::for('ai', function (Request $request) {
            return [
                Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()),
                Limit::perHour(50)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        // File Upload - Very strict (storage costs)
        RateLimiter::for('uploads', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->user()?->id ?: $request->ip()),
                Limit::perHour(20)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        // Authentication - Progressive delay
        RateLimiter::for('auth', function (Request $request) {
            $key = $request->input('email') . '|' . $request->ip();

            return [
                Limit::perMinute(5)->by($key),
                Limit::perHour(20)->by($key),
            ];
        });

        // Registration - Prevent spam accounts
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Search - Moderate (database intensive)
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        $middleware->throttleApi();
    })
    ->create();
```

**4.2. Apply Rate Limiters to Routes**

```php
// routes/api.php
<?php

use Illuminate\Support\Facades\Route;

// Authentication routes (strict)
Route::middleware(['throttle:auth'])->group(function () {
    Route::post('/auth/login', [LoginController::class, 'login']);
    Route::post('/auth/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
    Route::post('/auth/reset-password', [ResetPasswordController::class, 'reset']);
});

// Registration routes
Route::middleware(['throttle:register'])->group(function () {
    Route::post('/auth/register/student', [RegisterController::class, 'registerStudent']);
    Route::post('/auth/register/institution', [RegisterController::class, 'registerInstitution']);
});

// AI routes (expensive operations)
Route::middleware(['auth:sanctum', 'throttle:ai'])->group(function () {
    Route::post('/ai/verify-documents', [DocumentVerificationController::class, 'verify']);
    Route::post('/ai/chat', [ChatController::class, 'send']);
    Route::post('/ai/match-problems', [MatchingController::class, 'match']);
    Route::post('/ai/suggest-projects', [SuggestionController::class, 'suggest']);
});

// Upload routes (very strict)
Route::middleware(['auth:sanctum', 'throttle:uploads'])->group(function () {
    Route::post('/institutions/{id}/documents', [InstitutionController::class, 'uploadDocuments']);
    Route::post('/students/me/photo', [StudentController::class, 'uploadPhoto']);
    Route::post('/problems/{id}/attachments', [ProblemController::class, 'uploadAttachments']);
});

// Search routes
Route::middleware(['throttle:search'])->group(function () {
    Route::get('/problems/search', [ProblemController::class, 'search']);
    Route::get('/institutions/search', [InstitutionController::class, 'search']);
    Route::get('/students/search', [StudentController::class, 'search']);
});

// Standard API routes
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::apiResource('problems', ProblemController::class);
    Route::apiResource('applications', ApplicationController::class);
    Route::apiResource('projects', ProjectController::class);
});
```

**4.3. Dynamic Rate Limiting Service (Subscription-Based)**

```php
// app/Services/DynamicRateLimitService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;

class DynamicRateLimitService
{
    /**
     * Get rate limit based on user subscription tier
     */
    public function getLimitForUser(User $user, string $feature): int
    {
        // Free tier (no subscription)
        if (!$user->institution || !$user->institution->subscription) {
            return match($feature) {
                'ai_chat' => 10,              // 10 messages per hour
                'ai_matching' => 5,           // 5 matches per hour
                'document_verify' => 1,       // 1 verification per day
                'file_upload' => 5,           // 5 uploads per day
                'api_calls' => 60,            // 60 calls per minute
                default => 60,
            };
        }

        // Get subscription tier
        $tier = $user->institution->subscription->package->slug;

        return match([$tier, $feature]) {
            ['basic', 'ai_chat'] => 50,
            ['basic', 'ai_matching'] => 20,
            ['basic', 'document_verify'] => 5,
            ['basic', 'file_upload'] => 20,
            ['basic', 'api_calls'] => 100,

            ['pro', 'ai_chat'] => 200,
            ['pro', 'ai_matching'] => 100,
            ['pro', 'document_verify'] => 20,
            ['pro', 'file_upload'] => 50,
            ['pro', 'api_calls'] => 300,

            ['enterprise', 'ai_chat'] => 1000,
            ['enterprise', 'ai_matching'] => 500,
            ['enterprise', 'document_verify'] => 100,
            ['enterprise', 'file_upload'] => 200,
            ['enterprise', 'api_calls'] => 1000,

            default => 100,
        };
    }

    /**
     * Check if user has exceeded their limit
     */
    public function checkLimit(User $user, string $feature, int $decaySeconds = 3600): bool
    {
        $limit = $this->getLimitForUser($user, $feature);
        $key = "rate_limit:{$user->id}:{$feature}";

        $attempts = Cache::get($key, 0);

        return $attempts < $limit;
    }

    /**
     * Increment usage counter
     */
    public function hit(User $user, string $feature, int $decaySeconds = 3600): void
    {
        $key = "rate_limit:{$user->id}:{$feature}";
        $attempts = Cache::get($key, 0);
        Cache::put($key, $attempts + 1, $decaySeconds);

        // Log usage for analytics
        $this->logUsage($user, $feature, $attempts + 1);
    }

    /**
     * Get remaining attempts
     */
    public function remaining(User $user, string $feature): int
    {
        $limit = $this->getLimitForUser($user, $feature);
        $key = "rate_limit:{$user->id}:{$feature}";
        $attempts = Cache::get($key, 0);

        return max(0, $limit - $attempts);
    }

    /**
     * Reset counter (admin only)
     */
    public function reset(User $user, string $feature): void
    {
        $key = "rate_limit:{$user->id}:{$feature}";
        Cache::forget($key);
    }

    /**
     * Log usage for analytics
     */
    protected function logUsage(User $user, string $feature, int $count): void
    {
        \Log::info('Rate limit usage', [
            'user_id' => $user->id,
            'feature' => $feature,
            'count' => $count,
            'limit' => $this->getLimitForUser($user, $feature),
        ]);
    }
}
```

**4.4. Usage in Controllers**

```php
// app/Http/Controllers/API/ChatController.php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DynamicRateLimitService;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        protected DynamicRateLimitService $rateLimitService,
        protected ChatService $chatService
    ) {}

    /**
     * Send chat message
     */
    public function send(Request $request)
    {
        $user = $request->user();

        // Check custom rate limit
        if (!$this->rateLimitService->checkLimit($user, 'ai_chat')) {
            $remaining = $this->rateLimitService->remaining($user, 'ai_chat');

            return response()->json([
                'success' => false,
                'message' => 'AI chat limit exceeded for your subscription tier.',
                'data' => [
                    'remaining' => $remaining,
                    'current_tier' => $user->institution?->subscription?->package?->name ?? 'Free',
                    'upgrade_url' => route('subscription.upgrade'),
                ],
            ], 429);
        }

        // Validate request
        $request->validate([
            'message' => 'required|string|max:2000',
            'problem_id' => 'nullable|exists:problems,id',
        ]);

        // Process chat
        $response = $this->chatService->send(
            $user,
            $request->message,
            $request->problem_id
        );

        // Increment counter (1 hour decay)
        $this->rateLimitService->hit($user, 'ai_chat', 3600);

        return response()->json([
            'success' => true,
            'data' => $response,
            'meta' => [
                'remaining' => $this->rateLimitService->remaining($user, 'ai_chat'),
                'limit' => $this->rateLimitService->getLimitForUser($user, 'ai_chat'),
            ],
        ]);
    }
}
```

**4.5. Rate Limit Headers Middleware**

```php
// app/Http/Middleware/AddRateLimitHeaders.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AddRateLimitHeaders
{
    public function handle(Request $request, Closure $next, string $limiter = 'api')
    {
        $response = $next($request);

        // Get key
        $key = $request->user()?->id ?: $request->ip();

        // Get limit info
        $maxAttempts = 60; // Default
        $remaining = RateLimiter::remaining($key, $maxAttempts);
        $retryAfter = RateLimiter::availableIn($key);

        // Add headers
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', $remaining);

        if ($remaining === 0) {
            $response->headers->set('Retry-After', $retryAfter);
            $response->headers->set('X-RateLimit-Reset', now()->addSeconds($retryAfter)->timestamp);
        }

        return $response;
    }
}
```

---

### Enhancement 5: Session & Cookie Security

#### 🎯 Objective
Prevent session hijacking, fixation, and CSRF attacks through secure session management.

#### 📋 Implementation

**5.1. Production Environment Configuration**

```bash
# .env - Production Settings

# Session Security
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=yourdomain.com
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# Force HTTPS
APP_URL=https://yourdomain.com
FORCE_HTTPS=true

# Sanctum (API Authentication)
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
SESSION_COOKIE=kkn_go_session
```

**5.2. Session Configuration**

```php
// config/session.php
<?php

return [
    'driver' => env('SESSION_DRIVER', 'database'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => env('SESSION_ENCRYPT', true),
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => env('SESSION_TABLE', 'sessions'),
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100], // 2% chance cleanup on every request
    'cookie' => env('SESSION_COOKIE', 'kkn_go_session'),
    'path' => env('SESSION_PATH', '/'),
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS only
    'http_only' => env('SESSION_HTTP_ONLY', true), // Not accessible via JavaScript
    'same_site' => env('SESSION_SAME_SITE', 'strict'), // CSRF protection
    'partitioned' => false,
];
```

**5.3. Sanctum Configuration (API Tokens)**

```php
// config/sanctum.php
<?php

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ',' . parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    'guard' => ['web'],

    'expiration' => null, // Never expire (use remember token)

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],
];
```

**5.4. Enhanced Login Controller with Session Security**

```php
// app/Http/Controllers/Auth/LoginController.php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Handle login attempt with enhanced security
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Rate limiting
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            // Log suspicious activity
            Log::warning('Login rate limit exceeded', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        // Attempt authentication
        $credentials = [
            filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Clear rate limiter
            RateLimiter::clear($throttleKey);

            // CRITICAL: Regenerate session ID (prevent session fixation)
            $request->session()->regenerate();

            // Save session explicitly
            $request->session()->save();

            // Store session metadata for security monitoring
            $this->storeSessionMetadata($request);

            // Log successful login
            Log::info('User logged in successfully', [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->authenticated($request, Auth::user());
        }

        // Failed login - increment rate limiter
        RateLimiter::hit($throttleKey, 60);

        // Log failed attempt
        Log::warning('Failed login attempt', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
        ]);

        throw ValidationException::withMessages([
            'email' => 'The provided credentials are incorrect.',
        ]);
    }

    /**
     * Store session metadata for security monitoring
     */
    protected function storeSessionMetadata(Request $request): void
    {
        $request->session()->put([
            'login_at' => now()->timestamp,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'fingerprint' => $this->generateFingerprint($request),
        ]);
    }

    /**
     * Generate browser fingerprint
     */
    protected function generateFingerprint(Request $request): string
    {
        return hash('sha256', implode('|', [
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
        ]));
    }

    /**
     * Logout with session cleanup
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();

        // Revoke all API tokens (Sanctum)
        $request->user()->tokens()->delete();

        // Logout from guard
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Clear session data
        $request->session()->flush();

        Log::info('User logged out', [
            'user_id' => $userId,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('home')
            ->with('success', 'You have been successfully logged out');
    }
}
```

**5.5. Session Security Middleware**

```php
// app/Http/Middleware/ValidateSession.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ValidateSession
{
    /**
     * Validate session integrity
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip for guests
        if (!Auth::check()) {
            return $next($request);
        }

        // Validate session metadata
        if (!$this->validateSessionMetadata($request)) {
            Log::warning('Session validation failed - possible hijacking', [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
                'stored_ip' => $request->session()->get('ip_address'),
                'fingerprint' => $this->generateFingerprint($request),
                'stored_fingerprint' => $request->session()->get('fingerprint'),
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['session' => 'Your session has been invalidated for security reasons. Please login again.']);
        }

        // Check session age
        $loginAt = $request->session()->get('login_at');
        $maxAge = config('session.lifetime') * 60; // Convert minutes to seconds

        if ($loginAt && (now()->timestamp - $loginAt) > $maxAge) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('login')
                ->withErrors(['session' => 'Your session has expired. Please login again.']);
        }

        return $next($request);
    }

    /**
     * Validate session metadata
     */
    protected function validateSessionMetadata(Request $request): bool
    {
        // Check IP address (strict mode - comment out for dynamic IPs)
        // $storedIp = $request->session()->get('ip_address');
        // if ($storedIp && $storedIp !== $request->ip()) {
        //     return false;
        // }

        // Check browser fingerprint
        $storedFingerprint = $request->session()->get('fingerprint');
        $currentFingerprint = $this->generateFingerprint($request);

        if ($storedFingerprint && $storedFingerprint !== $currentFingerprint) {
            return false;
        }

        return true;
    }

    /**
     * Generate browser fingerprint
     */
    protected function generateFingerprint(Request $request): string
    {
        return hash('sha256', implode('|', [
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
        ]));
    }
}
```

---

*To be continued in next message with Enhancement 6-12...*

Would you like me to continue with the remaining enhancements (6-12)?
