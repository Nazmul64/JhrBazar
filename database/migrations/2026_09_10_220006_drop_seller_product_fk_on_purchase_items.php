<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            // Drop foreign key to seller_products
            try {
                $table->dropForeign('purchase_items_product_id_foreign');
            } catch (\Throwable $e) {
                // Ignore if not present
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
