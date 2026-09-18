<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The spine of the Burgundy × Victoria partnership pipeline.
 *
 * Deliberately stores NO payment state. `subscription_id` points at the
 * existing subscriptions table and the dashboard reads amount / status /
 * next billing / refunds live through it. The Authorize.Net webhook already
 * maintains those values, so copying them here would create a second source
 * of truth that drifts the moment a webhook lands.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('burgundy_clients', function (Blueprint $table) {
            $table->id();

            // ── Identity ──
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('zip', 10)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('ssn_last4', 4)->nullable();

            // ── Provenance & matching ──
            $table->string('source', 20)->default('legacy');        // legacy | new
            $table->string('match_status', 20)->default('matched'); // matched | needs_review
            $table->string('matched_on', 20)->nullable();           // seed|email|phone|name|manual

            // ── Pipeline (humans set these; payment/onboarding set themselves) ──
            $table->string('contact_status', 30)->default('not_contacted');
            // not_contacted | contacted | interested | not_interested | payment_link_sent
            $table->string('onboarding_status', 20)->default('pending'); // pending | complete
            $table->string('client_status', 20)->default('prospect');    // prospect | active | cancelled

            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('interested_at')->nullable();
            $table->timestamp('link_sent_at')->nullable();
            $table->text('notes')->nullable();

            // ── Links out (plain indexed columns, not FKs — the referenced rows
            //    outlive and are managed independently of this table) ──
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('onboarding_submission_id')->nullable();
            $table->unsignedBigInteger('payment_agreement_id')->nullable();

            // ── Apex delivery ──
            $table->string('apex_status', 20)->nullable(); // pending | sent | failed
            $table->string('apex_id', 64)->nullable();

            // Seeded duplicate pairs point at each other until a human decides.
            $table->unsignedBigInteger('possible_duplicate_of')->nullable();

            $table->timestamps();

            $table->index('subscription_id');
            $table->index('onboarding_submission_id');
            $table->index('possible_duplicate_of');
            $table->index('contact_status');
            $table->index('client_status');
            $table->index('match_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('burgundy_clients');
    }
};
