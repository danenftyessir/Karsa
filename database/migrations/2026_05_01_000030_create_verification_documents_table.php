<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('verification_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('cascade');

            // Document info
            $table->string('document_type', 50); // 'official_letter', 'logo', 'pic_identity', 'npwp'
            $table->string('file_url');
            $table->string('file_name');
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();

            // AI Analysis
            $table->string('ai_verification_id')->nullable();
            $table->string('ai_status', 50)->nullable(); // 'approved', 'needs_review', 'rejected'
            $table->decimal('ai_score', 5, 2)->nullable();
            $table->decimal('ai_confidence', 3, 2)->nullable();
            $table->json('ai_flags')->nullable();
            $table->json('ai_extracted_data')->nullable();
            $table->text('ai_reasoning')->nullable();
            $table->timestamp('ai_processed_at')->nullable();

            // Human Review
            $table->boolean('human_reviewed')->default(false);
            $table->string('human_status', 50)->nullable();
            $table->text('human_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['institution_id', 'document_type']);
            $table->index('ai_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_documents');
    }
};
