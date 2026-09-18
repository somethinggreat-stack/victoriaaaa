<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Which partner an onboarding submission belongs to. Victoria's funnel and
 * Burgundy's funnel post to the SAME Apex endpoint with DIFFERENT intake keys,
 * so a queued retry must remember whose key to re-send under — otherwise a
 * retried Burgundy client would land in Victoria's Apex dashboard.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->string('partner', 20)->default('victoria')->index();
        });

        Schema::table('apex_retry_jobs', function (Blueprint $table) {
            $table->string('partner', 20)->default('victoria')->index();
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->dropIndex(['partner']);
            $table->dropColumn('partner');
        });

        Schema::table('apex_retry_jobs', function (Blueprint $table) {
            $table->dropIndex(['partner']);
            $table->dropColumn('partner');
        });
    }
};
