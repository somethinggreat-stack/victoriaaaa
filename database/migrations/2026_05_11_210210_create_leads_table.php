<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            // Multi-step popup quiz answers
            $table->string('score')->nullable();           // Step 1 — score range
            $table->string('issue')->nullable();           // Step 2 — biggest culprit
            $table->string('goal')->nullable();            // Step 3 — primary goal
            $table->string('source')->default('popup');    // popup / banner / paid etc
            $table->string('status')->default('new');      // new, contacted, converted, archived
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
        Schema::dropIfExists('leads');
    }
};
