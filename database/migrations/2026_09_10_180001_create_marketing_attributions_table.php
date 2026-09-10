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
        if (!Schema::hasTable('marketing_attributions')) {
            Schema::create('marketing_attributions', function (Blueprint $table) {
                $table->id();
                $table->string('visitor_id', 100)->nullable()->index();
                $table->string('session_id', 100)->nullable()->index();
                $table->unsignedBigInteger('order_id')->nullable()->index();
                $table->unsignedBigInteger('invoice_id')->nullable()->index();
                
                // Traffic Source Classification
                $table->string('traffic_source', 100)->default('Direct Visit')->index();
                $table->string('utm_source')->nullable()->index();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable()->index();
                $table->string('utm_term')->nullable();
                $table->string('utm_content')->nullable();
                $table->string('click_id')->nullable();
                
                // URLs & Metadata
                $table->text('referrer_url')->nullable();
                $table->text('landing_page_url')->nullable();
                $table->string('ip_address', 50)->nullable()->index();
                $table->string('country', 100)->nullable();
                $table->string('device_type', 50)->nullable();
                $table->string('browser', 50)->nullable();
                $table->string('os', 50)->nullable();
                
                // Financial and Order Attribution
                $table->decimal('revenue', 12, 2)->default(0);
                $table->string('order_status', 50)->nullable()->index();
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_attributions');
    }
};
