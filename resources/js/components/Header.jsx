import React, { useState, useEffect } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import CategoryDropdown from './CategoryDropdown';
import { useCart } from '../context/CartContext';
import { useSettings } from '../context/SettingsContext';
import { useWishlist } from '../context/WishlistContext';
import { useLanguage, useTheme } from '../context/LanguageThemeContext';

const TypingSearchInput = ({ mainColor }) => {
    const navigate = useNavigate();
    const { t, isBangla, formatNumber } = useLanguage();
    
    const placeholderTexts = isBangla ? [
        "পণ্য, ব্র্যান্ড বা ক্যাটাগরি অনুসন্ধান করুন...",
        "ল্যাপটপ খুঁজছেন?",
        "স্মার্টফোন খুঁজছেন?",
        "স্মার্টওয়াচ খুঁজছেন?",
        "হেডফোন খুঁজছেন?"
    ] : [
        "Search products, brands, categories...",
        "Looking for a laptop?",
        "Looking for a smartphone?",
        "Looking for a smartwatch?",
        "Looking for headphones?"
    ];

    const [placeholderIndex, setPlaceholderIndex] = useState(0);
    const [placeholderText, setPlaceholderText] = useState("");
    const [isDeleting, setIsDeleting] = useState(false);

    // Search Logic States
    const [query, setQuery] = useState("");
    const [results, setResults] = useState([]);
    const [isSearching, setIsSearching] = useState(false);
    const [showDropdown, setShowDropdown] = useState(false);

    // Placeholder Animation
    useEffect(() => {
        const currentText = placeholderTexts[placeholderIndex] || placeholderTexts[0];
        let timer;

        if (isDeleting) {
            timer = setTimeout(() => {
                setPlaceholderText(currentText.substring(0, placeholderText.length - 1));
                if (placeholderText.length === 0) {
                    setIsDeleting(false);
                    setPlaceholderIndex((prev) => (prev + 1) % placeholderTexts.length);
                }
            }, 50);
        } else {
            timer = setTimeout(() => {
                setPlaceholderText(currentText.substring(0, placeholderText.length + 1));
                if (placeholderText.length === currentText.length) {
                    timer = setTimeout(() => setIsDeleting(true), 2000);
                }
            }, 100);
        }

        return () => clearTimeout(timer);
    }, [placeholderText, isDeleting, placeholderIndex, isBangla]);

    // Live Search Effect (Debounced)
    useEffect(() => {
        const delayDebounceFn = setTimeout(async () => {
            if (query.trim().length >= 2) {
                setIsSearching(true);
                try {
                    const res = await axios.get(`/api/products/search?q=${encodeURIComponent(query)}`);
                    if (res.data.success) {
                        setResults(res.data.data);
                        setShowDropdown(true);
                    }
                } catch (err) {
                    console.error("Search error:", err);
                } finally {
                    setIsSearching(false);
                }
            } else {
                setResults([]);
                setShowDropdown(false);
            }
        }, 400);

        return () => clearTimeout(delayDebounceFn);
    }, [query]);

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        if (query.trim()) {
            setShowDropdown(false);
            navigate(`/search?q=${encodeURIComponent(query)}`);
        }
    };

    return (
        <div className="position-relative w-100">
            <form onSubmit={handleSearchSubmit} className="input-group search-input-wrapper" style={{
                borderRadius: '10px',
                overflow: 'hidden',
                border: `1px solid var(--border-color, #ddd)`,
                backgroundColor: 'var(--bg-input, #fff)',
                boxShadow: showDropdown ? '0 10px 25px rgba(0,0,0,0.1)' : 'none'
            }}>
                <input
                    type="text"
                    className="form-control border-0 bg-transparent px-3 px-md-4 py-2"
                    placeholder={placeholderText}
                    value={query}
                    onChange={(e) => setQuery(e.target.value)}
                    onFocus={() => query.length >= 2 && setShowDropdown(true)}
                    style={{ fontSize: '14px' }}
                />
                <button type="submit" className="btn border-0 px-3 px-md-4 d-flex align-items-center gap-2" style={{ backgroundColor: 'var(--button-color, #57b500)', color: '#fff', fontWeight: 'bold' }}>
                    {isSearching ? <span className="spinner-border spinner-border-sm me-1"></span> : <><i className="fas fa-search"></i> <span className="d-none d-sm-inline">{t('search')}</span></>}
                </button>
            </form>

            {/* Results Dropdown */}
            {showDropdown && results.length > 0 && (
                <div className="position-absolute w-100 search-results-dropdown shadow-lg rounded-3 mt-1 overflow-hidden" style={{ zIndex: 11000, border: '1px solid var(--border-color, #eee)', backgroundColor: 'var(--bg-dropdown, #fff)' }}>
                    <div className="p-2 bg-light small fw-bold text-muted border-bottom d-flex justify-content-between">
                        <span>{t('suggestions')}</span>
                        <span>{formatNumber(results.length)} {t('results')}</span>
                    </div>
                    <div style={{ maxHeight: '400px', overflowY: 'auto' }}>
                        {results.map((item) => (
                            <Link
                                key={item.uid || item.id}
                                to={`/product/${item.slug}`}
                                className="d-flex align-items-center gap-3 p-2 text-decoration-none border-bottom hover-bg-light"
                                onClick={() => setShowDropdown(false)}
                            >
                                <img src={item.image} alt="" style={{ width: '45px', height: '45px', objectFit: 'cover', borderRadius: '6px' }} />
                                <div className="flex-grow-1 overflow-hidden">
                                    <div className="text-dark fw-bold text-truncate" style={{ fontSize: '13px' }}>{item.title}</div>
                                    <div style={{ color: mainColor, fontWeight: 'bold', fontSize: '12px' }}>৳ {formatNumber((item.price || 0).toLocaleString())}</div>
                                </div>
                                <div className="text-muted" style={{ fontSize: '10px' }}>
                                    <i className="bi bi-chevron-right"></i>
                                </div>
                            </Link>
                        ))}
                    </div>
                    <Link
                        to={`/search?q=${encodeURIComponent(query)}`}
                        className="d-block p-2 text-center text-decoration-none fw-bold small"
                        style={{ color: mainColor, backgroundColor: 'rgba(87, 181, 0, 0.08)' }}
                        onClick={() => setShowDropdown(false)}
                    >
                        {t('view_all_results')} <i className="bi bi-arrow-right ms-1"></i>
                    </Link>
                </div>
            )}

            {showDropdown && results.length === 0 && query.length >= 2 && !isSearching && (
                <div className="position-absolute w-100 search-results-dropdown shadow-lg rounded-3 mt-1 p-4 text-center text-muted small" style={{ zIndex: 11000, border: '1px solid var(--border-color, #eee)', backgroundColor: 'var(--bg-dropdown, #fff)' }}>
                    <div className="mb-2 fs-4">🔍</div>
                    "{query}" {t('no_products_found')}
                </div>
            )}

            {/* Dropdown Overlay to close on click outside */}
            {showDropdown && (
                <div
                    className="position-fixed top-0 start-0 w-100 h-100"
                    style={{ zIndex: 10500, pointerEvents: 'auto' }}
                    onClick={() => setShowDropdown(false)}
                ></div>
            )}
        </div>
    );
};

const Header = () => {
    const [isCategoryOpen, setIsCategoryOpen] = useState(false);
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
    const [expandedCategory, setExpandedCategory] = useState(null);
    const [isSticky, setIsSticky] = useState(false);

    const { settings, categories: realCategories } = useSettings();
    const { cartCount } = useCart();
    const { wishlist } = useWishlist();
    const { language, setLanguage, toggleLanguage, t, isBangla, formatNumber } = useLanguage();
    const { theme, isDark, toggleTheme } = useTheme();

    const location = useLocation();
    
    // Use initialSettings for immediate color application
    const initial = window.initialSettings || {};
    const mainColor = settings?.primary_color || initial.primary_color || '#57b500';
    const topHeaderColor = isDark ? '#0b1120' : (settings?.top_header_color || initial.top_header_color || '#57b500');
    const headerColor = isDark ? '#0f172a' : (settings?.header_color || initial.header_color || '#ffffff');
    const subnavColor = isDark ? '#131c31' : '#ffffff';

    useEffect(() => {
        const handleScroll = () => {
            if (window.scrollY > 40) setIsSticky(true);
            else setIsSticky(false);
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const toggleCategory = (id) => {
        setExpandedCategory(expandedCategory === id ? null : id);
    };

    const navLinkStyle = (path) => ({
        padding: '12px 14px',
        textDecoration: 'none',
        color: location.pathname === path ? mainColor : (isDark ? '#cbd5e1' : '#333'),
        fontWeight: 'bold',
        fontSize: '13.5px',
        position: 'relative',
        transition: 'all 0.3s'
    });

    return (
        <header style={{ fontFamily: 'inherit' }}>
            {/* Top Bar with Language & Dark Mode Toggles */}
            <div style={{ backgroundColor: topHeaderColor, color: '#fff', padding: '6px 0', fontSize: '12px', borderBottom: isDark ? '1px solid #1e293b' : 'none', transition: 'background-color 0.3s ease' }}>
                <div className="container d-flex justify-content-between align-items-center">
                    <div style={{ fontWeight: '600' }} className="d-flex align-items-center gap-2">
                        <span>{settings?.free_shipping_text || t('free_shipping')}</span>
                    </div>
                    
                    <div className="d-flex align-items-center gap-3">
                        {/* Language Switcher Pill */}
                        <div className="d-inline-flex align-items-center bg-dark bg-opacity-25 rounded-pill p-0.5" style={{ border: '1px solid rgba(255,255,255,0.2)' }}>
                            <button
                                type="button"
                                onClick={() => setLanguage('bn')}
                                className={`btn btn-sm py-0 px-2 rounded-pill border-0 text-white ${isBangla ? 'bg-success fw-bold shadow-sm' : 'opacity-75'}`}
                                style={{ fontSize: '11px', transition: 'all 0.2s' }}
                                title="বাংলা ভাষা নির্বাচন করুন"
                            >
                                🇧🇩 বাংলা
                            </button>
                            <button
                                type="button"
                                onClick={() => setLanguage('en')}
                                className={`btn btn-sm py-0 px-2 rounded-pill border-0 text-white ${!isBangla ? 'bg-success fw-bold shadow-sm' : 'opacity-75'}`}
                                style={{ fontSize: '11px', transition: 'all 0.2s' }}
                                title="Switch to English"
                            >
                                🇬🇧 English
                            </button>
                        </div>

                        {/* Dark Mode Toggle Switch */}
                        <button
                            type="button"
                            onClick={toggleTheme}
                            className="btn btn-sm py-0 px-2 rounded-pill border-0 d-flex align-items-center gap-1 text-white bg-dark bg-opacity-25 hover-scale"
                            style={{ fontSize: '11px', border: '1px solid rgba(255,255,255,0.2)', height: '24px', cursor: 'pointer' }}
                            title={isDark ? t('light_mode') : t('dark_mode')}
                        >
                            {isDark ? (
                                <>
                                    <span style={{ color: '#fbbf24' }}>☀️</span>
                                    <span className="d-none d-md-inline fw-semibold">{t('light_mode')}</span>
                                </>
                            ) : (
                                <>
                                    <span style={{ color: '#e2e8f0' }}>🌙</span>
                                    <span className="d-none d-md-inline fw-semibold">{t('dark_mode')}</span>
                                </>
                            )}
                        </button>

                        <div className="d-none d-lg-flex align-items-center gap-3 ms-1">
                            <Link to="/products" className="text-white text-decoration-none opacity-90 hover-opacity">{t('all_products')}</Link>
                            <span className="opacity-50">|</span>
                            <a href="/seller/login" className="text-white text-decoration-none opacity-90 hover-opacity">{t('become_a_seller')}</a>
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Header */}
            <div style={{
                position: isSticky ? 'fixed' : 'relative',
                top: 0, left: 0, width: '100%', zIndex: 10000,
                backgroundColor: headerColor,
                boxShadow: isSticky ? (isDark ? '0 4px 20px rgba(0,0,0,0.6)' : '0 4px 15px rgba(0,0,0,0.08)') : 'none',
                transition: 'background-color 0.3s ease'
            }}>
                <div style={{ padding: '12px 0', borderBottom: `1px solid ${isDark ? '#1e293b' : '#f0f0f0'}` }}>
                    <div className="container">
                        <div className="row align-items-center">
                            {/* Logo & Mobile Menu Toggle */}
                            <div className="col-4 col-lg-2 d-flex align-items-center gap-2">
                                <button className="btn d-lg-none p-0 border-0 text-dark" onClick={() => setIsMobileMenuOpen(true)} style={{ fontSize: '24px' }}>☰</button>
                                <Link to="/" className="text-decoration-none d-flex align-items-center" onClick={(e) => {
                                    if (window.location.pathname === '/') {
                                        e.preventDefault();
                                        window.location.reload();
                                    }
                                }}>
                                    {settings?.logo ? (
                                        <img
                                            src={settings.logo}
                                            alt={settings?.website_name || "Logo"}
                                            style={{ maxHeight: '42px', maxWidth: '160px', objectFit: 'contain' }}
                                            onError={(e) => {
                                                e.target.style.display = 'none';
                                                const fallback = e.target.parentElement.querySelector('.brand-text-logo');
                                                if (fallback) fallback.style.display = 'inline-block';
                                            }}
                                        />
                                    ) : null}
                                    <span 
                                        className="brand-text-logo fw-bold fs-4 text-success" 
                                        style={{ display: settings?.logo ? 'none' : 'inline-block', letterSpacing: '-0.5px' }}
                                    >
                                        {settings?.website_name || "JHR Bazar"}
                                    </span>
                                </Link>
                            </div>

                            {/* Desktop Search */}
                            <div className="col-lg-5 d-none d-lg-block">
                                <TypingSearchInput mainColor={mainColor} />
                            </div>

                            {/* Icons & Header Buttons Row */}
                            <div className="col-8 col-lg-5 d-flex justify-content-end align-items-center gap-2 gap-md-3">
                                {/* Become a Seller Button (Slightly more compact as requested) */}
                                <a 
                                    href="/seller/login" 
                                    className="btn btn-sm text-white px-2.5 py-1.5 d-flex align-items-center gap-1.5 hover-scale shadow-sm" 
                                    style={{ 
                                        backgroundColor: mainColor, 
                                        borderRadius: '6px', 
                                        fontWeight: '700', 
                                        fontSize: '11px', 
                                        transition: 'all 0.3s', 
                                        border: 'none',
                                        whiteSpace: 'nowrap'
                                    }}
                                >
                                    <i className="fas fa-store" style={{ fontSize: '11px' }}></i>
                                    <span className="d-none d-sm-inline">{t('become_a_seller')}</span>
                                    <span className="d-inline d-sm-none">{t('seller')}</span>
                                </a>

                                {/* Order Tracking Button (Slightly more compact as requested) */}
                                <Link 
                                    to="/order-tracking" 
                                    className="text-decoration-none d-none d-md-flex align-items-center gap-1.5 hover-primary" 
                                    style={{ 
                                        border: `1px solid ${mainColor}`, 
                                        borderRadius: '6px', 
                                        padding: '4.5px 9px', 
                                        color: mainColor, 
                                        backgroundColor: `${mainColor}12`, 
                                        transition: 'all 0.3s',
                                        fontSize: '11px',
                                        fontWeight: '700',
                                        whiteSpace: 'nowrap'
                                    }}
                                >
                                    <i className="fas fa-truck" style={{ fontSize: '12px' }}></i>
                                    <span>{t('order_tracking')}</span>
                                </Link>

                                {/* Login / Dashboard */}
                                <Link to={localStorage.getItem('auth_token') ? "/customer/dashboard" : "/customer/login"} className="text-decoration-none text-dark d-flex flex-column align-items-center hover-primary px-1">
                                    <div style={{ fontSize: '20px', lineHeight: '1' }}>👤</div>
                                    <span style={{ fontSize: '10px', fontWeight: 'bold', marginTop: '3px' }}>{localStorage.getItem('auth_token') ? t('dashboard') : t('login')}</span>
                                </Link>

                                {/* Wishlist */}
                                <Link to="/wishlist" className="text-decoration-none text-dark d-flex flex-column align-items-center hover-primary px-1">
                                    <div style={{ fontSize: '20px', position: 'relative', lineHeight: '1' }}>
                                        🤍
                                        <span className="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style={{ fontSize: '9px', padding: '2px 5px' }}>
                                            {formatNumber(wishlist.length)}
                                        </span>
                                    </div>
                                    <span style={{ fontSize: '10px', fontWeight: 'bold', marginTop: '3px' }}>{t('wishlist')}</span>
                                </Link>

                                {/* Cart */}
                                <Link to="/cart" className="text-decoration-none text-dark d-flex flex-column align-items-center hover-primary px-1">
                                    <div style={{ fontSize: '20px', position: 'relative', lineHeight: '1' }}>
                                        🛒
                                        <span className="position-absolute top-0 start-100 translate-middle badge rounded-pill" style={{ backgroundColor: mainColor, fontSize: '9px', padding: '2px 5px', color: '#fff' }}>
                                            {formatNumber(cartCount)}
                                        </span>
                                    </div>
                                    <span style={{ fontSize: '10px', fontWeight: 'bold', marginTop: '3px' }}>{t('cart')}</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Mobile Search Bar (Visible only on mobile/tablet) */}
                <div className="d-lg-none pb-2 pt-1">
                    <div className="container">
                        <TypingSearchInput mainColor={mainColor} />
                    </div>
                </div>

                {/* Desktop Nav Links */}
                <div className="d-none d-lg-block shadow-sm subnav-custom-bg" style={{ backgroundColor: subnavColor, borderBottom: `1px solid ${isDark ? '#1e293b' : '#eee'}`, transition: 'background-color 0.3s ease' }}>
                    <div className="container d-flex align-items-center justify-content-between">
                        <div className="d-flex align-items-center">
                            {!(settings?.sidebar_behavior === 'fixed' && location.pathname === '/') && (
                                <div onMouseEnter={() => setIsCategoryOpen(true)} onMouseLeave={() => setIsCategoryOpen(false)} style={{ position: 'relative', height: '100%' }}>
                                    <div style={{ padding: '11px 22px', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '8px', color: '#fff', backgroundColor: mainColor, fontWeight: 'bold', borderRadius: '4px 4px 0 0', height: '100%' }}>
                                        <i className="fas fa-th-large fs-6"></i>
                                        <span style={{ fontSize: '14.5px' }}>{t('all_categories')}</span>
                                        <i className="fas fa-chevron-down ms-2" style={{ fontSize: '11px', opacity: 0.8 }}></i>
                                    </div>
                                    <CategoryDropdown isOpen={isCategoryOpen} />
                                </div>
                            )}
                            <nav className={`d-flex ${settings?.sidebar_behavior === 'fixed' && location.pathname === '/' ? '' : 'ms-2'}`}>
                                <Link to="/" style={navLinkStyle('/')} className="nav-item-custom">{t('home')}</Link>
                                <Link to="/products-all/all" style={navLinkStyle('/products-all/all')} className="nav-item-custom">{t('all_products')}</Link>
                                <Link to="/products-all/digital" style={navLinkStyle('/products-all/digital')} className="nav-item-custom">{t('digital_products')}</Link>
                                <Link to="/products-all/popular" style={navLinkStyle('/products-all/popular')} className="nav-item-custom">{t('popular_products')}</Link>
                                <Link to="/products-all/best-deal" style={navLinkStyle('/products-all/best-deal')} className="nav-item-custom">{t('best_deals')}</Link>
                                <Link to="/contact" style={navLinkStyle('/contact')} className="nav-item-custom">{t('contact')}</Link>
                                <Link to="/blogs" style={navLinkStyle('/blogs')} className="nav-item-custom">{t('blog')}</Link>
                                <Link to="/about" style={navLinkStyle('/about')} className="nav-item-custom">{t('about_us')}</Link>
                                <Link to="/terms" style={navLinkStyle('/terms')} className="nav-item-custom">{t('terms')}</Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            {/* Mobile Sidebar */}
            {isMobileMenuOpen && (
                <div onClick={() => setIsMobileMenuOpen(false)} style={{ position: 'fixed', top: 0, left: 0, width: '100%', height: '100%', backgroundColor: 'rgba(0,0,0,0.6)', zIndex: 10001 }}>
                    <div onClick={(e) => e.stopPropagation()} className="mobile-sidebar-drawer" style={{ width: '300px', height: '100%', backgroundColor: isDark ? '#111827' : '#fff', color: isDark ? '#f1f5f9' : '#333', overflowY: 'auto', paddingBottom: '80px', boxShadow: '5px 0 25px rgba(0,0,0,0.3)' }}>
                        <div style={{ padding: '16px 20px', backgroundColor: mainColor, color: '#fff', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <h5 className="mb-0 fw-bold">{t('menu')}</h5>
                            <button className="btn text-white p-0 fs-5" onClick={() => setIsMobileMenuOpen(false)}>✕</button>
                        </div>

                        {/* Language and Theme Switch in Mobile Menu */}
                        <div className="p-3 border-bottom d-flex align-items-center justify-content-between" style={{ backgroundColor: isDark ? '#1f2937' : '#f8fafc', borderColor: isDark ? '#374151' : '#eee' }}>
                            <div className="d-flex align-items-center gap-1">
                                <button
                                    onClick={() => setLanguage('bn')}
                                    className={`btn btn-sm py-1 px-2 rounded ${isBangla ? 'btn-success' : 'btn-outline-secondary'}`}
                                    style={{ fontSize: '11px', fontWeight: 'bold' }}
                                >
                                    বাংলা
                                </button>
                                <button
                                    onClick={() => setLanguage('en')}
                                    className={`btn btn-sm py-1 px-2 rounded ${!isBangla ? 'btn-success' : 'btn-outline-secondary'}`}
                                    style={{ fontSize: '11px', fontWeight: 'bold' }}
                                >
                                    EN
                                </button>
                            </div>

                            <button
                                onClick={toggleTheme}
                                className={`btn btn-sm py-1 px-2.5 rounded d-flex align-items-center gap-1.5 ${isDark ? 'btn-warning text-dark' : 'btn-dark'}`}
                                style={{ fontSize: '11px', fontWeight: 'bold' }}
                            >
                                {isDark ? '☀️ Light' : '🌙 Dark'}
                            </button>
                        </div>

                        <div style={{ padding: '10px 0' }}>
                            <div style={{ padding: '12px 20px', fontWeight: 'bold', color: mainColor, borderBottom: `1px solid ${isDark ? '#1f2937' : '#f0f0f0'}` }}>
                                ⣿ {t('browse_categories')}
                            </div>
                            {realCategories.map(cat => (
                                <div key={cat.id} className="border-bottom" style={{ borderColor: isDark ? '#1f2937' : '#f0f0f0' }}>
                                    <div onClick={() => toggleCategory(cat.id)} style={{ padding: '12px 20px', cursor: 'pointer', display: 'flex', justifyContent: 'space-between', fontSize: '14px' }}>
                                        <div className="d-flex align-items-center gap-2">
                                            {cat.thumbnail && typeof cat.thumbnail === 'string' && !cat.thumbnail.includes('placeholder') && !cat.thumbnail.includes('no_image') && (
                                                <img src={cat.thumbnail} alt="" style={{ width: '30px', height: '30px', objectFit: 'cover', borderRadius: '6px' }} />
                                            )}
                                            <Link to={`/category/${cat.id}`} onClick={() => setIsMobileMenuOpen(false)} className="text-decoration-none text-dark">{cat.name}</Link>
                                        </div>
                                        {(cat.sub_categories?.length > 0 || cat.subCategories?.length > 0) && <span>{expandedCategory === cat.id ? '▼' : '▶'}</span>}
                                    </div>
                                    {expandedCategory === cat.id && (cat.sub_categories?.length > 0 || cat.subCategories?.length > 0) && (
                                        <div style={{ backgroundColor: isDark ? '#1f2937' : '#fdfdfd', paddingLeft: '45px' }}>
                                            {(cat.sub_categories || cat.subCategories || []).map(sub => (
                                                <Link
                                                    key={sub.id}
                                                    to={`/subcategory/${sub.id}`}
                                                    onClick={() => setIsMobileMenuOpen(false)}
                                                    className="d-flex align-items-center gap-2 py-2 text-decoration-none text-muted"
                                                    style={{ fontSize: '13px' }}
                                                >
                                                    {sub.thumbnail && typeof sub.thumbnail === 'string' && !sub.thumbnail.includes('placeholder') && !sub.thumbnail.includes('no_image') && (
                                                        <img 
                                                            src={sub.thumbnail} 
                                                            alt="" 
                                                            style={{ width: '24px', height: '24px', objectFit: 'cover', borderRadius: '5px' }} 
                                                        />
                                                    )}
                                                    <span>{sub.name}</span>
                                                </Link>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            ))}
                            <div style={{ padding: '15px 20px', fontWeight: 'bold', marginTop: '10px' }}>{t('quick_links')}</div>
                            <Link to="/" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom" style={{ borderColor: isDark ? '#1f2937' : '#f0f0f0' }}>{t('home')}</Link>
                            <Link to="/products" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom" style={{ borderColor: isDark ? '#1f2937' : '#f0f0f0' }}>{t('all_products')}</Link>
                            <Link to="/cart" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom" style={{ borderColor: isDark ? '#1f2937' : '#f0f0f0' }}>{t('my_cart')}</Link>
                            <Link to="/wishlist" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom" style={{ borderColor: isDark ? '#1f2937' : '#f0f0f0' }}>{t('my_wishlist')}</Link>
                            <Link to="/order-tracking" onClick={() => setIsMobileMenuOpen(false)} className="d-flex align-items-center p-3 px-4 text-decoration-none border-bottom" style={{ color: mainColor, backgroundColor: `${mainColor}12`, fontWeight: 'bold', borderColor: isDark ? '#1f2937' : '#f0f0f0' }}>
                                <i className="fas fa-truck me-3 fs-5"></i> {t('order_tracking')}
                            </Link>
                            {localStorage.getItem('auth_token') ? (
                                <Link to="/customer/dashboard" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom" style={{ borderColor: isDark ? '#1f2937' : '#f0f0f0' }}>{t('dashboard')}</Link>
                            ) : (
                                <Link to="/customer/login" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom" style={{ borderColor: isDark ? '#1f2937' : '#f0f0f0' }}>{t('login_register')}</Link>
                            )}
                            <a href="/seller/login" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom" style={{ borderColor: isDark ? '#1f2937' : '#f0f0f0' }}>{t('become_a_seller')}</a>
                        </div>
                    </div>
                </div>
            )}

            <style>{`
                .hover-scale {
                    transition: transform 0.2s ease, opacity 0.2s ease;
                }
                .hover-scale:hover {
                    transform: scale(1.04) !important;
                    opacity: 0.95;
                    color: #fff !important;
                }
                .hover-opacity:hover {
                    opacity: 1 !important;
                }
                .hover-primary:hover { color: ${mainColor} !important; opacity: 0.85; }
                .nav-item-custom:hover { color: ${mainColor} !important; }
                .nav-item-custom { position: relative; }
                ${location.pathname === '/' ? '.nav-item-custom:first-child::after' : ''}
                ${location.pathname === '/products' ? '.nav-item-custom:nth-child(2)::after' : ''}
                .nav-item-custom::after {
                    content: '';
                    position: absolute;
                    bottom: 0;
                    left: 14px;
                    right: 14px;
                    height: 3px;
                    background-color: #ff4d4d;
                    transform: scaleX(0);
                    transition: transform 0.3s;
                }
                .nav-item-custom[href="${location.pathname}"]::after {
                    transform: scaleX(1);
                }
            `}</style>

            {isSticky && <div style={{ height: '145px' }}></div>}
        </header>
    );
};

export default Header;
