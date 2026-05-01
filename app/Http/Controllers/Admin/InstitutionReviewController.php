<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\AIValidationLog;
use App\Mail\InstitutionApproved;
use App\Mail\InstitutionRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * InstitutionReviewController
 *
 * Handles admin review and approval/rejection of institutions
 * Supports both AI-validated and manually reviewed institutions
 */
class InstitutionReviewController extends Controller
{
    /**
     * Admin dashboard - overview of all institutions
     */
    public function dashboard()
    {
        $stats = [
            'pending_ai' => Institution::where('ai_validation_status', 'pending_ai_validation')->count(),
            'manual_review' => Institution::where('ai_validation_status', 'manual_review')->count(),
            'approved' => Institution::where('ai_validation_status', 'approved')->count(),
            'rejected' => Institution::where('ai_validation_status', 'rejected')->count(),
            'failed' => Institution::where('ai_validation_status', 'failed')->count(),
        ];

        $recentInstitutions = Institution::with(['province', 'regency', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentInstitutions'));
    }

    /**
     * List institutions pending AI validation
     */
    public function pending()
    {
        $institutions = Institution::with(['province', 'regency', 'user'])
            ->where('ai_validation_status', 'pending_ai_validation')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.institutions.pending', compact('institutions'));
    }

    /**
     * List institutions requiring manual review
     * (AI score 80-84 - borderline cases)
     */
    public function manualReview()
    {
        $institutions = Institution::with(['province', 'regency', 'user'])
            ->where('ai_validation_status', 'manual_review')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.institutions.manual-review', compact('institutions'));
    }

    /**
     * List approved institutions
     */
    public function approved()
    {
        $institutions = Institution::with(['province', 'regency', 'user'])
            ->where('ai_validation_status', 'approved')
            ->orderBy('ai_validated_at', 'desc')
            ->paginate(20);

        return view('admin.institutions.approved', compact('institutions'));
    }

    /**
     * List rejected institutions
     */
    public function rejected()
    {
        $institutions = Institution::with(['province', 'regency', 'user'])
            ->where('ai_validation_status', 'rejected')
            ->orderBy('ai_validated_at', 'desc')
            ->paginate(20);

        return view('admin.institutions.rejected', compact('institutions'));
    }

    /**
     * Review institution details
     * Shows all documents, AI validation results, and manual review options
     */
    public function review($id)
    {
        $institution = Institution::with(['province', 'regency', 'user'])
            ->findOrFail($id);

        // Get validation logs
        $validationLogs = AIValidationLog::where('institution_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.institutions.review', compact('institution', 'validationLogs'));
    }

    /**
     * Approve institution manually
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $institution = Institution::findOrFail($id);

        try {
            // Update institution status
            $institution->update([
                'ai_validation_status' => 'approved',
                'is_verified' => true,
                'ai_validation_notes' => $request->admin_notes ?? 'Approved by admin: ' . auth()->user()->name,
                'ai_validated_at' => now(),
            ]);

            // Update user status to active
            $institution->user->update(['is_active' => true]);

            // Send approval email
            Mail::to($institution->email)->send(new InstitutionApproved($institution));

            Log::info('✅ Institution manually approved by admin', [
                'institution_id' => $institution->id,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
            ]);

            return redirect()->back()
                ->with('success', 'Institution berhasil disetujui! Email konfirmasi telah dikirim.');

        } catch (\Exception $e) {
            Log::error('❌ Failed to approve institution', [
                'institution_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menyetujui institution. Error: ' . $e->getMessage());
        }
    }

    /**
     * Reject institution manually
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $institution = Institution::findOrFail($id);

        try {
            // Update institution status
            $institution->update([
                'ai_validation_status' => 'rejected',
                'is_verified' => false,
                'ai_validation_notes' => 'Rejected by admin (' . auth()->user()->name . '): ' . $request->rejection_reason,
                'ai_validated_at' => now(),
            ]);

            // Deactivate user
            $institution->user->update(['is_active' => false]);

            // Send rejection email
            Mail::to($institution->email)->send(new InstitutionRejected($institution));

            Log::info('✅ Institution manually rejected by admin', [
                'institution_id' => $institution->id,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'reason' => $request->rejection_reason,
            ]);

            return redirect()->back()
                ->with('success', 'Institution berhasil ditolak. Email notifikasi telah dikirim.');

        } catch (\Exception $e) {
            Log::error('❌ Failed to reject institution', [
                'institution_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menolak institution. Error: ' . $e->getMessage());
        }
    }

    /**
     * Get validation logs for an institution
     */
    public function validationLogs($id)
    {
        $logs = AIValidationLog::where('institution_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'logs' => $logs,
        ]);
    }
}
