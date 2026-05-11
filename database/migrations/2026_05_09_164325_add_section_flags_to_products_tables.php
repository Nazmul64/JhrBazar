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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_new_arrival')->default(false)->after('is_active');
            $table->boolean('is_best_seller')->default(false)->after('is_new_arrival');
            $table->boolean('is_hot_product')->default(false)->after('is_best_seller');
            $table->boolean('is_flash_sale')->default(false)->after('is_hot_product');
            $table->boolean('is_just_for_you')->default(false)->after('is_flash_sale');
        });

        Schema::table('seller_products', function (Blueprint $table) {
            $table->boolean('is_new_arrival')->default(false)->after('is_active');
            $table->boolean('is_best_seller')->default(false)->after('is_new_arrival');
            $table->boolean('is_hot_product')->default(false)->after('is_best_seller');
            $table->boolean('is_flash_sale')->default(false)->after('is_hot_product');
            $table->boolean('is_just_for_you')->default(false)->after('is_flash_sale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_tables', function (Blueprint $table) {
            //
        });
    }
};
