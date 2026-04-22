<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aiprompts', function (Blueprint $table) {
            $table->id();
            $table->longText('product_description')->nullable();
            $table->longText('page_description')->nullable();
            $table->longText('blog_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aiprompts');
    }
};
