<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_chat_sessions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('course_outline_id')->nullable()->constrained()->onDelete('set null');
            $table->string('topic_context')->nullable(); // Current topic for context restriction
            $table->json('context_parameters')->nullable(); // Parameters to keep chat on topic
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'course_id']);
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_sessions');
    }
};
