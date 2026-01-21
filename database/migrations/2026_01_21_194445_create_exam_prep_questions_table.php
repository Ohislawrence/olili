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
        Schema::create('exam_prep_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_prep_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_outline_id')->nullable()->constrained()->onDelete('set null');

            // Question details from quiz
            $table->text('question_text');
            $table->json('options')->nullable();
            $table->string('correct_answer');
            $table->string('question_type')->default('multiple_choice');
            $table->integer('points')->default(1);
            $table->string('difficulty')->default('medium');
            $table->json('metadata')->nullable();

            // Usage tracking
            $table->integer('times_used')->default(0);
            $table->integer('times_correct')->default(0);
            $table->integer('times_incorrect')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['exam_prep_id', 'question_type']);
            $table->index('difficulty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_prep_questions');
    }
};
