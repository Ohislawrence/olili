<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('created_by', ['admin', 'student'])->default('student')->after('student_profile_id');
            $table->foreignId('created_by_user_id')->nullable()->after('created_by')->constrained('users');
            $table->boolean('is_public')->default(false)->after('created_by_user_id');
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('private')->after('is_public');
            $table->integer('enrollment_limit')->nullable()->after('visibility');
            $table->integer('current_enrollment')->default(0)->after('enrollment_limit');
            $table->foreignId('original_course_id')->nullable()->after('current_enrollment')->constrained('courses')->onDelete('set null');
            $table->text('thumbnail_url')->nullable()->after('description');

            // Add indexes
            $table->index(['is_public', 'visibility']);
            $table->index('created_by');
            $table->index('created_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'created_by',
                'created_by_user_id',
                'is_public',
                'visibility',
                'enrollment_limit',
                'current_enrollment',
                'original_course_id',
                'thumbnail_url',
            ]);
        });
    }
};
