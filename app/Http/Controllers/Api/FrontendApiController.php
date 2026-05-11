<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProduct;
use App\Models\Banner;
use App\Models\DigitalProduct;
use App\Models\SellerDigitalProduct;
use App\Models\Shop;
use App\Models\GenaralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontendApiController extends Controller
{
    /**
     * Consolidated home data for high performance.
     */
    public function getHomeData()
    {
        // Increased cache time to 5 minutes for performance
        return Cache::remember('homepage_data', 300, function () {
            $settings = GenaralSetting::first();
            if ($settings) {
                $settings->logo = $settings->logo ? (str_starts_with($settings->logo, 'http') ? $settings->logo : '/' . ltrim($settings->logo, '/')) : null;
                $settings->favicon = $settings->favicon ? (str_starts_with($settings->favicon, 'http') ? $settings->favicon : '/' . ltrim($settings->favicon, '/')) : null;
                $settings->footer_logo = $settings->footer_logo ? (str_starts_with($settings->footer_logo, 'http') ? $settings->footer_logo : '/' . ltrim($settings->footer_logo, '/')) : null;
                $settings->app_logo = $settings->app_logo ? (str_starts_with($settings->app_logo, 'http') ? $settings->app_logo : '/' . ltrim($settings->app_logo, '/')) : null;
            }

            $banners = Banner::where('is_active', 1)->latest()->get()->map(fn($b) => [
                'id' => $b->id,
                'image' => $b->image ? (str_starts_with($b->image, 'http') ? $b->image : '/' . ltrim($b->image, '/')) : '/placeholder.jpg',
                'for_own_shop' => (bool) $b->for_own_shop,
            ]);

            $categories = Category::with(['subCategories' => fn($q) => $q->where('is_active', 1)->orderBy('name', 'asc')])
                ->where('is_active', 1)
                ->orderBy('name', 'asc')
                ->get()
                ->map(function($cat) {
                    $cat->thumbnail = $cat->thumbnail ? (str_starts_with($cat->thumbnail, 'http') ? $cat->thumbnail : '/' . ltrim($cat->thumbnail, '/')) : '/placeholder.jpg';
                    return $cat;
                });

            // Fetch products for different sections with limited columns
            $productColumns = ['id', 'name', 'thumbnail', 'selling_price', 'discount_price', 'is_active', 'created_at', 'cash_on_delivery', 'online_payment', 'seller_id'];
            
            $popular = $this->getCombinedProducts('is_popular', 10, $productColumns);
            $newArrivals = $this->getCombinedProducts('is_new_arrival', 10, $productColumns);
            $justForYou = $this->getCombinedProducts('is_just_for_you', 12, $productColumns);
            $bestDeals = $this->getCombinedProducts('discount_price', 6, $productColumns, '>');
            
            $digitalAdmin = DigitalProduct::where('is_active', 1)->latest()->limit(6)->get()->map(fn($p) => $this->mapProduct($p, 'digital_admin'));
            $digitalSeller = SellerDigitalProduct::where('is_active', 1)->latest()->limit(6)->get()->map(fn($p) => $this->mapProduct($p, 'digital_seller'));
            $digital = $digitalAdmin->concat($digitalSeller)->sortByDesc('created_at')->take(6)->values();

            $allProducts = $this->getCombinedProducts(null, 20, $productColumns);

            // Optimized Top Shops (Fixing N+1)
            $topShops = Shop::whereHas('user', fn($q) => $q->where('status', 'active'))
                ->withCount(['sellerProducts as item_count' => fn($q) => $q->where('is_active', 1)])
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn($shop) => [
                    'id'          => $shop->id,
                    'seller_id'   => $shop->user_id,
                    'name'        => $shop->name,
                    'logo'        => $shop->logo ? (str_starts_with($shop->logo, 'http') ? $shop->logo : '/' . ltrim($shop->logo, '/')) : '/assets/admin/images/default-avatar.png',
                    'banner'      => $shop->banner ? (str_starts_with($shop->banner, 'http') ? $shop->banner : '/' . ltrim($shop->banner, '/')) : '/placeholder.jpg',
                    'item_count'  => $shop->item_count,
                    'rating'      => '5.0',
                    'description' => $shop->description,
                ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'settings'             => $settings,
                    'banners'              => $banners,
                    'categories'           => $categories,
                    'popularProducts'      => $popular,
                    'newArrivals'          => $newArrivals,
                    'justForYouProducts'   => $justForYou,
                    'digitalProducts'      => $digital,
                    'bestDeals'            => $bestDeals,
                    'allProducts'          => $allProducts,
                    'topShops'             => $topShops,
                ]
            ]);
        });
    }

    /**
     * Helper to get combined products from admin and seller tables efficiently.
     */
    private function getCombinedProducts($field, $limit, $columns, $operator = '=')
    {
        // Use a more efficient way to fetch combined products
        $adminColumns = array_filter($columns, fn($c) => $c !== 'seller_id');
        $adminColumns[] = 'rating';
        
        $adminProducts = Product::where('is_active', 1)
            ->select($adminColumns)
            ->when($field, function($q) use ($field, $operator) {
                if ($operator === '>') return $q->where($field, $operator, 0);
                return $q->where($field, 1);
            })
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'admin'));
        
        $sellerProducts = SellerProduct::where('is_active', 1)
            ->select($columns)
            ->when($field, function($q) use ($field, $operator) {
                if ($operator === '>') return $q->where($field, $operator, 0);
                return $q->where($field, 1);
            })
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'seller'));

        return $adminProducts->concat($sellerProducts)->sortByDesc('created_at')->take($limit)->values();
    }

    /**
     * Get general settings.
     */
    public function getSettings()
    {
        $data = Cache::remember('general_settings_with_cats', 300, function() {
            $s = GenaralSetting::first();
            if ($s) {
                $s->logo = $s->logo ? (str_starts_with($s->logo, 'http') ? $s->logo : '/' . ltrim($s->logo, '/')) : null;
                $s->favicon = $s->favicon ? (str_starts_with($s->favicon, 'http') ? $s->favicon : '/' . ltrim($s->favicon, '/')) : null;
                $s->footer_logo = $s->footer_logo ? (str_starts_with($s->footer_logo, 'http') ? $s->footer_logo : '/' . ltrim($s->footer_logo, '/')) : null;
                $s->app_logo = $s->app_logo ? (str_starts_with($s->app_logo, 'http') ? $s->app_logo : '/' . ltrim($s->app_logo, '/')) : null;
            }
            
            $categories = Category::with(['subCategories' => fn($q) => $q->where('is_active', 1)->orderBy('name', 'asc')])
                ->where('is_active', 1)
                ->orderBy('name', 'asc')
                ->get()
                ->map(function($cat) {
                    $cat->thumbnail = $cat->thumbnail ? (str_starts_with($cat->thumbnail, 'http') ? $cat->thumbnail : '/' . ltrim($cat->thumbnail, '/')) : '/placeholder.jpg';
                    return $cat;
                });

            return [
                'settings'   => $s,
                'categories' => $categories
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data['settings'],
            'categories' => $data['categories']
        ]);
    }

    /**
     * Get banners list.
     */
    public function getBanners()
    {
        $banners = Cache::remember('banners_list', 60, function() {
            return Banner::where('is_active', 1)
                ->latest()
                ->get()
                ->map(function($banner) {
                    return [
                        'id'           => $banner->id,
                        'image'        => $banner->image ? (str_starts_with($banner->image, 'http') ? $banner->image : '/' . ltrim($banner->image, '/')) : '/placeholder.jpg',
                        'for_own_shop' => (bool) $banner->for_own_shop,
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'data'    => $banners
        ]);
    }

    /**
     * Get all categories for the frontend.
     */
    public function getCategories()
    {
        $categories = Category::with(['subCategories' => function($q) {
                $q->where('is_active', 1);
            }])
            ->where('is_active', 1)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $categories
        ]);
    }

    public function getCategoriesWithSub()
    {
        $categories = Cache::remember('categories_with_sub', 60, function() {
            return Category::with(['subCategories' => function($q) {
                    $q->where('is_active', 1)->orderBy('name', 'asc');
                }])
                ->where('is_active', 1)
                ->orderBy('name', 'asc')
                ->get()
                ->map(function($cat) {
                    $cat->thumbnail = $cat->thumbnail ? (str_starts_with($cat->thumbnail, 'http') ? $cat->thumbnail : '/' . ltrim($cat->thumbnail, '/')) : '/placeholder.jpg';
                    return $cat;
                });
        });

        return response()->json([
            'success' => true,
            'data'    => $categories
        ]);
    }

    /**
     * Get all products.
     */
    public function getAllProducts(Request $request)
    {
        $limit = $request->query('limit', 10);

        $adminProducts = Product::where('is_active', 1)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'admin'));

        $sellerProducts = SellerProduct::where('is_active', 1)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'seller'));

        $all = $adminProducts->concat($sellerProducts)->sortByDesc('created_at');

        if ($limit !== 'all') {
            $all = $all->take($limit);
        }

        return response()->json([
            'success' => true,
            'data'    => $all->values()
        ]);
    }

    /**
     * Get category name.
     */
    public function getCategoryName($id)
    {
        $category = Category::find($id);
        return response()->json([
            'success' => true,
            'name'    => $category ? $category->name : 'Category'
        ]);
    }

    /**
     * Get subcategory name.
     */
    public function getSubCategoryName($id)
    {
        $subcategory = \App\Models\SubCategory::find($id);
        return response()->json([
            'success' => true,
            'name'    => $subcategory ? $subcategory->name : 'Sub-Category'
        ]);
    }

    /**
     * Get popular products (best sellers).
     */
    public function getPopularProducts(Request $request)
    {
        $limit = $request->query('limit', 10);
        
        $adminQuery = Product::where('is_active', 1)->where('is_popular', 1)->latest();
        $sellerQuery = SellerProduct::where('is_active', 1)->where('is_popular', 1)->latest();

        if ($limit !== 'all') {
            $adminQuery->take($limit);
            $sellerQuery->take($limit);
        }

        $adminProducts = $adminQuery->get()->map(fn($p) => $this->mapProduct($p, 'admin'));
        $sellerProducts = $sellerQuery->get()->map(fn($p) => $this->mapProduct($p, 'seller'));

        $combined = $adminProducts->concat($sellerProducts)->sortByDesc('id');
        if ($limit !== 'all') {
            $combined = $combined->take($limit);
        }

        return response()->json([
            'success' => true,
            'data'    => $combined->values()
        ]);
    }

    /**
     * Get New Arrival products.
     */
    public function getNewArrivals(Request $request)
    {
        $limit = $request->query('limit', 10);

        $adminQuery = Product::where('is_active', 1)->where('is_new_arrival', 1)->latest();
        $sellerQuery = SellerProduct::where('is_active', 1)->where('is_new_arrival', 1)->latest();

        if ($limit !== 'all') {
            $adminQuery->take($limit);
            $sellerQuery->take($limit);
        }

        $adminProducts = $adminQuery->get()->map(fn($p) => $this->mapProduct($p, 'admin'));
        $sellerProducts = $sellerQuery->get()->map(fn($p) => $this->mapProduct($p, 'seller'));

        $combined = $adminProducts->concat($sellerProducts)->sortByDesc('id');
        if ($limit !== 'all') {
            $combined = $combined->take($limit);
        }

        return response()->json([
            'success' => true,
            'data'    => $combined->values()
        ]);
    }

    /**
     * Get Just For You products.
     */
    public function getJustForYouProducts(Request $request)
    {
        $limit = $request->query('limit', 10);

        $adminQuery = Product::where('is_active', 1)->where('is_just_for_you', 1)->orderBy('id', 'desc');
        $sellerQuery = SellerProduct::where('is_active', 1)->where('is_just_for_you', 1)->orderBy('id', 'desc');

        if ($limit !== 'all') {
            $adminQuery->take($limit);
            $sellerQuery->take($limit);
        }

        $adminProducts = $adminQuery->get()->map(fn($p) => $this->mapProduct($p, 'admin'));
        $sellerProducts = $sellerQuery->get()->map(fn($p) => $this->mapProduct($p, 'seller'));

        $combined = $adminProducts->concat($sellerProducts)->sortByDesc('id');
        if ($limit !== 'all') {
            $combined = $combined->take($limit);
        }

        return response()->json([
            'success' => true,
            'data'    => $combined->values()
        ]);
    }

    /**
     * Get products by category.
     */
    public function getProductsByCategory($id)
    {
        $adminProducts = Product::where('is_active', 1)
            ->where('category_id', $id)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'admin'));

        $sellerProducts = SellerProduct::where('is_active', 1)
            ->where('category_id', $id)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'seller'));

        $combined = $adminProducts->concat($sellerProducts)->sortByDesc('id');

        return response()->json([
            'success' => true,
            'data'    => $combined->values()
        ]);
    }

    /**
     * Get products by sub-category.
     */
    public function getProductsBySubCategory($id)
    {
        $adminProducts = Product::where('is_active', 1)
            ->where('sub_category_id', $id)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'admin'));

        $sellerProducts = SellerProduct::where('is_active', 1)
            ->where('sub_category_id', $id)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'seller'));

        $combined = $adminProducts->concat($sellerProducts)->sortByDesc('id');

        return response()->json([
            'success' => true,
            'data'    => $combined->values()
        ]);
    }

    /**
     * Get single product details.
     */
    public function getProductDetails($type, $id)
    {
        $product = null;
        if ($type === 'admin') {
            $product = Product::with(['category', 'brand'])->find($id);
        } else {
            $product = SellerProduct::with(['category', 'brand'])->find($id);
        }

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->mapProductDetails($product, $type)
        ]);
    }

    private function mapProductDetails($product, $type)
    {
        return [
            'id'                => $product->id,
            'uid'               => $type . '_' . $product->id,
            'product_type'      => $type,
            'name'              => $product->name,
            'short_description' => $product->short_description,
            'description'       => $product->description,
            'price'             => (float) $product->selling_price,
            'discount_price'    => (float) $product->discount_price,
            'old_price'         => (float) ($product->selling_price + $product->discount_price),
            'discount'          => $product->discount_price > 0 ? round(($product->discount_price / ($product->selling_price + $product->discount_price)) * 100) : 0,
            'stock'             => $product->stock_quantity,
            'sku'               => $product->sku,
            'thumbnail'         => $product->thumbnail ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : '/' . ltrim($product->thumbnail, '/')) : '/placeholder.jpg',
            'gallery'           => collect($product->gallery_images)->map(fn($img) => (str_starts_with($img, 'http') ? $img : '/' . $img)),
            'category'          => $product->category ? $product->category->name : null,
            'category_id'       => $product->category_id,
            'brand'             => $product->brand ? $product->brand->name : null,
            'color'             => $product->color,
            'size'              => $product->size,
            'unit'              => $product->unit,
            'video'             => $product->video,
            'video_type'        => $product->video_type,
            'cash_on_delivery'  => (bool) $product->cash_on_delivery,
            'online_payment'    => (bool) $product->online_payment,
        ];
    }

    /**
     * Get related products based on category.
     */
    public function getRelatedProducts($type, $id)
    {
        $product = null;
        if ($type === 'admin') {
            $product = Product::find($id);
        } else {
            $product = SellerProduct::find($id);
        }

        if (!$product) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $categoryId = $product->category_id;

        $adminRelated = Product::where('is_active', 1)
            ->where('category_id', $categoryId)
            ->where('id', '!=', $type === 'admin' ? $id : 0)
            ->latest()
            ->take(6)
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'admin'));

        $sellerRelated = SellerProduct::where('is_active', 1)
            ->where('category_id', $categoryId)
            ->where('id', '!=', $type === 'seller' ? $id : 0)
            ->latest()
            ->take(6)
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'seller'));

        $combined = $adminRelated->concat($sellerRelated)->sortByDesc('id')->take(6);

        return response()->json([
            'success' => true,
            'data'    => $combined->values()
        ]);
    }

    /**
     * Get top rated shops (active shops).
     */
    public function getTopShops()
    {
        $settings = GenaralSetting::first();
        if ($settings && !$settings->top_rated_shops_status) {
            return response()->json([
                'success' => true,
                'data'    => []
            ]);
        }

        $shops = Shop::whereHas('user', function($query) {
                $query->where('status', 'active');
            })
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($shop) {
                // Count products for this seller
                $productCount = SellerProduct::where('seller_id', $shop->user_id)
                    ->where('is_active', 1)
                    ->count();

                return [
                    'id'           => $shop->id,
                    'seller_id'    => $shop->user_id,
                    'name'         => $shop->name,
                    'logo'         => $shop->logo ? (str_starts_with($shop->logo, 'http') ? $shop->logo : '/' . ltrim($shop->logo, '/')) : '/assets/admin/images/default-avatar.png',
                    'banner'       => $shop->banner ? (str_starts_with($shop->banner, 'http') ? $shop->banner : '/' . ltrim($shop->banner, '/')) : '/placeholder.jpg',
                    'item_count'   => $productCount,
                    'rating'       => '5.0', // Placeholder for now
                    'description'  => $shop->description,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $shops
        ]);
    }

    /**
     * Get products by seller ID.
     */
    public function getProductsBySeller($seller_id)
    {
        $products = SellerProduct::where('is_active', 1)
            ->where('seller_id', $seller_id)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'seller'));

        $shop = Shop::where('user_id', $seller_id)->first();

        return response()->json([
            'success' => true,
            'data'    => $products,
            'shop'    => $shop ? [
                'name'   => $shop->name,
                'logo'   => $shop->logo ? (str_starts_with($shop->logo, 'http') ? $shop->logo : '/' . ltrim($shop->logo, '/')) : '/placeholder.jpg',
                'banner' => $shop->banner ? (str_starts_with($shop->banner, 'http') ? $shop->banner : '/' . ltrim($shop->banner, '/')) : '/placeholder.jpg',
            ] : null
        ]);
    }

    /**
     * Search products by name or price (AJAX).
     */
    public function searchProducts(Request $request)
    {
        $q = $request->query('q');
        if (!$q || strlen($q) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        // Search Admin Products
        $adminProducts = Product::where('is_active', 1)
            ->where(function($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('selling_price', 'LIKE', "%{$q}%");
            })
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'admin'));

        // Search Seller Products
        $sellerProducts = SellerProduct::where('is_active', 1)
            ->where(function($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('selling_price', 'LIKE', "%{$q}%");
            })
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'seller'));

        // Search Digital Products
        $digitalAdmin = DigitalProduct::where('is_active', 1)
            ->where(function($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('selling_price', 'LIKE', "%{$q}%");
            })
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'digital_admin'));

        $digitalSeller = SellerDigitalProduct::where('is_active', 1)
            ->where(function($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('selling_price', 'LIKE', "%{$q}%");
            })
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'digital_seller'));

        $combined = $adminProducts->concat($sellerProducts)
                                  ->concat($digitalAdmin)
                                  ->concat($digitalSeller)
                                  ->sortByDesc('created_at')
                                  ->take(12);

        return response()->json([
            'success' => true,
            'data'    => $combined->values()
        ]);
    }

    /**
     * Get digital products.
     */
    public function getDigitalProducts(Request $request)
    {
        $limit = $request->query('limit', 10);
        
        $adminProducts = DigitalProduct::where('is_active', 1)
            ->latest()
            ->when($limit !== 'all', fn($q) => $q->take($limit))
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'digital_admin'));

        $sellerProducts = SellerDigitalProduct::where('is_active', 1)
            ->latest()
            ->when($limit !== 'all', fn($q) => $q->take($limit))
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'digital_seller'));

        $all = $adminProducts->concat($sellerProducts)->sortByDesc('created_at');
        
        if ($limit !== 'all') {
            $all = $all->take($limit);
        }

        return response()->json([
            'success' => true,
            'data'    => $all->values()
        ]);
    }

    /**
     * Get best deals (high discount products).
     */
    public function getBestDeals(Request $request)
    {
        $limit = $request->query('limit', 10);

        $adminProducts = Product::where('is_active', 1)
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'admin'));

        $sellerProducts = SellerProduct::where('is_active', 1)
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'seller'));

        $all = $adminProducts->concat($sellerProducts)->sortByDesc('discount_percentage');

        if ($limit !== 'all') {
            $all = $all->take($limit);
        }

        return response()->json([
            'success' => true,
            'data'    => $all->values()
        ]);
    }

    /**
     * Get footer data (categories and page categories).
     */
    public function getFooterData()
    {
        $productCategories = Category::where('is_active', 1)
            ->orderBy('name', 'asc')
            ->take(12)
            ->get();

        $pageCategories = \App\Models\PageCategory::with(['pages' => function($q) {
                $q->where('status', 1)->orderBy('name', 'asc');
            }])
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        $settings = GenaralSetting::first();
        if ($settings) {
            $settings->logo = $settings->logo ? (str_starts_with($settings->logo, 'http') ? $settings->logo : '/' . ltrim($settings->logo, '/')) : null;
            $settings->footer_logo = $settings->footer_logo ? (str_starts_with($settings->footer_logo, 'http') ? $settings->footer_logo : '/' . ltrim($settings->footer_logo, '/')) : null;
        }

        return response()->json([
            'success'            => true,
            'product_categories' => $productCategories,
            'page_categories'    => $pageCategories,
            'settings'           => $settings
        ]);
    }

    /**
     * Get single page details.
     */
    public function getPage($id)
    {
        $page = \App\Models\Page::where('status', 1)->find($id);
        if (!$page) {
            return response()->json(['success' => false, 'message' => 'Page not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $page
        ]);
    }

    /**
     * Helper to map product data for frontend.
     */
    private function mapProduct($product, $type)
    {
        $sellingPrice = (float) $product->selling_price;
        $discountAmount = (float) ($product->discount_price ?? 0);
        $originalPrice = $sellingPrice + $discountAmount;
        $discountPercentage = $originalPrice > 0 ? round(($discountAmount / $originalPrice) * 100) : 0;

        return [
            'id'                  => $product->id,
            'uid'                 => $type . '_' . $product->id,
            'product_type'        => $type,
            'seller_id'           => ($type === 'seller' || $type === 'digital_seller') ? ($product->seller_id ?? 0) : 0, 
            'title'               => $product->name,
            'image'               => $product->thumbnail ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : '/' . ltrim($product->thumbnail, '/')) : '/placeholder.jpg',
            'price'               => $sellingPrice,
            'oldPrice'            => $originalPrice,
            'discount'            => $discountPercentage,
            'discount_percentage' => $discountPercentage, 
            'rating'              => $product->rating ?? '0.0',
            'reviews'             => 0,
            'sold'                => 0,
            'cash_on_delivery'    => (bool) ($product->cash_on_delivery ?? false),
            'online_payment'      => (bool) ($product->online_payment ?? true),
            'created_at'          => $product->created_at,
        ];
    }
}
