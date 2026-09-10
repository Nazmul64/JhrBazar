<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountsLedger;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountsLedgerController extends Controller
{
    /**
     * Display accounts ledger list with KPI cards and filters.
     */
    public function index(Request $request)
    {
        $query = AccountsLedger::with(['category', 'creator'])->latest('transaction_date')->latest('id');

        // Specific Date
        if ($request->filled('date')) {
            $query->whereDate('transaction_date', $request->date);
        }

        // Date range filter
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate   = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;

        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        } elseif ($startDate) {
            $query->where('transaction_date', '>=', $startDate->format('Y-m-d'));
        } elseif ($endDate) {
            $query->where('transaction_date', '<=', $endDate->format('Y-m-d'));
        }

        // Month filter
        if ($request->filled('month')) {
            $query->whereMonth('transaction_date', $request->month);
        }

        // Year filter
        if ($request->filled('year')) {
            $query->whereYear('transaction_date', $request->year);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Transaction Type filter (income / expense)
        if ($request->filled('transaction_type') && in_array($request->transaction_type, ['income', 'expense'])) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Payment Method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search filter
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('voucher_no', 'like', "%{$s}%")
                  ->orWhere('reference', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $ledgers = $query->paginate(25)->withQueryString();

        // Summary Calculations:
        // 1. Filtered summary
        $filteredIncome  = (clone $query)->sum('income_amount');
        $filteredExpense = (clone $query)->sum('expense_amount');
        $filteredBalance = $filteredIncome - $filteredExpense;

        // 2. Current Month summary
        $currentMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentMonthEnd   = Carbon::now()->endOfMonth()->format('Y-m-d');
        $monthIncome  = AccountsLedger::whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd])->sum('income_amount');
        $monthExpense = AccountsLedger::whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd])->sum('expense_amount');
        $monthBalance = $monthIncome - $monthExpense;

        // 3. Till Date (All-time) summary
        $allTimeIncome  = AccountsLedger::sum('income_amount');
        $allTimeExpense = AccountsLedger::sum('expense_amount');
        $allTimeBalance = $allTimeIncome - $allTimeExpense;

        // Categories for select
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.accounts.index', compact(
            'ledgers',
            'categories',
            'filteredIncome',
            'filteredExpense',
            'filteredBalance',
            'monthIncome',
            'monthExpense',
            'monthBalance',
            'allTimeIncome',
            'allTimeExpense',
            'allTimeBalance'
        ));
    }

    /**
     * Store a new income / expense entry.
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_type' => 'nullable|in:income,expense',
            'category_id'      => 'nullable|exists:expense_categories,id',
            'title'            => 'nullable|string|max:255',
            'amount'           => 'nullable|numeric|min:0',
            'income_amount'    => 'nullable|numeric|min:0',
            'expense_amount'   => 'nullable|numeric|min:0',
            'payment_method'   => 'nullable|string|max:50',
            'transaction_date' => 'required|date',
            'voucher_no'       => 'nullable|string|max:100',
            'reference'        => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'document'         => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx|max:5120',
        ]);

        $type = $request->transaction_type ?: 'expense';
        $incomeAmount = 0.00;
        $expenseAmount = 0.00;

        if ($request->filled('income_amount') || $request->filled('expense_amount')) {
            $incomeAmount  = (float) ($request->income_amount ?? 0);
            $expenseAmount = (float) ($request->expense_amount ?? 0);
            $type = $incomeAmount > 0 ? 'income' : 'expense';
        } elseif ($request->filled('amount')) {
            $amt = (float) $request->amount;
            if ($type === 'income') {
                $incomeAmount = $amt;
            } else {
                $expenseAmount = $amt;
            }
        }

        if ($incomeAmount <= 0 && $expenseAmount <= 0) {
            return back()->withInput()->withErrors(['amount' => 'Please enter a valid income or expense amount greater than 0.']);
        }

        $docPath = null;
        if ($request->hasFile('document')) {
            $docPath = $request->file('document')->store('accounts_documents', 'public');
        }

        $category = $request->category_id ? ExpenseCategory::find($request->category_id) : null;
        $title = $request->title ?: ($category ? $category->name : ($type === 'income' ? 'Income Entry' : 'Expense Entry'));
        $voucherNo = $request->voucher_no ?: AccountsLedger::generateVoucherNo($type === 'income' ? 'INC' : 'EXP');

        AccountsLedger::create([
            'transaction_type' => $type,
            'category_id'      => $request->category_id,
            'voucher_no'       => $voucherNo,
            'title'            => $title,
            'description'      => $request->description,
            'income_amount'    => $incomeAmount,
            'expense_amount'   => $expenseAmount,
            'payment_method'   => $request->payment_method ?? 'Cash',
            'reference'        => $request->reference,
            'document_file'    => $docPath,
            'transaction_date' => $request->transaction_date,
            'created_by'       => Auth::id() ?? 1,
        ]);

        return redirect()->route('admin.accounts.index')->with('success', ucfirst($type) . ' entry recorded successfully!');
    }

    /**
     * Delete an accounts ledger entry.
     */
    public function destroy($id)
    {
        $ledger = AccountsLedger::findOrFail($id);
        if ($ledger->document_file) {
            Storage::disk('public')->delete($ledger->document_file);
        }
        $ledger->delete();

        return redirect()->route('admin.accounts.index')->with('success', 'Accounts entry deleted successfully!');
    }

    /**
     * List and manage Categories.
     */
    public function categories()
    {
        $categories = ExpenseCategory::withCount('accountsLedgers')->latest()->get();
        return view('admin.accounts.categories', compact('categories'));
    }

    /**
     * Store new Category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:expense,income,both',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);

        ExpenseCategory::create([
            'name'        => $request->name,
            'type'        => $request->type,
            'color'       => $request->color ?? '#3b82f6',
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Category created successfully!');
    }

    /**
     * Update Category.
     */
    public function updateCategory(Request $request, $id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:expense,income,both',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'nullable|boolean',
        ]);

        $category->update([
            'name'        => $request->name,
            'type'        => $request->type,
            'color'       => $request->color ?? $category->color,
            'description' => $request->description,
            'is_active'   => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return back()->with('success', 'Category updated successfully!');
    }

    /**
     * Delete Category.
     */
    public function destroyCategory($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        if ($category->accountsLedgers()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated ledger transactions!');
        }
        $category->delete();
        return back()->with('success', 'Category deleted successfully!');
    }

    /**
     * Printable Financial Statement / Cashbook.
     */
    public function printReport(Request $request)
    {
        $query = AccountsLedger::with(['category', 'creator'])->orderBy('transaction_date', 'asc')->orderBy('id', 'asc');

        if ($request->filled('date')) {
            $query->whereDate('transaction_date', $request->date);
        }

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate   = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;

        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        } elseif ($startDate) {
            $query->where('transaction_date', '>=', $startDate->format('Y-m-d'));
        } elseif ($endDate) {
            $query->where('transaction_date', '<=', $endDate->format('Y-m-d'));
        }

        if ($request->filled('month')) {
            $query->whereMonth('transaction_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('transaction_date', $request->year);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('transaction_type') && in_array($request->transaction_type, ['income', 'expense'])) {
            $query->where('transaction_type', $request->transaction_type);
        }

        $ledgers = $query->get();

        $totalIncome  = $ledgers->sum('income_amount');
        $totalExpense = $ledgers->sum('expense_amount');
        $netBalance   = $totalIncome - $totalExpense;

        return view('admin.accounts.report', compact('ledgers', 'totalIncome', 'totalExpense', 'netBalance', 'startDate', 'endDate'));
    }
}
