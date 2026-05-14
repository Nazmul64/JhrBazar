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
            // Modify the status column to include 'pending'
            $table->enum('status', ['completed', 'draft', 'cancelled', 'pending'])
                  ->default('completed')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pointofsalepos', function (Blueprint $table) {
            // Revert to original enum values
            $table->enum('status', ['completed', 'draft', 'cancelled'])
                  ->default('completed')
                  ->change();
        });
    }
};
