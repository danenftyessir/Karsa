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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->onDelete('cascade');
            $table->string('role'); // user, assistant, system
            $table->text('content');
            $table->jsonb('context')->nullable(); // Context used for this message (documents, filters, etc.)
            $table->jsonb('metadata')->nullable(); // Additional metadata (model used, processing time, etc.)
            $table->decimal('confidence_score', 5, 2)->nullable(); // AI confidence (0-1)
            $table->integer('token_usage')->nullable(); // Tokens used for this message
            $table->boolean('flagged')->default(false); // For inappropriate content
            $table->string('flag_reason')->nullable(); // Reason for flagging
            $table->timestamps();
            $table->softDeletes();

            $table->index('conversation_id');
            $table->index(['conversation_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
