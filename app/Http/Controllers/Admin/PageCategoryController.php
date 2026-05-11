<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageCategoryController extends Controller
{
    public function index()
    {
        $categories = PageCategory::latest()->get();
        return view('admin.page_category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.page_category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|integer',
        ]);

        PageCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status,
        ]);

        return redirect()->route('admin.page_categories.index')->with('success', 'Page Category created successfully.');
    }

    public function edit(string $id)
    {
        $category = PageCategory::findOrFail($id);
        return view('admin.page_category.edit', compact('category'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|integer',
        ]);

        $category = PageCategory::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status,
        ]);

        return redirect()->route('admin.page_categories.index')->with('success', 'Page Category updated successfully.');
    }

    public function destroy(string $id)
    {
        $category = PageCategory::findOrFail($id);
        $category->delete();
        return redirect()->route('admin.page_categories.index')->with('success', 'Page Category deleted successfully.');
    }

    public function toggleStatus(Request $request, string $id)
    {
        $category = PageCategory::findOrFail($id);
        $category->status = $category->status == 1 ? 0 : 1;
        $category->save();

        return response()->json(['success' => true, 'status' => $category->status]);
    }
}
