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
            $table->decimal('seller_commission', 8, 2)->default(10.00)->after('max_withdraw');
            $table->decimal('withdraw_commission', 8, 2)->default(2.00)->after('seller_commission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            $table->dropColumn(['seller_commission', 'withdraw_commission']);
        });
    }
};
