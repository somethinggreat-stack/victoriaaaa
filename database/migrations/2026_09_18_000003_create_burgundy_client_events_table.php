<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit trail for a Burgundy client: status changes, payments, signatures,
 * onboarding, Apex delivery, merges. Rendered as the timeline on the client
 * detail page — and the record of who decided what when a merge is disputed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('burgundy_client_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('burgundy_client_id')->constrained('burgundy_clients')->cascadeOnDelete();

            $table->string('event_type', 40); // contact_status | paid | agreement_signed | onboarded | apex_sent | merged | note
            $table->text('note')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index('created_at');
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('burgundy_client_events');
    }
};
