<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * CohereAIService - Semantic Analysis dan Embeddings
 *
 * Service ini menggunakan Cohere untuk:
 * 1. Text embeddings untuk similarity matching
 * 2. Semantic analysis untuk content validation
 * 3. Classification untuk document categorization
 */
class CohereAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.cohere.ai/v1';
    protected $embedModel = 'embed-multilingual-v3.0';
    protected $generateModel = 'command';

    public function __construct()
    {
        $this->apiKey = config('services.cohere.api_key');

        if (empty($this->apiKey)) {
            Log::warning('⚠️ Cohere API key not configured - AI features will be disabled');
            $this->apiKey = null;
        }
    }

    /**
     * Check if Cohere is properly configured
     */
    protected function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Generate embeddings untuk text
     */
    public function generateEmbeddings(string $text, string $inputType = 'search_document'): ?array
    {
        if (!$this->isConfigured()) {
            Log::debug('⚠️ Cohere not configured, skipping embeddings');
            return null;
        }

        try {
            Log::info('🔍 Generating embeddings dengan Cohere', [
                'text_length' => strlen($text),
                'input_type' => $inputType
            ]);

            // SSL Verification: Disabled for local development, enabled for production
            $http = Http::timeout(30);

            if (config('app.env') === 'local') {
                $http = $http->withoutVerifying(); // Only for local Windows SSL issues
            }

            $response = $http->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/embed", [
                    'texts' => [$text],
                    'model' => $this->embedModel,
                    'input_type' => $inputType,
                    'truncate' => 'END'
                ]);

            if (!$response->successful()) {
                Log::error('❌ Cohere embed request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            return $data['embeddings'][0] ?? null;

        } catch (\Exception $e) {
            Log::error('❌ Error generating embeddings', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Calculate semantic similarity antara dua text
     */
    public function calculateSimilarity(string $text1, string $text2): float
    {
        try {
            $embedding1 = $this->generateEmbeddings($text1, 'search_document');
            $embedding2 = $this->generateEmbeddings($text2, 'search_query');

            if (!$embedding1 || !$embedding2) {
                return 0.0;
            }

            // Cosine similarity
            return $this->cosineSimilarity($embedding1, $embedding2);

        } catch (\Exception $e) {
            Log::error('❌ Error calculating similarity', [
                'error' => $e->getMessage()
            ]);
            return 0.0;
        }
    }

    /**
     * Calculate cosine similarity between two vectors
     */
    protected function cosineSimilarity(array $vec1, array $vec2): float
    {
        if (count($vec1) !== count($vec2)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $magnitude1 = 0.0;
        $magnitude2 = 0.0;

        for ($i = 0; $i < count($vec1); $i++) {
            $dotProduct += $vec1[$i] * $vec2[$i];
            $magnitude1 += $vec1[$i] * $vec1[$i];
            $magnitude2 += $vec2[$i] * $vec2[$i];
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0.0;
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * Classify institution type based on description
     */
    public function classifyInstitutionType(string $description): ?string
    {
        if (!$this->isConfigured()) {
            Log::debug('⚠️ Cohere not configured, skipping classification');
            return null;
        }

        try {
            Log::info('🔍 Classifying institution type with Cohere');

            // SSL Verification: Disabled for local development, enabled for production
            $http = Http::timeout(30);

            if (config('app.env') === 'local') {
                $http = $http->withoutVerifying(); // Only for local Windows SSL issues
            }

            $response = $http->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/classify", [
                    'model' => 'embed-multilingual-v3.0',
                    'inputs' => [$description],
                    'examples' => [
                        [
                            'text' => 'Pemerintah desa yang melayani masyarakat desa',
                            'label' => 'pemerintah_desa'
                        ],
                        [
                            'text' => 'Dinas pemerintahan kabupaten/kota',
                            'label' => 'dinas'
                        ],
                        [
                            'text' => 'Organisasi non-pemerintah yang fokus pada pemberdayaan masyarakat',
                            'label' => 'ngo'
                        ],
                        [
                            'text' => 'Pusat kesehatan masyarakat yang memberikan layanan kesehatan',
                            'label' => 'puskesmas'
                        ],
                        [
                            'text' => 'Sekolah pendidikan dasar dan menengah',
                            'label' => 'sekolah'
                        ],
                        [
                            'text' => 'Universitas atau institut pendidikan tinggi',
                            'label' => 'perguruan_tinggi'
                        ]
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('❌ Cohere classify request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            return $data['classifications'][0]['prediction'] ?? null;

        } catch (\Exception $e) {
            Log::error('❌ Error classifying institution type', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Analyze text sentiment and legitimacy
     */
    public function analyzeTextLegitimacy(string $text): array
    {
        if (!$this->isConfigured()) {
            Log::debug('⚠️ Cohere not configured, skipping text legitimacy analysis');
            return ['is_legitimate' => false, 'confidence' => 0, 'error' => 'cohere_not_configured'];
        }

        try {
            Log::info('🔍 Analyzing text legitimacy with Cohere');

            $prompt = <<<PROMPT
Analyze the following Indonesian text and determine if it appears to be legitimate formal/official text or not.

Text:
{$text}

Respond in JSON format:
{
    "is_legitimate": true/false,
    "confidence": 0-100,
    "language_quality": "excellent/good/poor",
    "formality_score": 0-100,
    "issues": ["array of issues if any"]
}
PROMPT;

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/generate", [
                    'model' => $this->generateModel,
                    'prompt' => $prompt,
                    'max_tokens' => 500,
                    'temperature' => 0.3,
                ]);

            if (!$response->successful()) {
                Log::error('❌ Cohere generate request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['is_legitimate' => false, 'confidence' => 0];
            }

            $data = $response->json();
            $generatedText = $data['generations'][0]['text'] ?? '';

            // Extract JSON from response
            if (preg_match('/\{[^}]+\}/', $generatedText, $matches)) {
                $result = json_decode($matches[0], true);
                if ($result) {
                    return $result;
                }
            }

            return ['is_legitimate' => false, 'confidence' => 0];

        } catch (\Exception $e) {
            Log::error('❌ Error analyzing text legitimacy', [
                'error' => $e->getMessage()
            ]);
            return ['is_legitimate' => false, 'confidence' => 0];
        }
    }
}
