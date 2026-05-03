import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import CategoryDropdown from './CategoryDropdown';

// Detailed Category Data for Mobile Menu
const categoriesData = [
    {
        id: 1, name: 'Beauty & Care',
        sub: [
            { id: 101, name: 'Makeup', child: ['Lipstick', 'Foundation', 'Eyeliner'] },
            { id: 102, name: 'Skin Care', child: ['Moisturizer', 'Sunscreen'] }
        ]
    },
    {
        id: 2, name: 'Sports & Fitness',
        sub: [
            { id: 201, name: 'Cricket', child: ['Bats', 'Balls', 'Pads'] },
            { id: 202, name: 'Football', child: ['Boots', 'Jersey'] }
        ]
    },
    {
        id: 3, name: 'Gadgets & Tech',
        sub: [
            { id: 301, name: 'Mobile', child: ['Smartphones', 'Tablets'] },
            { id: 302, name: 'Accessories', child: ['Earbuds', 'Chargers'] }
        ]
    }
];

const Header = () => {
    const [isCategoryOpen, setIsCategoryOpen] = useState(false);
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
    const [expandedCategory, setExpandedCategory] = useState(null);
    const [isSticky, setIsSticky] = useState(false);
    
    const location = useLocation();
    const mainColor = '#57b500';

    useEffect(() => {
        const handleScroll = () => {
            if (window.scrollY > 40) setIsSticky(true);
            else setIsSticky(false);
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const toggleCategory = (id) => {
        setExpandedCategory(expandedCategory === id ? null : id);
    };

    const navLinkStyle = (path) => ({
        padding: '12px 15px',
        textDecoration: 'none',
        color: location.pathname === path ? mainColor : '#333',
        fontWeight: 'bold',
        fontSize: '14px',
        position: 'relative',
        transition: 'all 0.3s'
    });

    return (
        <header style={{ fontFamily: 'Arial, sans-serif' }}>
            {/* Professional Top Bar */}
            <div style={{ backgroundColor: mainColor, color: '#fff', padding: '8px 0', fontSize: '12px' }}>
                <div className="container d-flex justify-content-between align-items-center">
                    <div style={{ fontWeight: 'bold' }}>⚡ Free shipping on orders over ৳ 5,000</div>
                    <div className="d-none d-lg-flex gap-4">
                        <Link to="/products" className="text-white text-decoration-none">সব পণ্য</Link>
                        <Link to="/" className="text-white text-decoration-none">অর্ডার ট্র্যাক</Link>
                        <Link to="/" className="text-white text-decoration-none">Sell on JHR Bazar</Link>
                    </div>
                </div>
            </div>

            {/* Main Header */}
            <div style={{ 
                position: isSticky ? 'fixed' : 'relative', 
                top: 0, left: 0, width: '100%', zIndex: 10000, 
                backgroundColor: '#fff',
                boxShadow: isSticky ? '0 4px 15px rgba(0,0,0,0.08)' : 'none'
            }}>
                <div style={{ padding: '15px 0', borderBottom: '1px solid #f0f0f0' }}>
                    <div className="container">
                        <div className="row align-items-center">
                            {/* Logo & Toggle */}
                            <div className="col-4 col-lg-2 d-flex align-items-center gap-2">
                                <button className="btn d-lg-none p-0 border-0" onClick={() => setIsMobileMenuOpen(true)} style={{ fontSize: '24px' }}>☰</button>
                                <Link to="/"><img src="https://demo.readyecommerce.app/public/assets/front-end/img/logo.png" alt="Logo" style={{ maxHeight: '45px' }} /></Link>
                            </div>
                            
                            {/* Desktop Search */}
                            <div className="col-lg-5 d-none d-lg-block">
                                <div className="input-group" style={{ borderRadius: '30px', overflow: 'hidden', border: `1px solid ${mainColor}`, backgroundColor: '#f9f9f9' }}>
                                    <input type="text" className="form-control border-0 bg-transparent px-4" placeholder="পণ্য খুঁজুন..." />
                                    <button className="btn border-0 px-4" style={{ backgroundColor: mainColor, color: '#fff', fontWeight: 'bold' }}>Search</button>
                                </div>
                            </div>

                            {/* Icons Row */}
                            <div className="col-8 col-lg-5 d-flex justify-content-end align-items-center gap-3 gap-md-4">
                                <Link to="/" className="text-decoration-none text-dark d-flex flex-column align-items-center hover-primary">
                                    <div style={{ fontSize: '22px' }}>👤</div>
                                    <span style={{ fontSize: '10px', fontWeight: 'bold' }}>LOGIN</span>
                                </Link>
                                <Link to="/wishlist" className="text-decoration-none text-dark d-flex flex-column align-items-center hover-primary">
                                    <div style={{ fontSize: '22px', position: 'relative' }}>
                                        🤍
                                        <span className="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style={{ fontSize: '9px', padding: '2px 5px' }}>0</span>
                                    </div>
                                    <span style={{ fontSize: '10px', fontWeight: 'bold' }}>WISHLIST</span>
                                </Link>
                                <Link to="/cart" className="text-decoration-none text-dark d-flex flex-column align-items-center hover-primary">
                                    <div style={{ fontSize: '22px', position: 'relative' }}>
                                        🛒
                                        <span className="position-absolute top-0 start-100 translate-middle badge rounded-pill" style={{ backgroundColor: mainColor, fontSize: '9px', padding: '2px 5px', color: '#fff' }}>0</span>
                                    </div>
                                    <span style={{ fontSize: '10px', fontWeight: 'bold' }}>CART</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Desktop Nav Links (Matches Screenshot) */}
                <div className="d-none d-lg-block shadow-sm" style={{ backgroundColor: '#fff', borderBottom: '1px solid #eee' }}>
                    <div className="container d-flex align-items-center justify-content-between">
                        <div className="d-flex align-items-center">
                            <div onMouseEnter={() => setIsCategoryOpen(true)} onMouseLeave={() => setIsCategoryOpen(false)} style={{ position: 'relative' }}>
                                <div style={{ padding: '12px 20px', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '8px', color: '#333', fontWeight: 'bold', borderRight: '1px solid #eee' }}>
                                    <span style={{ color: mainColor, fontSize: '18px' }}>⣿</span> Categories
                                </div>
                                <CategoryDropdown isOpen={isCategoryOpen} />
                            </div>
                            <nav className="d-flex ms-2">
                                <Link to="/" style={navLinkStyle('/')} className="nav-item-custom">Home</Link>
                                <Link to="/products" style={navLinkStyle('/products')} className="nav-item-custom">Products</Link>
                                <Link to="/products" style={navLinkStyle('/digital')} className="nav-item-custom">Digital Products</Link>
                                <Link to="/shop-details" style={navLinkStyle('/shops')} className="nav-item-custom">Shops</Link>
                                <Link to="/products" style={navLinkStyle('/popular')} className="nav-item-custom">Most Popular</Link>
                                <Link to="/best-deal" style={navLinkStyle('/best-deal')} className="nav-item-custom">Best Deal</Link>
                                <Link to="/contact" style={navLinkStyle('/contact')} className="nav-item-custom">Contact</Link>
                                <Link to="/blogs" style={navLinkStyle('/blogs')} className="nav-item-custom">Blogs</Link>
                            </nav>
                        </div>
                        <div className="d-flex align-items-center gap-2">
                            <span style={{ fontSize: '18px' }}>📱</span>
                            <span className="small fw-bold text-muted">Download our app</span>
                            <span className="small ms-1">▼</span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Mobile Sidebar */}
            {isMobileMenuOpen && (
                <div onClick={() => setIsMobileMenuOpen(false)} style={{ position: 'fixed', top: 0, left: 0, width: '100%', height: '100%', backgroundColor: 'rgba(0,0,0,0.5)', zIndex: 10001 }}>
                    <div onClick={(e) => e.stopPropagation()} style={{ width: '300px', height: '100%', backgroundColor: '#fff', overflowY: 'auto' }}>
                        <div style={{ padding: '20px', backgroundColor: mainColor, color: '#fff', display: 'flex', justifyContent: 'space-between' }}>
                            <h5 className="mb-0 fw-bold">Menu</h5>
                            <button className="btn text-white p-0" onClick={() => setIsMobileMenuOpen(false)}>✕</button>
                        </div>
                        <div style={{ padding: '10px 0' }}>
                            <div style={{ padding: '15px 20px', fontWeight: 'bold', color: mainColor, borderBottom: '1px solid #f0f0f0' }}>⣿ Browse Categories</div>
                            {categoriesData.map(cat => (
                                <div key={cat.id} className="border-bottom">
                                    <div onClick={() => toggleCategory(cat.id)} style={{ padding: '15px 20px', cursor: 'pointer', display: 'flex', justifyContent: 'space-between', fontSize: '14px' }}>
                                        {cat.name} <span>{expandedCategory === cat.id ? '▼' : '▶'}</span>
                                    </div>
                                    {expandedCategory === cat.id && (
                                        <div style={{ backgroundColor: '#fdfdfd', paddingLeft: '20px' }}>
                                            {cat.sub.map(sub => (
                                                <div key={sub.id} style={{ padding: '10px 20px', fontSize: '13px' }}>
                                                    <div style={{ fontWeight: 'bold', color: '#555', marginBottom: '5px' }}>{sub.name}</div>
                                                    <div className="d-flex flex-wrap gap-2">
                                                        {sub.child.map(child => (
                                                            <Link key={child} to="/products" className="text-decoration-none text-muted" style={{ fontSize: '11px', border: '1px solid #eee', padding: '2px 8px', borderRadius: '4px' }}>{child}</Link>
                                                        ))}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            ))}
                            <div style={{ padding: '15px 20px', fontWeight: 'bold', marginTop: '10px' }}>Quick Links</div>
                            <Link to="/" className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">Home</Link>
                            <Link to="/products" className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">All Products</Link>
                            <Link to="/cart" className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">My Cart</Link>
                            <Link to="/wishlist" className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">My Wishlist</Link>
                        </div>
                    </div>
                </div>
            )}
            
            <style>{`
                .hover-primary:hover { color: ${mainColor} !important; opacity: 0.8; }
                .nav-item-custom:hover { color: ${mainColor} !important; }
                .nav-item-custom { position: relative; }
                ${location.pathname === '/' ? '.nav-item-custom:first-child::after' : ''}
                ${location.pathname === '/products' ? '.nav-item-custom:nth-child(2)::after' : ''}
                .nav-item-custom::after {
                    content: '';
                    position: absolute;
                    bottom: 0;
                    left: 15px;
                    right: 15px;
                    height: 3px;
                    background-color: #ff4d4d;
                    transform: scaleX(0);
                    transition: transform 0.3s;
                }
                /* Dynamic Active Underline */
                .nav-item-custom[href="${location.pathname}"]::after {
                    transform: scaleX(1);
                }
            `}</style>
            
            {isSticky && <div style={{ height: '150px' }}></div>}
        </header>
    );
};

export default Header;
