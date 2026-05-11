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
            $table->string('slider_height')->nullable()->default('420px');
            $table->string('category_img_height')->nullable()->default('100px');
            $table->string('category_img_width')->nullable()->default('100%');
            $table->string('category_shape')->nullable()->default('rounded')->comment('rounded, circle, square');
            $table->string('category_behavior')->nullable()->default('slider')->comment('slider, grid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            $table->dropColumn([
                'slider_height',
                'category_img_height',
                'category_img_width',
                'category_shape',
                'category_behavior',
            ]);
        });
    }
};
