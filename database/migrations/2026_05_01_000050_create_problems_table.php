<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('background')->nullable();
            $table->text('objectives')->nullable();
            $table->text('scope')->nullable();
            
            // lokasi
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->foreignId('regency_id')->constrained()->onDelete('cascade');
            $table->string('village')->nullable();
            $table->text('detailed_location')->nullable();
            
            // kategori SDG (stored as json array)
            $table->json('sdg_categories');
            
            // requirements
            $table->integer('required_students');
            $table->json('required_skills');
            $table->json('required_majors')->nullable();
            
            // timeline
            $table->date('start_date');
            $table->date('end_date');
            $table->date('application_deadline');
            $table->integer('duration_months');
            
            // tingkat kesulitan
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('intermediate');
            
            // status
            $table->enum('status', ['draft', 'open', 'in_progress', 'completed', 'closed'])->default('draft');
            
            // expected outcomes
            $table->text('expected_outcomes')->nullable();
            $table->json('deliverables')->nullable();
            
            // fasilitas
            $table->json('facilities_provided')->nullable();
            
            // statistik
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->integer('accepted_students')->default(0);
            
            // featured/priority
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('difficulty_level');
            $table->index('application_deadline');
            $table->index('province_id');
            $table->index('regency_id');
            $table->index(['status', 'application_deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};
