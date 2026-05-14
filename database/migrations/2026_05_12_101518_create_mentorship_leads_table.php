<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentorship_leads', function (Blueprint $table) {
            $table->id();
            // Contact (last step)
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone', 20);
            // Qualifying answers
            $table->string('situation')->nullable();   // Step 1
            $table->string('timeline')->nullable();    // Step 2
            $table->string('hours')->nullable();       // Step 3
            $table->string('investment')->nullable();  // Step 4
            // Pipeline
            $table->string('status')->default('new');  // new, contacted, qualified, enrolled, archived
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
        Schema::dropIfExists('mentorship_leads');
    }
};
