<?php
// database/migrations/xxxx_create_currencies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // e.g. USD, BDT, INR
            $table->string('symbol');         // e.g. $, ৳, ₹
            $table->decimal('rate', 15, 6);   // rate from USD (1 USD = X)
            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
