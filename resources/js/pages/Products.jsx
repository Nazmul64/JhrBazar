import React, { useState, useEffect } from 'react';
import axios from 'axios';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';
import { useSettings } from '../context/SettingsContext';

const Products = () => {
    const { settings } = useSettings();
    const mainColor = settings?.primary_color || '#57b500';
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchProducts = async () => {
            try {
                const res = await axios.get('/api/all-products?limit=all');
                if (res.data.success) {
                    setProducts(res.data.data);
                }
            } catch (error) {
                console.error("Error fetching products:", error);
            } finally {
                setLoading(false);
            }
        };
        fetchProducts();
        window.scrollTo(0, 0);
    }, []);

    let mobileCol = 6;
    if (settings?.products_per_row_mobile) {
        mobileCol = Math.max(1, Math.floor(12 / parseInt(settings.products_per_row_mobile)));
    }
    
    let desktopCol = 2;
    let customDesktopClass = '';
    if (settings?.products_per_row_desktop) {
        const perRow = parseInt(settings.products_per_row_desktop);
        if ([1, 2, 3, 4, 6, 12].includes(perRow)) {
            desktopCol = 12 / perRow;
        } else {
            customDesktopClass = `custom-desktop-col-${perRow}`;
            desktopCol = 2; // fallback
        }
    }
    const finalColClass = `col-${mobileCol} col-md-4 col-lg-${desktopCol} ${customDesktopClass}`;

    return (
        <MasterLayout>
            <div className="container py-4">
                {/* Product Header */}
                <div className="bg-white p-3 shadow-sm mb-4 d-flex justify-content-between align-items-center" style={{ borderRadius: '10px' }}>
                    <div className="d-flex align-items-center gap-3">
                        <button onClick={() => window.history.back()} className="btn btn-sm btn-light rounded-pill px-3" style={{ fontSize: '12px' }}>← Back</button>
                        <div className="small">
                            <span className="text-success fw-bold">"all"</span> 
                            <span className="text-muted ms-1">{products.length} items found</span>
                        </div>
                    </div>
                    <button className="btn btn-sm btn-light rounded px-3 d-flex align-items-center gap-2" style={{ backgroundColor: '#f0f2f5', border: 'none', color: '#555' }}>
                        <span>🔍</span> Filter
                    </button>
                </div>

                {loading ? (
                    <div className="py-5" style={{ minHeight: '60vh' }}></div>
                ) : (
                    <>
                        {/* Product Grid */}
                        <div className="row g-2 g-md-4">
                            {products.length > 0 ? (
                                products.map(product => (
                                    <div key={product.uid} className={finalColClass}>
                                        <ProductCard product={product} />
                                    </div>
                                ))
                            ) : (
                                <div className="col-12 text-center py-5 text-muted">
                                    No products found.
                                </div>
                            )}
                        </div>

                        {/* Pagination - Placeholder for now as we load all */}
                        {products.length > 0 && (
                            <div className="d-flex justify-content-center mt-5">
                                <nav>
                                    <ul className="pagination mb-0 gap-2">
                                        <li className="page-item"><button className="page-link rounded-3 border-0 active" style={{ backgroundColor: mainColor, color: '#fff' }}>1</button></li>
                                    </ul>
                                </nav>
                            </div>
                        )}
                    </>
                )}
            </div>

            <style>{`
                .page-link { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px; }
                @media (min-width: 992px) {
                    .custom-desktop-col-5 { width: 20%; flex: 0 0 20%; }
                    .custom-desktop-col-8 { width: 12.5%; flex: 0 0 12.5%; }
                }
            `}</style>
        </MasterLayout>
    );
};

export default Products;
