import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';
import axios from 'axios';
import SEO from '../components/SEO';

const AllProducts = () => {
    const { type } = useParams(); // 'popular', 'new-arrivals', 'just-for-you'
    const mainColor = '#57b500';
    
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [title, setTitle] = useState("");

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
                    <div className="text-center py-5">
                        <div className="spinner-border text-success" role="status">
                            <span className="visually-hidden">Loading...</span>
                        </div>
                    </div>
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
                                products.map(product => (
                                    <div key={product.uid} className="col-6 col-md-4 col-lg-2">
                                        <ProductCard product={product} />
                                    </div>
                                ))
                            ) : (
                                <div className="col-12 text-center py-5 text-muted">
                                    No products found in this section.
                                </div>
                            )}
                        </div>
                    </>
                )}
            </div>
        </MasterLayout>
    );
};

export default AllProducts;
