<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk membuat tabel projects
 * tabel ini menyimpan proyek KKN yang sedang/sudah dikerjakan mahasiswa
 * proyek dibuat otomatis ketika aplikasi mahasiswa diterima (accepted)
 * 
 * path: database/migrations/2026_05_01_000001_create_projects_table.php
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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            
            // relasi ke application yang diterima
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            
            // relasi ke student dan problem
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('problem_id')->constrained()->onDelete('cascade');
            
            // relasi ke institution
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            
            // informasi proyek
            $table->string('title'); // sama dengan problem title
            $table->text('description')->nullable();
            
            // status proyek: active, on_hold, completed, cancelled
            $table->enum('status', ['active', 'on_hold', 'completed', 'cancelled'])->default('active');
            
            // timeline
            $table->date('start_date');
            $table->date('end_date');
            $table->date('actual_start_date')->nullable(); // tanggal mulai aktual
            $table->date('actual_end_date')->nullable(); // tanggal selesai aktual
            
            // progress tracking (0-100)
            $table->integer('progress_percentage')->default(0);
            
            // final report
            $table->string('final_report_path')->nullable();
            $table->text('final_report_summary')->nullable();
            $table->timestamp('submitted_at')->nullable();
            
            // review dari institusi
            $table->integer('rating')->nullable(); // 1-5
            $table->text('institution_review')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            // impact metrics
            $table->json('impact_metrics')->nullable(); // {"beneficiaries": 100, "activities": 15}
            
            // visibility untuk portfolio
            $table->boolean('is_portfolio_visible')->default(true);
            $table->boolean('is_featured')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            // indexes
            $table->index('student_id');
            $table->index('problem_id');
            $table->index('institution_id');
            $table->index('status');
            $table->index(['student_id', 'status']);
            $table->index(['institution_id', 'status']);
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};