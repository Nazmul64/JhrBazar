<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_unlimited')) {
                $table->boolean('is_unlimited')->default(false)->after('stock_quantity');
            }
            if (!Schema::hasColumn('products', 'low_stock_threshold')) {
                $table->integer('low_stock_threshold')->default(3)->after('is_unlimited');
            }
            if (!Schema::hasColumn('products', 'total_in')) {
                $table->integer('total_in')->default(0)->after('low_stock_threshold');
            }
            if (!Schema::hasColumn('products', 'total_sold')) {
                $table->integer('total_sold')->default(0)->after('total_in');
            }
        });

        // Initialize total_in for existing products with stock_quantity
        DB::table('products')->where('stock_quantity', '>', 0)->update([
            'total_in' => DB::raw('stock_quantity')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_unlimited', 'low_stock_threshold', 'total_in', 'total_sold']);
        });
    }
};
