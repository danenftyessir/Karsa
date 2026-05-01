<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk membuat tabel project_reports
 * tabel ini menyimpan laporan progress berkala dari mahasiswa
 * bisa weekly report, monthly report, atau final report
 * 
 * path: database/migrations/2026_05_01_000003_create_project_reports_table.php
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
        Schema::create('project_reports', function (Blueprint $table) {
            $table->id();
            
            // relasi ke project
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            // relasi ke student yang submit
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            
            // tipe report: weekly, monthly, final
            $table->enum('type', ['weekly', 'monthly', 'final'])->default('weekly');
            
            // informasi report
            $table->string('title');
            $table->text('summary'); // ringkasan kegiatan
            $table->text('activities'); // kegiatan yang dilakukan
            $table->text('challenges')->nullable(); // kendala/tantangan
            $table->text('next_plans')->nullable(); // rencana ke depan
            
            // periode report
            $table->date('period_start');
            $table->date('period_end');
            
            // file attachment
            $table->string('document_path')->nullable();
            
            // foto dokumentasi (json array of paths)
            $table->json('photos')->nullable();
            
            // status review: pending, reviewed, approved, revision_needed
            $table->enum('status', ['pending', 'reviewed', 'approved', 'revision_needed'])->default('pending');
            
            // feedback dari institusi
            $table->text('institution_feedback')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
            
            // indexes
            $table->index('project_id');
            $table->index('student_id');
            $table->index('type');
            $table->index('status');
            $table->index(['project_id', 'created_at']);
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('project_reports');
    }
};