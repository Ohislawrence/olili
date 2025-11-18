<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_progress_tracking_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('progress_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_outline_id')->constrained()->onDelete('cascade');
            $table->enum('activity_type', ['content_view', 'quiz_attempt', 'project_submission', 'chat_interaction']);
            $table->decimal('time_spent_minutes', 8, 2)->default(0);
            $table->boolean('is_completed')->default(false);
            $table->decimal('completion_score', 5, 2)->nullable();
            $table->json('performance_metrics')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_outline_id', 'activity_type']);
            $table->index(['user_id', 'course_id']);
            $table->index(['user_id', 'is_completed']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('progress_tracking');
    }
};
