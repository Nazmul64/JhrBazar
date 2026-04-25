<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_checks', function (Blueprint $table) {
            $table->id();
            $table->string('check_id')->unique();
            $table->string('type');
            $table->string('input_value');
            $table->string('status')->default('pending');
            $table->decimal('risk_score', 5, 2)->default(0);
            $table->string('risk_level')->default('low');
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->boolean('vpn_detected')->default(false);
            $table->boolean('proxy_detected')->default(false);
            $table->boolean('tor_detected')->default(false);
            $table->boolean('email_valid')->nullable();
            $table->boolean('email_disposable')->nullable();
            $table->string('email_domain')->nullable();
            $table->integer('email_domain_age')->nullable();
            $table->json('social_profiles')->nullable();
            $table->boolean('phone_valid')->nullable();
            $table->string('phone_carrier')->nullable();
            $table->string('phone_type')->nullable();
            $table->string('phone_country')->nullable();
            $table->decimal('transaction_amount', 15, 2)->nullable();
            $table->string('transaction_currency')->default('BDT');
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->text('device_fingerprint')->nullable();
            $table->json('triggered_rules')->nullable();
            $table->json('flags')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('risk_level');
            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_checks');
    }
};
