<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landingpages', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
            $table->json('sections')->nullable()->after('reviews');
        });
    }

    public function down(): void
    {
        Schema::table('landingpages', function (Blueprint $table) {
            $table->dropColumn(['slug', 'sections']);
        });
    }
};
