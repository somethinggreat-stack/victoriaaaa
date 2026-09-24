<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Some agreements cover two people — a couples plan is one document, one price,
 * and both clients bound by it. Previously an agreement held a single
 * signature, so a joint plan could only ever bind whoever happened to sign.
 *
 * The second signature lives alongside the first rather than in a second
 * agreement, because raising two full-price contracts for one couple would
 * imply each of them owes the whole amount.
 *
 * Either party may sign first, so the agreement sits at 'partial' until both
 * have — the same link works for whoever is left.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_agreements', function (Blueprint $table) {
            $table->boolean('requires_cosigner')->default(false)->after('client_phone');
            // Who we expect to sign, shown on the form before they type it.
            $table->string('cosigner_name', 150)->nullable()->after('requires_cosigner');
            $table->string('cosigner_email', 150)->nullable()->after('cosigner_name');

            // Filled in when the second person actually signs.
            $table->string('cosigner_full_name', 150)->nullable()->after('cosigner_email');
            $table->longText('cosigner_signature_data')->nullable()->after('cosigner_full_name');
            $table->string('cosigner_ip_address', 45)->nullable()->after('cosigner_signature_data');
            $table->string('cosigner_user_agent', 512)->nullable()->after('cosigner_ip_address');
            $table->timestamp('cosigner_signed_at')->nullable()->after('cosigner_user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('payment_agreements', function (Blueprint $table) {
            $table->dropColumn([
                'requires_cosigner', 'cosigner_name', 'cosigner_email',
                'cosigner_full_name', 'cosigner_signature_data',
                'cosigner_ip_address', 'cosigner_user_agent', 'cosigner_signed_at',
            ]);
        });
    }
};
