<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('name');  
            $table->string('type'); 
            
            $table->text('address');
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->foreignId('regency_id')->constrained()->onDelete('cascade');
            $table->string('email'); 
            $table->string('phone', 20); 
            $table->string('logo_path')->nullable();
            $table->string('pic_name'); // person in charge
            $table->string('pic_position');
            $table->string('pic_phone', 20)->nullable();
            $table->string('verification_document_path')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();

            // Legacy verification (for backward compatibility)
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();

            // AI Verification fields
            $table->string('verification_status', 50)->default('pending_verification');
            // 'pending_verification', 'needs_review', 'pending_payment', 'active', 'suspended', 'rejected'
            $table->decimal('verification_score', 5, 2)->nullable();
            $table->decimal('verification_confidence', 3, 2)->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();

            // Subscription fields
            $table->string('subscription_status', 50)->default('pending_payment');
            // 'pending_payment', 'active', 'expired', 'cancelled', 'suspended'
            $table->unsignedBigInteger('subscription_package_id')->nullable();
            $table->timestamp('subscription_started_at')->nullable();
            $table->timestamp('subscription_expires_at')->nullable();

            $table->timestamps();
            
            $table->index('user_id');
            $table->index('province_id');
            $table->index('regency_id');
            $table->index('is_verified');
            $table->index('verification_status');
            $table->index('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};