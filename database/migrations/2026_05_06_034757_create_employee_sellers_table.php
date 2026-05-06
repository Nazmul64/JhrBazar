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
        Schema::create('employee_sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender')->default('male');
            $table->string('email')->nullable();
            $table->string('role')->default('employee');
            $table->string('password');
            $table->text('address')->nullable();
            $table->string('profile_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_sellers');
    }
};
