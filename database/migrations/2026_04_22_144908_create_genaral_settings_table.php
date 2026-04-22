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
        Schema::create('genaral_settings', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('website_name')->nullable();
            $table->string('website_title')->nullable();
            $table->string('default_currency')->default('USD ($)');
            $table->string('currency_position')->default('Prefix');

            // Logos
            $table->string('logo')->nullable();       // Logo Ratio 4:1 (200x50)
            $table->string('favicon')->nullable();     // Favicon (300x300)
            $table->string('app_logo')->nullable();    // App Logo (300x300)

            // Others Information
            $table->string('mobile_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('address')->nullable();

            // Download App Links
            $table->text('google_playstore_link')->nullable();
            $table->text('apple_store_link')->nullable();
            $table->boolean('show_download_app')->default(1);

            // Footer Section
            $table->string('hotline_number')->nullable();
            $table->string('footer_text')->nullable();
            $table->string('footer_logo')->nullable();  // Frontend Footer Logo 4:1
            $table->string('footer_qr')->nullable();    // Frontend Scan the QR (200x200)
            $table->boolean('show_footer_section')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genaral_settings');
    }
};
