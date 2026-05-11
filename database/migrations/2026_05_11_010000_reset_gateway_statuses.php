<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $gatewayTables = [
            'stripe_gateways',
            'paypal_gateways',
            'razorpay_gateways',
            'paystack_gateways',
            'aamarpay_gateways',
            'bkash_gateways',
            'paytabs_gateways',
            'qicard_gateways',
            'jazzcash_gateways',
            'shurjopay_gateways',
            'bkash_payments',
            'steadfast_couriers',
            'pathao_couriers',
            'sms_gateways',
            'twilio_gateways',
            'telesign_gateways',
            'nexmo_gateways',
            'messagebird_gateways',
        ];

        foreach ($gatewayTables as $table) {
            if (Schema::hasTable($table)) {
                if (Schema::hasColumn($table, 'status')) {
                    DB::table($table)->update(['status' => false]);
                    // For SMS gateways, reset other flags too
                    if ($table === 'sms_gateways') {
                        DB::table($table)->update([
                            'order_confirm' => false,
                            'forgot_password' => false,
                        ]);
                    }
                } elseif (Schema::hasColumn($table, 'is_active')) {
                    DB::table($table)->update(['is_active' => false]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed as we are resetting to a baseline
    }
};
