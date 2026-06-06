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
        // 1. Add device fingerprinting columns to pointofsalepos table
        Schema::table('pointofsalepos', function (Blueprint $table) {
            if (!Schema::hasColumn('pointofsalepos', 'device_fingerprint')) {
                $table->string('device_fingerprint')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('pointofsalepos', 'browser')) {
                $table->string('browser')->nullable()->after('device_fingerprint');
            }
            if (!Schema::hasColumn('pointofsalepos', 'os')) {
                $table->string('os')->nullable()->after('browser');
            }
            if (!Schema::hasColumn('pointofsalepos', 'device_type')) {
                $table->string('device_type')->nullable()->after('os');
            }
            
            // Add indexes to pointofsalepos columns
            $table->index('ip_address');
            $table->index('phone');
            $table->index('device_fingerprint');
        });

        // 2. Add indexes to customer_visits table
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->index('phone_number');
            $table->index('visited_at');
            $table->index(['phone_number', 'page_visited', 'visited_at'], 'cust_visits_composite_idx');
        });

        // 3. Add index to fraud_checks table
        Schema::table('fraud_checks', function (Blueprint $table) {
            $table->index('ip_address');
            $table->index('customer_phone');
        });

        // 4. Add index to fraud_blacklists table
        Schema::table('fraud_blacklists', function (Blueprint $table) {
            $table->index(['type', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pointofsalepos', function (Blueprint $table) {
            $table->dropIndex(['ip_address']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['device_fingerprint']);
            $table->dropColumn(['device_fingerprint', 'browser', 'os', 'device_type']);
        });

        Schema::table('customer_visits', function (Blueprint $table) {
            $table->dropIndex(['phone_number']);
            $table->dropIndex(['visited_at']);
            $table->dropIndex('cust_visits_composite_idx');
        });

        Schema::table('fraud_checks', function (Blueprint $table) {
            $table->dropIndex(['ip_address']);
            $table->dropIndex(['customer_phone']);
        });

        Schema::table('fraud_blacklists', function (Blueprint $table) {
            $table->dropIndex(['type', 'value']);
        });
    }
};
