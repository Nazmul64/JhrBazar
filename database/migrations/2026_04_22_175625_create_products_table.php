<?php
// database/migrations/xxxx_create_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('short_description');
            $table->longText('description')->nullable();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->string('color')->nullable();
            $table->string('unit')->nullable();
            $table->string('size')->nullable();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique(); // auto-generated on save
            $table->decimal('buying_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('discount_price', 10, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->string('thumbnail')->nullable();          // single main image
            $table->json('gallery_images')->nullable();       // multiple gallery images
            $table->string('video_type')->nullable();         // 'file' | 'url' | 'youtube'
            $table->string('video')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('rating', 3, 1)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
