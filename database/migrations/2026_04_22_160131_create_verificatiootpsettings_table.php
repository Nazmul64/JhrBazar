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
        Schema::create('verificatiootpsettings', function (Blueprint $table) {
            $table->id();
            $table->boolean('customer_registration_otp_verify')->default(false);
            $table->boolean('must_verify_account_on_order_placement')->default(true);
            $table->enum('register_otp_send_method', ['phone', 'email'])->default('email');
            $table->enum('forget_password_otp_send_method', ['phone', 'email'])->default('email');
            $table->boolean('registration_phone_required')->default(true);
            $table->integer('min_phone_length')->default(9);
            $table->integer('max_phone_length')->default(14);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verificatiootpsettings');
    }
};
