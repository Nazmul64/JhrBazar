<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commission_setups', function (Blueprint $table) {
            $table->id();
            $table->decimal('withdraw_commission_percent', 5, 2)->default(0);
            $table->decimal('min_withdraw_amount', 10, 2)->default(0);
            $table->decimal('max_withdraw_amount', 10, 2)->default(0);
            $table->decimal('withdraw_charge', 5, 2)->default(0); // flat fee
            $table->json('seller_withdraw_rules')->nullable(); // flexible JSON rules
            $table->decimal('daily_limit', 10, 2)->nullable();
            $table->boolean('verification_required')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_setups');
    }
};
?>
