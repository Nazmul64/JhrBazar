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
        Schema::create('landingpages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('media_type')->default('image'); // image / video
            $table->string('image')->nullable();            // main image

            // reviews (JSON array: [{review, image}])
            $table->json('reviews')->nullable();

            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landingpages');
    }
};
