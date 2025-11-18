<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_student_profiles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_board_id')->nullable()->constrained()->onDelete('set null');
            $table->string('current_level'); // beginner, intermediate, advanced
            $table->string('target_level'); // beginner, intermediate, advanced, expert
            $table->date('target_completion_date');
            $table->json('learning_goals')->nullable(); // Specific goals as JSON
            $table->json('learning_preferences')->nullable(); // visual, auditory, reading/writing, kinesthetic
            $table->integer('weekly_study_hours')->default(5);
            $table->decimal('current_grade', 3, 1)->nullable(); // Current grade if applicable
            $table->decimal('target_grade', 3, 1)->nullable(); // Target grade if applicable
            $table->timestamps();

            $table->index(['user_id', 'exam_board_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_profiles');
    }
};
