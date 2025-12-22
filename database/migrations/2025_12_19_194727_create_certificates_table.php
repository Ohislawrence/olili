<?php
// database/migrations/2024_01_01_000001_create_certificates_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained('organization_profiles')->onDelete('set null');
            $table->string('issued_by_type')->nullable();
            $table->unsignedBigInteger('issued_by_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('certificate_data')->nullable();
            $table->dateTime('issue_date');
            $table->dateTime('expiry_date')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('verification_url')->nullable();
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->integer('download_count')->default(0);
            $table->boolean('is_public')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['issued_by_type', 'issued_by_id']);
            $table->index(['user_id', 'course_id']);
        });

        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('template_type')->default('standard');
            $table->string('background_image')->nullable();
            $table->string('background_color')->default('#ffffff');
            $table->string('text_color')->default('#000000');
            $table->string('accent_color')->default('#10b981');
            $table->string('font_family')->default('Roboto');
            $table->integer('font_size')->default(14);
            $table->json('layout_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->foreignId('organization_id')->nullable()->constrained('organization_profiles')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificate_templates');
        Schema::dropIfExists('certificates');
    }
};