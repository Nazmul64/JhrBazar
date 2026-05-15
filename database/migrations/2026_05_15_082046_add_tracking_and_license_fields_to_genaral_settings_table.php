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
            $table->string('google_analytics_id')->nullable();
            $table->string('facebook_pixel_id')->nullable();
            $table->string('gtm_id')->nullable();
            $table->boolean('enable_analytics')->default(false);
            $table->boolean('enable_pixel')->default(false);
            $table->boolean('enable_gtm')->default(false);
            $table->string('trade_license_number')->nullable();
            $table->string('dbid_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            $table->dropColumn([
                'google_analytics_id',
                'facebook_pixel_id',
                'gtm_id',
                'enable_analytics',
                'enable_pixel',
                'enable_gtm',
                'trade_license_number',
                'dbid_number'
            ]);
        });
    }
};
