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
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->double('working_hours', 8, 2)->nullable();
            $table->integer('late_minutes')->default(0);
            $table->string('device_ip')->nullable();
            $table->string('location_coordinates')->nullable();
            $table->string('shift_name')->default('Day Shift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'clock_in',
                'clock_out',
                'working_hours',
                'late_minutes',
                'device_ip',
                'location_coordinates',
                'shift_name'
            ]);
        });
    }
};
