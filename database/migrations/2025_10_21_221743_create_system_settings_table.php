<?php
// database/migrations/2024_01_01_000000_create_system_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->json('value')->nullable();
            $table->timestamps();

            $table->index('key');
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};
