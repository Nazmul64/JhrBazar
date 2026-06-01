<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('salary_advances')) {
            DB::statement("ALTER TABLE salary_advances MODIFY COLUMN status ENUM('Pending', 'Approved', 'Rejected', 'Completed') DEFAULT 'Pending'");
            DB::statement("ALTER TABLE salary_advances MODIFY COLUMN paid_status ENUM('Unpaid', 'Paid', 'Partial') DEFAULT 'Unpaid'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('salary_advances')) {
            DB::statement("ALTER TABLE salary_advances MODIFY COLUMN status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending'");
            DB::statement("ALTER TABLE salary_advances MODIFY COLUMN paid_status ENUM('Unpaid', 'Paid') DEFAULT 'Unpaid'");
        }
    }
};
