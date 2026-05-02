<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\CertificateService;
use Illuminate\Console\Command;

class GenerateMissingCertificates extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'certificates:generate-missing {--dry-run : Show what would be generated without generating}';

    /**
     * The console command description.
     */
    protected $description = 'Generate certificates for completed projects that do not have one yet';

    public function __construct(
        protected CertificateService $certificateService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // Cari semua project completed yang belum punya certificate
        $projects = Project::with(['student.user', 'institution'])
            ->where('status', 'completed')
            ->whereNull('certificate_path')
            ->get();

        if ($projects->isEmpty()) {
            $this->info('Tidak ada project yang perlu generate sertifikat.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$projects->count()} project yang perlu generate sertifikat.\n");

        $bar = $this->output->createProgressBar($projects->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($projects as $project) {
            if ($dryRun) {
                $projectTitle = $project->problem?->title ?? ('Project #' . $project->id);
            $this->line("  -> [DRY-RUN] {$project->student->user->name} - {$projectTitle}");
            } else {
                $result = $this->certificateService->generateCertificate($project);

                if ($result['success']) {
                    $success++;
                    $this->line("  OK {$project->student->user->name} - {$result['number']}");
                } else {
                    $failed++;
                    $this->error("  FAIL {$project->student->user->name} - {$result['message']}");
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("{$projects->count()} project akan mendapat sertifikat.");
        } else {
            $this->info("Selesai. Berhasil: {$success}, Gagal: {$failed}");
        }

        return Command::SUCCESS;
    }
}