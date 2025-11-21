<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// controller untuk halaman manajemen job posting oleh company
class JobPostingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        // TO DO: ambil data job postings dari database
        $jobPostings = [];

        return view('company.jobs.index', compact('company', 'jobPostings'));
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

        // daftar skills untuk multi-select
        $availableSkills = [
            'React', 'Node.js', 'AWS', 'TypeScript', 'Python', 'TensorFlow',
            'NLP', 'Data Science', 'Figma', 'UX Research', 'Prototyping',
            'UI/UX', 'Kubernetes', 'Docker', 'Jenkins', 'Ansible',
            'SQL', 'Power BI', 'Excel', 'Statistical Analysis', 'SEO',
            'Content Marketing', 'Social Media', 'Analytics', 'Agile',
            'Scrum', 'Risk Management', 'Stakeholder Management',
            'Java', 'Go', 'Rust', 'PHP', 'Laravel', 'Vue.js', 'Angular'
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
        // TO DO: validasi dan simpan job posting ke database
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'job_type' => 'required|string|max:50',
            'salary_range' => 'nullable|string|max:100',
            'description' => 'required|string',
            'responsibilities' => 'required|string',
            'qualifications' => 'required|string',
            'skills' => 'required|array|min:1',
            'sdg_alignment' => 'nullable|array',
            'impact_metrics' => 'nullable|string',
            'success_criteria' => 'nullable|string',
        ]);

        // TO DO: simpan ke database
        // $jobPosting = JobPosting::create([...]);

        return redirect()->route('company.jobs.index')
            ->with('success', 'Lowongan berhasil dibuat!');
    }

    public function show($id)
    {
        // TO DO: ambil data job posting dari database
        return view('company.jobs.show');
    }

    public function edit($id)
    {
        // TO DO: ambil data job posting dari database untuk diedit
        return view('company.jobs.edit');
    }

    public function update(Request $request, $id)
    {
        // TO DO: validasi dan update job posting di database
        return redirect()->route('company.jobs.show', $id)
            ->with('success', 'Lowongan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // TO DO: hapus job posting dari database
        return redirect()->route('company.jobs.index')
            ->with('success', 'Lowongan berhasil dihapus!');
    }
}
