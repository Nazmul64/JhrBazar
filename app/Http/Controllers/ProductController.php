<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Base product query — active, in-stock, with relations & aggregates.
     */
    private function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Product::with(['category:id,name,slug', 'brand:id,name'])
            ->withAvg('reviews as avg_rating', 'rating')
            ->withCount('reviews as review_count')
            ->where('is_active', true);
    }

    /**
     * Format a single Product model into the standard API response shape.
     */
    private function format(Product $p, bool $full = false): array
    {
        $baseUrl   = rtrim(config('app.url'), '/');
        $cacheBust = '?v=' . config('app.version', '1.0.0');

        // ── Thumbnail ──
        $thumb = $p->thumbnail
            ? $baseUrl . '/' . ltrim($p->thumbnail, '/') . $cacheBust
            : null;

        // ── Gallery ──
        $gallery = collect($p->gallery_images ?? [])
            ->map(fn($img) => $baseUrl . '/' . ltrim($img, '/'))
            ->values()
            ->all();

        // ── Pricing ──
        $sellingPrice  = (float) $p->selling_price;
        $discountPrice = (float) ($p->discount_price ?? 0);
        $activePrice   = $discountPrice > 0 ? $discountPrice : $sellingPrice;
        $discountPct   = ($discountPrice > 0 && $sellingPrice > 0)
            ? (int) round((($sellingPrice - $discountPrice) / $sellingPrice) * 100)
            : 0;

        $base = [
            'id'               => $p->id,
            'uid'              => 'admin_' . $p->id,
            'product_type'     => 'admin',
            'name'             => $p->name,
            'slug'             => $p->slug,
            'sku'              => $p->sku,
            'thumbnail'        => $thumb,
            'price'            => $activePrice,
            'selling_price'    => $sellingPrice,
            'discount_price'   => $discountPrice,
            'discount_percent' => $discountPct,
            'stock'            => (int) ($p->stock_quantity ?? 0),
            'in_stock'         => ($p->stock_quantity ?? 0) > 0,
            'category'         => optional($p->category)->name,
            'category_id'      => $p->category_id,
            'category_slug'    => optional($p->category)->slug,
            'sub_category_id'  => $p->sub_category_id,
            'brand'            => optional($p->brand)->name,
            'brand_id'         => $p->brand_id,
            'color'            => $p->color ? (is_array($p->color) ? $p->color : [$p->color]) : [],
            'size'             => $p->size  ? (is_array($p->size)  ? $p->size  : [$p->size])  : [],
            'unit'             => $p->unit,
            'cash_on_delivery' => (bool) ($p->cash_on_delivery  ?? false),
            'online_payment'   => (bool) ($p->online_payment    ?? false),
            'is_shipping_charge'=> (bool)($p->is_shipping_charge ?? false),
            'avg_rating'       => round((float) ($p->avg_rating   ?? 0), 1),
            'review_count'     => (int)  ($p->review_count ?? 0),
            'is_new_arrival'   => (bool) ($p->is_new_arrival  ?? false),
            'is_best_seller'   => (bool) ($p->is_best_seller  ?? false),
            'is_hot_product'   => (bool) ($p->is_hot_product  ?? false),
            'is_flash_sale'    => (bool) ($p->is_flash_sale   ?? false),
            'is_popular'       => (bool) ($p->is_popular      ?? false),
            'is_just_for_you'  => (bool) ($p->is_just_for_you ?? false),
            'created_at'       => $p->created_at?->toDateTimeString(),
        ];

        // ── Full detail (show endpoint) ──
        if ($full) {
            $base['short_description'] = $p->short_description;
            $base['description']       = $p->description;
            $base['gallery']           = $gallery;
            $base['video']             = $p->video;
            $base['video_type']        = $p->video_type;
            $base['barcode']           = $p->barcode;
            $base['buying_price']      = (float) ($p->buying_price ?? 0);
            $base['meta_title']        = $p->meta_title;
            $base['meta_description']  = $p->meta_description;
            $base['meta_keywords']     = $p->meta_keywords;
        }

        return $base;
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /api/v1/products
    // ──────────────────────────────────────────────────────────────
    /**
     * Query Parameters (all optional):
     *  - search       : string   — name / SKU fuzzy search
     *  - category_id  : int      — filter by category
     *  - sub_category_id: int    — filter by subcategory
     *  - brand_id     : int      — filter by brand
     *  - min_price    : numeric  — minimum active price
     *  - max_price    : numeric  — maximum active price
     *  - in_stock     : bool     — 1 = only in-stock items
     *  - is_new_arrival / is_best_seller / is_hot_product
     *    / is_flash_sale / is_popular / is_just_for_you : bool flags
     *  - sort         : string   — latest | price_asc | price_desc | rating | name_asc
     *  - limit        : int|"all" — default 15
     *  - page         : int      — for pagination (ignored when limit=all)
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'search'          => 'nullable|string|min:1|max:255',
            'category_id'     => 'nullable|integer|exists:categories,id',
            'sub_category_id' => 'nullable|integer|exists:sub_categories,id',
            'brand_id'        => 'nullable|integer|exists:brands,id',
            'min_price'       => 'nullable|numeric|min:0',
            'max_price'       => 'nullable|numeric|min:0',
            'in_stock'        => 'nullable|boolean',
            'sort'            => 'nullable|string|in:latest,price_asc,price_desc,rating,name_asc',
            'limit'           => 'nullable',
            'page'            => 'nullable|integer|min:1',
            // Boolean flag filters
            'is_new_arrival'  => 'nullable|boolean',
            'is_best_seller'  => 'nullable|boolean',
            'is_hot_product'  => 'nullable|boolean',
            'is_flash_sale'   => 'nullable|boolean',
            'is_popular'      => 'nullable|boolean',
            'is_just_for_you' => 'nullable|boolean',
        ]);

        $query = $this->baseQuery();

        // ── Search ──
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku',  'like', "%{$s}%")
                  ->orWhere('short_description', 'like', "%{$s}%")
            );
        }

        // ── Category / Subcategory ──
        if ($request->filled('category_id')) {
            $catId  = (int) $request->category_id;
            $subIds = SubCategory::where('category_id', $catId)->pluck('id');
            $query->where(fn($q) =>
                $q->where('category_id', $catId)
                  ->orWhereIn('sub_category_id', $subIds)
            );
        }

        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', (int) $request->sub_category_id);
        }

        // ── Brand ──
        if ($request->filled('brand_id')) {
            $query->where('brand_id', (int) $request->brand_id);
        }

        // ── Price Range (use discount_price if set, else selling_price) ──
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where(function ($inner) use ($request) {
                    // Has a discount price — filter on that
                    $inner->where('discount_price', '>', 0);
                    if ($request->filled('min_price')) {
                        $inner->where('discount_price', '>=', (float) $request->min_price);
                    }
                    if ($request->filled('max_price')) {
                        $inner->where('discount_price', '<=', (float) $request->max_price);
                    }
                })->orWhere(function ($inner) use ($request) {
                    // No discount — filter on selling_price
                    $inner->where(fn($x) => $x->where('discount_price', 0)->orWhereNull('discount_price'));
                    if ($request->filled('min_price')) {
                        $inner->where('selling_price', '>=', (float) $request->min_price);
                    }
                    if ($request->filled('max_price')) {
                        $inner->where('selling_price', '<=', (float) $request->max_price);
                    }
                });
            });
        }

        // ── In-Stock Filter ──
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // ── Boolean Flag Filters ──
        $flags = [
            'is_new_arrival', 'is_best_seller', 'is_hot_product',
            'is_flash_sale',  'is_popular',     'is_just_for_you',
        ];

        foreach ($flags as $flag) {
            if ($request->has($flag) && $request->boolean($flag)) {
                $query->where($flag, true);
            }
        }

        // ── Sorting ──
        match ($request->input('sort', 'latest')) {
            'price_asc'  => $query->orderByRaw('IF(discount_price > 0, discount_price, selling_price) ASC'),
            'price_desc' => $query->orderByRaw('IF(discount_price > 0, discount_price, selling_price) DESC'),
            'rating'     => $query->orderByDesc('avg_rating'),
            'name_asc'   => $query->orderBy('name'),
            default      => $query->latest(),
        };

        // ── Pagination / Limit ──
        $limitRaw = $request->input('limit', 15);

        if ($limitRaw === 'all') {
            $products = $query->get()->map(fn($p) => $this->format($p));
            return response()->json([
                'success' => true,
                'total'   => $products->count(),
                'data'    => $products,
            ]);
        }

        $limit    = max(1, (int) $limitRaw);
        $paginated = $query->paginate($limit);

        return response()->json([
            'success'      => true,
            'total'        => $paginated->total(),
            'per_page'     => $paginated->perPage(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'next_page_url'=> $paginated->nextPageUrl(),
            'prev_page_url'=> $paginated->previousPageUrl(),
            'data'         => collect($paginated->items())->map(fn($p) => $this->format($p)),
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /api/v1/products/{id}
    // ──────────────────────────────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $product = $this->baseQuery()
            ->with(['reviews.user:id,name,profile_image'])
            ->findOrFail($id);

        if (!$product->is_active) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        // ── Full formatted product ──
        $data = $this->format($product, full: true);

        // ── Reviews ──
        $data['reviews'] = $product->reviews
            ->map(fn($r) => [
                'id'         => $r->id,
                'rating'     => (int) $r->rating,
                'comment'    => $r->comment,
                'created_at' => $r->created_at?->diffForHumans(),
                'user'       => [
                    'id'            => optional($r->user)->id,
                    'name'          => optional($r->user)->name,
                    'profile_image' => optional($r->user)->profile_image,
                ],
            ])
            ->values()
            ->all();

        // ── Related Products (same category, limit 8) ──
        $data['related'] = $this->baseQuery()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($p) => $this->format($p))
            ->values()
            ->all();

        return response()->json(['success' => true, 'data' => $data]);
    }
}
