<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('refunds')) {
            Schema::create('refunds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
                $table->foreignId('seller_id')->nullable()->constrained('users')->onDelete('set null');
                $table->unsignedBigInteger('courier_id')->nullable();
                $table->string('product_name');
                $table->decimal('product_price', 12, 2);
                $table->integer('quantity');
                $table->decimal('total_amount', 12, 2);
                $table->enum('cancel_reason', [
                    'admin_cancel',
                    'seller_fraud',
                    'payment_issue',
                    'courier_cancel',
                    'customer_request',
                    'damaged_product',
                    'other'
                ])->default('other');
                $table->text('cancel_reason_description')->nullable();
                $table->enum('refund_status', [
                    'pending',
                    'approved',
                    'processing',
                    'completed',
                    'rejected'
                ])->default('pending');
                $table->datetime('refund_date')->nullable();
                $table->text('admin_note')->nullable();
                $table->text('seller_note')->nullable();
                $table->timestamps();
                $table->index('order_id');
                $table->index('product_id');
                $table->index('seller_id');
                $table->index('refund_status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
