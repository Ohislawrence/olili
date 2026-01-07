<?php
// database/migrations/2024_01_15_000000_add_generation_fields_to_courses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add new columns for content generation tracking
            $table->boolean('needs_content_generation')->default(false)->after('is_shared');
            $table->timestamp('content_generated_at')->nullable()->after('needs_content_generation');
            $table->string('content_generation_status', 20)->nullable()->after('content_generated_at')->comment('processing, completed, failed, failed_permanently');
            $table->json('content_generation_summary')->nullable()->after('content_generation_status');

            // Add new columns for quiz generation tracking
            $table->timestamp('quiz_generated_at')->nullable()->after('content_generation_summary');
            $table->string('quiz_generation_status', 20)->nullable()->after('quiz_generated_at')->comment('processing, completed, failed, failed_permanently');
            $table->json('quiz_generation_summary')->nullable()->after('quiz_generation_status');

            // Index for performance
            $table->index('needs_content_generation');
            $table->index('content_generation_status');
            $table->index('quiz_generation_status');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['needs_content_generation']);
            $table->dropIndex(['content_generation_status']);
            $table->dropIndex(['quiz_generation_status']);

            $table->dropColumn([
                'needs_content_generation',
                'content_generated_at',
                'content_generation_status',
                'content_generation_summary',
                'quiz_generated_at',
                'quiz_generation_status',
                'quiz_generation_summary',
            ]);
        });
    }
};
