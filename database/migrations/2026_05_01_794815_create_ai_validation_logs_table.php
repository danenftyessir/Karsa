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
        Schema::create('ai_validation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('cascade');
            $table->string('validation_type', 50); // 'document', 'ktp', 'npwp', 'logo', 'data', 'type'
            $table->decimal('score', 5, 2);
            $table->json('details')->nullable();
            $table->string('api_used', 50)->nullable(); // 'claude', 'cohere'
            $table->integer('processing_time')->nullable(); // milliseconds
            $table->timestamps();

            // Index untuk query performa
            $table->index(['institution_id', 'validation_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_validation_logs');
    }
};
