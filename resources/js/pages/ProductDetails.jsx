import React, { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';
import axios from 'axios';
import { useCart } from '../context/CartContext';
import { useWishlist } from '../context/WishlistContext';
import SEO from '../components/SEO';
import { useSettings } from '../context/SettingsContext';

const ProductDetails = () => {
    const { slug } = useParams();
    const { settings } = useSettings();
    const mainColor = settings?.primary_color || '#57b500';
    const [product, setProduct] = useState(null);
    const [loading, setLoading] = useState(true);
    const [quantity, setQuantity] = useState(1);
    const [zoomPos, setZoomPos] = useState({ x: 0, y: 0, show: false });
    const [activeImageIndex, setActiveImageIndex] = useState(0);
    const [activeTab, setActiveTab] = useState('about');
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    const [toast, setToast] = useState({ show: false, message: '' });
    const [reviews, setReviews] = useState([]);
    const [relatedProducts, setRelatedProducts] = useState([]);
    const { addToCart } = useCart();
    const { toggleWishlist, isInWishlist } = useWishlist();
    const [supportSettings, setSupportSettings] = useState(null);
    const navigate = useNavigate();

    const decodeHTML = (html) => {
        const txt = document.createElement('textarea');
        txt.innerHTML = html;
        return txt.value;
    };

    useEffect(() => {
        const fetchProduct = async () => {
            setLoading(true);
            try {
                const res = await axios.get(`/api/product/${slug}`);
                if (res.data.success) {
                    const pData = res.data.data;
                    setProduct(pData);
                    setReviews(pData.reviews || []);
                    setRelatedProducts(pData.related || []);
                }
            } catch (error) {
                console.error("Error fetching product details:", error);
            } finally {
                setLoading(false);
            }
        };
        fetchProduct();

        const fetchSupportSettings = async () => {
            try {
                const res = await axios.get('/api/admin-support');
                if (res.data.success) {
                    setSupportSettings(res.data.data);
                }
            } catch (error) {
                console.error("Error fetching support settings:", error);
            }
        };
        fetchSupportSettings();

        window.scrollTo(0, 0);
    }, [slug]);

    // Data Layer: view_item
    useEffect(() => {
        if (product) {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'view_item',
                currency: 'BDT',
                value: Number(product.selling_price || product.price || 0),
                items: [
                    {
                        item_id: String(product.id),
                        item_name: product.name,
                        item_brand: product.brand?.name || '',
                        item_category: product.category?.name || '',
                        price: Number(product.selling_price || product.price || 0),
                        quantity: 1
                    }
                ]
            });
        }
    }, [product]);

    const getImageUrl = (url) => {
        if (!url) return '/assets/admin/images/no-image.png';
        if (url.startsWith('http')) return url;
        if (url.startsWith('/')) return url;
        if (url.startsWith('uploads/')) return '/' + url;
        return '/uploads/product/' + url;
    };

    const productImages = product ? [product.thumbnail, ...(product.gallery || [])] : [];

    // Auto Slider Logic
    useEffect(() => {
        if (!product || productImages.length <= 1) return;

        const interval = setInterval(() => {
            setActiveImageIndex((prev) => (prev + 1) % productImages.length);
        }, 3000); // Change image every 3 seconds

        return () => clearInterval(interval);
    }, [product, productImages.length]);

    const showToast = (message) => {
        setToast({ show: true, message });
        setTimeout(() => setToast({ show: false, message: '' }), 3000);
    };

    const handleBuyNow = () => {
        addToCart(product, quantity, product.color, product.size);
        
        // Data Layer
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'add_to_cart',
            currency: 'BDT',
            value: Number(product.selling_price || product.price || 0) * Number(quantity),
            items: [
                {
                    item_id: String(product.id),
                    item_name: product.name,
                    price: Number(product.selling_price || product.price || 0),
                    quantity: Number(quantity)
                }
            ]
        });

        navigate('/checkout');
    };

    const handleAddToCart = () => {
        addToCart(product, quantity, product.color, product.size);
        showToast('কার্টে যোগ করা হয়েছে!');

        // Data Layer
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'add_to_cart',
            currency: 'BDT',
            value: Number(product.selling_price || product.price || 0) * Number(quantity),
            items: [
                {
                    item_id: String(product.id),
                    item_name: product.name,
                    price: Number(product.selling_price || product.price || 0),
                    quantity: Number(quantity)
                }
            ]
        });
    };

    const handleMouseMove = (e) => {
        const rect = e.currentTarget.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;
        const boundedX = Math.max(0, Math.min(100, x));
        const boundedY = Math.max(0, Math.min(100, y));
        setZoomPos({ x: boundedX, y: boundedY, show: true });
    };

    const getYouTubeId = (url) => {
        if (!url) return null;
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    };

    if (loading) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center" style={{ minHeight: '60vh' }}>
                </div>
            </MasterLayout>
        );
    }

    if (!product) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center">
                    <h3>পণ্যটি পাওয়া যায়নি</h3>
                    <Link to="/" className="btn btn-success mt-3">হোমে ফিরে যান</Link>
                </div>
            </MasterLayout>
        );
    }


    return (
        <MasterLayout>
            <SEO 
                title={product.meta_title || product.name}
                description={product.meta_description || product.short_description}
                keywords={product.meta_keywords}
                image={getImageUrl(product.thumbnail)}
                url={window.location.href}
                type="product"
                schema={{
                    "@context": "https://schema.org/",
                    "@type": "Product",
                    "name": product.name,
                    "image": [getImageUrl(product.thumbnail)],
                    "description": product.short_description,
                    "sku": product.sku,
                    "brand": {
                        "@type": "Brand",
                        "name": product.brand || settings?.website_name || ""
                    },
                    "offers": {
                        "@type": "Offer",
                        "url": window.location.href,
                        "priceCurrency": "BDT",
                        "price": product.price,
                        "availability": product.stock > 0 ? "https://schema.org/InStock" : "https://schema.org/OutOfStock"
                    }
                }}
            />
            {/* Custom Toast Notification */}
            {toast.show && (
                <div style={{
                    position: 'fixed',
                    bottom: '30px',
                    right: '30px',
                    backgroundColor: '#333',
                    color: '#fff',
                    padding: '12px 24px',
                    borderRadius: '8px',
                    boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
                    zIndex: 9999,
                    display: 'flex',
                    alignItems: 'center',
                    gap: '10px',
                    animation: 'fadeIn 0.3s ease-in-out'
                }}>
                    <span style={{ color: mainColor }}>✓</span>
                    <span style={{ fontSize: '14px', fontWeight: '500' }}>{toast.message}</span>
                </div>
            )}
            <style>{`
                .description-content ul, .description-content ol { padding-left: 20px; margin-bottom: 20px; }
                .description-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 15px 0; }
                .description-content table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .description-content table td, .description-content table th { border: 1px solid #eee; padding: 10px; }
                .description-content p { margin-bottom: 15px; }
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @keyframes imgFade {
                    from { opacity: 0; transform: scale(0.98); }
                    to { opacity: 1; transform: scale(1); }
                }
                .product-main-img {
                    animation: imgFade 0.5s ease-out forwards;
                }
                .thumbnail-item {
                    transition: all 0.3s ease;
                }
                .thumbnail-item:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                }
                .hover-wishlist:hover { color: #ff4d4d !important; transform: scale(1.05); }
                .hover-share:hover { color: ${mainColor} !important; transform: scale(1.05); }
                
                /* Mobile Responsive Adjustments */
                @media (max-width: 768px) {
                    .product-title-responsive { font-size: 18px !important; margin-bottom: 8px !important; }
                    .product-price-responsive { font-size: 22px !important; }
                    .product-btn-responsive { 
                        font-size: 13px !important; 
                        padding: 8px 5px !important; 
                        border-radius: 8px !important;
                    }
                    .support-btn-responsive {
                        font-size: 11px !important;
                        padding: 8px 4px !important;
                        white-space: nowrap !important;
                        gap: 4px !important;
                    }
                    .thumbnail-item-mobile {
                        width: 55px !important;
                        height: 55px !important;
                        min-width: 55px !important;
                    }
                }
            `}</style>

            <div className={`${product.seller_id ? 'container-fluid px-4 px-md-5' : 'container'} py-4`}>
                {/* Breadcrumbs */}
                <nav className="mb-4">
                    <ol className="breadcrumb small" style={{ fontSize: '14px' }}>
                        <li className="breadcrumb-item"><Link to="/" className="text-decoration-none text-muted">হোম</Link></li>
                        <li className="breadcrumb-item active text-dark fw-bold">{product.name}</li>
                    </ol>
                </nav>

                <div className="row g-4">
                    {/* Left: Gallery & Main Info */}
                    <div className={product.seller_id ? "col-lg-8" : "col-lg-12"}>
                        <div className="row g-4">
                            {/* Product Images with Vertical Thumbnails */}
                            <div className="col-md-6">
                                <div className="d-flex gap-3">
                                    {/* Thumbnails (Vertical on the left) */}
                                    <div
                                        className="d-flex flex-column gap-2 d-none d-md-flex"
                                        style={{ width: '85px', flexShrink: 0, maxHeight: '480px', overflowY: 'auto', scrollbarWidth: 'none' }}
                                    >
                                        {productImages.map((img, i) => (
                                            <div
                                                key={i}
                                                onClick={() => setActiveImageIndex(i)}
                                                className="thumbnail-item"
                                                style={{
                                                    width: '75px',
                                                    height: '75px',
                                                    minWidth: '75px',
                                                    minHeight: '75px',
                                                    flexShrink: 0,
                                                    border: i === activeImageIndex ? `2px solid ${mainColor}` : '1px solid #eee',
                                                    borderRadius: '10px',
                                                    overflow: 'hidden',
                                                    cursor: 'pointer',
                                                    padding: '3px',
                                                    backgroundColor: '#fff',
                                                    transition: 'all 0.2s',
                                                    opacity: i === activeImageIndex ? 1 : 0.7
                                                }}
                                            >
                                                <img src={getImageUrl(img)} style={{ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '7px' }} alt="thumb" onError={(e) => { e.target.src = '/assets/admin/images/no-image.png'; }} />
                                            </div>
                                        ))}
                                    </div>

                                    {/* Main Image Container */}
                                    <div className="flex-grow-1">
                                        <div
                                            className="position-relative shadow-sm"
                                            onMouseMove={handleMouseMove}
                                            onMouseLeave={() => setZoomPos({ ...zoomPos, show: false })}
                                            style={{
                                                border: '1px solid #f0f0f0',
                                                borderRadius: '15px',
                                                overflow: 'hidden',
                                                backgroundColor: '#fff',
                                                cursor: 'crosshair',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center',
                                                padding: '0',
                                                maxHeight: '480px'
                                            }}
                                        >
                                            <img
                                                key={activeImageIndex}
                                                src={getImageUrl(productImages[activeImageIndex])}
                                                alt={product.name}
                                                className="product-main-img"
                                                style={{ width: '100%', height: '100%', maxHeight: '480px', objectFit: 'contain', display: 'block' }}
                                                onError={(e) => { e.target.src = '/assets/admin/images/no-image.png'; }}
                                            />
                                            {/* Floating Wishlist Button */}
                                            <div
                                                className="position-absolute cursor-pointer d-flex align-items-center justify-content-center shadow-sm hover-wishlist"
                                                onClick={() => toggleWishlist(product)}
                                                title={isInWishlist(product.id, product.product_type) ? "উইশলিস্ট থেকে সরান" : "উইশলিস্টে যোগ করুন"}
                                                style={{
                                                    top: '15px',
                                                    right: '15px',
                                                    width: '40px',
                                                    height: '40px',
                                                    borderRadius: '50%',
                                                    backgroundColor: '#fff',
                                                    zIndex: 10,
                                                    transition: 'all 0.3s',
                                                    color: isInWishlist(product.id, product.product_type) ? '#ff4d4d' : '#666',
                                                    fontSize: '22px'
                                                }}
                                            >
                                                {isInWishlist(product.id, product.product_type) ? '❤️' : '🤍'}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Horizontal Thumbnails for Mobile Only */}
                                <div className="d-flex d-md-none gap-2 mt-3 overflow-auto pb-2" style={{ scrollbarWidth: 'none' }}>
                                    {productImages.map((img, i) => (
                                        <div
                                            key={i}
                                            onClick={() => setActiveImageIndex(i)}
                                            className="thumbnail-item thumbnail-item-mobile"
                                            style={{
                                                width: '75px',
                                                height: '75px',
                                                minWidth: '75px',
                                                minHeight: '75px',
                                                flexShrink: 0,
                                                border: i === activeImageIndex ? `2px solid ${mainColor}` : '1px solid #eee',
                                                borderRadius: '10px',
                                                overflow: 'hidden',
                                                cursor: 'pointer',
                                                padding: '3px',
                                                backgroundColor: '#fff',
                                                opacity: i === activeImageIndex ? 1 : 0.6
                                            }}
                                        >
                                            <img src={getImageUrl(img)} style={{ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '7px' }} alt="thumb" />
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Product Purchase Info */}
                            <div className="col-md-6 ps-md-5 position-relative">
                                {/* Zoom Overlay */}
                                {zoomPos.show && (
                                    <div className="d-none d-md-block" style={{
                                        position: 'absolute',
                                        top: 0,
                                        left: '15px',
                                        right: 0,
                                        bottom: 0,
                                        width: 'calc(100% - 15px)',
                                        height: '100%',
                                        backgroundImage: `url(${productImages[activeImageIndex]})`,
                                        backgroundPosition: `${zoomPos.x}% ${zoomPos.y}%`,
                                        backgroundSize: '200%',
                                        backgroundRepeat: 'no-repeat',
                                        backgroundColor: '#fff',
                                        pointerEvents: 'none',
                                        zIndex: 100,
                                        borderRadius: '15px',
                                        boxShadow: '0 10px 30px rgba(0,0,0,0.08)',
                                        transition: 'background-position 0.05s ease-out'
                                    }}></div>
                                )}

                                <span className="badge mb-2 px-3 py-2" style={{ backgroundColor: '#fff0f3', color: '#ff4d4d', fontSize: '12px', fontWeight: 'bold', borderRadius: '5px' }}>{product.category || 'Product'}</span>
                                <h1 className="fw-bold mb-3 product-title-responsive" style={{ color: '#333', fontSize: '32px' }}>{product.name}</h1>
                                <div 
                                    className="text-muted mb-4" 
                                    style={{ fontSize: '15px', lineHeight: '1.6' }}
                                    dangerouslySetInnerHTML={{ __html: decodeHTML(product.short_description || "") }}
                                ></div>

                                <div className="d-flex align-items-center gap-3 mb-4">
                                    <div className="text-warning small">
                                        {'★'.repeat(Math.round(product.avg_rating || 0))}{'☆'.repeat(5 - Math.round(product.avg_rating || 0))}
                                        <span className="text-muted ms-1">({product.review_count || 0} রিভিউ)</span>
                                    </div>
                                    <div className="text-muted small">| <span className="text-dark fw-bold">০</span> টি বিক্রিত</div>
                                    {product.seller_id && (
                                        <div className="ms-auto d-flex gap-3 align-items-center">
                                            <button
                                                onClick={() => {
                                                    window.dispatchEvent(new CustomEvent('openSellerChat', {
                                                        detail: { sellerId: product.seller_id, sellerName: product.seller_name }
                                                    }));
                                                }}
                                                className="btn btn-outline-secondary btn-sm rounded-circle shadow-sm"
                                                title="Chat with Seller"
                                                style={{ width: '35px', height: '35px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}
                                            >
                                                💬
                                            </button>
                                        </div>
                                    )}
                                </div>

                                <div className="d-flex align-items-center gap-3 mb-4">
                                    <h2 className="fw-bold mb-0 product-price-responsive" style={{ color: mainColor, fontSize: '36px' }}>৳{Number(product.price).toLocaleString('en-BD')}</h2>
                                    {product.old_price > product.price && (
                                        <span className="text-muted text-decoration-line-through">
                                            ৳{Number(product.old_price).toLocaleString('en-BD')}
                                        </span>
                                    )}
                                </div>

                                {/* Product Variations */}
                                <div className="mb-4">
                                    <div className="d-flex flex-wrap align-items-center gap-4 mb-4">
                                        <div className="fs-6"><span className="text-muted">ব্র্যান্ড:</span> <span className="fw-bold text-dark">{product.brand || 'N/A'}</span></div>
                                        <div className="fs-6"><span className="text-muted">SKU:</span> <span className="fw-bold text-dark">{product.sku || 'N/A'}</span></div>
                                        <div className="fs-6">
                                            <span className="text-muted">স্টক:</span>{' '}
                                            <span className="fw-bold" style={{ color: (product.stock > 0 || product.stock >= 999999) ? '#28a745' : '#dc3545' }}>
                                                {(product.stock >= 999999 || product.stock_quantity >= 999999) ? 'Unlimited' : (product.stock > 0 ? `${product.stock} Available` : 'Out of Stock')}
                                            </span>
                                        </div>
                                    </div>

                                    {product.color && (
                                        <div className="mb-3">
                                            <label className="small fw-bold mb-2">কালার: <span className="text-muted fw-normal">{product.color}</span></label>
                                            <div className="d-flex gap-2">
                                                <div style={{
                                                    width: '30px', height: '30px', borderRadius: '50%',
                                                    backgroundColor: product.color.toLowerCase(),
                                                    border: `2px solid ${mainColor}`
                                                }}></div>
                                            </div>
                                        </div>
                                    )}

                                    {product.size && (
                                        <div className="mb-3">
                                            <label className="small fw-bold mb-2">সাইজ: <span className="text-muted fw-normal">{product.size}</span></label>
                                            <div className="d-flex gap-2">
                                                <div className="d-flex align-items-center justify-content-center"
                                                    style={{
                                                        minWidth: '40px', height: '35px', borderRadius: '5px',
                                                        border: `1px solid ${mainColor}`,
                                                        color: mainColor,
                                                        fontWeight: 'bold',
                                                        backgroundColor: '#f0fdf4',
                                                        fontSize: '13px'
                                                    }}
                                                >
                                                    {product.size}
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* Action Area: Quantity & Buttons */}
                                    <div className="d-flex flex-column flex-md-row align-items-md-end gap-3 mb-4">
                                        {/* Quantity Selection */}
                                        <div>
                                            <label className="small fw-bold mb-2">পরিমাণ:</label>
                                            <div className="d-flex align-items-center" style={{ width: '120px', border: '1px solid #ddd', borderRadius: '8px', overflow: 'hidden' }}>
                                                <button onClick={() => setQuantity(Math.max(1, quantity - 1))} className="btn btn-light border-0 px-3 py-2" style={{ borderRadius: 0, backgroundColor: '#f8f9fa', fontSize: '18px', fontWeight: 'bold' }}>-</button>
                                                <input type="text" value={quantity} readOnly className="form-control border-0 text-center px-0 bg-white fw-bold" style={{ width: '40px', fontSize: '16px', borderRadius: 0, boxShadow: 'none' }} />
                                                <button onClick={() => setQuantity(quantity + 1)} className="btn btn-light border-0 px-3 py-2" style={{ borderRadius: 0, backgroundColor: '#f8f9fa', fontSize: '18px', fontWeight: 'bold' }}>+</button>
                                            </div>
                                        </div>

                                        {/* Buttons */}
                                        <div className="d-flex flex-grow-1 gap-3">
                                            <button
                                                onClick={handleAddToCart}
                                                className="btn flex-grow-1 d-flex align-items-center justify-content-center gap-2 product-btn-responsive"
                                                style={{
                                                    border: `1.5px solid ${mainColor}`,
                                                    color: mainColor,
                                                    backgroundColor: '#fff',
                                                    borderRadius: '8px',
                                                    fontSize: '16px',
                                                    fontWeight: 'bold',
                                                    padding: '12px'
                                                }}
                                            >
                                                🛒 কার্টে যোগ করুন
                                            </button>
                                            <button
                                                onClick={handleBuyNow}
                                                className="btn flex-grow-1 text-white fw-bold product-btn-responsive"
                                                style={{
                                                    backgroundColor: mainColor,
                                                    borderRadius: '8px',
                                                    fontSize: '16px',
                                                    padding: '12px'
                                                }}
                                            >
                                                অর্ডার করুন
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {/* Support Buttons */}
                                <div className="mt-2 d-flex flex-row gap-2">
                                    <a
                                        href={`tel:${supportSettings?.phone_number || "+8801700000000"}`}
                                        className="btn flex-grow-1 d-flex align-items-center justify-content-center gap-2 text-white support-btn-responsive shadow-sm"
                                        style={{ backgroundColor: '#111', borderRadius: '8px', padding: '12px', fontSize: '15px', fontWeight: 'bold', textDecoration: 'none', transition: 'all 0.3s' }}
                                    >
                                        <i className="fas fa-phone-alt"></i> কল করুন: {supportSettings?.phone_number || "+8801700000000"}
                                    </a>
                                    <a
                                        href={`https://wa.me/${supportSettings?.whatsapp_number || "8801700000000"}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="btn flex-grow-1 d-flex align-items-center justify-content-center gap-2 text-white support-btn-responsive shadow-sm"
                                        style={{ backgroundColor: '#25D366', borderRadius: '8px', padding: '12px', fontSize: '15px', fontWeight: 'bold', textDecoration: 'none', transition: 'all 0.3s' }}
                                    >
                                        <i className="fab fa-whatsapp"></i> WhatsApp অর্ডার
                                    </a>
                                </div>
                            </div>
                        </div>

                        {/* Tabs */}
                        <div className="mt-5">
                            <ul className="nav nav-tabs border-0 gap-4 mb-3">
                                <li className="nav-item">
                                    <button
                                        onClick={() => setActiveTab('about')}
                                        className={`nav-link border-0 p-0 pb-2 ${activeTab === 'about' ? 'active border-bottom border-3 fw-bold' : 'text-muted'}`}
                                        style={{ borderColor: activeTab === 'about' ? `${mainColor} !important` : 'transparent', color: activeTab === 'about' ? '#333' : '', fontSize: '16px' }}
                                    >
                                        প্রোডাক্ট বিবরণ
                                    </button>
                                </li>
                                {product.video && (
                                    <li className="nav-item">
                                        <button
                                            onClick={() => setActiveTab('video')}
                                            className={`nav-link border-0 p-0 pb-2 ${activeTab === 'video' ? 'active border-bottom border-3 fw-bold' : 'text-muted'}`}
                                            style={{ borderColor: activeTab === 'video' ? `${mainColor} !important` : 'transparent', color: activeTab === 'video' ? '#333' : '', fontSize: '16px' }}
                                        >
                                            প্রোডাক্ট ভিডিও
                                        </button>
                                    </li>
                                )}
                                <li className="nav-item">
                                    <button
                                        onClick={() => setActiveTab('reviews')}
                                        className={`nav-link border-0 p-0 pb-2 ${activeTab === 'reviews' ? 'active border-bottom border-3 fw-bold' : 'text-muted'}`}
                                        style={{ borderColor: activeTab === 'reviews' ? `${mainColor} !important` : 'transparent', color: activeTab === 'reviews' ? '#333' : '', fontSize: '16px' }}
                                    >
                                        রিভিউ ({product.review_count || 0})
                                    </button>
                                </li>
                            </ul>
                            <div className="bg-white p-4 p-md-5 rounded shadow-sm border description-content" style={{ fontSize: '16px', lineHeight: '1.8', color: '#444' }}>
                                {activeTab === 'about' && (
                                    <div className="description-content-inner" dangerouslySetInnerHTML={{ __html: decodeHTML(product.description || "") }}></div>
                                )}

                                {activeTab === 'video' && product.video && (
                                    <div className="video-container-wrapper" style={{ position: 'relative', zIndex: 10 }}>
                                        {product.video_type === 'youtube' ? (
                                            <div className="ratio ratio-16x9 shadow-sm" style={{ backgroundColor: '#000', borderRadius: '15px', overflow: 'hidden' }}>
                                                <iframe
                                                    src={`https://www.youtube.com/embed/${getYouTubeId(product.video)}`}
                                                    title="Product Video"
                                                    frameBorder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                    allowFullScreen
                                                    style={{ width: '100%', height: '100%', position: 'absolute', top: 0, left: 0 }}
                                                ></iframe>
                                            </div>
                                        ) : product.video_type === 'file' ? (
                                            <video controls style={{ width: '100%', borderRadius: '15px', maxHeight: '500px', display: 'block', margin: '0 auto' }}>
                                                <source src={getImageUrl(product.video)} type="video/mp4" />
                                                Your browser does not support the video tag.
                                            </video>
                                        ) : (
                                            <div className="ratio ratio-16x9 shadow-sm" style={{ backgroundColor: '#000', borderRadius: '15px', overflow: 'hidden' }}>
                                                <iframe
                                                    src={product.video}
                                                    title="Product Video"
                                                    frameBorder="0"
                                                    allowFullScreen
                                                    style={{ width: '100%', height: '100%', position: 'absolute', top: 0, left: 0 }}
                                                ></iframe>
                                            </div>
                                        )}
                                    </div>
                                )}

                                {activeTab === 'reviews' && (
                                    <div>
                                        <div className="mb-5">
                                            {reviews.length > 0 ? (
                                                <div className="d-flex flex-column gap-4">
                                                    {reviews.map(review => (
                                                        <div key={review.id} className="pb-3 border-bottom">
                                                            <div className="d-flex justify-content-between mb-2">
                                                                <h6 className="fw-bold text-dark mb-0">{review.user?.name}</h6>
                                                                <span className="text-muted small">{new Date(review.created_at).toLocaleDateString()}</span>
                                                            </div>
                                                            <div className="text-warning small mb-2">
                                                                {'★'.repeat(review.rating)}{'☆'.repeat(5 - review.rating)}
                                                            </div>
                                                            <p className="mb-0 text-dark">{review.comment}</p>
                                                        </div>
                                                    ))}
                                                </div>
                                            ) : (
                                                <div className="text-center py-4 text-muted">এই প্রোডাক্টে এখনো কোনো রিভিউ নেই।</div>
                                            )}
                                        </div>

                                        <div className="alert alert-info py-2" style={{ borderRadius: '10px' }}>
                                            রিভিউ দিতে আপনার <Link to="/customer/dashboard" className="fw-bold">ড্যাশবোর্ড</Link> থেকে কেনা প্রোডাক্টের ওপর রিভিউ অপশনটি ব্যবহার করুন।
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Right: Sidebar */}
                    {product.seller_id && (
                        <div className="col-lg-4">
                            {/* Sold By Section (Conditional) */}
                            {product.seller_id && (
                                <div className="bg-white p-4 rounded shadow-sm border mb-4">
                                    <div className="d-flex align-items-center justify-content-between mb-4">
                                        <div className="d-flex align-items-center gap-3">
                                            <img
                                                src={product.seller_logo || '/assets/admin/images/default-avatar.png'}
                                                alt={product.seller_name}
                                                style={{ width: '45px', height: '45px', borderRadius: '50%', objectFit: 'cover', border: '1px solid #eee' }}
                                            />
                                            <div>
                                                <div className="text-muted" style={{ fontSize: '11px' }}>Sold by</div>
                                                <div className="fw-bold text-dark" style={{ fontSize: '14px' }}>{product.seller_name}</div>
                                            </div>
                                        </div>
                                        <div className="text-warning small d-flex align-items-center gap-1">
                                            ★ <span className="text-dark fw-bold">{product.seller_rating.toFixed(1)}</span>
                                        </div>
                                    </div>
                                    <div className="text-center border-top pt-3">
                                        <Link to={`/shop/${product.seller_id}`} className="text-danger text-decoration-none small fw-bold">Visit Store</Link>
                                    </div>
                                </div>
                            )}

                            {/* Popular Products From Them (Conditional) */}
                            {product.seller_id && relatedProducts.length > 0 && (
                                <div className="mb-4">
                                    <h6 className="fw-bold mb-3" style={{ fontSize: '14px' }}>Popular Products From Them</h6>
                                    <div className="d-flex flex-column gap-3">
                                        {relatedProducts.slice(0, 4).map(prod => (
                                            <Link key={prod.uid} to={`/product/${prod.slug}`} className="text-decoration-none text-dark bg-white p-2 rounded border shadow-sm d-flex gap-3 align-items-center">
                                                <img src={prod.image} alt={prod.title} style={{ width: '60px', height: '60px', objectFit: 'cover', borderRadius: '8px' }} />
                                                <div className="flex-grow-1 min-width-0">
                                                    <div className="small fw-bold text-truncate">{prod.title}</div>
                                                    <div className="d-flex align-items-center gap-2">
                                                        <span className="fw-bold" style={{ color: mainColor }}>${prod.price.toFixed(2)}</span>
                                                        {prod.old_price > prod.price && (
                                                            <span className="text-muted text-decoration-line-through" style={{ fontSize: '10px' }}>${prod.old_price.toFixed(2)}</span>
                                                        )}
                                                    </div>
                                                    <div className="d-flex align-items-center justify-content-between mt-1">
                                                        <div className="text-warning" style={{ fontSize: '10px' }}>★ {prod.avg_rating || 5.0} (0)</div>
                                                        <div className="text-muted" style={{ fontSize: '10px' }}>19 Sold</div>
                                                    </div>
                                                </div>
                                            </Link>
                                        ))}
                                    </div>
                                </div>
                            )}

                        </div>
                    )}
                </div>

                {/* Related Products */}
                {relatedProducts.length > 0 && (
                    <div className="mt-5 pt-5 border-top" style={{ position: 'relative', zIndex: 1 }}>
                        <div className="d-flex align-items-center gap-2 mb-4">
                            <div style={{ width: '5px', height: '25px', backgroundColor: mainColor, borderRadius: '10px' }}></div>
                            <h4 className="fw-bold mb-0">Similar Products</h4>
                        </div>
                        <div className="row g-3 g-md-4">
                            {relatedProducts.map(prod => (
                                <div key={prod.uid} className="col-6 col-md-4 col-lg-2">
                                    <ProductCard product={prod} />
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </MasterLayout>
    );
};

export default ProductDetails;
