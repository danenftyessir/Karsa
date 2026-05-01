<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk menambahkan kolom proposal_content dan proposal_filename
 * untuk menyimpan file proposal langsung di database
 * 
 * jalankan: php artisan migrate
 * 
 * path: database/migrations/2026_05_01_000001_add_proposal_content_to_applications_table.php
 */
return new class extends Migration
{
    /**
     * jalankan migrasi
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // simpan konten file sebagai longblob (binary)
            // longblob bisa simpan sampai 4GB, cukup untuk PDF 5MB
            $table->longText('proposal_content')->nullable()->after('proposal_path');
            
            // simpan nama file original
            $table->string('proposal_filename')->nullable()->after('proposal_content');
            
            // simpan mime type file
            $table->string('proposal_mime_type')->nullable()->after('proposal_filename');
            
            // simpan ukuran file (bytes)
            $table->unsignedInteger('proposal_size')->nullable()->after('proposal_mime_type');
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'proposal_content',
                'proposal_filename',
                'proposal_mime_type',
                'proposal_size',
            ]);
        });
    }
};