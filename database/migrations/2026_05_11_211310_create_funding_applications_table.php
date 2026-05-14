<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_applications', function (Blueprint $table) {
            $table->id();
            // Contact details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone', 20);
            // Qualification answers
            $table->string('amount')->nullable();        // Step 1 — funding goal
            $table->string('confirmed')->nullable();     // Step 2 — accuracy confirmation
            $table->string('limits')->nullable();        // Step 3 — credit card limits
            $table->string('usage')->nullable();         // Step 4 — CC usage %
            $table->string('fico')->nullable();          // Step 5 — FICO score
            $table->string('situation')->nullable();     // Step 6 — business situation
            $table->string('income')->nullable();        // Step 7 — annual income
            $table->json('negatives')->nullable();       // Step 8 — array of marks
            // Status / pipeline
            $table->string('status')->default('new');    // new, contacted, qualified, funded, archived
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
        Schema::dropIfExists('funding_applications');
    }
};
