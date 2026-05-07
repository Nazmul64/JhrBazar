<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalProduct;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DigitalProductController extends Controller
{
    public function index()
    {
        $products = DigitalProduct::latest()->get();
        return view('admin.digital_product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $brands = Brand::all();
        return view('admin.digital_product.create', compact('categories', 'subcategories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $thumbnailPath = $this->saveFile($request->file('thumbnail'));
        
        $additionalThumbs = [];
        if ($request->hasFile('additional_thumbnails')) {
            foreach ($request->file('additional_thumbnails') as $file) {
                $additionalThumbs[] = $this->saveFile($file);
            }
        }

        $digitalFilePath = null;
        if ($request->hasFile('digital_file')) {
            $digitalFilePath = $this->saveFile($request->file('digital_file'));
        }

        $videoVal = null;
        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            $videoVal = $this->saveFile($request->file('video_file'));
        } elseif ($request->video_type !== 'upload') {
            $videoVal = $request->video_link;
        }

        DigitalProduct::create([
            'name'                  => $request->name,
            'short_description'     => $request->short_description,
            'description'           => $request->description,
            'category_id'           => $request->category_id,
            'sub_category_id'       => $request->sub_category_id,
            'brand_id'              => $request->brand_id,
            'sku'                   => $request->sku ?? 'DP-'.strtoupper(Str::random(8)),
            'buying_price'          => $request->buying_price,
            'selling_price'         => $request->selling_price,
            'discount_price'        => $request->discount_price ?? 0,
            'stock_quantity'        => $request->stock_quantity ?? 0,
            'thumbnail'             => $thumbnailPath,
            'additional_thumbnails' => $additionalThumbs,
            'digital_file'          => $digitalFilePath,
            'license_keys'          => array_filter($request->license_keys ?? [], fn($k) => !empty(trim($k))),
            'video_type'            => $request->video_type,
            'video'                 => $videoVal,
            'meta_title'            => $request->meta_title,
            'meta_description'      => $request->meta_description,
            'meta_keywords'         => $request->meta_keywords,
            'is_active'             => true,
        ]);

        return redirect()->route('admin.digital_product.index')->with('success', 'Digital Product Created Successfully');
    }

    public function edit($id)
    {
        $product = DigitalProduct::findOrFail($id);
        $categories = Category::all();
        $category = Category::find($product->category_id);
        $subcategories = $category ? $category->subCategories : collect();
        $brands = Brand::all();
        return view('admin.digital_product.edit', compact('product', 'categories', 'subcategories', 'brands'));
    }


    public function update(Request $request, $id)
    {
        $product = DigitalProduct::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
        ]);

        $thumbnailPath = $product->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $this->deleteFile($product->thumbnail);
            $thumbnailPath = $this->saveFile($request->file('thumbnail'));
        }

        $additionalThumbs = $product->additional_thumbnails ?? [];
        if ($request->hasFile('additional_thumbnails')) {
            foreach ($additionalThumbs as $old) $this->deleteFile($old);
            $additionalThumbs = [];
            foreach ($request->file('additional_thumbnails') as $file) {
                $additionalThumbs[] = $this->saveFile($file);
            }
        }

        $digitalFilePath = $product->digital_file;
        if ($request->hasFile('digital_file')) {
            $this->deleteFile($product->digital_file);
            $digitalFilePath = $this->saveFile($request->file('digital_file'));
        }

        $videoVal = $product->video;
        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            if ($product->video_type === 'upload') $this->deleteFile($product->video);
            $videoVal = $this->saveFile($request->file('video_file'));
        } elseif ($request->video_type !== 'upload') {
            $videoVal = $request->video_link;
        }

        $product->update([
            'name'                  => $request->name,
            'short_description'     => $request->short_description,
            'description'           => $request->description,
            'category_id'           => $request->category_id,
            'sub_category_id'       => $request->sub_category_id,
            'brand_id'              => $request->brand_id,
            'sku'                   => $request->sku,
            'buying_price'          => $request->buying_price,
            'selling_price'         => $request->selling_price,
            'discount_price'        => $request->discount_price ?? 0,
            'stock_quantity'        => $request->stock_quantity ?? 0,
            'thumbnail'             => $thumbnailPath,
            'additional_thumbnails' => $additionalThumbs,
            'digital_file'          => $digitalFilePath,
            'license_keys'          => array_filter($request->license_keys ?? [], fn($k) => !empty(trim($k))),
            'video_type'            => $request->video_type,
            'video'                 => $videoVal,
            'meta_title'            => $request->meta_title,
            'meta_description'      => $request->meta_description,
            'meta_keywords'         => $request->meta_keywords,
        ]);

        return redirect()->route('admin.digital_product.index')->with('success', 'Digital Product Updated Successfully');
    }

    public function destroy($id)
    {
        $product = DigitalProduct::findOrFail($id);
        $this->deleteFile($product->thumbnail);
        if($product->additional_thumbnails) foreach($product->additional_thumbnails as $img) $this->deleteFile($img);
        $this->deleteFile($product->digital_file);
        if($product->video_type === 'upload') $this->deleteFile($product->video);
        $product->delete();
        return redirect()->back()->with('success', 'Product Deleted Successfully');
    }

    public function toggleStatus($id)
    {
        $product = DigitalProduct::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
        return redirect()->back()->with('success', 'Status Updated');
    }

    public function getSubCategories($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category) return response()->json([]);
        
        $subcategories = $category->subCategories; // Corrected relationship name
        return response()->json($subcategories);
    }



    public function barcode(Request $request, $id)
    {
        $product = DigitalProduct::findOrFail($id);
        $print_quantity = $request->input('quantity', 4);
        return view('admin.digital_product.barcode', compact('product', 'print_quantity'));
    }

    public function show($id)
    {
        $product = DigitalProduct::with(['category', 'brand'])->findOrFail($id);
        return view('admin.digital_product.show', compact('product'));
    }

    private function saveFile($file): string
    {
        $dir = public_path('uploads/digitalproduct');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return 'uploads/digitalproduct/' . $filename;
    }

    private function deleteFile($path)
    {
        if ($path && File::exists(public_path($path))) File::delete(public_path($path));
    }
}
