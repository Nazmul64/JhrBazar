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
            $table->boolean('show_top_rated_shops')->default(false)->after('show_digital_products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            //
        });
    }
};
