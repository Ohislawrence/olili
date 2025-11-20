<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_course_outlines_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_outlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->enum('type', ['module', 'topic', 'lesson', 'quiz', 'project']);
            $table->integer('estimated_duration_minutes')->default(0);
            $table->json('learning_objectives')->nullable();
            $table->json('key_concepts')->nullable();
            $table->json('resources')->nullable(); // Recommended resources
            $table->boolean('has_quiz')->default(false);
            $table->boolean('has_project')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('course_outlines');
    }
};
