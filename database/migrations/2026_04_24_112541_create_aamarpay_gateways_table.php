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
       Schema::create('aamarpay_gateways', function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['test', 'live'])->default('test');
            $table->string('store_id')->nullable();
            $table->string('signature_key')->nullable();
            $table->string('title')->default('aamarPay');
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
        Schema::dropIfExists('aamarpay_gateways');
    }
};
