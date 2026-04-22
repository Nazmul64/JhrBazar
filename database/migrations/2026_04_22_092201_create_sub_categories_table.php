<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('thumbnail')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table: a subcategory can belong to many categories
        Schema::create('category_sub_category', function (Blueprint $table) {
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->onDelete('cascade');
            $table->foreignId('sub_category_id')
                  ->constrained('sub_categories')
                  ->onDelete('cascade');
            $table->primary(['category_id', 'sub_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_sub_category');
        Schema::dropIfExists('sub_categories');
    }
};
