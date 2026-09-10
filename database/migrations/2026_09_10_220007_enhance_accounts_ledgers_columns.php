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
        Schema::table('accounts_ledgers', function (Blueprint $table) {
            if (!Schema::hasColumn('accounts_ledgers', 'transaction_type')) {
                $table->string('transaction_type', 20)->default('expense')->after('id');
            }
            if (!Schema::hasColumn('accounts_ledgers', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('transaction_type');
            }
            if (!Schema::hasColumn('accounts_ledgers', 'payment_method')) {
                $table->string('payment_method', 50)->default('cash')->after('expense_amount');
            }
            if (!Schema::hasColumn('accounts_ledgers', 'reference')) {
                $table->string('reference')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('accounts_ledgers', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('accounts_ledgers', 'running_balance')) {
                $table->decimal('running_balance', 14, 2)->default(0)->after('expense_amount');
            }
            if (!Schema::hasColumn('accounts_ledgers', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
