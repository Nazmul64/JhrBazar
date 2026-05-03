import React, { useState } from 'react';

const categoriesData = [
    { 
        id: 1, name: 'Software License', image: 'https://demo.readyecommerce.app/public/assets/front-end/img/category/1.png', 
        sub: [
            { id: 11, name: 'Antivirus', child: ['Avast', 'Kaspersky', 'Norton Premium', 'ESET NOD32'] },
            { id: 12, name: 'Operating System', child: ['Windows 11 Pro', 'Windows 10 Home', 'Server 2022'] },
            { id: 13, name: 'Design Tools', child: ['Adobe Photoshop', 'Illustrator', 'CorelDraw'] }
        ] 
    },
    { 
        id: 2, name: 'Pets & Supplies', image: 'https://demo.readyecommerce.app/public/assets/front-end/img/category/2.png', 
        sub: [
            { id: 21, name: 'Pet Food', child: ['Royal Canin', 'Drools', 'Whiskas', 'Pedigree'] },
            { id: 22, name: 'Pet Grooming', child: ['Shampoo', 'Brushes', 'Nail Clippers'] },
            { id: 23, name: 'Pet Health', child: ['Vitamins', 'Tick & Flea', 'Supplements'] }
        ] 
    },
    { 
        id: 3, name: 'Electronics & Gadgets', image: 'https://demo.readyecommerce.app/public/assets/front-end/img/category/7.png', 
        sub: [
            { id: 31, name: 'Smartphones', child: ['iPhone', 'Samsung Galaxy', 'Google Pixel'] },
            { id: 32, name: 'Laptops', child: ['MacBook Pro', 'Dell XPS', 'HP Spectre'] },
            { id: 33, name: 'Accessories', child: ['AirPods', 'Smart Watches', 'Power Banks'] }
        ] 
    },
    { id: 4, name: 'Groceries', image: 'https://demo.readyecommerce.app/public/assets/front-end/img/category/4.png', sub: [] },
    { id: 5, name: 'Fashion & Cloth', image: 'https://demo.readyecommerce.app/public/assets/front-end/img/category/8.png', sub: [] },
    { id: 6, name: 'Home Appliances', image: 'https://demo.readyecommerce.app/public/assets/front-end/img/category/10.png', sub: [] },
];

const CategoryDropdown = ({ isOpen }) => {
    const [activeCatId, setActiveCatId] = useState(categoriesData[0].id);

    if (!isOpen) return null;

    const activeCategory = categoriesData.find(c => c.id === activeCatId) || categoriesData[0];

    return (
        <div style={{
            position: 'absolute', top: '100%', left: 0, width: '900px',
            backgroundColor: '#fff', boxShadow: '0 15px 50px rgba(0,0,0,0.15)',
            zIndex: 9999, display: 'flex', borderRadius: '0 0 10px 10px',
            overflow: 'hidden', border: '1px solid #eee'
        }}>
            {/* Left Sidebar: Categories List */}
            <div style={{ width: '250px', backgroundColor: '#f9f9f9', borderRight: '1px solid #eee' }}>
                {categoriesData.map(cat => (
                    <div 
                        key={cat.id}
                        onMouseEnter={() => setActiveCatId(cat.id)}
                        style={{
                            padding: '12px 20px', cursor: 'pointer',
                            backgroundColor: activeCatId === cat.id ? '#fff' : 'transparent',
                            color: activeCatId === cat.id ? '#57b500' : '#333',
                            fontWeight: activeCatId === cat.id ? 'bold' : 'normal',
                            borderLeft: activeCatId === cat.id ? '4px solid #57b500' : '4px solid transparent',
                            display: 'flex', alignItems: 'center', gap: '10px',
                            transition: 'all 0.2s'
                        }}
                    >
                        <img src={cat.image} alt="" style={{ width: '20px' }} />
                        <span style={{ fontSize: '14px' }}>{cat.name}</span>
                        {cat.sub.length > 0 && <span style={{ marginLeft: 'auto', fontSize: '10px' }}>▶</span>}
                    </div>
                ))}
            </div>

            {/* Right Panel: Sub & Child Categories with Images */}
            <div style={{ flexGrow: 1, padding: '25px', backgroundColor: '#fff', minHeight: '400px' }}>
                <h5 style={{ fontWeight: 'bold', marginBottom: '20px', color: '#333', borderBottom: '1px solid #f0f0f0', paddingBottom: '10px' }}>
                    {activeCategory.name}
                </h5>
                
                {activeCategory.sub.length > 0 ? (
                    <div className="row g-4">
                        {activeCategory.sub.map(sub => (
                            <div key={sub.id} className="col-md-4">
                                <div style={{ fontWeight: 'bold', color: '#57b500', fontSize: '15px', marginBottom: '10px' }}>
                                    {sub.name}
                                </div>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '5px' }}>
                                    {sub.child.map((child, idx) => (
                                        <div 
                                            key={idx} 
                                            style={{ 
                                                fontSize: '13px', color: '#666', cursor: 'pointer',
                                                padding: '2px 0'
                                            }}
                                            className="child-category-hover"
                                        >
                                            {child}
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div style={{ textAlign: 'center', padding: '50px', color: '#999' }}>
                         <img src={activeCategory.image} style={{ width: '80px', opacity: 0.5, marginBottom: '10px' }} />
                         <p>Explore our {activeCategory.name} collection</p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default CategoryDropdown;
