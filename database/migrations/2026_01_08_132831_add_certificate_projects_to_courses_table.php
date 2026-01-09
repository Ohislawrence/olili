<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_certificate_projects_to_courses_table.php
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('has_certificate')->default(false);
            $table->boolean('has_projects')->default(false);
            $table->json('tags')->nullable();
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['has_certificate', 'has_projects', 'tags']);
        });
    }
};
