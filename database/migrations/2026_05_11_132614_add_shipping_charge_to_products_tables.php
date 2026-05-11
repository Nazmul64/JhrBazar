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
            $table->boolean('is_shipping_charge')->default(1)->after('is_active');
        });
        Schema::table('seller_products', function (Blueprint $table) {
            $table->boolean('is_shipping_charge')->default(1)->after('is_active');
        });
        if (Schema::hasTable('digital_products')) {
            Schema::table('digital_products', function (Blueprint $table) {
                $table->boolean('is_shipping_charge')->default(1)->after('is_active');
            });
        }
        if (Schema::hasTable('seller_digital_products')) {
            Schema::table('seller_digital_products', function (Blueprint $table) {
                $table->boolean('is_shipping_charge')->default(1)->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_shipping_charge');
        });
        Schema::table('seller_products', function (Blueprint $table) {
            $table->dropColumn('is_shipping_charge');
        });
        if (Schema::hasTable('digital_products')) {
            Schema::table('digital_products', function (Blueprint $table) {
                $table->dropColumn('is_shipping_charge');
            });
        }
        if (Schema::hasTable('seller_digital_products')) {
            Schema::table('seller_digital_products', function (Blueprint $table) {
                $table->dropColumn('is_shipping_charge');
            });
        }
    }
};
