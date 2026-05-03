import React, { useState } from 'react';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';

const similarProducts = [
    { 
        id: 1, title: "Adobe After Effects 2025 Professional", 
        price: 18.00, discount: 0, sold: 10, rating: '4.8', reviews: 15,
        image: "https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 2, title: "Official Licensed Photoshop for Creators", 
        price: 15.00, oldPrice: 18.00, discount: 16, sold: 45, rating: '4.9', reviews: 32,
        image: "https://images.unsplash.com/photo-1573167243872-43cce44c797a?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 3, title: "Kaspersky Total Security Anti-Virus", 
        price: 27.00, oldPrice: 29.00, discount: 6, sold: 60, rating: '4.7', reviews: 25,
        image: "https://images.unsplash.com/photo-1563986768609-322da13575f3?q=80&w=500&auto=format&fit=crop" 
    },
    { 
        id: 4, title: "Microsoft Windows 11 Pro 64-Bit", 
        price: 31.00, discount: 0, sold: 89, rating: '5.0', reviews: 18,
        image: "https://images.unsplash.com/photo-1633419461186-7d40a38105ec?q=80&w=500&auto=format&fit=crop" 
    }
];

const ProductDetails = () => {
    const mainColor = '#57b500';
    const [quantity, setQuantity] = useState(1);
    const [selectedSize, setSelectedSize] = useState('M');
    const [selectedColor, setSelectedColor] = useState('Black');
    const [zoomPos, setZoomPos] = useState({ x: 0, y: 0, show: false });

    const handleMouseMove = (e) => {
        const { left, top, width, height } = e.currentTarget.getBoundingClientRect();
        const x = ((e.pageX - left) / width) * 100;
        const y = ((e.pageY - top) / height) * 100;
        setZoomPos({ x, y, show: true });
    };

    return (
        <MasterLayout>
            <div className="container py-4">
                {/* Breadcrumbs */}
                <nav className="mb-4">
                    <ol className="breadcrumb small" style={{ fontSize: '12px' }}>
                        <li className="breadcrumb-item"><a href="/" className="text-decoration-none text-muted">Home</a></li>
                        <li className="breadcrumb-item active text-dark fw-bold">Adobe Premiere Pro 2025</li>
                    </ol>
                </nav>

                <div className="row g-4">
                    {/* Left: Gallery & Main Info */}
                    <div className="col-lg-9">
                        <div className="row g-4">
                            {/* Product Images with Zoom - Fixed to prevent cutting */}
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
                                        cursor: 'zoom-in',
                                        height: '400px',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        padding: '20px'
                                    }}
                                >
                                    <img 
                                        src="https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?q=80&w=800&auto=format&fit=crop" 
                                        alt="Product" 
                                        style={{ width: '100%', height: '100%', objectFit: 'contain', display: 'block' }} 
                                    />
                                    {zoomPos.show && (
                                        <div style={{
                                            position: 'absolute',
                                            top: 0, left: 0, width: '100%', height: '100%',
                                            backgroundImage: `url(https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?q=80&w=1200&auto=format&fit=crop)`,
                                            backgroundPosition: `${zoomPos.x}% ${zoomPos.y}%`,
                                            backgroundSize: '250%',
                                            pointerEvents: 'none',
                                            zIndex: 10
                                        }}></div>
                                    )}
                                </div>
                                <div className="d-flex gap-2 mt-3">
                                    {[1, 2].map(i => (
                                        <div key={i} style={{ width: '80px', height: '80px', border: i === 1 ? `2px solid ${mainColor}` : '1px solid #eee', borderRadius: '10px', overflow: 'hidden', cursor: 'pointer', padding: '5px', backgroundColor: '#fff' }}>
                                            <img src="https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?q=80&w=150&auto=format&fit=crop" style={{ width: '100%', height: '100%', objectFit: 'contain' }} alt="thumb" />
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Product Purchase Info */}
                            <div className="col-md-7 ps-md-4">
                                <span className="badge mb-2 px-3 py-2" style={{ backgroundColor: '#fff0f3', color: '#ff4d4d', fontSize: '10px', fontWeight: 'bold', borderRadius: '5px' }}>Top Software</span>
                                <h2 className="fw-bold mb-2" style={{ color: '#333' }}>Adobe Premiere Pro 2025</h2>
                                <p className="text-muted mb-4" style={{ fontSize: '13px', lineHeight: '1.6' }}>Professional video editing software for creating high-quality, cinematic videos with ease and precision.</p>
                                
                                <div className="d-flex align-items-center gap-3 mb-4">
                                    <div className="text-warning small">⭐⭐⭐⭐⭐ <span className="text-muted">(120 Reviews)</span></div>
                                    <div className="text-muted small">| <span className="text-dark fw-bold">450</span> Sold</div>
                                    <div className="text-muted small ms-auto cursor-pointer">🔗 Share</div>
                                    <div className="text-muted small cursor-pointer">🤍</div>
                                </div>

                                <h2 className="fw-bold mb-4" style={{ color: mainColor }}>$19.00</h2>

                                {/* Buttons */}
                                <div className="d-flex gap-3 mt-5">
                                    <button 
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
                                        🛒 Add to Cart
                                    </button>
                                    <button 
                                        className="btn btn-lg flex-grow-1 text-white fw-bold" 
                                        style={{ 
                                            backgroundColor: mainColor, 
                                            borderRadius: '8px', 
                                            fontSize: '15px', 
                                            padding: '12px'
                                        }}
                                    >
                                        Buy Now
                                    </button>
                                </div>
                            </div>
                        </div>

                        {/* Tabs */}
                        <div className="mt-5">
                            <ul className="nav nav-tabs border-0 gap-4 mb-3">
                                <li className="nav-item">
                                    <button className="nav-link active border-0 border-bottom border-3 fw-bold p-0 pb-2" style={{ borderColor: `${mainColor} !important`, color: '#333', fontSize: '14px' }}>About Product</button>
                                </li>
                                <li className="nav-item">
                                    <button className="nav-link border-0 text-muted p-0 pb-2" style={{ fontSize: '14px' }}>Reviews</button>
                                </li>
                            </ul>
                            <div className="bg-white p-4 rounded shadow-sm border" style={{ fontSize: '13px', lineHeight: '1.8', color: '#666' }}>
                                <p className="fw-bold text-dark">Elevate Your Video Editing Experience</p>
                                <p>Unlock your creative potential with Adobe Premiere Pro 2025, the leading professional video editing software designed for filmmakers, content creators, and video enthusiasts.</p>
                                <h6 className="fw-bold text-dark mt-4 mb-2">Key Features:</h6>
                                <ul className="ps-3">
                                    <li><b>AI Text-Based Editing:</b> Create rough cuts just by reading the transcript.</li>
                                    <li><b>Auto Reframe:</b> Automatically optimizes your video for social formats.</li>
                                    <li><b>HDR Support:</b> High Dynamic Range for professional broadcast standards.</li>
                                </ul>
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
                                        <div className="small text-muted" style={{ fontSize: '11px' }}>Delivery charge</div>
                                        <div className="fw-bold small">$2.00</div>
                                    </div>
                                </div>
                                <div className="d-flex align-items-center gap-3">
                                    <div style={{ fontSize: '18px' }}>🕒</div>
                                    <div>
                                        <div className="small text-muted" style={{ fontSize: '11px' }}>Estimated delivery</div>
                                        <div className="fw-bold small">4 Days</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Sold By */}
                        <div className="card border-0 shadow-sm mb-3" style={{ borderRadius: '12px' }}>
                            <div className="card-body p-3">
                                <div className="d-flex align-items-center gap-2 mb-3 text-start">
                                    <img src="https://images.unsplash.com/photo-1614850523296-d8c1af93d400?q=80&w=45&auto=format&fit=crop" alt="logo" style={{ width: '45px', height: '45px', borderRadius: '50%' }} />
                                    <div className="text-start">
                                        <div className="small text-muted" style={{ fontSize: '10px' }}>Sold by</div>
                                        <div className="fw-bold small">JHR Tech World</div>
                                    </div>
                                    <div className="ms-auto text-warning fw-bold small">★ 5.0</div>
                                </div>
                                <button className="btn btn-link w-100 text-decoration-none fw-bold" style={{ color: mainColor, fontSize: '13px' }}>Visit Store</button>
                            </div>
                        </div>

                        {/* Popular Products List */}
                        <div className="card border-0 shadow-sm" style={{ borderRadius: '12px' }}>
                            <div className="card-body p-3">
                                <h6 className="fw-bold mb-4" style={{ fontSize: '13px' }}>Popular Products From Them</h6>
                                <div className="d-flex flex-column gap-3">
                                    {[1, 2, 3].map(i => (
                                        <div key={i} className="d-flex gap-2 align-items-center">
                                            <img src="https://images.unsplash.com/photo-1557935728-e6d1eaabe558?q=80&w=50&auto=format&fit=crop" alt="p" style={{ width: '60px', height: '60px', borderRadius: '8px', objectFit: 'contain', backgroundColor: '#fcfcfc' }} />
                                            <div>
                                                <div className="small text-truncate" style={{ maxWidth: '140px', fontSize: '12px' }}>Fitbit Charge 6 Fitness Tracker</div>
                                                <div className="d-flex align-items-center gap-2 mt-1">
                                                    <span className="text-danger fw-bold" style={{ fontSize: '12px' }}>$1000.00</span>
                                                </div>
                                                <div className="mt-1" style={{ fontSize: '10px', color: mainColor, cursor: 'pointer' }}>Buy Now →</div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Similar Products */}
                <div className="mt-5 pt-4">
                    <h4 className="fw-bold mb-4">Similar Products</h4>
                    <div className="row g-3 g-md-4">
                        {similarProducts.map(product => (
                            <div key={product.id} className="col-6 col-md-3">
                                <ProductCard product={product} />
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </MasterLayout>
    );
};

export default ProductDetails;
