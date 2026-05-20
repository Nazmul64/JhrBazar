import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { useSettings } from '../context/SettingsContext';

const CategoryDropdown = ({ isOpen }) => {
    const { categories, loading } = useSettings();
    const [activeCatId, setActiveCatId] = useState(null);
    const activeCategory = categories.find(c => c.id === activeCatId);

    const formatImagePath = (path) => {
        if (!path) return '/placeholder.jpg';
        if (path.startsWith('http')) return path;
        return path.startsWith('/') ? path : '/' + path;
    };

    if (!isOpen) return null;

    return (
        <div 
            onMouseLeave={() => setActiveCatId(null)}
            style={{
                position: 'absolute', top: '100%', left: 0, width: '250px',
                backgroundColor: '#fff', boxShadow: '0 10px 30px rgba(0,0,0,0.15)',
                zIndex: 11000, borderRadius: '0 0 10px 10px',
                overflow: 'visible', border: '1px solid #eee',
                padding: '10px 0'
            }}
        >
            {loading ? (
                <div className="p-4 text-center">
                    <div className="spinner-border spinner-border-sm text-success"></div>
                </div>
            ) : (
                categories.length > 0 ? (
                    <>
                        {categories.map(cat => (
                            <div 
                                key={cat.id}
                                onMouseEnter={() => setActiveCatId(cat.id)}
                                style={{ position: 'relative' }}
                            >
                                <Link 
                                    to={`/category/${cat.id}`}
                                    style={{
                                        display: 'flex',
                                        alignItems: 'center',
                                        gap: '12px',
                                        padding: '10px 20px',
                                        textDecoration: 'none',
                                        color: activeCatId === cat.id ? 'var(--button-color, #57b500)' : '#333',
                                        fontSize: '14px',
                                        transition: 'all 0.2s',
                                        backgroundColor: activeCatId === cat.id ? '#f8f9fa' : 'transparent',
                                        borderLeft: activeCatId === cat.id ? '3px solid var(--button-color, #57b500)' : '3px solid transparent'
                                    }}
                                    className="category-dropdown-item"
                                >
                                    <img 
                                        src={formatImagePath(cat.thumbnail)} 
                                        alt="" 
                                        style={{ width: '20px', height: '20px', objectFit: 'contain' }} 
                                    />
                                    <span style={{ flexGrow: 1 }}>{cat.name}</span>
                                    {(cat.sub_categories?.length > 0 || cat.subCategories?.length > 0) && <span style={{ fontSize: '10px' }}>▶</span>}
                                </Link>
                            </div>
                        ))}

                        {/* Subcategories Side Panel */}
                        {activeCatId && (activeCategory?.sub_categories?.length > 0 || activeCategory?.subCategories?.length > 0) && (
                            <div style={{
                                position: 'absolute', top: '-1px', left: '100%', width: '250px',
                                backgroundColor: '#fff', boxShadow: '15px 10px 30px rgba(0,0,0,0.1)',
                                border: '1px solid #eee', borderRadius: '0 10px 10px 0',
                                padding: '20px', minHeight: '100%', zIndex: 11001
                            }}>
                                <h6 className="fw-bold mb-3 border-bottom pb-2" style={{ color: 'var(--button-color, #57b500)', fontSize: '14px' }}>
                                    {activeCategory.name}
                                </h6>
                                <div className="d-flex flex-column gap-2">
                                    {(activeCategory.sub_categories || activeCategory.subCategories || []).map(sub => (
                                        <Link 
                                            key={sub.id} 
                                            to={`/subcategory/${sub.id}`} 
                                            className="text-decoration-none text-muted d-flex align-items-center gap-2"
                                            style={{ fontSize: '13px', transition: 'color 0.2s', padding: '5px 0' }}
                                            onMouseEnter={(e) => e.target.style.color = 'var(--button-color, #57b500)'}
                                            onMouseLeave={(e) => e.target.style.color = 'inherit'}
                                        >
                                            <img 
                                                src={formatImagePath(sub.thumbnail)} 
                                                alt="" 
                                                style={{ width: '18px', height: '18px', objectFit: 'contain', borderRadius: '3px' }} 
                                            />
                                            <span>{sub.name}</span>
                                        </Link>
                                    ))}
                                </div>
                            </div>
                        )}
                    </>
                ) : (
                    <div className="p-3 text-center text-muted small">No categories found</div>
                )
            )}
            <style>{`
                .category-dropdown-item:hover {
                    background-color: #f8f9fa;
                    color: var(--button-color, #57b500) !important;
                    border-left: 3px solid var(--button-color, #57b500);
                }
            `}</style>
        </div>
    );
};

export default CategoryDropdown;
