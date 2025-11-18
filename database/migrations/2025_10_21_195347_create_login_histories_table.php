<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_login_histories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('login_type')->default('password'); // password, socialite, magic_link
            $table->boolean('was_successful')->default(true);
            $table->text('failure_reason')->nullable(); // Invalid credentials, expired token, etc.
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->integer('session_duration_seconds')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'login_at']);
            $table->index(['ip_address']);
            $table->index(['was_successful']);
            $table->index(['login_type']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('last_login_ip')->nullable();
            $table->integer('login_count')->default(0);
            $table->integer('consecutive_login_days')->default(0);
            $table->timestamp('last_failed_login_at')->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('account_locked_until')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_histories');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_ip',
                'login_count',
                'consecutive_login_days',
                'last_failed_login_at',
                'failed_login_attempts',
                'account_locked_until'
            ]);
        });
    }
};
