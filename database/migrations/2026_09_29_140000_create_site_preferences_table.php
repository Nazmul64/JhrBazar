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
        if (!Schema::hasTable('site_preferences')) {
            Schema::create('site_preferences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('session_id', 100)->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->string('language', 10)->default('en');
                $table->string('theme_mode', 20)->default('light'); // 'light' or 'dark'
                $table->timestamps();
            });
        }

        if (Schema::hasTable('genaral_settings')) {
            Schema::table('genaral_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('genaral_settings', 'default_language')) {
                    $table->string('default_language', 10)->default('en')->after('website_name');
                }
                if (!Schema::hasColumn('genaral_settings', 'default_theme_mode')) {
                    $table->string('default_theme_mode', 20)->default('light')->after('default_language');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_preferences');

        if (Schema::hasTable('genaral_settings')) {
            Schema::table('genaral_settings', function (Blueprint $table) {
                if (Schema::hasColumn('genaral_settings', 'default_language')) {
                    $table->dropColumn('default_language');
                }
                if (Schema::hasColumn('genaral_settings', 'default_theme_mode')) {
                    $table->dropColumn('default_theme_mode');
                }
            });
        }
    }
};
