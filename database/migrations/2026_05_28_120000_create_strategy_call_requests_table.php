<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strategy_call_requests', function (Blueprint $table) {
            $table->id();
            // Contact
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30);
            $table->string('best_time')->nullable();
            // Qualification
            $table->string('situation')->nullable();           // starting/mid/stuck/already_worked
            $table->string('score')->nullable();               // current score band
            $table->string('timeline')->nullable();            // start window
            $table->text('goal')->nullable();                  // 90-day goal
            $table->boolean('prior_repair')->default(false);
            $table->text('prior_repair_notes')->nullable();
            // Monitoring access (no password ever stored — bring it to the call)
            $table->string('monitoring_service')->nullable();
            $table->string('monitoring_username')->nullable();
            $table->boolean('will_bring_login')->default(false);
            $table->string('investment_range')->nullable();
            $table->boolean('showup_confirmed')->default(false);
            // Pipeline
            $table->string('status')->default('new');          // new, booked, showed, no_show, converted, archived
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
        Schema::dropIfExists('strategy_call_requests');
    }
};
