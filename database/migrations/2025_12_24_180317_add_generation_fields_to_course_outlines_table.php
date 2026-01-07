<?php
// database/migrations/2024_01_15_000002_add_generation_fields_to_course_outlines_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_outlines', function (Blueprint $table) {
            $table->boolean('needs_content_generation')->default(false)->after('has_project');
            $table->timestamp('content_generated_at')->nullable()->after('needs_content_generation');
            $table->boolean('needs_quiz_generation')->default(false)->after('content_generated_at');
            $table->timestamp('quiz_generated_at')->nullable()->after('needs_quiz_generation');

            // Index for performance
            $table->index('needs_content_generation');
            $table->index('needs_quiz_generation');
        });
    }

    public function down(): void
    {
        Schema::table('course_outlines', function (Blueprint $table) {
            $table->dropIndex(['needs_content_generation']);
            $table->dropIndex(['needs_quiz_generation']);

            $table->dropColumn([
                'needs_content_generation',
                'content_generated_at',
                'needs_quiz_generation',
                'quiz_generated_at',
            ]);
        });
    }
};
