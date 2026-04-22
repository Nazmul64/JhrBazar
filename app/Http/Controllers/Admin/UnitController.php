<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->get();
        return view('admin.unit.index', compact('units'));
    }

    public function create()
    {
        return redirect()->route('admin.units.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
        ]);

        Unit::create([
            'name'      => $request->name,
            'is_active' => false,
        ]);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit created successfully.');
    }

    public function edit(Unit $unit)
    {
        return view('admin.unit.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
        ]);

        $unit->update([
            'name' => $request->name,
        ]);

        // ✅ FIX: admin.unit.index → admin.units.index
        return redirect()->route('admin.units.index')
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('admin.units.index')
            ->with('success', 'Unit deleted successfully.');
    }

    public function toggleStatus(Unit $unit)
    {
        $unit->update(['is_active' => !$unit->is_active]);
        return redirect()->back()->with('success', 'Status updated.');
    }
}
