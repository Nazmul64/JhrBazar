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
         Schema::create('steadfast_couriers', function (Blueprint $table) {
            $table->id();
            $table->string('api_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('url')->default('https://portal.steadfast.com.bd/api/v1/create_order');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('steadfast_couriers');
    }
};
