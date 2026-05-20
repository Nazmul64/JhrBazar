<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expenditure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenditureController extends Controller
{
    /**
     * Display a listing of company expenditures with monthly breakdown.
     */
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $category = $request->get('category');

        $query = Expenditure::with('creator')
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        if ($category) {
            $query->where('category', $category);
        }

        $expenditures = $query->orderBy('date', 'desc')->get();

        // Calculate sum statistics for cards
        $totalExpenditure = $expenditures->sum('amount');
        
        // Sums by core categories
        $salaryExpense = $expenditures->where('category', 'Salaries')->sum('amount');
        $officeExpense = $expenditures->where('category', 'Office Rent')->sum('amount');
        $billsExpense = $expenditures->where('category', 'Utility Bills')->sum('amount');
        $otherExpense = $expenditures->whereNotIn('category', ['Salaries', 'Office Rent', 'Utility Bills'])->sum('amount');

        $categories = ['Utility Bills', 'Office Rent', 'Tea/Snacks', 'Marketing', 'Salaries', 'Others'];

        return view('admin.expenditure.index', compact(
            'expenditures',
            'month',
            'year',
            'category',
            'categories',
            'totalExpenditure',
            'salaryExpense',
            'officeExpense',
            'billsExpense',
            'otherExpense'
        ));
    }

    /**
     * Store a newly created expenditure.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        Expenditure::create($validated);

        return redirect()->route('admin.expenditure.index', ['month' => date('m', strtotime($validated['date'])), 'year' => date('Y', strtotime($validated['date']))])
            ->with('success', 'Expenditure logged successfully.');
    }

    /**
     * Update an expenditure log.
     */
    public function update(Request $request, Expenditure $expenditure)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'description' => 'nullable|string',
        ]);

        $expenditure->update($validated);

        return redirect()->route('admin.expenditure.index', ['month' => date('m', strtotime($validated['date'])), 'year' => date('Y', strtotime($validated['date']))])
            ->with('success', 'Expenditure updated successfully.');
    }

    /**
     * Remove an expenditure log.
     */
    public function destroy(Expenditure $expenditure)
    {
        $expenditure->delete();
        return redirect()->back()
            ->with('success', 'Expenditure deleted successfully.');
    }
}
