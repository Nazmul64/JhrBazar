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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE seller_transactions MODIFY COLUMN `type` VARCHAR(50) NOT NULL DEFAULT 'earning'");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE seller_transactions MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE seller_transactions MODIFY COLUMN `type` ENUM('earning', 'withdrawal') NOT NULL DEFAULT 'earning'");
    }
};
