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
            $table->string('button_color')->nullable()->default('#57b500')->comment('Primary button color');
            $table->string('button_hover_color')->nullable()->default('#4a9a00')->comment('Button hover color');
            $table->string('footer_text_color')->nullable()->default('#333333')->comment('Footer text color');
            $table->boolean('show_product_tax')->nullable()->default(1)->comment('Show product tax line');
            $table->unsignedInteger('product_tax_truncate')->nullable()->default(30)->comment('Number of characters before ellipsis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            $table->dropColumn([
                'button_color',
                'button_hover_color',
                'footer_text_color',
                'show_product_tax',
                'product_tax_truncate',
            ]);
        });
    }
};
