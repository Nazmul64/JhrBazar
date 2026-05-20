import React, { useRef, useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import axios from 'axios';
import { useSettings } from '../context/SettingsContext';

const Categories = ({ categories, loading }) => {
  const { settings } = useSettings();
  const scrollRef = useRef(null);
  const [isDown, setIsDown] = useState(false);
  const [startX, setStartX] = useState(0);
  const [scrollLeft, setScrollLeft] = useState(0);
  const [isHovered, setIsHovered] = useState(false);

  const handleMouseDown = (e) => {
    setIsDown(true);
    setStartX(e.pageX - scrollRef.current.offsetLeft);
    setScrollLeft(scrollRef.current.scrollLeft);
  };

  const handleMouseLeave = () => {
    setIsDown(false);
  };

  const handleMouseUp = () => {
    setIsDown(false);
  };

  const navigate = useNavigate();

  const handleMouseMove = (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - scrollRef.current.offsetLeft;
    const walk = (x - startX) * 2;
    scrollRef.current.scrollLeft = scrollLeft - walk;
  };

  const handleCategoryClick = (catId) => {
    // If it was just a click and not a drag, navigate
    navigate(`/category/${catId}`);
  };

  useEffect(() => {
    if (!scrollRef.current || isDown || isHovered || categories.length < 5) return;

    const speed = (settings?.category_slide_speed || 4) * 1000;

    const interval = setInterval(() => {
      const container = scrollRef.current;
      const maxScroll = container.scrollWidth - container.clientWidth;
      
      if (container.scrollLeft >= maxScroll - 5) {
        container.scrollTo({ left: 0, behavior: 'smooth' });
      } else {
        container.scrollBy({ left: 200, behavior: 'smooth' });
      }
    }, speed);

    return () => clearInterval(interval);
  }, [categories, isDown, isHovered, settings?.category_slide_speed]);

  if (loading) {
    return (
      <section className="container mb-5">
        <div className="d-flex justify-content-between align-items-center mb-4">
          <div className="d-flex align-items-center gap-2">
              <div style={{ width: '5px', height: '25px', backgroundColor: '#eee', borderRadius: '10px' }}></div>
              <div className="skeleton-text" style={{ width: '150px', height: '25px', backgroundColor: '#f0f0f0', borderRadius: '4px' }}></div>
          </div>
        </div>
        <div className="d-flex gap-3 overflow-hidden">
          {[1, 2, 3, 4, 5, 6, 7].map(i => (
            <div key={i} style={{ flex: '0 0 160px' }}>
              <div style={{ backgroundColor: '#fff', border: '1px solid #f0f0f0', borderRadius: '15px', padding: '10px', textAlign: 'center' }}>
                <div className="skeleton-box shimmer" style={{ width: '100%', height: '100px', borderRadius: '12px', backgroundColor: '#f9f9f9', marginBottom: '10px' }}></div>
                <div className="skeleton-text shimmer" style={{ width: '80%', height: '15px', backgroundColor: '#f9f9f9', margin: '0 auto', borderRadius: '4px' }}></div>
              </div>
            </div>
          ))}
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
      </section>
    );
  }

  if (!categories || categories.length === 0) {
    return null;
  }


  return (
    <section className="container mb-3">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div className="d-flex align-items-center gap-2">
            <div style={{ width: '5px', height: '25px', backgroundColor: '#57b500', borderRadius: '10px' }}></div>
            <h3 className="fw-bold mb-0">Explore Categories</h3>
        </div>
        <Link to="/products-all/all" className="btn btn-link text-decoration-none small" style={{ color: '#57b500' }}>View All ›</Link>
      </div>

      <div 
        ref={scrollRef}
        onMouseDown={handleMouseDown}
        onMouseLeave={() => { handleMouseLeave(); setIsHovered(false); }}
        onMouseEnter={() => setIsHovered(true)}
        onMouseUp={handleMouseUp}
        onMouseMove={handleMouseMove}
        style={{
          display: 'flex',
          gap: '20px',
          overflowX: 'auto',
          padding: '10px 5px',
          cursor: isDown ? 'grabbing' : 'grab',
          scrollbarWidth: 'none',
          msOverflowStyle: 'none',
          WebkitOverflowScrolling: 'touch'
        }}
        className="category-slider"
      >
        <style>{`
          .category-slider::-webkit-scrollbar {
            display: none;
          }
          .category-card-hover {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important;
          }
          .category-card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(87,181,0,0.2) !important;
            border-color: #57b500 !important;
          }
        `}</style>
        
        {categories.map(cat => {
          const cardWidth = settings?.category_img_width && !settings.category_img_width.includes('%') 
              ? `calc(${settings.category_img_width} + 20px)` 
              : '160px';
          return (
            <div key={cat.id} style={{ flex: `0 0 ${cardWidth}` }}>
              <div 
                onClick={() => handleCategoryClick(cat.id)}
                style={{
                backgroundColor: '#fff',
                border: '1px solid #f0f0f0',
                borderRadius: '15px',
                padding: '10px',
                textAlign: 'center',
                transition: 'all 0.3s',
                boxShadow: '0 4px 15px rgba(0,0,0,0.05)',
                userSelect: 'none',
                cursor: 'pointer'
              }} className="category-card-hover">
                <div style={{ 
                    width: '100%', 
                    height: settings?.category_img_height || '100px', 
                    borderRadius: 'var(--category-border-radius, 12px)', 
                    overflow: 'hidden',
                    marginBottom: '10px',
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center'
                }}>
                  <img 
                    src={cat.thumbnail} 
                    alt={cat.name} 
                    loading="lazy" 
                    style={{ 
                      width: settings?.category_img_width || '100%', 
                      height: '100%', 
                      objectFit: 'cover', 
                      pointerEvents: 'none',
                      borderRadius: 'var(--category-border-radius, 12px)'
                    }} 
                  />
                </div>
              <div style={{ 
                fontSize: '13px', 
                fontWeight: 'bold', 
                color: '#333',
                whiteSpace: 'nowrap',
                overflow: 'hidden',
                textOverflow: 'ellipsis',
                width: '100%'
              }}>
                {cat.name}
              </div>
            </div>
          </div>
        );
      })}
      </div>
    </section>
  );
};

export default Categories;
