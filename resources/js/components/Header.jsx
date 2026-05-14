import React, { useState, useEffect } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import CategoryDropdown from './CategoryDropdown';
import { useCart } from '../context/CartContext';
import { useSettings } from '../context/SettingsContext';
import { useWishlist } from '../context/WishlistContext';

// Detailed Category Data for Mobile Menu (Removed - Now using real data)

const TypingSearchInput = ({ mainColor }) => {
    const navigate = useNavigate();
    const placeholderTexts = [
        "পণ্য, ব্র্যান্ড, ক্যাটাগরি খুঁজুন...",
        "ল্যাপটপ খুঁজছেন?",
        "স্মার্টফোন খুঁজছেন?",
        "স্মার্ট ওয়াচ খুঁজছেন?",
        "হেডফোন খুঁজছেন?"
    ];
    const [placeholderIndex, setPlaceholderIndex] = useState(0);
    const [placeholderText, setPlaceholderText] = useState("");
    const [isDeleting, setIsDeleting] = useState(false);

    // Search Logic States
    const [query, setQuery] = useState("");
    const [results, setResults] = useState([]);
    const [isSearching, setIsSearching] = useState(false);
    const [showDropdown, setShowDropdown] = useState(false);

    // Placeholder Animation
    useEffect(() => {
        const currentText = placeholderTexts[placeholderIndex];
        let timer;

        if (isDeleting) {
            timer = setTimeout(() => {
                setPlaceholderText(currentText.substring(0, placeholderText.length - 1));
                if (placeholderText.length === 0) {
                    setIsDeleting(false);
                    setPlaceholderIndex((prev) => (prev + 1) % placeholderTexts.length);
                }
            }, 50);
        } else {
            timer = setTimeout(() => {
                setPlaceholderText(currentText.substring(0, placeholderText.length + 1));
                if (placeholderText.length === currentText.length) {
                    timer = setTimeout(() => setIsDeleting(true), 2000);
                }
            }, 100);
        }

        return () => clearTimeout(timer);
    }, [placeholderText, isDeleting, placeholderIndex]);

    // Live Search Effect (Debounced)
    useEffect(() => {
        const delayDebounceFn = setTimeout(async () => {
            if (query.trim().length >= 2) {
                setIsSearching(true);
                try {
                    const res = await axios.get(`/api/products/search?q=${query}`);
                    if (res.data.success) {
                        setResults(res.data.data);
                        setShowDropdown(true);
                    }
                } catch (err) {
                    console.error("Search error:", err);
                } finally {
                    setIsSearching(false);
                }
            } else {
                setResults([]);
                setShowDropdown(false);
            }
        }, 400);

        return () => clearTimeout(delayDebounceFn);
    }, [query]);

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        if (query.trim()) {
            setShowDropdown(false);
            navigate(`/search?q=${query}`);
        }
    };

    return (
        <div className="position-relative w-100">
            <form onSubmit={handleSearchSubmit} className="input-group" style={{
                borderRadius: '10px',
                overflow: 'hidden',
                border: `1px solid #ddd`,
                backgroundColor: '#fff',
                boxShadow: showDropdown ? '0 10px 25px rgba(0,0,0,0.1)' : 'none'
            }}>
                <input
                    type="text"
                    className="form-control border-0 bg-transparent px-4 py-2"
                    placeholder={placeholderText}
                    value={query}
                    onChange={(e) => setQuery(e.target.value)}
                    onFocus={() => query.length >= 2 && setShowDropdown(true)}
                    style={{ fontSize: '14px' }}
                />
                <button type="submit" className="btn border-0 px-4 d-flex align-items-center gap-2" style={{ backgroundColor: 'var(--button-color, #57b500)', color: '#fff', fontWeight: 'bold' }}>
                    {isSearching ? <span className="spinner-border spinner-border-sm me-1"></span> : <><i className="fas fa-search"></i> সার্চ</>}
                </button>
            </form>

            {/* Results Dropdown */}
            {showDropdown && results.length > 0 && (
                <div className="position-absolute w-100 bg-white shadow-lg rounded-3 mt-1 overflow-hidden" style={{ zIndex: 11000, border: '1px solid #eee' }}>
                    <div className="p-2 bg-light small fw-bold text-muted border-bottom d-flex justify-content-between">
                        <span>পরামর্শ</span>
                        <span>{results.length} টি ফলাফল</span>
                    </div>
                    <div style={{ maxHeight: '400px', overflowY: 'auto' }}>
                        {results.map((item) => (
                            <Link
                                key={item.uid}
                                to={`/product-details/${item.product_type}/${item.slug}`}
                                className="d-flex align-items-center gap-3 p-2 text-decoration-none border-bottom hover-bg-light"
                                onClick={() => setShowDropdown(false)}
                            >
                                <img src={item.image} alt="" style={{ width: '45px', height: '45px', objectFit: 'cover', borderRadius: '6px' }} />
                                <div className="flex-grow-1 overflow-hidden">
                                    <div className="text-dark fw-bold text-truncate" style={{ fontSize: '13px' }}>{item.title}</div>
                                    <div style={{ color: mainColor, fontWeight: 'bold', fontSize: '12px' }}>৳ {item.price.toLocaleString()}</div>
                                </div>
                                <div className="text-muted" style={{ fontSize: '10px' }}>
                                    <i className="bi bi-chevron-right"></i>
                                </div>
                            </Link>
                        ))}
                    </div>
                    <Link
                        to={`/search?q=${query}`}
                        className="d-block p-2 text-center text-decoration-none fw-bold small"
                        style={{ color: mainColor, backgroundColor: '#f8fff0' }}
                        onClick={() => setShowDropdown(false)}
                    >
                        সব ফলাফল দেখুন <i className="bi bi-arrow-right ms-1"></i>
                    </Link>
                </div>
            )}

            {showDropdown && results.length === 0 && query.length >= 2 && !isSearching && (
                <div className="position-absolute w-100 bg-white shadow-lg rounded-3 mt-1 p-4 text-center text-muted small" style={{ zIndex: 11000, border: '1px solid #eee' }}>
                    <div className="mb-2 fs-4">🔍</div>
                    "{query}" এর জন্য কোনো পণ্য পাওয়া যায়নি
                </div>
            )}

            {/* Dropdown Overlay to close on click outside */}
            {showDropdown && (
                <div
                    className="position-fixed top-0 start-0 w-100 h-100"
                    style={{ zIndex: 10500, pointerEvents: 'auto' }}
                    onClick={() => setShowDropdown(false)}
                ></div>
            )}

            <style>{`
                .hover-bg-light:hover { background-color: #f8fafc; }
                .hover-bg-light:hover .text-dark { color: ${mainColor} !important; }
            `}</style>
        </div>
    );
};

const Header = () => {
    const [isCategoryOpen, setIsCategoryOpen] = useState(false);
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
    const [expandedCategory, setExpandedCategory] = useState(null);
    const [isSticky, setIsSticky] = useState(false);

    const { settings, categories: realCategories } = useSettings();
    const { cartCount } = useCart();
    const { wishlist } = useWishlist();

    const location = useLocation();
    const mainColor = settings?.primary_color || '#001fcc';
    const topHeaderColor = settings?.top_header_color || '#001fcc';
    const headerColor = settings?.header_color || '#ffffff';

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
        <header style={{ fontFamily: 'inherit' }}>
            {/* Professional Top Bar */}
            <div style={{ backgroundColor: topHeaderColor, color: '#fff', padding: '8px 0', fontSize: '12px' }}>
                <div className="container d-flex justify-content-between align-items-center">
                    <div style={{ fontWeight: 'bold' }}>⚡ ৫,০০০ টাকার বেশি অর্ডারে ফ্রি শিপিং</div>
                    <div className="d-none d-lg-flex gap-4">
                        <Link to="/products" className="text-white text-decoration-none">সব পণ্য</Link>
                        <Link to="/order-tracking" className="text-white text-decoration-none">অর্ডার ট্র্যাক</Link>
                        <a href="/seller/login" className="text-white text-decoration-none">সেলার হন</a>
                    </div>
                </div>
            </div>

            {/* Main Header */}
            <div style={{
                position: isSticky ? 'fixed' : 'relative',
                top: 0, left: 0, width: '100%', zIndex: 10000,
                backgroundColor: headerColor,
                boxShadow: isSticky ? '0 4px 15px rgba(0,0,0,0.08)' : 'none'
            }}>
                <div style={{ padding: '15px 0', borderBottom: '1px solid #f0f0f0' }}>
                    <div className="container">
                        <div className="row align-items-center">
                            {/* Logo & Toggle */}
                            <div className="col-4 col-lg-2 d-flex align-items-center gap-2">
                                <button className="btn d-lg-none p-0 border-0" onClick={() => setIsMobileMenuOpen(true)} style={{ fontSize: '24px' }}>☰</button>
                                <Link to="/">
                                    <img
                                        src={settings?.logo || "https://demo.readyecommerce.app/public/assets/front-end/img/logo.png"}
                                        alt={settings?.website_name || "Logo"}
                                        style={{ maxHeight: '45px' }}
                                    />
                                </Link>
                            </div>

                            {/* Desktop Search */}
                            <div className="col-lg-5 d-none d-lg-block">
                                <TypingSearchInput mainColor={mainColor} />
                            </div>

                            {/* Icons Row */}
                            <div className="col-8 col-lg-5 d-flex justify-content-end align-items-center gap-3 gap-md-4">
                                <Link to={localStorage.getItem('auth_token') ? "/customer/dashboard" : "/customer/login"} className="text-decoration-none text-dark d-flex flex-column align-items-center hover-primary">
                                    <div style={{ fontSize: '22px' }}>👤</div>
                                    <span style={{ fontSize: '10px', fontWeight: 'bold' }}>{localStorage.getItem('auth_token') ? "ড্যাশবোর্ড" : "লগইন"}</span>
                                </Link>
                                <Link to="/wishlist" className="text-decoration-none text-dark d-flex flex-column align-items-center hover-primary">
                                    <div style={{ fontSize: '22px', position: 'relative' }}>
                                        🤍
                                        <span className="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style={{ fontSize: '9px', padding: '2px 5px' }}>{wishlist.length}</span>
                                    </div>
                                    <span style={{ fontSize: '10px', fontWeight: 'bold' }}>উইশলিস্ট</span>
                                </Link>
                                <Link to="/cart" className="text-decoration-none text-dark d-flex flex-column align-items-center hover-primary">
                                    <div style={{ fontSize: '22px', position: 'relative' }}>
                                        🛒
                                        <span className="position-absolute top-0 start-100 translate-middle badge rounded-pill" style={{ backgroundColor: mainColor, fontSize: '9px', padding: '2px 5px', color: '#fff' }}>{cartCount}</span>
                                    </div>
                                    <span style={{ fontSize: '10px', fontWeight: 'bold' }}>কার্ট</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Mobile Search Bar (Visible only on mobile/tablet) */}
                <div className="d-lg-none pb-3 pt-1">
                    <div className="container">
                        <TypingSearchInput mainColor={mainColor} />
                    </div>
                </div>

                {/* Desktop Nav Links (Matches Screenshot) */}
                <div className="d-none d-lg-block shadow-sm" style={{ backgroundColor: '#fff', borderBottom: '1px solid #eee' }}>
                    <div className="container d-flex align-items-center justify-content-between">
                        <div className="d-flex align-items-center">
                            {!(settings?.sidebar_behavior === 'fixed' && location.pathname === '/') && (
                                <div onMouseEnter={() => setIsCategoryOpen(true)} onMouseLeave={() => setIsCategoryOpen(false)} style={{ position: 'relative' }}>
                                    <div style={{ padding: '12px 20px', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '8px', color: '#333', fontWeight: 'bold', borderRight: '1px solid #eee' }}>
                                        <span style={{ color: mainColor, fontSize: '18px' }}>⣿</span> ক্যাটাগরি
                                    </div>
                                    <CategoryDropdown isOpen={isCategoryOpen} />
                                </div>
                            )}
                            <nav className={`d-flex ${settings?.sidebar_behavior === 'fixed' && location.pathname === '/' ? '' : 'ms-2'}`}>
                                <Link to="/" style={navLinkStyle('/')} className="nav-item-custom">হোম</Link>
                                <Link to="/products-all/all" style={navLinkStyle('/products-all/all')} className="nav-item-custom">প্রোডাক্ট</Link>
                                <Link to="/products-all/digital" style={navLinkStyle('/products-all/digital')} className="nav-item-custom">ডিজিটাল প্রোডাক্ট</Link>
                                <Link to="/products-all/popular" style={navLinkStyle('/products-all/popular')} className="nav-item-custom">জনপ্রিয় প্রোডাক্ট</Link>
                                <Link to="/products-all/best-deal" style={navLinkStyle('/products-all/best-deal')} className="nav-item-custom">সেরা অফার</Link>
                                <Link to="/contact" style={navLinkStyle('/contact')} className="nav-item-custom">যোগাযোগ</Link>
                                <Link to="/blogs" style={navLinkStyle('/blogs')} className="nav-item-custom">ব্লগ</Link>
                                <Link to="/about" style={navLinkStyle('/about')} className="nav-item-custom">আমাদের সম্পর্কে</Link>
                                <Link to="/terms" style={navLinkStyle('/terms')} className="nav-item-custom">শর্তাবলী</Link>
                            </nav>
                        </div>

                    </div>
                </div>
            </div>

            {/* Mobile Sidebar */}
            {isMobileMenuOpen && (
                <div onClick={() => setIsMobileMenuOpen(false)} style={{ position: 'fixed', top: 0, left: 0, width: '100%', height: '100%', backgroundColor: 'rgba(0,0,0,0.5)', zIndex: 10001 }}>
                    <div onClick={(e) => e.stopPropagation()} style={{ width: '300px', height: '100%', backgroundColor: '#fff', overflowY: 'auto' }}>
                        <div style={{ padding: '20px', backgroundColor: mainColor, color: '#fff', display: 'flex', justifyContent: 'space-between' }}>
                            <h5 className="mb-0 fw-bold">মেনু</h5>
                            <button className="btn text-white p-0" onClick={() => setIsMobileMenuOpen(false)}>✕</button>
                        </div>
                        <div style={{ padding: '10px 0' }}>
                            <div style={{ padding: '15px 20px', fontWeight: 'bold', color: mainColor, borderBottom: '1px solid #f0f0f0' }}>⣿ ক্যাটাগরি ব্রাউজ করুন</div>
                            {realCategories.map(cat => (
                                <div key={cat.id} className="border-bottom">
                                    <div onClick={() => toggleCategory(cat.id)} style={{ padding: '15px 20px', cursor: 'pointer', display: 'flex', justifyContent: 'space-between', fontSize: '14px' }}>
                                        <div className="d-flex align-items-center gap-2">
                                            <img src={cat.thumbnail || '/placeholder.jpg'} alt="" style={{ width: '20px', height: '20px', objectFit: 'contain' }} />
                                            <Link to={`/category/${cat.id}`} onClick={() => setIsMobileMenuOpen(false)} className="text-decoration-none text-dark">{cat.name}</Link>
                                        </div>
                                        {cat.subCategories?.length > 0 && <span>{expandedCategory === cat.id ? '▼' : '▶'}</span>}
                                    </div>
                                    {expandedCategory === cat.id && cat.subCategories?.length > 0 && (
                                        <div style={{ backgroundColor: '#fdfdfd', paddingLeft: '45px' }}>
                                            {cat.subCategories.map(sub => (
                                                <Link
                                                    key={sub.id}
                                                    to={`/subcategory/${sub.id}`}
                                                    onClick={() => setIsMobileMenuOpen(false)}
                                                    className="d-block py-2 text-decoration-none text-muted"
                                                    style={{ fontSize: '13px' }}
                                                >
                                                    {sub.name}
                                                </Link>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            ))}
                            <div style={{ padding: '15px 20px', fontWeight: 'bold', marginTop: '10px' }}>কুইক লিংক</div>
                            <Link to="/" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">হোম</Link>
                            <Link to="/products" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">সব পণ্য</Link>
                            <Link to="/cart" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">আমার কার্ট</Link>
                            <Link to="/wishlist" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">আমার উইশলিস্ট</Link>
                            <Link to="/order-tracking" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">অর্ডার ট্র্যাক</Link>
                            {localStorage.getItem('auth_token') ? (
                                <Link to="/customer/dashboard" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">ড্যাশবোর্ড</Link>
                            ) : (
                                <Link to="/customer/login" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">লগইন / রেজিস্টার</Link>
                            )}
                            <a href="/seller/login" onClick={() => setIsMobileMenuOpen(false)} className="d-block p-3 px-4 text-decoration-none text-dark border-bottom">সেলার হন</a>
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
