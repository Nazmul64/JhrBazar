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
        Schema::table('shops', function (Blueprint $table) {
            $table->string('opening_time')->nullable();
            $table->string('closing_time')->nullable();
            $table->string('estimated_delivery')->nullable();
            $table->string('order_prefix')->nullable();
            $table->decimal('min_order_amount', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['opening_time', 'closing_time', 'estimated_delivery', 'order_prefix', 'min_order_amount']);
        });
    }
};
