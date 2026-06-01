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
        Schema::table('genaral_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('genaral_settings', 'customer_visit_notification_status')) {
                $table->boolean('customer_visit_notification_status')->default(true)->after('loader_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            if (Schema::hasColumn('genaral_settings', 'customer_visit_notification_status')) {
                $table->dropColumn('customer_visit_notification_status');
            }
        });
    }
};
