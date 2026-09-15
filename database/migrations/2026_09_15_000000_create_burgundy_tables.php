<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Burgundy Clients — the credit-repair clients serviced with Burgundy (backend
 * partner) on a 50/50 net-profit split. Kept completely separate from the
 * onboarding_submissions ("Paid Credit Repair Clients") pipeline.
 *
 * Revenue is NOT duplicated: a client can point at an existing subscription
 * (payments table) and payment links are referenced by id. burgundy_payments
 * only holds money that has no other home (off-platform / prior payments) plus
 * a pointer row for each payment link sent from the Burgundy page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('burgundy_clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();

            // invited | signed_up | payment_pending | paid | active | paused | cancelled
            $table->string('status', 20)->default('invited');
            // transitioned (existing Burgundy client moved in) | new
            $table->string('source', 20)->default('new');

            // Existing records this client's revenue comes from (no FK — the
            // referenced tables are managed by other features).
            $table->unsignedBigInteger('subscription_id')->nullable();

            $table->decimal('monthly_fee', 10, 2)->nullable();
            $table->unsignedInteger('current_round')->default(0);

            $table->timestamp('invited_at')->nullable();
            $table->date('signed_up_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();

            $table->string('next_action', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('status');
            $table->index('subscription_id');
            $table->index('last_activity_at');
        });

        // One row per dispute round Burgundy processes — each costs the backend fee.
        Schema::create('burgundy_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('burgundy_client_id')->constrained('burgundy_clients')->cascadeOnDelete();
            $table->unsignedInteger('round_number');
            $table->timestamp('processed_at');
            $table->decimal('cost', 10, 2)->default(15);
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index('processed_at');
        });

        Schema::create('burgundy_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('burgundy_client_id')->constrained('burgundy_clients')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('status', 20)->default('paid');          // pending | paid | void
            $table->string('method', 40)->nullable();               // Zelle, Cash App, Payment link, …
            $table->unsignedBigInteger('payment_link_id')->nullable();
            // false = paid before the transition; shows in "Amount Paid" but is
            // excluded from the profit split.
            $table->boolean('counts_toward_profit')->default(true);
            $table->timestamp('paid_at')->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('paid_at');
            $table->unique('payment_link_id');
        });

        // Other expenses — only "approved" ones reduce net profit.
        Schema::create('burgundy_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description', 150);
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->string('status', 20)->default('pending');       // pending | approved | rejected
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('expense_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('burgundy_expenses');
        Schema::dropIfExists('burgundy_payments');
        Schema::dropIfExists('burgundy_rounds');
        Schema::dropIfExists('burgundy_clients');
    }
};
