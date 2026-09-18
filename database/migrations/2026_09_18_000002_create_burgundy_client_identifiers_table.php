<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Every email / phone a Burgundy client is known by, normalized, one row each.
 *
 * The $100 checkout link is shared, so a payment has to be traced back to a
 * client after the fact. A client can legitimately hold several addresses
 * (two of the seeded legacy records already do), which a single email column
 * on burgundy_clients cannot express. Matching is therefore one indexed
 * lookup here rather than a scan across columns.
 *
 * When a human resolves a review by merging a payment into an existing client,
 * the payer's email/phone is added here — so the next charge auto-matches.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('burgundy_client_identifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('burgundy_client_id')->constrained('burgundy_clients')->cascadeOnDelete();

            $table->string('type', 10);    // email | phone
            $table->string('value', 150);  // normalized: email lowercased/trimmed, phone digits-only

            $table->timestamps();

            // One owner per identifier. A collision means two clients share an
            // email or phone, which is exactly the ambiguity the review queue exists for.
            $table->unique(['type', 'value']);
            $table->index('burgundy_client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('burgundy_client_identifiers');
    }
};
