import React, { useState, useEffect } from 'react';
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
    const [loading, setLoading] = useState(!homeData);

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
                            <span className="badge bg-danger text-white rounded-pill" style={{ fontSize: '10px' }}>BIG SAVINGS</span>
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
                            <span className="badge bg-warning text-dark rounded-pill" style={{ fontSize: '10px' }}>E-BOOKS &amp; SOFTWARES</span>
                        </div>
                        <Link to="/products-all/digital" className="btn btn-link text-muted text-decoration-none small" style={{ fontSize: '13px' }}>View All →</Link>
                    </div>
                    {renderProductGrid(digitalProducts)}
                </section>
            )}

            {/* ===== Top Rated Shops ===== */}
            <TopRatedShops shops={topShops} loading={loading} />

            {/* ===== Customer Reviews ===== */}
            {!loading && recentReviews.length > 0 && (
                <section className="container mb-4 mt-4">
                    <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <div className="d-flex align-items-center gap-2">
                            <h4 className="fw-bold mb-0" style={{ color: '#333' }}>কাস্টমার রিভিউ</h4>
                            <span className="badge rounded-pill text-white" style={{ backgroundColor: mainColor, fontSize: '10px' }}>REVIEWS</span>
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
                            <span className="badge rounded-pill text-white" style={{ backgroundColor: mainColor, fontSize: '10px' }}>FEATURED</span>
                        </div>
                    </div>
                    {renderProductGrid(section.products)}
                </section>
            ))}


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
