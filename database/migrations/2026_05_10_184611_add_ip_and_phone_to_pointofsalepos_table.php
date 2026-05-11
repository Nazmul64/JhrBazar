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
        Schema::table('pointofsalepos', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('note');
            $table->string('phone')->nullable()->after('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pointofsalepos', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'phone']);
        });
    }
};
