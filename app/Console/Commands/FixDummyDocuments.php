<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\ProjectReport;
use Illuminate\Console\Command;

class FixDummyDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:fix-dummy
                            {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix all documents and project reports with dummy file paths to use real Supabase files';

    /**
     * Real PDF files available in Supabase storage
     */
    private array $realFiles = [
        '3341b-laporan_kkn_hasbi_mudzaki_fix-1-.pdf',
        'aaLAPORAN-PROGRAM-KERJA-KKN.pdf',
        'bc4f599c360deae829ef0952f9200a4f.pdf',
        'd5460592f2ee74a2f9f5910138d650e6.pdf',
        'f3f3ec539ee2d963e804d3a964b3290f.pdf',
        'KKN_III.D.3_REG.96_2022.pdf',
        'LAPORAN AKHIR KKN .pdf',
        'laporan akhir KKN PPM OK.pdf',
        'LAPORAN KELOMPOK KKN 1077fix.pdf',
        'LAPORAN KKN DEMAPESA.pdf',
        'LAPORAN KKN KELOMPOK 2250.pdf',
        'LAPORAN KKN_1.A.2_REG.119_2024.pdf',
        'LAPORAN KKN.pdf',
        'laporan_3460160906115724.pdf',
        'laporan_akhir_201_35_2.pdf',
        'laporan_akhir_3011_45_5.pdf',
        'laporan-kelompok.pdf',
        'Laporan-KKN-2019.pdf',
        'Laporan-Tugas-Akhir-KKN-156.pdf',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Step 1: Find all documents with dummy files
        $this->info('📊 Analyzing documents table...');
        $dummyDocs = Document::where('file_path', 'like', '%dummy%')->get();
        $totalDummyDocs = $dummyDocs->count();

        $this->info('📊 Analyzing project_reports table...');
        $dummyReports = ProjectReport::where('document_path', 'like', '%dummy%')->get();
        $totalDummyReports = $dummyReports->count();

        $totalDummy = $totalDummyDocs + $totalDummyReports;

        if ($totalDummy === 0) {
            $this->info('✅ No dummy files found! All documents and reports are using real files.');
            return 0;
        }

        $this->warn("⚠️  Found {$totalDummyDocs} documents with dummy files");
        $this->warn("⚠️  Found {$totalDummyReports} project reports with dummy files");
        $this->warn("⚠️  Total: {$totalDummy} records to fix");
        $this->newLine();

        // Step 2: Show sample of dummy files
        if ($totalDummyDocs > 0) {
            $this->info('Sample dummy documents:');
            $dummyDocs->take(5)->each(function ($doc) {
                $this->line("  Document ID {$doc->id}: {$doc->file_path}");
            });
            $this->newLine();
        }

        if ($totalDummyReports > 0) {
            $this->info('Sample dummy project reports:');
            $dummyReports->take(5)->each(function ($report) {
                $this->line("  Report ID {$report->id}: {$report->document_path}");
            });
            $this->newLine();
        }

        // Step 3: Confirm update
        if (!$dryRun && !$this->confirm('Do you want to update these documents?', true)) {
            $this->warn('❌ Operation cancelled');
            return 1;
        }

        // Step 4: Update dummy documents and reports
        $this->info('🔧 Updating records...');
        $progressBar = $this->output->createProgressBar($totalDummy);
        $progressBar->start();

        $updated = 0;

        // Update documents table
        foreach ($dummyDocs as $doc) {
            $randomFile = $this->realFiles[array_rand($this->realFiles)];
            $newPath = 'documents/reports/' . $randomFile;

            if (!$dryRun) {
                $doc->file_path = $newPath;
                $doc->save();
            }

            $updated++;
            $progressBar->advance();
        }

        // Update project_reports table
        foreach ($dummyReports as $report) {
            $randomFile = $this->realFiles[array_rand($this->realFiles)];
            $newPath = 'documents/reports/' . $randomFile;

            if (!$dryRun) {
                $report->document_path = $newPath;
                $report->save();
            }

            $updated++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Step 5: Verify results
        if (!$dryRun) {
            $remainingDummyDocs = Document::where('file_path', 'like', '%dummy%')->count();
            $remainingDummyReports = ProjectReport::where('document_path', 'like', '%dummy%')->count();
            $remainingTotal = $remainingDummyDocs + $remainingDummyReports;

            if ($remainingTotal === 0) {
                $this->info("✅ Successfully updated {$updated} records!");
                $this->info('✅ No dummy files remaining!');
            } else {
                $this->error("⚠️  Still {$remainingDummyDocs} dummy documents remaining!");
                $this->error("⚠️  Still {$remainingDummyReports} dummy reports remaining!");
            }
        } else {
            $this->info("ℹ️  Would update {$updated} records (dry run)");
        }

        $this->newLine();

        // Step 6: Show sample of updated documents
        $this->info('Sample documents after update:');
        Document::orderBy('id')->limit(5)->get()->each(function ($doc) {
            $url = document_url($doc->file_path);
            $this->line("  ID {$doc->id}: {$doc->file_path}");
            $this->line("    URL: {$url}");
        });

        return 0;
    }
}
