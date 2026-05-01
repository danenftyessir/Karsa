<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk membuat tabel applications
 * tabel ini menyimpan data aplikasi mahasiswa ke problems/proyek
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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            
            // relasi ke student dan problem
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('problem_id')->constrained()->onDelete('cascade');
            
            // status aplikasi: pending, reviewed, accepted, rejected
            $table->enum('status', ['pending', 'reviewed', 'accepted', 'rejected'])->default('pending');
            
            // dokumen proposal (optional)
            $table->string('proposal_path')->nullable();
            
            // cover letter dan motivasi dari mahasiswa
            $table->text('cover_letter')->nullable();
            $table->text('motivation');
            
            // timestamp untuk tracking proses review
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            // feedback dari instansi untuk aplikasi yang ditolak/diterima
            $table->text('feedback')->nullable();
            
            // tracking untuk prevent duplicate applications
            $table->unique(['student_id', 'problem_id']);
            
            $table->timestamps();
            
            // index untuk query performance
            $table->index('student_id');
            $table->index('problem_id');
            $table->index('status');
            $table->index('applied_at');
            $table->index(['problem_id', 'status']);
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};