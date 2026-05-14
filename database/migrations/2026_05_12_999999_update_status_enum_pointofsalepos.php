<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the status column to include new enum values and set default to 'pending'
        Schema::table('pointofsalepos', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
                'completed',
                'draft',
            ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the original enum values used before the update.
        Schema::table('pointofsalepos', function (Blueprint $table) {
            $table->enum('status', [
                'completed',
                'draft',
                'cancelled',
            ])->default('completed')->change();
        });
    }
};
?>
