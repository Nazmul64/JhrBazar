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
        Schema::create('sslcommerz_gateways', function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['test', 'live'])->default('test');
            $table->string('store_id')->nullable();
            $table->string('store_password')->nullable();
            $table->string('title')->default('SSLCommerz');
            $table->string('logo')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sslcommerz_gateways');
    }
};
