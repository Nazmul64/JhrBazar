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
        // ── Enhance Expense Categories Table ──
        Schema::table('expense_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('expense_categories', 'type')) {
                $table->string('type', 20)->default('expense')->after('name'); // expense, income, both
            }
            if (!Schema::hasColumn('expense_categories', 'description')) {
                $table->text('description')->nullable()->after('type');
            }
            if (!Schema::hasColumn('expense_categories', 'status')) {
                $table->boolean('status')->default(true)->after('is_active');
            }
        });

        // ── Create Accounts Ledgers Table (Cashbook & Income/Expense) ──
        if (!Schema::hasTable('accounts_ledgers')) {
            Schema::create('accounts_ledgers', function (Blueprint $table) {
                $table->id();
                $table->date('transaction_date');
                $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
                $table->string('title')->nullable();
                $table->decimal('income_amount', 12, 2)->nullable()->default(0.00);
                $table->decimal('expense_amount', 12, 2)->nullable()->default(0.00);
                $table->string('payment_account')->nullable()->default('Cash Drawer'); // Cash Drawer, Petty Cash, Bank, bKash
                $table->string('voucher_no')->nullable()->index();
                $table->string('document_file')->nullable();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_ledgers');

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn(['type', 'description', 'status']);
        });
    }
};
