<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Remove student_profile_id from courses
            //$table->dropForeign(['student_profile_id']);
            //$table->dropColumn('student_profile_id');

            // Remove student-specific fields
            //$table->dropColumn('progress_percentage');
            //$table->dropColumn('actual_completion_date');
            //$table->dropColumn('actual_duration_hours');

            // Add admin-specific fields
            //$table->string('code')->unique()->after('id'); // Course code like "CS101"
            //$table->text('syllabus')->nullable()->after('description');
            //$table->decimal('price', 10, 2)->default(0)->after('enrollment_limit');
            //$table->boolean('is_paid')->default(false)->after('price');
            //$table->json('tags')->nullable()->after('level');

            // Set defaults
            //$table->boolean('is_public')->default(true)->change();
            //$table->string('created_by')->default('admin')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
