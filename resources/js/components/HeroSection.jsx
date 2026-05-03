import React, { useState, useEffect } from 'react';

const slides = [
    {
        id: 1,
        image: "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?q=80&w=1200&auto=format&fit=crop", // Smartphone lifestyle
    },
    {
        id: 2,
        image: "https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1200&auto=format&fit=crop", // Fashion/Store lifestyle
    },
    {
        id: 3,
        image: "https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?q=80&w=1200&auto=format&fit=crop", // Home/Furniture lifestyle
    }
];

const sideBanners = [
    { id: 1, image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=600&auto=format&fit=crop" }, // Red Sneaker
    { id: 2, image: "https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?q=80&w=600&auto=format&fit=crop" }  // iPhone Lifestyle
];

const HeroSection = () => {
    const [currentSlide, setCurrentSlide] = useState(0);

    useEffect(() => {
        const timer = setInterval(() => {
            setCurrentSlide((prev) => (prev === slides.length - 1 ? 0 : prev + 1));
        }, 5000);
        return () => clearInterval(timer);
    }, []);

    return (
        <section className="container my-4">
            <div className="row g-3">
                {/* Main Slider */}
                <div className="col-lg-8">
                    <div style={{
                        position: 'relative',
                        height: '420px',
                        borderRadius: '20px',
                        overflow: 'hidden',
                        boxShadow: '0 10px 30px rgba(0,0,0,0.1)',
                        transition: 'all 0.8s ease-in-out'
                    }}>
                        <div style={{
                            height: '100%',
                            width: '100%',
                            backgroundImage: `url(${slides[currentSlide].image})`,
                            backgroundSize: 'cover',
                            backgroundPosition: 'center',
                            transition: 'background-image 1s ease-in-out'
                        }}></div>

                        {/* Dot Pagination */}
                        <div style={{ position: 'absolute', bottom: '25px', left: '50%', transform: 'translateX(-50%)', display: 'flex', gap: '12px' }}>
                            {slides.map((_, idx) => (
                                <div 
                                    key={idx}
                                    onClick={() => setCurrentSlide(idx)}
                                    style={{
                                        width: idx === currentSlide ? '35px' : '12px',
                                        height: '12px',
                                        borderRadius: '6px',
                                        backgroundColor: '#fff',
                                        cursor: 'pointer',
                                        transition: 'all 0.3s',
                                        boxShadow: '0 2px 5px rgba(0,0,0,0.3)'
                                    }}
                                />
                            ))}
                        </div>
                    </div>
                </div>

                {/* Side Banners */}
                <div className="col-lg-4">
                    <div className="d-flex flex-column gap-3 h-100">
                        {sideBanners.map((banner, idx) => (
                            <div key={banner.id} style={{
                                flex: 1,
                                borderRadius: '20px',
                                overflow: 'hidden',
                                backgroundImage: `url(${banner.image})`,
                                backgroundSize: 'cover',
                                backgroundPosition: 'center',
                                position: 'relative',
                                minHeight: '202px',
                                boxShadow: '0 8px 25px rgba(0,0,0,0.08)',
                                cursor: 'pointer',
                                transition: 'transform 0.3s'
                            }} className="banner-hover">
                            </div>
                        ))}
                    </div>
                </div>
            </div>
            <style>{`
                .banner-hover:hover {
                    transform: scale(1.02);
                }
            `}</style>
        </section>
    );
};

export default HeroSection;
