<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk menambahkan field tambahan ke tabel students
 * 
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
        Schema::table('students', function (Blueprint $table) {
            // field untuk menyimpan skills mahasiswa sebagai json
            $table->json('skills')->nullable()->after('bio');
            
            // field untuk mengatur visibility portfolio publik
            $table->boolean('portfolio_visible')->default(true)->after('skills');
            
            // TODO: field tambahan untuk future enhancement
            // $table->string('linkedin_url')->nullable()->after('portfolio_visible');
            // $table->string('github_url')->nullable()->after('linkedin_url');
            // $table->string('personal_website')->nullable()->after('github_url');
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'skills',
                'portfolio_visible',
                'linkedin_url'
                // 'github_url',
                // 'personal_website',
            ]);
        });
    }
};