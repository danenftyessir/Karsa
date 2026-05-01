<?php

namespace App\Jobs;

use App\Models\Institution;
use App\Models\AIValidationLog;
use App\Services\ClaudeAIService;
use App\Services\CohereAIService;
use App\Mail\InstitutionApproved;
use App\Mail\InstitutionRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

/**
 * ValidateInstitutionDocumentsJob
 *
 * Background job untuk validate KTP, NPWP, dan Verification Document
 * menggunakan Claude AI dengan STRICT scoring system
 *
 * SCORING THRESHOLDS (VERY STRICT):
 * - KTP: minimum 85/100
 * - NPWP: minimum 85/100
 * - Verification Document: minimum 90/100
 * - Overall: ALL must pass untuk auto-approve
 */
class ValidateInstitutionDocumentsJob implements ShouldQueue
{
    use Queueable;

    protected $institution;
    protected $claudeService;
    protected $cohereService;

    public $timeout = 600; // 10 minutes timeout
    public $tries = 2; // Retry up to 2 times if failed

    /**
     * Create a new job instance.
     */
    public function __construct(Institution $institution)
    {
        $this->institution = $institution;
    }

    /**
     * Execute the job - STRICT document validation
     */
    public function handle(ClaudeAIService $claudeService, CohereAIService $cohereService): void
    {
        $this->claudeService = $claudeService;
        $this->cohereService = $cohereService;

        Log::info('🚀 Starting STRICT AI validation for institution', [
            'institution_id' => $this->institution->id,
            'institution_name' => $this->institution->name
        ]);

        try {
            DB::beginTransaction();

            // Update status to validating
            $this->institution->update([
                'ai_validation_status' => 'validating'
            ]);

            $institutionData = [
                'institution_name' => $this->institution->name,
                'institution_type' => $this->institution->type,
                'pic_name' => $this->institution->pic_name,
                'pic_position' => $this->institution->pic_position,
                'address' => $this->institution->address,
            ];

            // Step 1: Validate KTP dengan STRICT criteria
            $ktpResult = $this->validateKTP($institutionData);

            // Step 2: Validate NPWP dengan STRICT criteria
            $npwpResult = $this->validateNPWP($institutionData);

            // Step 3: Validate Verification Document dengan EXTREMELY STRICT criteria
            $docResult = $this->validateVerificationDocument($institutionData);

            // Calculate aggregate score
            $aggregateScore = $this->calculateAggregateScore(
                $ktpResult,
                $npwpResult,
                $docResult
            );

            // Determine final validation status
            $finalStatus = $this->determineFinalStatus(
                $ktpResult,
                $npwpResult,
                $docResult,
                $aggregateScore
            );

            // Update institution dengan hasil validasi
            $this->institution->update([
                'ai_validation_status' => $finalStatus['status'],
                'ai_validation_score' => $aggregateScore,
                'ai_validation_notes' => $finalStatus['notes'],
                'ai_validated_at' => now(),
                'is_verified' => $finalStatus['is_verified']
            ]);

            // Log detailed AI validation
            $this->logValidation($ktpResult, $npwpResult, $docResult, $finalStatus);

            DB::commit();

            // Send email notification
            $this->sendEmailNotification($finalStatus['status']);

            Log::info('✅ AI validation completed', [
                'institution_id' => $this->institution->id,
                'status' => $finalStatus['status'],
                'score' => $aggregateScore
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ AI validation job failed', [
                'institution_id' => $this->institution->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Mark as failed for manual review
            $this->institution->update([
                'ai_validation_status' => 'failed',
                'ai_validation_notes' => 'AI validation error: ' . $e->getMessage()
            ]);

            // Log failure
            AIValidationLog::create([
                'institution_id' => $this->institution->id,
                'validation_type' => 'error',
                'is_passed' => false,
                'score' => 0,
                'confidence' => 0,
                'details' => ['error' => $e->getMessage()],
                'issues' => ['AI validation system error'],
                'recommendation' => 'manual_review'
            ]);

            throw $e;
        }
    }

    /**
     * Validate KTP dengan STRICT criteria
     */
    protected function validateKTP(array $institutionData): array
    {
        if (empty($this->institution->ktp_path)) {
            Log::warning('⚠️ KTP path is empty', [
                'institution_id' => $this->institution->id
            ]);
            return $this->createFailResult('KTP file not found');
        }

        Log::info('🔍 Validating KTP', [
            'path' => $this->institution->ktp_path
        ]);

        $result = $this->claudeService->validateKTP(
            $this->institution->ktp_path,
            $institutionData
        );

        // Log KTP validation
        AIValidationLog::create([
            'institution_id' => $this->institution->id,
            'validation_type' => 'ktp',
            'is_passed' => $result['is_valid'] ?? false,
            'score' => $result['score'] ?? 0,
            'confidence' => $result['confidence'] ?? 0,
            'details' => $result['details'] ?? [],
            'issues' => $result['issues'] ?? [],
            'recommendation' => $result['recommendation'] ?? 'reject'
        ]);

        return $result;
    }

    /**
     * Validate NPWP dengan STRICT criteria
     */
    protected function validateNPWP(array $institutionData): array
    {
        if (empty($this->institution->npwp_path)) {
            Log::warning('⚠️ NPWP path is empty', [
                'institution_id' => $this->institution->id
            ]);
            return $this->createFailResult('NPWP file not found');
        }

        Log::info('🔍 Validating NPWP', [
            'path' => $this->institution->npwp_path
        ]);

        $result = $this->claudeService->validateNPWP(
            $this->institution->npwp_path,
            $institutionData
        );

        // Log NPWP validation
        AIValidationLog::create([
            'institution_id' => $this->institution->id,
            'validation_type' => 'npwp',
            'is_passed' => $result['is_valid'] ?? false,
            'score' => $result['score'] ?? 0,
            'confidence' => $result['confidence'] ?? 0,
            'details' => $result['details'] ?? [],
            'issues' => $result['issues'] ?? [],
            'recommendation' => $result['recommendation'] ?? 'reject'
        ]);

        return $result;
    }

    /**
     * Validate Verification Document dengan EXTREMELY STRICT criteria
     */
    protected function validateVerificationDocument(array $institutionData): array
    {
        if (empty($this->institution->verification_document_path)) {
            Log::warning('⚠️ Verification document path is empty', [
                'institution_id' => $this->institution->id
            ]);
            return $this->createFailResult('Verification document not found');
        }

        Log::info('🔍 Validating Verification Document', [
            'path' => $this->institution->verification_document_path
        ]);

        $result = $this->claudeService->validateVerificationDocument(
            $this->institution->verification_document_path,
            $institutionData
        );

        // Log verification document validation
        AIValidationLog::create([
            'institution_id' => $this->institution->id,
            'validation_type' => 'verification_document',
            'is_passed' => $result['is_valid'] ?? false,
            'score' => $result['score'] ?? 0,
            'confidence' => $result['confidence'] ?? 0,
            'details' => $result['details'] ?? [],
            'issues' => $result['issues'] ?? [],
            'recommendation' => $result['recommendation'] ?? 'reject'
        ]);

        return $result;
    }

    /**
     * Calculate aggregate score from all documents
     */
    protected function calculateAggregateScore(array $ktpResult, array $npwpResult, array $docResult): float
    {
        $ktpScore = $ktpResult['score'] ?? 0;
        $npwpScore = $npwpResult['score'] ?? 0;
        $docScore = $docResult['score'] ?? 0;

        // Weighted average (verification document has higher weight)
        // KTP: 30%, NPWP: 30%, Doc: 40%
        $aggregateScore = ($ktpScore * 0.30) + ($npwpScore * 0.30) + ($docScore * 0.40);

        return round($aggregateScore, 2);
    }

    /**
     * Determine final validation status dengan STRICT rules
     */
    protected function determineFinalStatus(
        array $ktpResult,
        array $npwpResult,
        array $docResult,
        float $aggregateScore
    ): array {
        $ktpValid = $ktpResult['is_valid'] ?? false;
        $npwpValid = $npwpResult['is_valid'] ?? false;
        $docValid = $docResult['is_valid'] ?? false;

        $ktpScore = $ktpResult['score'] ?? 0;
        $npwpScore = $npwpResult['score'] ?? 0;
        $docScore = $docResult['score'] ?? 0;

        // Collect all issues
        $allIssues = array_merge(
            $ktpResult['issues'] ?? [],
            $npwpResult['issues'] ?? [],
            $docResult['issues'] ?? []
        );

        // STRICT RULES:
        // 1. ALL documents must pass minimum threshold
        // 2. Verification document MUST pass (non-negotiable)
        // 3. Aggregate score must be >= 85

        if ($ktpValid && $npwpValid && $docValid && $aggregateScore >= 85) {
            // AUTO APPROVE - All criteria met
            return [
                'status' => 'approved',
                'is_verified' => true,
                'notes' => sprintf(
                    'Auto-approved by AI. Scores - KTP: %d, NPWP: %d, Doc: %d, Aggregate: %.2f',
                    $ktpScore,
                    $npwpScore,
                    $docScore,
                    $aggregateScore
                )
            ];
        }

        if (!$docValid || $docScore < 90) {
            // REJECT - Verification document failed (critical)
            return [
                'status' => 'rejected',
                'is_verified' => false,
                'notes' => sprintf(
                    'Rejected: Verification document failed (score: %d/100, required: 90). Issues: %s',
                    $docScore,
                    implode('; ', $docResult['issues'] ?? ['Unknown'])
                )
            ];
        }

        if (!$ktpValid || $ktpScore < 85) {
            // REJECT - KTP failed
            return [
                'status' => 'rejected',
                'is_verified' => false,
                'notes' => sprintf(
                    'Rejected: KTP validation failed (score: %d/100, required: 85). Issues: %s',
                    $ktpScore,
                    implode('; ', $ktpResult['issues'] ?? ['Unknown'])
                )
            ];
        }

        if (!$npwpValid || $npwpScore < 85) {
            // REJECT - NPWP failed
            return [
                'status' => 'rejected',
                'is_verified' => false,
                'notes' => sprintf(
                    'Rejected: NPWP validation failed (score: %d/100, required: 85). Issues: %s',
                    $npwpScore,
                    implode('; ', $npwpResult['issues'] ?? ['Unknown'])
                )
            ];
        }

        // PENDING MANUAL REVIEW - Scores too close to threshold
        if ($aggregateScore >= 80 && $aggregateScore < 85) {
            return [
                'status' => 'pending_manual_review',
                'is_verified' => false,
                'notes' => sprintf(
                    'Pending manual review: Aggregate score %.2f is close to threshold. All issues: %s',
                    $aggregateScore,
                    implode('; ', array_unique($allIssues))
                )
            ];
        }

        // REJECT - Below minimum standards
        return [
            'status' => 'rejected',
            'is_verified' => false,
            'notes' => sprintf(
                'Rejected: Aggregate score %.2f below minimum (required: 85). Issues: %s',
                $aggregateScore,
                implode('; ', array_unique($allIssues))
            )
        ];
    }

    /**
     * Log detailed validation results
     */
    protected function logValidation(
        array $ktpResult,
        array $npwpResult,
        array $docResult,
        array $finalStatus
    ): void {
        AIValidationLog::create([
            'institution_id' => $this->institution->id,
            'validation_type' => 'aggregate',
            'is_passed' => $finalStatus['is_verified'],
            'score' => $this->institution->ai_validation_score,
            'confidence' => min([
                $ktpResult['confidence'] ?? 0,
                $npwpResult['confidence'] ?? 0,
                $docResult['confidence'] ?? 0
            ]),
            'details' => [
                'ktp' => $ktpResult,
                'npwp' => $npwpResult,
                'verification_document' => $docResult,
                'final_status' => $finalStatus
            ],
            'issues' => [],
            'recommendation' => $finalStatus['status']
        ]);
    }

    /**
     * Send email notification based on validation status
     */
    protected function sendEmailNotification(string $status): void
    {
        try {
            if ($status === 'approved') {
                Mail::to($this->institution->email)->send(
                    new InstitutionApproved($this->institution)
                );
                Log::info('✅ Approval email sent', [
                    'institution_id' => $this->institution->id
                ]);
            } elseif ($status === 'rejected') {
                Mail::to($this->institution->email)->send(
                    new InstitutionRejected($this->institution)
                );
                Log::info('📧 Rejection email sent', [
                    'institution_id' => $this->institution->id
                ]);
            }
            // For pending_manual_review, admin will send email manually
        } catch (\Exception $e) {
            Log::error('❌ Failed to send email notification', [
                'institution_id' => $this->institution->id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create fail result for missing documents
     */
    protected function createFailResult(string $reason): array
    {
        return [
            'success' => false,
            'is_valid' => false,
            'score' => 0,
            'confidence' => 0,
            'issues' => [$reason],
            'recommendation' => 'reject'
        ];
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ ValidateInstitutionDocumentsJob permanently failed', [
            'institution_id' => $this->institution->id,
            'error' => $exception->getMessage()
        ]);

        // Update institution status to failed
        $this->institution->update([
            'ai_validation_status' => 'failed',
            'ai_validation_notes' => 'Job failed after retries: ' . $exception->getMessage()
        ]);
    }
}
