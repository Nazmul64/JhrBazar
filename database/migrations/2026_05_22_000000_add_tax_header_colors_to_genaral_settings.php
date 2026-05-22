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
        Schema::table('genaral_settings', function (Blueprint $table) {
            $table->string('tax_header_color')->nullable()->default('#f8f9fa')->comment('Tax section header background color');
            $table->string('tax_header_text_color')->nullable()->default('#1a1a2e')->comment('Tax section header text color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            $table->dropColumn(['tax_header_color', 'tax_header_text_color']);
        });
    }
};
