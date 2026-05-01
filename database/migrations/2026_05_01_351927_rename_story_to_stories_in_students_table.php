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
        Schema::table('students', function (Blueprint $table) {
            // rename story column to stories
            $table->renameColumn('story', 'stories');
        });

        // use raw SQL for PostgreSQL to handle the type conversion with USING clause
        DB::statement('ALTER TABLE students ALTER COLUMN stories TYPE json USING CASE WHEN stories IS NULL OR stories = \'\' THEN \'null\'::json ELSE stories::json END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // change stories back to text
        DB::statement('ALTER TABLE students ALTER COLUMN stories TYPE text');

        Schema::table('students', function (Blueprint $table) {
            // rename stories back to story
            $table->renameColumn('stories', 'story');
        });
    }
};
