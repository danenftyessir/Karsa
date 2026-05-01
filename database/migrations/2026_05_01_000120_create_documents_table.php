<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk membuat tabel documents
 * tabel ini menyimpan dokumen hasil proyek untuk knowledge repository
 * dokumen bisa diakses oleh semua user sebagai referensi
 * 
 * path: database/migrations/2026_05_01_000004_create_documents_table.php
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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            
            // relasi ke project (nullable untuk dokumen umum)
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            
            // uploader
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            
            // informasi dokumen
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type'); // pdf, docx, xlsx, pptx
            $table->bigInteger('file_size'); // dalam bytes
            
            // kategori dokumen
            $table->json('categories')->nullable(); // SDG categories
            $table->json('tags')->nullable(); // custom tags
            
            // metadata
            $table->string('author_name')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('university_name')->nullable();
            $table->integer('year')->nullable();
            
            // lokasi terkait
            $table->foreignId('province_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('regency_id')->nullable()->constrained()->onDelete('set null');
            
            // statistik
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('citation_count')->default(0);
            
            // visibility
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            
            // approval status untuk quality control
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // indexes
            $table->index('project_id');
            $table->index('uploaded_by');
            $table->index('status');
            $table->index('year');
            $table->index('province_id');
            $table->index('regency_id');
            $table->index(['status', 'is_public']);
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};