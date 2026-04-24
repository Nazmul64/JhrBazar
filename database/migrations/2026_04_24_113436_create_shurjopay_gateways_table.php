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
        Schema::create('shurjopay_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('prefix')->nullable();
            $table->string('success_url')->nullable();
            $table->string('return_url')->nullable();
            $table->string('base_url')->default('https://sandbox.shurjopayment.com');
            $table->string('password')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shurjopay_gateways');
    }
};
