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
         Schema::create('qicard_gateways', function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['test', 'live'])->default('test');
            $table->string('currency')->default('IQD');
            $table->string('password')->nullable();
            $table->string('username')->nullable();
            $table->string('terminal_id')->nullable();
            $table->string('title')->default('QiCard');
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
        Schema::dropIfExists('qicard_gateways');
    }
};
