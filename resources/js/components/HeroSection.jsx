import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Link } from 'react-router-dom';
import { useSettings } from '../context/SettingsContext';

const HeroSection = ({ banners: initialBanners, categories: initialCategories, loading }) => {
    const { settings } = useSettings();
    const behavior = settings?.sidebar_behavior || 'fixed';
    
    const [slides, setSlides] = useState([]);
    const [categories, setCategories] = useState([]);
    const [currentSlide, setCurrentSlide] = useState(0);
    const [activeCatId, setActiveCatId] = useState(null);
    const [isMenuOpen, setIsMenuOpen] = useState(behavior === 'fixed');

    useEffect(() => {
        setIsMenuOpen(behavior === 'fixed');
    }, [behavior]);

    useEffect(() => {
        if (initialBanners && initialBanners.length > 0) {
            setSlides(initialBanners);
        }
        if (initialCategories) {
            setCategories(initialCategories);
        }
    }, [initialBanners, initialCategories]);

    useEffect(() => {
        if (slides.length > 1) {
            const speed = (settings?.slider_speed || 5) * 1000;
            const timer = setInterval(() => {
                setCurrentSlide((prev) => (prev === slides.length - 1 ? 0 : prev + 1));
            }, speed);
            return () => clearInterval(timer);
        }
    }, [slides.length, settings?.slider_speed]);

    if (loading) {
        const hasCategories = !window.initialHomeData || (window.initialHomeData?.data?.categories?.length > 0) || (initialCategories?.length > 0);
        return (
            <div className="container my-4">
                <div className="row g-3">
                    {hasCategories && (
                        <div className="col-lg-3 d-none d-lg-block">
                            <div className="rounded-4 w-100 shimmer" style={{ height: '420px', backgroundColor: '#f0f0f0' }}></div>
                        </div>
                    )}
                    <div className={hasCategories ? "col-lg-9 col-md-12" : "col-lg-12 col-md-12"}>
                        <div className="rounded-4 w-100 shimmer" style={{ height: '420px', backgroundColor: '#f0f0f0' }}></div>
                    </div>
                </div>
                <style>{`
                    .shimmer {
                        background: linear-gradient(90deg, #f9f9f9 25%, #f0f0f0 50%, #f9f9f9 75%);
                        background-size: 200% 100%;
                        animation: shimmer 1.5s infinite;
                    }
                    @keyframes shimmer {
                        0% { background-position: -200% 0; }
                        100% { background-position: 200% 0; }
                    }
                `}</style>
            </div>
        );
    }

    const activeCategory = categories.find(c => c.id === activeCatId);

    return (
        <section className="container mb-3 mt-4">
            <div className="row g-3">
                {/* Column 1: Vertical Categories Sidebar */}
                {settings && behavior === 'fixed' && categories.length > 0 && (
                    <div className="col-lg-3 d-none d-lg-block">
                        <div 
                            className="bg-white shadow-sm border overflow-hidden hero-sidebar-container" 
                            style={{ 
                                borderRadius: '12px', 
                                position: 'relative',
                                zIndex: 1000
                            }}
                        >
                            <div className="p-3 text-white fw-bold d-flex align-items-center gap-2" style={{ backgroundColor: '#2c2c2c' }}>
                                <span style={{ fontSize: '18px' }}>☰</span> সব ক্যাটাগরি
                            </div>
                            
                            <div className="py-1" onMouseLeave={() => setActiveCatId(null)}>
                                {categories.map(cat => (
                                    <div 
                                        key={cat.id} 
                                        onMouseEnter={() => setActiveCatId(cat.id)}
                                        className="px-3 py-2 border-bottom-0 category-sidebar-item"
                                        style={{ 
                                            cursor: 'pointer',
                                            fontSize: '14px',
                                            color: activeCatId === cat.id ? 'var(--button-color, #57b500)' : '#444',
                                            backgroundColor: activeCatId === cat.id ? '#f8f9fa' : 'transparent',
                                            transition: 'all 0.2s',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'space-between'
                                        }}
                                    >
                                        <div className="d-flex align-items-center gap-2">
                                            <img src={cat.thumbnail || '/placeholder.jpg'} alt="" loading="lazy" style={{ width: '20px', height: '20px', objectFit: 'contain' }} />
                                            <Link to={`/category/${cat.id}`} style={{ color: 'inherit', textDecoration: 'none' }}>{cat.name}</Link>
                                        </div>
                                        {(cat.sub_categories?.length > 0 || cat.subCategories?.length > 0) && <span style={{ fontSize: '10px' }}>▶</span>}
                                    </div>
                                ))}

                                {/* Subcategories Panel (Appears on Hover) */}
                                {activeCatId && (activeCategory?.sub_categories?.length > 0 || activeCategory?.subCategories?.length > 0) && (
                                    <div className="position-absolute bg-white shadow-lg border" style={{ 
                                        top: 0, 
                                        left: '100%', 
                                        width: '280px', 
                                        height: '100%',
                                        zIndex: 10,
                                        borderRadius: '0 12px 12px 0',
                                        padding: '20px',
                                        overflowY: 'auto'
                                    }}>
                                        <h6 className="fw-bold mb-3 border-bottom pb-2" style={{ color: 'var(--button-color, #57b500)' }}>{activeCategory.name}</h6>
                                        <div className="d-flex flex-column gap-2">
                                            {(activeCategory.sub_categories || activeCategory.subCategories || []).map(sub => (
                                                <Link 
                                                    key={sub.id} 
                                                    to={`/subcategory/${sub.id}`} 
                                                    className="text-decoration-none text-muted small hover-primary d-flex align-items-center gap-2 py-1"
                                                    style={{ transition: 'color 0.2s' }}
                                                >
                                                    <img src={sub.thumbnail || '/placeholder.jpg'} alt="" style={{ width: '16px', height: '16px', objectFit: 'contain' }} />
                                                    <span>{sub.name}</span>
                                                </Link>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                )}

                {/* Column 2: Main Slider */}
                <div className={(behavior === 'fixed' && categories.length > 0) ? 'col-lg-9 col-md-12' : 'col-lg-12 col-md-12'}>
                    <div className="hero-slider-container" style={{
                        position: 'relative',
                        borderRadius: '12px',
                        overflow: 'hidden',
                        boxShadow: '0 5px 15px rgba(0,0,0,0.05)',
                        backgroundColor: '#f8f9fa'
                    }}>
                        {slides.length > 0 ? (
                            <img 
                                key={currentSlide}
                                src={slides[currentSlide]?.image}
                                alt="Banner"
                                className="hero-slide-image"
                                style={{
                                    height: '100%',
                                    width: '100%',
                                    objectFit: 'fill',
                                    animation: 'fadeInSlide 0.8s ease-in-out'
                                }}
                            />
                        ) : (
                            <div className="h-100 w-100 d-flex align-items-center justify-content-center text-center p-4" style={{ 
                                background: 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)',
                                position: 'relative',
                                overflow: 'hidden'
                            }}>
                                {/* Decorative Circles */}
                                <div style={{ position: 'absolute', top: '-50px', right: '-50px', width: '200px', height: '200px', borderRadius: '50%', backgroundColor: 'rgba(87, 181, 0, 0.05)' }}></div>
                                <div style={{ position: 'absolute', bottom: '-20px', left: '-20px', width: '100px', height: '100px', borderRadius: '50%', backgroundColor: 'rgba(0, 0, 0, 0.02)' }}></div>
                                
                                <div style={{ position: 'relative', zIndex: 2 }}>
                                    <h2 className="fw-bold mb-3" style={{ color: '#333' }}>Welcome to <span style={{ color: 'var(--button-color, #57b500)' }}>{settings?.website_name || ''}</span></h2>
                                    <p className="text-muted mb-4">Discover premium products at the best prices. Enjoy 100% secure shopping experience.</p>
                                    <Link to="/products-all/all" className="btn text-white px-4 py-2" style={{ backgroundColor: 'var(--button-color, #57b500)', borderRadius: '30px' }}>
                                        Shop Now
                                    </Link>
                                </div>
                            </div>
                        )}

                        {/* Manual Navigation Arrows */}
                        {slides.length > 1 && (
                            <>
                                <button 
                                    onClick={() => setCurrentSlide(prev => prev === 0 ? slides.length - 1 : prev - 1)}
                                    className="slider-nav-btn prev"
                                    style={{ left: '15px' }}
                                >
                                    ‹
                                </button>
                                <button 
                                    onClick={() => setCurrentSlide(prev => prev === slides.length - 1 ? 0 : prev + 1)}
                                    className="slider-nav-btn next"
                                    style={{ right: '15px' }}
                                >
                                    ›
                                </button>
                            </>
                        )}

                        {/* Dot Pagination */}
                        {slides.length > 1 && (
                            <div style={{ position: 'absolute', bottom: '20px', left: '50%', transform: 'translateX(-50%)', display: 'flex', gap: '8px', zIndex: 5 }}>
                                {slides.map((_, idx) => (
                                    <div
                                        key={idx}
                                        onClick={() => setCurrentSlide(idx)}
                                        style={{
                                            width: idx === currentSlide ? '30px' : '10px',
                                            height: '6px',
                                            borderRadius: '3px',
                                            backgroundColor: idx === currentSlide ? 'var(--button-color, #57b500)' : '#fff',
                                            cursor: 'pointer',
                                            transition: 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)',
                                            boxShadow: '0 2px 5px rgba(0,0,0,0.2)',
                                            opacity: idx === currentSlide ? 1 : 0.5
                                        }}
                                    />
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
            <style>{`
                .category-sidebar-item:hover {
                    padding-left: 20px !important;
                }
                .hover-primary:hover {
                    color: var(--button-color, #57b500) !important;
                }
                
                .slider-nav-btn {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.2);
                    backdrop-filter: blur(5px);
                    border: 1px solid rgba(255, 255, 255, 0.3);
                    color: #fff;
                    font-size: 24px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s;
                    z-index: 5;
                    outline: none;
                }
                .slider-nav-btn:hover {
                    background: rgba(255, 255, 255, 0.4);
                    color: #000;
                }
                
                @keyframes fadeInSlide {
                    0% { opacity: 0; transform: scale(1.05); filter: blur(5px); }
                    100% { opacity: 1; transform: scale(1); filter: blur(0); }
                }
                
                /* Dynamic Slider Height */
                @media (max-width: 768px) {
                    .hero-slider-container { height: ${settings?.slider_height_mobile || '200px'} !important; }
                    .hero-sidebar-container { height: auto !important; }
                    .slider-nav-btn { width: 30px; height: 30px; font-size: 18px; }
                }
                @media (min-width: 769px) {
                    .hero-slider-container { height: ${settings?.slider_height || '420px'} !important; }
                    .hero-sidebar-container { height: ${settings?.slider_height || '420px'} !important; }
                }
            `}</style>
        </section>
    );
};

export default HeroSection;
