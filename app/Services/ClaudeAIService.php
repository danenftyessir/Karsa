<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * ClaudeAIService - Strict Document Validation dengan Claude AI
 *
 * Service ini menggunakan Claude Sonnet 4 untuk:
 * 1. Analyze KTP images dengan validasi ketat
 * 2. Analyze NPWP images dengan validasi ketat
 * 3. Analyze PDF verification documents dengan validasi content
 *
 * SCORING SYSTEM (STRICT):
 * - KTP: minimum 85/100
 * - NPWP: minimum 85/100
 * - Verification Document: minimum 90/100
 */
class ClaudeAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.anthropic.com/v1/messages';
    protected $model = 'claude-sonnet-4-20250514';
    protected $maxTokens = 4096;

    public function __construct()
    {
        $this->apiKey = config('services.claude.api_key');

        if (empty($this->apiKey)) {
            Log::error('❌ Claude API key not configured!');
            throw new \Exception('Claude API key is not configured in services.php');
        }
    }

    /**
     * Validate KTP image dengan STRICT validation
     *
     * Scoring Criteria (Total: 100):
     * - NIK valid format (20 points)
     * - Foto KTP jelas & tidak blur (20 points)
     * - Data personal lengkap (20 points)
     * - Tidak ada tanda manipulasi/fake (20 points)
     * - Format KTP resmi Indonesia (20 points)
     *
     * Minimum passing score: 85/100
     */
    public function validateKTP(string $imagePath, array $institutionData): array
    {
        try {
            Log::info('🔍 Starting STRICT KTP validation', [
                'image_path' => $imagePath,
                'institution' => $institutionData['institution_name'] ?? 'Unknown'
            ]);

            // Convert image to base64
            $imageBase64 = $this->convertImageToBase64($imagePath);
            if (!$imageBase64) {
                return $this->createFailureResponse('Failed to read KTP image');
            }

            $prompt = $this->buildKTPValidationPrompt($institutionData);
            $mimeType = $this->getMimeType($imagePath);

            $response = $this->sendClaudeRequest($prompt, $imageBase64, $mimeType);

            return $this->parseValidationResponse($response, 'KTP', 85);

        } catch (\Exception $e) {
            Log::error('❌ KTP validation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->createFailureResponse($e->getMessage());
        }
    }

    /**
     * Validate NPWP image dengan STRICT validation
     *
     * Scoring Criteria (Total: 100):
     * - Format NPWP valid (25 points)
     * - Nomor NPWP jelas terbaca (25 points)
     * - Nama match dengan data institusi (25 points)
     * - Tidak ada tanda manipulasi/fake (25 points)
     *
     * Minimum passing score: 85/100
     */
    public function validateNPWP(string $imagePath, array $institutionData): array
    {
        try {
            Log::info('🔍 Starting STRICT NPWP validation', [
                'image_path' => $imagePath,
                'institution' => $institutionData['institution_name'] ?? 'Unknown'
            ]);

            $imageBase64 = $this->convertImageToBase64($imagePath);
            if (!$imageBase64) {
                return $this->createFailureResponse('Failed to read NPWP image');
            }

            $prompt = $this->buildNPWPValidationPrompt($institutionData);
            $mimeType = $this->getMimeType($imagePath);

            $response = $this->sendClaudeRequest($prompt, $imageBase64, $mimeType);

            return $this->parseValidationResponse($response, 'NPWP', 85);

        } catch (\Exception $e) {
            Log::error('❌ NPWP validation failed', [
                'error' => $e->getMessage()
            ]);

            return $this->createFailureResponse($e->getMessage());
        }
    }

    /**
     * Validate Verification Document (PDF) dengan EXTREMELY STRICT validation
     *
     * Scoring Criteria (Total: 100):
     * - Header resmi instansi/pemerintah (20 points)
     * - Content legitimasi & purpose jelas (20 points)
     * - Tanda tangan pejabat berwenang (20 points)
     * - Cap/stempel resmi (20 points)
     * - Format dokumen resmi Indonesia (20 points)
     *
     * Minimum passing score: 90/100 (VERY STRICT!)
     */
    public function validateVerificationDocument(string $pdfPath, array $institutionData): array
    {
        try {
            Log::info('🔍 Starting EXTREMELY STRICT PDF validation', [
                'pdf_path' => $pdfPath,
                'institution' => $institutionData['institution_name'] ?? 'Unknown'
            ]);

            // Convert PDF to base64
            $pdfBase64 = $this->convertPDFToBase64($pdfPath);
            if (!$pdfBase64) {
                return $this->createFailureResponse('Failed to read PDF document');
            }

            $prompt = $this->buildPDFValidationPrompt($institutionData);

            $response = $this->sendClaudeRequest($prompt, $pdfBase64, 'application/pdf');

            return $this->parseValidationResponse($response, 'Verification Document', 90);

        } catch (\Exception $e) {
            Log::error('❌ PDF validation failed', [
                'error' => $e->getMessage()
            ]);

            return $this->createFailureResponse($e->getMessage());
        }
    }

    /**
     * Build STRICT KTP validation prompt
     */
    protected function buildKTPValidationPrompt(array $data): string
    {
        $institutionName = $data['institution_name'] ?? 'Unknown';
        $picName = $data['pic_name'] ?? 'Unknown';

        return <<<PROMPT
You are an expert document validator for Indonesian government documents. Analyze this KTP (Indonesian ID card) image with EXTREMELY STRICT validation.

INSTITUTION DATA:
- Institution Name: {$institutionName}
- PIC Name: {$picName}

VALIDATION CRITERIA (Score out of 100):
1. NIK Format Valid (20 points): Check if 16-digit NIK is clearly visible and follows Indonesian NIK format
2. Photo Quality (20 points): Photo must be clear, not blurred, properly lit, authentic KTP format
3. Complete Data (20 points): All required fields visible (Name, NIK, Address, DOB, etc.)
4. No Manipulation (20 points): No signs of photoshop, fake, or manipulated image
5. Official Format (20 points): Matches official Indonesian KTP format (current e-KTP design)

ADDITIONAL STRICT CHECKS:
- Verify PIC name appears on the KTP
- Check for hologram/security features visibility
- Validate that this is a real photo of KTP, not screenshot or printout
- Ensure KTP is not expired or damaged

RESPOND IN EXACT JSON FORMAT:
{
    "is_valid": true/false,
    "score": 0-100,
    "confidence": 0-100,
    "details": {
        "nik": "extracted NIK or null",
        "name": "extracted name or null",
        "name_matches_pic": true/false,
        "photo_quality": "excellent/good/poor",
        "completeness": "complete/incomplete",
        "authenticity": "genuine/suspicious/fake"
    },
    "validation_checks": {
        "nik_format_valid": true/false,
        "photo_clear": true/false,
        "data_complete": true/false,
        "no_manipulation": true/false,
        "official_format": true/false
    },
    "issues": ["array of specific issues found"],
    "recommendation": "approve/reject with reason"
}

BE EXTREMELY STRICT. Minimum passing score is 85/100. Any suspicious elements should result in rejection.
PROMPT;
    }

    /**
     * Build STRICT NPWP validation prompt
     */
    protected function buildNPWPValidationPrompt(array $data): string
    {
        $institutionName = $data['institution_name'] ?? 'Unknown';

        return <<<PROMPT
You are an expert document validator for Indonesian tax documents. Analyze this NPWP (Indonesian Tax ID) image with EXTREMELY STRICT validation.

INSTITUTION DATA:
- Institution Name: {$institutionName}

VALIDATION CRITERIA (Score out of 100):
1. NPWP Format Valid (25 points): 15-digit format XX.XXX.XXX.X-XXX.XXX clearly visible
2. Number Legibility (25 points): All digits must be clearly readable, no blur
3. Name Match (25 points): Name on NPWP should relate to institution name
4. No Manipulation (25 points): No signs of fake, photoshop, or tampered document

ADDITIONAL STRICT CHECKS:
- Verify this is official NPWP format from Direktorat Jenderal Pajak
- Check for official letterhead/logo
- Validate document is not a photocopy of photocopy (poor quality)
- Ensure NPWP belongs to institution/PIC, not random person

RESPOND IN EXACT JSON FORMAT:
{
    "is_valid": true/false,
    "score": 0-100,
    "confidence": 0-100,
    "details": {
        "npwp_number": "extracted NPWP or null",
        "name": "extracted name or null",
        "name_similarity_to_institution": 0-100,
        "document_quality": "excellent/good/poor",
        "authenticity": "genuine/suspicious/fake"
    },
    "validation_checks": {
        "npwp_format_valid": true/false,
        "number_clear": true/false,
        "name_relevant": true/false,
        "no_manipulation": true/false
    },
    "issues": ["array of specific issues found"],
    "recommendation": "approve/reject with reason"
}

BE EXTREMELY STRICT. Minimum passing score is 85/100. Reject if any doubts about authenticity.
PROMPT;
    }

    /**
     * Build EXTREMELY STRICT PDF validation prompt
     */
    protected function buildPDFValidationPrompt(array $data): string
    {
        $institutionName = $data['institution_name'] ?? 'Unknown';
        $institutionType = $data['institution_type'] ?? 'Unknown';

        return <<<PROMPT
You are an expert Indonesian government document validator. Analyze this PDF verification document with EXTREMELY STRICT validation.

INSTITUTION DATA:
- Institution Name: {$institutionName}
- Institution Type: {$institutionType}

REQUIRED DOCUMENT TYPES (one of):
- Surat Tugas/Surat Penugasan (for government institutions)
- SK (Surat Keputusan) Pengangkatan
- Akta Pendirian (for NGO/Organization)
- Surat Pengesahan dari Dinas terkait
- Surat Keterangan Resmi dari Kepala Desa/Lurah

VALIDATION CRITERIA (Score out of 100):
1. Official Header (20 points): Must have official letterhead with institution logo/emblem
2. Content Legitimacy (20 points): Clear purpose, proper legal language, relevant content
3. Authorized Signature (20 points): Signature of authorized official (Kepala Desa, Direktur, etc.)
4. Official Stamp/Seal (20 points): Must have wet stamp or official seal clearly visible
5. Proper Format (20 points): Follows Indonesian official document format standards

ADDITIONAL EXTREMELY STRICT CHECKS:
- Document must be recent (check date if visible)
- Must explicitly mention the institution name
- Language must be formal Indonesian (Bahasa Indonesia formal)
- No signs of template documents or generic letters
- Must have nomor surat (document reference number)
- Signed by someone with proper authority

RED FLAGS (automatic rejection):
- Generic template letter without specifics
- No signature or stamp
- Document appears to be self-made/not official
- Content doesn't relate to institution registration/authorization
- Poor quality scan (suggests photocopy of photocopy)
- Document appears fake or manipulated

RESPOND IN EXACT JSON FORMAT:
{
    "is_valid": true/false,
    "score": 0-100,
    "confidence": 0-100,
    "details": {
        "document_type": "SK/Surat Tugas/Akta/Other",
        "has_official_header": true/false,
        "has_signature": true/false,
        "has_stamp": true/false,
        "institution_name_mentioned": true/false,
        "document_date": "extracted date or null",
        "signatory_name": "extracted name or null",
        "signatory_title": "extracted title or null",
        "document_number": "nomor surat or null"
    },
    "validation_checks": {
        "official_header": true/false,
        "content_legitimate": true/false,
        "authorized_signature": true/false,
        "official_stamp": true/false,
        "proper_format": true/false
    },
    "issues": ["array of ALL issues found - be very detailed"],
    "red_flags": ["array of serious concerns"],
    "recommendation": "approve/reject with detailed reason"
}

BE EXTREMELY STRICT. Minimum passing score is 90/100.
This document proves institution legitimacy - reject anything suspicious or generic.
When in doubt, REJECT. False positive is acceptable, false negative is NOT.
PROMPT;
    }

    /**
     * Send request to Claude API
     */
    protected function sendClaudeRequest(string $prompt, string $base64Content, string $mimeType): array
    {
        // Determine content type based on media type
        // Claude API uses 'image' for image files and 'document' for PDFs
        $contentType = ($mimeType === 'application/pdf') ? 'document' : 'image';

        $messages = [
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => $contentType,
                        'source' => [
                            'type' => 'base64',
                            'media_type' => $mimeType,
                            'data' => $base64Content
                        ]
                    ],
                    [
                        'type' => 'text',
                        'text' => $prompt
                    ]
                ]
            ]
        ];

        Log::info('📤 Sending request to Claude API', [
            'model' => $this->model,
            'content_type' => $mimeType
        ]);

        // SSL Verification: Disabled for local development, enabled for production
        $http = Http::timeout(120);

        if (config('app.env') === 'local') {
            $http = $http->withoutVerifying(); // Only for local Windows SSL issues
            Log::debug('⚠️ SSL verification disabled (local environment)');
        }

        $response = $http->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => $this->maxTokens,
                'messages' => $messages
            ]);

        if (!$response->successful()) {
            Log::error('❌ Claude API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('Claude API request failed: ' . $response->body());
        }

        $data = $response->json();

        Log::info('✅ Claude API response received', [
            'usage' => $data['usage'] ?? null
        ]);

        return $data;
    }

    /**
     * Parse Claude validation response
     */
    protected function parseValidationResponse(array $response, string $documentType, int $minScore): array
    {
        try {
            $content = $response['content'][0]['text'] ?? '';

            // Extract JSON from markdown code blocks if present
            if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
                $content = $matches[1];
            } elseif (preg_match('/```\s*(.*?)\s*```/s', $content, $matches)) {
                $content = $matches[1];
            }

            $result = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('❌ Failed to parse Claude response JSON', [
                    'error' => json_last_error_msg(),
                    'content' => $content
                ]);
                return $this->createFailureResponse('Invalid JSON response from AI');
            }

            $score = $result['score'] ?? 0;
            $isValid = $result['is_valid'] ?? false;

            // Apply STRICT threshold
            if ($score < $minScore) {
                $isValid = false;
                $result['is_valid'] = false;
            }

            Log::info("✅ {$documentType} validation completed", [
                'score' => $score,
                'min_required' => $minScore,
                'passed' => $isValid,
                'recommendation' => $result['recommendation'] ?? 'unknown'
            ]);

            return [
                'success' => true,
                'is_valid' => $isValid,
                'score' => $score,
                'min_required_score' => $minScore,
                'confidence' => $result['confidence'] ?? 0,
                'details' => $result['details'] ?? [],
                'validation_checks' => $result['validation_checks'] ?? [],
                'issues' => $result['issues'] ?? [],
                'red_flags' => $result['red_flags'] ?? [],
                'recommendation' => $result['recommendation'] ?? 'unknown',
                'raw_response' => $result
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error parsing validation response', [
                'error' => $e->getMessage()
            ]);
            return $this->createFailureResponse('Failed to parse AI response');
        }
    }

    /**
     * Detect mime type from file path
     */
    protected function getMimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            default => 'image/jpeg', // fallback
        };
    }

    /**
     * Convert image to base64
     */
    protected function convertImageToBase64(string $path): ?string
    {
        try {
            // First, check if file exists in local storage
            // Files can be stored as either 'public/institutions/...' or 'institutions/...'
            $localPath = str_starts_with($path, 'public/') ? str_replace('public/', '', $path) : $path;

            if (Storage::disk('public')->exists($localPath)) {
                Log::info('🖼️ Reading image from local storage', ['path' => $localPath]);
                $imageContent = Storage::disk('public')->get($localPath);
                return base64_encode($imageContent);
            }

            // If not in local storage, try to fetch from Supabase
            $supabaseService = app(SupabaseStorageService::class);
            $url = $supabaseService->getPublicUrl($path);

            Log::info('📥 Downloading image from Supabase', ['url' => $url]);

            $imageContent = file_get_contents($url);
            if ($imageContent === false) {
                Log::error('❌ Failed to download from Supabase', ['url' => $url]);
                return null;
            }

            return base64_encode($imageContent);

        } catch (\Exception $e) {
            Log::error('❌ Error converting image to base64', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Convert PDF to base64
     */
    protected function convertPDFToBase64(string $path): ?string
    {
        try {
            // First, check if file exists in local storage
            // Files can be stored as either 'public/institutions/...' or 'institutions/...'
            $localPath = str_starts_with($path, 'public/') ? str_replace('public/', '', $path) : $path;

            if (Storage::disk('public')->exists($localPath)) {
                Log::info('📄 Reading PDF from local storage', ['path' => $localPath]);
                $pdfContent = Storage::disk('public')->get($localPath);
                return base64_encode($pdfContent);
            }

            // If not in local storage, try to fetch from Supabase
            $supabaseService = app(SupabaseStorageService::class);
            $url = $supabaseService->getPublicUrl($path);

            Log::info('📥 Downloading PDF from Supabase', ['url' => $url]);

            $pdfContent = file_get_contents($url);
            if ($pdfContent === false) {
                Log::error('❌ Failed to download PDF from Supabase', ['url' => $url]);
                return null;
            }

            return base64_encode($pdfContent);

        } catch (\Exception $e) {
            Log::error('❌ Error converting PDF to base64', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create failure response
     */
    protected function createFailureResponse(string $reason): array
    {
        return [
            'success' => false,
            'is_valid' => false,
            'score' => 0,
            'confidence' => 0,
            'error' => $reason,
            'recommendation' => 'reject',
            'issues' => [$reason]
        ];
    }
}
