<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\VerificationDocument;
use App\Services\AIDocumentVerificationService;
use App\Services\SupabaseStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentVerificationController extends Controller
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
     * Upload verification documents for an institution
     *
     * POST /api/institutions/{id}/documents
     */
    public function uploadDocuments(Request $request, int $institutionId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'official_letter' => 'nullable|file|mimes:pdf|max:5120',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'pic_identity' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'npwp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $institution = Institution::findOrFail($institutionId);

        // Check authorization (only institution owner can upload)
        if (auth()->id() !== $institution->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $uploadedDocuments = [];

            // Process each document type
            $documentTypes = ['official_letter', 'logo', 'pic_identity', 'npwp'];

            foreach ($documentTypes as $type) {
                if ($request->hasFile($type)) {
                    $file = $request->file($type);

                    // Store file in Supabase
                    $path = "verification_documents/{$institutionId}/" . $type . '_' . time() . '.' . $file->getClientOriginalExtension();

                    // Upload to Supabase storage
                    $uploadedPath = $this->storageService->uploadFile($file, $path);

                    if (!$uploadedPath) {
                        continue; // Skip if upload failed
                    }

                    // Get public URL
                    $fileUrl = $this->storageService->getPublicUrl($uploadedPath);

                    // Create verification document record
                    $verificationDoc = VerificationDocument::create([
                        'institution_id' => $institutionId,
                        'document_type' => $type,
                        'file_url' => $fileUrl,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);

                    $uploadedDocuments[] = [
                        'id' => $verificationDoc->id,
                        'type' => $type,
                        'file_name' => $verificationDoc->file_name,
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Documents uploaded successfully',
                'data' => [
                    'documents' => $uploadedDocuments,
                    'institution_id' => $institutionId
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Document upload error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload documents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify institution documents using AI
     *
     * POST /api/ai/verify-documents
     */
    public function verifyDocuments(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|exists:institutions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $institutionId = $request->institution_id;
        $institution = Institution::with('verificationDocuments')->findOrFail($institutionId);

        // Get all verification documents
        $documents = $institution->verificationDocuments;

        if ($documents->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No documents found for verification'
            ], 400);
        }

        try {
            // Call AI verification service
            // The service will handle all document analysis and institution status update
            $verificationResult = $this->verificationService->verifyInstitutionDocuments($institution);

            return response()->json([
                'success' => true,
                'message' => 'Documents verified successfully',
                'data' => $verificationResult
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Document verification error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify documents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get verification status for an institution
     *
     * GET /api/institutions/{id}/verification-status
     */
    public function getVerificationStatus(int $institutionId): JsonResponse
    {
        $institution = Institution::findOrFail($institutionId);

        // Check authorization
        if (auth()->id() !== $institution->user_id && !auth()->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $documents = VerificationDocument::where('institution_id', $institutionId)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'institution_id' => $institutionId,
                'verification_status' => $institution->verification_status,
                'verification_score' => $institution->verification_score,
                'verification_confidence' => $institution->verification_confidence,
                'verified_at' => $institution->verified_at,
                'is_verified' => $institution->is_verified,
                'documents' => $documents->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'type' => $doc->document_type,
                        'file_name' => $doc->file_name,
                        'ai_status' => $doc->ai_status,
                        'ai_score' => $doc->ai_score,
                        'ai_processed_at' => $doc->ai_processed_at,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Get detailed verification result
     *
     * GET /api/ai/verification/{verificationId}
     */
    public function getVerificationResult(string $verificationId): JsonResponse
    {
        // Find documents with this verification ID
        $documents = VerificationDocument::where('ai_verification_id', $verificationId)
            ->with('institution')
            ->get();

        if ($documents->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Verification not found'
            ], 404);
        }

        $institution = $documents->first()->institution;

        // Check authorization
        if (auth()->id() !== $institution->user_id && !auth()->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $documentsData = $documents->map(function ($doc) {
            return [
                'id' => $doc->id,
                'type' => $doc->document_type,
                'file_name' => $doc->file_name,
                'ai_status' => $doc->ai_status,
                'ai_score' => $doc->ai_score,
                'ai_confidence' => $doc->ai_confidence,
                'ai_flags' => $doc->ai_flags,
                'ai_extracted_data' => $doc->ai_extracted_data,
                'ai_reasoning' => $doc->ai_reasoning,
                'ai_processed_at' => $doc->ai_processed_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'verification_id' => $verificationId,
                'institution' => [
                    'id' => $institution->id,
                    'name' => $institution->name,
                    'type' => $institution->type,
                    'verification_status' => $institution->verification_status,
                    'verification_score' => $institution->verification_score,
                    'verification_confidence' => $institution->verification_confidence,
                ],
                'documents' => $documentsData,
            ]
        ]);
    }
}
