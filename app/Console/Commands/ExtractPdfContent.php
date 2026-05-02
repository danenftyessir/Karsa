<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Services\SupabaseStorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class ExtractPdfContent extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'documents:extract
                            {--all : Extract from all documents}
                            {--id= : Extract from specific document ID}
                            {--limit=100 : Maximum documents to process}';

    /**
     * The console command description.
     */
    protected $description = 'Extract text content from PDF documents and store in database';

    protected $supabaseStorage;

    public function __construct()
    {
        parent::__construct();
        $this->supabaseStorage = new SupabaseStorageService();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting PDF content extraction...');

        // Get documents to process
        $query = Document::where('file_type', 'pdf')
            ->whereNotNull('file_path');

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        } elseif (!$this->option('all')) {
            $query->whereNull('content'); // Only process documents without content
        }

        $limit = (int) $this->option('limit');
        $documents = $query->limit($limit)->get();

        $this->info("Found {$documents->count()} documents to process.");

        $success = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar($documents->count());
        $bar->start();

        foreach ($documents as $document) {
            try {
                $content = $this->extractContent($document);

                if ($content) {
                    $document->update(['content' => $content]);
                    $this->line(" [OK] Doc #{$document->id}: " . strlen($content) . " chars");
                    $success++;
                } else {
                    $this->warn(" [EMPTY] Doc #{$document->id}: No content extracted");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error(" [ERROR] Doc #{$document->id}: {$e->getMessage()}");
                Log::error("PDF extraction failed for doc #{$document->id}", [
                    'error' => $e->getMessage(),
                    'file_path' => $document->file_path
                ]);
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Extraction complete!");
        $this->info("Success: {$success}");
        $this->info("Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Extract text content from PDF document
     */
    protected function extractContent(Document $document): ?string
    {
        $fileUrl = $this->supabaseStorage->getPublicUrl($document->file_path);

        $this->info("Fetching: {$fileUrl}");

        // Download PDF content
        $http = Http::timeout(60);
        if (config('app.env') === 'local') {
            $http = $http->withoutVerifying();
        }
        $response = $http->get($fileUrl);

        if (!$response->successful()) {
            throw new \Exception("Failed to download PDF: HTTP {$response->status()}");
        }

        $pdfContent = $response->body();

        // Save to temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'pdf_') . '.pdf';
        file_put_contents($tempFile, $pdfContent);

        try {
            // Parse PDF
            $parser = new Parser();
            $pdf = $parser->parseFile($tempFile);

            // Extract text from all pages
            $text = $pdf->getText();

            // Clean up text
            $text = $this->cleanText($text);

            // Limit content size (max 500KB to prevent database issues)
            if (strlen($text) > 500000) {
                $text = substr($text, 0, 500000);
                $this->warn("  Content truncated to 500KB");
            }

            return $text ?: null;

        } finally {
            // Clean up temp file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * Clean extracted text
     */
    protected function cleanText(string $text): string
    {
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Remove non-printable characters except newlines
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Normalize line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Trim
        $text = trim($text);

        return $text;
    }
}