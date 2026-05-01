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
        Schema::create('chat_document_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_messages')->onDelete('cascade');
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->decimal('relevance_score', 5, 2)->nullable(); // How relevant this document is (0-1)
            $table->text('excerpt')->nullable(); // Relevant excerpt from the document
            $table->jsonb('metadata')->nullable(); // Additional metadata about the reference
            $table->timestamps();

            $table->index('message_id');
            $table->index('document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_document_references');
    }
};
