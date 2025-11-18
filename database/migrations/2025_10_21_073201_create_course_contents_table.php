<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_course_contents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_outline_id')->constrained()->onDelete('cascade');
            $table->text('content'); // AI-generated content
            $table->enum('content_type', ['text', 'video', 'interactive', 'reading', 'exercise']);
            $table->integer('order')->default(0);
            $table->json('metadata')->nullable(); // Additional metadata for the content
            $table->string('ai_model_used')->nullable();
            $table->json('generation_prompt')->nullable(); // The prompt used to generate this content
            $table->integer('token_count')->default(0);
            $table->decimal('generation_cost', 8, 6)->default(0); // Cost to generate this content
            $table->timestamps();

            $table->index(['course_outline_id', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_contents');
    }
};
