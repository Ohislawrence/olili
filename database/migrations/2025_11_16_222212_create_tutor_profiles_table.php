<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_tutor_profiles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('set null');
            $table->string('qualification')->nullable();
            $table->text('bio')->nullable();
            $table->text('specialties')->nullable(); // JSON array of specialties
            $table->text('teaching_style')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->string('timezone')->nullable();
            $table->json('availability')->nullable(); // JSON schedule
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_online')->default(false);
            $table->integer('max_students')->default(10);
            $table->json('preferences')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('cover_image')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('completed_sessions')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['is_verified', 'is_online']);
            $table->index('rating');
            $table->index('specialties');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_profiles');
    }
};
