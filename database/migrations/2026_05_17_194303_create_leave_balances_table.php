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
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('casual_leave_quota')->default(14);
            $table->integer('casual_leave_used')->default(0);
            $table->integer('sick_leave_quota')->default(10);
            $table->integer('sick_leave_used')->default(0);
            $table->integer('annual_leave_quota')->default(20);
            $table->integer('annual_leave_used')->default(0);
            $table->integer('year')->default((int)date('Y'));
            $table->timestamps();

            $table->unique(['employee_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
