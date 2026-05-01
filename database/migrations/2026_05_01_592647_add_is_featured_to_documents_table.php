<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk menambahkan field is_featured ke tabel documents
 * 
 * path: database/migrations/2026_05_01_075000_add_is_featured_to_documents_table.php
 * jalankan: php artisan migrate
 * rollback: php artisan migrate:rollback
 */
return new class extends Migration
{
    /**
     * jalankan migrasi
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // tambahkan field is_featured jika belum ada
            if (!Schema::hasColumn('documents', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_public');
            }
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
        });
    }
};