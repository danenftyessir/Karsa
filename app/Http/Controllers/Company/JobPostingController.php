<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\JobCategory;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * JobPostingController - Manage Job Postings
 *
 * IMPLEMENTED: Semua operasi CRUD langsung ke Supabase PostgreSQL
 * TIDAK ADA data yang disimpan di local database
 */
class JobPostingController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('home')
                ->with('error', 'profil perusahaan tidak ditemukan');
        }

        // IMPLEMENTED: Ambil data job postings dari Supabase
        $jobPostings = $company->jobPostings()
            ->with('jobCategory')
            ->withCount('jobApplications')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // IMPLEMENTED: Ambil data job categories dari Supabase
        $categories = JobCategory::orderBy('name', 'asc')->get();

        return view('company.jobs.index', compact('company', 'jobPostings', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $company = $user->company;

        // daftar department untuk dropdown
        $departments = [
            'Engineering', 'Marketing', 'Design', 'HR', 'Sales',
            'Finance', 'Operations', 'Product', 'Customer Support', 'Legal'
        ];

        // daftar job types untuk dropdown
        $jobTypes = [
            'Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance'
        ];

        // daftar skills untuk multi-select (KKN-focused)
        $availableSkills = [
            // Pendidikan & Pengajaran
            'Mengajar', 'Literasi', 'Bimbingan Belajar', 'Pendidikan Anak',

            // Kesehatan & Sanitasi
            'Penyuluhan Kesehatan', 'Sanitasi Lingkungan', 'Gizi & Nutrisi', 'Kesehatan Ibu & Anak',

            // Pertanian & Peternakan
            'Pertanian Organik', 'Budidaya Tanaman', 'Peternakan', 'Hidroponik', 'Kompos',

            // Bisnis & Kewirausahaan
            'UMKM', 'Pemasaran Produk Lokal', 'Manajemen Usaha', 'Koperasi', 'E-commerce',

            // Infrastruktur & Konstruksi
            'Renovasi Fasilitas', 'Pembangunan', 'Pengecatan', 'Pertukangan',

            // Lingkungan
            'Pengelolaan Sampah', 'Daur Ulang', 'Reboisasi', 'Konservasi Air', 'Bank Sampah',

            // Sosial & Kemasyarakatan
            'Pemberdayaan Masyarakat', 'Posyandu', 'PKK', 'Karang Taruna', 'Gotong Royong',

            // Teknologi & Digital
            'Komputer Dasar', 'Media Sosial', 'Desain Grafis', 'Fotografi', 'Videografi',

            // Seni & Budaya
            'Kesenian Daerah', 'Kerajinan Tangan', 'Musik', 'Tari', 'Bahasa Daerah',

            // Administrasi & Dokumentasi
            'Administrasi Desa', 'Dokumentasi', 'Laporan', 'Data Entry', 'Survei'
        ];

        // daftar SDG untuk alignment
        $sdgOptions = [
            ['id' => 1, 'name' => 'SDG 1: No Poverty', 'color' => 'red'],
            ['id' => 2, 'name' => 'SDG 2: Zero Hunger', 'color' => 'yellow'],
            ['id' => 3, 'name' => 'SDG 3: Good Health And Well-being', 'color' => 'green'],
            ['id' => 4, 'name' => 'SDG 4: Quality Education', 'color' => 'red'],
            ['id' => 5, 'name' => 'SDG 5: Gender Equality', 'color' => 'orange'],
            ['id' => 6, 'name' => 'SDG 6: Clean Water And Sanitation', 'color' => 'blue'],
            ['id' => 7, 'name' => 'SDG 7: Affordable And Clean Energy', 'color' => 'yellow'],
            ['id' => 8, 'name' => 'SDG 8: Decent Work And Economic Growth', 'color' => 'red'],
            ['id' => 9, 'name' => 'SDG 9: Industry, Innovation, And Infrastructure', 'color' => 'orange'],
            ['id' => 10, 'name' => 'SDG 10: Reduced Inequalities', 'color' => 'pink'],
            ['id' => 11, 'name' => 'SDG 11: Sustainable Cities And Communities', 'color' => 'amber'],
            ['id' => 12, 'name' => 'SDG 12: Responsible Consumption And Production', 'color' => 'yellow'],
            ['id' => 13, 'name' => 'SDG 13: Climate Action', 'color' => 'green'],
            ['id' => 14, 'name' => 'SDG 14: Life Below Water', 'color' => 'blue'],
            ['id' => 15, 'name' => 'SDG 15: Life On Land', 'color' => 'green'],
            ['id' => 16, 'name' => 'SDG 16: Peace, Justice, And Strong Institutions', 'color' => 'blue'],
            ['id' => 17, 'name' => 'SDG 17: Partnerships For The Goals', 'color' => 'blue'],
        ];

        return view('company.jobs.create', compact(
            'company',
            'departments',
            'jobTypes',
            'availableSkills',
            'sdgOptions'
        ));
    }

    public function store(Request $request)
    {
        // IMPLEMENTED: Validasi dan simpan job posting ke Supabase
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'department' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'job_type' => 'required|string|max:50',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string|max:10',
            'description' => 'required|string',
            'responsibilities' => 'required|string',
            'qualifications' => 'required|string',
            'benefits' => 'nullable|string',
            'skills' => 'required|array|min:1',
            'sdg_alignment' => 'nullable|array',
            'impact_metrics' => 'nullable|string',
            'success_criteria' => 'nullable|string',
            'status' => 'nullable|in:draft,posted',
            'allow_guest_applications' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $company = $user->company;

        // Generate slug dari title
        $slug = Str::slug($validated['title']);

        // IMPLEMENTED: Simpan ke Supabase PostgreSQL
        $jobPosting = JobPosting::create([
            'company_id' => $company->id,
            'job_category_id' => $validated['job_category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'department' => $validated['department'],
            'location' => $validated['location'],
            'job_type' => $validated['job_type'],
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'salary_currency' => $validated['salary_currency'] ?? 'USD',
            'description' => $validated['description'],
            'responsibilities' => $validated['responsibilities'],
            'qualifications' => $validated['qualifications'],
            'benefits' => $validated['benefits'] ?? null,
            'skills' => json_encode($validated['skills']),
            'sdg_alignment' => isset($validated['sdg_alignment']) ? json_encode($validated['sdg_alignment']) : null,
            'impact_metrics' => $validated['impact_metrics'] ?? null,
            'success_criteria' => $validated['success_criteria'] ?? null,
            'status' => $validated['status'] ?? 'draft',
            'allow_guest_applications' => $validated['allow_guest_applications'] ?? false,
            'published_at' => ($validated['status'] ?? 'draft') === 'posted' ? now() : null,
        ]);

        return redirect()->route('company.jobs.show', $jobPosting->id)
            ->with('success', 'Lowongan berhasil dibuat!');
    }

    public function show($id)
    {
        $user = Auth::user();
        $company = $user->company;

        // IMPLEMENTED: Ambil data job posting dari Supabase
        $jobPosting = JobPosting::with(['jobCategory', 'jobApplications.user'])
            ->where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        // Increment views count
        $jobPosting->incrementViews();

        // IMPLEMENTED: Ambil recent applicants dari Supabase
        $recentApplicants = $jobPosting->jobApplications()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'name' => $application->user->name ?? 'Unknown',
                    'avatar' => $application->user->avatar ?? null,
                    'is_new' => $application->status === 'new',
                    'status' => $application->status,
                    'created_at' => $application->created_at,
                ];
            });

        // IMPLEMENTED: Ambil statistik dari Supabase
        $statistics = [
            'applications_received' => $jobPosting->applications_count,
            'total_views' => $jobPosting->views_count,
            'shortlisted' => $jobPosting->getApplicationsCountByStatus('shortlisted'),
            'offers_extended' => $jobPosting->getApplicationsCountByStatus('offer'),
            'interviewed' => $jobPosting->getApplicationsCountByStatus('interview'),
            'hired' => $jobPosting->getApplicationsCountByStatus('hired'),
        ];

        return view('company.jobs.show', compact(
            'company',
            'jobPosting',
            'recentApplicants',
            'statistics'
        ));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $company = $user->company;

        // IMPLEMENTED: Ambil data job posting dari Supabase untuk diedit
        $jobPosting = JobPosting::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        // Data untuk form
        $departments = [
            'Engineering', 'Marketing', 'Design', 'HR', 'Sales',
            'Finance', 'Operations', 'Product', 'Customer Support', 'Legal'
        ];

        $jobTypes = [
            'Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance'
        ];

        $availableSkills = [
            // Pendidikan & Pengajaran
            'Mengajar', 'Literasi', 'Bimbingan Belajar', 'Pendidikan Anak',

            // Kesehatan & Sanitasi
            'Penyuluhan Kesehatan', 'Sanitasi Lingkungan', 'Gizi & Nutrisi', 'Kesehatan Ibu & Anak',

            // Pertanian & Peternakan
            'Pertanian Organik', 'Budidaya Tanaman', 'Peternakan', 'Hidroponik', 'Kompos',

            // Bisnis & Kewirausahaan
            'UMKM', 'Pemasaran Produk Lokal', 'Manajemen Usaha', 'Koperasi', 'E-commerce',

            // Infrastruktur & Konstruksi
            'Renovasi Fasilitas', 'Pembangunan', 'Pengecatan', 'Pertukangan',

            // Lingkungan
            'Pengelolaan Sampah', 'Daur Ulang', 'Reboisasi', 'Konservasi Air', 'Bank Sampah',

            // Sosial & Kemasyarakatan
            'Pemberdayaan Masyarakat', 'Posyandu', 'PKK', 'Karang Taruna', 'Gotong Royong',

            // Teknologi & Digital
            'Komputer Dasar', 'Media Sosial', 'Desain Grafis', 'Fotografi', 'Videografi',

            // Seni & Budaya
            'Kesenian Daerah', 'Kerajinan Tangan', 'Musik', 'Tari', 'Bahasa Daerah',

            // Administrasi & Dokumentasi
            'Administrasi Desa', 'Dokumentasi', 'Laporan', 'Data Entry', 'Survei'
        ];

        $sdgOptions = [
            ['id' => 1, 'name' => 'SDG 1: No Poverty', 'color' => 'red'],
            ['id' => 2, 'name' => 'SDG 2: Zero Hunger', 'color' => 'yellow'],
            ['id' => 3, 'name' => 'SDG 3: Good Health And Well-being', 'color' => 'green'],
            ['id' => 4, 'name' => 'SDG 4: Quality Education', 'color' => 'red'],
            ['id' => 5, 'name' => 'SDG 5: Gender Equality', 'color' => 'orange'],
            ['id' => 6, 'name' => 'SDG 6: Clean Water And Sanitation', 'color' => 'blue'],
            ['id' => 7, 'name' => 'SDG 7: Affordable And Clean Energy', 'color' => 'yellow'],
            ['id' => 8, 'name' => 'SDG 8: Decent Work And Economic Growth', 'color' => 'red'],
            ['id' => 9, 'name' => 'SDG 9: Industry, Innovation, And Infrastructure', 'color' => 'orange'],
            ['id' => 10, 'name' => 'SDG 10: Reduced Inequalities', 'color' => 'pink'],
            ['id' => 11, 'name' => 'SDG 11: Sustainable Cities And Communities', 'color' => 'amber'],
            ['id' => 12, 'name' => 'SDG 12: Responsible Consumption And Production', 'color' => 'yellow'],
            ['id' => 13, 'name' => 'SDG 13: Climate Action', 'color' => 'green'],
            ['id' => 14, 'name' => 'SDG 14: Life Below Water', 'color' => 'blue'],
            ['id' => 15, 'name' => 'SDG 15: Life On Land', 'color' => 'green'],
            ['id' => 16, 'name' => 'SDG 16: Peace, Justice, And Strong Institutions', 'color' => 'blue'],
            ['id' => 17, 'name' => 'SDG 17: Partnerships For The Goals', 'color' => 'blue'],
        ];

        $categories = JobCategory::orderBy('name')->get();

        return view('company.jobs.edit', compact(
            'company',
            'jobPosting',
            'departments',
            'jobTypes',
            'availableSkills',
            'sdgOptions',
            'categories'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $company = $user->company;

        // IMPLEMENTED: Validasi dan update job posting di Supabase
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'department' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'job_type' => 'required|string|max:50',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string|max:10',
            'description' => 'required|string',
            'responsibilities' => 'required|string',
            'qualifications' => 'required|string',
            'benefits' => 'nullable|string',
            'skills' => 'required|array|min:1',
            'sdg_alignment' => 'nullable|array',
            'impact_metrics' => 'nullable|string',
            'success_criteria' => 'nullable|string',
            'status' => 'nullable|in:draft,posted,closed,archived',
            'allow_guest_applications' => 'nullable|boolean',
        ]);

        // IMPLEMENTED: Update di Supabase PostgreSQL
        $jobPosting = JobPosting::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $updateData = [
            'job_category_id' => $validated['job_category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'department' => $validated['department'],
            'location' => $validated['location'],
            'job_type' => $validated['job_type'],
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'salary_currency' => $validated['salary_currency'] ?? 'USD',
            'description' => $validated['description'],
            'responsibilities' => $validated['responsibilities'],
            'qualifications' => $validated['qualifications'],
            'benefits' => $validated['benefits'] ?? null,
            'skills' => json_encode($validated['skills']),
            'sdg_alignment' => isset($validated['sdg_alignment']) ? json_encode($validated['sdg_alignment']) : null,
            'impact_metrics' => $validated['impact_metrics'] ?? null,
            'success_criteria' => $validated['success_criteria'] ?? null,
            'status' => $validated['status'] ?? $jobPosting->status,
            'allow_guest_applications' => $validated['allow_guest_applications'] ?? false,
        ];

        // Update published_at jika status berubah ke posted
        if ($validated['status'] === 'posted' && $jobPosting->status !== 'posted') {
            $updateData['published_at'] = now();
        }

        $jobPosting->update($updateData);

        return redirect()->route('company.jobs.show', $id)
            ->with('success', 'Lowongan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $company = $user->company;

        try {
            // IMPLEMENTED: Hapus job posting dari Supabase (soft delete)
            $jobPosting = JobPosting::where('id', $id)
                ->where('company_id', $company->id)
                ->firstOrFail();

            $jobPosting->delete();

            // Return JSON response for AJAX request
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lowongan berhasil dihapus!'
                ]);
            }

            return redirect()->route('company.jobs.index')
                ->with('success', 'Lowongan berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus lowongan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('company.jobs.index')
                ->with('error', 'Gagal menghapus lowongan!');
        }
    }

    public function updateLinkedin(Request $request, $id)
    {
        $user = Auth::user();
        $company = $user->company;

        // Validasi input
        $validated = $request->validate([
            'linkedin_url' => 'nullable|url',
            'sdg_goals' => 'nullable|array',
            'sdg_goals.*' => 'integer|min:1|max:17',
        ]);

        // IMPLEMENTED: Update LinkedIn URL dan required SDGs di Supabase
        $jobPosting = JobPosting::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $jobPosting->update([
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'required_sdgs' => isset($validated['sdg_goals']) ? json_encode($validated['sdg_goals']) : null,
        ]);

        return redirect()->route('company.jobs.show', $id)
            ->with('success', 'LinkedIn reference dan SDG requirements berhasil diperbarui!');
    }
}
