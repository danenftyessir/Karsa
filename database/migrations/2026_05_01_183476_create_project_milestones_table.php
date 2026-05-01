<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk membuat tabel project_milestones
 * tabel ini menyimpan milestone/tahapan dari setiap proyek
 * mahasiswa update progress melalui milestone ini
 * 
 * path: database/migrations/2026_05_01_000002_create_project_milestones_table.php
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
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            
            // relasi ke project
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            // informasi milestone
            $table->string('title');
            $table->text('description')->nullable();
            
            // urutan milestone
            $table->integer('order')->default(0);
            
            // target date dan actual completion
            $table->date('target_date');
            $table->date('completed_at')->nullable();
            
            // status: pending, in_progress, completed, delayed
            $table->enum('status', ['pending', 'in_progress', 'completed', 'delayed'])->default('pending');
            
            // progress (0-100)
            $table->integer('progress_percentage')->default(0);
            
            // notes dari mahasiswa
            $table->text('notes')->nullable();
            
            // deliverables/output dari milestone ini
            $table->json('deliverables')->nullable();
            
            $table->timestamps();
            
            // indexes
            $table->index('project_id');
            $table->index(['project_id', 'order']);
            $table->index('status');
            $table->index('target_date');
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};