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
        Schema::create('cyber_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable();
            $table->string('wifi_provider')->nullable();
            $table->string('location')->nullable();
            $table->double('lat')->nullable();
            $table->double('lon')->nullable();
            $table->text('device_agent')->nullable();
            $table->string('device_type')->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cyber_alerts');
    }
};
