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
        Schema::create('promocodes', function (Blueprint $table) {
            $table->id();
            $table->string('shop_ids')->nullable(); // JSON বা comma separated shop IDs
            $table->string('coupon_code')->unique();
            $table->enum('discount_type', ['amount', 'percentage'])->default('amount');
            $table->decimal('discount', 10, 2);
            $table->decimal('minimum_order_amount', 10, 2);
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
        Schema::dropIfExists('promocodes');
    }
};
