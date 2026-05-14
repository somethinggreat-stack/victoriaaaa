<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('middlename')->nullable();
            $table->string('suffix')->nullable();
            $table->string('email');
            $table->string('phone', 20);
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip', 10)->nullable();
            // Full 9-digit SSN, encrypted at rest.
            $table->text('ssn_encrypted');
            $table->string('ssn_last4', 4);
            $table->date('birth_date');
            // Sync status with Credit Repair Cloud
            $table->string('crc_status')->default('pending');   // pending, sent, failed
            $table->string('crc_id')->nullable();               // CRC record id returned
            $table->text('crc_response')->nullable();           // raw response for debugging
            $table->string('status')->default('new');           // new, in_progress, active, archived
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_submissions');
    }
};
