import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';
import axios from 'axios';
import { useCart } from '../context/CartContext';
import { useWishlist } from '../context/WishlistContext';

const ProductDetails = () => {
    const { type, id } = useParams();
    const mainColor = '#57b500';
    const [product, setProduct] = useState(null);
    const [loading, setLoading] = useState(true);
    const [quantity, setQuantity] = useState(1);
    const [zoomPos, setZoomPos] = useState({ x: 0, y: 0, show: false });
    const [activeImageIndex, setActiveImageIndex] = useState(0);
    const [activeTab, setActiveTab] = useState('about');
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    const [toast, setToast] = useState({ show: false, message: '' });
    const [relatedProducts, setRelatedProducts] = useState([]);
    const { addToCart } = useCart();
    const { toggleWishlist, isInWishlist } = useWishlist();
    const navigate = useNavigate();

    useEffect(() => {
        const fetchProduct = async () => {
            setLoading(true);
            try {
                const res = await axios.get(`/api/product/${type}/${id}`);
                if (res.data.success) {
                    setProduct(res.data.data);

                    // Fetch related products
                    const relatedRes = await axios.get(`/api/product/${type}/${id}/related`);
                    if (relatedRes.data.success) {
                        setRelatedProducts(relatedRes.data.data);
                    }
                }
            } catch (error) {
                console.error("Error fetching product details:", error);
            } finally {
                setLoading(false);
            }
        };
        fetchProduct();
        window.scrollTo(0, 0);
    }, [type, id]);

    const showToast = (message) => {
        setToast({ show: true, message });
        setTimeout(() => setToast({ show: false, message: '' }), 3000);
    };

    const handleBuyNow = () => {
        addToCart(product, quantity, product.color, product.size);
        navigate('/checkout');
    };

    const handleAddToCart = () => {
        addToCart(product, quantity, product.color, product.size);
        showToast('কার্টে যোগ করা হয়েছে!');
    };

    const handleMouseMove = (e) => {
        const rect = e.currentTarget.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;
        const boundedX = Math.max(0, Math.min(100, x));
        const boundedY = Math.max(0, Math.min(100, y));
        setZoomPos({ x: boundedX, y: boundedY, show: true });
    };

    if (loading) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center">
                    <div className="spinner-border text-success" role="status">
                        <span className="visually-hidden">লোড হচ্ছে...</span>
                    </div>
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

    const productImages = [product.thumbnail, ...(product.gallery || [])];

    return (
        <MasterLayout>
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
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .hover-wishlist:hover { color: #ff4d4d !important; transform: scale(1.05); }
                .hover-share:hover { color: ${mainColor} !important; transform: scale(1.05); }
            `}</style>

            <div className="container py-4">
                {/* Breadcrumbs */}
                <nav className="mb-4">
                    <ol className="breadcrumb small" style={{ fontSize: '12px' }}>
                        <li className="breadcrumb-item"><Link to="/" className="text-decoration-none text-muted">হোম</Link></li>
                        <li className="breadcrumb-item active text-dark fw-bold">{product.name}</li>
                    </ol>
                </nav>

                <div className="row g-4">
                    {/* Left: Gallery & Main Info */}
                    <div className="col-lg-9">
                        <div className="row g-4">
                            {/* Product Images with Zoom */}
                            <div className="col-md-5">
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
                                        padding: '0'
                                    }}
                                >
                                    <img
                                        src={productImages[activeImageIndex]}
                                        alt={product.name}
                                        style={{ width: '100%', height: 'auto', objectFit: 'cover', display: 'block' }}
                                    />
                                </div>
                                {/* Thumbnail Slider */}
                                <div className="d-flex gap-2 mt-3 overflow-auto pb-2" style={{ scrollbarWidth: 'thin' }}>
                                    {productImages.map((img, i) => (
                                        <div
                                            key={i}
                                            onClick={() => setActiveImageIndex(i)}
                                            style={{
                                                width: '80px',
                                                height: '80px',
                                                minWidth: '80px',
                                                border: i === activeImageIndex ? `2px solid ${mainColor}` : '1px solid #eee',
                                                borderRadius: '10px',
                                                overflow: 'hidden',
                                                cursor: 'pointer',
                                                padding: '5px',
                                                backgroundColor: '#fff',
                                                transition: 'all 0.2s',
                                                opacity: i === activeImageIndex ? 1 : 0.6
                                            }}
                                        >
                                            <img src={img} style={{ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '5px' }} alt="thumb" />
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Product Purchase Info */}
                            <div className="col-md-7 ps-md-4 position-relative">
                                {/* Zoom Overlay */}
                                {zoomPos.show && (
                                    <div style={{
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

                                <span className="badge mb-2 px-3 py-2" style={{ backgroundColor: '#fff0f3', color: '#ff4d4d', fontSize: '10px', fontWeight: 'bold', borderRadius: '5px' }}>{product.category || 'Product'}</span>
                                <h2 className="fw-bold mb-2" style={{ color: '#333' }}>{product.name}</h2>
                                <p className="text-muted mb-4" style={{ fontSize: '13px', lineHeight: '1.6' }}>{product.short_description}</p>

                                <div className="d-flex align-items-center gap-3 mb-4">
                                    <div className="text-warning small">★★★★★ <span className="text-muted">(০ রিভিউ)</span></div>
                                    <div className="text-muted small">| <span className="text-dark fw-bold">০</span> টি বিক্রিত</div>
                                    <div className="ms-auto d-flex gap-3 align-items-center">
                                        <div className="text-muted small cursor-pointer hover-share" title="শেয়ার করুন">🔗 <span className="d-none d-sm-inline">শেয়ার</span></div>
                                        <div
                                            className="d-flex align-items-center gap-1 cursor-pointer hover-wishlist"
                                            onClick={() => toggleWishlist(product)}
                                            title={isInWishlist(product.id, type) ? "উইশলিস্ট থেকে সরান" : "উইশলিস্টে যোগ করুন"}
                                            style={{
                                                color: isInWishlist(product.id, type) ? '#ff4d4d' : '#666',
                                                transition: 'all 0.3s'
                                            }}
                                        >
                                            <span style={{ fontSize: '20px' }}>{isInWishlist(product.id, type) ? '❤️' : '🤍'}</span>
                                            <span className="small fw-bold d-none d-sm-inline">
                                                {isInWishlist(product.id, type) ? 'উইশলিস্টে আছে' : 'উইশলিস্ট'}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div className="d-flex align-items-center gap-3 mb-4">
                                    <h2 className="fw-bold mb-0" style={{ color: mainColor }}>${product.price.toFixed(2)}</h2>
                                    {product.old_price > product.price && (
                                        <span className="text-muted text-decoration-line-through">${product.old_price.toFixed(2)}</span>
                                    )}
                                </div>

                                {/* Product Variations */}
                                <div className="mb-4">
                                    <div className="d-flex align-items-center gap-4 mb-3">
                                        <div className="small"><span className="text-muted">ব্র্যান্ড:</span> <span className="fw-bold text-dark">{product.brand || 'N/A'}</span></div>
                                        <div className="small"><span className="text-muted">SKU:</span> <span className="fw-bold text-dark">{product.sku || 'N/A'}</span></div>
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

                                    {/* Quantity Selection */}
                                    <div className="mb-4">
                                        <label className="small fw-bold mb-2">পরিমাণ:</label>
                                        <div className="d-flex align-items-center" style={{ width: '120px', border: '1px solid #ddd', borderRadius: '5px', overflow: 'hidden' }}>
                                            <button onClick={() => setQuantity(Math.max(1, quantity - 1))} className="btn btn-light border-0 px-3 py-1" style={{ borderRadius: 0, backgroundColor: '#f8f9fa' }}>-</button>
                                            <input type="text" value={quantity} readOnly className="form-control border-0 text-center px-0 py-1 bg-white" style={{ width: '40px', fontSize: '14px', borderRadius: 0, boxShadow: 'none' }} />
                                            <button onClick={() => setQuantity(quantity + 1)} className="btn btn-light border-0 px-3 py-1" style={{ borderRadius: 0, backgroundColor: '#f8f9fa' }}>+</button>
                                        </div>
                                        <small className="text-muted mt-2 d-block">স্টক: {product.stock}</small>
                                    </div>
                                </div>

                                {/* Buttons */}
                                <div className="d-flex gap-3 mt-5">
                                    <button
                                        onClick={handleAddToCart}
                                        className="btn btn-lg flex-grow-1 d-flex align-items-center justify-content-center gap-2"
                                        style={{
                                            border: `1.5px solid ${mainColor}`,
                                            color: mainColor,
                                            backgroundColor: '#fff',
                                            borderRadius: '8px',
                                            fontSize: '15px',
                                            fontWeight: 'bold',
                                            padding: '12px'
                                        }}
                                    >
                                        🛒 কার্টে যোগ করুন
                                    </button>
                                    <button
                                        onClick={handleBuyNow}
                                        className="btn btn-lg flex-grow-1 text-white fw-bold"
                                        style={{
                                            backgroundColor: mainColor,
                                            borderRadius: '8px',
                                            fontSize: '15px',
                                            padding: '12px'
                                        }}
                                    >
                                        অর্ডার করুন
                                    </button>
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
                                        style={{ borderColor: activeTab === 'about' ? `${mainColor} !important` : 'transparent', color: activeTab === 'about' ? '#333' : '', fontSize: '14px' }}
                                    >
                                        প্রোডাক্ট বিবরণ
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button
                                        onClick={() => setActiveTab('reviews')}
                                        className={`nav-link border-0 p-0 pb-2 ${activeTab === 'reviews' ? 'active border-bottom border-3 fw-bold' : 'text-muted'}`}
                                        style={{ borderColor: activeTab === 'reviews' ? `${mainColor} !important` : 'transparent', color: activeTab === 'reviews' ? '#333' : '', fontSize: '14px' }}
                                    >
                                        রিভিউ (১২০)
                                    </button>
                                </li>
                            </ul>
                            <div className="bg-white p-4 rounded shadow-sm border" style={{ fontSize: '13px', lineHeight: '1.8', color: '#666' }}>
                                {activeTab === 'about' && (
                                    <div dangerouslySetInnerHTML={{ __html: product.description }}></div>
                                )}

                                {activeTab === 'reviews' && (
                                    <div>
                                        {!isLoggedIn ? (
                                            <div className="text-center py-5">
                                                <div className="mb-3 text-muted" style={{ fontSize: '40px' }}>🔒</div>
                                                <h5 className="fw-bold text-dark mb-2">লগইন প্রয়োজন</h5>
                                                <p className="text-muted mb-4">রিভিউ দেখতে এবং লিখতে আপনাকে অবশ্যই লগইন করতে হবে।</p>
                                                <button
                                                    onClick={() => setIsLoggedIn(true)}
                                                    className="btn text-white px-4 py-2"
                                                    style={{ backgroundColor: mainColor, borderRadius: '8px', fontWeight: 'bold' }}
                                                >
                                                    লগইন করুন
                                                </button>
                                            </div>
                                        ) : (
                                            <div>
                                                <h6 className="fw-bold text-dark mb-3">একটি রিভিউ লিখুন</h6>
                                                <div className="mb-3">
                                                    <label className="fw-bold mb-2 d-block">আপনার রেটিং</label>
                                                    <div className="d-flex gap-1" style={{ fontSize: '20px', color: '#ffc107', cursor: 'pointer' }}>
                                                        <span>★</span><span>★</span><span>★</span><span>★</span><span className="text-muted">★</span>
                                                    </div>
                                                </div>
                                                <div className="mb-3">
                                                    <label className="fw-bold mb-2">আপনার মন্তব্য</label>
                                                    <textarea className="form-control" rows="4" placeholder="আপনার অভিজ্ঞতা লিখুন..."></textarea>
                                                </div>
                                                <button className="btn text-white fw-bold px-4 py-2" style={{ backgroundColor: mainColor, borderRadius: '8px' }}>
                                                    রিভিউ জমা দিন
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Right Sidebar */}
                    <div className="col-lg-3">
                        {/* Delivery Info */}
                        <div className="card border-0 shadow-sm mb-3" style={{ borderRadius: '12px', backgroundColor: '#f8f9fa' }}>
                            <div className="card-body p-3">
                                <div className="d-flex align-items-center gap-3 mb-3 pb-2 border-bottom">
                                    <div style={{ fontSize: '18px' }}>🚚</div>
                                    <div>
                                        <div className="small text-muted" style={{ fontSize: '11px' }}>ডেলিভারি চার্জ</div>
                                        <div className="fw-bold small">৳০.০০</div>
                                    </div>
                                </div>
                                <div className="d-flex align-items-center gap-3">
                                    <div style={{ fontSize: '18px' }}>🕒</div>
                                    <div>
                                        <div className="small text-muted" style={{ fontSize: '11px' }}>সম্ভাব্য ডেলিভারি</div>
                                        <div className="fw-bold small">৩-৭ দিন</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {/* Related Products */}
                {relatedProducts.length > 0 && (
                    <div className="mt-5 pt-4">
                        <div className="d-flex align-items-center gap-2 mb-4">
                            <div style={{ width: '5px', height: '25px', backgroundColor: mainColor, borderRadius: '10px' }}></div>
                            <h4 className="fw-bold mb-0">সম্পর্কিত প্রোডাক্ট</h4>
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
