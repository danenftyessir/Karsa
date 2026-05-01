<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AIDocumentVerificationService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.anthropic.com/v1/messages';
    protected string $model = 'claude-3-haiku-20240307'; // Using Haiku for cost optimization

    public function __construct()
    {
        $this->apiKey = config('services.claude.api_key', env('CLAUDE_API_KEY'));
    }

    /**
     * Verify institution documents using Claude AI
     *
     * @param array $documents Array of document info: ['type' => 'official_letter', 'path' => '...']
     * @param array $institutionData Institution information for cross-validation
     * @return array Verification result
     */
    public function verifyDocuments(array $documents, array $institutionData): array
    {
        try {
            $analysisResults = [];

            // Analyze each document
            foreach ($documents as $document) {
                $result = $this->analyzeDocument($document, $institutionData);
                $analysisResults[$document['type']] = $result;
            }

            // Calculate overall verification score
            $overallResult = $this->calculateOverallScore($analysisResults, $institutionData);

            return $overallResult;

        } catch (\Exception $e) {
            Log::error('AI Document Verification Error: ' . $e->getMessage());

            return [
                'status' => 'error',
                'score' => 0,
                'confidence' => 0,
                'error' => $e->getMessage(),
                'flags' => [
                    [
                        'type' => 'error',
                        'field' => 'system',
                        'message' => 'AI verification system error',
                        'severity' => 10
                    ]
                ]
            ];
        }
    }

    /**
     * Analyze a single document
     */
    protected function analyzeDocument(array $document, array $institutionData): array
    {
        $documentType = $document['type'];
        $filePath = $document['path'];

        // Load document content
        $content = $this->loadDocumentContent($filePath);

        if (!$content) {
            return [
                'status' => 'error',
                'score' => 0,
                'message' => 'Failed to load document'
            ];
        }

        // Create prompt based on document type
        $prompt = $this->createVerificationPrompt($documentType, $institutionData);

        // Call Claude API
        $response = $this->callClaudeAPI($prompt, $content);

        return $response;
    }

    /**
     * Load document content (convert to base64 for images, extract text for PDFs)
     */
    protected function loadDocumentContent(string $filePath): ?array
    {
        try {
            // Check if file exists in storage
            if (!Storage::disk('supabase')->exists($filePath)) {
                return null;
            }

            $fileContent = Storage::disk('supabase')->get($filePath);
            $mimeType = Storage::disk('supabase')->mimeType($filePath);

            // Handle images (KTP, Logo)
            if (str_starts_with($mimeType, 'image/')) {
                return [
                    'type' => 'image',
                    'media_type' => $mimeType,
                    'data' => base64_encode($fileContent)
                ];
            }

            // Handle PDFs (Official Letter)
            if ($mimeType === 'application/pdf') {
                // For MVP, we'll send PDF as base64 and let Claude handle it
                // In production, you might want to extract text first
                return [
                    'type' => 'document',
                    'media_type' => $mimeType,
                    'data' => base64_encode($fileContent)
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Failed to load document: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create verification prompt based on document type
     */
    protected function createVerificationPrompt(string $documentType, array $institutionData): string
    {
        $institutionName = $institutionData['name'] ?? '';
        $picName = $institutionData['pic_name'] ?? '';
        $institutionType = $institutionData['type'] ?? '';

        $basePrompt = "You are an expert document verification specialist. Analyze this document and provide a detailed verification assessment.\n\n";
        $basePrompt .= "Institution Information:\n";
        $basePrompt .= "- Name: {$institutionName}\n";
        $basePrompt .= "- Type: {$institutionType}\n";
        $basePrompt .= "- PIC Name: {$picName}\n\n";

        switch ($documentType) {
            case 'official_letter':
                $prompt = $basePrompt . "This is an official letter (Surat Pengantar/SK) from the institution.\n\n";
                $prompt .= "Verify the following:\n";
                $prompt .= "1. Document authenticity (official letterhead, signature, stamp)\n";
                $prompt .= "2. Institution name consistency\n";
                $prompt .= "3. Document format (proper official letter structure)\n";
                $prompt .= "4. Date validity (not too old or future dated)\n";
                $prompt .= "5. Any signs of tampering or manipulation\n\n";
                break;

            case 'pic_identity':
                $prompt = $basePrompt . "This is an identity card (KTP) of the Person in Charge.\n\n";
                $prompt .= "Verify the following:\n";
                $prompt .= "1. ID card authenticity (format, hologram patterns if visible)\n";
                $prompt .= "2. Name on ID matches PIC name: {$picName}\n";
                $prompt .= "3. Photo quality and consistency\n";
                $prompt .= "4. NIK (ID number) format validity\n";
                $prompt .= "5. Any signs of photo manipulation or fake ID\n\n";
                break;

            case 'logo':
                $prompt = $basePrompt . "This is the institution's official logo.\n\n";
                $prompt .= "Verify the following:\n";
                $prompt .= "1. Logo quality (professional, not pixelated)\n";
                $prompt .= "2. Consistency with institution type\n";
                $prompt .= "3. No inappropriate content\n";
                $prompt .= "4. Appears to be official/authentic\n\n";
                break;

            case 'npwp':
                $prompt = $basePrompt . "This is a tax ID document (NPWP).\n\n";
                $prompt .= "Verify the following:\n";
                $prompt .= "1. NPWP format authenticity\n";
                $prompt .= "2. Institution name consistency\n";
                $prompt .= "3. Valid NPWP number format\n";
                $prompt .= "4. No signs of tampering\n\n";
                break;

            default:
                $prompt = $basePrompt . "Verify the authenticity and validity of this document.\n\n";
        }

        $prompt .= "Respond in JSON format with the following structure:\n";
        $prompt .= "{\n";
        $prompt .= "  \"score\": <0-100>,\n";
        $prompt .= "  \"confidence\": <0.0-1.0>,\n";
        $prompt .= "  \"status\": \"approved|needs_review|rejected\",\n";
        $prompt .= "  \"reasoning\": \"Detailed explanation of your assessment\",\n";
        $prompt .= "  \"flags\": [\n";
        $prompt .= "    {\"type\": \"warning|error|info\", \"field\": \"field_name\", \"message\": \"description\", \"severity\": <1-10>}\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"extracted_data\": {\n";
        $prompt .= "    \"institution_name\": \"extracted name if found\",\n";
        $prompt .= "    \"pic_name\": \"extracted name if found\",\n";
        $prompt .= "    \"identity_number\": \"extracted NIK/NPWP if found\",\n";
        $prompt .= "    \"issue_date\": \"extracted date if found\"\n";
        $prompt .= "  }\n";
        $prompt .= "}\n\n";
        $prompt .= "Be thorough but fair in your assessment. Flag suspicious elements but don't be overly strict on minor issues.";

        return $prompt;
    }

    /**
     * Call Claude API with document content
     */
    protected function callClaudeAPI(string $prompt, array $content): array
    {
        try {
            $messages = [];

            // Prepare content based on type
            if ($content['type'] === 'image') {
                $messages[] = [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => $content['media_type'],
                                'data' => $content['data']
                            ]
                        ],
                        [
                            'type' => 'text',
                            'text' => $prompt
                        ]
                    ]
                ];
            } else {
                // For PDFs or text documents
                $messages[] = [
                    'role' => 'user',
                    'content' => $prompt . "\n\n[Document attached as base64]"
                ];
            }

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(60)->post($this->apiUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'messages' => $messages
            ]);

            if (!$response->successful()) {
                Log::error('Claude API Error: ' . $response->body());
                throw new \Exception('Claude API request failed: ' . $response->status());
            }

            $result = $response->json();

            // Extract the text content from Claude's response
            $textContent = $result['content'][0]['text'] ?? '';

            // Parse JSON from the response
            $parsedResult = $this->parseClaudeResponse($textContent);

            return $parsedResult;

        } catch (\Exception $e) {
            Log::error('Claude API call failed: ' . $e->getMessage());

            return [
                'score' => 50,
                'confidence' => 0.3,
                'status' => 'needs_review',
                'reasoning' => 'AI analysis failed. Manual review required.',
                'flags' => [
                    [
                        'type' => 'error',
                        'field' => 'system',
                        'message' => 'AI processing error: ' . $e->getMessage(),
                        'severity' => 8
                    ]
                ],
                'extracted_data' => []
            ];
        }
    }

    /**
     * Parse Claude's response and extract JSON
     */
    protected function parseClaudeResponse(string $response): array
    {
        // Try to extract JSON from response
        // Claude might wrap JSON in markdown code blocks
        $response = trim($response);

        // Remove markdown code blocks if present
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*$/', '', $response);
        $response = trim($response);

        try {
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from Claude');
            }

            // Ensure all required fields exist
            return [
                'score' => $data['score'] ?? 50,
                'confidence' => $data['confidence'] ?? 0.5,
                'status' => $data['status'] ?? 'needs_review',
                'reasoning' => $data['reasoning'] ?? 'No reasoning provided',
                'flags' => $data['flags'] ?? [],
                'extracted_data' => $data['extracted_data'] ?? []
            ];

        } catch (\Exception $e) {
            Log::error('Failed to parse Claude response: ' . $e->getMessage());

            // Return default needs_review result
            return [
                'score' => 50,
                'confidence' => 0.3,
                'status' => 'needs_review',
                'reasoning' => 'Failed to parse AI response. Manual review required.',
                'flags' => [
                    [
                        'type' => 'warning',
                        'field' => 'parsing',
                        'message' => 'AI response parsing failed',
                        'severity' => 6
                    ]
                ],
                'extracted_data' => []
            ];
        }
    }

    /**
     * Calculate overall verification score from all document analyses
     */
    protected function calculateOverallScore(array $analysisResults, array $institutionData): array
    {
        if (empty($analysisResults)) {
            return [
                'status' => 'rejected',
                'score' => 0,
                'confidence' => 0,
                'decision' => 'rejected',
                'reasoning' => 'No documents provided for verification',
                'flags' => [],
                'documents' => []
            ];
        }

        $totalScore = 0;
        $totalConfidence = 0;
        $documentCount = 0;
        $allFlags = [];
        $criticalFlags = 0;
        $documentResults = [];

        foreach ($analysisResults as $docType => $result) {
            if (isset($result['score'])) {
                $totalScore += $result['score'];
                $totalConfidence += $result['confidence'] ?? 0.5;
                $documentCount++;

                $documentResults[$docType] = $result;

                // Count critical flags
                foreach ($result['flags'] ?? [] as $flag) {
                    if ($flag['type'] === 'error' || $flag['severity'] >= 8) {
                        $criticalFlags++;
                    }
                    $allFlags[] = array_merge($flag, ['document' => $docType]);
                }
            }
        }

        if ($documentCount === 0) {
            $avgScore = 0;
            $avgConfidence = 0;
        } else {
            $avgScore = $totalScore / $documentCount;
            $avgConfidence = $totalConfidence / $documentCount;
        }

        // Decision logic based on specification
        $decision = $this->makeVerificationDecision($avgScore, $avgConfidence, $criticalFlags);

        return [
            'status' => $decision,
            'score' => round($avgScore, 2),
            'confidence' => round($avgConfidence, 2),
            'decision' => $decision,
            'reasoning' => $this->generateOverallReasoning($decision, $avgScore, $criticalFlags, $documentResults),
            'flags' => $allFlags,
            'documents' => $documentResults,
            'verification_id' => 'ver_' . uniqid(),
            'processed_at' => now()->toIso8601String()
        ];
    }

    /**
     * Make verification decision based on score and confidence
     */
    protected function makeVerificationDecision(float $score, float $confidence, int $criticalFlags): string
    {
        // Auto-reject if critical flags exist
        if ($criticalFlags > 0) {
            return 'rejected';
        }

        // Decision matrix from specification
        if ($score >= 85 && $confidence >= 0.8) {
            return 'approved';
        } elseif ($score >= 60) {
            return 'needs_review';
        } else {
            return 'rejected';
        }
    }

    /**
     * Generate overall reasoning text
     */
    protected function generateOverallReasoning(string $decision, float $score, int $criticalFlags, array $documentResults): string
    {
        $reasoning = "Overall verification score: " . round($score, 2) . "/100. ";

        switch ($decision) {
            case 'approved':
                $reasoning .= "All documents appear authentic and meet verification standards. ";
                $reasoning .= "The institution's documents have been automatically approved. ";
                break;

            case 'needs_review':
                $reasoning .= "Documents show some inconsistencies or quality issues that require manual review. ";
                if ($criticalFlags > 0) {
                    $reasoning .= "Critical issues detected: {$criticalFlags}. ";
                }
                $reasoning .= "A human reviewer should verify the documents before final approval. ";
                break;

            case 'rejected':
                $reasoning .= "Documents failed verification checks. ";
                if ($criticalFlags > 0) {
                    $reasoning .= "Critical issues detected: {$criticalFlags}. ";
                }
                $reasoning .= "The documents do not meet minimum authentication standards. ";
                break;
        }

        // Add summary of document statuses
        $docSummary = [];
        foreach ($documentResults as $type => $result) {
            $docSummary[] = "{$type}: {$result['status']}";
        }
        $reasoning .= "Document status: " . implode(', ', $docSummary) . ".";

        return $reasoning;
    }
}
