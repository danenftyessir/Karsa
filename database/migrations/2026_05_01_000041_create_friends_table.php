<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * migration untuk tabel friends (koneksi pertemanan antar mahasiswa)
 * 
 * struktur pertemanan menggunakan sistem two-way relationship
 * status: pending, accepted, rejected, blocked
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
        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            
            // relasi ke students
            $table->foreignId('requester_id')
                  ->constrained('students')
                  ->onDelete('cascade')
                  ->comment('mahasiswa yang mengirim permintaan');
            
            $table->foreignId('receiver_id')
                  ->constrained('students')
                  ->onDelete('cascade')
                  ->comment('mahasiswa yang menerima permintaan');
            
            // status pertemanan
            $table->enum('status', ['pending', 'accepted', 'rejected', 'blocked'])
                  ->default('pending')
                  ->comment('status permintaan pertemanan');
            
            // pesan opsional saat request
            $table->text('message')->nullable()->comment('pesan saat mengirim permintaan');
            
            // timestamp kapan di-accept/reject
            $table->timestamp('responded_at')->nullable();
            
            $table->timestamps();
            
            // index untuk query performa
            $table->index(['requester_id', 'status']);
            $table->index(['receiver_id', 'status']);
            
            // unique constraint: satu user tidak bisa kirim request berulang ke user yang sama
            $table->unique(['requester_id', 'receiver_id']);
        });
    }

    /**
     * rollback migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('friends');
    }
}; 