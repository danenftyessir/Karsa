<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\SupabaseService;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * ProfileController - Manage Company Profile
 * Semua operasi CRUD langsung ke Supabase PostgreSQL
 * Semua foto/logo langsung ke Supabase Storage
 */
class ProfileController extends Controller
{
    protected $supabase;
    protected $storageService;

    public function __construct(SupabaseService $supabase, SupabaseStorageService $storageService)
    {
        $this->supabase = $supabase;
        $this->storageService = $storageService;
    }

    /**
     * Display company profile
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company()->with('province')->first();

        if (!$company) {
            return redirect()->route('home')
                ->with('error', 'profil perusahaan tidak ditemukan');
        }

        $stats = [
            'total_jobs' => $company->jobPostings()->count(),
            'active_jobs' => $company->jobPostings()->active()->count(),
            'total_applications' => $company->jobApplications()->count(),
            'total_hires' => $company->jobApplications()->where('job_applications.status', \App\Models\JobApplication::STATUS_HIRED)->count(),
        ];

        return view('company.profile.index', compact('company', 'stats'));
    }

    /**
     * Display public company profile
     * Accessible by all users (students, companies, guests)
     */
    public function showPublic($id)
    {
        $company = Company::with('province')->findOrFail($id);

        $activeJobs = $company->jobPostings()
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'total_jobs' => $company->jobPostings()->count(),
            'active_jobs' => $company->jobPostings()->active()->count(),
        ];

        return view('company.profile.public', compact('company', 'activeJobs', 'stats'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = Auth::user();
        $company = $user->company()->with('province')->first();

        $provinces = \App\Models\Province::orderBy('name')->get();

        $industries = [
            'Technology',
            'Healthcare',
            'Finance',
            'Education',
            'Manufacturing',
            'Retail',
            'Hospitality',
            'Real Estate',
            'Consulting',
            'Media & Entertainment',
            'Non-Profit',
            'Government',
            'Other',
        ];

        $companySizes = [
            '1-10',
            '11-50',
            '51-200',
            '201-500',
            '501-1000',
            '1001-5000',
            '5000+',
        ];

        return view('company.profile.edit', compact(
            'company',
            'provinces',
            'industries',
            'companySizes'
        ));
    }

    /**
     * Update company profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:100',
            'company_size' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'location' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province_id' => 'nullable|exists:provinces,id',
            'employee_count' => 'nullable|string|max:20',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
        ]);

        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');

            if ($company->logo) {
                $this->storageService->delete($company->logo);
            }

            $logoPath = $this->storageService->uploadCompanyLogo($logoFile, $company->id);

            // jika upload berhasil, simpan path. Jika gagal, tetap gunakan logo lama
            if ($logoPath) {
                $validated['logo'] = $logoPath;
                \Log::info("Logo berhasil diupload untuk company ID {$company->id}", ['path' => $logoPath]);
            } else {
                $validated['logo'] = $company->logo;
                \Log::warning("Gagal upload logo untuk company ID {$company->id}, menggunakan logo lama");
            }
        }
        $company->update($validated);

        return redirect()->route('company.profile.index')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Upload or update company logo
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
        ]);

        $user = Auth::user();
        $company = $user->company;

        $logoFile = $request->file('logo');

        // Delete old logo from Supabase Storage if exists
        if ($company->logo) {
            $this->storageService->delete($company->logo);
        }

        $logoPath = $this->storageService->uploadCompanyLogo($logoFile, $company->id);

        // jika upload berhasil, update logo. Jika gagal, kembalikan error
        if ($logoPath) {
            $company->update(['logo' => $logoPath]);

            \Log::info("Logo berhasil diupload untuk company ID {$company->id}", ['path' => $logoPath]);

            return response()->json([
                'success' => true,
                'message' => 'Logo uploaded successfully',
                'logo_url' => $company->logo_url,
            ]);
        } else {
            \Log::error("Gagal upload logo untuk company ID {$company->id}");

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload logo. Please try again.',
            ], 500);
        }
    }

    /**
     * Delete company logo
     */
    public function deleteLogo()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company->logo) {
            return response()->json([
                'success' => false,
                'message' => 'No logo to delete',
            ], 400);
        }
        $this->storageService->delete($company->logo);

        $company->update(['logo' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Logo deleted successfully',
        ]);
    }

    /**
     * Request verification for company
     */
    public function requestVerification(Request $request)
    {
        $validated = $request->validate([
            'verification_documents' => 'nullable|array',
            'verification_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max per file
        ]);

        $user = Auth::user();
        $company = $user->company;

        $documentUrls = [];
        if ($request->hasFile('verification_documents')) {
            foreach ($request->file('verification_documents') as $index => $file) {
                $fileName = 'verification_' . $company->id . '_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                $documentUrl = $this->supabase->uploadFile('company_documents', $fileName, $file);
                $documentUrls[] = $documentUrl;
            }
        }

        $company->update([
            'verification_status' => 'pending_verification',
            'verification_documents' => json_encode($documentUrls),
        ]);

        return redirect()->route('company.profile.index')
            ->with('success', 'Verification request submitted successfully! We will review it soon.');
    }

    /**
     * Update company settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'nullable|boolean',
            'application_notifications' => 'nullable|boolean',
            'marketing_emails' => 'nullable|boolean',
        ]);
        $user = Auth::user();
        $company = $user->company;
        $company->update([
            'settings' => json_encode($validated),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
        ]);
    }
}