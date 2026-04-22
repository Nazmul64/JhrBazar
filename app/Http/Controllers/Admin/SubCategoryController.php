<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SubCategoryController extends Controller
{
    // ══════════════════════════════════════════
    //  INDEX
    // ══════════════════════════════════════════
    public function index()
    {
        $subCategories = SubCategory::with('categories')->latest()->get();
        return view('admin.subcategory.index', compact('subCategories'));
    }

    // ══════════════════════════════════════════
    //  CREATE
    // ══════════════════════════════════════════
    public function create()
    {
        // Use 1 instead of true — works regardless of DB driver / cast issue
        $categories = Category::where('is_active', 1)->orderBy('name')->get();
        return view('admin.subcategory.create', compact('categories'));
    }

    // ══════════════════════════════════════════
    //  STORE
    // ══════════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'category_ids'   => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'name'           => 'required|string|max:255|unique:sub_categories,name',
            'thumbnail'      => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $thumbnailPath = $this->uploadImage($request->file('thumbnail'));

        $sub = SubCategory::create([
            'name'      => $request->name,
            'thumbnail' => $thumbnailPath,
            'is_active' => 1,
        ]);

        $sub->categories()->sync($request->category_ids);

        return redirect()->route('admin.subcategory.index')
            ->with('success', 'Sub Category created successfully.');
    }

    // ══════════════════════════════════════════
    //  SHOW  (redirect to index)
    // ══════════════════════════════════════════
    public function show(string $id)
    {
        return redirect()->route('admin.subcategory.index');
    }

    // ══════════════════════════════════════════
    //  EDIT
    // ══════════════════════════════════════════
    public function edit(SubCategory $subcategory)
    {
        $categories         = Category::where('is_active', 1)->orderBy('name')->get();
        $selectedCategories = $subcategory->categories->pluck('id')->toArray();
        return view('admin.subcategory.edit',
            compact('subcategory', 'categories', 'selectedCategories'));
    }

    // ══════════════════════════════════════════
    //  UPDATE
    // ══════════════════════════════════════════
    public function update(Request $request, SubCategory $subcategory)
    {
        $request->validate([
            'category_ids'   => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'name'           => 'required|string|max:255|unique:sub_categories,name,' . $subcategory->id,
            'thumbnail'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = ['name' => $request->name];

        if ($request->hasFile('thumbnail')) {
            $this->deleteImage($subcategory->thumbnail);
            $data['thumbnail'] = $this->uploadImage($request->file('thumbnail'));
        }

        $subcategory->update($data);
        $subcategory->categories()->sync($request->category_ids);

        return redirect()->route('admin.subcategory.index')
            ->with('success', 'Sub Category updated successfully.');
    }

    // ══════════════════════════════════════════
    //  DESTROY
    // ══════════════════════════════════════════
    public function destroy(SubCategory $subcategory)
    {
        $this->deleteImage($subcategory->thumbnail);
        $subcategory->categories()->detach();
        $subcategory->delete();

        return redirect()->route('admin.subcategory.index')
            ->with('success', 'Sub Category deleted successfully.');
    }

    // ══════════════════════════════════════════
    //  TOGGLE STATUS
    // ══════════════════════════════════════════
    public function toggleStatus(SubCategory $subcategory)
    {
        $subcategory->update(['is_active' => !$subcategory->is_active]);
        return redirect()->back()->with('success', 'Status updated.');
    }

    // ══════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════
    private function uploadImage($file): string
    {
        $path = public_path('uploads/subcategory');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $fileName);
        return 'uploads/subcategory/' . $fileName;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
