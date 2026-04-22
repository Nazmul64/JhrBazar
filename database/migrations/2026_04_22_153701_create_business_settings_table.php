<?php
// database/migrations/xxxx_create_business_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();

            // ── Basic Info ──────────────────────────────
            $table->string('company_name')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->enum('business_model', ['single_shop', 'multi_shop'])->default('multi_shop');
            $table->enum('currency_position', ['left', 'right'])->default('left');
            $table->string('timezone')->default('UTC/GMT +06:00 - Asia/Dhaka');
            $table->unsignedInteger('return_order_within_days')->default(3);

            // Payment Methods
            $table->boolean('cash_on_delivery')->default(1);
            $table->boolean('online_payment')->default(1);

            // ── Shops ───────────────────────────────────
            $table->boolean('commission_enabled')->default(1);
            $table->boolean('subscription_enabled')->default(0);
            $table->decimal('commission', 8, 2)->default(10);
            $table->enum('commission_type', ['fixed', 'percentage'])->default('fixed');
            $table->enum('commission_charge', ['per_order', 'per_item'])->default('per_order');
            $table->boolean('pos_in_shop_panel')->default(1);
            $table->boolean('shop_registration')->default(1);
            $table->boolean('need_product_approval')->default(1);
            $table->boolean('update_product_approval')->default(1);

            // ── Withdraw ────────────────────────────────
            $table->decimal('min_withdraw_amount', 10, 2)->default(0);
            $table->decimal('max_withdraw_amount', 10, 2)->nullable();
            $table->unsignedInteger('min_day_withdraw_request')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
