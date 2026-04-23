<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flashsales', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('minimum_discount', 5, 2)->default(0);
            $table->date('start_date');
            $table->time('start_time');
            $table->date('end_date');
            $table->time('end_time');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();   // stores "uploads/flashsale/filename.ext"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot: flashsale ↔ products
        Schema::create('flashsale_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flashsale_id')->constrained('flashsales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashsale_products');
        Schema::dropIfExists('flashsales');
    }
};
