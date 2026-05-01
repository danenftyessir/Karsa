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
        Schema::table('ai_validation_logs', function (Blueprint $table) {
            $table->boolean('is_passed')->default(false)->after('validation_type');
            $table->decimal('confidence', 5, 2)->nullable()->after('score');
            $table->json('issues')->nullable()->after('details');
            $table->text('recommendation')->nullable()->after('issues');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_validation_logs', function (Blueprint $table) {
            $table->dropColumn(['is_passed', 'confidence', 'issues', 'recommendation']);
        });
    }
};
