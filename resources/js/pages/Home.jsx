import React, { useState, useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import HeroSection from '../components/HeroSection';
import Categories from '../components/Categories';
import ProductCard from '../components/ProductCard';
import TopRatedShops from '../components/TopRatedShops';
import axios from 'axios';
import { useSettings } from '../context/SettingsContext';
import SEO from '../components/SEO';

const Home = () => {
    const { settings, homeData, setHomeData } = useSettings();
    const mainColor = settings?.primary_color || window.initialSettings?.primary_color || '#57b500';
    const importantColor = settings?.important_color || window.initialSettings?.important_color || '#ffffff';
    const importantBgColor = settings?.important_background_color || window.initialSettings?.important_background_color || '#dc3545';


    const [popularProducts, setPopularProducts] = useState(homeData?.popularProducts || []);
    const [newArrivals, setNewArrivals] = useState(homeData?.newArrivals || []);
    const [justForYouProducts, setJustForYouProducts] = useState(homeData?.justForYouProducts || []);
    const [digitalProducts, setDigitalProducts] = useState(homeData?.digitalProducts || []);
    const [bestDeals, setBestDeals] = useState(homeData?.bestDeals || []);
    const [allProducts, setAllProducts] = useState(homeData?.allProducts || []);
    const [banners, setBanners] = useState(homeData?.banners || []);
    const [categories, setCategories] = useState(homeData?.categories || []);
    const [topShops, setTopShops] = useState(homeData?.topShops || []);
    const [recentReviews, setRecentReviews] = useState(homeData?.recentReviews || []);
    const [frontendSections, setFrontendSections] = useState(homeData?.frontendSections || []);
    const [ourBrands, setOurBrands] = useState(homeData?.ourBrands || []);
    const [loading, setLoading] = useState(!homeData);
    const brandSliderRef = useRef(null);

    const applyData = (data) => {
        setPopularProducts(data.popularProducts || []);
        setNewArrivals(data.newArrivals || []);
        setJustForYouProducts(data.justForYouProducts || []);
        setDigitalProducts(data.digitalProducts || []);
        setBestDeals(data.bestDeals || []);
        setAllProducts(data.allProducts || []);
        setBanners(data.banners || []);
        setCategories(data.categories || []);
        setTopShops(data.topShops || []);
        setRecentReviews(data.recentReviews || []);
        setFrontendSections(data.frontendSections || []);
        setOurBrands(data.ourBrands || []);
    };

    useEffect(() => {
        const fetchData = async () => {
            try {
                const res = await axios.get('/api/home-data');
                if (res.data.success) {
                    const data = res.data.data;
                    setHomeData(data);
                    applyData(data);
                }
            } catch (error) {
                console.error("Error fetching homepage data:", error);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, []);

    // Data Layer: view_item_list for Home Sections
    useEffect(() => {
        if (!loading) {
            window.dataLayer = window.dataLayer || [];

            if (popularProducts.length > 0) {
                window.dataLayer.push({
                    event: 'view_item_list',
                    item_list_id: 'home_popular',
                    item_list_name: 'Popular Products',
                    items: popularProducts.map((p, i) => ({
                        item_id: String(p.id),
                        item_name: p.title || p.name,
                        price: Number(p.selling_price || p.price || 0),
                        index: i + 1
                    }))
                });
            }

            if (newArrivals.length > 0) {
                window.dataLayer.push({
                    event: 'view_item_list',
                    item_list_id: 'home_new_arrivals',
                    item_list_name: 'New Arrivals',
                    items: newArrivals.map((p, i) => ({
                        item_id: String(p.id),
                        item_name: p.title || p.name,
                        price: Number(p.selling_price || p.price || 0),
                        index: i + 1
                    }))
                });
            }

            if (bestDeals.length > 0) {
                window.dataLayer.push({
                    event: 'view_item_list',
                    item_list_id: 'home_best_deals',
                    item_list_name: 'Best Deals',
                    items: bestDeals.map((p, i) => ({
                        item_id: String(p.id),
                        item_name: p.title || p.name,
                        price: Number(p.selling_price || p.price || 0),
                        index: i + 1
                    }))
                });
            }
        }
    }, [loading, popularProducts, newArrivals, bestDeals]);

    const renderSpinner = () => {
        return null; // No spinner as per user request for <1s loading
    };

    const renderProductGrid = (products, colClass = null) => {
        let finalColClass = colClass;

        if (!colClass) {
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
            finalColClass = `col-${mobileCol} col-md-4 col-lg-${desktopCol} ${customDesktopClass}`;
        }

        return (
            <div className="row g-2 g-md-3">
                {products.map(product => (
                    <div key={product.uid} className={finalColClass}>
                        <ProductCard product={product} />
                    </div>
                ))}
            </div>
        );
    };

    return (
        <MasterLayout>
            <SEO
                title={settings?.website_title}
                siteName={settings?.website_name}
                description={settings?.meta_description}
                keywords={settings?.meta_keywords}
                image={settings?.og_image}
                url={window.location.href}
            />
            {/* Hero and Categories */}
            <HeroSection banners={banners} categories={categories} loading={loading} />
            <Categories categories={categories} loading={loading} />

            {/* ===== Popular Products ===== */}
            {!loading && popularProducts.length > 0 && (
                <section className="container mb-4">
                    <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <h4 className="fw-bold mb-0" style={{ color: '#333' }}>Popular Products</h4>
                        <Link to="/products-all/popular" className="btn btn-link text-muted text-decoration-none small" style={{ fontSize: '13px' }}>View All →</Link>
                    </div>
                    {renderProductGrid(popularProducts)}
                </section>
            )}

            {/* ===== New Arrivals ===== */}
            {!loading && newArrivals.length > 0 && (
                <section className="container mb-4">
                    <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <h4 className="fw-bold mb-0" style={{ color: '#333' }}>New Arrivals</h4>
                        <Link to="/products-all/new-arrivals" className="btn btn-link text-muted text-decoration-none small" style={{ fontSize: '13px' }}>View All →</Link>
                    </div>
                    {renderProductGrid(newArrivals)}
                </section>
            )}

            {/* ===== Best Deals ===== */}
            {!loading && bestDeals.length > 0 && (
                <section className="container mb-4">
                    <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <div className="d-flex align-items-center gap-2">
                            <h4 className="fw-bold mb-0" style={{ color: '#333' }}>Best Deals</h4>
                            <span className="badge rounded-pill" style={{ backgroundColor: importantBgColor, color: importantColor, fontSize: '10px' }}>BIG SAVINGS</span>
                        </div>
                        <Link to="/products-all/best-deal" className="btn btn-link text-muted text-decoration-none small" style={{ fontSize: '13px' }}>View All →</Link>
                    </div>
                    {renderProductGrid(bestDeals)}
                </section>
            )}

            {/* ===== Digital Products ===== */}
            {!loading && digitalProducts.length > 0 && (
                <section className="container mb-4">
                    <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <div className="d-flex align-items-center gap-2">
                            <h4 className="fw-bold mb-0" style={{ color: '#333' }}>Digital Products</h4>
                            <span className="badge rounded-pill" style={{ backgroundColor: importantBgColor, color: importantColor, fontSize: '10px' }}>E-BOOKS &amp; SOFTWARES</span>
                        </div>
                        <Link to="/products-all/digital" className="btn btn-link text-muted text-decoration-none small" style={{ fontSize: '13px' }}>View All →</Link>
                    </div>
                    {renderProductGrid(digitalProducts)}
                </section>
            )}

            {/* ===== Top Rated Shops ===== */}
            {(!settings || settings.top_rated_shops_status === undefined || settings.top_rated_shops_status == 1) && (
                <TopRatedShops shops={topShops} loading={loading} />
            )}

            {/* ===== Customer Reviews ===== */}
            {!loading && recentReviews.length > 0 && (
                <section className="container mb-4 mt-4">
                    <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <div className="d-flex align-items-center gap-2">
                            <h4 className="fw-bold mb-0" style={{ color: '#333' }}>কাস্টমার রিভিউ</h4>
                            <span className="badge rounded-pill" style={{ backgroundColor: importantBgColor, color: importantColor, fontSize: '10px' }}>REVIEWS</span>
                        </div>
                    </div>
                    <div className="row g-3">
                        {recentReviews.map(review => (
                            <div key={review.id} className="col-md-4">
                                <div className="card h-100 border-0 shadow-sm p-4" style={{ borderRadius: '15px' }}>
                                    <div className="d-flex align-items-center gap-3 mb-3">
                                        <div className="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold" style={{ width: '45px', height: '45px' }}>
                                            {review.user?.name?.charAt(0).toUpperCase()}
                                        </div>
                                        <div>
                                            <h6 className="fw-bold mb-0">{review.user?.name}</h6>
                                            <div className="text-warning small">
                                                {'★'.repeat(review.rating)}{'☆'.repeat(5 - review.rating)}
                                            </div>
                                        </div>
                                    </div>
                                    <p className="text-muted small mb-0" style={{ fontStyle: 'italic' }}>
                                        "{review.comment?.substring(0, 120)}{review.comment?.length > 120 ? '...' : ''}"
                                    </p>
                                </div>
                            </div>
                        ))}
                    </div>
                </section>
            )}

            {/* ===== Just For You ===== */}
            {!loading && justForYouProducts.length > 0 && (
                <section className="container mb-4">
                    <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <h4 className="fw-bold mb-0" style={{ color: '#333' }}>Just For You</h4>
                        <Link to="/products-all/just-for-you" className="btn btn-link text-muted text-decoration-none small" style={{ fontSize: '13px' }}>View All →</Link>
                    </div>
                    <>
                        {renderProductGrid(justForYouProducts, 'col-4 col-md-4 col-lg-3')}
                        <div className="text-center mt-5">
                            <Link to="/products-all/just-for-you" style={{
                                padding: '12px 50px',
                                backgroundColor: '#fff',
                                color: mainColor,
                                border: `1.5px solid ${mainColor}`,
                                borderRadius: '30px',
                                fontWeight: 'bold',
                                transition: 'all 0.3s',
                                fontSize: '14px',
                                boxShadow: '0 4px 15px rgba(87, 181, 0, 0.1)',
                                display: 'inline-block',
                                textDecoration: 'none'
                            }} className="load-more-btn">
                                Load More Products
                            </Link>
                        </div>
                    </>
                </section>
            )}

            {/* ===== Dynamic Category-wise Sections (frontendSections) ===== */}
            {!loading && frontendSections && frontendSections.map(section => (
                <section key={section.title} className="container mb-4 mt-4">
                    <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <div className="d-flex align-items-center gap-2">
                            <h4 className="fw-bold mb-0" style={{ color: '#333' }}>{section.title}</h4>
                            <span className="badge rounded-pill" style={{ backgroundColor: importantBgColor, color: importantColor, fontSize: '10px' }}>FEATURED</span>
                        </div>
                    </div>
                    {renderProductGrid(section.products)}
                </section>
            ))}

            {/* ===== Our Brand Slider ===== */}
            {!loading && ourBrands.length > 0 && (
                <section className="container mb-4">
                    <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <h4 className="fw-bold mb-0" style={{ color: '#333' }}>Our Brand Partners</h4>
                    </div>
                    <div className="position-relative">
                        <button
                            type="button"
                            className="brand-slider-arrow prev"
                            onClick={() => {
                                const container = brandSliderRef.current;
                                if (container) container.scrollBy({ left: -container.clientWidth * 0.85, behavior: 'smooth' });
                            }}
                        >
                            ‹
                        </button>
                        <div
                            ref={brandSliderRef}
                            className="our-brand-slider d-flex gap-3 overflow-x-auto pb-2 scroll-smooth"
                        >
                            {ourBrands.map((brand) => (
                                <div
                                    key={brand.id}
                                    className="card border-0 shadow-sm"
                                    style={{ minWidth: 210, maxWidth: 240, borderRadius: 18, overflow: 'hidden', flex: '0 0 auto' }}
                                >
                                    <div className="d-flex align-items-center justify-content-center" style={{ minHeight: 120, padding: '18px 14px', background: '#fff' }}>
                                        <img src={brand.image} alt={brand.title || 'Brand'} style={{ maxHeight: 90, maxWidth: '100%', objectFit: 'contain' }} />
                                    </div>
                                    {brand.title && (
                                        <div className="text-center py-2" style={{ fontSize: 13, color: '#4b5563' }}>
                                            {brand.title}
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                        <button
                            type="button"
                            className="brand-slider-arrow next"
                            onClick={() => {
                                const container = brandSliderRef.current;
                                if (container) container.scrollBy({ left: container.clientWidth * 0.85, behavior: 'smooth' });
                            }}
                        >
                            ›
                        </button>
                    </div>
                    <style>{`
                        .our-brand-slider {
                            scroll-behavior: smooth;
                            -webkit-overflow-scrolling: touch;
                        }
                        .our-brand-slider::-webkit-scrollbar { height: 10px; }
                        .our-brand-slider::-webkit-scrollbar-thumb { background-color: rgba(148,163,184,0.55); border-radius: 999px; }
                        .our-brand-slider::-webkit-scrollbar-track { background: transparent; }

                        .brand-slider-arrow {
                            position: absolute;
                            top: 50%;
                            transform: translateY(-50%);
                            width: 40px;
                            height: 40px;
                            border-radius: 50%;
                            border: none;
                            background: rgba(255,255,255,0.9);
                            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
                            cursor: pointer;
                            z-index: 5;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 22px;
                            color: #111;
                        }
                        .brand-slider-arrow:hover {
                            background: #fff;
                        }
                        .brand-slider-arrow.prev {
                            left: -10px;
                        }
                        .brand-slider-arrow.next {
                            right: -10px;
                        }
                        @media (max-width: 992px) {
                            .brand-slider-arrow.prev { left: 0; }
                            .brand-slider-arrow.next { right: 0; }
                        }
                        @media (max-width: 768px) {
                            .our-brand-slider { gap: 14px; }
                            .brand-slider-arrow { width: 34px; height: 34px; font-size: 20px; }
                        }
                    `}</style>
                </section>
            )}


            <style>{`
                .load-more-btn:hover {
                    background-color: ${mainColor} !important;
                    color: #fff !important;
                    transform: translateY(-3px);
                    box-shadow: 0 6px 20px rgba(87, 181, 0, 0.3) !important;
                }
                @media (min-width: 992px) {
                    .custom-desktop-col-5 { width: 20%; flex: 0 0 20%; }
                    .custom-desktop-col-8 { width: 12.5%; flex: 0 0 12.5%; }
                }
            `}</style>
        </MasterLayout>
    );
};

export default Home;
