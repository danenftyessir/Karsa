<?php

namespace App\Console\Commands;

use App\Models\Institution;
use App\Jobs\ValidateInstitutionDocumentsJob;
use Illuminate\Console\Command;

class TriggerAIValidation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:validate {institution_id? : Institution ID to validate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger AI validation for an institution (latest if no ID provided)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $institutionId = $this->argument('institution_id');

        if ($institutionId) {
            $institution = Institution::find($institutionId);
        } else {
            $institution = Institution::latest()->first();
        }

        if (!$institution) {
            $this->error('Institution not found!');
            return 1;
        }

        $this->info("Institution: {$institution->name}");
        $this->info("Email: {$institution->email}");
        $this->info("Status: {$institution->ai_validation_status}");
        $this->info("Created: {$institution->created_at}");
        $this->newLine();

        $this->info("Documents:");
        $this->info("- KTP: " . ($institution->ktp_path ?? 'NOT UPLOADED'));
        $this->info("- NPWP: " . ($institution->npwp_path ?? 'NOT UPLOADED'));
        $this->info("- Verification Doc: " . ($institution->verification_document_path ?? 'NOT UPLOADED'));
        $this->newLine();

        if ($this->confirm('Dispatch AI validation job now?', true)) {
            ValidateInstitutionDocumentsJob::dispatch($institution)
                ->onQueue('validations');

            $this->info('✅ AI validation job dispatched successfully!');
            $this->info('Check logs with: tail -f storage/logs/laravel.log');
        }

        return 0;
    }
}
