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
        Schema::create('seller_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('voucher_code')->unique();
            $table->string('discount_type')->default('amount'); // amount, percentage
            $table->decimal('discount', 10, 2);
            $table->decimal('minimum_order_amount', 10, 2)->default(0);
            $table->integer('limit_for_single_user')->nullable();
            $table->decimal('maximum_discount_amount', 10, 2)->nullable();
            $table->date('start_date');
            $table->time('start_time');
            $table->date('expired_date');
            $table->time('expired_time');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_vouchers');
    }
};
