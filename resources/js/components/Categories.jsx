import React, { useRef, useState } from 'react';

const categories = [
  { id: 1, name: 'Beauty & Care', icon: 'https://images.unsplash.com/photo-1596462502278-27bfdc4033c8?q=80&w=300&auto=format&fit=crop' },
  { id: 2, name: 'Sports & Fitness', icon: 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=300&auto=format&fit=crop' },
  { id: 3, name: 'Gadgets & Tech', icon: 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=300&auto=format&fit=crop' },
  { id: 4, name: 'Fashion & Cloth', icon: 'https://images.unsplash.com/photo-1445205170230-053b83016050?q=80&w=300&auto=format&fit=crop' },
  { id: 5, name: 'Books & Office', icon: 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?q=80&w=300&auto=format&fit=crop' },
  { id: 6, name: 'Groceries', icon: 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=300&auto=format&fit=crop' },
  { id: 7, name: 'Toys & Kids', icon: 'https://images.unsplash.com/photo-1558060370-d644479cb6f7?q=80&w=300&auto=format&fit=crop' },
  { id: 8, name: 'Home Appliances', icon: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=300&auto=format&fit=crop' },
  { id: 9, name: 'Jewelry', icon: 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?q=80&w=300&auto=format&fit=crop' },
  { id: 10, name: 'Automotive', icon: 'https://images.unsplash.com/photo-1485291571170-d4be0d43e75c?q=80&w=300&auto=format&fit=crop' },
];

const Categories = () => {
  const scrollRef = useRef(null);
  const [isDown, setIsDown] = useState(false);
  const [startX, setStartX] = useState(0);
  const [scrollLeft, setScrollLeft] = useState(0);

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

  const handleMouseMove = (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - scrollRef.current.offsetLeft;
    const walk = (x - startX) * 2;
    scrollRef.current.scrollLeft = scrollLeft - walk;
  };

  return (
    <section className="container mb-5">
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
        onMouseLeave={handleMouseLeave}
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
            <div style={{
              backgroundColor: '#fff',
              border: '1px solid #f0f0f0',
              borderRadius: '15px',
              padding: '10px',
              textAlign: 'center',
              transition: 'all 0.3s',
              boxShadow: '0 4px 15px rgba(0,0,0,0.05)',
              userSelect: 'none'
            }} className="category-card-hover">
              <div style={{ 
                  width: '100%', 
                  height: '100px', 
                  borderRadius: '12px', 
                  overflow: 'hidden',
                  marginBottom: '10px'
              }}>
                <img src={cat.icon} alt={cat.name} style={{ width: '100%', height: '100%', objectFit: 'cover', pointerEvents: 'none' }} />
              </div>
              <div style={{ fontSize: '13px', fontWeight: 'bold', color: '#333' }}>{cat.name}</div>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
};

export default Categories;
