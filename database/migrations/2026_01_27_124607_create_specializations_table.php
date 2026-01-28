<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_specializations_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Specializations table
        Schema::create('specializations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            $table->string('target_exam')->nullable();
            $table->string('target_career')->nullable();
            $table->string('level')->default('secondary');
            $table->integer('estimated_duration_hours')->default(0);
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->boolean('has_discount')->default(false);
            $table->json('learning_objectives')->nullable();
            $table->json('prerequisites')->nullable();
            $table->json('target_skills')->nullable();
            $table->json('career_opportunities')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);
            $table->string('status')->default('draft');
            $table->integer('enrollment_count')->default(0);
            $table->integer('completion_count')->default(0);
            $table->integer('min_electives')->default(0);
            $table->integer('max_electives')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();

            // Fix: Make nullable and use CASCADE instead of SET NULL
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });

        // Specialization courses pivot table
        Schema::create('specialization_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialization_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->string('category')->nullable();
            $table->integer('recommended_weeks')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['specialization_id', 'course_id']);
            $table->index(['specialization_id', 'order']);
        });

        // Specialization enrollments
        Schema::create('specialization_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Make student_profile_id nullable
            $table->foreignId('student_profile_id')
                ->nullable()
                ->constrained('student_profiles')
                ->onDelete('set null');

            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->json('completed_courses')->nullable();
            $table->json('selected_electives')->nullable();
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('enrolled');
            $table->timestamps();

            $table->unique(['specialization_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['specialization_id', 'status']);
        });

        // Specialization exam resources
        Schema::create('specialization_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialization_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('type')->default('resource');
            $table->text('description')->nullable();
            $table->string('file_url')->nullable();
            $table->string('external_url')->nullable();
            $table->integer('download_count')->default(0);
            $table->integer('order')->default(0);
            $table->boolean('is_free')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Specialization tags
        Schema::create('specialization_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialization_id')->constrained()->onDelete('cascade');
            $table->string('tag');
            $table->timestamps();

            $table->index(['tag', 'specialization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialization_tags');
        Schema::dropIfExists('specialization_resources');
        Schema::dropIfExists('specialization_enrollments');
        Schema::dropIfExists('specialization_courses');
        Schema::dropIfExists('specializations');
    }
};
