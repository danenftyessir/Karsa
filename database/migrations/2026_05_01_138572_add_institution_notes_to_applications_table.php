<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * jalankan migrations
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->text('institution_notes')->nullable()->after('feedback');
        });
    }

    /**
     * kembalikan migrations
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('institution_notes');
        });
    }
};