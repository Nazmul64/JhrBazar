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
            $table->string('slider_height_mobile')->default('200px')->after('slider_height');
            $table->string('product_img_height_desktop')->default('200px')->after('product_card_height');
            $table->string('product_img_height_mobile')->default('150px')->after('product_img_height_desktop');
            $table->string('product_title_size_desktop')->default('14px')->after('font_size');
            $table->string('product_title_size_mobile')->default('12px')->after('product_title_size_desktop');
            $table->string('product_price_size')->default('15px')->after('product_title_size_mobile');
            $table->string('product_old_price_size')->default('12px')->after('product_price_size');
        });
    }

    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            $table->dropColumn([
                'slider_height_mobile',
                'product_img_height_desktop',
                'product_img_height_mobile',
                'product_title_size_desktop',
                'product_title_size_mobile',
                'product_price_size',
                'product_old_price_size'
            ]);
        });
    }
};
