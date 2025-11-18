<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_quizzes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_outline_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('time_limit_minutes')->nullable(); // NULL means no time limit
            $table->integer('max_attempts')->default(1);
            $table->decimal('passing_score', 5, 2)->default(70.00);
            $table->boolean('is_active')->default(true);
            $table->json('questions'); // AI-generated questions in JSON format
            $table->string('ai_model_used')->nullable();
            $table->timestamps();

            $table->index(['course_outline_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('quizzes');
    }
};
