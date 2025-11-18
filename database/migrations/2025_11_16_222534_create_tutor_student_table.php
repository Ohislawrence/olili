<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_tutor_student_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('tutor_profiles')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('student_profiles')->onDelete('cascade');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->json('goals')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate relationships
            $table->unique(['tutor_id', 'student_id']);

            // Indexes
            $table->index('status');
            $table->index('enrolled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_student');
    }
};
