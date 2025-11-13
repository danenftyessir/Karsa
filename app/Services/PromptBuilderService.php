<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * PromptBuilderService
 *
 * Service untuk build prompts untuk Claude AI dengan context dari documents
 */
class PromptBuilderService
{
    /**
     * Build system prompt untuk chatbot
     *
     * @return string
     */
    public function buildSystemPrompt(): string
    {
        return <<<PROMPT
Kamu adalah KARA (Karsa Artificial Response Assistant), asisten AI untuk platform Karsa (KKN-GO), sebuah platform yang menghubungkan mahasiswa dengan proyek KKN di berbagai institusi.

PERAN & TANGGUNG JAWAB:
- Membantu mahasiswa mencari informasi tentang proyek KKN, dokumentasi, dan best practices
- Menjawab pertanyaan berdasarkan dokumentasi dan knowledge base yang tersedia
- Memberikan saran dan rekomendasi yang konstruktif dan berbasis data

GUIDELINES PENTING:
1. FOKUS PADA KARSA: Hanya jawab pertanyaan yang berkaitan dengan:
   - Proyek KKN dan dokumentasi di platform Karsa
   - Institusi yang terdaftar di Karsa
   - Best practices KKN
   - Proses aplikasi dan pelaksanaan KKN
   - Informasi teknis terkait proyek

2. BATASAN TOPIK:
   - JANGAN jawab pertanyaan pribadi, SARA, politik, atau topik sensitif
   - JANGAN jawab pertanyaan yang tidak terkait dengan Karsa atau KKN
   - Jika ditanya topik di luar scope, sopan tolak dengan:
     "Maaf, saya tidak dapat menjawab pertanyaan yang tidak berkaitan dengan proyek Karsa atau bersifat pribadi."

3. VARIASI RESPONS:
   - Gunakan variasi kata dalam penolakan (jangan monoton)
   - Contoh variasi:
     * "Mohon maaf, pertanyaan tersebut di luar cakupan bantuan saya untuk platform Karsa."
     * "Saya khusus membantu informasi seputar KKN di platform Karsa. Untuk topik lain, saya tidak dapat membantu."
     * "Maaf, saya fokus pada informasi terkait proyek dan dokumentasi Karsa saja."

4. SUMBER INFORMASI:
   - Berikan jawaban berdasarkan dokumen yang tersedia di knowledge base
   - Jika informasi tidak tersedia, jangan membuat jawaban (no hallucination)
   - Gunakan variasi respons untuk "tidak tahu", contoh:
     * "Maaf, saya tidak menemukan informasi tentang hal itu dalam dokumentasi Karsa."
     * "Informasi tersebut belum tersedia di knowledge base kami saat ini."
     * "Saya tidak memiliki data spesifik mengenai hal tersebut dalam sistem Karsa."
   - SELALU sertakan sumber dokumen jika memungkinkan

5. GAYA KOMUNIKASI:
   - Ramah, profesional, dan membantu
   - Gunakan bahasa Indonesia yang baik dan mudah dipahami
   - Berikan contoh konkret jika memungkinkan
   - Struktur jawaban dengan jelas menggunakan paragraf yang terorganisir
   - JANGAN gunakan format markdown seperti #, *, **, -, atau formatting lainnya
   - Gunakan tanda baca dan paragraf untuk membuat jawaban terstruktur dan mudah dibaca

6. KEAMANAN:
   - JANGAN pernah membagikan informasi pribadi pengguna lain
   - JANGAN memberikan saran yang bisa merugikan atau berbahaya
   - Jika ada permintaan mencurigakan, tolak dengan sopan

REMEMBER: Tujuan utamamu adalah membantu mahasiswa mendapatkan informasi yang akurat dan bermanfaat tentang platform Karsa dan proyek KKN.
PROMPT;
    }

    /**
     * Build user prompt dengan context dari documents
     *
     * @param string $userMessage
     * @param array $documentExcerpts
     * @param array $conversationHistory
     * @return string
     */
    public function buildUserPrompt(
        string $userMessage,
        array $documentExcerpts = [],
        array $conversationHistory = []
    ): string {
        $prompt = "";

        // Add conversation context if exists
        if (!empty($conversationHistory)) {
            $prompt .= "RIWAYAT PERCAKAPAN:\n";
            foreach ($conversationHistory as $message) {
                $role = $message['role'] === 'user' ? 'User' : 'Assistant';
                $prompt .= "{$role}: {$message['content']}\n";
            }
            $prompt .= "\n";
        }

        // Add document context if exists
        if (!empty($documentExcerpts)) {
            $prompt .= "KNOWLEDGE BASE (Dokumen Relevan):\n\n";

            foreach ($documentExcerpts as $index => $excerpt) {
                $docNum = $index + 1;
                $prompt .= "[DOKUMEN {$docNum}]\n";
                $prompt .= "Judul: {$excerpt['title']}\n";

                if (!empty($excerpt['author'])) {
                    $prompt .= "Penulis: {$excerpt['author']}\n";
                }

                if (!empty($excerpt['institution'])) {
                    $prompt .= "Institusi: {$excerpt['institution']}\n";
                }

                if (!empty($excerpt['year'])) {
                    $prompt .= "Tahun: {$excerpt['year']}\n";
                }

                if (!empty($excerpt['categories'])) {
                    $categories = is_array($excerpt['categories'])
                        ? implode(', ', $excerpt['categories'])
                        : $excerpt['categories'];
                    $prompt .= "Kategori: {$categories}\n";
                }

                $prompt .= "\nIsi:\n{$excerpt['excerpt']}\n\n";
                $prompt .= str_repeat('-', 80) . "\n\n";
            }
        }

        // Add current user message
        $prompt .= "PERTANYAAN USER:\n";
        $prompt .= "{$userMessage}\n\n";

        // Add instructions
        $prompt .= "INSTRUKSI:\n";
        if (!empty($documentExcerpts)) {
            $prompt .= "- Gunakan informasi dari dokumen di atas untuk menjawab pertanyaan\n";
            $prompt .= "- Sebutkan sumber dokumen yang digunakan (nomor dokumen)\n";
        } else {
            $prompt .= "- Tidak ada dokumen relevan ditemukan di knowledge base\n";
            $prompt .= "- Jika kamu tidak memiliki informasi, jawab dengan jujur bahwa informasi tidak tersedia\n";
        }
        $prompt .= "- Berikan jawaban yang spesifik, informatif, dan mudah dipahami\n";
        $prompt .= "- Variasikan gaya bahasa (jangan monoton)\n";

        return $prompt;
    }

    /**
     * Build prompt untuk content filtering (SARA detection)
     *
     * @param string $userMessage
     * @return string
     */
    public function buildContentFilterPrompt(string $userMessage): string
    {
        return <<<PROMPT
Analisis pesan berikut dan tentukan apakah pesan tersebut:
1. Berisi konten SARA (Suku, Agama, Ras, Antar-golongan)
2. Berisi konten yang tidak pantas atau ofensif
3. Berisi pertanyaan pribadi yang tidak relevan dengan platform Karsa/KKN
4. Topik di luar scope platform Karsa (politik, ekonomi makro, dll)

PESAN USER:
"{$userMessage}"

Jawab HANYA dengan format JSON berikut (tanpa markdown):
{
    "is_appropriate": true/false,
    "reason": "alasan singkat jika tidak appropriate",
    "category": "appropriate/sara/offensive/personal/off_topic"
}
PROMPT;
    }

    /**
     * Build prompt untuk extract keywords dari user message
     *
     * @param string $userMessage
     * @return string
     */
    public function buildKeywordExtractionPrompt(string $userMessage): string
    {
        return <<<PROMPT
Extract kata kunci utama dari pertanyaan berikut untuk pencarian dokumen:

PERTANYAAN: "{$userMessage}"

Jawab dengan format JSON (tanpa markdown):
{
    "keywords": ["keyword1", "keyword2", "keyword3"],
    "categories": ["kategori1", "kategori2"],
    "intent": "informasi/saran/tutorial/lainnya"
}
PROMPT;
    }

    /**
     * Format document excerpts untuk display
     *
     * @param array $excerpts
     * @return string
     */
    public function formatDocumentSources(array $excerpts): string
    {
        if (empty($excerpts)) {
            return '';
        }

        $formatted = "\n\n📚 **Sumber Informasi:**\n";

        foreach ($excerpts as $index => $excerpt) {
            $docNum = $index + 1;
            $formatted .= "\n{$docNum}. **{$excerpt['title']}**";

            if (!empty($excerpt['author'])) {
                $formatted .= " - {$excerpt['author']}";
            }

            if (!empty($excerpt['year'])) {
                $formatted .= " ({$excerpt['year']})";
            }
        }

        return $formatted;
    }

    /**
     * Build conversation context dari message history
     *
     * @param Collection $messages
     * @param int $limit
     * @return array
     */
    public function buildConversationContext(Collection $messages, int $limit = 5): array
    {
        $context = [];

        // Get last N messages for context
        $recentMessages = $messages->sortByDesc('created_at')->take($limit)->reverse();

        foreach ($recentMessages as $message) {
            $context[] = [
                'role' => $message->role,
                'content' => $message->content,
            ];
        }

        return $context;
    }
}
