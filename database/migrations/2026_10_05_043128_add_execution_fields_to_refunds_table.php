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
        if (Schema::hasTable('refunds')) {
            Schema::table('refunds', function (Blueprint $table) {
                if (!Schema::hasColumn('refunds', 'customer_id')) {
                    $table->foreignId('customer_id')->nullable()->after('order_id')->constrained('users')->onDelete('set null');
                }
                if (!Schema::hasColumn('refunds', 'order_item_id')) {
                    $table->unsignedBigInteger('order_item_id')->nullable()->after('order_id');
                }
                if (!Schema::hasColumn('refunds', 'images')) {
                    $table->json('images')->nullable()->after('cancel_reason_description');
                }
                if (!Schema::hasColumn('refunds', 'seller_approval')) {
                    $table->enum('seller_approval', ['pending', 'approved', 'rejected'])->default('pending')->after('refund_status');
                }
                if (!Schema::hasColumn('refunds', 'refund_method')) {
                    $table->enum('refund_method', ['gateway', 'wallet', 'manual'])->default('gateway')->after('seller_approval');
                }
                if (!Schema::hasColumn('refunds', 'transaction_id')) {
                    $table->string('transaction_id')->nullable()->after('refund_method');
                }
                if (!Schema::hasColumn('refunds', 'refunded_at')) {
                    $table->timestamp('refunded_at')->nullable()->after('transaction_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('refunds')) {
            Schema::table('refunds', function (Blueprint $table) {
                $table->dropColumn([
                    'customer_id',
                    'order_item_id',
                    'images',
                    'seller_approval',
                    'refund_method',
                    'transaction_id',
                    'refunded_at'
                ]);
            });
        }
    }
};
