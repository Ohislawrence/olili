<?php
// database/migrations/2024_01_01_create_flashcards_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('flashcard_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->string('study_mode')->default('spaced_repetition');
            $table->timestamps();
        });

        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_outline_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('flashcard_set_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->text('answer');
            $table->text('explanation')->nullable();
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium');
            $table->boolean('is_public')->default(false);
            $table->integer('study_interval')->default(0);
            $table->date('next_review_date')->nullable();
            $table->timestamp('last_studied_at')->nullable();
            $table->decimal('ease_factor', 3, 2)->default(2.5);
            $table->integer('repetitions')->default(0);
            $table->integer('interval')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'next_review_date']);
            $table->index(['flashcard_set_id', 'next_review_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('flashcards');
        Schema::dropIfExists('flashcard_sets');
    }
};
