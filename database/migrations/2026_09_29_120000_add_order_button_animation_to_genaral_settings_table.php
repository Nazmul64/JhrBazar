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
        if (Schema::hasTable('genaral_settings')) {
            Schema::table('genaral_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('genaral_settings', 'order_button_animation')) {
                    $table->boolean('order_button_animation')->default(true)->after('button_hover_color');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('genaral_settings')) {
            Schema::table('genaral_settings', function (Blueprint $table) {
                if (Schema::hasColumn('genaral_settings', 'order_button_animation')) {
                    $table->dropColumn('order_button_animation');
                }
            });
        }
    }
};
