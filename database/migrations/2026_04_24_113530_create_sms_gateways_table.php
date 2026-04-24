<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sms_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('url')->default('https://msg.elitbuzz-bd.com/smsapi');
            $table->string('api_key')->nullable();
            $table->string('sender_id')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('order_confirm')->default(true);
            $table->boolean('forgot_password')->default(true);
            $table->boolean('password_generator')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_gateways');
    }
};
