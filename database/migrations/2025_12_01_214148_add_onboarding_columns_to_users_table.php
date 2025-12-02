<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->timestamp('onboarding_skipped_at')->nullable();
            $table->timestamp('onboarding_seen_at')->nullable();
            $table->json('onboarding_data')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'onboarding_completed_at',
                'onboarding_skipped_at',
                'onboarding_seen_at',
                'onboarding_data',
            ]);
        });
    }
};