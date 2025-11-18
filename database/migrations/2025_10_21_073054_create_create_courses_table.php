<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_courses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_board_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('subject');
            $table->text('description')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert']);
            $table->enum('status', ['draft', 'active', 'completed', 'paused'])->default('draft');
            $table->date('start_date')->nullable();
            $table->date('target_completion_date');
            $table->date('actual_completion_date')->nullable();
            $table->integer('estimated_duration_hours')->default(0);
            $table->integer('actual_duration_hours')->default(0);
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->json('learning_objectives')->nullable();
            $table->json('prerequisites')->nullable();
            $table->string('ai_model_used')->nullable(); // Which AI model generated this course
            $table->json('generation_parameters')->nullable(); // Parameters used for AI generation
            $table->timestamps();

            $table->index(['student_profile_id', 'status']);
            $table->index(['subject', 'level']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
};
