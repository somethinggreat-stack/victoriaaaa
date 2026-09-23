<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A payment link is an arbitrary amount with an optional memo, so nothing in it
 * describes the work being bought. The contract the client signs afterwards has
 * to say what they are paying for, so the person creating the link states it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_links', function (Blueprint $table) {
            $table->string('service_description', 500)->nullable()->after('note');
            // Set when the client signs the agreement that follows payment.
            $table->unsignedBigInteger('payment_agreement_id')->nullable()->after('paid_at')->index();
        });
    }

    public function down(): void
    {
        Schema::table('payment_links', function (Blueprint $table) {
            $table->dropIndex(['payment_agreement_id']);
            $table->dropColumn(['service_description', 'payment_agreement_id']);
        });
    }
};
