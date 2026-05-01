<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Column 'stories' already exists as TEXT from previous migration
        // If JSON type is needed, run this SQL in Supabase:
        // ALTER TABLE students ALTER COLUMN stories TYPE json USING CASE WHEN stories = '' THEN NULL ELSE stories::json END;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // change stories back to text
            $table->text('stories')->nullable()->change();
        });

        Schema::table('students', function (Blueprint $table) {
            // rename stories back to story
            $table->renameColumn('stories', 'story');
        });
    }
};
