<?php
// 2024_01_01_000003_create_fraud_alerts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_id')->unique();
            $table->foreignId('fraud_check_id')->constrained('fraud_checks')->cascadeOnDelete();
            $table->string('severity');
            $table->string('type');
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_note')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_alerts');
    }
};
