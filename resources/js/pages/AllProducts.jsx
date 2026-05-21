import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';
import axios from 'axios';
import SEO from '../components/SEO';
import { useSettings } from '../context/SettingsContext';

const AllProducts = () => {
    const { type } = useParams(); // 'popular', 'new-arrivals', 'just-for-you'
    const mainColor = '#57b500';
    const { settings } = useSettings();
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [title, setTitle] = useState("");
    const [visibleCount, setVisibleCount] = useState(settings?.products_per_page || 10);

    useEffect(() => {
        setVisibleCount(settings?.products_per_page || 10);
    }, [settings]);

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            try {
                let endpoint = "";
                let pageTitle = "";

                if (type === 'popular') {
                    endpoint = '/api/popular-products?limit=all';
                    pageTitle = "Popular Products";
                } else if (type === 'new-arrivals') {
                    endpoint = '/api/new-arrivals?limit=all';
                    pageTitle = "New Arrivals";
                } else if (type === 'just-for-you') {
                    endpoint = '/api/just-for-you?limit=all';
                    pageTitle = "Just For You";
                } else if (type === 'digital') {
                    endpoint = '/api/digital-products?limit=all';
                    pageTitle = "Digital Products";
                } else if (type === 'best-deal') {
                    endpoint = '/api/best-deals?limit=all';
                    pageTitle = "Best Deals";
                } else if (type === 'all') {
                    endpoint = '/api/all-products?limit=all';
                    pageTitle = "All Products";
                } else {
                    endpoint = '/api/all-products?limit=all';
                    pageTitle = "Products";
                }

                setTitle(pageTitle);
                const res = await axios.get(endpoint);
                if (res.data.success) {
                    setProducts(res.data.data);
                }
            } catch (error) {
                console.error("Error fetching products:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
        window.scrollTo(0, 0);
    }, [type]);

    // Data Layer: view_item_list
    useEffect(() => {
        if (products.length > 0) {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'view_item_list',
                item_list_id: String(type),
                item_list_name: title,
                items: products.map((product, index) => ({
                    item_id: String(product.id),
                    item_name: product.name || product.title,
                    price: Number(product.selling_price || product.price || 0),
                    index: index + 1
                }))
            });
        }
    }, [products, title, type]);

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

    return (
        <MasterLayout>
            <SEO title={title} url={window.location.href} />
            {/* Page Header */}
            <div className="bg-light py-4 mb-4 border-bottom">
                <div className="container">
                    <h2 className="fw-bold mb-1 text-dark">{title}</h2>
                    <nav aria-label="breadcrumb">
                        <ol className="breadcrumb mb-0 small">
                            <li className="breadcrumb-item"><Link to="/" className="text-decoration-none text-muted">Home</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">{title}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div className="container pb-5">
                {loading ? (
                    <div className="py-5" style={{ minHeight: '60vh' }}></div>
                ) : (
                    <>
                        {/* Top Bar */}
                        <div className="bg-white p-3 shadow-sm rounded-3 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div className="text-muted small">
                                Showing <span className="fw-bold text-dark">{products.length}</span> results
                            </div>
                            <div className="d-flex align-items-center gap-2 small">
                                <span className="text-muted">Sort by:</span>
                                <select className="form-select form-select-sm border-0 bg-light fw-bold" style={{ width: '130px', boxShadow: 'none' }}>
                                    <option>Latest</option>
                                    <option>Price: Low to High</option>
                                    <option>Price: High to Low</option>
                                </select>
                            </div>
                        </div>

                                {/* Products Grid */}
                        <div className="row g-3 g-md-4">
                            {products.length > 0 ? (
                                products.slice(0, visibleCount).map(product => (
                                    <div key={product.uid} className={finalColClass}>
                                        <ProductCard product={product} />
                                    </div>
                                ))
                            ) : (
                                <div className="col-12 text-center py-5 text-muted">
                                    No products found in this section.
                                </div>
                            )}
                        </div>

                        {products.length > visibleCount && (
                            <div className="text-center mt-4">
                                <button
                                    type="button"
                                    className="btn btn-outline-secondary"
                                    onClick={() => setVisibleCount(prev => Math.min(prev + 10, products.length))}
                                    style={{ minWidth: '180px' }}
                                >
                                    Load More Products
                                </button>
                            </div>
                        )}
                    </>
                )}
            </div>

            <style>{`
                .fade-in-item { opacity: 0; animation: fadeInUp 0.35s ease forwards; }
                @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
                @media (min-width: 992px) {
                    .custom-desktop-col-5 { width: 20%; flex: 0 0 20%; }
                    .custom-desktop-col-8 { width: 12.5%; flex: 0 0 12.5%; }
                }
            `}</style>
        </MasterLayout>
    );
};

export default AllProducts;
