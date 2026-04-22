<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    // ══════════════════════════════════════════
    //  INDEX
    // ══════════════════════════════════════════
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.category.index', compact('categories'));
    }

    // ══════════════════════════════════════════
    //  CREATE
    // ══════════════════════════════════════════
    public function create()
    {
        return view('admin.category.create');
    }

    // ══════════════════════════════════════════
    //  STORE
    // ══════════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'thumbnail'   => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string|max:1000',
        ]);

        $thumbnailPath = $this->uploadImage($request->file('thumbnail'));

        Category::create([
            'name'        => $request->name,
            'thumbnail'   => $thumbnailPath,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    // ══════════════════════════════════════════
    //  SHOW  (not used — redirect to index)
    // ══════════════════════════════════════════
    public function show(string $id)
    {
        return redirect()->route('admin.categories.index');
    }

    // ══════════════════════════════════════════
    //  EDIT
    // ══════════════════════════════════════════
    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    // ══════════════════════════════════════════
    //  UPDATE
    // ══════════════════════════════════════════
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string|max:1000',
        ]);

        $data = [
            'name'        => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('thumbnail')) {
            $this->deleteImage($category->thumbnail);
            $data['thumbnail'] = $this->uploadImage($request->file('thumbnail'));
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    // ══════════════════════════════════════════
    //  DESTROY
    // ══════════════════════════════════════════
    public function destroy(Category $category)
    {
        $this->deleteImage($category->thumbnail);
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    // ══════════════════════════════════════════
    //  TOGGLE STATUS  (extra route)
    // ══════════════════════════════════════════
    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        return redirect()->back()->with('success', 'Status updated.');
    }

    // ══════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════
    private function uploadImage($file): string
    {
        $path = public_path('uploads/category');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $fileName);
        return 'uploads/category/' . $fileName;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
