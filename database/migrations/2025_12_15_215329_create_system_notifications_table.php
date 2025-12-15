<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info');
            $table->boolean('send_email')->default(false);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('user_selection')->default('all'); // all, roles, specific
            $table->json('user_ids')->nullable(); // For specific users
            $table->json('role_names')->nullable(); // For role-based selection
            $table->string('status')->default('draft');
            $table->json('data')->nullable();
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('system_notifications')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('channel')->default('database');
            $table->string('status')->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['notification_id', 'user_id']);
            $table->index(['status', 'channel']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('system_notifications');
    }
};
