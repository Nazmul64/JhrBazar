<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number', 30)->unique();    // e.g. RC000001

            $table->foreignId('pointofsalepo_id')
                  ->constrained('pointofsalepos')
                  ->cascadeOnDelete();

            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->nullOnDelete();

            $table->json('items');                             // cart snapshot
            $table->json('tax_breakdown')->nullable();         // per-tax breakdown array

            $table->decimal('sub_total',      10, 2)->default(0);
            $table->decimal('discount',       10, 2)->default(0);
            $table->decimal('tax_amount',     10, 2)->default(0);
            $table->decimal('delivery_charge',10, 2)->default(0);
            $table->decimal('grand_total',    10, 2)->default(0);

            $table->decimal('received_amount',10, 2)->default(0);
            $table->decimal('change_amount',  10, 2)->default(0);

            $table->string('payment_method', 50)->default('cash');
            $table->string('coupon_code',   100)->nullable();
            $table->text('note')->nullable();

            $table->timestamps();

            // indexes
            $table->index('invoice_number');
            $table->index('customer_id');
            $table->index('pointofsalepo_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_invoices');
    }
};
