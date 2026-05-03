import React from 'react';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';

const wishlistItems = [
    { 
        id: 1, title: "iPhone 15 Pro Max - 256GB Titanium Blue", 
        price: 1199.00, oldPrice: 1299.00, discount: 8, sold: 150, rating: '4.9', reviews: 45,
        image: "https://images.unsplash.com/photo-1696446701796-da61225697cc?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 2, title: "Sony WH-1000XM5 Wireless Noise Canceling Headphones", 
        price: 348.00, oldPrice: 399.00, discount: 12, sold: 320, rating: '4.8', reviews: 120,
        image: "https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 3, title: "Rolex Submariner Date Luxury Watch - Oystersteel", 
        price: 12500.00, discount: 0, sold: 12, rating: '5.0', reviews: 5,
        image: "https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=500&auto=format&fit=crop" 
    }
];

const Wishlist = () => {
    const mainColor = '#57b500';

    return (
        <MasterLayout>
            <div className="container py-5">
                {/* Header Section */}
                <div className="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
                    <div>
                        <h2 className="fw-bold mb-1">My Wishlist</h2>
                        <p className="text-muted mb-0">Manage your favorite products and buy them anytime.</p>
                    </div>
                    <div className="d-flex gap-2">
                        <span className="badge bg-light text-dark border p-2 px-3 rounded-pill fw-bold" style={{ fontSize: '13px' }}>
                            {wishlistItems.length} ITEMS SAVED
                        </span>
                        <button className="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold">Clear All</button>
                    </div>
                </div>

                {/* Wishlist Grid */}
                {wishlistItems.length > 0 ? (
                    <div className="row g-2 g-md-4">
                        {wishlistItems.map(product => (
                            <div key={product.id} className="col-6 col-md-4 col-lg-3 col-xl-2">
                                <ProductCard product={product} />
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="text-center py-5 my-5">
                        <div style={{ fontSize: '80px', animation: 'bounce 2s infinite' }}>🤍</div>
                        <h3 className="fw-bold mt-4">Your Wishlist is Empty</h3>
                        <p className="text-muted mb-4">Seems like you haven't added any favorites yet.</p>
                        <a href="/" className="btn btn-lg text-white px-5 rounded-pill shadow-sm" style={{ backgroundColor: mainColor }}>
                            Browse Products
                        </a>
                    </div>
                )}

                {/* Recommendation Section (Optional but Professional) */}
                <div className="mt-5 pt-5">
                    <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <h4 className="fw-bold mb-0">Recommended For You</h4>
                        <a href="/" className="text-decoration-none" style={{ color: mainColor, fontWeight: 'bold', fontSize: '14px' }}>See More →</a>
                    </div>
                    <div className="row g-2 g-md-4">
                        {/* Placeholder for recommendations */}
                        {[1, 2, 3, 4, 5, 6].map(i => (
                            <div key={i} className="col-6 col-md-4 col-lg-2 opacity-50">
                                <div className="bg-light rounded" style={{ height: '250px' }}></div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            <style>{`
                @keyframes bounce {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-15px); }
                }
            `}</style>
        </MasterLayout>
    );
};

export default Wishlist;
