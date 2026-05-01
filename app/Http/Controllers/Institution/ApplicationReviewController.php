<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * controller untuk review aplikasi mahasiswa oleh instansi
 * 
 * PERBAIKAN BUG:
 * - ubah status 'reviewed' menjadi 'under_review' untuk konsistensi
 * - status tidak berubah otomatis saat aplikasi dibuka
 * - hanya ada 4 status: pending, under_review, accepted, rejected
 */
class ApplicationReviewController extends Controller
{
    /**
     * tampilkan daftar aplikasi yang masuk
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;

        // query base
        $query = Application::with([
            'student.user',
            'student.university',
            'problem'
        ])
        ->whereHas('problem', function($q) use ($institution) {
            $q->where('institution_id', $institution->id);
        });

        // filter berdasarkan search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // PERBAIKAN: filter status menggunakan 'under_review' bukan 'reviewed'
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // filter berdasarkan problem
        if ($request->filled('problem_id')) {
            $query->where('problem_id', $request->problem_id);
        }

        // sorting
        switch ($request->sort) {
            case 'oldest':
                $query->oldest('applied_at');
                break;
            case 'name':
                $query->join('students', 'applications.student_id', '=', 'students.id')
                     ->join('users', 'students.user_id', '=', 'users.id')
                     ->orderBy('users.name')
                     ->select('applications.*');
                break;
            default:
                $query->latest('applied_at');
        }

        $applications = $query->paginate(10)->withQueryString();

        // PERBAIKAN: statistik menggunakan 'under_review' bukan 'reviewed'
        $baseQuery = Application::whereHas('problem', function($q) use ($institution) {
            $q->where('institution_id', $institution->id);
        });

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'under_review' => (clone $baseQuery)->where('status', 'under_review')->count(),
            'accepted' => (clone $baseQuery)->where('status', 'accepted')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        // daftar problems untuk filter dropdown
        $problems = $institution->problems()->orderBy('title')->get(['id', 'title']);

        return view('institution.applications.index', compact('applications', 'stats', 'problems'));
    }

    /**
     * tampilkan detail aplikasi untuk review
     * 
     * PERBAIKAN: status TIDAK berubah otomatis saat dibuka
     * status hanya berubah saat institution melakukan action
     */
    public function show($id)
    {
        $institution = auth()->user()->institution;

        $application = Application::with([
            'student.user',
            'student.university',
            'problem.images',
            'problem.province',
            'problem.regency'
        ])
        ->whereHas('problem', function($q) use ($institution) {
            $q->where('institution_id', $institution->id);
        })
        ->findOrFail($id);

        // PERBAIKAN: HAPUS auto-update status saat view
        // biarkan status tetap seperti semula
        // status hanya berubah saat institution melakukan action (accept/reject)

        return view('institution.applications.show', compact('application'));
    }

    /**
     * tampilkan form review aplikasi
     */
    public function review($id)
    {
        $institution = auth()->user()->institution;

        $application = Application::with([
            'student.user',
            'student.university',
            'problem'
        ])
        ->whereHas('problem', function($q) use ($institution) {
            $q->where('institution_id', $institution->id);
        })
        ->findOrFail($id);

        // PERBAIKAN: hanya bisa review jika status pending atau under_review
        if (!in_array($application->status, ['pending', 'under_review'])) {
            return redirect()->route('institution.applications.show', $application->id)
                           ->with('error', 'aplikasi ini sudah diproses');
        }

        return view('institution.applications.review', compact('application'));
    }

    /**
     * terima aplikasi mahasiswa
     */
    public function accept(Request $request, $id)
    {
        $institution = auth()->user()->institution;

        $application = Application::with('problem')
            ->whereHas('problem', function($q) use ($institution) {
                $q->where('institution_id', $institution->id);
            })
            ->findOrFail($id);

        // PERBAIKAN: validasi status menggunakan 'under_review'
        if (!in_array($application->status, ['pending', 'under_review'])) {
            return back()->with('error', 'aplikasi ini sudah diproses');
        }

        // validasi slot mahasiswa
        $problem = $application->problem;
        
        if ($problem->accepted_students >= $problem->required_students) {
            return back()->with('error', 'slot mahasiswa untuk masalah ini sudah penuh');
        }

        $validated = $request->validate([
            'feedback' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // update application
            $application->update([
                'status' => 'accepted',
                'reviewed_at' => now(),
                'feedback' => $validated['feedback'] ?? null,
                'institution_notes' => $validated['notes'] ?? null,
            ]);

            // buat project otomatis
            Project::create([
                'application_id' => $application->id,
                'student_id' => $application->student_id,
                'problem_id' => $application->problem_id,
                'institution_id' => $institution->id,
                'title' => $problem->title,
                'description' => $problem->description,
                'start_date' => $problem->start_date,
                'end_date' => $problem->end_date,
                'status' => 'active',
                'role_in_team' => 'anggota tim',
            ]);

            // update counter problem
            $problem->increment('accepted_students');

            DB::commit();

            return redirect()->route('institution.applications.show', $application->id)
                           ->with('success', 'aplikasi berhasil diterima dan proyek telah dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * tolak aplikasi mahasiswa
     */
    public function reject(Request $request, $id)
    {
        $institution = auth()->user()->institution;

        $application = Application::with('problem')
            ->whereHas('problem', function($q) use ($institution) {
                $q->where('institution_id', $institution->id);
            })
            ->findOrFail($id);

        // PERBAIKAN: validasi menggunakan 'under_review'
        if (!in_array($application->status, ['pending', 'under_review'])) {
            return back()->with('error', 'aplikasi ini sudah diproses');
        }

        $validated = $request->validate([
            'feedback' => 'required|string|max:1000',
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // update application
            $application->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'feedback' => $validated['feedback'],
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            DB::commit();

            return redirect()->route('institution.applications.show', $application->id)
                           ->with('success', 'aplikasi telah ditolak');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * batalkan keputusan review (kembalikan ke pending)
     */
    public function cancel($id)
    {
        $institution = auth()->user()->institution;

        $application = Application::with('problem')
            ->whereHas('problem', function($q) use ($institution) {
                $q->where('institution_id', $institution->id);
            })
            ->findOrFail($id);

        // hanya bisa cancel jika rejected atau accepted (dan belum ada project yang aktif)
        if (!in_array($application->status, ['rejected', 'accepted'])) {
            return back()->with('error', 'tidak dapat membatalkan review untuk aplikasi ini');
        }

        // jika accepted, cek apakah sudah ada project
        if ($application->status === 'accepted') {
            $project = Project::where('student_id', $application->student_id)
                             ->where('problem_id', $application->problem_id)
                             ->where('status', 'active')
                             ->first();
            
            if ($project) {
                return back()->with('error', 'tidak dapat membatalkan karena proyek sudah berjalan');
            }
        }

        try {
            DB::beginTransaction();

            $wasAccepted = $application->status === 'accepted';

            // kembalikan ke pending
            $application->update([
                'status' => 'pending',
                'reviewed_at' => null,
                'feedback' => null,
                'rejection_reason' => null,
                'institution_notes' => null,
            ]);

            // jika ada project, hapus
            Project::where('student_id', $application->student_id)
                  ->where('problem_id', $application->problem_id)
                  ->where('status', 'planning')
                  ->delete();

            // update counter problem jika sebelumnya accepted
            if ($wasAccepted) {
                $application->problem->decrement('accepted_students');
            }

            DB::commit();

            return redirect()->route('institution.applications.show', $application->id)
                           ->with('success', 'review berhasil dibatalkan');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * download proposal dari database
     * validasi: aplikasi harus milik institusi yang sedang login
     */
    public function downloadProposal($id)
    {
        $institution = auth()->user()->institution;

        // ambil aplikasi yang milik institusi ini
        $application = Application::with('problem')
            ->whereHas('problem', function($q) use ($institution) {
                $q->where('institution_id', $institution->id);
            })
            ->findOrFail($id);

        // cek apakah ada proposal
        if (!$application->proposal_content) {
            abort(404, 'Proposal tidak ditemukan');
        }

        Log::info("Institution downloading proposal", [
            'application_id' => $id,
            'filename' => $application->proposal_filename,
            'institution_id' => $institution->id,
        ]);

        // decode base64 content
        $fileContent = base64_decode($application->proposal_content);

        // return file sebagai download
        return response($fileContent)
            ->header('Content-Type', $application->proposal_mime_type)
            ->header('Content-Disposition', 'inline; filename="' . $application->proposal_filename . '"')
            ->header('Content-Length', strlen($fileContent));
    }

    /**
     * bulk action untuk multiple applications
     */
    public function bulkAction(Request $request)
    {
        $institution = auth()->user()->institution;

        $validated = $request->validate([
            'action' => 'required|in:accept,reject,delete',
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'exists:applications,id',
            'feedback' => 'nullable|string|max:1000',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $successCount = 0;
            $errorCount = 0;

            foreach ($validated['application_ids'] as $applicationId) {
                try {
                    $application = Application::with('problem')
                        ->whereHas('problem', function($q) use ($institution) {
                            $q->where('institution_id', $institution->id);
                        })->findOrFail($applicationId);

                    switch ($validated['action']) {
                        case 'accept':
                            // PERBAIKAN: cek status menggunakan 'under_review'
                            if (in_array($application->status, ['pending', 'under_review'])) {
                                $problem = $application->problem;
                                
                                if ($problem->accepted_students < $problem->required_students) {
                                    $application->update([
                                        'status' => 'accepted',
                                        'reviewed_at' => now(),
                                        'feedback' => $validated['feedback'] ?? null,
                                        'institution_notes' => $validated['notes'] ?? null,
                                    ]);

                                    // buat project
                                    Project::create([
                                        'application_id' => $application->id,
                                        'student_id' => $application->student_id,
                                        'problem_id' => $application->problem_id,
                                        'institution_id' => $institution->id,
                                        'title' => $problem->title,
                                        'description' => $problem->description,
                                        'start_date' => $problem->start_date,
                                        'end_date' => $problem->end_date,
                                        'status' => 'active',
                                        'role_in_team' => 'anggota tim',
                                    ]);

                                    $problem->increment('accepted_students');
                                    $successCount++;
                                } else {
                                    $errorCount++;
                                }
                            } else {
                                $errorCount++;
                            }
                            break;

                        case 'reject':
                            // PERBAIKAN: cek status menggunakan 'under_review'
                            if (in_array($application->status, ['pending', 'under_review'])) {
                                $application->update([
                                    'status' => 'rejected',
                                    'reviewed_at' => now(),
                                    'feedback' => $validated['feedback'] ?? 'aplikasi ditolak',
                                    'rejection_reason' => $validated['rejection_reason'] ?? 'tidak sesuai kriteria',
                                ]);
                                $successCount++;
                            } else {
                                $errorCount++;
                            }
                            break;

                        case 'delete':
                            // hanya bisa delete jika pending atau rejected
                            if (in_array($application->status, ['pending', 'rejected'])) {
                                $application->delete();
                                $successCount++;
                            } else {
                                $errorCount++;
                            }
                            break;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    continue;
                }
            }

            DB::commit();

            $message = "{$successCount} aplikasi berhasil diproses";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} aplikasi gagal diproses";
            }

            return redirect()->route('institution.applications.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'terjadi kesalahan: ' . $e->getMessage());
        }
    }
}