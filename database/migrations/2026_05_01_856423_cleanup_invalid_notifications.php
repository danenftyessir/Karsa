<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * migration untuk membersihkan notifikasi dengan action_url yang invalid
 * 
 * file: database/migrations/2026_05_01_000001_cleanup_invalid_notifications.php
 * 
 * jalankan dengan: php artisan migrate
 * 
 * migration ini akan:
 * 1. hapus notifikasi dengan action_url yang mengarah ke /notifications/latest
 * 2. hapus notifikasi dengan action_url yang mengarah ke endpoint API
 * 3. set null untuk action_url yang hanya berisi '#' atau kosong
 * 4. log jumlah notifikasi yang dihapus/diupdate
 */
return new class extends Migration
{
    /**
     * jalankan migrasi
     */
    public function up(): void
    {
        Log::info('=== CLEANUP INVALID NOTIFICATIONS START ===');
        
        // 1. hitung notifikasi dengan action_url invalid sebelum cleanup
        $countBefore = DB::table('notifications')->count();
        Log::info("Total notifications before cleanup: {$countBefore}");
        
        // 2. hapus notifikasi dengan action_url yang mengarah ke /notifications/latest
        $deletedLatest = DB::table('notifications')
            ->where('action_url', 'LIKE', '%/notifications/latest%')
            ->delete();
        Log::info("Deleted notifications with /notifications/latest: {$deletedLatest}");
        
        // 3. hapus notifikasi dengan action_url yang mengarah ke /notifications/getLatest
        $deletedGetLatest = DB::table('notifications')
            ->where('action_url', 'LIKE', '%/notifications/getLatest%')
            ->delete();
        Log::info("Deleted notifications with /notifications/getLatest: {$deletedGetLatest}");
        
        // 4. hapus notifikasi dengan action_url yang mengarah ke /api/
        $deletedApi = DB::table('notifications')
            ->where('action_url', 'LIKE', '%/api/%')
            ->delete();
        Log::info("Deleted notifications with /api/: {$deletedApi}");
        
        // 5. set null untuk action_url yang hanya berisi '#', kosong, atau spasi
        $updatedEmpty = DB::table('notifications')
            ->whereIn('action_url', ['#', '', ' '])
            ->update(['action_url' => null]);
        Log::info("Updated empty action_url to null: {$updatedEmpty}");
        
        // 6. hitung total setelah cleanup
        $countAfter = DB::table('notifications')->count();
        $totalCleaned = $countBefore - $countAfter;
        Log::info("Total notifications after cleanup: {$countAfter}");
        Log::info("Total cleaned: {$totalCleaned}");
        
        // 7. summary report
        $summary = [
            'before_count' => $countBefore,
            'after_count' => $countAfter,
            'total_cleaned' => $totalCleaned,
            'deleted_latest' => $deletedLatest,
            'deleted_get_latest' => $deletedGetLatest,
            'deleted_api' => $deletedApi,
            'updated_empty' => $updatedEmpty,
        ];
        
        Log::info('Cleanup Summary:', $summary);
        Log::info('=== CLEANUP INVALID NOTIFICATIONS END ===');
        
        // tampilkan summary di console
        echo "\n";
        echo "=== NOTIFICATION CLEANUP SUMMARY ===\n";
        echo "Total notifications before: {$countBefore}\n";
        echo "Total notifications after:  {$countAfter}\n";
        echo "Total cleaned:              {$totalCleaned}\n";
        echo "===================================\n";
        echo "\n";
    }

    /**
     * rollback migrasi
     * 
     * CATATAN: tidak ada rollback untuk cleaning data
     * karena data yang sudah dihapus tidak bisa dikembalikan
     */
    public function down(): void
    {
        Log::warning('Cannot rollback cleanup migration - deleted data is permanent');
        echo "\n";
        echo "WARNING: Cannot rollback cleanup migration.\n";
        echo "Deleted notifications cannot be restored.\n";
        echo "\n";
    }
};