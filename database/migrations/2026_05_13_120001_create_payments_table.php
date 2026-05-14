<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();

            $table->string('transaction_id', 64)->nullable();
            $table->string('invoice_number', 64)->nullable();
            $table->decimal('amount', 10, 2)->default(0);

            // type:   initial | recurring | refund | void
            $table->string('type', 20);
            // status: captured | refunded | voided | failed
            $table->string('status', 20)->default('captured');

            $table->string('event_type_raw', 80)->nullable();
            $table->timestamp('charged_at')->nullable();
            $table->json('raw_payload')->nullable();

            $table->timestamps();

            $table->unique(['transaction_id', 'type'], 'payments_txn_type_unique');
            $table->index('invoice_number');
            $table->index('charged_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
