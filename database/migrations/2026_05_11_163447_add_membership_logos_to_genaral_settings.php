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
            $table->string('membership_logo_1')->nullable();
            $table->string('membership_logo_2')->nullable();
            $table->string('membership_logo_3')->nullable();
            $table->boolean('show_membership_section')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genaral_settings', function (Blueprint $table) {
            $table->dropColumn(['membership_logo_1', 'membership_logo_2', 'membership_logo_3', 'show_membership_section']);
        });
    }
};
