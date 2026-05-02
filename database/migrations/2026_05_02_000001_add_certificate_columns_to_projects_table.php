<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * jalankan migrasi
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('certificate_path')->nullable()->after('institution_review');
            $table->string('certificate_number')->nullable()->after('certificate_path');
            $table->timestamp('certificate_generated_at')->nullable()->after('certificate_number');
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['certificate_path', 'certificate_number', 'certificate_generated_at']);
        });
    }
};
