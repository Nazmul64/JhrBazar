<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('salary_advances')) {
            return;
        }

        Schema::table('salary_advances', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_advances', 'installments')) {
                $table->integer('installments')->default(1)->after('amount');
            }
            if (!Schema::hasColumn('salary_advances', 'per_installment')) {
                $table->decimal('per_installment', 10, 2)->default(0)->after('installments');
            }
            if (!Schema::hasColumn('salary_advances', 'request_date')) {
                $table->date('request_date')->nullable()->after('per_installment');
            }
            if (!Schema::hasColumn('salary_advances', 'deducted_amount')) {
                $table->decimal('deducted_amount', 12, 2)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('salary_advances', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('paid_status');
            }
            if (!Schema::hasColumn('salary_advances', 'approved_date')) {
                $table->date('approved_date')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('salary_advances', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('approved_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('salary_advances')) {
            return;
        }

        Schema::table('salary_advances', function (Blueprint $table) {
            if (Schema::hasColumn('salary_advances', 'admin_note')) {
                $table->dropColumn('admin_note');
            }
            if (Schema::hasColumn('salary_advances', 'approved_date')) {
                $table->dropColumn('approved_date');
            }
            if (Schema::hasColumn('salary_advances', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('salary_advances', 'deducted_amount')) {
                $table->dropColumn('deducted_amount');
            }
            if (Schema::hasColumn('salary_advances', 'request_date')) {
                $table->dropColumn('request_date');
            }
            if (Schema::hasColumn('salary_advances', 'per_installment')) {
                $table->dropColumn('per_installment');
            }
            if (Schema::hasColumn('salary_advances', 'installments')) {
                $table->dropColumn('installments');
            }
        });
    }
};
