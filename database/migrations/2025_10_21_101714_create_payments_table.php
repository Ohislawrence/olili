<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('NGN');
            $table->string('status')->default('pending'); // pending, success, failed, abandoned
            $table->string('gateway')->default('paystack');
            $table->json('metadata')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['reference']);
            $table->index(['created_at']);
        });

        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('NGN');
            $table->integer('billing_cycle_days')->default(30); // 30 days for monthly
            $table->json('features')->nullable();
            $table->integer('max_courses')->default(3);
            $table->integer('max_ai_requests_per_month')->default(100);
            $table->boolean('ai_grading')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('active'); // active, canceled, expired, pending
            $table->string('paystack_subscription_code')->nullable();
            $table->string('paystack_customer_code')->nullable();
            $table->string('authorization_code')->nullable();
            $table->string('card_type')->nullable();
            $table->string('last4')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['paystack_subscription_code']);
            $table->index(['ends_at']);
        });

        Schema::create('payment_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->string('paystack_reference')->nullable();
            $table->json('payload');
            $table->string('status')->default('processed'); // processed, failed, pending
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'paystack_reference']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_webhook_logs');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('payments');
    }
};
