<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Affiliate program: someone refers a client, the client pays, the affiliate
 * is owed a flat fee (default $100) straight away. A referral row is created at
 * the moment of the client's first successful payment, so "owed" is always a
 * real, already-paid sale.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('code', 30)->unique();
            $table->decimal('payout_amount', 10, 2)->default(100);
            $table->boolean('active')->default(true);
            $table->string('notes', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_id')->index();
            $table->unsignedBigInteger('subscription_id')->nullable()->unique();
            $table->string('client_name', 150)->nullable();
            $table->string('client_email', 150)->nullable();
            $table->string('plan_label', 150)->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('status', 10)->default('owed')->index(); // owed | paid
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_referrals');
        Schema::dropIfExists('affiliates');
    }
};
