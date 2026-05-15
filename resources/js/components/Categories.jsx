import React, { useRef, useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const Categories = ({ categories, loading }) => {
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

    const interval = setInterval(() => {
      const container = scrollRef.current;
      const maxScroll = container.scrollWidth - container.clientWidth;
      
      if (container.scrollLeft >= maxScroll - 5) {
        container.scrollTo({ left: 0, behavior: 'smooth' });
      } else {
        container.scrollBy({ left: 200, behavior: 'smooth' });
      }
    }, 4000);

    return () => clearInterval(interval);
  }, [categories, isDown, isHovered]);

  if (loading || !categories || categories.length === 0) return null;


  return (
    <section className="container mb-3">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div className="d-flex align-items-center gap-2">
            <div style={{ width: '5px', height: '25px', backgroundColor: '#57b500', borderRadius: '10px' }}></div>
            <h3 className="fw-bold mb-0">Explore Categories</h3>
        </div>
        <button className="btn btn-link text-decoration-none small" style={{ color: '#57b500' }}>View All ›</button>
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
        `}</style>
        
        {categories.map(cat => (
          <div key={cat.id} style={{ flex: '0 0 160px' }}>
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
                  height: '100px', 
                  borderRadius: '12px', 
                  overflow: 'hidden',
                  marginBottom: '10px'
              }}>
                <img src={cat.thumbnail} alt={cat.name} loading="lazy" style={{ width: '100%', height: '100%', objectFit: 'cover', pointerEvents: 'none' }} />
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
        ))}
      </div>
    </section>
  );
};

export default Categories;
