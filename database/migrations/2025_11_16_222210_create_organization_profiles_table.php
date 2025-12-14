<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_organization_profiles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->integer('founded_year')->nullable();
            $table->integer('total_students')->default(0);
            $table->integer('total_tutors')->default(0);
            $table->integer('max_students')->default(100);
            $table->integer('max_tutors')->default(10);
            $table->json('settings')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['is_verified', 'is_active']);
            $table->index('slug');
            $table->index('country');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_profiles');
    }
};
