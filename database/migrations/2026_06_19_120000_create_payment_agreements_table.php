<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_agreements', function (Blueprint $table) {
            $table->id();

            // Which plan the contract was signed for
            $table->string('plan_key', 32)->index();
            $table->string('plan_label', 80);

            // Frozen financial terms at the moment of signing
            $table->decimal('deposit_amount', 10, 2);
            $table->decimal('installment_amount', 10, 2)->nullable();
            $table->unsignedSmallInteger('installment_count')->nullable();
            $table->decimal('total_amount', 10, 2);

            // The signature itself
            $table->string('full_name', 150);          // typed full legal name
            $table->longText('signature_data')->nullable(); // drawn signature (base64 PNG data URL)
            $table->longText('contract_text')->nullable();   // snapshot of the exact terms shown
            $table->string('terms_version', 16)->default('v1');

            // Cross-reference (filled in once the client completes checkout)
            $table->string('email', 150)->nullable()->index();
            $table->string('invoice_number', 64)->nullable()->index();
            $table->unsignedBigInteger('subscription_id')->nullable()->index();

            // Audit trail
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('signed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_agreements');
    }
};
