<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductBrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->get();
        return view('admin.productbrand.index', compact('brands'));
    }

    public function create()
    {
        return redirect()->route('admin.productbrands.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
        ]);

        Brand::create([
            'name'      => $request->name,
            'is_active' => true,
        ]);

        return redirect()->route('admin.productbrands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $productbrand)
    {
        return view('admin.productbrand.edit', compact('productbrand'));
    }

    public function update(Request $request, Brand $productbrand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $productbrand->id,
        ]);

        $productbrand->update([
            'name'      => $request->name,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.productbrands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $productbrand)
    {
        $productbrand->delete();

        return redirect()->route('admin.productbrands.index')
            ->with('success', 'Brand deleted successfully.');
    }

    public function toggleStatus(Brand $productbrand)
    {
        $productbrand->update(['is_active' => !$productbrand->is_active]);
        return redirect()->back()->with('success', 'Status updated.');
    }
}
