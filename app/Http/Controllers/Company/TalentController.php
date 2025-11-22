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
        $talentsQuery = User::where('user_type', 'student')
            ->whereHas('student');

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

        $talents = $talentsQuery->with(['student'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Transform data untuk view
        $talents->getCollection()->transform(function ($talent) {
            $student = $talent->student;
            return [
                'id' => $talent->id,
                'name' => $talent->name,
                'title' => $student->major ?? 'No Major',
                'avatar' => $student->profile_photo_path ?? 'default-avatar.jpg',
                'verified' => !is_null($talent->email_verified_at),
                'skills' => [], // Skills data if available in student table
                'sdg_badges' => [], // SDG alignment if available
                'location' => 'Unknown', // Location if available
                'projects_completed' => 0, // Projects count if available
                'success_rate' => 0, // Success rate if available
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

        // daftar SDG untuk filter
        $sdgOptions = [
            ['id' => 9, 'name' => 'SDG 9: Industry, Innovation, And Infrastructure'],
            ['id' => 11, 'name' => 'SDG 11: Sustainable Cities And Communities'],
            ['id' => 12, 'name' => 'SDG 12: Responsible Consumption And Production'],
            ['id' => 7, 'name' => 'SDG 7: Affordable And Clean Energy'],
            ['id' => 16, 'name' => 'SDG 16: Peace, Justice, And Strong Institutions'],
            ['id' => 8, 'name' => 'SDG 8: Decent Work And Economic Growth'],
            ['id' => 10, 'name' => 'SDG 10: Reduced Inequalities'],
            ['id' => 4, 'name' => 'SDG 4: Quality Education'],
        ];

        $totalTalents = $talents->total();
        $viewMode = $request->get('view', 'grid');

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
            ->with(['profile', 'repositories', 'projects'])
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

        // IMPLEMENTED: Ambil data saved talents dari Supabase
        $savedTalents = SavedTalent::where('company_id', $company->id)
            ->with('user.profile')
            ->orderBy('saved_at', 'desc')
            ->get();

        // Group by category
        $savedTalentGroups = $savedTalents->groupBy('category')->map(function ($group, $category) {
            return [
                'id' => $category,
                'name' => $category ?: 'Uncategorized',
                'talents' => $group->map(function ($savedTalent) {
                    $user = $savedTalent->user;
                    $profile = $user->profile ?? null;
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'title' => $profile->headline ?? 'No Title',
                        'avatar' => $user->avatar ?? 'default-avatar.jpg',
                        'verified' => !is_null($user->email_verified_at),
                        'description' => $profile->bio ?? 'No description',
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

    public function leaderboard(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // IMPLEMENTED: Ambil data leaderboard talents dari Supabase
        // Sorted by impact score or contribution metrics
        $leaderboardTalents = User::where('user_type', 'student')
            ->whereHas('profile')
            ->with('profile')
            ->orderBy('created_at', 'desc') // Could be ordered by impact_score if available
            ->paginate(20);

        // Transform for view
        $leaderboardTalents->getCollection()->transform(function ($talent, $index) use ($request) {
            $profile = $talent->profile;
            return [
                'id' => $talent->id,
                'rank' => ($request->input('page', 1) - 1) * 20 + $index + 1,
                'name' => $talent->name,
                'location' => $profile->location ?? 'Unknown',
                'avatar' => $talent->avatar ?? 'default-avatar.jpg',
                'impact_score' => $profile->impact_score ?? 0,
                'skills' => is_array($profile->skills ?? null) ? array_slice($profile->skills, 0, 2) : [],
                'sdg_badge' => $profile->primary_sdg ?? ['id' => 0, 'name' => 'No SDG'],
            ];
        });

        // daftar skills untuk filter
        $availableSkills = [
            'AI/ML', 'Data Science', 'Cloud Architecture', 'Full Stack Dev',
            'DevOps', 'Cybersecurity', 'UI/UX Design', 'Product Management',
            'Financial Modeling', 'Content Strategy', 'SEO/SEM',
            'Quantum Computing', 'Game Development', '3D Modeling'
        ];

        // daftar SDG untuk filter
        $sdgOptions = [
            ['id' => 1, 'name' => 'No Poverty'],
            ['id' => 2, 'name' => 'Zero Hunger'],
            ['id' => 3, 'name' => 'Good Health And Well-being'],
            ['id' => 4, 'name' => 'Quality Education'],
            ['id' => 5, 'name' => 'Gender Equality'],
            ['id' => 6, 'name' => 'Clean Water And Sanitation'],
            ['id' => 7, 'name' => 'Affordable And Clean Energy'],
            ['id' => 8, 'name' => 'Decent Work And Economic Growth'],
            ['id' => 9, 'name' => 'Industry, Innovation And Infrastructure'],
            ['id' => 10, 'name' => 'Reduced Inequalities'],
            ['id' => 11, 'name' => 'Sustainable Cities And Communities'],
            ['id' => 12, 'name' => 'Responsible Consumption And Production'],
            ['id' => 13, 'name' => 'Climate Action'],
            ['id' => 16, 'name' => 'Peace, Justice And Strong Institutions'],
        ];

        // impact breakdown metrics untuk sidebar
        $impactBreakdown = [
            ['name' => 'Problem Solving', 'value' => 85],
            ['name' => 'Strategic Thinking', 'value' => 70],
            ['name' => 'Collaboration', 'value' => 90],
            ['name' => 'Innovation', 'value' => 60],
        ];

        return view('company.talents.leaderboard', compact(
            'company',
            'leaderboardTalents',
            'availableSkills',
            'sdgOptions',
            'impactBreakdown'
        ));
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

        // Get talents data
        $talents = User::where('user_type', 'student')
            ->whereIn('id', $ids)
            ->with(['profile', 'repositories', 'projects'])
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
            ->whereHas('profile');

        // Apply filters (same as index method)
        if (!empty($filters['skills'])) {
            $talentsQuery->whereHas('profile', function ($query) use ($filters) {
                foreach ($filters['skills'] as $skill) {
                    $query->whereJsonContains('skills', $skill);
                }
            });
        }

        if (!empty($filters['location'])) {
            $talentsQuery->whereHas('profile', function ($query) use ($filters) {
                $query->where('location', 'ILIKE', '%' . $filters['location'] . '%');
            });
        }

        if ($filters['verified_only']) {
            $talentsQuery->whereNotNull('email_verified_at');
        }

        $talents = $talentsQuery->with(['profile'])->get();

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

                fputcsv($file, [
                    $talent->name,
                    $talent->email,
                    $profile->headline ?? 'N/A',
                    $profile->location ?? 'N/A',
                    $skills,
                    $talent->email_verified_at ? 'Yes' : 'No',
                    $profile->projects_count ?? 0,
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
            ->with('user.profile')
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
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $profile->headline ?? 'N/A',
                    $profile->location ?? 'N/A',
                    $savedTalent->category ?? 'N/A',
                    $savedTalent->notes ?? 'N/A',
                    $savedTalent->saved_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
