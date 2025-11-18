<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_ai_usage_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_provider_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('model_used');
            $table->string('endpoint'); // chat, completion, etc.
            $table->string('purpose'); // course_generation, content_creation, chat, quiz_generation, etc.
            $table->integer('prompt_tokens')->default(0);
            $table->integer('completion_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->decimal('cost', 10, 8)->default(0);
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->json('request_metadata')->nullable();
            $table->json('response_metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['ai_provider_id', 'created_at']);
            $table->index(['purpose', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
