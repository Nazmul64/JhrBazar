<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Brand;
use App\Models\Category;
use App\Models\LandingPage;
use App\Models\Page;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\GenaralSetting;

use Illuminate\Support\Facades\Cache;

class FrontendApiController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Return a consistent success JSON response.
     */
    private function success(mixed $data, string $key = 'data'): JsonResponse
    {
        return response()->json(['success' => true, $key => $data]);
    }

    /**
     * Format a product model/row into the standard API shape.
     * Works with both Eloquent models and stdClass DB rows.
     */
    private function formatProduct(mixed $p): array
    {
        $isModel   = $p instanceof Product;
        $gallery   = $isModel ? ($p->gallery_images ?? []) : (json_decode($p->gallery_images ?? '[]', true) ?? []);
        $baseUrl   = rtrim(config('app.url'), '/');
        $cacheBust = '?v=' . config('app.version', '1.0.0');

        $thumb = $p->thumbnail
            ? $baseUrl . '/' . ltrim($p->thumbnail, '/') . $cacheBust
            : null;

        $galleryUrls = collect($gallery)->map(
            fn($img) => $baseUrl . '/' . ltrim($img, '/')
        )->values()->all();

        $price         = (float) ($p->discount_price > 0 ? $p->discount_price : $p->selling_price);
        $originalPrice = (float) $p->selling_price;
        $discount      = $originalPrice > 0 && $p->discount_price > 0
            ? (int) round((($originalPrice - (float) $p->discount_price) / $originalPrice) * 100)
            : 0;

        return [
            'id'                => $p->id,
            'name'              => $p->name,
            'slug'              => $p->slug,
            'uid'               => 'admin_' . $p->id,
            'product_type'      => 'admin',
            'seller_id'         => null,
            'short_description' => $p->short_description ?? null,
            'thumbnail'         => $thumb,
            'price'             => $price,
            'discount_price'    => (float) ($p->discount_price ?? 0),
            'old_price'         => $originalPrice,
            'discount'          => $discount,
            'stock'             => (int) ($p->stock_quantity ?? 0),
            'sku'               => $p->sku ?? null,
            'brand'             => $isModel && $p->relationLoaded('brand') ? optional($p->brand)->name : null,
            'category'          => $isModel && $p->relationLoaded('category') ? optional($p->category)->name : null,
            'category_id'       => $p->category_id ?? null,
            'color'             => $p->color ? (is_array($p->color) ? $p->color : [$p->color]) : [],
            'size'              => $p->size  ? (is_array($p->size)  ? $p->size  : [$p->size])  : [],
            'unit'              => $p->unit  ?? null,
            'cash_on_delivery'  => (bool) ($p->cash_on_delivery ?? false),
            'online_payment'    => (bool) ($p->online_payment ?? false),
            'is_shipping_charge'=> (bool) ($p->is_shipping_charge ?? false),
            'avg_rating'        => (float) ($p->avg_rating ?? 0),
            'review_count'      => (int)  ($p->review_count ?? 0),
            'gallery'           => $galleryUrls,
        ];
    }

    /**
     * Build the base product query with common constraints.
     */
    private function activeProductQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Product::with(['category', 'brand'])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->withAvg('reviews as avg_rating', 'rating')
            ->withCount('reviews as review_count');
    }

    /**
     * Resolve a limit query parameter — accepts integer or "all".
     */
    private function resolveLimit(Request $request, int $default = 10): int|null
    {
        $limit = $request->query('limit', $default);
        if ($limit === 'all') return null;
        return (int) $limit;
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /home-data
    // ──────────────────────────────────────────────────────────────
    public function getHomeData(): JsonResponse
    {
        $data = Cache::remember('home_data_v2', now()->addMinutes(30), function () {
            $settings        = $this->buildSettings();
            $banners         = $this->buildBanners();
            $categories      = $this->buildCategories();
            $popularProducts = $this->buildProductList('is_popular', 12);
            $newArrivals     = $this->buildProductList('is_new_arrival', 12);
            $justForYou      = $this->buildProductList('is_just_for_you', 12);
            $digitalProducts = $this->buildDigitalProducts(12);
            $bestDeals       = $this->buildBestDeals(12);
            $topShops        = $this->buildTopShops(8);
            $allProducts     = $this->buildAllProducts(20);
            $recentReviews   = $this->buildRecentReviews(10);
            $frontendSections= $this->buildFrontendSections();

            return compact(
                'settings', 'banners', 'categories',
                'popularProducts', 'newArrivals', 'justForYou',
                'digitalProducts', 'bestDeals', 'topShops',
                'allProducts', 'recentReviews', 'frontendSections'
            );
        });

        return $this->success([
            'settings'         => $data['settings'],
            'banners'          => $data['banners'],
            'categories'       => $data['categories'],
            'popularProducts'  => $data['popularProducts'],
            'newArrivals'      => $data['newArrivals'],
            'justForYouProducts' => $data['justForYou'],
            'digitalProducts'  => $data['digitalProducts'],
            'bestDeals'        => $data['bestDeals'],
            'topShops'         => $data['topShops'],
            'allProducts'      => $data['allProducts'],
            'recentReviews'    => $data['recentReviews'],
            'frontendSections' => $data['frontendSections'],
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /settings
    // ──────────────────────────────────────────────────────────────
    public function getSettings(): JsonResponse
    {
        return $this->success($this->buildSettings());
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /banners
    // ──────────────────────────────────────────────────────────────
    public function getBanners(): JsonResponse
    {
        return $this->success($this->buildBanners());
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /categories
    //  GET /categories-with-sub
    // ──────────────────────────────────────────────────────────────
    public function getCategories(): JsonResponse
    {
        return $this->success($this->buildCategories());
    }

    public function getCategoriesWithSub(): JsonResponse
    {
        return $this->success($this->buildCategories());
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /category/{id}/name
    //  GET /subcategory/{id}/name
    // ──────────────────────────────────────────────────────────────
    public function getCategoryName(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        return response()->json(['success' => true, 'name' => $category->name]);
    }

    public function getSubCategoryName(int $id): JsonResponse
    {
        $sub = SubCategory::findOrFail($id);
        return response()->json(['success' => true, 'name' => $sub->name]);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /all-products
    // ──────────────────────────────────────────────────────────────
    public function getAllProducts(Request $request): JsonResponse
    {
        $limit    = $this->resolveLimit($request);
        $products = $this->buildAllProducts($limit);
        return $this->success($products);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /popular-products
    // ──────────────────────────────────────────────────────────────
    public function getPopularProducts(Request $request): JsonResponse
    {
        $limit    = $this->resolveLimit($request);
        $products = $this->buildProductList('is_popular', $limit);
        return $this->success($products);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /new-arrivals
    // ──────────────────────────────────────────────────────────────
    public function getNewArrivals(Request $request): JsonResponse
    {
        $limit    = $this->resolveLimit($request);
        $products = $this->buildProductList('is_new_arrival', $limit);
        return $this->success($products);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /just-for-you
    // ──────────────────────────────────────────────────────────────
    public function getJustForYouProducts(Request $request): JsonResponse
    {
        $limit    = $this->resolveLimit($request);
        $products = $this->buildProductList('is_just_for_you', $limit);
        return $this->success($products);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /digital-products
    // ──────────────────────────────────────────────────────────────
    public function getDigitalProducts(Request $request): JsonResponse
    {
        $limit    = $this->resolveLimit($request);
        $products = $this->buildDigitalProducts($limit);
        return $this->success($products);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /best-deals
    // ──────────────────────────────────────────────────────────────
    public function getBestDeals(Request $request): JsonResponse
    {
        $limit    = $this->resolveLimit($request);
        $products = $this->buildBestDeals($limit);
        return $this->success($products);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /top-shops
    // ──────────────────────────────────────────────────────────────
    public function getTopShops(): JsonResponse
    {
        return $this->success($this->buildTopShops());
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /shop/{seller_id}/products
    // ──────────────────────────────────────────────────────────────
    public function getProductsBySeller(int $seller_id): JsonResponse
    {
        // Seller products are outside the admin Product model scope.
        // Adjust model/table name if your project uses a SellerProduct model.
        $products = Product::with(['category', 'brand'])
            ->where('is_active', true)
            ->where('seller_id', $seller_id)
            ->withAvg('reviews as avg_rating', 'rating')
            ->withCount('reviews as review_count')
            ->latest()
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        return $this->success($products);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /shop/{seller_id}/reviews
    // ──────────────────────────────────────────────────────────────
    public function getReviewsBySeller(int $seller_id): JsonResponse
    {
        $reviews = Review::with('user:id,name,profile_image')
            ->where('seller_id', $seller_id)
            ->latest()
            ->get()
            ->map(fn($r) => $this->formatReview($r));

        return $this->success($reviews);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /products/search?q=
    // ──────────────────────────────────────────────────────────────
    public function searchProducts(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);
        $q = $request->query('q');

        $products = $this->activeProductQuery()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('short_description', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%");
            })
            ->latest()
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        return $this->success($products);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /products/category/{id}
    // ──────────────────────────────────────────────────────────────
    public function getProductsByCategory(int $id): JsonResponse
    {
        // Include products from all subcategories of this category
        $subIds = SubCategory::where('category_id', $id)->pluck('id');

        $products = $this->activeProductQuery()
            ->where(function ($q) use ($id, $subIds) {
                $q->where('category_id', $id)
                  ->orWhereIn('sub_category_id', $subIds);
            })
            ->latest()
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        return $this->success($products);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /products/subcategory/{id}
    // ──────────────────────────────────────────────────────────────
    public function getProductsBySubCategory(int $id): JsonResponse
    {
        $products = $this->activeProductQuery()
            ->where('sub_category_id', $id)
            ->latest()
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        return $this->success($products);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /product/{slug}
    // ──────────────────────────────────────────────────────────────
    public function getProductBySlug(string $slug): JsonResponse
    {
        $product = Product::with(['category', 'brand'])
            ->withAvg('reviews as avg_rating', 'rating')
            ->withCount('reviews as review_count')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $reviews = Review::with('user:id,name,profile_image')
            ->where('product_id', $product->id)
            ->where('product_type', 'admin')
            ->latest()
            ->get()
            ->map(fn($r) => $this->formatReview($r));

        $related = $this->activeProductQuery()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        $data = $this->formatProduct($product);
        $data['description']      = $product->description;
        $data['seller_name']      = optional(GenaralSetting::first())->shop_name ?? 'JHR Bazar';
        $data['seller_logo']      = null;
        $data['seller_rating']    = 5.0;
        $data['estimated_delivery'] = '2-5 days';
        $data['video']            = $product->video;
        $data['video_type']       = $product->video_type;
        $data['reviews']          = $reviews;
        $data['related']          = $related;

        return $this->success($data);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /product/{type}/{id}
    // ──────────────────────────────────────────────────────────────
    public function getProductDetails(string $type, int $id): JsonResponse
    {
        // Delegate admin products to slug resolver (by ID fallback)
        if ($type === 'admin') {
            $product = Product::with(['category', 'brand'])
                ->withAvg('reviews as avg_rating', 'rating')
                ->withCount('reviews as review_count')
                ->findOrFail($id);

            return $this->getProductBySlug($product->slug);
        }

        // For seller/digital product types, adjust to your seller product model
        return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /product/{type}/{id}/related
    // ──────────────────────────────────────────────────────────────
    public function getRelatedProducts(string $type, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $related = $this->activeProductQuery()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        return $this->success($related);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /footer-data
    // ──────────────────────────────────────────────────────────────
    public function getFooterData(): JsonResponse
    {
        $setting    = GenaralSetting::first();
        $categories = Category::where('is_active', true)->orderBy('name')->limit(8)->get(['id', 'name', 'slug']);

        return $this->success([
            'shop_name'      => $setting?->shop_name,
            'footer_logo'    => $setting?->footer_logo ? asset($setting->footer_logo) : null,
            'address'        => $setting?->address,
            'phone'          => $setting?->phone,
            'email'          => $setting?->email,
            'facebook'       => $setting?->facebook,
            'instagram'      => $setting?->instagram,
            'youtube'        => $setting?->youtube,
            'categories'     => $categories,
            'footer_text'    => $setting?->footer_text,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /blog-categories
    // ──────────────────────────────────────────────────────────────
    public function getBlogCategories(): JsonResponse
    {
        $categories = BlogCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return $this->success($categories);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /blogs
    // ──────────────────────────────────────────────────────────────
    public function getBlogs(Request $request): JsonResponse
    {
        $query = Blog::where('is_active', true)->latest();

        if ($request->filled('category')) {
            $query->where('blog_category_id', $request->category);
        }

        $limit = $this->resolveLimit($request, 12);
        $blogs = ($limit ? $query->limit($limit) : $query)
            ->get()
            ->map(fn($b) => [
                'id'          => $b->id,
                'title'       => $b->title,
                'slug'        => $b->slug,
                'thumbnail'   => $b->thumbnail ? asset($b->thumbnail) : null,
                'excerpt'     => $b->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($b->content ?? ''), 150),
                'category'    => optional($b->blogCategory)->name,
                'created_at'  => $b->created_at?->format('d M Y'),
            ]);

        return $this->success($blogs);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /blog/{slug}
    // ──────────────────────────────────────────────────────────────
    public function getBlogDetails(string $slug): JsonResponse
    {
        $blog = Blog::with('blogCategory')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return $this->success([
            'id'          => $blog->id,
            'title'       => $blog->title,
            'slug'        => $blog->slug,
            'content'     => $blog->content,
            'thumbnail'   => $blog->thumbnail ? asset($blog->thumbnail) : null,
            'category'    => optional($blog->blogCategory)->name,
            'category_id' => $blog->blog_category_id,
            'created_at'  => $blog->created_at?->format('d M Y'),
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /page/{slug}
    // ──────────────────────────────────────────────────────────────
    public function getPage(string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return $this->success([
            'id'      => $page->id,
            'title'   => $page->title,
            'slug'    => $page->slug,
            'content' => $page->content,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /about-company
    // ──────────────────────────────────────────────────────────────
    public function getAboutCompany(): JsonResponse
    {
        $page = Page::where('slug', 'about-company')
            ->orWhere('slug', 'about-us')
            ->first();

        return $this->success([
            'title'   => $page?->title ?? 'About Us',
            'content' => $page?->content ?? '',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /privacy-policy
    // ──────────────────────────────────────────────────────────────
    public function getPrivacyPolicy(): JsonResponse
    {
        $page = Page::where('slug', 'privacy-policy')->first();

        return $this->success([
            'title'   => $page?->title ?? 'Privacy Policy',
            'content' => $page?->content ?? '',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /landingpage/{slug}
    // ──────────────────────────────────────────────────────────────
    public function getLandingPageBySlug(string $slug): JsonResponse
    {
        $page = LandingPage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return $this->success([
            'id'       => $page->id,
            'title'    => $page->title,
            'slug'     => $page->slug,
            'sections' => $page->sections ?? [],
            'settings' => $page->settings ?? [],
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    //  PRIVATE BUILDER METHODS  (used by getHomeData & individual
    //  endpoints to avoid duplicating query logic)
    // ══════════════════════════════════════════════════════════════

    private function buildSettings(): array
    {
        $s = GenaralSetting::first();
        $baseUrl = rtrim(config('app.url'), '/');

        return [
            'id'                    => $s?->id,
            'shop_name'             => $s?->shop_name ?? 'JHR Bazar',
            'logo'                  => $s?->logo    ? $baseUrl . '/' . ltrim($s->logo,    '/') : null,
            'footer_logo'           => $s?->footer_logo ? $baseUrl . '/' . ltrim($s->footer_logo, '/') : null,
            'favicon'               => $s?->favicon  ? $baseUrl . '/' . ltrim($s->favicon, '/') : null,
            'top_rated_shops_status'=> (int) ($s?->top_rated_shops_status ?? 1),
        ];
    }

    private function buildBanners(): array
    {
        $baseUrl   = rtrim(config('app.url'), '/');
        $cacheBust = '?v=' . config('app.version', '1.0.0');

        return Banner::where('is_active', true)
            ->latest()
            ->get()
            ->map(fn($b) => [
                'id'           => $b->id,
                'image'        => $b->image ? $baseUrl . '/' . ltrim($b->image, '/') . $cacheBust : null,
                'for_own_shop' => (bool) $b->for_own_shop,
            ])
            ->values()
            ->all();
    }

    private function buildCategories(): array
    {
        $baseUrl   = rtrim(config('app.url'), '/');
        $cacheBust = '?v=' . config('app.version', '1.0.0');

        return Category::with(['subCategories' => fn($q) => $q->where('is_active', true)->orderBy('name')])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($cat) use ($baseUrl, $cacheBust) {
                return [
                    'id'             => $cat->id,
                    'name'           => $cat->name,
                    'slug'           => $cat->slug,
                    'thumbnail'      => $cat->thumbnail
                        ? $baseUrl . '/' . ltrim($cat->thumbnail, '/') . $cacheBust
                        : null,
                    'sub_categories' => $cat->subCategories->map(fn($s) => [
                        'id'          => $s->id,
                        'category_id' => $cat->id,
                        'name'        => $s->name,
                        'slug'        => $s->slug,
                        'thumbnail'   => $s->thumbnail
                            ? $baseUrl . '/' . ltrim($s->thumbnail, '/') . $cacheBust
                            : null,
                    ])->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Fetch products filtered by a boolean flag column.
     *
     * @param  string   $flag   Column name (e.g. "is_popular")
     * @param  int|null $limit  null = no limit
     */
    private function buildProductList(string $flag, ?int $limit = 12): array
    {
        $query = $this->activeProductQuery()->where($flag, true)->latest();

        if ($limit) $query->limit($limit);

        return $query->get()->map(fn($p) => $this->formatProduct($p))->values()->all();
    }

    private function buildDigitalProducts(?int $limit = 12): array
    {
        // Adjust if you have a separate DigitalProduct model.
        // For now, pull products flagged as digital if the column exists,
        // otherwise fall back to an empty list rather than crash.
        try {
            $query = $this->activeProductQuery()
                ->where('is_digital', true)
                ->latest();

            if ($limit) $query->limit($limit);
            return $query->get()->map(fn($p) => $this->formatProduct($p))->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function buildBestDeals(?int $limit = 12): array
    {
        $query = $this->activeProductQuery()
            ->where('is_flash_sale', true)
            ->orWhere(fn($q) => $q->where('discount_price', '>', 0))
            ->latest();

        if ($limit) $query->limit($limit);

        return $query->get()->map(fn($p) => $this->formatProduct($p))->values()->all();
    }

    private function buildTopShops(?int $limit = 8): array
    {
        try {
            return Shop::where('is_active', true)
                ->withAvg('reviews as rating', 'rating')
                ->withCount('products as item_count')
                ->orderByDesc('rating')
                ->limit($limit)
                ->get()
                ->map(fn($s) => [
                    'id'          => $s->id,
                    'seller_id'   => $s->seller_id ?? $s->id,
                    'name'        => $s->name,
                    'logo'        => $s->logo   ? asset($s->logo)   : null,
                    'banner'      => $s->banner ? asset($s->banner) : null,
                    'item_count'  => $s->item_count,
                    'rating'      => number_format((float) ($s->rating ?? 0), 1),
                    'description' => $s->description ?? null,
                ])
                ->values()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function buildAllProducts(?int $limit = 20): array
    {
        $query = $this->activeProductQuery()->latest();
        if ($limit) $query->limit($limit);
        return $query->get()->map(fn($p) => $this->formatProduct($p))->values()->all();
    }

    private function buildRecentReviews(?int $limit = 10): array
    {
        return Review::with('user:id,name,profile_image')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn($r) => $this->formatReview($r))
            ->values()
            ->all();
    }

    /**
     * Build custom frontend sections (products grouped by label stored on
     * the product's `frontend_sections` JSON column).
     */
    private function buildFrontendSections(): array
    {
        $products = Product::where('is_active', true)
            ->whereNotNull('frontend_sections')
            ->get(['id', 'name', 'slug', 'thumbnail', 'selling_price', 'discount_price',
                   'stock_quantity', 'cash_on_delivery', 'online_payment', 'frontend_sections']);

        $grouped = [];
        foreach ($products as $product) {
            $sections = is_array($product->frontend_sections)
                ? $product->frontend_sections
                : json_decode($product->frontend_sections ?? '[]', true);

            foreach ((array) $sections as $sectionTitle) {
                $grouped[$sectionTitle][] = $this->formatProduct($product);
            }
        }

        return collect($grouped)
            ->map(fn($items, $title) => ['title' => $title, 'products' => $items])
            ->values()
            ->all();
    }

    private function formatReview(Review $r): array
    {
        return [
            'id'           => $r->id,
            'product_id'   => $r->product_id,
            'product_type' => $r->product_type ?? 'admin',
            'rating'       => (int) $r->rating,
            'comment'      => $r->comment,
            'created_at'   => $r->created_at?->diffForHumans(),
            'user'         => [
                'id'            => optional($r->user)->id,
                'name'          => optional($r->user)->name,
                'profile_image' => optional($r->user)->profile_image,
            ],
        ];
    }
}
