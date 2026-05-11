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
        Schema::table('pointofsalepos', function (Blueprint $table) {
            $table->string('payment_status')->default('pending')->after('payment_method');
            $table->unsignedBigInteger('staff_id')->nullable()->after('customer_id');
            $table->string('courier_name')->nullable()->after('status');
            $table->string('courier_status')->nullable()->after('courier_name');
            $table->string('steadfast_order_id')->nullable()->after('courier_status');
            $table->string('pathao_consignment_id')->nullable()->after('steadfast_order_id');
            
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pointofsalepos', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn([
                'payment_status',
                'staff_id',
                'courier_name',
                'courier_status',
                'steadfast_order_id',
                'pathao_consignment_id'
            ]);
        });
    }
};
