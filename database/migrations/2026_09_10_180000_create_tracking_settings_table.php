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
        if (!Schema::hasTable('tracking_settings')) {
            Schema::create('tracking_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('master_tracking_status')->default(true);
                
                // Platform IDs and Statuses
                $table->string('gtm_id')->nullable();
                $table->boolean('gtm_status')->default(false);
                
                $table->string('ga4_id')->nullable();
                $table->boolean('ga4_status')->default(false);
                
                $table->string('facebook_pixel_id')->nullable();
                $table->boolean('facebook_pixel_status')->default(false);
                
                $table->string('tiktok_pixel_id')->nullable();
                $table->boolean('tiktok_pixel_status')->default(false);
                
                // DataLayer Events Toggle Configuration (JSON array)
                $table->json('datalayer_events')->nullable();
                
                // Attribution & Cookie Configuration
                $table->integer('cookie_lifetime_days')->default(90);
                $table->boolean('enable_visitor_cookies')->default(true);
                $table->boolean('enable_utm_tracking')->default(true);
                $table->boolean('enable_referrer_tracking')->default(true);
                $table->boolean('enable_device_tracking')->default(true);
                $table->boolean('enable_country_detection')->default(true);
                $table->boolean('enable_custom_events_logging')->default(true);
                $table->boolean('enable_admin_reports_dashboard')->default(true);
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_settings');
    }
};
