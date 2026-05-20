<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerDigitalProduct;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


class SellerDigitalProductController extends Controller
{
    public function index()
    {
        $products = SellerDigitalProduct::where('seller_id', Auth::id())->latest()->get();
        return view('seller.digital_product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $subcategories = SubCategory::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();
        return view('seller.digital_product.create', compact('categories', 'subcategories', 'brands'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'digital_file' => 'nullable|file|max:51200', // 50MB max
        ];

        if ($request->video_type === 'upload') {
            $rules['video_file'] = 'nullable|file|mimes:mp4,avi,mov,wmv|max:20480';
        } else {
            $rules['video_link'] = 'nullable|string|url';
        }
        $request->validate($rules);

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

        SellerDigitalProduct::create([
            'seller_id'             => Auth::id(),
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
            'digital_file'          => $digitalFilePath,
            'license_keys'          => array_filter($request->license_keys ?? [], fn($k) => !empty(trim($k))),
            'video_type'            => $request->video_type,
            'video'                 => $videoVal,
            'meta_title'            => $request->meta_title,
            'meta_description'      => $request->meta_description,
            'meta_keywords'         => $request->meta_keywords,
            'is_shipping_charge'    => $request->has('is_shipping_charge'),
            'is_active'             => true,
        ]);

        Cache::forget('homepage_data_v2');
        Cache::forget('home_data_v2');
        return redirect()->route('seller.digital_product.index')->with('success', 'Digital Product Created Successfully');

    }

    public function show($id)
    {
        $product = SellerDigitalProduct::where('seller_id', Auth::id())->with(['category', 'brand'])->findOrFail($id);
        return view('seller.digital_product.show', compact('product'));
    }

    public function edit($id)
    {
        $product = SellerDigitalProduct::where('seller_id', Auth::id())->findOrFail($id);
        $categories = Category::orderBy('name', 'asc')->get();
        $subcategories = SubCategory::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();
        return view('seller.digital_product.edit', compact('product', 'categories', 'subcategories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $product = SellerDigitalProduct::where('seller_id', Auth::id())->findOrFail($id);
        
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        if ($request->video_type === 'upload') {
            $rules['video_file'] = 'nullable|file|mimes:mp4,avi,mov,wmv|max:20480';
        } else {
            $rules['video_link'] = 'nullable|string|url';
        }
        $request->validate($rules);

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
        if ($request->video_type !== 'upload' && $product->video_type === 'upload' && $product->video) {
            $this->deleteFile($product->video);
        }

        if ($request->video_type === 'upload') {
            if ($request->hasFile('video_file')) {
                if ($product->video_type === 'upload' && $product->video) $this->deleteFile($product->video);
                $videoVal = $this->saveFile($request->file('video_file'));
            }
        } else {
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
            'is_shipping_charge'    => $request->has('is_shipping_charge'),
        ]);

        Cache::forget('homepage_data_v2');
        Cache::forget('home_data_v2');
        return redirect()->route('seller.digital_product.index')->with('success', 'Digital Product Updated Successfully');

    }

    public function destroy($id)
    {
        $product = SellerDigitalProduct::where('seller_id', Auth::id())->findOrFail($id);
        $this->deleteFile($product->thumbnail);
        if($product->additional_thumbnails) foreach($product->additional_thumbnails as $img) $this->deleteFile($img);
        $this->deleteFile($product->digital_file);
        if($product->video_type === 'upload') $this->deleteFile($product->video);
        $product->delete();
        Cache::forget('homepage_data_v2');
        Cache::forget('home_data_v2');
        return redirect()->back()->with('success', 'Product Deleted Successfully');

    }

    public function toggleStatus($id)
    {
        $product = SellerDigitalProduct::where('seller_id', Auth::id())->findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
        Cache::forget('homepage_data_v2');
        Cache::forget('home_data_v2');
        return redirect()->back()->with('success', 'Status Updated');

    }

    public function barcode(Request $request, $id)
    {
        $product = SellerDigitalProduct::where('seller_id', Auth::id())->findOrFail($id);
        $print_quantity = $request->input('quantity', 4);
        return view('seller.digital_product.barcode', compact('product', 'print_quantity'));
    }

    private function saveFile($file): string
    {
        $dir = public_path('uploads/sellrdegitalproduct');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return 'uploads/sellrdegitalproduct/' . $filename;
    }

    private function deleteFile($path)
    {
        if ($path && File::exists(public_path($path))) File::delete(public_path($path));
    }
}
