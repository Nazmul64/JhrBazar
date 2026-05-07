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
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('seller_id')->after('id')->constrained('users')->onDelete('cascade');
            $table->string('purchase_name')->after('seller_id')->nullable();
            $table->date('purchase_date')->after('invoice_no')->nullable();
            $table->string('purchase_slip')->after('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropColumn(['seller_id', 'purchase_name', 'purchase_date', 'purchase_slip']);
        });
    }
};
