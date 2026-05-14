<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // Customer
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150);
            $table->string('phone', 30)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 10)->nullable();
            $table->string('zip', 20)->nullable();

            // Plan
            $table->string('plan_key', 32);
            $table->string('plan_label', 80);
            $table->decimal('amount', 10, 2);
            $table->decimal('recurring_amount', 10, 2)->nullable();

            // Authorize.Net references
            $table->string('invoice_number', 64)->unique();
            $table->string('transaction_id', 64)->nullable()->index();
            $table->string('auth_code', 32)->nullable();
            $table->string('arb_subscription_id', 64)->nullable()->index();
            $table->string('customer_profile_id', 64)->nullable();
            $table->string('customer_payment_profile_id', 64)->nullable();

            // Lifecycle
            $table->string('referral_code', 50)->nullable()->index();
            $table->string('status', 24)->default('active')->index(); // active, past_due, terminated
            $table->unsignedInteger('failed_payment_count')->default(0);
            $table->timestamp('first_failed_at')->nullable();
            $table->timestamp('grace_period_ends_at')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('next_billing_date')->nullable();
            $table->timestamp('terminated_at')->nullable();

            $table->timestamps();

            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
