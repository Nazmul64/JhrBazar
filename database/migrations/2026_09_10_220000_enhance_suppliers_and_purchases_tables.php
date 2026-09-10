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
        // ── Enhance Suppliers Table ──
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('suppliers', 'phone')) {
                $table->string('phone')->nullable()->after('name');
            }
            if (!Schema::hasColumn('suppliers', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('suppliers', 'status')) {
                $table->boolean('status')->default(true)->after('profile_image');
            }
        });

        // ── Enhance Purchases Table ──
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'payment_method')) {
                $table->string('payment_method')->default('Cash')->after('due_amount');
            }
            if (!Schema::hasColumn('purchases', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('purchase_slip');
            }
        });

        // ── Enhance Purchase Items Table ──
        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'subtotal') && !Schema::hasColumn('purchase_items', 'sub_total')) {
                $table->decimal('subtotal', 12, 2)->default(0)->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['name', 'phone', 'email', 'status']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'created_by']);
        });
    }
};
