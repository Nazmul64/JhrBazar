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
        Schema::create('jazzcash_gateways', function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['test', 'live'])->default('test');
            $table->string('base_url')->default('https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform');
            $table->string('password')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('integrity_salt')->nullable();
            $table->string('title')->default('JazzCash');
            $table->string('logo')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jazzcash_gateways');
    }
};
