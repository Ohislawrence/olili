<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_exam_boards_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_boards', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // WAEC, NECO, CFA, ICAN, AWS, etc.
            $table->string('code')->unique(); // waec, neco, cfa, etc.
            $table->text('description')->nullable();
            $table->string('country')->nullable();
            $table->json('subjects')->nullable(); // Available subjects for this board
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_boards');
    }
};
