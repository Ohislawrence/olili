<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_profile_id')->constrained('student_profiles')->onDelete('cascade');

            // Progress tracking
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->integer('completed_modules')->default(0);
            $table->integer('total_modules')->default(0);

            // Status and dates
            $table->enum('status', ['enrolled', 'active', 'paused', 'completed', 'dropped'])->default('enrolled');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();

            // Student-specific course data
            $table->json('personalized_data')->nullable(); // Store any student-specific modifications
            $table->decimal('actual_duration_hours', 8, 2)->nullable();

            $table->unique(['course_id', 'user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
