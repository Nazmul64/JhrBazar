<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Models\SubCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductControllerController extends Controller
{
    // ─────────────────────────────────────────────
    //  Helper: ফাইল সেভ করে uploads/product তে
    //  return করে:  "uploads/product/filename.ext"
    // ─────────────────────────────────────────────
    private function saveFile($file): string
    {
        $dir = public_path('uploads/product');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return 'uploads/product/' . $filename;
    }

    // ─────────────────────────────────────────────
    //  Helper: ফাইল delete করে public/ থেকে
    // ─────────────────────────────────────────────
    private function deleteFile(?string $path): void
    {
        if (!$path) return;
        $full = public_path($path);
        if (file_exists($full)) {
            unlink($full);
        }
    }

    // ──────────────────────────────────────────────────────────────
    //  Index
    // ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();

        return view('admin.product.index', compact('products'));
    }

    // ──────────────────────────────────────────────────────────────
    //  Create
    // ──────────────────────────────────────────────────────────────
    public function create()
    {
        $categories    = Category::orderBy('name')->get();
        $brands        = Brand::orderBy('name')->get();
        $colors        = Color::orderBy('name')->get();
        $units         = Unit::orderBy('name')->get();
        $sizes         = Size::orderBy('name')->get();
        $subCategories = collect();

        return view('admin.product.create', compact(
            'categories', 'brands', 'colors', 'units', 'sizes', 'subCategories'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    //  Store
    // ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'short_description' => 'required|string|max:1000',
            'description'       => 'nullable|string',
            'category_id'       => 'required|exists:categories,id',
            'sub_category_id'   => 'nullable|exists:sub_categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'color'             => 'nullable|string|max:100',
            'unit'              => 'nullable|string|max:100',
            'size'              => 'nullable|string|max:100',
            'sku'               => 'required|string|unique:products,sku',
            'buying_price'      => 'required|numeric|min:0',
            'selling_price'     => 'required|numeric|min:0',
            'discount_price'    => 'nullable|numeric|min:0',
            'stock_quantity'    => 'nullable|integer|min:0',
            'thumbnail'         => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'video_type'        => 'nullable|in:file,url,youtube',
            'video_file'        => 'nullable|file|mimes:mp4,avi,mov,wmv|max:51200',
            'video_url'         => 'nullable|string|max:500',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string',
            'meta_keywords'     => 'nullable|string',
        ]);

        // ── Thumbnail → public/uploads/product/ ──
        $thumbnailPath = $this->saveFile($request->file('thumbnail'));

        // ── Gallery → public/uploads/product/ ──
        $galleryPaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $this->saveFile($file);
            }
        }

        // ── Video → public/uploads/product/ ──
        $videoValue = null;
        $videoType  = $request->video_type ?? 'file';

        if ($videoType === 'file' && $request->hasFile('video_file')) {
            $videoValue = $this->saveFile($request->file('video_file'));
        } elseif (in_array($videoType, ['url', 'youtube'])) {
            $videoValue = $request->video_url;
        }

        Product::create([
            'name'              => $request->name,
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'category_id'       => $request->category_id,
            'sub_category_id'   => $request->sub_category_id ?: null,
            'brand_id'          => $request->brand_id ?: null,
            'color'             => $request->color,
            'unit'              => $request->unit,
            'size'              => $request->size,
            'sku'               => $request->sku,
            'barcode'           => $request->sku,
            'buying_price'      => $request->buying_price,
            'selling_price'     => $request->selling_price,
            'discount_price'    => $request->discount_price ?? 0,
            'stock_quantity'    => $request->stock_quantity ?? 0,
            'thumbnail'         => $thumbnailPath,
            'gallery_images'    => $galleryPaths ?: null,
            'video_type'        => $videoType,
            'video'             => $videoValue,
            'meta_title'        => $request->meta_title,
            'meta_description'  => $request->meta_description,
            'meta_keywords'     => $request->meta_keywords,
            'is_active'         => true,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    // ──────────────────────────────────────────────────────────────
    //  Edit
    // ──────────────────────────────────────────────────────────────
    public function edit(Product $product)
    {
        $categories    = Category::orderBy('name')->get();
        $brands        = Brand::orderBy('name')->get();
        $colors        = Color::orderBy('name')->get();
        $units         = Unit::orderBy('name')->get();
        $sizes         = Size::orderBy('name')->get();

        $subCategories = $product->category_id
            ? $this->subCategoriesFor($product->category_id)
            : collect();

        return view('admin.product.edit', compact(
            'product', 'categories', 'brands', 'colors', 'units', 'sizes', 'subCategories'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    //  Update
    // ──────────────────────────────────────────────────────────────
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'short_description' => 'required|string|max:1000',
            'description'       => 'nullable|string',
            'category_id'       => 'required|exists:categories,id',
            'sub_category_id'   => 'nullable|exists:sub_categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'color'             => 'nullable|string|max:100',
            'unit'              => 'nullable|string|max:100',
            'size'              => 'nullable|string|max:100',
            'sku'               => 'required|string|unique:products,sku,' . $product->id,
            'barcode'           => 'nullable|string|max:255',
            'buying_price'      => 'required|numeric|min:0',
            'selling_price'     => 'required|numeric|min:0',
            'discount_price'    => 'nullable|numeric|min:0',
            'stock_quantity'    => 'nullable|integer|min:0',
            'thumbnail'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_images'     => 'nullable|array',
            'video_type'        => 'nullable|in:file,url,youtube',
            'video_file'        => 'nullable|file|mimes:mp4,avi,mov,wmv|max:51200',
            'video_url'         => 'nullable|string|max:500',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string',
            'meta_keywords'     => 'nullable|string',
        ]);

        // ── Thumbnail ──
        $thumbnailPath = $product->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $this->deleteFile($product->thumbnail);
            $thumbnailPath = $this->saveFile($request->file('thumbnail'));
        }

        // ── Gallery: remove marked images + add new ──
        $existingGallery = $product->gallery_images ?? [];
        $toRemove        = $request->input('remove_images', []);

        if (!empty($toRemove)) {
            foreach ($toRemove as $path) {
                $this->deleteFile($path);
            }
            $existingGallery = array_values(
                array_filter($existingGallery, fn($p) => !in_array($p, $toRemove))
            );
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $existingGallery[] = $this->saveFile($file);
            }
        }

        // ── Video ──
        $videoValue = $product->video;
        $videoType  = $request->video_type ?? 'file';

        if ($videoType === 'file' && $request->hasFile('video_file')) {
            if ($product->video_type === 'file') {
                $this->deleteFile($product->video);
            }
            $videoValue = $this->saveFile($request->file('video_file'));
        } elseif (in_array($videoType, ['url', 'youtube'])) {
            $videoValue = $request->video_url;
        }

        // ── Barcode ──
        $barcode = $request->filled('barcode') ? $request->barcode : $product->barcode;

        $product->update([
            'name'              => $request->name,
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'category_id'       => $request->category_id,
            'sub_category_id'   => $request->sub_category_id ?: null,
            'brand_id'          => $request->brand_id ?: null,
            'color'             => $request->color,
            'unit'              => $request->unit,
            'size'              => $request->size,
            'sku'               => $request->sku,
            'barcode'           => $barcode,
            'buying_price'      => $request->buying_price,
            'selling_price'     => $request->selling_price,
            'discount_price'    => $request->discount_price ?? 0,
            'stock_quantity'    => $request->stock_quantity ?? 0,
            'thumbnail'         => $thumbnailPath,
            'gallery_images'    => $existingGallery ?: null,
            'video_type'        => $videoType,
            'video'             => $videoValue,
            'meta_title'        => $request->meta_title,
            'meta_description'  => $request->meta_description,
            'meta_keywords'     => $request->meta_keywords,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    // ──────────────────────────────────────────────────────────────
    //  Destroy
    // ──────────────────────────────────────────────────────────────
    public function destroy(Product $product)
    {
        $this->deleteFile($product->thumbnail);

        if ($product->gallery_images) {
            foreach ($product->gallery_images as $img) {
                $this->deleteFile($img);
            }
        }

        if ($product->video_type === 'file') {
            $this->deleteFile($product->video);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    // ──────────────────────────────────────────────────────────────
    //  Toggle Status
    // ──────────────────────────────────────────────────────────────
    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        return redirect()->back()->with('success', 'Product status updated.');
    }

    // ──────────────────────────────────────────────────────────────
    //  Barcode View
    // ──────────────────────────────────────────────────────────────
    public function barcode(Product $product)
    {
        return view('admin.product.barcode', compact('product'));
    }

    // ──────────────────────────────────────────────────────────────
    //  AJAX: Sub-Categories
    //  Route: GET admin/products/subcategories/{categoryId}
    // ──────────────────────────────────────────────────────────────
    public function getSubCategories($categoryId)
    {
        $categoryId = (int) $categoryId;
        if (!$categoryId) return response()->json([]);
        return response()->json($this->subCategoriesFor($categoryId)->values());
    }

    // ──────────────────────────────────────────────────────────────
    //  Private: smart sub-category fetcher (3 fallback steps)
    // ──────────────────────────────────────────────────────────────
    private function subCategoriesFor(int $categoryId)
    {
        try {
            if (Schema::hasTable('category_sub_category')) {
                $rows = DB::table('sub_categories as sc')
                    ->join('category_sub_category as pivot', 'sc.id', '=', 'pivot.sub_category_id')
                    ->where('pivot.category_id', $categoryId)
                    ->where('sc.is_active', true)
                    ->orderBy('sc.name')
                    ->select('sc.id', 'sc.name')
                    ->get();
                if ($rows->isNotEmpty()) return $rows;
            }
        } catch (\Throwable $e) {}

        try {
            if (Schema::hasColumn('sub_categories', 'category_id')) {
                $rows = SubCategory::where('category_id', $categoryId)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name']);
                if ($rows->isNotEmpty()) return $rows;
            }
        } catch (\Throwable $e) {}

        return SubCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }
}
