<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_prep_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_prep_id')->constrained()->onDelete('cascade');

            // Attempt details
            $table->integer('attempt_number')->default(1);
            $table->json('questions'); // Store selected questions for this attempt
            $table->json('answers')->nullable(); // User's answers
            $table->integer('score')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->boolean('is_passed')->default(false);
            $table->integer('time_spent_seconds')->default(0);

            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Results details
            $table->json('results_breakdown')->nullable();
            $table->text('ai_feedback')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'exam_prep_id']);
            $table->index(['exam_prep_id', 'is_passed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_prep_attempts');
    }
};
