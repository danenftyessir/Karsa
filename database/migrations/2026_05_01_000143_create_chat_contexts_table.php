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
        Schema::create('chat_contexts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->onDelete('cascade');
            $table->jsonb('indexed_documents')->nullable(); // Array of document IDs used in this conversation
            $table->jsonb('filters_applied')->nullable(); // Filters applied to document search
            $table->text('knowledge_base_summary')->nullable(); // Summary of the knowledge base context
            $table->integer('indexed_doc_count')->default(0); // Number of documents indexed
            $table->timestamps();

            $table->index('conversation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_contexts');
    }
};
