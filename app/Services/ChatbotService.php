<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatDocumentReference;
use App\Models\ChatContext;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * ChatbotService - Main Orchestrator
 *
 * Service utama untuk handle chat dengan AI
 * Includes: RAG, Content Filtering (SARA), Response Generation
 */
class ChatbotService
{
    protected $documentRetriever;
    protected $promptBuilder;
    protected $claudeApiKey;
    protected $claudeModel = 'claude-sonnet-4-20250514';
    protected $maxTokens = 4096;

    public function __construct(
        DocumentRetrieverService $documentRetriever,
        PromptBuilderService $promptBuilder
    ) {
        $this->documentRetriever = $documentRetriever;
        $this->promptBuilder = $promptBuilder;
        $this->claudeApiKey = config('services.claude.api_key');

        if (empty($this->claudeApiKey)) {
            Log::error('❌ Claude API key not configured for chatbot!');
            throw new \Exception('Claude API key is not configured');
        }
    }

    /**
     * Create a new conversation
     *
     * @param User $user
     * @param string|null $title
     * @return ChatConversation
     */
    public function createConversation(User $user, ?string $title = null): ChatConversation
    {
        $conversation = ChatConversation::create([
            'user_id' => $user->id,
            'student_id' => $user->student?->id,
            'title' => $title ?? 'Percakapan Baru',
            'system_prompt' => $this->promptBuilder->buildSystemPrompt(),
            'status' => 'active',
            'message_count' => 0,
        ]);

        // Create context for conversation
        ChatContext::create([
            'conversation_id' => $conversation->id,
            'indexed_documents' => [],
            'filters_applied' => [],
            'indexed_doc_count' => 0,
        ]);

        Log::info('✅ Created new conversation', [
            'conversation_id' => $conversation->id,
            'user_id' => $user->id
        ]);

        return $conversation;
    }

    /**
     * Send message and get AI response
     *
     * @param ChatConversation $conversation
     * @param string $userMessage
     * @param array $filters
     * @return array
     */
    public function sendMessage(
        ChatConversation $conversation,
        string $userMessage,
        array $filters = []
    ): array {
        try {
            DB::beginTransaction();

            Log::info('💬 Processing user message', [
                'conversation_id' => $conversation->id,
                'message_length' => strlen($userMessage),
                'filters' => $filters
            ]);

            // Step 1: Content filtering (SARA, inappropriate content)
            $contentCheck = $this->checkContentAppropriateness($userMessage);

            if (!$contentCheck['is_appropriate']) {
                $response = $this->handleInappropriateContent($contentCheck);

                // Save flagged user message
                $userMsg = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'user',
                    'content' => $userMessage,
                    'flagged' => true,
                    'flag_reason' => $contentCheck['reason'],
                    'metadata' => $contentCheck,
                ]);

                // Save bot response
                $botMsg = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $response['message'],
                    'metadata' => ['filtered' => true, 'reason' => $contentCheck['category']],
                ]);

                $conversation->incrementMessageCount();
                $conversation->incrementMessageCount();

                DB::commit();

                return [
                    'success' => true,
                    'message' => $response['message'],
                    'filtered' => true,
                    'message_id' => $botMsg->id,
                    'sources' => [],
                ];
            }

            // Step 2: Save user message
            $userMsg = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'content' => $userMessage,
                'flagged' => false,
            ]);

            $conversation->incrementMessageCount();

            // Step 3: Retrieve relevant documents (RAG)
            $documents = $this->documentRetriever->searchDocuments(
                $userMessage,
                $filters,
                $limit = 10
            );

            $documentExcerpts = $this->documentRetriever->getDocumentExcerpts(
                $documents,
                $userMessage,
                $excerptLength = 500
            );

            Log::info('📚 Retrieved documents', [
                'count' => count($documentExcerpts)
            ]);

            // Step 4: Build conversation history
            $conversationHistory = $this->promptBuilder->buildConversationContext(
                $conversation->messages()->orderBy('created_at', 'desc')->limit(10)->get(),
                $limit = 5
            );

            // Step 5: Build prompt and call Claude API
            $userPrompt = $this->promptBuilder->buildUserPrompt(
                $userMessage,
                $documentExcerpts,
                $conversationHistory
            );

            $aiResponse = $this->callClaudeAPI($userPrompt);

            // Step 6: Save AI response
            $botMsg = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $aiResponse['content'],
                'context' => [
                    'document_count' => count($documentExcerpts),
                    'filters' => $filters,
                ],
                'metadata' => [
                    'model' => $this->claudeModel,
                    'usage' => $aiResponse['usage'] ?? null,
                ],
                'token_usage' => $aiResponse['usage']['output_tokens'] ?? null,
            ]);

            $conversation->incrementMessageCount();

            // Step 7: Save document references (only if AI actually used them)
            $usedSources = $this->shouldShowSources($aiResponse['content'], $documentExcerpts);

            if ($usedSources) {
                foreach ($documentExcerpts as $excerpt) {
                    ChatDocumentReference::create([
                        'message_id' => $botMsg->id,
                        'document_id' => $excerpt['document_id'],
                        'relevance_score' => $excerpt['relevance_score'],
                        'excerpt' => $excerpt['excerpt'],
                        'metadata' => [
                            'title' => $excerpt['title'],
                            'author' => $excerpt['author'],
                            'year' => $excerpt['year'],
                        ],
                    ]);
                }
            }

            // Step 8: Update conversation title if first message
            if ($conversation->message_count <= 2 && $conversation->title === 'Percakapan Baru') {
                $title = $this->generateConversationTitle($userMessage);
                $conversation->update(['title' => $title]);
            }

            DB::commit();

            Log::info('✅ Message processed successfully', [
                'conversation_id' => $conversation->id,
                'message_id' => $botMsg->id,
                'document_count' => count($documentExcerpts)
            ]);

            return [
                'success' => true,
                'message' => $aiResponse['content'],
                'message_id' => $botMsg->id,
                'sources' => $usedSources ? $this->formatSources($documentExcerpts) : [],
                'token_usage' => $aiResponse['usage'] ?? null,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ Failed to process message', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan saat memproses pesan Anda. Silakan coba lagi.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if content is appropriate (SARA filtering)
     *
     * @param string $message
     * @return array
     */
    protected function checkContentAppropriateness(string $message): array
    {
        try {
            $prompt = $this->promptBuilder->buildContentFilterPrompt($message);

            // Use Claude API to check content
            $response = $this->callClaudeAPI($prompt, $maxTokens = 500);

            // Parse response
            $content = $response['content'];

            // Extract JSON from response
            if (preg_match('/\{[^}]+\}/', $content, $matches)) {
                $result = json_decode($matches[0], true);

                if ($result && isset($result['is_appropriate'])) {
                    return [
                        'is_appropriate' => $result['is_appropriate'],
                        'reason' => $result['reason'] ?? '',
                        'category' => $result['category'] ?? 'unknown',
                    ];
                }
            }

            // Default to appropriate if parsing fails
            Log::warning('⚠️ Failed to parse content filter response, defaulting to appropriate');
            return [
                'is_appropriate' => true,
                'reason' => '',
                'category' => 'appropriate',
            ];

        } catch (\Exception $e) {
            Log::error('❌ Content filtering failed', [
                'error' => $e->getMessage()
            ]);

            // Default to appropriate on error (fail open)
            return [
                'is_appropriate' => true,
                'reason' => '',
                'category' => 'error',
            ];
        }
    }

    /**
     * Handle inappropriate content
     *
     * @param array $contentCheck
     * @return array
     */
    protected function handleInappropriateContent(array $contentCheck): array
    {
        $category = $contentCheck['category'] ?? 'inappropriate';

        // Variasi respons untuk berbagai kategori
        $responses = [
            'sara' => [
                'Maaf, saya tidak dapat menjawab pertanyaan yang mengandung unsur SARA.',
                'Mohon maaf, topik yang Anda tanyakan mengandung konten sensitif yang tidak dapat saya bahas.',
                'Saya tidak dapat membantu dengan pertanyaan yang bersifat SARA. Silakan tanyakan hal lain terkait platform Karsa.',
            ],
            'offensive' => [
                'Maaf, saya tidak dapat merespons konten yang tidak pantas.',
                'Mohon maaf, pertanyaan Anda mengandung konten yang tidak sesuai. Mari kita fokus pada topik Karsa.',
            ],
            'personal' => [
                'Maaf, saya tidak dapat menjawab pertanyaan yang bersifat pribadi. Saya fokus membantu informasi seputar platform Karsa.',
                'Mohon maaf, pertanyaan pribadi di luar cakupan bantuan saya. Ada yang bisa saya bantu tentang Karsa?',
            ],
            'off_topic' => [
                'Maaf, pertanyaan tersebut di luar cakupan platform Karsa. Saya khusus membantu informasi seputar proyek KKN.',
                'Saya fokus pada informasi terkait proyek dan dokumentasi Karsa. Untuk topik lain, saya belum bisa membantu.',
                'Mohon maaf, saya hanya dapat membantu dengan pertanyaan seputar platform Karsa dan KKN.',
            ],
        ];

        $categoryResponses = $responses[$category] ?? $responses['off_topic'];
        $message = $categoryResponses[array_rand($categoryResponses)];

        return [
            'message' => $message,
            'category' => $category,
        ];
    }

    /**
     * Call Claude API
     *
     * @param string $prompt
     * @param int $maxTokens
     * @return array
     */
    protected function callClaudeAPI(string $prompt, int $maxTokens = null): array
    {
        $messages = [
            [
                'role' => 'user',
                'content' => $prompt,
            ]
        ];

        Log::info('📤 Sending request to Claude API for chatbot');

        // SSL Verification: Disabled for local development
        $http = Http::timeout(120);

        if (config('app.env') === 'local') {
            $http = $http->withoutVerifying();
        }

        $response = $http->withHeaders([
                'x-api-key' => $this->claudeApiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->claudeModel,
                'max_tokens' => $maxTokens ?? $this->maxTokens,
                'messages' => $messages,
                'system' => $this->promptBuilder->buildSystemPrompt(),
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

        return [
            'content' => $data['content'][0]['text'] ?? '',
            'usage' => $data['usage'] ?? null,
        ];
    }

    /**
     * Format sources untuk response
     *
     * @param array $excerpts
     * @return array
     */
    protected function formatSources(array $excerpts): array
    {
        return array_map(function ($excerpt) {
            return [
                'id' => $excerpt['document_id'],
                'title' => $excerpt['title'],
                'author' => $excerpt['author'],
                'year' => $excerpt['year'],
                'relevance' => $excerpt['relevance_score'],
            ];
        }, array_slice($excerpts, 0, 5)); // Return top 5 sources
    }

    /**
     * Get conversations for user
     *
     * @param User $user
     * @param int $limit
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserConversations(User $user, int $limit = 20)
    {
        return ChatConversation::where('user_id', $user->id)
            ->active()
            ->orderBy('last_message_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Get conversation with messages
     *
     * @param int $conversationId
     * @param User $user
     * @return ChatConversation|null
     */
    public function getConversation(int $conversationId, User $user): ?ChatConversation
    {
        return ChatConversation::with(['messages.documentReferences.document'])
            ->where('id', $conversationId)
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Archive conversation
     *
     * @param ChatConversation $conversation
     * @return bool
     */
    public function archiveConversation(ChatConversation $conversation): bool
    {
        return $conversation->update(['status' => 'archived']);
    }

    /**
     * Delete conversation
     *
     * @param ChatConversation $conversation
     * @return bool
     */
    public function deleteConversation(ChatConversation $conversation): bool
    {
        return $conversation->delete();
    }

    /**
     * Determine if sources should be shown based on AI response
     *
     * @param string $aiResponse
     * @param array $documentExcerpts
     * @return bool
     */
    protected function shouldShowSources(string $aiResponse, array $documentExcerpts): bool
    {
        // If no documents were retrieved, don't show sources
        if (empty($documentExcerpts)) {
            return false;
        }

        // Keywords that indicate AI is rejecting/not using documents
        $rejectKeywords = [
            'tidak menemukan',
            'tidak dapat menjawab',
            'tidak dapat membantu',
            'di luar cakupan',
            'di luar scope',
            'tidak tersedia',
            'belum tersedia',
            'tidak dapat menjawab pertanyaan yang',
            'tidak dapat merespons',
            'maaf, pertanyaan',
            'mohon maaf, pertanyaan',
            'saya khusus membantu',
            'saya fokus pada',
            'apakah ada hal lain',
            'ada yang bisa saya bantu',
            'tidak berkaitan dengan',
            'tidak terkait dengan',
        ];

        // Check if response contains rejection keywords
        $lowerResponse = mb_strtolower($aiResponse);

        // Get first 200 characters to check for early rejection signals
        $responseStart = mb_substr($lowerResponse, 0, 200);

        foreach ($rejectKeywords as $keyword) {
            if (mb_strpos($responseStart, $keyword) !== false) {
                Log::info('🚫 Sources hidden - AI response indicates rejection or no information found', [
                    'keyword_matched' => $keyword
                ]);
                return false;
            }
        }

        // Check if AI is asking for confirmation/clarification without providing info
        $clarificationPatterns = [
            'apakah anda',
            'apakah kamu',
            'bisa anda jelaskan',
            'bisa kamu jelaskan',
            'maksud anda',
            'maksudnya',
        ];

        foreach ($clarificationPatterns as $pattern) {
            if (mb_strpos($lowerResponse, $pattern) !== false &&
                mb_strlen($aiResponse) < 300) {
                Log::info('🚫 Sources hidden - AI asking for clarification', [
                    'pattern_matched' => $pattern
                ]);
                return false;
            }
        }

        Log::info('✅ Sources will be shown - AI provided substantive answer');
        return true;
    }

    /**
     * Generate a meaningful conversation title from the first user message
     *
     * @param string $userMessage
     * @return string
     */
    protected function generateConversationTitle(string $userMessage): string
    {
        try {
            $prompt = "Buatlah judul percakapan yang singkat dan informatif (maksimal 50 karakter) berdasarkan pertanyaan berikut. Judul harus menangkap inti/topik utama dari pertanyaan, bukan hanya memotong awalnya.\n\nPertanyaan: \"$userMessage\"\n\nJudul harus:\n- Fokus pada topik utama/kata kunci penting\n- Maksimal 50 karakter\n- Tanpa tanda petik\n- Bahasa Indonesia yang natural\n\nContoh:\nPertanyaan: \"saya lagi ga pengen dokumen tapi saya ingin tutorial, saya pas kkn disuruh bersiin kamar mandi gimana ya caranya\"\nJudul: \"Tutorial Membersihkan Kamar Mandi saat KKN\"\n\nJawab hanya dengan judulnya saja, tanpa penjelasan tambahan.";

            $response = $this->callClaudeAPI($prompt, $maxTokens = 100);
            $title = trim($response['content']);

            // Pastikan title tidak terlalu panjang
            if (strlen($title) > 60) {
                $title = mb_substr($title, 0, 57) . '...';
            }

            // Fallback jika title kosong
            if (empty($title)) {
                return mb_substr($userMessage, 0, 50) . (strlen($userMessage) > 50 ? '...' : '');
            }

            Log::info('✅ Generated conversation title', [
                'original_length' => strlen($userMessage),
                'title' => $title
            ]);

            return $title;

        } catch (\Exception $e) {
            Log::error('❌ Failed to generate conversation title', [
                'error' => $e->getMessage()
            ]);

            // Fallback to original method
            return mb_substr($userMessage, 0, 50) . (strlen($userMessage) > 50 ? '...' : '');
        }
    }
}
