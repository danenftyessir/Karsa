<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk tabel wishlists
 * tabel ini menyimpan daftar problem yang di-save/bookmark oleh mahasiswa
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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            
            // relasi ke student dan problem
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('problem_id')->constrained()->onDelete('cascade');
            
            // catatan pribadi mahasiswa (optional)
            $table->text('notes')->nullable();
            
            // timestamp untuk tracking
            $table->timestamps();
            
            // unique constraint: satu mahasiswa tidak bisa save problem yang sama lebih dari sekali
            $table->unique(['student_id', 'problem_id']);
            
            // index untuk query performance
            $table->index('student_id');
            $table->index('problem_id');
            $table->index('created_at');
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};