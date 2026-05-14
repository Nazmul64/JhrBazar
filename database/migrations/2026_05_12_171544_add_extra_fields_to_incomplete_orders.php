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
        Schema::table('incomplete_orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('estimated_total');
            $table->string('area')->nullable()->after('payment_method');
            $table->json('cart_items')->nullable()->after('area');
            $table->unsignedBigInteger('staff_id')->nullable()->after('cart_items');
            $table->string('steadfast_id')->nullable()->after('staff_id');
            $table->string('pathao_id')->nullable()->after('steadfast_id');
            $table->text('address')->nullable()->after('pathao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomplete_orders', function (Blueprint $table) {
            //
        });
    }
};
