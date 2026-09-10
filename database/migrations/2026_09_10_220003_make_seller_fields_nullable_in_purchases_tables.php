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
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'seller_id')) {
                $table->unsignedBigInteger('seller_id')->nullable()->change();
            }
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_items', 'seller_id')) {
                $table->unsignedBigInteger('seller_id')->nullable()->change();
            }
            if (Schema::hasColumn('purchase_items', 'seller_product_id')) {
                $table->unsignedBigInteger('seller_product_id')->nullable()->change();
            }
            if (!Schema::hasColumn('purchase_items', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('purchase_id');
            } else {
                $table->unsignedBigInteger('product_id')->nullable()->change();
            }
            if (!Schema::hasColumn('purchase_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep nullable safely
    }
};
