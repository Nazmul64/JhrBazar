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
        Schema::table('digital_products', function (Blueprint $table) {
            $table->boolean('cash_on_delivery')->default(1);
            $table->boolean('online_payment')->default(1);
        });

        Schema::table('seller_digital_products', function (Blueprint $table) {
            $table->boolean('cash_on_delivery')->default(1);
            $table->boolean('online_payment')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digital_products', function (Blueprint $table) {
            $table->dropColumn(['cash_on_delivery', 'online_payment']);
        });

        Schema::table('seller_digital_products', function (Blueprint $table) {
            $table->dropColumn(['cash_on_delivery', 'online_payment']);
        });
    }
};
