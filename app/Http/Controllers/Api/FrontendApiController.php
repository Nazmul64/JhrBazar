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
use App\Models\SociallinkList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontendApiController extends Controller
{
    /**
     * Consolidated home data for high performance.
     */
    const ASSET_VERSION = '1.0.4';

    public function getHomeData()
    {
        return Cache::remember('home_data_v2', 60, function() {
            $settings = GenaralSetting::first();

            if ($settings) {
                $settings->logo = $settings->logo ? (str_starts_with($settings->logo, 'http') ? $settings->logo : '/' . ltrim($settings->logo, '/')) : null;
                $settings->footer_logo = $settings->footer_logo ? (str_starts_with($settings->footer_logo, 'http') ? $settings->footer_logo : '/' . ltrim($settings->footer_logo, '/')) : null;
            }

            $banners = Banner::where('is_active', 1)->latest()->get()->map(function($b) {
                $image = $b->image ? (str_starts_with($b->image, 'http') ? $b->image : '/' . ltrim($b->image, '/')) : '/placeholder.jpg';
                return [
                    'id' => $b->id,
                    'image' => $image . '?v=' . self::ASSET_VERSION,
                    'for_own_shop' => (bool) $b->for_own_shop,
                ];
            });

            $categories = Category::with(['subCategories' => fn($q) => $q->where('is_active', 1)->orderBy('name', 'asc')])
                ->where('is_active', 1)
                ->orderBy('name', 'asc')
                ->get()
                ->map(function($cat) {
                    $thumbnail = $cat->thumbnail ? (str_starts_with($cat->thumbnail, 'http') ? $cat->thumbnail : '/' . ltrim($cat->thumbnail, '/')) : '/placeholder.jpg';
                    $cat->thumbnail = $thumbnail . '?v=' . self::ASSET_VERSION;
                    if ($cat->subCategories) {
                        $cat->subCategories->map(function($sub) {
                            $subThumbnail = $sub->thumbnail ? (str_starts_with($sub->thumbnail, 'http') ? $sub->thumbnail : '/' . ltrim($sub->thumbnail, '/')) : '/placeholder.jpg';
                            $sub->thumbnail = $subThumbnail . '?v=' . self::ASSET_VERSION;
                            return $sub;
                        });
                    }
                    return $cat;
                });

            $productColumns = ['id', 'name', 'slug', 'thumbnail', 'selling_price', 'discount_price', 'is_active', 'created_at', 'seller_id', 'cash_on_delivery', 'online_payment', 'frontend_sections', 'stock_quantity'];

            $popular = $this->getCombinedProducts('is_popular', 10, $productColumns);
            $newArrivals = $this->getCombinedProducts('is_new_arrival', 10, $productColumns);
            $justForYou = $this->getCombinedProducts('is_just_for_you', 12, $productColumns);
            $bestDeals = $this->getCombinedProducts('is_best_seller', 6, $productColumns);

            $digitalAdmin = DigitalProduct::where('is_active', 1)->withCount('reviews')->withAvg('reviews', 'rating')->latest()->limit(6)->get()->map(fn($p) => $this->mapProduct($p, 'digital_admin'));
            $digitalSeller = SellerDigitalProduct::where('is_active', 1)->withCount('reviews')->withAvg('reviews', 'rating')->latest()->limit(6)->get()->map(fn($p) => $this->mapProduct($p, 'digital_seller'));
            $digital = $digitalAdmin->concat($digitalSeller)->sortByDesc('created_at')->take(6)->values();

            $topShops = Shop::whereHas('user', fn($q) => $q->where('status', 'active'))
                ->withCount(['sellerProducts as item_count' => fn($q) => $q->where('is_active', 1)])
                ->latest()->limit(8)->get()
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

            $allProducts = $this->getCombinedProducts(null, 20, $productColumns);
            $recentReviews = \App\Models\Review::with('user:id,name')->where('status', 1)->latest()->take(6)->get();

            // Group products by frontend_sections for dynamic category sections
            $sectionAdminProducts = Product::where('is_active', 1)
                ->whereNotNull('frontend_sections')
                ->select(['id', 'name', 'slug', 'thumbnail', 'selling_price', 'discount_price', 'is_active', 'created_at', 'cash_on_delivery', 'online_payment', 'frontend_sections', 'stock_quantity'])
                ->withCount('reviews')->withAvg('reviews', 'rating')
                ->latest()
                ->get()
                ->map(fn($p) => $this->mapProduct($p, 'admin'));

            $sectionSellerProducts = SellerProduct::where('is_active', 1)
                ->whereNotNull('frontend_sections')
                ->select(['id', 'name', 'slug', 'thumbnail', 'selling_price', 'discount_price', 'is_active', 'created_at', 'seller_id', 'cash_on_delivery', 'online_payment', 'frontend_sections', 'stock_quantity'])
                ->withCount('reviews')->withAvg('reviews', 'rating')
                ->latest()
                ->get()
                ->map(fn($p) => $this->mapProduct($p, 'seller'));

            $allSectionProducts = $sectionAdminProducts->concat($sectionSellerProducts)->sortByDesc('created_at');

            $frontendSections = [];
            foreach ($allSectionProducts as $p) {
                $sections = $p['frontend_sections'];
                if (is_array($sections)) {
                    foreach ($sections as $secName) {
                        if ($secName) {
                            if (!isset($frontendSections[$secName])) {
                                $frontendSections[$secName] = [];
                            }
                            if (count($frontendSections[$secName]) < 12) {
                                $frontendSections[$secName][] = $p;
                            }
                        }
                    }
                }
            }

            $formattedSections = [];
            foreach ($frontendSections as $title => $products) {
                $formattedSections[] = [
                    'title' => $title,
                    'products' => $products
                ];
            }

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
                    'topShops'             => $topShops,
                    'allProducts'          => $allProducts,
                    'recentReviews'        => $recentReviews,
                    'frontendSections'     => $formattedSections,
                ]
            ])->getData();
        });
    }

    /**
     * Helper to get combined products from admin and seller tables efficiently.
     */
    private function getCombinedProducts($field, $limit, $columns, $operator = '=')
    {
        $adminColumns = array_filter($columns, fn($c) => $c !== 'seller_id');
        $adminColumns[] = 'rating';

        // Featured flags to exclude from "All Products" if no specific field is provided
        $featuredFlags = ['is_new_arrival', 'is_popular', 'is_best_seller', 'is_just_for_you', 'is_hot_product', 'is_flash_sale'];

        $adminQuery = Product::where('is_active', 1)->select($adminColumns)->withCount('reviews')->withAvg('reviews', 'rating');
        $sellerQuery = SellerProduct::where('is_active', 1)->select($columns)->withCount('reviews')->withAvg('reviews', 'rating');

        if ($field) {
            $adminQuery->when($field, function($q) use ($field, $operator) {
                if ($operator === '>') return $q->where($field, $operator, 0);
                return $q->where($field, 1);
            });
            $sellerQuery->when($field, function($q) use ($field, $operator) {
                if ($operator === '>') return $q->where($field, $operator, 0);
                return $q->where($field, 1);
            });
        } else {
            // This is for "All Products" section - EXCLUDE featured products to avoid duplicates on home
            foreach ($featuredFlags as $flag) {
                $adminQuery->where($flag, 0);
                $sellerQuery->where($flag, 0);
            }
        }

        $adminProducts = $adminQuery->latest()->limit($limit)->get()->map(fn($p) => $this->mapProduct($p, 'admin'));
        $sellerProducts = $sellerQuery->latest()->limit($limit)->get()->map(fn($p) => $this->mapProduct($p, 'seller'));

        return $adminProducts->concat($sellerProducts)->sortByDesc('created_at')->take($limit)->values();
    }

    /**
     * Get general settings.
     */
    public function getSettings()
    {
        $data = Cache::remember('general_settings_with_cats', 10, function() {
            $s = GenaralSetting::first();
            if ($s) {
                $s->logo = $s->logo ? (str_starts_with($s->logo, 'http') ? $s->logo : '/' . ltrim($s->logo, '/')) : null;
                $s->favicon = $s->favicon ? (str_starts_with($s->favicon, 'http') ? $s->favicon : '/' . ltrim($s->favicon, '/')) : null;
                $s->footer_logo = $s->footer_logo ? (str_starts_with($s->footer_logo, 'http') ? $s->footer_logo : '/' . ltrim($s->footer_logo, '/')) : null;
                $s->app_logo = $s->app_logo ? (str_starts_with($s->app_logo, 'http') ? $s->app_logo : '/' . ltrim($s->app_logo, '/')) : null;
                $s->og_image = $s->og_image ? (str_starts_with($s->og_image, 'http') ? $s->og_image : '/' . ltrim($s->og_image, '/')) : null;
            }

            $categories = Category::with(['subCategories' => fn($q) => $q->where('is_active', 1)->orderBy('name', 'asc')])
                ->where('is_active', 1)
                ->orderBy('name', 'asc')
                ->get()
                ->map(function($cat) {
                    $cat->thumbnail = $cat->thumbnail ? (str_starts_with($cat->thumbnail, 'http') ? $cat->thumbnail : '/' . ltrim($cat->thumbnail, '/')) : '/placeholder.jpg';
                    if ($cat->subCategories) {
                        $cat->subCategories->map(function($sub) {
                            $sub->thumbnail = $sub->thumbnail ? (str_starts_with($sub->thumbnail, 'http') ? $sub->thumbnail : '/' . ltrim($sub->thumbnail, '/')) : '/placeholder.jpg';
                            return $sub;
                        });
                    }
                    return $cat;
                });

            return [
                'settings'   => $s,
                'categories' => $categories,
                'social_links' => SociallinkList::where('is_active', 1)->get()
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data['settings'],
            'categories' => $data['categories'],
            'social_links' => $data['social_links']
        ]);
    }

    /**
     * Get categories with subcategories.
     */
    public function getCategoriesWithSub()
    {
        return Cache::remember('categories_with_sub_v2', 60, function() {
            $categories = Category::with(['subCategories' => fn($q) => $q->where('is_active', 1)->orderBy('name', 'asc')])
                ->where('is_active', 1)
                ->orderBy('name', 'asc')
                ->get()
                ->map(function($cat) {
                    $cat->thumbnail = $cat->thumbnail ? (str_starts_with($cat->thumbnail, 'http') ? $cat->thumbnail : '/' . ltrim($cat->thumbnail, '/')) : '/placeholder.jpg';
                    if ($cat->subCategories) {
                        $cat->subCategories->map(function($sub) {
                            $sub->thumbnail = $sub->thumbnail ? (str_starts_with($sub->thumbnail, 'http') ? $sub->thumbnail : '/' . ltrim($sub->thumbnail, '/')) : '/placeholder.jpg';
                            return $sub;
                        });
                    }
                    return $cat;
                });
            return response()->json([
                'success' => true,
                'data' => $categories
            ])->getData();
        });
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

    public function getCategories()
    {
        $categories = Category::with(['subCategories' => function($q) {
                $q->where('is_active', 1);
            }])
            ->where('is_active', 1)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function($cat) {
                $cat->thumbnail = $cat->thumbnail ? (str_starts_with($cat->thumbnail, 'http') ? $cat->thumbnail : '/' . ltrim($cat->thumbnail, '/')) : '/placeholder.jpg';
                if ($cat->subCategories) {
                    $cat->subCategories->map(function($sub) {
                        $sub->thumbnail = $sub->thumbnail ? (str_starts_with($sub->thumbnail, 'http') ? $sub->thumbnail : '/' . ltrim($sub->thumbnail, '/')) : '/placeholder.jpg';
                        return $sub;
                    });
                }
                return $cat;
            });

        return response()->json([
            'success' => true,
            'data'    => $categories
        ]);
    }

// Duplicate getCategoriesWithSub method fully removed – syntax cleaned

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
        $category = Category::with('subCategories')->find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $subCategoryIds = $category->subCategories->pluck('id')->toArray();
        $allIds = array_merge([$id], $subCategoryIds);

        $adminProducts = Product::where('is_active', 1)
            ->withCount('reviews')->withAvg('reviews', 'rating')
            ->where(function($q) use ($id, $subCategoryIds) {
                $q->where('category_id', $id)
                  ->orWhereIn('sub_category_id', $subCategoryIds);
            })
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'admin'));

        $sellerProducts = SellerProduct::where('is_active', 1)
            ->withCount('reviews')->withAvg('reviews', 'rating')
            ->where(function($q) use ($id, $subCategoryIds) {
                $q->where('category_id', $id)
                  ->orWhereIn('sub_category_id', $subCategoryIds);
            })
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
            ->withCount('reviews')->withAvg('reviews', 'rating')
            ->where('sub_category_id', $id)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'admin'));

        $sellerProducts = SellerProduct::where('is_active', 1)
            ->withCount('reviews')->withAvg('reviews', 'rating')
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
     * Get single product details by slug.
     */
    public function getProductBySlug($slug)
    {
        // Try Admin Products
        $product = Product::with(['category', 'brand'])->withCount('reviews')->withAvg('reviews', 'rating')->where('slug', $slug)->orWhere('id', $slug)->first();
        $type = 'admin';

        if (!$product) {
            // Try Seller Products
            $product = SellerProduct::with(['category', 'brand'])->withCount('reviews')->withAvg('reviews', 'rating')->where('slug', $slug)->orWhere('id', $slug)->first();
            $type = 'seller';
        }

        if (!$product) {
            // Try Digital Admin
            $product = DigitalProduct::with(['category'])->withCount('reviews')->withAvg('reviews', 'rating')->where('slug', $slug)->orWhere('id', $slug)->first();
            $type = 'digital_admin';
        }

        if (!$product) {
            // Try Digital Seller
            $product = SellerDigitalProduct::with(['category'])->withCount('reviews')->withAvg('reviews', 'rating')->where('slug', $slug)->orWhere('id', $slug)->first();
            $type = 'digital_seller';
        }

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $reviews = \App\Models\Review::with(['user:id,name,profile_image'])
            ->where('product_id', $product->id)
            ->where('product_type', $type)
            ->where('status', 1)
            ->latest()
            ->get();

        $avgRating = $reviews->avg('rating') ?: 0;
        $reviewCount = $reviews->count();

        // Fetch Related Products (Optimized)
        $categoryId = $product->category_id;
        $related = collect([]);
        if ($type === 'admin') {
            $related = Product::where('is_active', 1)->withCount('reviews')->withAvg('reviews', 'rating')->where('category_id', $categoryId)->where('id', '!=', $product->id)->latest()->take(6)->get()->map(fn($p) => $this->mapProduct($p, 'admin'));
        } else {
            $related = SellerProduct::where('is_active', 1)->withCount('reviews')->withAvg('reviews', 'rating')->where('category_id', $categoryId)->where('id', '!=', $product->id)->latest()->take(6)->get()->map(fn($p) => $this->mapProduct($p, 'seller'));
        }

        return response()->json([
            'success' => true,
            'data'    => array_merge($this->mapProductDetails($product, $type), [
                'avg_rating'   => round($avgRating, 1),
                'review_count' => $reviewCount,
                'reviews'      => $reviews,
                'related'      => $related
            ])
        ]);
    }

    public function getProductDetails($type, $id)
    {
        // Keep this for backward compatibility if needed, but internally call the same logic
        return $this->getProductBySlug($id);
    }

    private function mapProductDetails($product, $type)
    {
        $shop = null;
        if ($type === 'seller' || $type === 'digital_seller') {
            $shop = Shop::where('user_id', $product->seller_id)->first();
        }

        return [
            'id'                => $product->id,
            'slug'              => $product->slug,
            'uid'               => $type . '_' . $product->id,
            'product_type'      => $type,
            'seller_id'         => ($type === 'seller' || $type === 'digital_seller') ? $product->seller_id : null,
            'seller_name'       => $shop ? $shop->name : 'JHR Bazar',
            'seller_logo'       => $shop ? $shop->logo_url : null,
            'seller_rating'     => 5.0, // Static for now as requested in screenshot
            'estimated_delivery'=> $shop ? $shop->estimated_delivery : '2-5 days',
            'name'              => $product->name,
            'short_description' => $product->short_description,
            'description'       => $product->description,
            'price'             => (float) $product->selling_price,
            'discount_price'    => (float) $product->discount_price,
            'old_price'         => (float) ($product->selling_price + $product->discount_price),
            'discount'          => $product->discount_price > 0 ? round(($product->discount_price / ($product->selling_price + $product->discount_price)) * 100) : 0,
            'stock'             => $product->stock_quantity,
            'sku'               => $product->sku,
            'thumbnail'         => ($product->thumbnail ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : '/' . ltrim($product->thumbnail, '/')) : '/placeholder.jpg') . '?v=' . self::ASSET_VERSION,
            'gallery'           => collect($product->gallery_images)->map(fn($img) => (str_starts_with($img, 'http') ? $img : '/' . $img) . '?v=' . time()),
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
            'is_shipping_charge'=> (bool) $product->is_shipping_charge,
            'meta_title'        => $product->meta_title,
            'meta_description'  => $product->meta_description,
            'meta_keywords'     => $product->meta_keywords,
        ];
    }

    /**
     * Get related products based on category.
     */
    public function getRelatedProducts($type, $id)
    {
        $product = null;
        if ($type === 'admin') {
            $product = Product::where('id', $id)->orWhere('slug', $id)->first();
        } else {
            $product = SellerProduct::where('id', $id)->orWhere('slug', $id)->first();
        }

        if (!$product) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $categoryId = $product->category_id;
        $sellerId = (str_contains($type, 'seller')) ? $product->seller_id : null;

        if ($sellerId) {
            // Priority: Same seller's products
            $sellerRelated = SellerProduct::where('is_active', 1)
                ->withCount('reviews')->withAvg('reviews', 'rating')
                ->where('seller_id', $sellerId)
                ->where('id', '!=', $id)
                ->latest()
                ->take(12)
                ->get()
                ->map(fn($p) => $this->mapProduct($p, 'seller'));

            return response()->json([
                'success' => true,
                'data'    => $sellerRelated->values()
            ]);
        }

        // Default: Same category products for Admin
        $adminRelated = Product::where('is_active', 1)
            ->withCount('reviews')->withAvg('reviews', 'rating')
            ->where('category_id', $categoryId)
            ->where('id', '!=', $type === 'admin' ? $id : 0)
            ->latest()
            ->take(6)
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'admin'));

        $sellerRelated = SellerProduct::where('is_active', 1)
            ->withCount('reviews')->withAvg('reviews', 'rating')
            ->where('category_id', $categoryId)
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
            ->withCount('reviews')->withAvg('reviews', 'rating')
            ->where('seller_id', $seller_id)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'seller'));

        $shop = Shop::where('user_id', $seller_id)->first();

        return response()->json([
            'success' => true,
            'data'    => $products,
            'shop'    => $shop ? [
                'id'          => $shop->id,
                'user_id'     => $shop->user_id,
                'name'        => $shop->name,
                'logo'        => $shop->logo ? (str_starts_with($shop->logo, 'http') ? $shop->logo : '/' . ltrim($shop->logo, '/')) : '/placeholder.jpg',
                'banner'      => $shop->banner ? (str_starts_with($shop->banner, 'http') ? $shop->banner : '/' . ltrim($shop->banner, '/')) : '/placeholder.jpg',
                'description' => $shop->description,
            ] : null
        ]);
    }

    /**
     * Get reviews by seller ID.
     */
    public function getReviewsBySeller($seller_id)
    {
        $sellerProductIds = SellerProduct::where('seller_id', $seller_id)->pluck('id');
        $digitalSellerProductIds = SellerDigitalProduct::where('seller_id', $seller_id)->pluck('id');

        $reviews = \App\Models\Review::with(['user:id,name,profile_image'])
            ->where(function($q) use ($sellerProductIds, $digitalSellerProductIds) {
                $q->where(function($sq) use ($sellerProductIds) {
                    $sq->where('product_type', 'seller')->whereIn('product_id', $sellerProductIds);
                })->orWhere(function($sq) use ($digitalSellerProductIds) {
                    $sq->where('product_type', 'digital_seller')->whereIn('product_id', $digitalSellerProductIds);
                });
            })
            ->where('status', 1)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $reviews
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
            ->withCount('reviews')->withAvg('reviews', 'rating')
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
            ->withCount('reviews')->withAvg('reviews', 'rating')
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
            ->withCount('reviews')->withAvg('reviews', 'rating')
            ->where(function($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('selling_price', 'LIKE', "%{$q}%");
            })
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'digital_admin'));

        $digitalSeller = SellerDigitalProduct::where('is_active', 1)
            ->withCount('reviews')->withAvg('reviews', 'rating')
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
            ->withCount('reviews')->withAvg('reviews', 'rating')
            ->latest()
            ->when($limit !== 'all', fn($q) => $q->take($limit))
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'digital_admin'));

        $sellerProducts = SellerDigitalProduct::where('is_active', 1)
            ->withCount('reviews')->withAvg('reviews', 'rating')
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
            ->withCount('reviews')->withAvg('reviews', 'rating')
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->latest()
            ->get()
            ->map(fn($p) => $this->mapProduct($p, 'admin'));

        $sellerProducts = SellerProduct::where('is_active', 1)
            ->withCount('reviews')->withAvg('reviews', 'rating')
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
        $productCategories = Category::where('is_active', 1)->select('id', 'name')->orderBy('name', 'asc')->get();

        $pageCategories = \App\Models\PageCategory::with(['pages' => function($q) {
                $q->where('status', 1)->select('id', 'page_category_id', 'name', 'slug')->orderBy('created_at', 'asc');
            }])
            ->where('status', 1)->select('id', 'name')->orderBy('created_at', 'asc')->get();

        $settings = GenaralSetting::first();
        if ($settings) {
            $settings->logo = $settings->logo ? (str_starts_with($settings->logo, 'http') ? $settings->logo : '/' . ltrim($settings->logo, '/')) : null;
            $settings->footer_logo = $settings->footer_logo ? (str_starts_with($settings->footer_logo, 'http') ? $settings->footer_logo : '/' . ltrim($settings->footer_logo, '/')) : null;
            $settings->payment_methods_logo = $settings->payment_methods_logo ? (str_starts_with($settings->payment_methods_logo, 'http') ? $settings->payment_methods_logo : '/' . ltrim($settings->payment_methods_logo, '/')) : null;
            $settings->footer_qr = $settings->footer_qr ? (str_starts_with($settings->footer_qr, 'http') ? $settings->footer_qr : '/' . ltrim($settings->footer_qr, '/')) : null;
        }

        $membershipLogos = \App\Models\MembershipLogo::where('is_active', 1)->select('id', 'image', 'name')->get()->map(function($logo) {
            $logo->image = $logo->image ? (str_starts_with($logo->image, 'http') ? $logo->image : '/' . ltrim($logo->image, '/')) : null;
            return $logo;
        });

        return response()->json([
            'success'            => true,
            'product_categories' => $productCategories,
            'page_categories'    => $pageCategories,
            'settings'           => $settings,
            'social_links'       => SociallinkList::where('is_active', 1)->select('id', 'name', 'link')->get(),
            'membership_logos'   => $membershipLogos
        ]);
    }

    /**
     * Get single page details.
     */
    public function getPage($slug)
    {
        $page = \App\Models\Page::where('status', 1)
            ->where(function($q) use ($slug) {
                $q->where('slug', $slug)->orWhere('id', $slug);
            })->first();

        if (!$page) {
            return response()->json(['success' => false, 'message' => 'Page not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $page
        ]);
    }

    /**
     * Get blog categories.
     */
    public function getBlogCategories()
    {
        $categories = \App\Models\BlogCategory::where('status', 1)->get();
        return response()->json(['success' => true, 'data' => $categories]);
    }

    /**
     * Get blogs with pagination and category filter.
     */
    public function getBlogs(Request $request)
    {
        $query = \App\Models\Blog::with('category')->where('status', 1);

        if ($request->category_id) {
            $query->where('blog_category_id', $request->category_id);
        }

        if ($request->category_slug) {
            $category = \App\Models\BlogCategory::where('slug', $request->category_slug)->first();
            if ($category) {
                $query->where('blog_category_id', $category->id);
            }
        }

        $blogs = $query->latest()->paginate(12);

        // Map thumbnails
        $blogs->getCollection()->transform(function($blog) {
            $blog->thumbnail = $blog->thumbnail ? (str_starts_with($blog->thumbnail, 'http') ? $blog->thumbnail : '/' . ltrim($blog->thumbnail, '/')) : null;
            return $blog;
        });

        return response()->json(['success' => true, 'data' => $blogs]);
    }

    /**
     * Get single blog details.
     */
    public function getBlogDetails($slug)
    {
        $blog = \App\Models\Blog::with('category')->where('status', 1)->where('slug', $slug)->first();
        if (!$blog) {
            return response()->json(['success' => false, 'message' => 'Blog not found'], 404);
        }

        $blog->thumbnail = $blog->thumbnail ? (str_starts_with($blog->thumbnail, 'http') ? $blog->thumbnail : '/' . ltrim($blog->thumbnail, '/')) : null;

        return response()->json(['success' => true, 'data' => $blog]);
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
            'slug'                => $product->slug ?: $product->id,
            'uid'                 => $type . '_' . $product->id,
            'product_type'        => $type,
            'seller_id'           => ($type === 'seller' || $type === 'digital_seller') ? ($product->seller_id ?? 0) : 0,
            'title'               => $product->name,
            'image'               => (
                                        $product->thumbnail ? (
                                            str_starts_with($product->thumbnail, 'http')
                                            ? $product->thumbnail
                                            : '/' . ltrim(str_starts_with($product->thumbnail, 'uploads/') ? $product->thumbnail : 'uploads/product/' . ltrim($product->thumbnail, '/'), '/')
                                        ) : '/placeholder.jpg'
                                     ) . '?v=' . self::ASSET_VERSION,
            'price'               => $sellingPrice,
            'oldPrice'            => $originalPrice,
            'discount'            => $discountPercentage,
            'discount_percentage' => $discountPercentage,
            'rating'              => round($product->reviews_avg_rating ?? ($product->rating ?? 0), 1),
            'reviews'             => $product->reviews_count ?? 0,
            'sold'                => 0,
            'cash_on_delivery'    => (bool) ($product->cash_on_delivery ?? false),
            'online_payment'      => (bool) ($product->online_payment ?? true),
            'created_at'          => $product->created_at,
            'frontend_sections'   => is_string($product->frontend_sections) ? json_decode($product->frontend_sections, true) : $product->frontend_sections,
            'stock_quantity'      => (int) ($product->stock_quantity ?? 0),
            'current_stock'       => (int) ($product->stock_quantity ?? 0),
            'stock'               => (int) ($product->stock_quantity ?? 0),
        ];
    }
    /**
     * Get about company details.
     */
    public function getAboutCompany()
    {
        $about = \App\Models\AboutCompany::first();
        if ($about && $about->image) {
            $about->image = str_starts_with($about->image, 'http') ? $about->image : '/' . ltrim($about->image, '/');
        }
        return response()->json(['success' => true, 'data' => $about]);
    }

    /**
     * Get privacy policy details.
     */
    public function getPrivacyPolicy()
    {
        $policy = \App\Models\PrivacyPolicy::first();
        return response()->json(['success' => true, 'data' => $policy]);
    }
}
