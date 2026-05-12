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
        if (initialBanners) {
            const mainBanners = initialBanners.filter(b => b.for_own_shop);
            setSlides(mainBanners.length > 0 ? mainBanners : initialBanners.slice(0, 1));
        }
        if (initialCategories) {
            setCategories(initialCategories);
        }
    }, [initialBanners, initialCategories]);

    useEffect(() => {
        if (slides.length > 1) {
            const timer = setInterval(() => {
                setCurrentSlide((prev) => (prev === slides.length - 1 ? 0 : prev + 1));
            }, 5000);
            return () => clearInterval(timer);
        }
    }, [slides]);

    if (loading) {
        return (
            <div className="container my-4">
                <div className="row g-3">
                    <div className="col-lg-3 d-none d-lg-block"><div className="bg-light rounded-4 w-100 shadow-sm" style={{ height: '420px' }}></div></div>
                    <div className="col-lg-9 col-md-12"><div className="bg-light rounded-4 w-100 shadow-sm" style={{ height: '420px' }}></div></div>
                </div>
            </div>
        );
    }

    const activeCategory = categories.find(c => c.id === activeCatId);

    return (
        <section className="container my-4">
            <div className="row g-3">
                {/* Column 1: Vertical Categories Sidebar */}
                {settings && behavior === 'fixed' && (
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
                                        {cat.subCategories?.length > 0 && <span style={{ fontSize: '10px' }}>▶</span>}
                                    </div>
                                ))}

                                {/* Subcategories Panel (Appears on Hover) */}
                                {activeCatId && activeCategory?.subCategories?.length > 0 && (
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
                                            {activeCategory.subCategories.map(sub => (
                                                <Link 
                                                    key={sub.id} 
                                                    to={`/subcategory/${sub.id}`} 
                                                    className="text-decoration-none text-muted small hover-primary"
                                                    style={{ transition: 'color 0.2s' }}
                                                >
                                                    {sub.name}
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
                <div className={behavior === 'fixed' ? 'col-lg-9 col-md-12' : 'col-lg-12 col-md-12'}>
                    <div className="hero-slider-container" style={{
                        position: 'relative',
                        borderRadius: '12px',
                        overflow: 'hidden',
                        boxShadow: '0 5px 15px rgba(0,0,0,0.05)',
                        backgroundColor: '#f8f9fa'
                    }}>
                        {slides.length > 0 ? (
                            <div style={{
                                height: '100%',
                                width: '100%',
                                backgroundImage: `url(${slides[currentSlide]?.image})`,
                                backgroundSize: '100% 100%',
                                backgroundPosition: 'center',
                                transition: 'background-image 0.8s ease-in-out'
                            }}></div>
                        ) : (
                            <div className="h-100 w-100 bg-light" style={{ opacity: 0.5 }}></div>
                        )}

                        {/* Dot Pagination */}
                        {slides.length > 1 && (
                            <div style={{ position: 'absolute', bottom: '20px', left: '50%', transform: 'translateX(-50%)', display: 'flex', gap: '8px' }}>
                                {slides.map((_, idx) => (
                                    <div
                                        key={idx}
                                        onClick={() => setCurrentSlide(idx)}
                                        style={{
                                            width: idx === currentSlide ? '25px' : '8px',
                                            height: '8px',
                                            borderRadius: '4px',
                                            backgroundColor: '#fff',
                                            cursor: 'pointer',
                                            transition: 'all 0.3s',
                                            boxShadow: '0 2px 5px rgba(0,0,0,0.2)',
                                            opacity: idx === currentSlide ? 1 : 0.6
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
                
                /* Dynamic Slider Height */
                @media (max-width: 768px) {
                    .hero-slider-container { height: ${settings?.slider_height_mobile || '200px'} !important; }
                    .hero-sidebar-container { height: auto !important; }
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
