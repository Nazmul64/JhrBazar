<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerCategoryController extends Controller
{
    public function categories()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('seller.categories.index', compact('categories'));
    }

    public function subCategories()
    {
        $subcategories = SubCategory::orderBy('name', 'asc')->get();
        // Subcategories usually have categories linked, but the model showed BelongsToMany
        return view('seller.categories.sub_index', compact('subcategories'));
    }

    public function childCategories()
    {
        // Check if table exists to avoid crash
        if (\Schema::hasTable('child_categories')) {
            $childcategories = DB::table('child_categories')->orderBy('name', 'asc')->get();
        } else {
            $childcategories = collect(); // Empty collection if not found
        }
        return view('seller.categories.child_index', compact('childcategories'));
    }
}
