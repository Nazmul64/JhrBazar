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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('house_rent_allowance', 12, 2)->default(0.00);
            $table->decimal('medical_allowance', 12, 2)->default(0.00);
            $table->decimal('conveyance_allowance', 12, 2)->default(0.00);
            $table->decimal('provident_fund', 12, 2)->default(0.00);
            $table->decimal('professional_tax', 12, 2)->default(0.00);
            $table->decimal('extra_incentives', 12, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'house_rent_allowance',
                'medical_allowance',
                'conveyance_allowance',
                'provident_fund',
                'professional_tax',
                'extra_incentives'
            ]);
        });
    }
};
