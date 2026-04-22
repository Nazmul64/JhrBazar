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
        Schema::create('themecolorssettings', function (Blueprint $table) {
            $table->id();
            $table->string('primary_color')->default('#eb2e61');
            $table->string('secondary_color')->default('#fbd5df');
            $table->string('palette_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themecolorssettings');
    }
};
