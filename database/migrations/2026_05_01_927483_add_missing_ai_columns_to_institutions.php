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
        Schema::table('institutions', function (Blueprint $table) {
            // Add missing AI validation columns
            $table->string('ai_validation_status')->default('pending_ai_validation')->after('ai_validation_report');
            $table->text('ai_validation_notes')->nullable()->after('ai_validation_status');
            $table->timestamp('ai_validated_at')->nullable()->after('ai_validation_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn([
                'ai_validation_status',
                'ai_validation_notes',
                'ai_validated_at'
            ]);
        });
    }
};
