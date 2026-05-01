<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk menambah kolom completed_at di tabel projects
 * kolom ini diperlukan untuk sorting dan filtering proyek yang sudah selesai
 * 
 * jalankan: php artisan migrate
 */
return new class extends Migration
{
    /**
     * jalankan migrations
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('submitted_at');
        });
    }

    /**
     * kembalikan migrations
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};