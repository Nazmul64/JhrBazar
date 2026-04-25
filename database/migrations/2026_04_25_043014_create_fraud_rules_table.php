<?php
// 2024_01_01_000002_create_fraud_rules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('condition_field');
            $table->string('condition_operator');
            $table->string('condition_value');
            $table->string('action');
            $table->integer('score_impact')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(50);
            $table->integer('triggered_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('is_active');
            $table->index('category');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_rules');
    }
};
