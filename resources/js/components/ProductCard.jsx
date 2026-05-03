import React, { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';

const ProductCard = ({ product }) => {
    const mainColor = '#57b500';
    const [isVisible, setIsVisible] = useState(false);
    const cardRef = useRef(null);

    useEffect(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setIsVisible(true);
                    observer.unobserve(entry.target);
                }
            },
            { threshold: 0.1 }
        );

        if (cardRef.current) {
            observer.observe(cardRef.current);
        }

        return () => {
            if (cardRef.current) observer.unobserve(cardRef.current);
        };
    }, []);

    return (
        <div 
            ref={cardRef}
            className={`card h-100 border shadow-sm product-card-animate ${isVisible ? 'is-visible' : ''}`} 
            style={{ 
                borderRadius: '12px', 
                overflow: 'hidden', 
                backgroundColor: '#fff', 
                position: 'relative',
                opacity: 0,
                transform: 'translateY(30px)',
                transition: 'all 0.6s cubic-bezier(0.2, 1, 0.3, 1)'
            }}
        >
            {/* Discount Badge */}
            {product.discount > 0 && (
                <div style={{ 
                    position: 'absolute', 
                    top: '12px', 
                    left: '12px', 
                    backgroundColor: '#ff4d4d', 
                    color: '#fff', 
                    padding: '3px 10px', 
                    borderRadius: '20px', 
                    fontSize: '10px', 
                    fontWeight: 'bold',
                    zIndex: 2
                }}>
                    {product.discount}% OFF
                </div>
            )}

            {/* Product Image Area - Edge-to-Edge Full Width */}
            <Link to="/product-details" style={{ display: 'block', height: '220px', textAlign: 'center', overflow: 'hidden' }}>
                <img 
                    src={product.image} 
                    alt={product.title} 
                    className="product-img-hover"
                    style={{ width: '100%', height: '100%', objectFit: 'cover', transition: 'transform 0.5s ease' }} 
                />
            </Link>

            <div className="card-body p-3">
                <Link to="/product-details" className="text-decoration-none text-dark">
                    <h6 className="mb-2 text-truncate-2" style={{ fontSize: '14px', fontWeight: '600', height: '40px', lineHeight: '1.4' }}>
                        {product.title}
                    </h6>
                </Link>

                <div className="d-flex align-items-center gap-2 mb-2">
                    <span className="fw-bold" style={{ color: '#ff4d4d', fontSize: '16px' }}>${product.price.toFixed(2)}</span>
                    {product.oldPrice && (
                        <span className="text-muted text-decoration-line-through" style={{ fontSize: '12px' }}>${product.oldPrice.toFixed(2)}</span>
                    )}
                </div>

                <div className="d-flex justify-content-between align-items-center mb-3 border-top pt-2" style={{ fontSize: '11px' }}>
                    <div className="text-warning">
                        ★ <span className="text-dark fw-bold">{product.rating || '0.0'}</span> <span className="text-muted">({product.reviews || 0})</span>
                    </div>
                    <div className="text-muted fw-bold">{product.sold || 0} Sold</div>
                </div>

                <div className="d-flex gap-2">
                    <button 
                        className="btn btn-sm d-flex align-items-center justify-content-center cart-btn-hover" 
                        style={{ 
                            width: '40px', height: '40px', 
                            border: `1.5px solid ${mainColor}`, 
                            color: mainColor, 
                            borderRadius: '10px',
                            backgroundColor: '#fff',
                            transition: 'all 0.3s'
                        }}
                    >
                        🛒
                    </button>
                    <Link 
                        to="/product-details" 
                        className="btn btn-sm flex-grow-1 d-flex align-items-center justify-content-center fw-bold text-white buy-now-btn" 
                        style={{ 
                            backgroundColor: mainColor, 
                            borderRadius: '10px',
                            fontSize: '13px',
                            transition: 'all 0.3s',
                            boxShadow: `0 4px 12px rgba(87, 181, 0, 0.2)`
                        }}
                    >
                        Buy Now
                    </Link>
                </div>
            </div>

            <style>{`
                .product-card-animate.is-visible {
                    opacity: 1 !important;
                    transform: translateY(0) !important;
                }
                .product-card-animate:hover {
                    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
                    transform: translateY(-8px) !important;
                }
                .product-card-animate:hover .product-img-hover {
                    transform: scale(1.1);
                }
                .text-truncate-2 {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                .buy-now-btn:hover {
                    background-color: #4a9a00 !important;
                    box-shadow: 0 6px 20px rgba(87, 181, 0, 0.4) !important;
                    transform: translateY(-2px);
                }
                .cart-btn-hover:hover {
                    background-color: ${mainColor} !important;
                    color: #fff !important;
                    transform: translateY(-2px);
                }
            `}</style>
        </div>
    );
};

export default ProductCard;
