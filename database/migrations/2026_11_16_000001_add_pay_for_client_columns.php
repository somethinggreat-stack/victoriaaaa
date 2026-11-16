<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * An affiliate can pay a client's full fee up front. The payment link remembers
 * which affiliate it was, and the referral remembers which link earned it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_links', function (Blueprint $table) {
            $table->unsignedBigInteger('affiliate_id')->nullable()->after('burgundy_client_id')->index();
        });

        Schema::table('affiliate_referrals', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_link_id')->nullable()->after('subscription_id')->unique();
        });
    }

    public function down(): void
    {
        Schema::table('payment_links', function (Blueprint $table) {
            $table->dropIndex(['affiliate_id']);
            $table->dropColumn('affiliate_id');
        });

        Schema::table('affiliate_referrals', function (Blueprint $table) {
            $table->dropUnique(['payment_link_id']);
            $table->dropColumn('payment_link_id');
        });
    }
};
