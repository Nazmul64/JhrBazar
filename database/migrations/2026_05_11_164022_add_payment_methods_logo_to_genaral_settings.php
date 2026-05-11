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
            $table->string('payment_methods_logo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            $table->dropColumn('payment_methods_logo');
        });
    }
};
