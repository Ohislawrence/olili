<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_quiz_attempts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->integer('attempt_number')->default(0);
            $table->json('answers')->nullable(); // Student's answers
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->boolean('is_passed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_taken_seconds')->nullable();
            $table->json('ai_feedback')->nullable(); // AI-generated feedback
            $table->text('manual_feedback')->nullable(); // Tutor manual feedback
            $table->timestamps();

            $table->unique(['user_id', 'quiz_id', 'attempt_number']);
            $table->index(['user_id', 'quiz_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
