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
            if (!Schema::hasColumn('genaral_settings', 'free_shipping_text')) {
                $table->string('free_shipping_text')->nullable()->after('dbid_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            if (Schema::hasColumn('genaral_settings', 'free_shipping_text')) {
                $table->dropColumn('free_shipping_text');
            }
        });
    }
};
