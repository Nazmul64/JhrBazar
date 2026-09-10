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
        Schema::create('inventory_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->string('type', 50)->index(); // stock_in, sale_deduction, order_cancelled, order_returned, manual_adjustment
            $table->integer('quantity'); // Positive or negative
            $table->integer('previous_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_ledgers');
    }
};
