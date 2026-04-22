<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::latest()->get();
        return view('admin.size.index', compact('sizes'));
    }

    public function create()
    {
        return redirect()->route('admin.sizes.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name',
        ]);

        Size::create([
            'name'      => $request->name,
            'is_active' => false,
        ]);

        return redirect()->route('admin.sizes.index')
            ->with('success', 'Size created successfully.');
    }

    public function edit(Size $size)
    {
        return view('admin.size.edit', compact('size'));
    }

    public function update(Request $request, Size $size)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name,' . $size->id,
        ]);

        $size->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.sizes.index')
            ->with('success', 'Size updated successfully.');
    }

    public function destroy(Size $size)
    {
        $size->delete();
        return redirect()->route('admin.sizes.index')
            ->with('success', 'Size deleted successfully.');
    }

    public function toggleStatus(Size $size)
    {
        $size->update(['is_active' => !$size->is_active]);
        return redirect()->back()->with('success', 'Status updated.');
    }
}
