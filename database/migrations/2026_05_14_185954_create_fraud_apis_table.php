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
        Schema::create('fraud_apis', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // free, paid
            $table->string('api_url')->nullable();
            $table->string('api_key')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fraud_apis');
    }
};
