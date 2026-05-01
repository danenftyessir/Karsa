<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk menambahkan kolom is_cover ke tabel problem_images
 * kolom ini menandakan gambar mana yang menjadi cover/thumbnail problem
 * 
 * jalankan: php artisan migrate
 * rollback: php artisan migrate:rollback
 */
return new class extends Migration
{
    /**
     * jalankan migration
     */
    public function up(): void
    {
        Schema::table('problem_images', function (Blueprint $table) {
            // tambahkan kolom is_cover setelah kolom caption
            $table->boolean('is_cover')->default(false)->after('caption');
            
            // tambahkan index untuk query performance
            $table->index('is_cover');
        });
    }

    /**
     * rollback migration
     */
    public function down(): void
    {
        Schema::table('problem_images', function (Blueprint $table) {
            $table->dropIndex(['is_cover']);
            $table->dropColumn('is_cover');
        });
    }
};