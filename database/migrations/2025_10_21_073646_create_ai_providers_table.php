<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_ai_providers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // OpenAI, DeepSeek, Anthropic, etc.
            $table->string('code')->unique(); // openai, deepseek, anthropic
            $table->json('available_models'); // Available models from this provider
            $table->string('api_key')->nullable();
            $table->string('base_url')->nullable();
            $table->decimal('cost_per_token', 10, 8)->default(0);
            $table->integer('max_tokens_per_request')->default(4000);
            $table->integer('rate_limit_per_minute')->default(60);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('config')->nullable(); // Additional configuration
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_providers');
    }
};
