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
        Schema::create('digital_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('sku')->unique();
            $table->decimal('buying_price', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->decimal('discount_price', 15, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->string('thumbnail')->nullable();
            $table->json('additional_thumbnails')->nullable();
            $table->string('digital_file')->nullable();
            $table->json('license_keys')->nullable();
            $table->string('video_type')->nullable(); // upload, youtube, vimeo
            $table->string('video')->nullable(); // file path or link
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_products');
    }
};
