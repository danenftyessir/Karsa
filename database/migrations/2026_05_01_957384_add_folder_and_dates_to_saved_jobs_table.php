<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('saved_jobs', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('saved_jobs', 'notes')) {
                $table->text('notes')->nullable()->after('job_posting_id');
            }
            if (!Schema::hasColumn('saved_jobs', 'folder')) {
                $table->string('folder')->nullable()->after('job_posting_id');
            }
            if (!Schema::hasColumn('saved_jobs', 'reminder_at')) {
                $table->timestamp('reminder_at')->nullable()->after('job_posting_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_jobs', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('saved_jobs', 'notes')) $columns[] = 'notes';
            if (Schema::hasColumn('saved_jobs', 'folder')) $columns[] = 'folder';
            if (Schema::hasColumn('saved_jobs', 'reminder_at')) $columns[] = 'reminder_at';

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
