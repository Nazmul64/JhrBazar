import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';
import axios from 'axios';

const ShopDetails = () => {
    const { id } = useParams();
    const mainColor = '#57b500';
    const [products, setProducts] = useState([]);
    const [reviews, setReviews] = useState([]);
    const [shop, setShop] = useState(null);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState('products'); // 'products' or 'reviews'
    const [reviewsLoading, setReviewsLoading] = useState(false);

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

    const fetchReviews = async () => {
        if (reviews.length > 0) return;
        setReviewsLoading(true);
        try {
            const res = await axios.get(`/api/shop/${shop.user_id}/reviews`);
            if (res.data.success) {
                setReviews(res.data.data);
            }
        } catch (error) {
            console.error("Error fetching reviews:", error);
        } finally {
            setReviewsLoading(false);
        }
    };

    useEffect(() => {
        if (activeTab === 'reviews' && shop) {
            fetchReviews();
        }
    }, [activeTab, shop]);

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
                                <div className="text-warning mb-1">
                                    {Array(5).fill(0).map((_, i) => (
                                        <i key={i} className={`bi bi-star-fill ${i < 5 ? 'text-warning' : 'text-secondary'}`}></i>
                                    ))}
                                    <span className="text-dark fw-bold ms-2">5.0 (0)</span>
                                </div>
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
                            <button 
                                onClick={() => setActiveTab('products')}
                                className={`btn px-4 transition-all ${activeTab === 'products' ? 'text-white' : 'btn-link text-dark text-decoration-none'}`} 
                                style={{ 
                                    backgroundColor: activeTab === 'products' ? mainColor : 'transparent', 
                                    borderRadius: '20px' 
                                }}
                            >
                                All Products
                            </button>
                            <button 
                                onClick={() => setActiveTab('reviews')}
                                className={`btn px-4 transition-all ${activeTab === 'reviews' ? 'text-white' : 'btn-link text-dark text-decoration-none'}`}
                                style={{ 
                                    backgroundColor: activeTab === 'reviews' ? mainColor : 'transparent', 
                                    borderRadius: '20px' 
                                }}
                            >
                                Reviews
                            </button>
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
                {activeTab === 'products' ? (
                    <>
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

                        {products.length > 0 && (
                            <div className="d-flex justify-content-center mt-5">
                                <nav>
                                    <ul className="pagination">
                                        <li className="page-item active"><span className="page-link" style={{ backgroundColor: mainColor, borderColor: mainColor, color: '#fff' }}>1</span></li>
                                    </ul>
                                </nav>
                            </div>
                        )}
                    </>
                ) : (
                    <div className="row justify-content-center">
                        <div className="col-md-8">
                            {reviewsLoading ? (
                                <div className="text-center py-5">
                                    <div className="spinner-border text-success" role="status"></div>
                                </div>
                            ) : reviews.length > 0 ? (
                                reviews.map(review => (
                                    <div key={review.id} className="card border-0 shadow-sm mb-3">
                                        <div className="card-body">
                                            <div className="d-flex align-items-center gap-3 mb-2">
                                                <img 
                                                    src={review.user?.profile_image || '/assets/admin/images/default-avatar.png'} 
                                                    alt={review.user?.name}
                                                    style={{ width: '40px', height: '40px', borderRadius: '50%', objectFit: 'cover' }}
                                                />
                                                <div>
                                                    <h6 className="mb-0 fw-bold">{review.user?.name || 'Customer'}</h6>
                                                    <div className="text-warning small">
                                                        {Array(5).fill(0).map((_, i) => (
                                                            <i key={i} className={`bi bi-star-fill ${i < review.rating ? 'text-warning' : 'text-secondary'}`}></i>
                                                        ))}
                                                    </div>
                                                </div>
                                                <span className="ms-auto text-muted small">{new Date(review.created_at).toLocaleDateString()}</span>
                                            </div>
                                            <p className="mb-0 text-dark" style={{ fontSize: '14px' }}>{review.comment}</p>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <div className="text-center py-5 text-muted">
                                    <i className="bi bi-chat-left-text d-block mb-2" style={{ fontSize: '3rem' }}></i>
                                    <p>No reviews yet for this shop.</p>
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </MasterLayout>
    );
};

export default ShopDetails;
