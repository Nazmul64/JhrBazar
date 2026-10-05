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
        if (Schema::hasTable('seller_products')) {
            Schema::table('seller_products', function (Blueprint $table) {
                if (!Schema::hasColumn('seller_products', 'admin_status')) {
                    $table->enum('admin_status', ['pending', 'approved', 'rejected'])->default('pending')->after('is_active');
                }
                if (!Schema::hasColumn('seller_products', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('admin_status');
                }
            });

            // Set existing products to approved
            \Illuminate\Support\Facades\DB::table('seller_products')->whereNull('admin_status')->orWhere('admin_status', 'pending')->update(['admin_status' => 'approved']);
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'admin_status')) {
                    $table->enum('admin_status', ['pending', 'approved', 'rejected'])->default('approved')->after('is_active');
                }
                if (!Schema::hasColumn('products', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('admin_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('seller_products')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->dropColumn(['admin_status', 'rejection_reason']);
            });
        }
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['admin_status', 'rejection_reason']);
            });
        }
    }
};
