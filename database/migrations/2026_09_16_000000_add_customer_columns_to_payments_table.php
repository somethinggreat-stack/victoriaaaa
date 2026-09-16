<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A payment only showed a customer when it belonged to a subscription, so
 * payment-link charges, eBook sales and anything charged directly inside
 * Authorize.Net displayed as "Unlinked". Store who paid on the row itself.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('customer_name', 150)->nullable()->after('subscription_id');
            $table->string('customer_email', 150)->nullable()->after('customer_name');
            // subscription | payment_link | ebook | gateway
            $table->string('source', 30)->nullable()->after('customer_email');

            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['customer_email']);
            $table->dropColumn(['customer_name', 'customer_email', 'source']);
        });
    }
};
