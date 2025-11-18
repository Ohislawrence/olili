<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_chat_messages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->onDelete('cascade');
            $table->enum('sender_type', ['user', 'ai', 'tutor']);
            $table->text('message');
            $table->json('metadata')->nullable(); // Additional message metadata
            $table->string('ai_model_used')->nullable();
            $table->integer('token_count')->default(0);
            $table->decimal('generation_cost', 8, 6)->default(0);
            $table->boolean('is_related_to_topic')->default(true); // Flag if AI detected off-topic
            $table->timestamps();

            $table->index(['chat_session_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};
