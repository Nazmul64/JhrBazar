import React, { useEffect, useRef, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useCart } from '../context/CartContext';
import { useSettings } from '../context/SettingsContext';
import { useWishlist } from '../context/WishlistContext';

const ProductCard = ({ product }) => {
    const navigate = useNavigate();
    const { settings } = useSettings();
    const mainColor = settings?.primary_color || '#001fcc';
    const [isVisible, setIsVisible] = useState(false);
    const [added, setAdded] = useState(false);
    const cardRef = useRef(null);
    const { addToCart } = useCart();
    const { toggleWishlist, isInWishlist } = useWishlist();

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
        if (cardRef.current) observer.observe(cardRef.current);
        return () => { if (cardRef.current) observer.unobserve(cardRef.current); };
    }, []);

    const handleAddToCart = () => {
        addToCart(product, 1);
        setAdded(true);
        setTimeout(() => setAdded(false), 1500);

        // Data Layer
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'add_to_cart',
            currency: 'BDT',
            value: Number(product.selling_price || product.price || 0),
            items: [
                {
                    item_id: String(product.id),
                    item_name: product.title || product.name,
                    price: Number(product.selling_price || product.price || 0),
                    quantity: 1
                }
            ]
        });
    };

    const handleBuyNow = (e) => {
        e.preventDefault();
        addToCart(product, 1);

        // Data Layer
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'add_to_cart',
            currency: 'BDT',
            value: Number(product.selling_price || product.price || 0),
            items: [
                {
                    item_id: String(product.id),
                    item_name: product.title || product.name,
                    price: Number(product.selling_price || product.price || 0),
                    quantity: 1
                }
            ]
        });

        navigate('/checkout');
    };

    const handleWishlist = (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleWishlist(product);
    };

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
                transition: 'all 0.6s cubic-bezier(0.2, 1, 0.3, 1)',
                width: settings?.product_card_width || '100%',
                height: settings?.product_card_height && settings.product_card_height !== 'auto' ? settings.product_card_height : '100%',
                display: 'flex',
                flexDirection: 'column'
            }}
        >
            {/* Wishlist Button */}
            <button
                className="wishlist-btn-floating"
                onClick={handleWishlist}
                style={{
                    position: 'absolute', top: '12px', right: '12px',
                    width: '32px', height: '32px', borderRadius: '50%',
                    backgroundColor: '#fff', border: 'none',
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    boxShadow: '0 4px 12px rgba(0,0,0,0.1)',
                    zIndex: 5, cursor: 'pointer', transition: 'all 0.3s'
                }}
                title={isInWishlist(product.id, product.product_type) ? "উইশলিস্ট থেকে সরান" : "উইশলিস্টে যোগ করুন"}
            >
                <span style={{ fontSize: '18px', color: isInWishlist(product.id, product.product_type) ? '#ff4d4d' : '#888' }} className="wish-icon">
                    {isInWishlist(product.id, product.product_type) ? '❤️' : '🤍'}
                </span>
            </button>


            {/* Discount Badge */}
            {product.discount > 0 && settings?.show_product_stats !== false && (
                <div style={{
                    position: 'absolute', top: '12px', left: '12px',
                    backgroundColor: '#ff4d4d', color: '#fff',
                    padding: '3px 10px', borderRadius: '20px',
                    fontSize: '10px', fontWeight: 'bold', zIndex: 2
                }}>{product.discount}% OFF</div>
            )}

            {/* Added to Cart Flash */}
            {added && (
                <div style={{
                    position: 'absolute', top: 0, left: 0, right: 0, bottom: 0,
                    backgroundColor: 'rgba(87,181,0,0.12)', zIndex: 10,
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    borderRadius: '12px', transition: 'all 0.3s'
                }}>
                    <span style={{
                        backgroundColor: mainColor, color: '#fff',
                        padding: '8px 18px', borderRadius: '30px',
                        fontWeight: 'bold', fontSize: '13px',
                        boxShadow: '0 4px 15px rgba(87,181,0,0.4)'
                    }}>✓ কার্টে যোগ হয়েছে!</span>
                </div>
            )}

            {/* Product Image */}
            <Link
                to={`/product-details/${product.product_type}/${product.slug}`}
                className="product-card-img-wrapper"
                style={{
                    display: 'block',
                    height: '200px', // Fallback
                    flexShrink: 0,
                    textAlign: 'center',
                    overflow: 'hidden',
                    backgroundColor: '#f9f9f9'
                }}
            >
                <img
                    src={product.image?.startsWith('http') ? product.image : (product.image?.startsWith('/') ? product.image : (product.image?.startsWith('uploads/') ? `/${product.image}` : `/uploads/product/${product.image}`))}
                    alt={product.title}
                    className="product-img-hover"
                    loading="lazy"
                    style={{ width: '100%', height: '100%', objectFit: 'contain', transition: 'transform 0.5s ease' }}
                    onError={(e) => { e.target.src = '/assets/admin/images/no-image.png'; }}
                />
            </Link>

            <div className="card-body p-3 d-flex flex-column" style={{ flexGrow: 1 }}>
                <Link to={`/product-details/${product.product_type}/${product.slug}`} className="text-decoration-none text-dark">
                    <h6 className="mb-2 text-truncate-1 product-card-title" style={{ fontSize: '14px', fontWeight: '600', lineHeight: '1.4' }}>
                        {product.title}
                    </h6>
                </Link>

                <div className="d-flex align-items-center gap-2 mb-2 flex-grow-1">
                    <span className="fw-bold product-price-current" style={{ color: '#ff4d4d', fontSize: '16px' }}>
                        ৳{Number(product.price).toLocaleString('en-BD')}
                    </span>
                    {product.oldPrice > product.price && (
                        <span className="text-muted text-decoration-line-through product-price-old" style={{ fontSize: '12px' }}>
                            ৳{Number(product.oldPrice).toLocaleString('en-BD')}
                        </span>
                    )}
                </div>

                {settings?.show_product_stats !== false && (
                    <div className="d-flex justify-content-between align-items-center mb-3 border-top pt-2 mt-auto" style={{ fontSize: '11px' }}>
                        <div className="text-warning">
                            ★ <span className="text-dark fw-bold">{product.rating || '0.0'}</span>{' '}
                            <span className="text-muted">({product.reviews || 0})</span>
                        </div>
                        <div className="text-muted fw-bold">{product.sold || 0} Sold</div>
                    </div>
                )}

                <div className={`d-flex gap-2 ${settings?.show_product_stats === false ? 'mt-auto' : ''}`}>
                    <button
                        onClick={handleAddToCart}
                        className="btn btn-sm d-flex align-items-center justify-content-center cart-btn-hover"
                        title="কার্টে যোগ করুন"
                        style={{
                            width: '40px', height: '40px',
                            border: `1.5px solid var(--button-color, ${mainColor})`,
                            color: `var(--button-color, ${mainColor})`, borderRadius: '10px',
                            backgroundColor: '#fff', transition: 'all 0.3s',
                            flexShrink: 0
                        }}
                    >🛒</button>

                    <button
                        onClick={handleBuyNow}
                        className="btn btn-sm flex-grow-1 d-flex align-items-center justify-content-center fw-bold text-white buy-now-btn"
                        style={{
                            backgroundColor: 'var(--button-color, #57b500)',
                            borderRadius: '10px', fontSize: '13px',
                            transition: 'all 0.3s',
                            boxShadow: `0 4px 12px rgba(0, 0, 0, 0.1)`
                        }}
                    >অর্ডার করুন</button>
                </div>
            </div>

            <style>{`
                .product-card-animate.is-visible { opacity: 1 !important; transform: translateY(0) !important; }
                .product-card-animate:hover {
                    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
                    transform: translateY(-8px) !important;
                }
                .product-card-animate:hover .product-img-hover { transform: scale(1.08); }
                .text-truncate-1 {
                    display: -webkit-box;
                    -webkit-line-clamp: 1;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .buy-now-btn {
                    white-space: nowrap;
                }
                .buy-now-btn:hover {
                    background-color: var(--button-hover-color, #4a9a00) !important;
                    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2) !important;
                    transform: translateY(-2px);
                }
                .cart-btn-hover:hover {
                    background-color: var(--button-color, ${mainColor}) !important;
                    color: #fff !important;
                    transform: translateY(-2px);
                }
                .wishlist-btn-floating:hover {
                    transform: scale(1.1);
                    box-shadow: 0 6px 15px rgba(0,0,0,0.2) !important;
                }
                .wishlist-btn-floating:hover .wish-icon {
                    color: #ff4d4d !important;
                }
                
                /* Dynamic Responsive Styles */
                @media (max-width: 768px) {
                    .product-card-title { font-size: ${settings?.product_title_size_mobile || '12px'} !important; }
                    .product-card-img-wrapper { height: ${settings?.product_img_height_mobile || '150px'} !important; }
                    .buy-now-btn { font-size: 11px !important; padding: 8px 4px !important; }
                }
                @media (min-width: 769px) {
                    .product-card-title { font-size: ${settings?.product_title_size_desktop || '14px'} !important; }
                    .product-card-img-wrapper { height: ${settings?.product_img_height_desktop || '200px'} !important; }
                }
                .product-price-current { font-size: ${settings?.product_price_size || '16px'} !important; }
                .product-price-old { font-size: ${settings?.product_old_price_size || '12px'} !important; }
            `}</style>
        </div>
    );
};

export default ProductCard;
