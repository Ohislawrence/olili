<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_generated_contents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneratedContentsTable extends Migration
{
    public function up()
    {
        Schema::create('generated_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_outline_id')->constrained()->onDelete('cascade');
            $table->longText('content');
            $table->string('content_type')->default('text'); // text, video_script, quiz_content, etc.
            $table->timestamp('generated_at')->nullable();
            $table->string('status')->default('generated'); // generated, failed, pending
            $table->integer('word_count')->nullable();
            $table->integer('read_time_minutes')->nullable();
            $table->json('metadata')->nullable(); // Additional metadata like AI model used, tokens, etc.
            $table->timestamps();

            // Indexes for performance
            $table->index(['course_outline_id', 'content_type']);
            $table->index('generated_at');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('generated_contents');
    }
}
