<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * fix double-encoded JSON di database
 * convert dari ""[13,7]"" menjadi [13,7]
 * handle string slugs untuk documents
 * 
 * usage: php artisan fix:double-encoded-json
 */
class FixDoubleEncodedJsonCommand extends Command
{
    protected $signature = 'fix:double-encoded-json {--table= : Specific table to fix (problems or documents)}';
    protected $description = 'Fix double-encoded JSON in sdg_categories and categories columns';

    // mapping slug ke integer SDG
    protected $sdgMapping = [
        'no_poverty' => 1,
        'zero_hunger' => 2,
        'good_health' => 3,
        'quality_education' => 4,
        'gender_equality' => 5,
        'clean_water' => 6,
        'affordable_energy' => 7,
        'decent_work' => 8,
        'industry_innovation' => 9,
        'reduced_inequalities' => 10,
        'sustainable_cities' => 11,
        'responsible_consumption' => 12,
        'climate_action' => 13,
        'life_below_water' => 14,
        'life_on_land' => 15,
        'peace_justice' => 16,
        'partnerships' => 17,
    ];

    public function handle()
    {
        $this->info('🚀 Fixing double-encoded JSON data...');
        $this->newLine();

        $table = $this->option('table');
        
        if (!$table || $table === 'problems') {
            $this->fixProblemsTable();
        }
        
        if (!$table || $table === 'documents') {
            $this->fixDocumentsTable();
        }

        $this->newLine();
        $this->info('✅ Done!');
    }

    protected function fixProblemsTable()
    {
        $this->info('📋 Fixing Problems table...');
        
        $problems = DB::table('problems')->get(['id', 'title', 'sdg_categories']);
        $fixed = 0;
        $alreadyGood = 0;
        
        foreach ($problems as $problem) {
            $original = $problem->sdg_categories;
            
            // skip jika sudah proper JSON array
            $test = json_decode($original, true);
            if (is_array($test) && !empty($test) && is_int($test[0])) {
                $alreadyGood++;
                continue;
            }
            
            // jika string dengan quotes atau brackets
            if (is_string($original)) {
                $this->line("  Problem #{$problem->id}: {$problem->title}");
                $this->line("    Before: {$original}");
                
                // remove outer quotes jika ada
                $cleaned = trim($original, '"');
                
                // decode JSON
                $decoded = json_decode($cleaned, true);
                
                if (is_array($decoded) && !empty($decoded)) {
                    // convert ke integer array
                    $finalArray = array_values(array_map('intval', $decoded));
                    
                    $this->line("    After:  " . json_encode($finalArray));
                    
                    try {
                        // ✅ FIX: gunakan DB::statement dengan proper escaping
                        DB::statement(
                            "UPDATE problems SET sdg_categories = ? WHERE id = ?",
                            [json_encode($finalArray), $problem->id]
                        );
                        
                        $fixed++;
                    } catch (\Exception $e) {
                        $this->error("    ERROR: {$e->getMessage()}");
                    }
                } else {
                    $this->error("    ERROR: Cannot decode JSON for problem #{$problem->id}");
                }
                
                $this->newLine();
            }
        }
        
        $this->info("  ✓ Fixed: {$fixed}");
        $this->info("  - Already correct: {$alreadyGood}");
        $this->newLine();
    }

    protected function fixDocumentsTable()
    {
        $this->info('📄 Fixing Documents table...');
        
        $documents = DB::table('documents')->get(['id', 'title', 'categories']);
        $fixed = 0;
        $alreadyGood = 0;
        
        foreach ($documents as $document) {
            $original = $document->categories;
            
            // skip jika sudah proper JSON array dengan integer
            $test = json_decode($original, true);
            if (is_array($test) && !empty($test) && is_int($test[0])) {
                $alreadyGood++;
                continue;
            }
            
            if (is_string($original)) {
                $this->line("  Document #{$document->id}: " . substr($document->title, 0, 60));
                $this->line("    Before: {$original}");
                
                // remove outer quotes
                $cleaned = trim($original, '"');
                
                // decode JSON
                $decoded = json_decode($cleaned, true);
                
                if (is_array($decoded) && !empty($decoded)) {
                    $finalArray = [];
                    
                    // cek apakah array berisi string slugs atau integer
                    if (is_string($decoded[0])) {
                        // convert slug ke integer menggunakan mapping
                        foreach ($decoded as $slug) {
                            if (isset($this->sdgMapping[$slug])) {
                                $finalArray[] = $this->sdgMapping[$slug];
                            } else {
                                $this->warn("    WARNING: Unknown slug '{$slug}'");
                            }
                        }
                    } else {
                        // sudah integer, tinggal clean up
                        $finalArray = array_values(array_map('intval', $decoded));
                    }
                    
                    if (!empty($finalArray)) {
                        $this->line("    After:  " . json_encode($finalArray));
                        
                        try {
                            DB::statement(
                                "UPDATE documents SET categories = ? WHERE id = ?",
                                [json_encode($finalArray), $document->id]
                            );
                            
                            $fixed++;
                        } catch (\Exception $e) {
                            $this->error("    ERROR: {$e->getMessage()}");
                        }
                    } else {
                        $this->error("    ERROR: Empty final array for document #{$document->id}");
                    }
                } else {
                    $this->error("    ERROR: Cannot decode JSON for document #{$document->id}");
                }
                
                $this->newLine();
            }
        }
        
        $this->info("  ✓ Fixed: {$fixed}");
        $this->info("  - Already correct: {$alreadyGood}");
    }
}