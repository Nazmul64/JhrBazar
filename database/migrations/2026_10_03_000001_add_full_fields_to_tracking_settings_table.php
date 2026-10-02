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
        Schema::table('tracking_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('tracking_settings', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('id');
            }
            if (!Schema::hasColumn('tracking_settings', 'gtm_enabled')) {
                $table->boolean('gtm_enabled')->default(false)->after('gtm_id');
            }
            if (!Schema::hasColumn('tracking_settings', 'ga4_enabled')) {
                $table->boolean('ga4_enabled')->default(false);
            }
            if (!Schema::hasColumn('tracking_settings', 'ga4_measurement_id')) {
                $table->string('ga4_measurement_id')->nullable();
            }
            if (!Schema::hasColumn('tracking_settings', 'fb_pixel_enabled')) {
                $table->boolean('fb_pixel_enabled')->default(false);
            }
            if (!Schema::hasColumn('tracking_settings', 'fb_pixel_id')) {
                $table->string('fb_pixel_id')->nullable();
            }
            if (!Schema::hasColumn('tracking_settings', 'fb_access_token')) {
                $table->text('fb_access_token')->nullable();
            }
            if (!Schema::hasColumn('tracking_settings', 'tiktok_pixel_enabled')) {
                $table->boolean('tiktok_pixel_enabled')->default(false);
            }
            if (!Schema::hasColumn('tracking_settings', 'track_utm')) {
                $table->boolean('track_utm')->default(true);
            }
            if (!Schema::hasColumn('tracking_settings', 'track_referrer')) {
                $table->boolean('track_referrer')->default(true);
            }
            if (!Schema::hasColumn('tracking_settings', 'track_click_ids')) {
                $table->boolean('track_click_ids')->default(true);
            }
            if (!Schema::hasColumn('tracking_settings', 'track_direct')) {
                $table->boolean('track_direct')->default(true);
            }
            if (!Schema::hasColumn('tracking_settings', 'custom_head_script')) {
                $table->longText('custom_head_script')->nullable();
            }
            if (!Schema::hasColumn('tracking_settings', 'custom_body_script')) {
                $table->longText('custom_body_script')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracking_settings', function (Blueprint $table) {
            $cols = [
                'is_active',
                'gtm_enabled',
                'ga4_enabled',
                'ga4_measurement_id',
                'fb_pixel_enabled',
                'fb_pixel_id',
                'fb_access_token',
                'tiktok_pixel_enabled',
                'track_utm',
                'track_referrer',
                'track_click_ids',
                'track_direct',
                'custom_head_script',
                'custom_body_script'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('tracking_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
