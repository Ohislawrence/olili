<?php
// database/migrations/xxxx_xx_xx_create_exam_prep_enrollments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_prep_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_prep_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->enum('status', ['enrolled', 'active', 'completed', 'dropped'])->default('enrolled');
            $table->decimal('best_score', 5, 2)->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate enrollments
            $table->unique(['exam_prep_id', 'user_id']);

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['exam_prep_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_prep_enrollments');
    }
};
