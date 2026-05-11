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
        Schema::table('genaral_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('genaral_settings', 'products_per_row_desktop')) {
                $table->integer('products_per_row_desktop')->default(6)->after('product_img_height_mobile');
            }
            if (!Schema::hasColumn('genaral_settings', 'products_per_row_mobile')) {
                $table->integer('products_per_row_mobile')->default(2)->after('products_per_row_desktop');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            $table->dropColumn(['products_per_row_desktop', 'products_per_row_mobile']);
        });
    }
};
