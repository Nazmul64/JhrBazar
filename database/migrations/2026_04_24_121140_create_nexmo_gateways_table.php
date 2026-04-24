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
        Schema::create('nexmo_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('nexmo_key')->nullable();
            $table->string('nexmo_secret')->nullable();
            $table->string('nexmo_from')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexmo_gateways');
    }
};
