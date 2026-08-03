<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_links', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();          // obscure slug in the pay URL
            $table->string('client_name', 150);
            $table->string('email', 150)->nullable();        // where Victoria wants a copy sent (optional)
            $table->decimal('amount', 10, 2);                // one-time charge amount
            $table->string('note', 255)->nullable();         // optional memo shown on the pay page
            $table->string('status', 20)->default('unpaid'); // unpaid | paid | void

            // Filled in once the client pays.
            $table->string('invoice_number', 64)->nullable();
            $table->string('transaction_id', 64)->nullable();
            $table->string('auth_code', 32)->nullable();
            $table->string('payer_email', 150)->nullable();  // the email the payer actually entered
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_links');
    }
};
