<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SitePreference;
use App\Models\GenaralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{
    /**
     * Get active preferences for current user/guest.
     */
    public function getPreferences(Request $request)
    {
        $sessionId = $request->header('X-Session-Id') ?? $request->input('session_id');
        $userId = Auth::guard('sanctum')->id();
        $ip = $request->ip();

        $preference = null;
        if ($userId) {
            $preference = SitePreference::where('user_id', $userId)->latest()->first();
        }
        if (!$preference && $sessionId) {
            $preference = SitePreference::where('session_id', $sessionId)->latest()->first();
        }

        $general = GenaralSetting::first();
        $defaultLang = $general->default_language ?? 'en';
        $defaultTheme = $general->default_theme_mode ?? 'light';

        return response()->json([
            'success' => true,
            'data' => [
                'language' => $preference->language ?? $defaultLang,
                'theme_mode' => $preference->theme_mode ?? $defaultTheme,
            ]
        ]);
    }

    /**
     * Save user / guest language & theme preferences.
     */
    public function savePreferences(Request $request)
    {
        $request->validate([
            'language' => 'nullable|in:bn,en',
            'theme_mode' => 'nullable|in:light,dark',
        ]);

        $sessionId = $request->header('X-Session-Id') ?? $request->input('session_id') ?? session()->getId();
        $userId = Auth::guard('sanctum')->id();
        $ip = $request->ip();

        $language = $request->input('language', 'en');
        $themeMode = $request->input('theme_mode', 'light');

        $preference = null;
        if ($userId) {
            $preference = SitePreference::where('user_id', $userId)->first();
        } elseif ($sessionId) {
            $preference = SitePreference::where('session_id', $sessionId)->first();
        }

        if (!$preference) {
            $preference = new SitePreference();
            $preference->user_id = $userId;
            $preference->session_id = $sessionId;
            $preference->ip_address = $ip;
        }

        if ($request->has('language')) {
            $preference->language = $language;
        }
        if ($request->has('theme_mode')) {
            $preference->theme_mode = $themeMode;
        }

        $preference->save();

        return response()->json([
            'success' => true,
            'message' => 'Preferences saved successfully',
            'data' => [
                'language' => $preference->language,
                'theme_mode' => $preference->theme_mode,
            ]
        ]);
    }

    /**
     * Get UI translation strings.
     */
    public function getTranslations(Request $request, $lang = 'en')
    {
        $translations = [
            'en' => [
                'home' => 'Home',
                'all_products' => 'All Products',
                'digital_products' => 'Digital Products',
                'popular_products' => 'Popular Products',
                'best_deals' => 'Best Deals',
                'contact' => 'Contact',
                'blog' => 'Blog',
                'about_us' => 'About Us',
                'terms' => 'Terms',
                'all_categories' => 'All Categories',
                'browse_categories' => 'Browse Categories',
                'become_a_seller' => 'Become a Seller',
                'seller' => 'Seller',
                'order_tracking' => 'Order Tracking',
                'login' => 'Login',
                'dashboard' => 'Dashboard',
                'wishlist' => 'Wishlist',
                'cart' => 'Cart',
                'search' => 'Search',
                'search_placeholder' => 'Search products, brands, categories...',
                'order_now' => 'Order Now',
                'buy_now' => 'Buy Now',
                'add_to_cart' => 'Add to Cart',
                'free_shipping' => '⚡ Free shipping on orders over 5,000 BDT',
                'dark_mode' => 'Dark Mode',
                'light_mode' => 'Light Mode',
                'view_all' => 'View All',
                'explore_categories' => 'Explore Categories',
                'hot_deals' => 'Hot Deals',
                'flash_sale' => 'Flash Sale',
                'featured_products' => 'Featured Products',
                'top_rated_shops' => 'Top Rated Shops',
                'quick_links' => 'Quick Links',
                'my_account' => 'My Account',
                'customer_service' => 'Customer Service',
                'contact_info' => 'Contact Info',
                'suggestions' => 'Suggestions',
                'results' => 'Results',
                'view_all_results' => 'View all results',
                'no_products_found' => 'No products found',
                'language' => 'Language',
            ],
            'bn' => [
                'home' => 'হোম',
                'all_products' => 'সকল প্রোডাক্ট',
                'digital_products' => 'ডিজিটাল প্রোডাক্ট',
                'popular_products' => 'জনপ্রিয় প্রোডাক্ট',
                'best_deals' => 'সেরা অফার',
                'contact' => 'যোগাযোগ',
                'blog' => 'ব্লগ',
                'about_us' => 'আমাদের সম্পর্কে',
                'terms' => 'শর্তাবলী',
                'all_categories' => 'সব ক্যাটাগরি',
                'browse_categories' => 'ক্যাটাগরি ব্রাউজ করুন',
                'become_a_seller' => 'সেলার হন',
                'seller' => 'সেলার',
                'order_tracking' => 'অর্ডার ট্র্যাকিং',
                'login' => 'লগইন',
                'dashboard' => 'ড্যাশবোর্ড',
                'wishlist' => 'উইশলিস্ট',
                'cart' => 'কার্ট',
                'search' => 'অনুসন্ধান',
                'search_placeholder' => 'পণ্য, ব্র্যান্ড বা ক্যাটাগরি খুঁজুন...',
                'order_now' => 'অর্ডার করুন',
                'buy_now' => 'এখনই কিনুন',
                'add_to_cart' => 'কার্টে রাখুন',
                'free_shipping' => '⚡ ৫,০০০ টাকার বেশি অর্ডারে ফ্রি ডেলিভারি',
                'dark_mode' => 'ডার্ক মোড',
                'light_mode' => 'লাইট মোড',
                'view_all' => 'সব দেখুন',
                'explore_categories' => 'ক্যাটাগরি সমূহ',
                'hot_deals' => 'হট ডিল',
                'flash_sale' => 'ফ্ল্যাশ সেল',
                'featured_products' => 'ফিচার্ড পণ্য',
                'top_rated_shops' => 'টপ রেটেড শপ',
                'quick_links' => 'গুরুত্বপূর্ণ লিঙ্ক',
                'my_account' => 'আমার একাউন্ট',
                'customer_service' => 'গ্রাহক সেবা',
                'contact_info' => 'যোগাযোগের তথ্য',
                'suggestions' => 'পরামর্শ',
                'results' => 'ফলাফল',
                'view_all_results' => 'সকল ফলাফল দেখুন',
                'no_products_found' => 'কোন পণ্য পাওয়া যায়নি',
                'language' => 'ভাষা',
            ]
        ];

        $selectedLang = in_array($lang, ['bn', 'en']) ? $lang : 'en';

        return response()->json([
            'success' => true,
            'language' => $selectedLang,
            'data' => $translations[$selectedLang] ?? $translations['en'],
            'all' => $translations
        ]);
    }
}
