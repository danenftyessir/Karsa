<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk membuat tabel notifications
 * tabel ini menyimpan notifikasi untuk user (mahasiswa dan instansi)
 * 
 * jalankan: php artisan migrate
 * rollback: php artisan migrate:rollback
 */
return new class extends Migration
{
    /**
     * jalankan migrasi
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // relasi ke user yang menerima notifikasi
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // tipe notifikasi untuk kategorisasi
            // contoh: application_submitted, application_accepted, project_started, dll
            $table->string('type');
            
            // judul notifikasi
            $table->string('title');
            
            // isi pesan notifikasi
            $table->text('message');
            
            // data tambahan dalam format JSON (opsional)
            // bisa berisi id referensi, metadata, dsb
            $table->json('data')->nullable();
            
            // URL untuk action ketika notifikasi diklik
            $table->string('action_url')->nullable();
            
            // status apakah sudah dibaca
            $table->boolean('is_read')->default(false);
            
            // waktu dibaca
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
            
            // index untuk query performance
            $table->index('user_id');
            $table->index('type');
            $table->index('is_read');
            $table->index('created_at');
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};