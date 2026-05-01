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
            // File paths untuk KTP dan NPWP
            if (!Schema::hasColumn('institutions', 'ktp_path')) {
                $table->string('ktp_path')->nullable()->after('verification_document_path');
            }
            if (!Schema::hasColumn('institutions', 'npwp_path')) {
                $table->string('npwp_path')->nullable()->after('ktp_path');
            }

            // AI Validation
            if (!Schema::hasColumn('institutions', 'ai_validation_score')) {
                $table->decimal('ai_validation_score', 5, 2)->default(0)->after('npwp_path');
            }
            if (!Schema::hasColumn('institutions', 'ai_validation_report')) {
                $table->json('ai_validation_report')->nullable()->after('ai_validation_score');
            }

            // Skip verification_status karena sudah ada
            // Note: Untuk update ENUM values di PostgreSQL perlu ALTER TYPE, skip untuk sekarang

            // Rejection reason
            if (!Schema::hasColumn('institutions', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }

            // Verification tracking
            if (!Schema::hasColumn('institutions', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            if (!Schema::hasColumn('institutions', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'ktp_path',
                'npwp_path',
                'ai_validation_score',
                'ai_validation_report',
                'verification_status',
                'rejection_reason',
                'verified_at',
                'verified_by'
            ]);
        });
    }
};
