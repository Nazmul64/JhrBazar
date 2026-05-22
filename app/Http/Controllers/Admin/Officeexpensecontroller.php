<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficeExpense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfficeExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = OfficeExpense::with('category', 'addedBy')->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('expense_date', $request->month)
                  ->whereYear('expense_date', $request->year);
        }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('expense_date', [$request->from_date, $request->to_date]);
        }

        $expenses   = $query->paginate(20)->withQueryString();
        $categories = ExpenseCategory::where('is_active', true)->get();

        // Monthly Summary
        $month = $request->get('month', date('m'));
        $year  = $request->get('year', date('Y'));

        $totalThisMonth = OfficeExpense::whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        $totalThisYear = OfficeExpense::whereYear('expense_date', $year)->sum('amount');

        // Category-wise breakdown
        $categoryBreakdown = OfficeExpense::select('category_id', DB::raw('SUM(amount) as total'))
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->groupBy('category_id')
            ->with('category')
            ->get();

        return view('admin.hrm.expense.index', compact(
            'expenses', 'categories', 'totalThisMonth',
            'totalThisYear', 'categoryBreakdown', 'month', 'year'
        ));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('admin.hrm.expense.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'    => 'required|exists:expense_categories,id',
            'title'          => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'expense_date'   => 'required|date',
            'payment_method' => 'required|in:Cash,Bank,bKash,Nagad,Other',
            'paid_by'        => 'nullable|string|max:255',
            'reference'      => 'nullable|string|max:255',
            'note'           => 'nullable|string',
        ]);

        $data = $request->only([
            'category_id', 'title', 'amount', 'expense_date',
            'payment_method', 'paid_by', 'reference', 'note'
        ]);
        $data['added_by'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('expenses', 'public');
            $data['attachment'] = $path;
        }

        OfficeExpense::create($data);

        return redirect()->route('admin.hrm.expense.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function edit($id)
    {
        $expense    = OfficeExpense::findOrFail($id);
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('admin.hrm.expense.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $expense = OfficeExpense::findOrFail($id);
        $request->validate([
            'category_id'    => 'required|exists:expense_categories,id',
            'title'          => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'expense_date'   => 'required|date',
            'payment_method' => 'required|in:Cash,Bank,bKash,Nagad,Other',
        ]);

        $data = $request->only([
            'category_id', 'title', 'amount', 'expense_date',
            'payment_method', 'paid_by', 'reference', 'note'
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('expenses', 'public');
        }

        $expense->update($data);

        return redirect()->route('admin.hrm.expense.index')
            ->with('success', 'Expense updated.');
    }

    public function destroy($id)
    {
        OfficeExpense::findOrFail($id)->delete();
        return back()->with('success', 'Expense deleted.');
    }

    // ── Expense Categories ──────────────────────────────────────────────────

    public function categoryIndex()
    {
        $categories = ExpenseCategory::withCount('expenses')->get();
        return view('admin.hrm.expense.categories', compact('categories'));
    }

    public function categoryStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100', 'color' => 'nullable|string']);
        ExpenseCategory::create(['name' => $request->name, 'color' => $request->color ?? '#6366f1']);
        return back()->with('success', 'Category added.');
    }

    public function categoryDestroy($id)
    {
        ExpenseCategory::findOrFail($id)->delete();
        return back()->with('success', 'Category deleted.');
    }
}
