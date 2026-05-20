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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('allowances', 10, 2)->default(0.00);
            $table->decimal('bonuses', 10, 2)->default(0.00);
            $table->decimal('advances_deduction', 10, 2)->default(0.00);
            $table->decimal('total_deductions', 10, 2)->default(0.00);
            $table->decimal('net_salary', 10, 2);
            $table->enum('payment_status', ['Unpaid', 'Paid'])->default('Unpaid');
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable(); // Cash, Bank, Bkash, etc.
            $table->text('note')->nullable();
            $table->timestamps();

            // Unique key to prevent duplicate payroll generating for an employee in the same month
            $table->unique(['employee_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
