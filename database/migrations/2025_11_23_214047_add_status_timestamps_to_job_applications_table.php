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
        Schema::table('job_applications', function (Blueprint $table) {
            $table->timestamp('interview_scheduled_at')->nullable()->after('reviewed_at');
            $table->timestamp('offer_extended_at')->nullable()->after('interview_scheduled_at');
            $table->timestamp('hired_at')->nullable()->after('offer_extended_at');
            $table->timestamp('rejected_at')->nullable()->after('hired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['interview_scheduled_at', 'offer_extended_at', 'hired_at', 'rejected_at']);
        });
    }
};
