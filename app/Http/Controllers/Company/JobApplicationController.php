<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// controller untuk halaman manajemen job applications oleh company
class JobApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $viewMode = $request->get('view', 'kanban');

        // TO DO: ambil data applications dari database dengan grouping by status
        $applications = $this->getDummyApplications();

        // group applications by status untuk kanban view
        $columns = [
            'new' => [
                'title' => 'Lamaran Baru',
                'color' => 'blue',
                'applications' => array_filter($applications, fn($a) => $a['status'] === 'new')
            ],
            'reviewing' => [
                'title' => 'Sedang Direview',
                'color' => 'yellow',
                'applications' => array_filter($applications, fn($a) => $a['status'] === 'reviewing')
            ],
            'shortlisted' => [
                'title' => 'Terpilih',
                'color' => 'orange',
                'applications' => array_filter($applications, fn($a) => $a['status'] === 'shortlisted')
            ],
            'interview' => [
                'title' => 'Interview',
                'color' => 'purple',
                'applications' => array_filter($applications, fn($a) => $a['status'] === 'interview')
            ],
            'offer' => [
                'title' => 'Penawaran',
                'color' => 'green',
                'applications' => array_filter($applications, fn($a) => $a['status'] === 'offer')
            ],
            'rejected' => [
                'title' => 'Ditolak',
                'color' => 'red',
                'applications' => array_filter($applications, fn($a) => $a['status'] === 'rejected')
            ],
        ];

        $totalApplications = count($applications);

        return view('company.applications.index', compact(
            'company',
            'columns',
            'applications',
            'totalApplications',
            'viewMode'
        ));
    }

    public function show($id)
    {
        // TO DO: ambil data application dari database
        return view('company.applications.show');
    }

    public function updateStatus(Request $request, $id)
    {
        // TO DO: update status application di database
        $validated = $request->validate([
            'status' => 'required|in:new,reviewing,shortlisted,interview,offer,rejected'
        ]);

        // TO DO: update di database
        // $application = JobApplication::findOrFail($id);
        // $application->update(['status' => $validated['status']]);

        return response()->json(['success' => true]);
    }

    public function shortlist($id)
    {
        // TO DO: update status ke shortlisted
        return redirect()->back()->with('success', 'Kandidat berhasil ditambahkan ke shortlist');
    }

    public function reject($id)
    {
        // TO DO: update status ke rejected
        return redirect()->back()->with('success', 'Kandidat ditolak');
    }

    public function hire($id)
    {
        // TO DO: update status ke hired dan proses onboarding
        return redirect()->back()->with('success', 'Kandidat berhasil direkrut');
    }

    // fungsi helper untuk data dummy applications
    private function getDummyApplications()
    {
        return [
            [
                'id' => 1,
                'name' => 'Alice Johnson',
                'position' => 'Senior UI/UX Designer',
                'avatar' => 'profile_13523136.jpg',
                'applied_date' => '2023-10-26',
                'status' => 'new',
            ],
            [
                'id' => 2,
                'name' => 'Grace Lee',
                'position' => 'Financial Analyst',
                'avatar' => 'profile_13523155.jpg',
                'applied_date' => '2023-10-20',
                'status' => 'new',
            ],
            [
                'id' => 3,
                'name' => 'Ivy Queen',
                'position' => 'Operations Manager',
                'avatar' => 'profile_18223127.jpg',
                'applied_date' => '2023-10-18',
                'status' => 'new',
            ],
            [
                'id' => 4,
                'name' => 'Bob Williams',
                'position' => 'Lead Software Engineer',
                'avatar' => 'profile_13523136.jpg',
                'applied_date' => '2023-10-25',
                'status' => 'reviewing',
            ],
            [
                'id' => 5,
                'name' => 'Henry King',
                'position' => 'DevOps Engineer',
                'avatar' => 'profile_13523155.jpg',
                'applied_date' => '2023-10-19',
                'status' => 'reviewing',
            ],
            [
                'id' => 6,
                'name' => 'Charlie Brown',
                'position' => 'Product Manager',
                'avatar' => 'profile_18223127.jpg',
                'applied_date' => '2023-10-24',
                'status' => 'shortlisted',
            ],
            [
                'id' => 7,
                'name' => 'Diana Prince',
                'position' => 'Marketing Specialist',
                'avatar' => 'profile_13523136.jpg',
                'applied_date' => '2023-10-23',
                'status' => 'interview',
            ],
            [
                'id' => 8,
                'name' => 'Eve Adams',
                'position' => 'Data Scientist',
                'avatar' => 'profile_13523155.jpg',
                'applied_date' => '2023-10-22',
                'status' => 'offer',
            ],
            [
                'id' => 9,
                'name' => 'Frank White',
                'position' => 'HR Business Partner',
                'avatar' => 'profile_18223127.jpg',
                'applied_date' => '2023-10-21',
                'status' => 'rejected',
            ],
        ];
    }
}
