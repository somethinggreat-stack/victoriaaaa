<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apex_retry_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('onboarding_submission_id')->nullable();

            // For the admin list (non-sensitive).
            $table->string('client_name', 200)->nullable();
            $table->string('email', 150)->nullable();

            // Encrypted JSON of the funnel payload (text fields incl. SSN + CM
            // password) plus the computed date_of_birth. Needed to re-post to Apex.
            $table->text('payload_encrypted');

            // Private-disk paths to the uploaded documents (kept only until a
            // retry succeeds, then purged).
            $table->string('drivers_license_path', 255)->nullable();
            $table->string('proof_of_address_path', 255)->nullable();
            $table->string('ssn_card_path', 255)->nullable();

            $table->string('status', 20)->default('pending'); // pending | succeeded
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('succeeded_at')->nullable();
            $table->string('apex_id', 64)->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apex_retry_jobs');
    }
};
