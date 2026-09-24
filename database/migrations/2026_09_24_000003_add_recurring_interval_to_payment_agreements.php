<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recurring amounts were assumed to be monthly. The Couples Fast Track bills
 * weekly ($500 to start, then $250/week x 4), and a contract that called that
 * "per month" would overstate the term by four times — precisely the kind of
 * mismatch between the document and the charge that caused a refund before.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_agreements', function (Blueprint $table) {
            $table->string('recurring_interval', 10)->default('month')->after('installment_count');
        });
    }

    public function down(): void
    {
        Schema::table('payment_agreements', function (Blueprint $table) {
            $table->dropColumn('recurring_interval');
        });
    }
};
