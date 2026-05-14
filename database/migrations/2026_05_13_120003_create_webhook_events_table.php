<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('notification_id', 64)->nullable()->unique();
            $table->string('event_type', 80);
            $table->string('entity_id', 64)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('invoice_number', 64)->nullable();
            $table->string('arb_status', 32)->nullable();
            $table->string('response_code', 8)->nullable();
            $table->boolean('signature_valid')->nullable();
            $table->ipAddress('source_ip')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->json('payload')->nullable();

            // Denormalized customer snapshot for dashboards
            $table->unsignedBigInteger('matched_subscription_id')->nullable();
            $table->string('customer_first_name', 100)->nullable();
            $table->string('customer_last_name', 100)->nullable();
            $table->string('customer_email', 150)->nullable();
            $table->string('description', 500)->nullable();

            $table->timestamps();

            $table->index('event_type');
            $table->index('invoice_number');
            $table->index('matched_subscription_id');
            $table->index('received_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
