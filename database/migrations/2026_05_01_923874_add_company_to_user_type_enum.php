<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambahkan 'company' ke enum user_type di tabel users
     */
    public function up(): void
    {
        // Untuk PostgreSQL, kita perlu mengubah kolom dengan raw SQL
        // karena Laravel tidak support alter enum directly

        // Check if users table exists first
        if (!Schema::hasTable('users')) {
            return; // Skip if users table doesn't exist yet
        }

        try {
            // Check if constraint already has 'company' value
            $result = DB::select("
                SELECT pg_get_constraintdef(oid) as definition
                FROM pg_constraint
                WHERE conrelid = 'users'::regclass
                AND conname = 'users_user_type_check'
            ");

            // If constraint exists and already includes 'company', skip this migration
            if (!empty($result) && strpos($result[0]->definition, 'company') !== false) {
                return; // Already has 'company', skip
            }

            // Drop the specific constraint if it exists
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_user_type_check");

            // Recreate constraint with new values including 'company'
            DB::statement("
                ALTER TABLE users ADD CONSTRAINT users_user_type_check
                CHECK (user_type::text = ANY (ARRAY['student'::text, 'institution'::text, 'admin'::text, 'company'::text]))
            ");
        } catch (\Exception $e) {
            // Log error but don't fail migration if constraint already correct
            if (strpos($e->getMessage(), 'does not exist') === false &&
                strpos($e->getMessage(), 'already exists') === false) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     * Kembalikan ke enum tanpa 'company'
     */
    public function down(): void
    {
        // Hapus semua users dengan type 'company' sebelum rollback
        DB::table('users')->where('user_type', 'company')->delete();

        // Drop constraint yang ada
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_user_type_check");

        // Recreate constraint tanpa 'company'
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_user_type_check CHECK (user_type IN ('student', 'institution', 'admin'))");
    }
};
