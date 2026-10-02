import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';
import axios from 'axios';

const translations = {
    en: {
        // Navigation
        home: 'Home',
        all_products: 'All Products',
        digital_products: 'Digital Products',
        popular_products: 'Popular Products',
        best_deals: 'Best Deals',
        contact: 'Contact',
        blog: 'Blog',
        about_us: 'About Us',
        terms: 'Terms',
        all_categories: 'All Categories',
        browse_categories: 'Browse Categories',
        quick_links: 'Quick Links',
        menu: 'Menu',

        // Header actions
        become_a_seller: 'Become a Seller',
        seller: 'Seller',
        order_tracking: 'Order Tracking',
        login: 'Login',
        dashboard: 'Dashboard',
        wishlist: 'Wishlist',
        cart: 'Cart',
        my_cart: 'My Cart',
        my_wishlist: 'My Wishlist',
        login_register: 'Login / Register',

        // Search
        search: 'Search',
        search_placeholder_default: 'Search products, brands, categories...',
        search_looking_laptop: 'Looking for a laptop?',
        search_looking_phone: 'Looking for a smartphone?',
        search_looking_watch: 'Looking for a smartwatch?',
        search_looking_headphones: 'Looking for headphones?',
        suggestions: 'Suggestions',
        results: 'Results',
        view_all_results: 'View all results',
        no_products_found: 'No products found',

        // Product Cards & Actions
        order_now: 'Order Now',
        buy_now: 'Buy Now',
        add_to_cart: 'Add to Cart',
        view_details: 'View Details',
        out_of_stock: 'Out of Stock',
        in_stock: 'In Stock',
        discount: 'Off',
        currency_symbol: '৳',

        // Sections
        explore_categories: 'Explore Categories',
        flash_sale: 'Flash Sale',
        featured_products: 'Featured Products',
        new_arrivals: 'New Arrivals',
        top_rated_shops: 'Top Rated Shops',
        customer_reviews: 'Customer Reviews',
        free_shipping: '⚡ Free shipping on orders over 5,000 BDT',

        // Theme & Language
        dark_mode: 'Dark Mode',
        light_mode: 'Light Mode',
        language: 'Language',
        english: 'English',
        bangla: 'বাংলা',
        theme: 'Theme',

        // General
        view_all: 'View All',
        close: 'Close',
        back: 'Back',
        loading: 'Loading...',
    },
    bn: {
        // Navigation
        home: 'হোম',
        all_products: 'সকল প্রোডাক্ট',
        digital_products: 'ডিজিটাল প্রোডাক্ট',
        popular_products: 'জনপ্রিয় প্রোডাক্ট',
        best_deals: 'সেরা ডিল',
        contact: 'যোগাযোগ',
        blog: 'ব্লগ',
        about_us: 'আমাদের সম্পর্কে',
        terms: 'শর্তাবলী',
        all_categories: 'সব ক্যাটাগরি',
        browse_categories: 'ক্যাটাগরি ব্রাউজ করুন',
        quick_links: 'কুইক লিঙ্ক',
        menu: 'মেন্যু',

        // Header actions
        become_a_seller: 'সেলার হন',
        seller: 'সেলার',
        order_tracking: 'অর্ডার ট্র্যাকিং',
        login: 'লগইন',
        dashboard: 'ড্যাশবোর্ড',
        wishlist: 'উইশলিস্ট',
        cart: 'কার্ট',
        my_cart: 'আমার কার্ট',
        my_wishlist: 'আমার উইশলিস্ট',
        login_register: 'লগইন / রেজিস্টার',

        // Search
        search: 'অনুসন্ধান',
        search_placeholder_default: 'পণ্য, ব্র্যান্ড বা ক্যাটাগরি অনুসন্ধান করুন...',
        search_looking_laptop: 'ল্যাপটপ খুঁজছেন?',
        search_looking_phone: 'স্মার্টফোন খুঁজছেন?',
        search_looking_watch: 'স্মার্টওয়াচ খুঁজছেন?',
        search_looking_headphones: 'হেডফোন খুঁজছেন?',
        suggestions: 'পরামর্শ',
        results: 'ফলাফল',
        view_all_results: 'সকল ফলাফল দেখুন',
        no_products_found: 'কোন পণ্য পাওয়া যায়নি',

        // Product Cards & Actions
        order_now: 'অর্ডার করুন',
        buy_now: 'এখনই কিনুন',
        add_to_cart: 'কার্টে রাখুন',
        view_details: 'বিস্তারিত দেখুন',
        out_of_stock: 'স্টক শেষ',
        in_stock: 'স্টকে আছে',
        discount: 'ছাড়',
        currency_symbol: '৳',

        // Sections
        explore_categories: 'ক্যাটাগরি সমূহ',
        flash_sale: 'ফ্ল্যাশ সেল',
        featured_products: 'জনপ্রিয় পণ্য',
        new_arrivals: 'নতুন পণ্য',
        top_rated_shops: 'টপ রেটেড শপ',
        customer_reviews: 'কাস্টমার রিভিউ',
        free_shipping: '⚡ ৫,০০০ টাকার বেশি অর্ডারে ফ্রি ডেলিভারি',

        // Theme & Language
        dark_mode: 'ডার্ক মোড',
        light_mode: 'লাইট মোড',
        language: 'ভাষা',
        english: 'English',
        bangla: 'বাংলা',
        theme: 'থিম',

        // General
        view_all: 'সব দেখুন',
        close: 'বন্ধ করুন',
        back: 'পেছনে যান',
        loading: 'লোড হচ্ছে...',
    }
};

const LanguageThemeContext = createContext();

export const LanguageThemeProvider = ({ children }) => {
    // Default theme is 'light' as requested by user
    const [theme, setThemeState] = useState(() => {
        return localStorage.getItem('app_theme') || 'light';
    });

    // Default language can be from localStorage or 'en'
    const [language, setLanguageState] = useState(() => {
        return localStorage.getItem('app_language') || 'en';
    });

    // Apply theme to DOM
    const applyThemeToDOM = (currentTheme) => {
        const root = document.documentElement;
        const body = document.body;
        
        root.setAttribute('data-theme', currentTheme);
        if (currentTheme === 'dark') {
            root.classList.add('dark');
            body.classList.add('dark-mode');
            body.style.backgroundColor = '#0b0f19';
            body.style.color = '#f1f5f9';
        } else {
            root.classList.remove('dark');
            body.classList.remove('dark-mode');
            body.style.backgroundColor = '#f3f4f6';
            body.style.color = '#333333';
        }
    };

    useEffect(() => {
        applyThemeToDOM(theme);
    }, [theme]);

    // Initial preference load from API / localStorage
    useEffect(() => {
        const fetchPreferences = async () => {
            try {
                const res = await axios.get('/api/preferences');
                if (res.data.success && res.data.data) {
                    const serverPref = res.data.data;
                    const savedTheme = localStorage.getItem('app_theme');
                    const savedLang = localStorage.getItem('app_language');

                    if (!savedTheme && serverPref.theme_mode) {
                        setThemeState(serverPref.theme_mode);
                    }
                    if (!savedLang && serverPref.language) {
                        setLanguageState(serverPref.language);
                    }
                }
            } catch (err) {
                // Ignore silent failure
            }
        };

        fetchPreferences();
    }, []);

    const setTheme = useCallback((newTheme) => {
        const t = newTheme === 'dark' ? 'dark' : 'light';
        setThemeState(t);
        localStorage.setItem('app_theme', t);
        applyThemeToDOM(t);

        // Sync with backend API
        axios.post('/api/preferences', { theme_mode: t }).catch(() => {});
    }, []);

    const toggleTheme = useCallback(() => {
        setTheme(theme === 'dark' ? 'light' : 'dark');
    }, [theme, setTheme]);

    const setLanguage = useCallback((newLang) => {
        const l = newLang === 'bn' ? 'bn' : 'en';
        setLanguageState(l);
        localStorage.setItem('app_language', l);

        // Sync with backend API
        axios.post('/api/preferences', { language: l }).catch(() => {});
    }, []);

    const toggleLanguage = useCallback(() => {
        setLanguage(language === 'bn' ? 'en' : 'bn');
    }, [language, setLanguage]);

    // Translation function
    const t = useCallback((key, fallback = '') => {
        const currentDictionary = translations[language] || translations.en;
        if (currentDictionary && currentDictionary[key] !== undefined) {
            return currentDictionary[key];
        }
        if (translations.en && translations.en[key] !== undefined) {
            return translations.en[key];
        }
        return fallback || key;
    }, [language]);

    // Helper to format English numbers into Bangla if Bangla is selected
    const formatNumber = useCallback((num) => {
        if (num === null || num === undefined) return '';
        if (language === 'bn') {
            const banglaDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return String(num).replace(/[0-9]/g, (w) => banglaDigits[+w]);
        }
        return String(num);
    }, [language]);

    return (
        <LanguageThemeContext.Provider value={{
            theme,
            isDark: theme === 'dark',
            setTheme,
            toggleTheme,
            language,
            isBangla: language === 'bn',
            setLanguage,
            toggleLanguage,
            t,
            formatNumber,
            translations
        }}>
            {children}
        </LanguageThemeContext.Provider>
    );
};

export const useLanguageTheme = () => useContext(LanguageThemeContext);
export const useTheme = () => {
    const { theme, isDark, setTheme, toggleTheme } = useLanguageTheme();
    return { theme, isDark, setTheme, toggleTheme };
};
export const useLanguage = () => {
    const { language, isBangla, setLanguage, toggleLanguage, t, formatNumber } = useLanguageTheme();
    return { language, isBangla, setLanguage, toggleLanguage, t, formatNumber };
};
