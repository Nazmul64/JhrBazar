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
        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'subtotal')) {
                $table->decimal('subtotal', 14, 2)->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('purchase_items', 'sub_total')) {
                $table->decimal('sub_total', 14, 2)->default(0)->after('subtotal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep columns safely
    }
};
