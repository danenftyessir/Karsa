<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->string('major');
            $table->string('nim')->unique();
            $table->integer('semester');
            $table->string('phone', 20);
            $table->string('profile_photo_path')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('university_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};