<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Payment links used to be Victoria's alone. Burgundy now creates her own from
 * the partnership dashboard, so a link records whose it is and which of her
 * clients it was made for — that is what keeps her links, her contract and her
 * client record separate from Victoria's.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_links', function (Blueprint $table) {
            $table->string('partner', 20)->default('victoria')->after('token')->index();
            $table->unsignedBigInteger('burgundy_client_id')->nullable()->after('partner')->index();
        });
    }

    public function down(): void
    {
        Schema::table('payment_links', function (Blueprint $table) {
            $table->dropIndex(['partner']);
            $table->dropIndex(['burgundy_client_id']);
            $table->dropColumn(['partner', 'burgundy_client_id']);
        });
    }
};
