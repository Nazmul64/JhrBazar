import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';
import axios from 'axios';

const ShopDetails = () => {
    const { id } = useParams();
    const mainColor = '#57b500';
    const [products, setProducts] = useState([]);
    const [shop, setShop] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchShopData = async () => {
            setLoading(true);
            try {
                const res = await axios.get(`/api/shop/${id}/products`);
                if (res.data.success) {
                    setProducts(res.data.data);
                    setShop(res.data.shop);
                }
            } catch (error) {
                console.error("Error fetching shop details:", error);
            } finally {
                setLoading(false);
            }
        };
        fetchShopData();
        window.scrollTo(0, 0);
    }, [id]);

    if (loading) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center">
                    <div className="spinner-border text-success" role="status">
                        <span className="visually-hidden">Loading...</span>
                    </div>
                </div>
            </MasterLayout>
        );
    }

    if (!shop) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center">
                    <h3>Shop not found</h3>
                </div>
            </MasterLayout>
        );
    }

    return (
        <MasterLayout>
            {/* Shop Header Section */}
            <div style={{ backgroundColor: '#fff' }}>
                {/* Banner */}
                <div style={{ height: '250px', overflow: 'hidden' }}>
                    <img src={shop.banner} alt="Shop Banner" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                </div>

                {/* Profile Bar */}
                <div className="container py-4">
                    <div className="row align-items-center">
                        <div className="col-md-8 d-flex align-items-center gap-4">
                            <img src={shop.logo} alt="Logo" style={{ width: '100px', height: '100px', borderRadius: '50%', border: '4px solid #f5f5f5', backgroundColor: '#fff' }} />
                            <div>
                                <h3 className="fw-bold mb-1">{shop.name} <span className="badge bg-info small" style={{ fontSize: '10px' }}>ONLINE</span></h3>
                                <p className="text-muted small mb-1">{products.length}+ Items</p>
                                <p className="text-muted small d-none d-md-block" style={{ maxWidth: '500px' }}>{shop.description || 'Welcome to our store! We offer high-quality products to simplify your life.'}</p>
                            </div>
                        </div>
                        <div className="col-md-4 text-md-end">
                            <div className="d-flex flex-column align-items-md-end">
                                <div className="text-warning mb-1">⭐⭐⭐⭐⭐ <span className="text-dark fw-bold">5.0 (0)</span></div>
                                <button 
                                    onClick={() => {
                                        window.dispatchEvent(new CustomEvent('openSellerChat', { 
                                            detail: { sellerId: shop.user_id, sellerName: shop.name } 
                                        }));
                                    }}
                                    className="btn btn-outline-secondary btn-sm rounded-circle"
                                >
                                    💬
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Tabs & Search */}
                <div className="border-top border-bottom">
                    <div className="container d-flex justify-content-between align-items-center py-2">
                        <div className="d-flex gap-3">
                            <button className="btn text-white px-4" style={{ backgroundColor: mainColor, borderRadius: '20px' }}>All Products</button>
                            <button className="btn btn-link text-decoration-none text-dark">Reviews</button>
                        </div>
                        <div className="d-none d-md-block">
                            <div className="input-group" style={{ width: '300px' }}>
                                <input type="text" className="form-control rounded-pill-start border-end-0 bg-light" placeholder="Search product" />
                                <button className="btn bg-light border-start-0 rounded-pill-end">🔍</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Shop Content */}
            <div className="container py-5">
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
                            No products found in this store.
                        </div>
                    )}
                </div>

                {/* Pagination Placeholder */}
                {products.length > 0 && (
                    <div className="d-flex justify-content-center mt-5">
                        <nav>
                            <ul className="pagination">
                                <li className="page-item active"><span className="page-link" style={{ backgroundColor: mainColor, borderColor: mainColor, color: '#fff' }}>1</span></li>
                            </ul>
                        </nav>
                    </div>
                )}
            </div>
        </MasterLayout>
    );
};

export default ShopDetails;
