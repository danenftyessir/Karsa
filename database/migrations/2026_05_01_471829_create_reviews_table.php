<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk membuat tabel reviews
 * tabel ini menyimpan review/rating dari institusi ke mahasiswa
 * dan sebaliknya dari mahasiswa ke institusi
 * 
 * path: database/migrations/2026_05_01_000005_create_reviews_table.php
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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            
            // relasi ke project
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            // reviewer (bisa institution atau student)
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            
            // yang direview (bisa student atau institution)
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade');
            
            // tipe review: institution_to_student, student_to_institution
            $table->enum('type', ['institution_to_student', 'student_to_institution']);
            
            // rating (1-5 bintang)
            $table->integer('rating'); // 1-5
            
            // aspek penilaian (untuk institution_to_student)
            $table->integer('professionalism_rating')->nullable(); // 1-5
            $table->integer('communication_rating')->nullable(); // 1-5
            $table->integer('quality_rating')->nullable(); // 1-5
            $table->integer('timeliness_rating')->nullable(); // 1-5
            
            // review text
            $table->text('review_text');
            
            // highlights positif dan area improvement
            $table->text('strengths')->nullable();
            $table->text('improvements')->nullable();
            
            // visibility
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            
            // response dari yang direview
            $table->text('response')->nullable();
            $table->timestamp('responded_at')->nullable();
            
            $table->timestamps();
            
            // indexes
            $table->index('project_id');
            $table->index('reviewer_id');
            $table->index('reviewee_id');
            $table->index('type');
            $table->index('rating');
            $table->index(['reviewee_id', 'is_public']);
            
            // constraint: satu review per project per reviewer
            $table->unique(['project_id', 'reviewer_id']);
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};