<?php
// database/migrations/2024_01_15_000001_add_generation_fields_to_modules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->boolean('needs_content_generation')->default(false)->after('is_completed');
            $table->timestamp('content_generated_at')->nullable()->after('needs_content_generation');

            $table->index('needs_content_generation');
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropIndex(['needs_content_generation']);

            $table->dropColumn([
                'needs_content_generation',
                'content_generated_at',
            ]);
        });
    }
};
