import React from 'react';
import MasterLayout from '../layouts/MasterLayout';
import HeroSection from '../components/HeroSection';
import FeatureBar from '../components/FeatureBar';
import Categories from '../components/Categories';
import ProductCard from '../components/ProductCard';
import TopRatedShops from '../components/TopRatedShops';

const popularProducts = [
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
        id: 3, title: "MacBook Pro M3 Max - 14-inch Space Black", 
        price: 3199.00, discount: 0, sold: 45, rating: '5.0', reviews: 28,
        image: "https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 4, title: "Nike Air Jordan 1 Retro High OG 'Chicago'", 
        price: 180.00, oldPrice: 220.00, discount: 18, sold: 890, rating: '4.9', reviews: 310,
        image: "https://images.unsplash.com/photo-1552346154-21d32810aba3?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 5, title: "Rolex Submariner Date Luxury Watch - Oystersteel", 
        price: 12500.00, discount: 0, sold: 12, rating: '5.0', reviews: 5,
        image: "https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 6, title: "DJI Mavic 3 Pro Drone - 4K Camera System", 
        price: 2199.00, oldPrice: 2499.00, discount: 12, sold: 65, rating: '4.7', reviews: 15,
        image: "https://images.unsplash.com/photo-1507582020474-9a35b7d455d9?q=80&w=500&auto=format&fit=crop" 
    }
];

const justForYouProducts = [
    { 
        id: 101, title: "Samsung Galaxy S24 Ultra - 512GB Titanium Grey", 
        price: 1299.00, oldPrice: 1399.00, discount: 7, sold: 210, rating: '4.8', reviews: 55,
        image: "https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 102, title: "Canon EOS R5 Mirrorless Camera Body", 
        price: 3399.00, discount: 0, sold: 34, rating: '4.9', reviews: 12,
        image: "https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 103, title: "Dyson V15 Detect Cordless Vacuum Cleaner", 
        price: 749.00, oldPrice: 799.00, discount: 6, sold: 156, rating: '4.7', reviews: 89,
        image: "https://images.unsplash.com/photo-1558317374-067fb5f30001?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 104, title: "Lego Star Wars Millennium Falcon Set", 
        price: 849.00, discount: 0, sold: 88, rating: '5.0', reviews: 42,
        image: "https://images.unsplash.com/photo-1585366119957-e556f4325404?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 105, title: "Nespresso Vertuo Next Coffee Machine", 
        price: 179.00, oldPrice: 209.00, discount: 14, sold: 450, rating: '4.6', reviews: 120,
        image: "https://images.unsplash.com/photo-1517668808822-9ebb02f2a0e6?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 106, title: "Bose QuietComfort Ultra Earbuds", 
        price: 299.00, discount: 0, sold: 130, rating: '4.8', reviews: 67,
        image: "https://images.unsplash.com/photo-1590658268037-6bf12165a8df?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 107, title: "Logitech MX Master 3S Wireless Mouse", 
        price: 99.00, oldPrice: 109.00, discount: 9, sold: 890, rating: '4.9', reviews: 540,
        image: "https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 108, title: "ASUS ROG Swift 32-inch 4K Gaming Monitor", 
        price: 899.00, discount: 0, sold: 22, rating: '4.7', reviews: 8,
        image: "https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?q=80&w=500&auto=format&fit=crop" 
    }
];

const Home = () => {
    const mainColor = '#57b500';

    return (
        <MasterLayout>
            <HeroSection />
            <FeatureBar />
            <Categories />

            {/* Popular Products */}
            <section className="container mb-5">
                <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h4 className="fw-bold mb-0" style={{ color: '#333' }}>Popular Products</h4>
                    <button className="btn btn-link text-muted text-decoration-none small" style={{ fontSize: '13px' }}>View All →</button>
                </div>
                <div className="row g-2 g-md-3">
                    {popularProducts.map(product => (
                        <div key={product.id} className="col-6 col-md-4 col-lg-2">
                            <ProductCard product={product} />
                        </div>
                    ))}
                </div>
            </section>

            {/* Top Rated Shops */}
            <TopRatedShops />

            {/* Just For You */}
            <section className="container mb-5">
                <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h4 className="fw-bold mb-0" style={{ color: '#333' }}>Just For You</h4>
                    <button className="btn btn-link text-muted text-decoration-none small" style={{ fontSize: '13px' }}>View All →</button>
                </div>
                
                <div className="row g-2 g-md-3">
                    {justForYouProducts.map(product => (
                        <div key={product.id} className="col-6 col-md-4 col-lg-3">
                            <ProductCard product={product} />
                        </div>
                    ))}
                </div>

                {/* Load More Button */}
                <div className="text-center mt-5">
                    <button style={{ 
                        padding: '12px 50px', 
                        backgroundColor: '#fff', 
                        color: mainColor, 
                        border: `1.5px solid ${mainColor}`, 
                        borderRadius: '30px',
                        fontWeight: 'bold',
                        transition: 'all 0.3s',
                        fontSize: '14px',
                        boxShadow: '0 4px 15px rgba(87, 181, 0, 0.1)'
                    }} className="load-more-btn">
                        Load More Products
                    </button>
                </div>
            </section>

            <style>{`
                .load-more-btn:hover {
                    background-color: ${mainColor} !important;
                    color: #fff !important;
                    transform: translateY(-3px);
                    box-shadow: 0 6px 20px rgba(87, 181, 0, 0.3) !important;
                }
            `}</style>
        </MasterLayout>
    );
};

export default Home;
