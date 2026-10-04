import React, { useState, useEffect, useMemo } from 'react';
import { useParams, Link, useLocation } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';
import axios from 'axios';
import SEO from '../components/SEO';
import { useSettings } from '../context/SettingsContext';
import { useLanguage } from '../context/LanguageThemeContext';

const CategoryProducts = () => {
    const { id } = useParams();
    const location = useLocation();
    const isSubCategory = location.pathname.includes('subcategory');

    const { settings } = useSettings();
    const { isBangla, formatNumber, t } = useLanguage();
    const primaryColor = settings?.primary_color || '#57b500';

    const [categories, setCategories] = useState([]);
    const [allProducts, setAllProducts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [isFiltering, setIsFiltering] = useState(false);
    const [activeCategoryName, setActiveCategoryName] = useState("");
    
    // Price Filter States — by default price is 0
    const [minPrice, setMinPrice] = useState(0);
    const [maxPrice, setMaxPrice] = useState(0);
    const [maxBound, setMaxBound] = useState(5000);
    const [hasInteracted, setHasInteracted] = useState(false);
    const [sortBy, setSortBy] = useState('latest');
    const [visibleCount, setVisibleCount] = useState(settings?.products_per_page || 12);
    const [showMobileFilter, setShowMobileFilter] = useState(false);

    useEffect(() => {
        setVisibleCount(settings?.products_per_page || 12);
    }, [settings]);

    const formatImagePath = (path) => {
        if (!path) return '/placeholder.jpg';
        if (path.startsWith('http')) return path;
        return path.startsWith('/') ? path : '/' + path;
    };

    // Fetch Category, Subcategory & Products
    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            try {
                // 1. Fetch Categories for Sidebar
                const catRes = await axios.get('/api/categories');
                if (catRes.data.success) {
                    setCategories(catRes.data.data);
                }

                // 2. Fetch Category / Subcategory Name
                const nameEndpoint = isSubCategory
                    ? `/api/subcategory/${id}/name`
                    : `/api/category/${id}/name`;

                const nameRes = await axios.get(nameEndpoint);
                if (nameRes.data.success) {
                    setActiveCategoryName(nameRes.data.name);
                }

                // 3. Fetch Products
                const endpoint = isSubCategory
                    ? `/api/products/subcategory/${id}`
                    : `/api/products/category/${id}`;

                const prodRes = await axios.get(endpoint);
                if (prodRes.data.success && Array.isArray(prodRes.data.data)) {
                    const fetchedProducts = prodRes.data.data;
                    setAllProducts(fetchedProducts);

                    // Compute dynamic maximum bound from actual products
                    if (fetchedProducts.length > 0) {
                        const prices = fetchedProducts
                            .map(p => Number(p.selling_price || p.price || 0))
                            .filter(p => p > 0);
                        const highest = prices.length > 0 ? Math.ceil(Math.max(...prices) / 100) * 100 : 5000;
                        const finalMax = Math.max(highest, 1000);
                        setMaxBound(finalMax);
                    } else {
                        setMaxBound(5000);
                    }
                } else {
                    setAllProducts([]);
                    setMaxBound(5000);
                }

                // Reset price slider to 0 by default on category change
                setMinPrice(0);
                setMaxPrice(0);
                setHasInteracted(false);
            } catch (error) {
                console.error("Error fetching category data:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
        window.scrollTo(0, 0);
    }, [id, isSubCategory]);

    // Handle price change
    const handlePriceChange = (newMin, newMax) => {
        setHasInteracted(true);
        setIsFiltering(true);
        setMinPrice(newMin);
        setMaxPrice(newMax);
        const timer = setTimeout(() => {
            setIsFiltering(false);
        }, 120);
        return () => clearTimeout(timer);
    };

    // Filter is considered active only when user interacted and maxPrice > 0 or minPrice > 0
    const isFilterActive = hasInteracted && (maxPrice > 0 || minPrice > 0);

    // Filter & Sort Products
    const filteredProducts = useMemo(() => {
        let result = [...allProducts];

        // Apply price filter only when active
        if (isFilterActive) {
            result = result.filter(product => {
                const price = Number(product.selling_price || product.price || 0);
                const min = Number(minPrice);
                const max = Number(maxPrice);
                if (max > 0) {
                    return price >= min && price <= max;
                }
                return price >= min;
            });
        }

        // Apply Sorting
        if (sortBy === 'price_low_high') {
            result.sort((a, b) => Number(a.selling_price || a.price || 0) - Number(b.selling_price || b.price || 0));
        } else if (sortBy === 'price_high_low') {
            result.sort((a, b) => Number(b.selling_price || b.price || 0) - Number(a.selling_price || a.price || 0));
        } else if (sortBy === 'discount') {
            result.sort((a, b) => Number(b.discount || b.discount_percentage || 0) - Number(a.discount || a.discount_percentage || 0));
        } else if (sortBy === 'top_rated') {
            result.sort((a, b) => Number(b.reviews_avg_rating || 5) - Number(a.reviews_avg_rating || 5));
        } else {
            // Latest / Default
            result.sort((a, b) => Number(b.id || 0) - Number(a.id || 0));
        }

        return result;
    }, [allProducts, isFilterActive, minPrice, maxPrice, sortBy]);

    // Reset Price Filter back to default 0
    const handleResetFilter = () => {
        setIsFiltering(true);
        setMinPrice(0);
        setMaxPrice(0);
        setHasInteracted(false);
        setTimeout(() => setIsFiltering(false), 120);
    };

    // Data Layer: view_item_list
    useEffect(() => {
        if (filteredProducts.length > 0) {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'view_item_list',
                item_list_id: String(id),
                item_list_name: activeCategoryName,
                items: filteredProducts.map((product, index) => ({
                    item_id: String(product.id),
                    item_name: product.name || product.title,
                    price: Number(product.selling_price || product.price || 0),
                    index: index + 1
                }))
            });
        }
    }, [filteredProducts, activeCategoryName, id]);

    let mobileCol = 6;
    if (settings?.products_per_row_mobile) {
        mobileCol = Math.max(1, Math.floor(12 / parseInt(settings.products_per_row_mobile)));
    }

    let desktopCol = 2;
    let customDesktopClass = '';
    if (settings?.products_per_row_desktop) {
        const perRow = parseInt(settings.products_per_row_desktop);
        if ([1, 2, 3, 4, 6, 12].includes(perRow)) {
            desktopCol = 12 / perRow;
        } else {
            customDesktopClass = `custom-desktop-col-${perRow}`;
            desktopCol = 2; // fallback
        }
    }
    const finalColClass = `col-${mobileCol} col-md-4 col-lg-${desktopCol} ${customDesktopClass} fade-in-item`;

    // Localized Strings
    const labels = {
        home: isBangla ? 'হোম' : 'Home',
        allCategories: isBangla ? 'সব ক্যাটাগরি' : 'All Categories',
        filterByPrice: isBangla ? 'মূল্য ফিল্টার' : 'Filter by Price',
        priceRange: isBangla ? 'প্রাইস রেঞ্জ:' : 'Price Range:',
        min: isBangla ? 'সর্বনিম্ন' : 'Min',
        max: isBangla ? 'সর্বোচ্চ' : 'Max',
        reset: isBangla ? 'রিসেট' : 'Reset',
        totalResults: isBangla ? 'মোট ফলাফল:' : 'Showing results:',
        productsCount: (count) => isBangla ? `${formatNumber(count)} টি প্রোডাক্ট` : `${count} Products`,
        filterApplied: isBangla ? 'ফিল্টার সক্রিয়' : 'Filter Applied',
        sortBy: isBangla ? 'সাজান:' : 'Sort by:',
        sortLatest: isBangla ? 'সর্বশেষ' : 'Latest',
        sortLowHigh: isBangla ? 'দাম: কম থেকে বেশি' : 'Price: Low to High',
        sortHighLow: isBangla ? 'দাম: বেশি থেকে কম' : 'Price: High to Low',
        sortDiscount: isBangla ? 'সর্বাধিক ছাড়' : 'Discount',
        sortTopRated: isBangla ? 'টপ রেটেড' : 'Top Rated',
        availableTitle: isBangla ? 'উপলব্ধ আছে' : 'Available',
        availableText: (count) => isBangla ? `এই মূল্যে ${formatNumber(count)} টি প্রোডাক্ট রয়েছে` : `${count} products found in this price`,
        notFoundTitle: isBangla ? 'পাওয়া যায়নি' : 'Not Found',
        notFoundText: isBangla ? 'এই মূল্যে কোনো প্রোডাক্ট নেই' : 'No products available in this price range',
        defaultStatus: isBangla ? 'ফিল্টার করতে স্লাইড করুন' : 'Slide to filter by price',
        emptyPriceTitle: isBangla ? 'এই প্রাইস রেঞ্জে কোনো প্রোডাক্ট পাওয়া যায়নি' : 'No products found in this price range',
        emptyPriceDesc: isBangla 
            ? `৳${formatNumber(minPrice)} থেকে ৳${formatNumber(maxPrice)} মূল্যের মধ্যে কোনো প্রোডাক্ট খুঁজে পাওয়া যায়নি।`
            : `No products found between ৳${Number(minPrice).toLocaleString()} and ৳${Number(maxPrice).toLocaleString()}.`,
        emptyCategoryTitle: isBangla ? 'এই ক্যাটাগরিতে কোনো প্রোডাক্ট পাওয়া যায়নি' : 'No products found in this category',
        emptyCategoryDesc: isBangla ? 'বর্তমানে এই ক্যাটাগরিতে কোনো প্রোডাক্ট যুক্ত করা নেই।' : 'There are currently no products in this category.',
        resetFilterBtn: isBangla ? 'সকল প্রোডাক্ট দেখুন (Reset Filter)' : 'Show All Products (Reset Filter)',
        backHomeBtn: isBangla ? 'হোম পেজে ফিরে যান' : 'Back to Home',
        loadMore: (rem) => isBangla ? `আরও দেখুন (${formatNumber(rem)} টি বাকি)` : `Load More (${rem} remaining)`,
        loadingProducts: isBangla ? 'প্রোডাক্ট লোড হচ্ছে...' : 'Loading products...',
    };

    return (
        <MasterLayout>
            <SEO title={activeCategoryName} url={window.location.href} />

            {/* Page Header */}
            <div className="bg-light py-4 mb-4 border-bottom">
                <div className="container">
                    <div className="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h2 className="fw-bold mb-1 text-dark" style={{ fontSize: '26px' }}>
                                {activeCategoryName || (isSubCategory ? (isBangla ? 'সাব-ক্যাটাগরি' : 'Sub-Category') : (isBangla ? 'ক্যাটাগরি' : 'Category'))}
                            </h2>
                            <nav aria-label="breadcrumb">
                                <ol className="breadcrumb mb-0 small">
                                    <li className="breadcrumb-item">
                                        <Link to="/" className="text-decoration-none text-muted">{labels.home}</Link>
                                    </li>
                                    <li className="breadcrumb-item active fw-medium text-dark" aria-current="page">
                                        {activeCategoryName}
                                    </li>
                                </ol>
                            </nav>
                        </div>

                        {/* Mobile Filter Toggle Button */}
                        <div className="d-lg-none">
                            <button
                                type="button"
                                className="btn btn-sm btn-outline-dark d-flex align-items-center gap-2 rounded-pill px-3 py-2"
                                onClick={() => setShowMobileFilter(!showMobileFilter)}
                            >
                                <i className="fas fa-sliders-h text-primary"></i>
                                <span className="fw-semibold">{labels.filterByPrice}</span>
                                {isFilterActive && (
                                    <span className="badge rounded-pill bg-success ms-1">●</span>
                                )}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div className="container pb-5">
                <div className="row g-4">
                    {/* Left Sidebar Filter */}
                    <div className={`col-lg-3 ${showMobileFilter ? 'd-block' : 'd-none d-lg-block'}`}>
                        {/* Categories Accordion Card */}
                        <div className="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                            <div className="card-header bg-white border-bottom py-3 px-3 d-flex align-items-center gap-2">
                                <span className="category-icon-badge">📑</span>
                                <h6 className="mb-0 fw-bold text-dark">{labels.allCategories}</h6>
                            </div>
                            <div className="card-body p-0" style={{ maxHeight: '380px', overflowY: 'auto' }}>
                                <ul className="list-group list-group-flush border-0">
                                    {categories.map(cat => {
                                        const isActive = id == cat.id && !isSubCategory;
                                        const hasSub = (cat.sub_categories?.length > 0) || (cat.subCategories?.length > 0);
                                        const subCats = cat.sub_categories || cat.subCategories || [];

                                        return (
                                            <li key={cat.id} className="list-group-item border-0 p-0">
                                                <Link
                                                    to={`/category/${cat.id}`}
                                                    className={`d-flex justify-content-between align-items-center px-3 py-2.5 text-decoration-none transition-all ${isActive ? 'bg-light-success text-success fw-bold' : 'text-dark hover-bg-light'}`}
                                                >
                                                    <div className="d-flex align-items-center gap-2">
                                                        {cat.thumbnail ? (
                                                            <img
                                                                src={formatImagePath(cat.thumbnail)}
                                                                alt=""
                                                                style={{ width: '22px', height: '22px', objectFit: 'contain' }}
                                                                onError={(e) => { e.currentTarget.style.display = 'none'; }}
                                                            />
                                                        ) : null}
                                                        <span className="small">{cat.name}</span>
                                                    </div>
                                                    <i className={`fas ${isActive ? 'fa-chevron-down text-success' : 'fa-chevron-right text-muted'} small`} style={{ fontSize: '11px' }}></i>
                                                </Link>

                                                {/* Subcategories */}
                                                {(isActive || (isSubCategory && subCats.some(s => s.id == id))) && hasSub && (
                                                    <ul className="list-group list-group-flush ps-4 bg-light py-1">
                                                        {subCats.map(sub => (
                                                            <li key={sub.id} className="list-group-item border-0 bg-transparent p-0">
                                                                <Link
                                                                    to={`/subcategory/${sub.id}`}
                                                                    className={`d-flex align-items-center gap-2 py-1.5 px-2 text-decoration-none small ${id == sub.id && isSubCategory ? 'text-success fw-bold' : 'text-muted hover-text-dark'}`}
                                                                >
                                                                    <span>• {sub.name}</span>
                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </ul>
                                                )}
                                            </li>
                                        );
                                    })}
                                </ul>
                            </div>
                        </div>

                        {/* Interactive Modern Price Filter Card */}
                        <div className="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden price-filter-card">
                            <div className="card-header bg-white border-bottom py-3 px-3 d-flex justify-content-between align-items-center">
                                <div className="d-flex align-items-center gap-2">
                                    <span className="price-icon-badge">💰</span>
                                    <h6 className="mb-0 fw-bold text-dark">{labels.filterByPrice}</h6>
                                </div>
                                {isFilterActive && (
                                    <button
                                        type="button"
                                        onClick={handleResetFilter}
                                        className="btn btn-link text-danger p-0 text-decoration-none small fw-semibold"
                                        title={labels.reset}
                                        style={{ fontSize: '12px' }}
                                    >
                                        <i className="fas fa-undo-alt me-1"></i>{labels.reset}
                                    </button>
                                )}
                            </div>

                            <div className="card-body p-3">
                                {/* Price Slider Display */}
                                <div className="price-slider-container">
                                    <div className="d-flex justify-content-between align-items-center mb-2">
                                        <span className="text-muted small fw-medium">{labels.priceRange}</span>
                                        <span className="fw-bold price-badge px-2 py-1 rounded-3">
                                            {isBangla 
                                                ? `৳${formatNumber(minPrice)} - ৳${formatNumber(maxPrice)}` 
                                                : `৳${Number(minPrice).toLocaleString()} - ৳${Number(maxPrice).toLocaleString()}`}
                                        </span>
                                    </div>

                                    {/* Slider Input */}
                                    <div className="slider-wrapper my-3 position-relative">
                                        <input
                                            type="range"
                                            className="custom-range-slider"
                                            min="0"
                                            max={maxBound}
                                            step="50"
                                            value={maxPrice}
                                            onChange={(e) => handlePriceChange(minPrice, Number(e.target.value))}
                                            style={{
                                                background: maxPrice > 0 
                                                    ? `linear-gradient(to right, ${primaryColor} 0%, ${primaryColor} ${(maxPrice / (maxBound || 1)) * 100}%, #e2e8f0 ${(maxPrice / (maxBound || 1)) * 100}%, #e2e8f0 100%)`
                                                    : '#e2e8f0'
                                            }}
                                        />
                                    </div>

                                    {/* Dual Numeric Input Boxes */}
                                    <div className="row g-2 align-items-center mb-3">
                                        <div className="col-5">
                                            <label className="text-muted small mb-1" style={{ fontSize: '11px' }}>{labels.min}</label>
                                            <div className="input-group input-group-sm">
                                                <span className="input-group-text bg-light text-muted border-end-0">৳</span>
                                                <input
                                                    type="number"
                                                    className="form-control form-control-sm border-start-0 text-center fw-bold"
                                                    min="0"
                                                    max={maxBound}
                                                    value={minPrice}
                                                    onChange={(e) => handlePriceChange(Math.max(0, Number(e.target.value)), maxPrice)}
                                                />
                                            </div>
                                        </div>
                                        <div className="col-2 text-center text-muted fw-bold pt-3">-</div>
                                        <div className="col-5">
                                            <label className="text-muted small mb-1" style={{ fontSize: '11px' }}>{labels.max}</label>
                                            <div className="input-group input-group-sm">
                                                <span className="input-group-text bg-light text-muted border-end-0">৳</span>
                                                <input
                                                    type="number"
                                                    className="form-control form-control-sm border-start-0 text-center fw-bold"
                                                    min="0"
                                                    max={maxBound}
                                                    value={maxPrice}
                                                    onChange={(e) => handlePriceChange(minPrice, Number(e.target.value))}
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Live Availability Status Indicator */}
                                    <div className="mt-3">
                                        {!isFilterActive ? (
                                            <div className="status-box status-default d-flex align-items-center gap-2 p-2 rounded-3">
                                                <span className="status-dot bg-secondary"></span>
                                                <div className="small lh-sm text-muted" style={{ fontSize: '11.5px' }}>
                                                    {labels.defaultStatus}
                                                </div>
                                            </div>
                                        ) : filteredProducts.length > 0 ? (
                                            <div className="status-box status-available d-flex align-items-center gap-2 p-2 rounded-3">
                                                <span className="status-dot-pulse bg-success"></span>
                                                <div className="small lh-sm">
                                                    <span className="fw-bold text-success">{labels.availableTitle}</span>
                                                    <div className="text-muted" style={{ fontSize: '11px' }}>
                                                        {labels.availableText(filteredProducts.length)}
                                                    </div>
                                                </div>
                                            </div>
                                        ) : (
                                            <div className="status-box status-empty d-flex align-items-center gap-2 p-2 rounded-3">
                                                <span className="status-dot bg-danger"></span>
                                                <div className="small lh-sm">
                                                    <span className="fw-bold text-danger">{labels.notFoundTitle}</span>
                                                    <div className="text-muted" style={{ fontSize: '11px' }}>
                                                        {labels.notFoundText}
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right Product Grid */}
                    <div className="col-lg-9">
                        {loading ? (
                            <div className="py-5 text-center" style={{ minHeight: '50vh' }}>
                                <div className="spinner-border text-success" role="status" style={{ width: '3rem', height: '3rem' }}>
                                    <span className="visually-hidden">Loading...</span>
                                </div>
                                <p className="mt-3 text-muted">{labels.loadingProducts}</p>
                            </div>
                        ) : (
                            <>
                                {/* Top Sort & Count Bar */}
                                <div className="bg-white p-3 shadow-sm rounded-4 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                                    <div className="text-muted small d-flex align-items-center gap-2">
                                        <span>{labels.totalResults}</span>
                                        <span className="badge bg-light text-dark border px-2.5 py-1.5 fs-7 fw-bold">
                                            {labels.productsCount(filteredProducts.length)}
                                        </span>
                                        {isFilterActive && (
                                            <span className="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                {labels.filterApplied}
                                            </span>
                                        )}
                                    </div>

                                    <div className="d-flex align-items-center gap-2 small">
                                        <span className="text-muted fw-medium text-nowrap">{labels.sortBy}</span>
                                        <select
                                            className="form-select form-select-sm border-0 bg-light fw-semibold shadow-none rounded-3"
                                            style={{ width: '170px', cursor: 'pointer' }}
                                            value={sortBy}
                                            onChange={(e) => setSortBy(e.target.value)}
                                        >
                                            <option value="latest">{labels.sortLatest}</option>
                                            <option value="price_low_high">{labels.sortLowHigh}</option>
                                            <option value="price_high_low">{labels.sortHighLow}</option>
                                            <option value="discount">{labels.sortDiscount}</option>
                                            <option value="top_rated">{labels.sortTopRated}</option>
                                        </select>
                                    </div>
                                </div>

                                {/* Products Grid with Live Filtering State */}
                                <div className={`row g-3 g-md-4 transition-all ${isFiltering ? 'opacity-50' : 'opacity-100'}`}>
                                    {filteredProducts.length > 0 ? (
                                        filteredProducts.slice(0, visibleCount).map(product => (
                                            <div key={product.uid || product.id} className={finalColClass}>
                                                <ProductCard product={product} />
                                            </div>
                                        ))
                                    ) : allProducts.length > 0 ? (
                                        /* Case 1: Products exist in category, but not in selected price range */
                                        <div className="col-12 text-center py-5">
                                            <div className="empty-state-card bg-white p-5 rounded-4 shadow-sm mx-auto" style={{ maxWidth: '520px' }}>
                                                <div className="empty-state-icon mb-3">
                                                    <span className="p-3 bg-light-danger rounded-circle d-inline-flex text-danger fs-1">
                                                        <i className="fas fa-search-dollar"></i>
                                                    </span>
                                                </div>
                                                <h5 className="fw-bold text-dark mb-2">{labels.emptyPriceTitle}</h5>
                                                <p className="text-muted small mb-4">
                                                    {labels.emptyPriceDesc}
                                                </p>
                                                <button
                                                    type="button"
                                                    onClick={handleResetFilter}
                                                    className="btn btn-success px-4 py-2 rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center gap-2"
                                                >
                                                    <i className="fas fa-sync-alt"></i>
                                                    {labels.resetFilterBtn}
                                                </button>
                                            </div>
                                        </div>
                                    ) : (
                                        /* Case 2: No products in this category at all */
                                        <div className="col-12 text-center py-5">
                                            <div className="empty-state-card bg-white p-5 rounded-4 shadow-sm mx-auto" style={{ maxWidth: '520px' }}>
                                                <div className="empty-state-icon mb-3">
                                                    <span className="p-3 bg-light rounded-circle d-inline-flex text-muted fs-1">
                                                        <i className="fas fa-box-open"></i>
                                                    </span>
                                                </div>
                                                <h5 className="fw-bold text-dark mb-2">{labels.emptyCategoryTitle}</h5>
                                                <p className="text-muted small mb-4">
                                                    {labels.emptyCategoryDesc}
                                                </p>
                                                <Link
                                                    to="/"
                                                    className="btn btn-outline-dark px-4 py-2 rounded-pill fw-semibold"
                                                >
                                                    {labels.backHomeBtn}
                                                </Link>
                                            </div>
                                        </div>
                                    )}
                                </div>

                                {/* Load More Button */}
                                {filteredProducts.length > visibleCount && (
                                    <div className="text-center mt-5">
                                        <button
                                            type="button"
                                            className="btn btn-outline-success px-4 py-2.5 rounded-pill fw-semibold shadow-sm"
                                            onClick={() => setVisibleCount(prev => Math.min(prev + 12, filteredProducts.length))}
                                            style={{ minWidth: '220px' }}
                                        >
                                            <i className="fas fa-plus-circle me-2"></i>
                                            {labels.loadMore(filteredProducts.length - visibleCount)}
                                        </button>
                                    </div>
                                )}
                            </>
                        )}
                    </div>
                </div>
            </div>

            <style>{`
                .transition-all { transition: all 0.25s ease-in-out; }
                .hover-bg-light:hover { background-color: #f8fafc; }
                .hover-text-dark:hover { color: #1e293b !important; }
                .bg-light-success { background-color: #f0fdf4 !important; }
                .bg-light-danger { background-color: #fef2f2 !important; }

                .category-icon-badge, .price-icon-badge {
                    font-size: 18px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                }

                .price-filter-card {
                    background: #ffffff;
                    border: 1px solid #f1f5f9;
                }

                .price-badge {
                    background: #f1f5f9;
                    color: #0f172a;
                    font-size: 13px;
                    border: 1px solid #e2e8f0;
                }

                /* Range Slider Styling */
                .custom-range-slider {
                    -webkit-appearance: none;
                    width: 100%;
                    height: 6px;
                    border-radius: 5px;
                    outline: none;
                    cursor: pointer;
                    transition: background 0.15s ease-in-out;
                }

                .custom-range-slider::-webkit-slider-thumb {
                    -webkit-appearance: none;
                    appearance: none;
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    background: #ffffff;
                    border: 3px solid ${primaryColor};
                    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                    cursor: pointer;
                    transition: transform 0.15s, box-shadow 0.15s;
                }

                .custom-range-slider::-webkit-slider-thumb:hover {
                    transform: scale(1.15);
                    box-shadow: 0 3px 8px rgba(0,0,0,0.3);
                }

                .custom-range-slider::-moz-range-thumb {
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    background: #ffffff;
                    border: 3px solid ${primaryColor};
                    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                    cursor: pointer;
                }

                /* Live Status Box */
                .status-box.status-default {
                    background-color: #f8fafc;
                    border: 1px solid #e2e8f0;
                }
                .status-box.status-available {
                    background-color: #f0fdf4;
                    border: 1px solid #bbf7d0;
                }
                .status-box.status-empty {
                    background-color: #fef2f2;
                    border: 1px solid #fecaca;
                }

                .status-dot-pulse {
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    position: relative;
                    display: inline-block;
                }
                .status-dot-pulse::after {
                    content: '';
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    top: 0; left: 0;
                    border-radius: 50%;
                    background-color: inherit;
                    animation: pulse 1.5s infinite ease-out;
                    opacity: 0.7;
                }
                @keyframes pulse {
                    0% { transform: scale(1); opacity: 0.8; }
                    100% { transform: scale(2.5); opacity: 0; }
                }

                .status-dot {
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    display: inline-block;
                }

                .fade-in-item { opacity: 0; animation: fadeInUp 0.35s ease forwards; }
                @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

                @media (min-width: 992px) {
                    .custom-desktop-col-5 { width: 20%; flex: 0 0 20%; }
                    .custom-desktop-col-8 { width: 12.5%; flex: 0 0 12.5%; }
                }
            `}</style>
        </MasterLayout>
    );
};

export default CategoryProducts;
