<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProduct;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Unit;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


class SellerProductController extends Controller
{
    private function saveFile($file): string
    {
        $dir = public_path('uploads/sellerproduct');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return 'uploads/sellerproduct/' . $filename;
    }

    private function deleteFile(?string $path): void
    {
        if (!$path) return;
        $full = public_path($path);
        if (file_exists($full)) {
            unlink($full);
        }
    }

    public function index()
    {
        $products = SellerProduct::where('seller_id', Auth::id())
            ->with(['category', 'brand'])
            ->latest()
            ->get();
        return view('seller.product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $brands = Brand::all();
        $colors = Color::all();
        $units = Unit::all();
        $sizes = Size::all();

        return view('seller.product.create', compact('categories', 'subcategories', 'brands', 'colors', 'units', 'sizes'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
        ];

        if ($request->video_type === 'upload') {
            $rules['video_file'] = 'nullable|file|mimes:mp4,avi,mov,wmv|max:20480';
        } else {
            $rules['video_link'] = 'nullable|string|url';
        }
        $request->validate($rules);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $this->saveFile($request->file('thumbnail'));
        }

        $galleryPaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $this->saveFile($file);
            }
        }

        $videoVal = null;
        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            $videoVal = $this->saveFile($request->file('video_file'));
        } elseif ($request->video_type !== 'upload') {
            $videoVal = $request->video_link;
        }

        SellerProduct::create([
            'seller_id'         => Auth::id(),
            'name'              => $request->name,
            'slug'              => Str::slug($request->name) . '-' . Str::random(5),
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'category_id'       => $request->category_id,
            'sub_category_id'   => $request->sub_category_id,
            'brand_id'          => $request->brand_id,
            'color'             => $request->color,
            'unit'              => $request->unit,
            'size'              => $request->size,
            'sku'               => $request->sku,
            'buying_price'      => $request->buying_price,
            'selling_price'     => $request->selling_price,
            'discount_price'    => $request->discount_price ?? 0,
            'stock_quantity'    => $request->stock_quantity ?? 0,
            'thumbnail'         => $thumbnailPath,
            'gallery_images'    => $galleryPaths,
            'video_type'        => $request->video_type,
            'video'             => $videoVal,
            'meta_title'        => $request->meta_title,
            'meta_description'  => $request->meta_description,
            'meta_keywords'     => $request->meta_keywords,
            'is_new_arrival'    => $request->has('is_new_arrival'),
            'is_best_seller'    => $request->has('is_best_seller'),
            'is_hot_product'    => $request->has('is_hot_product'),
            'is_flash_sale'     => $request->has('is_flash_sale'),
            'is_just_for_you'   => $request->has('is_just_for_you'),
            'is_popular'        => $request->has('is_popular'),
            'cash_on_delivery'  => $request->has('cash_on_delivery'),
            'online_payment'    => $request->has('online_payment'),
            'is_shipping_charge' => $request->has('is_shipping_charge'),
            'is_active'         => true,
        ]);

        Cache::forget('homepage_data_v2');
        return redirect()->route('seller.product.index')->with('success', 'Product Created Successfully');

    }

    public function edit($id)
    {
        $product = SellerProduct::where('seller_id', Auth::id())->findOrFail($id);
        
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $brands = Brand::all();
        $colors = Color::all();
        $units = Unit::all();
        $sizes = Size::all();

        return view('seller.product.edit', compact('product', 'categories', 'subcategories', 'brands', 'colors', 'units', 'sizes'));
    }

    public function update(Request $request, $id)
    {
        $product = SellerProduct::where('seller_id', Auth::id())->findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
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

        $galleryPaths = $product->gallery_images ?? [];
        if ($request->hasFile('gallery_images')) {
            if (is_array($product->gallery_images)) {
                foreach ($product->gallery_images as $oldPath) {
                    $this->deleteFile($oldPath);
                }
            }
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $this->saveFile($file);
            }
        }

        $videoVal = $product->video;
        // If type changed from upload to link, delete old file
        if ($request->video_type !== 'upload' && $product->video_type === 'upload' && $product->video) {
            $this->deleteFile($product->video);
        }

        if ($request->video_type === 'upload') {
            if ($request->hasFile('video_file')) {
                if ($product->video_type === 'upload' && $product->video) {
                    $this->deleteFile($product->video);
                }
                $videoVal = $this->saveFile($request->file('video_file'));
            } else {
                // Keep existing file if any
                $videoVal = $product->video_type === 'upload' ? $product->video : null;
            }
        } else {
            $videoVal = $request->video_link;
        }

        $product->update([
            'name'              => $request->name,
            'slug'              => Str::slug($request->name) . '-' . Str::random(5),
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'category_id'       => $request->category_id,
            'sub_category_id'   => $request->sub_category_id,
            'brand_id'          => $request->brand_id,
            'color'             => $request->color,
            'unit'              => $request->unit,
            'size'              => $request->size,
            'sku'               => $request->sku,
            'buying_price'      => $request->buying_price,
            'selling_price'     => $request->selling_price,
            'discount_price'    => $request->discount_price ?? 0,
            'stock_quantity'    => $request->stock_quantity ?? 0,
            'thumbnail'         => $thumbnailPath,
            'gallery_images'    => $galleryPaths,
            'video_type'        => $request->video_type,
            'video'             => $videoVal,
            'meta_title'        => $request->meta_title,
            'meta_description'  => $request->meta_description,
            'meta_keywords'     => $request->meta_keywords,
            'is_new_arrival'    => $request->has('is_new_arrival'),
            'is_best_seller'    => $request->has('is_best_seller'),
            'is_hot_product'    => $request->has('is_hot_product'),
            'is_flash_sale'     => $request->has('is_flash_sale'),
            'is_just_for_you'   => $request->has('is_just_for_you'),
            'is_popular'        => $request->has('is_popular'),
            'cash_on_delivery'  => $request->has('cash_on_delivery'),
            'online_payment'    => $request->has('online_payment'),
            'is_shipping_charge' => $request->has('is_shipping_charge'),
        ]);

        Cache::forget('homepage_data_v2');
        return redirect()->route('seller.product.index')->with('success', 'Product Updated Successfully');

    }

    public function destroy($id)
    {
        $product = SellerProduct::where('seller_id', Auth::id())->findOrFail($id);
        $this->deleteFile($product->thumbnail);
        $this->deleteFile($product->additional_thumbnail);
        $product->delete();

        Cache::forget('homepage_data_v2');
        return redirect()->back()->with('success', 'Product Deleted Successfully');

    }

    public function toggleStatus($id)
    {
        $product = SellerProduct::where('seller_id', Auth::id())->findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        Cache::forget('homepage_data_v2');
        return redirect()->back()->with('success', 'Product Status Updated');

    }

    public function show($id)
    {
        $product = SellerProduct::where('seller_id', Auth::id())
            ->with(['category', 'brand'])
            ->findOrFail($id);
            
        return view('seller.product.show', compact('product'));
    }

    public function barcode(Request $request, $id)
    {
        $product = SellerProduct::where('seller_id', Auth::id())->findOrFail($id);
        $print_quantity = $request->input('quantity', 4);
        
        return view('seller.product.barcode', compact('product', 'print_quantity'));
    }
}
