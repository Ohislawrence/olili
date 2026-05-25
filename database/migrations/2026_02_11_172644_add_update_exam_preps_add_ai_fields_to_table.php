<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_exam_preps_add_ai_fields.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exam_preps', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_preps', 'content_generation_status')) {
                $table->string('content_generation_status')->nullable()->after('question_distribution');
            }
            if (!Schema::hasColumn('exam_preps', 'content_generation_job_id')) {
                $table->string('content_generation_job_id')->nullable();
            }
            if (!Schema::hasColumn('exam_preps', 'content_generation_started_at')) {
                $table->timestamp('content_generation_started_at')->nullable();
            }
            if (!Schema::hasColumn('exam_preps', 'content_generation_completed_at')) {
                $table->timestamp('content_generation_completed_at')->nullable();
            }
            if (!Schema::hasColumn('exam_preps', 'ai_model_used')) {
                $table->string('ai_model_used')->nullable();
            }
            if (!Schema::hasColumn('exam_preps', 'generation_parameters')) {
                $table->json('generation_parameters')->nullable();
            }
            if (!Schema::hasColumn('exam_preps', 'generation_summary')) {
                $table->json('generation_summary')->nullable();
            }
            if (!Schema::hasColumn('exam_preps', 'enrolled_count')) {
                $table->integer('enrolled_count')->default(0)->after('is_public');
            }
        });

        Schema::table('exam_prep_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_prep_questions', 'explanation')) {
                $table->text('explanation')->nullable()->after('correct_answer');
            }
            if (!Schema::hasColumn('exam_prep_questions', 'order')) {
                $table->integer('order')->default(0)->after('difficulty');
            }
            if (!Schema::hasColumn('exam_prep_questions', 'metadata')) {
                $table->json('metadata')->nullable()->after('order');
            }
            if (!Schema::hasColumn('exam_prep_questions', 'times_used')) {
                $table->integer('times_used')->default(0)->after('metadata');
            }
            if (!Schema::hasColumn('exam_prep_questions', 'times_correct')) {
                $table->integer('times_correct')->default(0)->after('times_used');
            }
            if (!Schema::hasColumn('exam_prep_questions', 'times_incorrect')) {
                $table->integer('times_incorrect')->default(0)->after('times_correct');
            }
        });
    }

    public function down()
    {
        Schema::table('exam_preps', function (Blueprint $table) {
            $table->dropColumn([
                'content_generation_status',
                'content_generation_job_id',
                'content_generation_started_at',
                'content_generation_completed_at',
                'ai_model_used',
                'generation_parameters',
                'generation_summary',
                'enrolled_count'
            ]);
        });

        Schema::table('exam_prep_questions', function (Blueprint $table) {
            $table->dropColumn([
                'explanation',
                'order',
                'metadata',
                'times_used',
                'times_correct',
                'times_incorrect'
            ]);
        });
    }
};
