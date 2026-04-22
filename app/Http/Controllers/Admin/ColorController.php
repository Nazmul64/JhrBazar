<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::latest()->get();
        return view('admin.color.index', compact('colors'));
    }

    public function create()
    {
        return redirect()->route('admin.colors.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:colors,name',
            'color_code' => 'required|string|max:20',
        ]);

        Color::create([
            'name'       => $request->name,
            'color_code' => $request->color_code,
            'is_active'  => true,
        ]);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Color created successfully.');
    }

    public function edit(Color $color)
    {
        return view('admin.color.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:colors,name,' . $color->id,
            'color_code' => 'required|string|max:20',
        ]);

        $color->update([
            'name'       => $request->name,
            'color_code' => $request->color_code,
        ]);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Color updated successfully.');
    }

    public function destroy(Color $color)
    {
        $color->delete();
        return redirect()->route('admin.colors.index')
            ->with('success', 'Color deleted successfully.');
    }

    public function toggleStatus(Color $color)
    {
        $color->update(['is_active' => !$color->is_active]);
        return redirect()->back()->with('success', 'Status updated.');
    }
}
