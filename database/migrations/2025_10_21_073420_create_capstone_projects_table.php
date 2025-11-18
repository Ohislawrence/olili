<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_capstone_projects_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('capstone_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('requirements'); // Project requirements
            $table->json('evaluation_criteria'); // AI grading criteria
            $table->date('due_date')->nullable();
            $table->enum('status', ['assigned', 'in_progress', 'submitted', 'graded'])->default('assigned');
            $table->text('student_submission')->nullable();
            $table->json('submission_files')->nullable(); // File paths or URLs
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('ai_score', 5, 2)->nullable();
            $table->decimal('tutor_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->json('ai_feedback')->nullable();
            $table->text('tutor_feedback')->nullable();
            $table->string('ai_model_used')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('capstone_projects');
    }
};
