<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * payment_agreements could only describe a mentorship instalment plan signed
 * before checkout, and only linked to a subscription. Every other way of taking
 * money — the credit-repair plans, the dashboard payment links — collected
 * payment with no signed agreement at all.
 *
 * This generalises the table so one agreement record can describe any sale, and
 * so an agreement can exist in a 'pending' state between the card being charged
 * and the client actually signing. That gap is the thing worth tracking: money
 * taken with nothing signed is worse than no contract at all, because everyone
 * assumes a contract exists.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_agreements', function (Blueprint $table) {
            // What was sold, and where it lives.
            $table->string('source', 30)->default('subscription')->after('id'); // subscription | payment_link
            $table->unsignedBigInteger('source_id')->nullable()->after('source');
            $table->string('partner', 20)->default('victoria')->after('source_id');

            // pending = charged but not yet signed. signed = done.
            $table->string('status', 20)->default('signed')->after('partner');

            // Free text for ad-hoc sales, where no plan describes the work.
            $table->string('service_description', 500)->nullable()->after('plan_label');

            // Where to send the client once they have signed.
            $table->string('next_url', 255)->nullable()->after('terms_version');

            // Who we believe is signing, before they type it themselves.
            $table->string('client_name', 150)->nullable()->after('next_url');
            $table->string('client_phone', 30)->nullable()->after('client_name');

            $table->index(['source', 'source_id']);
            $table->index('status');
            $table->index('partner');
        });

        // full_name is deliberately left NOT NULL. Modifying an existing column
        // on a live table is the riskiest thing a migration can do, and it buys
        // nothing here: a pending agreement stores an empty string, and `status`
        // is what says whether it has been signed.
    }

    public function down(): void
    {
        Schema::table('payment_agreements', function (Blueprint $table) {
            $table->dropIndex(['source', 'source_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['partner']);
            $table->dropColumn([
                'source', 'source_id', 'partner', 'status',
                'service_description', 'next_url', 'client_name', 'client_phone',
            ]);
        });
    }
};
