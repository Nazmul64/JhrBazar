import React, { useState, useEffect } from 'react';
import { useLocation, Link } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';
import axios from 'axios';

const SearchResults = () => {
    const location = useLocation();
    const queryParams = new URLSearchParams(location.search);
    const searchTerm = queryParams.get('q') || "";

    const mainColor = '#57b500';

    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchResults = async () => {
            if (!searchTerm) {
                setProducts([]);
                setLoading(false);
                return;
            }

            setLoading(true);
            try {
                const res = await axios.get(`/api/products/search?q=${searchTerm}`);
                if (res.data.success) {
                    setProducts(res.data.data);
                }
            } catch (error) {
                console.error("Error searching products:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchResults();
        window.scrollTo(0, 0);
    }, [searchTerm]);

    // Data Layer: view_item_list
    useEffect(() => {
        if (products.length > 0 && searchTerm) {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'view_item_list',
                item_list_id: 'search_results',
                item_list_name: `Search: ${searchTerm}`,
                items: products.map((product, index) => ({
                    item_id: String(product.id),
                    item_name: product.name || product.title,
                    price: Number(product.selling_price || product.price || 0),
                    index: index + 1
                }))
            });
        }
    }, [products, searchTerm]);

    return (
        <MasterLayout>
            {/* Page Header */}
            <div className="bg-light py-4 mb-4 border-bottom">
                <div className="container">
                    <h2 className="fw-bold mb-1 text-dark">Search Results</h2>
                    <nav aria-label="breadcrumb">
                        <ol className="breadcrumb mb-0 small">
                            <li className="breadcrumb-item"><Link to="/" className="text-decoration-none text-muted">Home</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">Search: "{searchTerm}"</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div className="container pb-5" style={{ minHeight: '60vh' }}>
                {loading ? (
                    <div className="text-center py-5">
                        <div className="spinner-border text-success" role="status">
                            <span className="visually-hidden">Searching...</span>
                        </div>
                        <p className="mt-3 text-muted">Searching for "{searchTerm}"...</p>
                    </div>
                ) : (
                    <>
                        {/* Results Count Bar */}
                        <div className="bg-white p-3 shadow-sm rounded-3 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div className="text-muted small">
                                Found <span className="fw-bold text-dark">{products.length}</span> items for <span className="fw-bold text-primary">"{searchTerm}"</span>
                            </div>
                            <div className="d-flex align-items-center gap-2 small">
                                <span className="text-muted">Sort by:</span>
                                <select className="form-select form-select-sm border-0 bg-light fw-bold" style={{ width: '130px', boxShadow: 'none' }}>
                                    <option>Relevance</option>
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
                                <div className="col-12 text-center py-5">
                                    <div className="mb-4 display-1 opacity-25">🔍</div>
                                    <h4 className="fw-bold text-dark">No products found</h4>
                                    <p className="text-muted">We couldn't find any products matching your search.</p>
                                    <Link to="/products-all/all" className="btn btn-success px-4 rounded-pill mt-3">
                                        Browse All Products
                                    </Link>
                                </div>
                            )}
                        </div>
                    </>
                )}
            </div>
        </MasterLayout>
    );
};

export default SearchResults;
