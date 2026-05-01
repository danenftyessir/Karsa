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
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();

            // Duration and pricing
            $table->integer('duration_months');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('IDR');

            // Limits
            $table->integer('problem_limit')->nullable(); // NULL = unlimited
            $table->integer('applicant_limit')->nullable();
            $table->integer('ai_verification_limit')->nullable();
            $table->integer('ai_chat_limit')->nullable();

            // Features (stored as JSON)
            $table->json('features')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('is_active');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_packages');
    }
};
