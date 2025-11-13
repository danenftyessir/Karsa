<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * DocumentRetrieverService - RAG (Retrieval-Augmented Generation)
 *
 * Service untuk retrieve dokumen relevan dari knowledge repository
 * Digunakan untuk memberikan context ke AI chatbot
 */
class DocumentRetrieverService
{
    /**
     * Search documents berdasarkan query dan filters
     *
     * @param string $query User query for semantic search
     * @param array $filters Optional filters (categories, year, province, etc.)
     * @param int $limit Maximum number of documents to return
     * @return Collection<Document>
     */
    public function searchDocuments(string $query, array $filters = [], int $limit = 10): Collection
    {
        try {
            Log::info('🔍 Searching documents for chatbot', [
                'query' => $query,
                'filters' => $filters,
                'limit' => $limit
            ]);

            // Start with approved and public documents
            $documentsQuery = Document::where('status', 'approved')
                ->where('is_public', true);

            // Apply category filters
            if (!empty($filters['categories'])) {
                $documentsQuery->where(function ($q) use ($filters) {
                    foreach ($filters['categories'] as $category) {
                        $q->orWhereJsonContains('categories', $category);
                    }
                });
            }

            // Apply year filter
            if (!empty($filters['year'])) {
                $documentsQuery->where('year', $filters['year']);
            }

            // Apply province filter
            if (!empty($filters['province_id'])) {
                $documentsQuery->where('province_id', $filters['province_id']);
            }

            // Apply regency filter
            if (!empty($filters['regency_id'])) {
                $documentsQuery->where('regency_id', $filters['regency_id']);
            }

            // Keyword search in title and description
            if (!empty($query)) {
                $keywords = $this->extractKeywords($query);
                $documentsQuery->where(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->orWhere('title', 'ILIKE', "%{$keyword}%")
                          ->orWhere('description', 'ILIKE', "%{$keyword}%")
                          ->orWhereJsonContains('tags', $keyword);
                    }
                });
            }

            // Order by relevance (view count, download count, featured)
            $documents = $documentsQuery
                ->orderBy('is_featured', 'desc')
                ->orderByRaw('(view_count + download_count + citation_count) DESC')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            Log::info('✅ Found documents', [
                'count' => $documents->count()
            ]);

            return $documents;

        } catch (\Exception $e) {
            Log::error('❌ Document search failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return collect();
        }
    }

    /**
     * Get document excerpts untuk context
     *
     * @param Collection $documents
     * @param string $query
     * @param int $excerptLength
     * @return array
     */
    public function getDocumentExcerpts(Collection $documents, string $query, int $excerptLength = 500): array
    {
        $excerpts = [];

        foreach ($documents as $document) {
            $excerpt = $this->extractRelevantExcerpt($document, $query, $excerptLength);

            $excerpts[] = [
                'document_id' => $document->id,
                'title' => $document->title,
                'author' => $document->author_name,
                'institution' => $document->institution_name,
                'year' => $document->year,
                'categories' => $document->categories,
                'excerpt' => $excerpt,
                'relevance_score' => $this->calculateRelevanceScore($document, $query),
            ];
        }

        // Sort by relevance score
        usort($excerpts, function ($a, $b) {
            return $b['relevance_score'] <=> $a['relevance_score'];
        });

        return $excerpts;
    }

    /**
     * Extract relevant excerpt from document
     *
     * @param Document $document
     * @param string $query
     * @param int $length
     * @return string
     */
    protected function extractRelevantExcerpt(Document $document, string $query, int $length): string
    {
        $description = $document->description ?? '';

        if (empty($description)) {
            return $document->title;
        }

        // If description is short, return it all
        if (strlen($description) <= $length) {
            return $description;
        }

        // Try to find the most relevant part containing query keywords
        $keywords = $this->extractKeywords($query);
        $bestPosition = 0;
        $bestMatchCount = 0;

        foreach ($keywords as $keyword) {
            $position = stripos($description, $keyword);
            if ($position !== false) {
                $matchCount = substr_count(strtolower($description), strtolower($keyword));
                if ($matchCount > $bestMatchCount) {
                    $bestMatchCount = $matchCount;
                    $bestPosition = max(0, $position - ($length / 2));
                }
            }
        }

        // Extract excerpt around the best position
        $excerpt = substr($description, $bestPosition, $length);

        // Clean up excerpt
        if ($bestPosition > 0) {
            $excerpt = '...' . $excerpt;
        }
        if (strlen($description) > $bestPosition + $length) {
            $excerpt .= '...';
        }

        return $excerpt;
    }

    /**
     * Calculate relevance score untuk document
     *
     * @param Document $document
     * @param string $query
     * @return float
     */
    protected function calculateRelevanceScore(Document $document, string $query): float
    {
        $score = 0.0;
        $keywords = $this->extractKeywords($query);

        // Title match (40 points)
        foreach ($keywords as $keyword) {
            if (stripos($document->title, $keyword) !== false) {
                $score += 10;
            }
        }

        // Description match (30 points)
        foreach ($keywords as $keyword) {
            if (stripos($document->description, $keyword) !== false) {
                $score += 7.5;
            }
        }

        // Tags match (20 points)
        if ($document->tags) {
            foreach ($keywords as $keyword) {
                foreach ($document->tags as $tag) {
                    if (stripos($tag, $keyword) !== false) {
                        $score += 5;
                    }
                }
            }
        }

        // Popularity score (10 points)
        $popularityScore = min(10, ($document->view_count + $document->download_count) / 10);
        $score += $popularityScore;

        // Featured bonus
        if ($document->is_featured) {
            $score += 10;
        }

        // Normalize to 0-1 scale
        return min(1.0, $score / 100);
    }

    /**
     * Extract keywords dari query
     *
     * @param string $query
     * @return array
     */
    protected function extractKeywords(string $query): array
    {
        // Remove common Indonesian stop words
        $stopWords = [
            'yang', 'dan', 'di', 'dari', 'ke', 'pada', 'untuk', 'dengan', 'oleh',
            'adalah', 'ini', 'itu', 'tersebut', 'ada', 'atau', 'juga', 'akan',
            'apa', 'apakah', 'bagaimana', 'kapan', 'dimana', 'siapa', 'mengapa',
            'karena', 'jika', 'saya', 'kamu', 'kami', 'mereka', 'nya', 'ku', 'mu'
        ];

        $words = explode(' ', strtolower($query));
        $keywords = array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });

        return array_values($keywords);
    }

    /**
     * Get featured documents untuk initial context
     *
     * @param int $limit
     * @return Collection<Document>
     */
    public function getFeaturedDocuments(int $limit = 5): Collection
    {
        return Document::where('status', 'approved')
            ->where('is_public', true)
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular documents untuk suggestions
     *
     * @param int $limit
     * @return Collection<Document>
     */
    public function getPopularDocuments(int $limit = 5): Collection
    {
        return Document::where('status', 'approved')
            ->where('is_public', true)
            ->orderByRaw('(view_count + download_count + citation_count) DESC')
            ->limit($limit)
            ->get();
    }
}
