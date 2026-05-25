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
            $table->string('content_generation_status')->nullable()->after('question_distribution');
            $table->string('content_generation_job_id')->nullable();
            $table->timestamp('content_generation_started_at')->nullable();
            $table->timestamp('content_generation_completed_at')->nullable();
            $table->string('ai_model_used')->nullable();
            $table->json('generation_parameters')->nullable();
            $table->json('generation_summary')->nullable();
            $table->integer('enrolled_count')->default(0)->after('is_public');
        });

        Schema::table('exam_prep_questions', function (Blueprint $table) {
            $table->text('explanation')->nullable()->after('correct_answer');
            $table->integer('order')->default(0)->after('difficulty');
            $table->json('metadata')->nullable()->after('order');
            $table->integer('times_used')->default(0)->after('metadata');
            $table->integer('times_correct')->default(0)->after('times_used');
            $table->integer('times_incorrect')->default(0)->after('times_correct');
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
