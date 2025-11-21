<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// controller untuk halaman pencarian dan browse talent oleh company
class TalentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // TO DO: ambil data filters dari request
        $filters = [
            'skills' => $request->get('skills', []),
            'sdg_alignment' => $request->get('sdg_alignment', []),
            'location' => $request->get('location', ''),
            'impact_score_min' => $request->get('impact_score_min', 0),
            'impact_score_max' => $request->get('impact_score_max', 100),
            'verified_only' => $request->get('verified_only', false),
        ];

        // TO DO: ambil data talents dari database dengan filter yang sesuai
        // untuk saat ini menggunakan data dummy
        $talents = $this->getDummyTalents();

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

        $totalTalents = count($talents);
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
        // TO DO: ambil data talent dari database berdasarkan id
        $talent = $this->getDummyTalentById($id);

        if (!$talent) {
            abort(404);
        }

        return view('company.talents.show', compact('talent'));
    }

    public function saved(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // TO DO: ambil data saved talents dari database berdasarkan company_id
        // menggunakan pivot table company_saved_talents atau sejenisnya
        $savedTalentGroups = $this->getDummySavedTalents();

        $totalSavedTalents = array_reduce($savedTalentGroups, function ($carry, $group) {
            return $carry + count($group['talents']);
        }, 0);

        return view('company.talents.saved', compact(
            'company',
            'savedTalentGroups',
            'totalSavedTalents'
        ));
    }

    // TO DO: implementasi toggle save/unsave talent
    public function toggleSave(Request $request, $id)
    {
        // TO DO: simpan atau hapus talent dari saved list di database
        // $company = Auth::user()->company;
        // $company->savedTalents()->toggle($id);

        return response()->json(['success' => true]);
    }

    // TO DO: implementasi contact talent
    public function contact(Request $request, $id)
    {
        // TO DO: kirim pesan atau request ke talent
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'required|in:message,interview_request'
        ]);

        return response()->json(['success' => true]);
    }

    // fungsi helper untuk data dummy talents
    private function getDummyTalents()
    {
        return [
            [
                'id' => 1,
                'name' => 'Anya Petrova',
                'title' => 'Senior Fullstack Developer',
                'avatar' => 'profile_13523136.jpg',
                'verified' => true,
                'skills' => ['React', 'Node.js', 'AWS', 'TypeScript'],
                'sdg_badges' => [
                    ['id' => 9, 'name' => 'SDG 9: Industry, Innovation, And Infrastructure', 'color' => 'orange'],
                    ['id' => 11, 'name' => 'SDG 11: Sustainable Cities And Communities', 'color' => 'amber'],
                ],
                'location' => 'Berlin, Germany',
                'projects_completed' => 12,
                'success_rate' => 95,
                'online' => true,
            ],
            [
                'id' => 2,
                'name' => 'Carlos Rivera',
                'title' => 'AI/ML Engineer',
                'avatar' => 'profile_13523155.jpg',
                'verified' => true,
                'skills' => ['Python', 'TensorFlow', 'NLP', 'Data Science'],
                'sdg_badges' => [
                    ['id' => 9, 'name' => 'SDG 9: Industry, Innovation, And Infrastructure', 'color' => 'orange'],
                ],
                'location' => 'San Francisco, USA',
                'algorithms_deployed' => 8,
                'impact_score' => 'A+',
                'online' => false,
            ],
            [
                'id' => 3,
                'name' => 'Mei Lin',
                'title' => 'Product Designer',
                'avatar' => 'profile_18223127.jpg',
                'verified' => false,
                'skills' => ['Figma', 'UX Research', 'Prototyping', 'UI/UX'],
                'sdg_badges' => [
                    ['id' => 12, 'name' => 'SDG 12: Responsible Consumption And Production', 'color' => 'yellow'],
                ],
                'location' => 'Shanghai, China',
                'user_feedback_cycles' => 20,
                'conversion_uplift' => 15,
                'online' => true,
            ],
            [
                'id' => 4,
                'name' => 'David Smith',
                'title' => 'DevOps Specialist',
                'avatar' => 'profile_13523136.jpg',
                'verified' => true,
                'skills' => ['Kubernetes', 'Docker', 'Jenkins', 'Ansible'],
                'sdg_badges' => [
                    ['id' => 7, 'name' => 'SDG 7: Affordable And Clean Energy', 'color' => 'yellow'],
                ],
                'location' => 'London, UK',
                'deployment_frequency' => 'Daily',
                'system_uptime' => 99.99,
                'online' => false,
            ],
            [
                'id' => 5,
                'name' => 'Fatima Zahra',
                'title' => 'Cybersecurity Analyst',
                'avatar' => 'profile_13523155.jpg',
                'verified' => true,
                'skills' => ['Threat Intel', 'Pen Testing', 'SIEM', 'Incident Response'],
                'sdg_badges' => [
                    ['id' => 16, 'name' => 'SDG 16: Peace, Justice, And Strong Institutions', 'color' => 'blue'],
                ],
                'location' => 'Dubai, UAE',
                'security_incidents_mitigated' => 50,
                'audit_compliance' => 100,
                'online' => true,
            ],
            [
                'id' => 6,
                'name' => 'Oliver Chen',
                'title' => 'Data Analyst',
                'avatar' => 'profile_18223127.jpg',
                'verified' => false,
                'skills' => ['SQL', 'Power BI', 'Excel', 'Statistical Analysis'],
                'sdg_badges' => [
                    ['id' => 8, 'name' => 'SDG 8: Decent Work And Economic Growth', 'color' => 'red'],
                ],
                'location' => 'Sydney, Australia',
                'reports_generated' => 30,
                'insight_discovery_rate' => 85,
                'online' => false,
            ],
            [
                'id' => 7,
                'name' => 'Sophia Rodriguez',
                'title' => 'Marketing Manager',
                'avatar' => 'profile_13523136.jpg',
                'verified' => true,
                'skills' => ['SEO', 'Content Marketing', 'Social Media', 'Analytics'],
                'sdg_badges' => [
                    ['id' => 10, 'name' => 'SDG 10: Reduced Inequalities', 'color' => 'pink'],
                ],
                'location' => 'Madrid, Spain',
                'campaign_roi' => 180,
                'brand_reach_increase' => 40,
                'online' => true,
            ],
            [
                'id' => 8,
                'name' => 'Kwame Nkrumah',
                'title' => 'Project Manager',
                'avatar' => 'profile_13523155.jpg',
                'verified' => true,
                'skills' => ['Agile', 'Scrum', 'Risk Management', 'Stakeholder Mgmt'],
                'sdg_badges' => [
                    ['id' => 4, 'name' => 'SDG 4: Quality Education', 'color' => 'red'],
                ],
                'location' => 'Accra, Ghana',
                'projects_delivered' => 7,
                'on_time_delivery' => 90,
                'online' => false,
            ],
        ];
    }

    // TO DO: implementasi fungsi untuk mendapatkan talent berdasarkan id dari database
    private function getDummyTalentById($id)
    {
        $talents = $this->getDummyTalents();
        foreach ($talents as $talent) {
            if ($talent['id'] == $id) {
                return $talent;
            }
        }
        return null;
    }

    // fungsi helper untuk data dummy saved talents (dikelompokkan per kategori)
    private function getDummySavedTalents()
    {
        return [
            [
                'id' => 1,
                'name' => 'AI & Machine Learning Specialists',
                'talents' => [
                    [
                        'id' => 1,
                        'name' => 'Alice Johnson',
                        'title' => 'Senior AI Engineer',
                        'avatar' => 'profile_13523136.jpg',
                        'verified' => true,
                        'description' => 'Innovator in natural language processing and deep learning applications. Proven track record in scalable AI solutions.',
                    ],
                    [
                        'id' => 2,
                        'name' => 'Bob Lee',
                        'title' => 'Machine Learning Scientist',
                        'avatar' => 'profile_13523155.jpg',
                        'verified' => true,
                        'description' => 'Specializes in predictive modeling and data-driven insights. Published research in reinforcement learning.',
                    ],
                    [
                        'id' => 3,
                        'name' => 'Carol White',
                        'title' => 'AI Ethics Researcher',
                        'avatar' => 'profile_18223127.jpg',
                        'verified' => false,
                        'description' => 'Dedicated to developing responsible AI frameworks and ensuring ethical implementation of new technologies.',
                    ],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Marketing & Growth Experts',
                'talents' => [
                    [
                        'id' => 4,
                        'name' => 'David Green',
                        'title' => 'Head of Digital Marketing',
                        'avatar' => 'profile_13523136.jpg',
                        'verified' => true,
                        'description' => 'Achieved 300% growth in organic traffic for previous startups through innovative SEO and content strategies.',
                    ],
                    [
                        'id' => 5,
                        'name' => 'Eve Black',
                        'title' => 'Brand Strategist',
                        'avatar' => 'profile_13523155.jpg',
                        'verified' => true,
                        'description' => 'Expert in brand identity development and market positioning. Drives consumer engagement and loyalty.',
                    ],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Recent Graduates - High Potential',
                'talents' => [
                    [
                        'id' => 6,
                        'name' => 'Frank Miller',
                        'title' => 'Junior Software Developer',
                        'avatar' => 'profile_18223127.jpg',
                        'verified' => false,
                        'description' => 'Fresh graduate with strong foundation in web development. Eager to learn and contribute to innovative projects.',
                    ],
                ],
            ],
        ];
    }
}
