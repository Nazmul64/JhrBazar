<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\FrontendApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerDashboardController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\LeadController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Customer Dashboard Routes
    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index']);
    Route::get('/customer/orders', [CustomerDashboardController::class, 'orders']);
    Route::get('/customer/wishlist', [CustomerDashboardController::class, 'wishlist']);
    Route::post('/customer/update-profile', [CustomerDashboardController::class, 'updateProfile']);
    Route::post('/customer/update-password', [CustomerDashboardController::class, 'updatePassword']);

    // Review Routes
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/reviews/{product_id}/{product_type}', [ReviewController::class, 'fetch']);
});

Route::get('/home-data', [FrontendApiController::class, 'getHomeData']);
Route::get('/categories', [FrontendApiController::class, 'getCategories']);
Route::get('/categories-with-sub', [FrontendApiController::class, 'getCategoriesWithSub']);
Route::get('/settings', [FrontendApiController::class, 'getSettings']);
Route::get('/banners', [FrontendApiController::class, 'getBanners']);
Route::get('/all-products', [FrontendApiController::class, 'getAllProducts']);
Route::get('/category/{id}/name', [FrontendApiController::class, 'getCategoryName']);
Route::get('/subcategory/{id}/name', [FrontendApiController::class, 'getSubCategoryName']);
Route::get('/popular-products', [FrontendApiController::class, 'getPopularProducts']);
Route::get('/new-arrivals', [FrontendApiController::class, 'getNewArrivals']);
Route::get('/just-for-you', [FrontendApiController::class, 'getJustForYouProducts']);
Route::get('/digital-products', [FrontendApiController::class, 'getDigitalProducts']);
Route::get('/best-deals', [FrontendApiController::class, 'getBestDeals']);
Route::get('/top-shops', [FrontendApiController::class, 'getTopShops']);
Route::get('/shop/{seller_id}/products', [FrontendApiController::class, 'getProductsBySeller']);
Route::get('/shop/{seller_id}/reviews', [FrontendApiController::class, 'getReviewsBySeller']);
Route::get('/products/search', [FrontendApiController::class, 'searchProducts']);
Route::get('/footer-data', [FrontendApiController::class, 'getFooterData']);
Route::get('/admin-support', [App\Http\Controllers\Admin\AdminSupportController::class, 'index']);
Route::get('/blog-categories', [FrontendApiController::class, 'getBlogCategories']);
Route::get('/blogs', [FrontendApiController::class, 'getBlogs']);
Route::get('/blog/{slug}', [FrontendApiController::class, 'getBlogDetails']);
Route::get('/page/{slug}', [FrontendApiController::class, 'getPage']);
Route::get('/about-company', [FrontendApiController::class, 'getAboutCompany']);
Route::get('/privacy-policy', [FrontendApiController::class, 'getPrivacyPolicy']);

Route::get('/products/category/{id}', [FrontendApiController::class, 'getProductsByCategory']);
Route::get('/products/subcategory/{id}', [FrontendApiController::class, 'getProductsBySubCategory']);
Route::get('/product/{slug}', [FrontendApiController::class, 'getProductBySlug']);
Route::get('/product/{type}/{id}/related', [FrontendApiController::class, 'getRelatedProducts']);
Route::get('/product/{type}/{id}', [FrontendApiController::class, 'getProductDetails']);
Route::get('/product/{type}/{id}/reviews', [App\Http\Controllers\Api\ReviewController::class, 'getProductReviews']);
Route::get('/recent-reviews', [App\Http\Controllers\Api\ReviewController::class, 'getRecentReviews']);
// Wishlist Routes
Route::get('/wishlist', [App\Http\Controllers\Api\WishlistController::class, 'index']);
Route::post('/wishlist/toggle', [App\Http\Controllers\Api\WishlistController::class, 'toggle']);
Route::post('/wishlist/sync', [App\Http\Controllers\Api\WishlistController::class, 'sync']);

// Checkout Routes
Route::get('/payment-gateways', [App\Http\Controllers\Api\CheckoutController::class, 'getPaymentGateways']);
Route::get('/shipping-charges', [App\Http\Controllers\Api\CheckoutController::class, 'getShippingCharges']);
Route::post('/apply-coupon', [App\Http\Controllers\Api\CheckoutController::class, 'applyCoupon']);
Route::post('/place-order', [App\Http\Controllers\Api\CheckoutController::class, 'placeOrder']);
Route::get('/track-order/{invoice_no}', [App\Http\Controllers\Api\CheckoutController::class, 'trackOrder']);

// Chat Routes
Route::post('/chat/send', [App\Http\Controllers\Api\ChatApiController::class, 'sendMessage']);
Route::get('/chat/messages', [App\Http\Controllers\Api\ChatApiController::class, 'getMessages']);
Route::get('/chat/unread-count', [App\Http\Controllers\Api\ChatApiController::class, 'getUnreadCount']);

// Lead Generation (Incomplete Orders)
Route::post('/leads/save', [LeadController::class, 'store']);

// Customer visit tracking API (public client-side react hook)
Route::post('track-visit', [App\Http\Controllers\Admin\CustomerDetectorController::class, 'trackVisit']);

// Blocked IP Check API for cyber security warning page
Route::get('check-ip-blocked', [App\Http\Controllers\Admin\FraudCheckerController::class, 'checkIpBlocked']);
