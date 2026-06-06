<?php

/**
 * ╔══════════════════════════════════════════════════════════════════════════╗
 * ║              JhrBazar — routes/api.php  (Full Rewrite)                  ║
 * ║                                                                          ║
 * ║  ⚠  এই ফাইলটি  routes/api.php  হিসেবে রাখুন।                           ║
 * ║     Laravel RouteServiceProvider স্বয়ংক্রিয়ভাবে /api prefix যোগ করে।   ║
 * ║     তাই এখানে Route::prefix('api') লেখার দরকার নেই।                     ║
 * ║                                                                          ║
 * ║  Base URL  →  https://your-domain.com/api                               ║
 * ╚══════════════════════════════════════════════════════════════════════════╝
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FrontendApiController;
use App\Http\Controllers\Api\CustomerDashboardController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\Api\AdminLandingPageBuilderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Admin\CustomerDetectorController;
use App\Http\Controllers\Admin\FraudCheckerController;
use App\Http\Controllers\Admin\AdminSupportController;


/*
|─────────────────────────────────────────────────────────────────────────────
|  1.  AUTHENTICATION  (Public)
|  Base: /api/register  |  /api/login
|─────────────────────────────────────────────────────────────────────────────
*/
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login',    [AuthController::class, 'login'])->name('api.login');


/*
|─────────────────────────────────────────────────────────────────────────────
|  2.  AUTHENTICATED ROUTES  (Sanctum Bearer Token Required)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::middleware('auth:sanctum')->group(function () {

    // ── Auth ──────────────────────────────────────────────────────────────
    Route::get('/user',    fn (Request $r) => $r->user())->name('api.user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // ── Customer Dashboard ────────────────────────────────────────────────
    Route::prefix('customer')->name('api.customer.')->group(function () {
        Route::get('/dashboard',        [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders',           [CustomerDashboardController::class, 'orders'])->name('orders');
        Route::get('/wishlist',         [CustomerDashboardController::class, 'wishlist'])->name('wishlist');
        Route::post('/update-profile',  [CustomerDashboardController::class, 'updateProfile'])->name('update-profile');
        Route::post('/update-password', [CustomerDashboardController::class, 'updatePassword'])->name('update-password');
    });

    // ── Reviews (Write) ───────────────────────────────────────────────────
    Route::post('/reviews', [ReviewController::class, 'store'])->name('api.reviews.store');

    // ── Order Details (Auth Protected) ────────────────────────────────────
    Route::get('/order-details/{invoice_no}', [CheckoutController::class, 'orderDetails'])->name('api.order.details');

    // ── Wishlist Sync (Call right after login — transfers guest → user) ───
    Route::post('/wishlist/sync', [WishlistController::class, 'sync'])->name('api.wishlist.sync');

    // ── Admin Landing Page Builder ────────────────────────────────────────
    Route::prefix('admin/landingpages')->name('api.admin.landingpage.')->group(function () {
        Route::get('/{id}/sections',       [AdminLandingPageBuilderController::class, 'getSections'])->name('sections');
        Route::post('/{id}/save-sections', [AdminLandingPageBuilderController::class, 'saveSections'])->name('save-sections');
        Route::post('/{id}/save-settings', [AdminLandingPageBuilderController::class, 'saveSettings'])->name('save-settings');
        Route::post('/upload-image',       [AdminLandingPageBuilderController::class, 'uploadImage'])->name('upload-image');
    });
});


/*
|─────────────────────────────────────────────────────────────────────────────
|  3.  SETTINGS & CONFIG  (Public)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::get('/settings',    [FrontendApiController::class, 'getSettings'])->name('api.settings');
Route::get('/banners',     [FrontendApiController::class, 'getBanners'])->name('api.banners');
Route::get('/footer-data', [FrontendApiController::class, 'getFooterData'])->name('api.footer');


/*
|─────────────────────────────────────────────────────────────────────────────
|  4.  CATEGORIES  (Public)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::get('/categories',            [FrontendApiController::class, 'getCategories'])->name('api.categories');
Route::get('/categories-with-sub',   [FrontendApiController::class, 'getCategoriesWithSub'])->name('api.categories-with-sub');
Route::get('/category/{id}/name',    [FrontendApiController::class, 'getCategoryName'])->name('api.category.name');
Route::get('/subcategory/{id}/name', [FrontendApiController::class, 'getSubCategoryName'])->name('api.subcategory.name');


/*
|─────────────────────────────────────────────────────────────────────────────
|  5.  HOME DATA  (Public — single optimized endpoint)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::get('/home-data', [FrontendApiController::class, 'getHomeData'])->name('api.home-data');


/*
|─────────────────────────────────────────────────────────────────────────────
|  6.  PRODUCT LISTS  (Public)
|  ⚠  Order matters: static paths MUST come before wildcard {slug}
|─────────────────────────────────────────────────────────────────────────────
*/

// ── Curated Lists ──────────────────────────────────────────────────────────
Route::get('/all-products',     [FrontendApiController::class, 'getAllProducts'])->name('api.products.all');
Route::get('/popular-products', [FrontendApiController::class, 'getPopularProducts'])->name('api.products.popular');
Route::get('/new-arrivals',     [FrontendApiController::class, 'getNewArrivals'])->name('api.products.new-arrivals');
Route::get('/just-for-you',     [FrontendApiController::class, 'getJustForYouProducts'])->name('api.products.just-for-you');
Route::get('/digital-products', [FrontendApiController::class, 'getDigitalProducts'])->name('api.products.digital');
Route::get('/best-deals',       [FrontendApiController::class, 'getBestDeals'])->name('api.products.best-deals');

// ── Search & Filtered Lists ────────────────────────────────────────────────
// ⚠ /products/search must be declared BEFORE /products/category/{id}
Route::get('/products/search',           [FrontendApiController::class, 'searchProducts'])->name('api.products.search');
Route::get('/products/category/{id}',    [FrontendApiController::class, 'getProductsByCategory'])->name('api.products.by-category');
Route::get('/products/subcategory/{id}', [FrontendApiController::class, 'getProductsBySubCategory'])->name('api.products.by-subcategory');


/*
|─────────────────────────────────────────────────────────────────────────────
|  7.  SINGLE PRODUCT  (Public)
|  ⚠  Route ordering is critical here:
|      /product/{type}/{id}/related  →  must come BEFORE  /product/{type}/{id}
|      /product/{type}/{id}          →  must come BEFORE  /product/{slug}
|─────────────────────────────────────────────────────────────────────────────
*/
Route::get('/product/{type}/{id}/related', [FrontendApiController::class, 'getRelatedProducts'])
    ->name('api.product.related')
    ->where(['id' => '[0-9]+']);

Route::get('/product/{type}/{id}/reviews', [ReviewController::class, 'getProductReviews'])
    ->name('api.product.reviews-by-type')
    ->where(['id' => '[0-9]+']);

Route::get('/product/{type}/{id}', [FrontendApiController::class, 'getProductDetails'])
    ->name('api.product.by-type-id')
    ->where(['id' => '[0-9]+']);

// ⚠ Slug route is LAST — fallback for /product/{slug}
Route::get('/product/{slug}', [FrontendApiController::class, 'getProductBySlug'])
    ->name('api.product.by-slug');


/*
|─────────────────────────────────────────────────────────────────────────────
|  8.  SHOPS  (Public)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::get('/top-shops',                  [FrontendApiController::class, 'getTopShops'])->name('api.shop.top');
Route::get('/shop/{seller_id}/products',  [FrontendApiController::class, 'getProductsBySeller'])->name('api.shop.products');
Route::get('/shop/{seller_id}/reviews',   [FrontendApiController::class, 'getReviewsBySeller'])->name('api.shop.reviews');


/*
|─────────────────────────────────────────────────────────────────────────────
|  9.  REVIEWS  (Public Read)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::get('/recent-reviews',                       [ReviewController::class, 'getRecentReviews'])->name('api.reviews.recent');
Route::get('/reviews/{product_id}/{product_type}',  [ReviewController::class, 'fetch'])->name('api.reviews.fetch');


/*
|─────────────────────────────────────────────────────────────────────────────
|  10. WISHLIST  (Guest via X-Session-Id header  +  Authenticated)
|  Note: /wishlist/sync is auth-protected — declared above in auth group
|─────────────────────────────────────────────────────────────────────────────
*/
Route::get('/wishlist',         [WishlistController::class, 'index'])->name('api.wishlist.index');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('api.wishlist.toggle');


/*
|─────────────────────────────────────────────────────────────────────────────
|  11. CHECKOUT & ORDERS  (Public + Auth mixed)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::get('/payment-gateways',         [CheckoutController::class, 'getPaymentGateways'])->name('api.checkout.gateways');
Route::get('/shipping-charges',         [CheckoutController::class, 'getShippingCharges'])->name('api.checkout.shipping');
Route::post('/apply-coupon',            [CheckoutController::class, 'applyCoupon'])->name('api.checkout.coupon');
Route::post('/place-order',             [CheckoutController::class, 'placeOrder'])->name('api.checkout.place');
Route::get('/track-order/{invoice_no}', [CheckoutController::class, 'trackOrder'])->name('api.checkout.track');


/*
|─────────────────────────────────────────────────────────────────────────────
|  12. CHAT & SUPPORT  (Guest via X-Session-Id header)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::post('/chat/send',        [ChatApiController::class, 'sendMessage'])->name('api.chat.send');
Route::get('/chat/messages',     [ChatApiController::class, 'getMessages'])->name('api.chat.messages');
Route::get('/chat/unread-count', [ChatApiController::class, 'getUnreadCount'])->name('api.chat.unread');


/*
|─────────────────────────────────────────────────────────────────────────────
|  13. BLOG & STATIC PAGES  (Public)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::get('/blog-categories', [FrontendApiController::class, 'getBlogCategories'])->name('api.content.blog-categories');
Route::get('/blogs',           [FrontendApiController::class, 'getBlogs'])->name('api.content.blogs');
Route::get('/blog/{slug}',     [FrontendApiController::class, 'getBlogDetails'])->name('api.content.blog');
Route::get('/about-company',   [FrontendApiController::class, 'getAboutCompany'])->name('api.content.about');
Route::get('/privacy-policy',  [FrontendApiController::class, 'getPrivacyPolicy'])->name('api.content.privacy');
Route::get('/page/{slug}',     [FrontendApiController::class, 'getPage'])->name('api.content.page');


/*
|─────────────────────────────────────────────────────────────────────────────
|  14. LANDING PAGES  (Public)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::get('/landingpage/{slug}', [FrontendApiController::class, 'getLandingPageBySlug'])->name('api.landingpage');


/*
|─────────────────────────────────────────────────────────────────────────────
|  15. LEAD GENERATION  (Public)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::post('/leads/save', [LeadController::class, 'store'])->name('api.leads.save');


/*
|─────────────────────────────────────────────────────────────────────────────
|  16. TRACKING & SECURITY  (Public)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::post('/track-visit',     [CustomerDetectorController::class, 'trackVisit'])->name('api.track-visit');
Route::get('/check-ip-blocked', [FraudCheckerController::class, 'checkIpBlocked'])->name('api.check-ip');


/*
|─────────────────────────────────────────────────────────────────────────────
|  17. ADMIN SUPPORT  (Public Read)
|─────────────────────────────────────────────────────────────────────────────
*/
Route::get('/admin-support', [AdminSupportController::class, 'index'])->name('api.admin-support');


/*
|═════════════════════════════════════════════════════════════════════════════
|  API VERSION 1  (JWT / Sanctum)
|  Base URL → https://your-domain.com/api/v1
|
|  ✅ FIX: আগে Route::prefix('api') এর ভেতরে prefix('api/v1') লেখা ছিল
|          → URL হত /api/api/v1/... (WRONG — double api)
|     এখন routes/api.php তে prefix('v1') লেখাই যথেষ্ট
|          → URL হবে /api/v1/... (CORRECT)
|═════════════════════════════════════════════════════════════════════════════
*/
Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Public ────────────────────────────────────────────────────────────
    Route::post('auth/login',    [AuthController::class, 'login'])->name('auth.login');
    Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');

    Route::get('products',      [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show')
        ->where('id', '[0-9]+');

    Route::post('leads/save', [LeadController::class, 'store'])->name('leads.save');

    // ── Protected (Sanctum) ───────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        // User Profile
        Route::get('user/profile', [UserController::class, 'profile'])->name('user.profile');

        // Cart
        Route::get('cart',              [CartController::class, 'show'])->name('cart.show');
        Route::post('cart',             [CartController::class, 'add'])->name('cart.add');
        Route::delete('cart/{itemId}',  [CartController::class, 'remove'])->name('cart.remove')
            ->where('itemId', '[0-9]+');

        // Wishlist
        Route::get('wishlist',         [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::post('wishlist/sync',   [WishlistController::class, 'sync'])->name('wishlist.sync');

        // Orders
        Route::get('orders',       [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}',  [OrderController::class, 'show'])->name('orders.show')
            ->where('id', '[0-9]+');

        // Checkout & Payments
        Route::post('checkout', [CheckoutController::class, 'process'])->name('checkout');
        Route::post('payments', [PaymentController::class, 'pay'])->name('payments');
    });
});
