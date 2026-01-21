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
        Schema::create('exam_preps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Relationships
            $table->foreignId('exam_board_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');

            // Exam configuration
            $table->integer('total_questions')->default(50);
            $table->integer('time_limit_minutes')->default(60);
            $table->integer('passing_score')->default(70);
            $table->integer('max_attempts')->default(0); // 0 = unlimited
            $table->boolean('randomize_questions')->default(true);
            $table->boolean('allow_pause')->default(false);
            $table->boolean('show_results_immediately')->default(true);

            // Question selection criteria
            $table->json('question_criteria')->nullable(); // For filtering questions
            $table->json('question_distribution')->nullable(); // Topic-wise distribution

            // Status and metadata
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->boolean('is_public')->default(false);
            $table->integer('enrolled_count')->default(0);
            $table->integer('completed_count')->default(0);
            $table->decimal('average_score', 5, 2)->default(0);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['exam_board_id', 'subject_id']);
            $table->index('status');
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_preps');
    }
};
