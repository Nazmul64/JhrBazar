<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('refund_items')) {
            Schema::create('refund_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('refund_id')->constrained('refunds')->onDelete('cascade');
                $table->unsignedBigInteger('order_item_id')->nullable();
                $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
                $table->string('product_name');
                $table->decimal('product_price', 12, 2);
                $table->integer('quantity');
                $table->decimal('item_total', 12, 2);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_items');
    }
};
