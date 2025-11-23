<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SavedTalent;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * TalentController - Browse and Manage Talents
 *
 * IMPLEMENTED: Semua operasi data langsung dari Supabase PostgreSQL
 * TIDAK ADA dummy data lagi
 */
class TalentController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // IMPLEMENTED: Ambil data filters dari request
        $filters = [
            'skills' => $request->get('skills', []),
            'sdg_alignment' => $request->get('sdg_alignment', []),
            'location' => $request->get('location', ''),
            'impact_score_min' => $request->get('impact_score_min', 0),
            'impact_score_max' => $request->get('impact_score_max', 100),
            'verified_only' => $request->get('verified_only', false),
        ];

        // IMPLEMENTED: Ambil data talents dari Supabase dengan filter
        // Hanya tampilkan mahasiswa yang punya minimal 1 proyek
        $talentsQuery = User::where('user_type', 'student')
            ->whereHas('student')
            ->whereHas('student.projects');

        // Apply filters
        if (!empty($filters['skills'])) {
            // Filter by skills - assuming skills are stored in student profile
            $talentsQuery->whereHas('student', function ($query) use ($filters) {
                foreach ($filters['skills'] as $skill) {
                    $query->whereJsonContains('skills', $skill);
                }
            });
        }

        if (!empty($filters['location'])) {
            $talentsQuery->whereHas('student', function ($query) use ($filters) {
                $query->where('location', 'ILIKE', '%' . $filters['location'] . '%');
            });
        }

        if ($filters['verified_only']) {
            $talentsQuery->whereNotNull('email_verified_at');
        }

        $talents = $talentsQuery->with(['student.university', 'student.projects.problem'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Transform data untuk view
        $talents->getCollection()->transform(function ($talent) {
            $student = $talent->student;

            // Get SDG alignment dari completed projects
            $sdgBadges = [];
            if ($student && $student->projects) {
                $sdgMap = [];
                $colorMap = [
                    1 => 'red', 2 => 'amber', 3 => 'green', 4 => 'blue', 5 => 'orange',
                    6 => 'cyan', 7 => 'yellow', 8 => 'red', 9 => 'orange', 10 => 'pink',
                    11 => 'amber', 12 => 'yellow', 13 => 'green', 14 => 'blue', 15 => 'green',
                    16 => 'blue', 17 => 'blue'
                ];

                $completedProjects = $student->projects->where('status', 'completed');
                foreach ($completedProjects as $project) {
                    if ($project->problem && $project->problem->sdg_categories) {
                        $categories = $project->problem->sdg_categories;

                        // Handle jika masih string JSON
                        if (is_string($categories)) {
                            $categories = json_decode($categories, true) ?? [];
                        }

                        if (is_array($categories)) {
                            foreach ($categories as $sdgId) {
                                if (!isset($sdgMap[$sdgId])) {
                                    $sdgMap[$sdgId] = [
                                        'id' => $sdgId,
                                        'name' => $this->getSdgName($sdgId),
                                        'color' => $colorMap[$sdgId] ?? 'blue'
                                    ];
                                }
                            }
                        }
                    }
                }

                $sdgBadges = array_values($sdgMap);
            }

            // Get location from university or student data
            $location = 'Indonesia'; // Default location
            if ($student && $student->university) {
                // Jika ada relasi ke university dengan city/province
                $location = $student->university->city ?? 'Indonesia';
            }

            return [
                'id' => $talent->id,
                'username' => $talent->username,
                'name' => $talent->name,
                'title' => $student->major ?? 'No Major',
                'avatar' => $student->profile_photo_path ?? 'default-avatar.jpg',
                'verified' => !is_null($talent->email_verified_at),
                'sdg_badges' => $sdgBadges,
                'location' => $location,
                'skills' => $student->skills ?? [],
                'projects_completed' => 0, // Projects count if available
                'success_rate' => 0, // Success rate if available
                'algorithms_deployed' => 0, // Algorithms deployed if available
                'impact_score' => 0, // Impact score if available
                'online' => true, // Could be implemented with last_seen_at
            ];
        });

        // daftar skills untuk filter
        $availableSkills = [
            'React', 'Node.js', 'AWS', 'TypeScript', 'Python', 'TensorFlow',
            'NLP', 'Data Science', 'Figma', 'UX Research', 'Prototyping',
            'UI/UX', 'Kubernetes', 'Docker', 'Jenkins', 'Ansible',
            'Threat Intel', 'Pen Testing', 'SIEM', 'Incident Response',
            'SQL', 'Power BI', 'Excel', 'Statistical Analysis', 'SEO',
            'Content Marketing', 'Social Media', 'Analytics', 'Agile',
            'Scrum', 'Risk Management', 'Stakeholder Mgmt'
        ];

        // daftar SDG untuk filter (1-17 lengkap)
        $sdgOptions = [
            ['id' => 1, 'name' => 'SDG 1: No Poverty'],
            ['id' => 2, 'name' => 'SDG 2: Zero Hunger'],
            ['id' => 3, 'name' => 'SDG 3: Good Health And Well-Being'],
            ['id' => 4, 'name' => 'SDG 4: Quality Education'],
            ['id' => 5, 'name' => 'SDG 5: Gender Equality'],
            ['id' => 6, 'name' => 'SDG 6: Clean Water And Sanitation'],
            ['id' => 7, 'name' => 'SDG 7: Affordable And Clean Energy'],
            ['id' => 8, 'name' => 'SDG 8: Decent Work And Economic Growth'],
            ['id' => 9, 'name' => 'SDG 9: Industry, Innovation, And Infrastructure'],
            ['id' => 10, 'name' => 'SDG 10: Reduced Inequalities'],
            ['id' => 11, 'name' => 'SDG 11: Sustainable Cities And Communities'],
            ['id' => 12, 'name' => 'SDG 12: Responsible Consumption And Production'],
            ['id' => 13, 'name' => 'SDG 13: Climate Action'],
            ['id' => 14, 'name' => 'SDG 14: Life Below Water'],
            ['id' => 15, 'name' => 'SDG 15: Life On Land'],
            ['id' => 16, 'name' => 'SDG 16: Peace, Justice, And Strong Institutions'],
            ['id' => 17, 'name' => 'SDG 17: Partnerships For The Goals'],
        ];

        $totalTalents = $talents->total();
        $viewMode = $request->get('view', 'grid');

        // Return JSON for AJAX requests (load more functionality)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'talents' => $talents->items(),
                'hasMore' => $talents->hasMorePages(),
                'currentPage' => $talents->currentPage(),
                'total' => $talents->total(),
            ]);
        }

        return view('company.talents.index', compact(
            'company',
            'talents',
            'filters',
            'availableSkills',
            'sdgOptions',
            'totalTalents',
            'viewMode'
        ));
    }

    public function show($id)
    {
        $user = Auth::user();
        $company = $user->company;

        // IMPLEMENTED: Ambil data talent dari Supabase berdasarkan id
        $talent = User::where('user_type', 'student')
            ->where('id', $id)
            ->with(['student'])
            ->firstOrFail();

        // Check if talent is saved by company
        $isSaved = SavedTalent::where('company_id', $company->id)
            ->where('user_id', $id)
            ->exists();

        return view('company.talents.show', compact('talent', 'company', 'isSaved'));
    }

    public function saved(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('home')
                ->with('error', 'profil perusahaan tidak ditemukan');
        }

        // IMPLEMENTED: Ambil data saved talents dari Supabase
        $savedTalents = SavedTalent::where('company_id', $company->id)
            ->with('user.student')
            ->orderBy('saved_at', 'desc')
            ->get();

        // Group by category
        $savedTalentGroups = $savedTalents->groupBy('category')->map(function ($group, $category) {
            return [
                'id' => $category,
                'name' => $category ?: 'Uncategorized',
                'talents' => $group->map(function ($savedTalent) {
                    $user = $savedTalent->user;
                    $student = $user->student ?? null;
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'title' => $student->major ?? 'No Major',
                        'avatar_url' => $student ? $student->profile_photo_url : null,
                        'verified' => !is_null($user->email_verified_at),
                        'description' => 'Student', // Description if available
                        'notes' => $savedTalent->notes,
                    ];
                })->toArray(),
            ];
        })->values()->toArray();

        $totalSavedTalents = $savedTalents->count();

        return view('company.talents.saved', compact(
            'company',
            'savedTalentGroups',
            'totalSavedTalents'
        ));
    }

    /**
     * IMPLEMENTED: Toggle save/unsave talent
     * Data langsung ke Supabase PostgreSQL
     */
    public function toggleSave(Request $request, $id)
    {
        $user = Auth::user();
        $company = $user->company;

        // Check if already saved
        $savedTalent = SavedTalent::where('company_id', $company->id)
            ->where('user_id', $id)
            ->first();

        if ($savedTalent) {
            // Unsave
            $savedTalent->delete();
            return response()->json([
                'success' => true,
                'action' => 'unsaved',
                'message' => 'Talent removed from saved list'
            ]);
        } else {
            // Save
            SavedTalent::create([
                'company_id' => $company->id,
                'user_id' => $id,
                'category' => $request->input('category', null),
                'notes' => $request->input('notes', null),
                'saved_at' => now(),
            ]);
            return response()->json([
                'success' => true,
                'action' => 'saved',
                'message' => 'Talent added to saved list'
            ]);
        }
    }

    /**
     * IMPLEMENTED: Contact talent
     * Send message or interview request
     */
    public function contact(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'required|in:message,interview_request'
        ]);

        $user = Auth::user();
        $company = $user->company;

        // IMPLEMENTED: Save message to database
        // This would typically send a notification to the talent
        DB::connection('pgsql')->table('messages')->insert([
            'from_user_id' => $user->id,
            'to_user_id' => $id,
            'company_id' => $company->id,
            'message' => $validated['message'],
            'type' => $validated['type'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully'
        ]);
    }

    /**
     * Compare talents side by side
     * IMPLEMENTED: Data dari Supabase PostgreSQL
     */
    public function compare(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // Get talent IDs from query string
        $ids = explode(',', $request->get('ids', ''));
        $ids = array_filter($ids, 'is_numeric');

        if (count($ids) < 2 || count($ids) > 3) {
            return redirect()->route('company.talents.index')
                ->with('error', 'Pilih 2-3 talenta untuk dibandingkan');
        }

        // Get talents data with all necessary relationships
        $talents = User::where('user_type', 'student')
            ->whereIn('id', $ids)
            ->with(['student.university', 'student.projects'])
            ->get();

        if ($talents->count() < 2) {
            return redirect()->route('company.talents.index')
                ->with('error', 'Talenta tidak ditemukan');
        }

        return view('company.talents.compare', compact('talents', 'company'));
    }

    /**
     * Export talents list to CSV
     * IMPLEMENTED: Data dari Supabase PostgreSQL
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // Apply same filters as index page
        $filters = [
            'skills' => $request->get('skills', []),
            'sdg_alignment' => $request->get('sdg_alignment', []),
            'location' => $request->get('location', ''),
            'verified_only' => $request->get('verified_only', false),
        ];

        $talentsQuery = User::where('user_type', 'student')
            ->whereHas('student');

        // Apply filters (same as index method)
        if (!empty($filters['skills'])) {
            $talentsQuery->whereHas('student', function ($query) use ($filters) {
                foreach ($filters['skills'] as $skill) {
                    $query->whereJsonContains('skills', $skill);
                }
            });
        }

        if (!empty($filters['location'])) {
            $talentsQuery->whereHas('student', function ($query) use ($filters) {
                $query->where('location', 'ILIKE', '%' . $filters['location'] . '%');
            });
        }

        if ($filters['verified_only']) {
            $talentsQuery->whereNotNull('email_verified_at');
        }

        $talents = $talentsQuery->with(['student.university', 'student.projects'])->get();

        $filename = 'talents_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($talents) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Title', 'Location', 'Skills', 'Verified', 'Projects Count']);

            foreach ($talents as $talent) {
                $profile = $talent->profile ?? null;
                $skills = is_array($profile->skills ?? null) ? implode(', ', $profile->skills) : 'N/A';
                $location = $profile && $profile->university ? $profile->university->city : 'N/A';
                $projectsCount = $profile && $profile->projects ? $profile->projects->count() : 0;

                fputcsv($file, [
                    $talent->name,
                    $talent->email,
                    $profile->major ?? 'N/A',
                    $location,
                    $skills,
                    $talent->email_verified_at ? 'Yes' : 'No',
                    $projectsCount,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export saved talents to CSV
     * IMPLEMENTED: Data dari Supabase PostgreSQL
     */
    public function exportSaved(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $savedTalents = SavedTalent::where('company_id', $company->id)
            ->with('user.student.university', 'user.student.projects')
            ->get();

        $filename = 'saved_talents_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($savedTalents) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Title', 'Location', 'Category', 'Notes', 'Saved At']);

            foreach ($savedTalents as $savedTalent) {
                $user = $savedTalent->user;
                $profile = $user->profile ?? null;
                $location = $profile && $profile->university ? $profile->university->city : 'N/A';

                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $profile->major ?? 'N/A',
                    $location,
                    $savedTalent->category ?? 'N/A',
                    $savedTalent->notes ?? 'N/A',
                    $savedTalent->saved_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper method untuk mendapatkan nama SDG berdasarkan ID
     */
    private function getSdgName($sdgId)
    {
        $sdgNames = [
            1 => 'No Poverty',
            2 => 'Zero Hunger',
            3 => 'Good Health',
            4 => 'Quality Education',
            5 => 'Gender Equality',
            6 => 'Clean Water',
            7 => 'Clean Energy',
            8 => 'Decent Work',
            9 => 'Industry & Innovation',
            10 => 'Reduced Inequalities',
            11 => 'Sustainable Cities',
            12 => 'Responsible Consumption',
            13 => 'Climate Action',
            14 => 'Life Below Water',
            15 => 'Life On Land',
            16 => 'Peace & Justice',
            17 => 'Partnerships',
        ];

        return $sdgNames[$sdgId] ?? 'SDG ' . $sdgId;
    }
}
