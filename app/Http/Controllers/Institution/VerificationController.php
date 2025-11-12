<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\VerificationDocument;
use App\Services\AIDocumentVerificationService;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    protected AIDocumentVerificationService $verificationService;
    protected SupabaseStorageService $storageService;

    public function __construct(
        AIDocumentVerificationService $verificationService,
        SupabaseStorageService $storageService
    ) {
        $this->verificationService = $verificationService;
        $this->storageService = $storageService;
    }

    /**
     * Show upload documents page
     */
    public function showUploadPage()
    {
        $institution = auth()->user()->institution;

        // Check if already has documents
        $existingDocuments = VerificationDocument::where('institution_id', $institution->id)->get();

        return view('institution.verification.upload', compact('institution', 'existingDocuments'));
    }

    /**
     * Handle document upload
     */
    public function uploadDocuments(Request $request)
    {
        $request->validate([
            'official_letter' => 'nullable|file|mimes:pdf|max:5120',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'pic_identity' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'npwp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $institution = auth()->user()->institution;

        try {
            DB::beginTransaction();

            $uploadedDocuments = [];
            $documentTypes = ['official_letter', 'logo', 'pic_identity', 'npwp'];

            foreach ($documentTypes as $type) {
                if ($request->hasFile($type)) {
                    $file = $request->file($type);

                    // Check if document already exists
                    $existingDoc = VerificationDocument::where('institution_id', $institution->id)
                        ->where('document_type', $type)
                        ->first();

                    // Store file in Supabase
                    $path = "verification_documents/{$institution->id}/" . $type . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $uploadedPath = $this->storageService->uploadFile($file, $path);

                    if (!$uploadedPath) {
                        continue;
                    }

                    $fileUrl = $this->storageService->getPublicUrl($uploadedPath);

                    if ($existingDoc) {
                        // Update existing document
                        $existingDoc->update([
                            'file_url' => $fileUrl,
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                            // Reset AI verification status
                            'ai_verification_id' => null,
                            'ai_status' => null,
                            'ai_score' => null,
                            'ai_confidence' => null,
                            'ai_processed_at' => null,
                        ]);
                        $uploadedDocuments[] = $type;
                    } else {
                        // Create new document
                        VerificationDocument::create([
                            'institution_id' => $institution->id,
                            'document_type' => $type,
                            'file_url' => $fileUrl,
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                        ]);
                        $uploadedDocuments[] = $type;
                    }
                }
            }

            DB::commit();

            if (empty($uploadedDocuments)) {
                return redirect()->back()->with('error', 'Tidak ada dokumen yang diupload.');
            }

            return redirect()->route('institution.verification.upload')
                ->with('success', 'Dokumen berhasil diupload! Klik tombol "Mulai Verifikasi AI" untuk memulai verifikasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Document upload error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal mengupload dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Trigger AI verification
     */
    public function triggerVerification()
    {
        $institution = auth()->user()->institution;

        // Check if has documents
        $documents = VerificationDocument::where('institution_id', $institution->id)->get();

        if ($documents->isEmpty()) {
            return redirect()->route('institution.verification.upload')
                ->with('error', 'Harap upload dokumen terlebih dahulu.');
        }

        try {
            // Call AI verification service
            $result = $this->verificationService->verifyInstitutionDocuments($institution);

            return redirect()->route('institution.verification.status')
                ->with('success', 'Verifikasi AI selesai! Lihat hasil verifikasi di bawah.');

        } catch (\Exception $e) {
            Log::error('Verification trigger error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memverifikasi dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Show verification status page
     */
    public function showStatus()
    {
        $institution = auth()->user()->institution;

        $documents = VerificationDocument::where('institution_id', $institution->id)
            ->orderBy('document_type')
            ->get();

        return view('institution.verification.status', compact('institution', 'documents'));
    }
}
