# Security Enhancements Part 3 (Enhancement 6-12 + Testing & Roadmap)

*This is continuation of security enhancements - To be merged into Spesifikasi.md*

---

### Enhancement 6: Comprehensive Audit Logging

#### 🎯 Objective
Track all critical actions, data changes, and security events for compliance, forensics, and security monitoring.

#### 📋 Implementation

**6.1. Audit Log Model & Migration**

```php
// database/migrations/2025_01_15_create_audit_logs_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Who
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_type')->nullable();  // student, institution, admin

            // What
            $table->string('action');                 // created, updated, deleted, login, logout
            $table->string('auditable_type');         // Model class
            $table->unsignedBigInteger('auditable_id')->nullable();

            // Details
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();

            // Context
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable();   // GET, POST, PUT, DELETE

            // Security
            $table->string('risk_level')->default('low'); // low, medium, high, critical
            $table->boolean('suspicious')->default(false);

            // Timestamp
            $table->timestamp('created_at');

            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('action');
            $table->index('risk_level');
            $table->index('suspicious');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
```

```php
// app/Models/AuditLog.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    const UPDATED_AT = null; // Only created_at

    protected $fillable = [
        'user_id',
        'user_type',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'risk_level',
        'suspicious',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'suspicious' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }
}
```

**6.2. Auditable Trait (Auto-audit model changes)**

```php
// app/Traits/Auditable.php
<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the trait
     */
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            $model->auditEvent('created');
        });

        static::updated(function ($model) {
            $model->auditEvent('updated');
        });

        static::deleted(function ($model) {
            $model->auditEvent('deleted');
        });

        // For soft deletes
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->auditEvent('restored');
            });
        }
    }

    /**
     * Log audit event
     */
    public function auditEvent(string $action): void
    {
        $oldValues = $action === 'updated' ? $this->getOriginal() : [];
        $newValues = $action !== 'deleted' ? $this->getAttributes() : [];

        // Remove sensitive fields from audit log
        $excludedFields = ['password', 'remember_token', 'api_token'];
        $oldValues = array_diff_key($oldValues, array_flip($excludedFields));
        $newValues = array_diff_key($newValues, array_flip($excludedFields));

        // Determine risk level
        $riskLevel = $this->determineRiskLevel($action);

        AuditLog::create([
            'user_id' => Auth::id(),
            'user_type' => Auth::user()?->user_type,
            'action' => $action,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'old_values' => $action === 'updated' ? $oldValues : null,
            'new_values' => $action !== 'deleted' ? $newValues : null,
            'description' => $this->getAuditDescription($action),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'risk_level' => $riskLevel,
            'suspicious' => $this->isSuspicious($action),
        ]);
    }

    /**
     * Get human-readable description
     */
    protected function getAuditDescription(string $action): string
    {
        $modelName = class_basename($this);
        $identifier = $this->name ?? $this->title ?? $this->id;

        return match($action) {
            'created' => "{$modelName} '{$identifier}' was created",
            'updated' => "{$modelName} '{$identifier}' was updated",
            'deleted' => "{$modelName} '{$identifier}' was deleted",
            'restored' => "{$modelName} '{$identifier}' was restored",
            default => "{$action} on {$modelName} '{$identifier}'",
        };
    }

    /**
     * Determine risk level based on action and model
     */
    protected function determineRiskLevel(string $action): string
    {
        // High-risk models
        $criticalModels = ['User', 'Institution', 'Payment'];
        if (in_array(class_basename($this), $criticalModels)) {
            return 'high';
        }

        // Critical actions
        if (in_array($action, ['deleted'])) {
            return 'high';
        }

        // Medium risk for updates
        if ($action === 'updated') {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Detect suspicious activity
     */
    protected function isSuspicious(string $action): bool
    {
        // Check for rapid changes
        $recentLogs = AuditLog::where('user_id', Auth::id())
            ->where('auditable_type', get_class($this))
            ->where('created_at', '>', now()->subMinutes(5))
            ->count();

        if ($recentLogs > 10) {
            return true;
        }

        // Check for after-hours activity (outside 6 AM - 10 PM)
        $hour = now()->hour;
        if ($hour < 6 || $hour > 22) {
            return true;
        }

        return false;
    }
}
```

**6.3. Usage in Models**

```php
// app/Models/Institution.php
<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use Auditable; // Auto-audit all changes

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'email',
        'phone',
        'verification_status',
        // ... other fields
    ];
}

// app/Models/Student.php
class Student extends Model
{
    use Auditable; // Auto-audit all changes
}

// app/Models/Payment.php
class Payment extends Model
{
    use Auditable; // Critical - audit all changes
}
```

**6.4. Manual Audit Logging (Custom Events)**

```php
// app/Services/AuditService.php
<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Log security event
     */
    public function logSecurityEvent(string $action, string $description, string $riskLevel = 'medium'): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'user_type' => Auth::user()?->user_type,
            'action' => $action,
            'auditable_type' => 'SecurityEvent',
            'auditable_id' => null,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'risk_level' => $riskLevel,
            'suspicious' => $riskLevel === 'critical',
        ]);
    }

    /**
     * Log authentication event
     */
    public function logAuth(string $action, bool $success, ?string $reason = null): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'user_type' => Auth::user()?->user_type,
            'action' => $action,
            'auditable_type' => 'AuthEvent',
            'description' => $success ? "{$action} successful" : "{$action} failed: {$reason}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'risk_level' => $success ? 'low' : 'medium',
            'suspicious' => !$success,
        ]);
    }

    /**
     * Log data access (sensitive data)
     */
    public function logDataAccess(string $dataType, $identifier): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'user_type' => Auth::user()?->user_type,
            'action' => 'accessed',
            'auditable_type' => $dataType,
            'auditable_id' => $identifier,
            'description' => "Accessed {$dataType} data",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'risk_level' => 'medium',
        ]);
    }
}
```

**6.5. Usage in Controllers**

```php
// app/Http/Controllers/Auth/LoginController.php
use App\Services\AuditService;

public function login(Request $request)
{
    $auditService = app(AuditService::class);

    // ... authentication logic

    if (Auth::attempt($credentials, $remember)) {
        $auditService->logAuth('login', true);
        // ...
    } else {
        $auditService->logAuth('login', false, 'Invalid credentials');
        // ...
    }
}

// app/Http/Controllers/API/StudentController.php
public function show(Request $request, $id)
{
    $auditService = app(AuditService::class);

    $student = Student::findOrFail($id);

    // Log sensitive data access
    $auditService->logDataAccess('Student', $id);

    return response()->json($student);
}
```

**6.6. Audit Log Viewer (Admin)**

```php
// app/Http/Controllers/Admin/AuditLogController.php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display audit logs
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }

        if ($request->filled('suspicious')) {
            $query->where('suspicious', true);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        return view('admin.audit-logs.index', compact('logs'));
    }

    /**
     * Show detailed audit log
     */
    public function show($id)
    {
        $log = AuditLog::with('user', 'auditable')->findOrFail($id);

        return view('admin.audit-logs.show', compact('log'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        // Generate CSV export
        $logs = AuditLog::whereBetween('created_at', [
            $request->date_from,
            $request->date_to
        ])->get();

        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'ID', 'User', 'Action', 'Model', 'Description',
                'IP Address', 'Risk Level', 'Created At'
            ]);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    $log->action,
                    $log->auditable_type,
                    $log->description,
                    $log->ip_address,
                    $log->risk_level,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
```

---

### Enhancement 7: GDPR Compliance & Data Retention

#### 🎯 Objective
Comply with GDPR and data protection regulations through automated data retention policies and user data management.

#### 📋 Implementation

**7.1. Data Retention Service**

```php
// app/Services/DataRetentionService.php
<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataRetentionService
{
    /**
     * Retention policies (in days)
     */
    protected array $retentionPolicies = [
        'student_questionnaire' => 730,       // 2 years
        'chat_history' => 365,                // 1 year
        'verification_documents' => 1825,     // 5 years (legal requirement)
        'ai_logs' => 90,                      // 90 days
        'audit_logs' => 2555,                 // 7 years (compliance)
        'sessions' => 7,                      // 7 days
        'password_resets' => 1,               // 1 day
    ];

    /**
     * Run cleanup for all data types
     */
    public function runCleanup(): array
    {
        Log::info('Starting data retention cleanup');

        $results = [
            'chat_history' => $this->cleanupChatHistory(),
            'ai_logs' => $this->cleanupAILogs(),
            'sessions' => $this->cleanupSessions(),
            'password_resets' => $this->cleanupPasswordResets(),
        ];

        Log::info('Data retention cleanup completed', $results);

        return $results;
    }

    /**
     * Cleanup old chat messages
     */
    protected function cleanupChatHistory(): int
    {
        $cutoffDate = now()->subDays($this->retentionPolicies['chat_history']);

        $deleted = DB::table('ai_chat_messages')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        return $deleted;
    }

    /**
     * Cleanup old AI audit logs
     */
    protected function cleanupAILogs(): int
    {
        $cutoffDate = now()->subDays($this->retentionPolicies['ai_logs']);

        $deleted = DB::table('ai_audit_logs')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        return $deleted;
    }

    /**
     * Cleanup old sessions
     */
    protected function cleanupSessions(): int
    {
        $cutoffTimestamp = now()->subDays($this->retentionPolicies['sessions'])->timestamp;

        $deleted = DB::table('sessions')
            ->where('last_activity', '<', $cutoffTimestamp)
            ->delete();

        return $deleted;
    }

    /**
     * Cleanup old password resets
     */
    protected function cleanupPasswordResets(): int
    {
        $cutoffDate = now()->subDays($this->retentionPolicies['password_resets']);

        $deleted = DB::table('password_reset_tokens')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        return $deleted;
    }

    /**
     * Anonymize user data (GDPR right to erasure)
     */
    public function anonymizeUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Anonymize student data
            if ($user->student) {
                $user->student->update([
                    'first_name' => '[DELETED]',
                    'last_name' => '[USER]',
                    'nim' => '[REDACTED]',
                    'phone' => null,
                    'address' => null,
                    'profile_photo_path' => null,
                ]);
            }

            // Anonymize institution data
            if ($user->institution) {
                $user->institution->update([
                    'name' => '[DELETED INSTITUTION]',
                    'email' => "deleted_{$user->id}@anonymized.com",
                    'phone' => null,
                    'address' => null,
                    'pic_name' => '[REDACTED]',
                    'description' => null,
                    'logo_path' => null,
                ]);
            }

            // Anonymize user account
            $user->update([
                'name' => '[DELETED USER]',
                'email' => "deleted_{$user->id}@anonymized.com",
                'username' => "deleted_user_{$user->id}",
                'password' => bcrypt(Str::random(32)),
                'remember_token' => null,
            ]);

            // Delete API tokens
            $user->tokens()->delete();

            // Mark as anonymized
            $user->update(['is_anonymized' => true]);

            Log::info('User data anonymized', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
            ]);
        });
    }

    /**
     * Export user data (GDPR right to data portability)
     */
    public function exportUserData(User $user): array
    {
        $data = [
            'user' => $user->toArray(),
            'profile' => null,
            'activities' => [],
            'audit_logs' => [],
        ];

        // Include profile data
        if ($user->student) {
            $data['profile'] = $user->student->toArray();
        } elseif ($user->institution) {
            $data['profile'] = $user->institution->toArray();
        }

        // Include audit logs
        $data['audit_logs'] = AuditLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        // Remove sensitive data
        unset($data['user']['password']);
        unset($data['user']['remember_token']);

        return $data;
    }
}
```

**7.2. Scheduled Task**

```php
// app/Console/Kernel.php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\DataRetentionService;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Run data retention cleanup daily at 2 AM
        $schedule->call(function () {
            $service = app(DataRetentionService::class);
            $results = $service->runCleanup();

            \Log::info('Scheduled data retention completed', $results);
        })->dailyAt('02:00')
          ->name('data-retention-cleanup')
          ->onOneServer(); // Only run on one server in cluster
    }
}
```

**7.3. User Data Export Controller**

```php
// app/Http/Controllers/User/DataExportController.php
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\DataRetentionService;
use Illuminate\Http\Request;

class DataExportController extends Controller
{
    /**
     * Request data export (GDPR)
     */
    public function request(Request $request)
    {
        $user = $request->user();

        // Generate export
        $dataRetention = app(DataRetentionService::class);
        $data = $dataRetention->exportUserData($user);

        // Create JSON file
        $filename = "data_export_{$user->id}_" . now()->format('Y-m-d_His') . ".json";
        $filePath = storage_path("app/exports/{$filename}");

        // Ensure directory exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Write file
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

        // Download
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    /**
     * Request account deletion (GDPR right to erasure)
     */
    public function requestDeletion(Request $request)
    {
        $user = $request->user();

        // Verify password
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Invalid password']);
        }

        // Schedule deletion (30 days grace period)
        $user->update([
            'deletion_requested_at' => now(),
            'deletion_scheduled_at' => now()->addDays(30),
        ]);

        return redirect()->route('home')
            ->with('success', 'Your account will be deleted in 30 days. You can cancel this request anytime before then.');
    }

    /**
     * Cancel deletion request
     */
    public function cancelDeletion(Request $request)
    {
        $user = $request->user();

        $user->update([
            'deletion_requested_at' => null,
            'deletion_scheduled_at' => null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Account deletion cancelled.');
    }
}
```

---

### Enhancement 8: Security Headers Middleware

#### 🎯 Objective
Implement HTTP security headers to protect against various web vulnerabilities.

#### 📋 Implementation

```php
// app/Http/Middleware/SecurityHeaders.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Add security headers to response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // X-Content-Type-Options
        // Prevents MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-Frame-Options
        // Prevents clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // X-XSS-Protection
        // Enable browser XSS filter
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy
        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy
        // Control browser features
        $response->headers->set('Permissions-Policy', implode(', ', [
            'geolocation=()',
            'microphone=()',
            'camera=()',
            'payment=()',
            'usb=()',
            'magnetometer=()',
        ]));

        // Content-Security-Policy
        // Prevent XSS and data injection attacks
        $csp = $this->getContentSecurityPolicy($request);
        $response->headers->set('Content-Security-Policy', $csp);

        // Strict-Transport-Security (HSTS)
        // Force HTTPS
        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        return $response;
    }

    /**
     * Get Content Security Policy
     */
    protected function getContentSecurityPolicy(Request $request): string
    {
        $directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net", // Allow CDN
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' https://api.anthropic.com https://api.cohere.ai", // AI APIs
            "frame-ancestors 'self'",
            "form-action 'self'",
            "base-uri 'self'",
            "object-src 'none'",
        ];

        // Add report URI for CSP violations (optional)
        if (config('app.csp_report_uri')) {
            $directives[] = "report-uri " . config('app.csp_report_uri');
        }

        return implode('; ', $directives);
    }
}
```

**Register Middleware:**

```php
// bootstrap/app.php
use App\Http\Middleware\SecurityHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
    })
    ->create();
```

---

### Enhancement 9-12: Summary Implementation

**Enhancement 9: Two-Factor Authentication (2FA)**
- Install `pragmarx/google2fa-laravel`
- Add 2FA setup/verification routes
- Require 2FA for admin and institution accounts
- Store encrypted 2FA secret in database

**Enhancement 10: SQL Injection Prevention**
- Always use Eloquent ORM or parameterized queries
- Never concatenate user input in SQL queries
- Use query builder's `where()` methods
- Validate and sanitize all input

**Enhancement 11: API Key & Credential Management**
- Use environment variables for all secrets
- Encrypt `.env` file in production
- Consider AWS Secrets Manager or HashiCorp Vault
- Rotate API keys periodically
- Never commit secrets to Git

**Enhancement 12: Security Monitoring Dashboard**
```php
// app/Http/Controllers/Admin/SecurityDashboardController.php
public function index()
{
    return view('admin.security.dashboard', [
        'failed_logins_24h' => AuditLog::where('action', 'login')
            ->where('suspicious', true)
            ->where('created_at', '>', now()->subDay())
            ->count(),

        'suspicious_activities' => AuditLog::where('suspicious', true)
            ->where('created_at', '>', now()->subWeek())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(),

        'high_risk_actions' => AuditLog::where('risk_level', 'high')
            ->where('created_at', '>', now()->subDay())
            ->count(),

        'active_sessions' => DB::table('sessions')
            ->where('last_activity', '>', now()->subHour()->timestamp)
            ->count(),

        'rate_limit_violations' => Cache::get('rate_limit_violations_24h', 0),
    ]);
}
```

---

## 🧪 Security Testing Guidelines

### Automated Security Testing

**Install Security Testing Tools:**

```bash
# Install Enlightn (Laravel Security Scanner)
composer require enlightn/enlightn --dev
php artisan enlightn

# Install Laravel Dusk (Browser Testing)
composer require --dev laravel/dusk
php artisan dusk:install

# Install PHPStan (Static Analysis)
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse

# Install Psalm (Static Analysis)
composer require --dev vimeo/psalm
./vendor/bin/psalm --init
```

### Manual Security Testing Checklist

**Authentication & Session:**
- [ ] Test brute force protection
- [ ] Test session fixation prevention
- [ ] Test session hijacking prevention
- [ ] Test remember me functionality
- [ ] Test logout (token revocation)
- [ ] Test concurrent sessions

**Input Validation:**
- [ ] Test XSS payloads in all input fields
- [ ] Test SQL injection in search/filter
- [ ] Test command injection
- [ ] Test path traversal in file operations
- [ ] Test SSRF in URL inputs

**File Upload:**
- [ ] Test malicious file upload (PHP, exe)
- [ ] Test oversized files
- [ ] Test invalid mime types
- [ ] Test zip bombs
- [ ] Test SVG with embedded scripts

**Authorization:**
- [ ] Test horizontal privilege escalation
- [ ] Test vertical privilege escalation
- [ ] Test IDOR (Insecure Direct Object Reference)
- [ ] Test API endpoint authorization

**API Security:**
- [ ] Test rate limiting bypass
- [ ] Test mass assignment
- [ ] Test CORS misconfiguration
- [ ] Test API authentication bypass

---

## 🗓️ Security Implementation Roadmap

### Phase 1: Critical Security (Week 1-2) ⚠️

**Priority: CRITICAL**

| Enhancement | Effort | Impact |
|-------------|--------|--------|
| 1. Data Encryption at Rest | Medium | HIGH |
| 2. Input Sanitization & XSS Prevention | Medium | HIGH |
| 3. Advanced File Upload Security | High | HIGH |
| 8. Security Headers Middleware | Low | MEDIUM |

**Deliverables:**
- ✅ All PII encrypted in database
- ✅ Global input sanitization middleware
- ✅ File upload with antivirus scanning
- ✅ Security headers implemented

---

### Phase 2: Access Control & Monitoring (Week 3-4) 📊

**Priority: HIGH**

| Enhancement | Effort | Impact |
|-------------|--------|--------|
| 4. Rate Limiting & API Abuse Prevention | Medium | HIGH |
| 5. Session & Cookie Security | Low | MEDIUM |
| 6. Comprehensive Audit Logging | Medium | HIGH |
| 7. GDPR Compliance & Data Retention | Medium | MEDIUM |

**Deliverables:**
- ✅ Multi-tier rate limiting active
- ✅ Secure session management
- ✅ Complete audit trail
- ✅ GDPR compliance tools

---

### Phase 3: Advanced Security (Month 2) 🔐

**Priority: MEDIUM**

| Enhancement | Effort | Impact |
|-------------|--------|--------|
| 9. Two-Factor Authentication (2FA) | High | HIGH |
| 10. SQL Injection Prevention Audit | Low | MEDIUM |
| 11. API Key & Credential Management | Medium | MEDIUM |
| 12. Security Monitoring Dashboard | Medium | MEDIUM |

**Deliverables:**
- ✅ 2FA for sensitive accounts
- ✅ Code audit completed
- ✅ Centralized credential management
- ✅ Security dashboard operational

---

### Phase 4: Continuous Security (Ongoing) 🔄

**Ongoing Activities:**

1. **Weekly Security Reviews**
   - Review audit logs for suspicious activity
   - Check rate limit violations
   - Monitor failed login attempts

2. **Monthly Security Audits**
   - Run automated security scanners
   - Review and update dependencies
   - Penetration testing

3. **Quarterly Security Updates**
   - Rotate encryption keys
   - Update security policies
   - Security training for team

4. **Annual Compliance Reviews**
   - GDPR compliance audit
   - Third-party security audit
   - Update disaster recovery plan

---

## 📈 Security Metrics & KPIs

### Track These Metrics:

**Security Incidents:**
- Failed login attempts (daily)
- Suspicious activities detected (weekly)
- Rate limit violations (daily)
- File upload rejections (daily)

**Compliance:**
- GDPR data export requests (monthly)
- Data deletion requests (monthly)
- Audit log coverage (% of actions logged)
- Session security compliance

**Performance:**
- Encryption/decryption latency
- Rate limiter response time
- Audit log write performance
- File scan time (antivirus)

---

## 🎯 Success Criteria

Security implementation is successful when:

✅ **All PII is encrypted** at rest in database
✅ **Zero XSS vulnerabilities** detected in testing
✅ **100% of critical actions** are audit logged
✅ **Rate limiting active** on all sensitive endpoints
✅ **File upload security** with antivirus scanning
✅ **GDPR compliance** tools operational
✅ **Security headers** implemented site-wide
✅ **Session security** with regeneration and validation
✅ **Automated security testing** in CI/CD pipeline
✅ **Security monitoring dashboard** for admins

---

## 📚 Additional Resources

**Laravel Security:**
- [Laravel Security Best Practices](https://laravel.com/docs/master/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Enlightn Security Documentation](https://www.laravel-enlightn.com/docs/)

**GDPR Compliance:**
- [GDPR Official Text](https://gdpr-info.eu/)
- [Laravel GDPR Tools](https://github.com/soved/laravel-gdpr)

**Tools:**
- [Burp Suite](https://portswigger.net/burp) - Penetration testing
- [OWASP ZAP](https://www.zaproxy.org/) - Security testing
- [ClamAV](https://www.clamav.net/) - Antivirus scanning
- [VirusTotal API](https://www.virustotal.com/gui/intelligence-overview) - File scanning

---

**END OF SECURITY ENHANCEMENTS**

*These security enhancements should be integrated into the main Spesifikasi.md file in the Security & Privacy section*
