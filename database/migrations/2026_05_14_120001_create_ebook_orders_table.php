<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebook_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ebook_id')->nullable()->constrained('ebooks')->nullOnDelete();
            $table->string('ebook_slug', 80);
            $table->string('ebook_title', 200);

            $table->decimal('amount', 8, 2);

            // Customer
            $table->string('first_name', 100);
            $table->string('last_name',  100);
            $table->string('email',      150);
            $table->string('phone',      30)->nullable();
            $table->string('address',    255)->nullable();
            $table->string('city',       100)->nullable();
            $table->string('state',      10)->nullable();
            $table->string('zip',        20)->nullable();

            // Authorize.Net response
            $table->string('invoice_number', 64)->unique();
            $table->string('transaction_id', 64)->nullable();
            $table->string('auth_code',      32)->nullable();

            // status: paid | failed | refunded
            $table->string('status', 20)->default('paid');

            $table->boolean('marketing_opt_in')->default(false);

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamp('charged_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->unsignedInteger('download_count')->default(0);

            $table->json('raw_payload')->nullable();

            $table->timestamps();

            $table->index('email');
            $table->index('ebook_slug');
            $table->index('charged_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_orders');
    }
};
