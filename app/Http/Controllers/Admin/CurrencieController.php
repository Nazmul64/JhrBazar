<?php
// app/Http/Controllers/Admin/CurrencieController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currencie;
use Illuminate\Http\Request;

class CurrencieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currencies = Currencie::latest()->get();
        return view('admin.currency.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.currency.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:50|unique:currencies,name',
            'symbol' => 'required|string|max:10',
            'rate'   => 'required|numeric|min:0',
        ]);

        Currencie::create([
            'name'       => strtoupper(trim($validated['name'])),
            'symbol'     => trim($validated['symbol']),
            'rate'       => $validated['rate'],
            'is_default' => false,
            'status'     => true,
        ]);

        return redirect()->route('admin.currencies.index')
            ->with('success', 'Currency created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $currency = Currencie::findOrFail($id);
        return view('admin.currency.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $currency = Currencie::findOrFail($id);

        $validated = $request->validate([
            'name'   => 'required|string|max:50|unique:currencies,name,' . $currency->id,
            'symbol' => 'required|string|max:10',
            'rate'   => 'required|numeric|min:0',
        ]);

        $currency->update([
            'name'   => strtoupper(trim($validated['name'])),
            'symbol' => trim($validated['symbol']),
            'rate'   => $validated['rate'],
        ]);

        return redirect()->route('admin.currencies.index')
            ->with('success', 'Currency updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $currency = Currencie::findOrFail($id);

        if ($currency->is_default) {
            return redirect()->route('admin.currencies.index')
                ->with('error', 'Cannot delete the default currency.');
        }

        $currency->delete();

        return redirect()->route('admin.currencies.index')
            ->with('success', 'Currency deleted successfully.');
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleStatus(string $id)
    {
        $currency = Currencie::findOrFail($id);
        $currency->update(['status' => !$currency->status]);

        return redirect()->route('admin.currencies.index')
            ->with('success', 'Currency status updated.');
    }
}
