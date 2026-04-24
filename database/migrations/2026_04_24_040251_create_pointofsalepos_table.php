<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pointofsalepos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->nullOnDelete();

            $table->json('items');                              // snapshot of cart items

            $table->decimal('sub_total',    10, 2)->default(0);
            $table->decimal('discount',     10, 2)->default(0);
            $table->decimal('tax_amount',   10, 2)->default(0);
            $table->decimal('grand_total',  10, 2)->default(0);

            $table->string('payment_method', 50)->default('cash');
            $table->string('coupon_code',   100)->nullable();
            $table->text('note')->nullable();

            $table->enum('status', ['completed', 'draft', 'cancelled'])->default('completed');

            $table->timestamps();

            // indexes
            $table->index('customer_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pointofsalepos');
    }
};
