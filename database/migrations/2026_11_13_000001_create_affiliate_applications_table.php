<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** People who applied to the affiliate program, with proof they accepted the agreement. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->index();
            $table->string('phone', 30)->nullable();
            $table->string('payout_method', 30);          // zelle | paypal | cashapp | check
            $table->string('payout_details', 200);
            $table->string('status', 10)->default('pending')->index(); // pending | approved | declined
            $table->string('agreement_version', 20);
            $table->timestamp('accepted_at');
            $table->string('accepted_ip', 45)->nullable();
            $table->unsignedBigInteger('affiliate_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_applications');
    }
};
