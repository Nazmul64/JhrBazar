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
            $table->string('primary_color')->nullable()->default('#57b500');
            $table->string('secondary_color')->nullable();
            $table->string('top_header_color')->nullable()->default('#57b500');
            $table->string('header_color')->nullable()->default('#ffffff');
            $table->string('footer_color')->nullable();
            $table->string('font_family')->nullable()->default('Arial, sans-serif');
            $table->string('font_size')->nullable()->default('14px');
            $table->string('product_card_height')->nullable()->default('auto');
            $table->string('product_card_width')->nullable()->default('100%');
            $table->integer('products_per_row_mobile')->nullable()->default(2);
            $table->integer('products_per_row_desktop')->nullable()->default(6);
            $table->boolean('show_product_stats')->default(1);
            $table->string('layout_style')->default('container');
            $table->text('marquee_text')->nullable();
            $table->boolean('show_marquee')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color', 'secondary_color', 'top_header_color', 'header_color', 'footer_color',
                'font_family', 'font_size', 'product_card_height', 'product_card_width',
                'products_per_row_mobile', 'products_per_row_desktop', 'show_product_stats',
                'layout_style', 'marquee_text', 'show_marquee'
            ]);
        });
    }
};
