import React from 'react';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';

const allProducts = [
    { id: 1, title: "Sticky Notes - Colorful 100 Sheets", price: 319.00, oldPrice: 333.00, discount: 4, sold: 2, rating: '0.0', image: "https://images.unsplash.com/photo-1586075010633-2442646776b7?q=80&w=300&auto=format&fit=crop" },
    { id: 2, title: "Mutton Meat Fresh - 1kg Premium", price: 88.20, oldPrice: 93.30, discount: 5, sold: 1, rating: '0.0', image: "https://images.unsplash.com/photo-1603048588665-791ca8aea617?q=80&w=300&auto=format&fit=crop" },
    { id: 3, title: "Shuttlecock - Professional Feather (Pack of 12)", price: 105.00, sold: 3, rating: '0.0', image: "https://images.unsplash.com/photo-1613918108466-292b78a8ef95?q=80&w=300&auto=format&fit=crop" },
    { id: 4, title: "Football - Official Size 5 High Performance", price: 105.00, sold: 2, rating: '0.0', image: "https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=300&auto=format&fit=crop" },
    { id: 5, title: "Classic Lipstick Colors of All-Time Red", price: 15.30, sold: 1, rating: '0.0', image: "https://images.unsplash.com/photo-1586495764447-c54d95df6ba4?q=80&w=300&auto=format&fit=crop" },
    { id: 6, title: "SHEGLAM Matte Allure Liquid Lipstick", price: 101624.00, sold: 3, rating: '0.0', image: "https://images.unsplash.com/photo-1596462502278-27bfdc4033c8?q=80&w=300&auto=format&fit=crop" },
    { id: 7, title: "Nudes Sheer Lipstick - Natural Look", price: 936.00, sold: 0, rating: '0.0', image: "https://images.unsplash.com/photo-1617389082390-410068595a81?q=80&w=300&auto=format&fit=crop" },
    { id: 8, title: "Freeman Beauty Korean Cica Soothing Mask", price: 60.00, sold: 14, rating: '0.0', image: "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?q=80&w=300&auto=format&fit=crop" },
    { id: 9, title: "YC Black Mask With Bamboo Charcoal", price: 8.20, sold: 1, rating: '0.0', image: "https://images.unsplash.com/photo-1556228720-195a672e8a03?q=80&w=300&auto=format&fit=crop" },
    { id: 10, title: "Hyaluronic Acid Hydrating Facial Sheet Mask", price: 250.00, sold: 2, rating: '0.0', image: "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?q=80&w=300&auto=format&fit=crop" },
    { id: 11, title: "Professional Makeup Kit Boxes Full Set", price: 500.00, sold: 1, rating: '0.0', image: "https://images.unsplash.com/photo-1522338223053-5d9c32835574?q=80&w=300&auto=format&fit=crop" },
    { id: 12, title: "Classic Makeup Box - Luxury Collection", price: 103.00, sold: 0, rating: '0.0', image: "https://images.unsplash.com/photo-1512496011212-72d20b5a3961?q=80&w=300&auto=format&fit=crop" }
];

const Products = () => {
    const mainColor = '#57b500';

    return (
        <MasterLayout>
            <div className="container py-4">
                {/* Product Header (Matches Screenshot) */}
                <div className="bg-white p-3 shadow-sm mb-4 d-flex justify-content-between align-items-center" style={{ borderRadius: '10px' }}>
                    <div className="d-flex align-items-center gap-3">
                        <button className="btn btn-sm btn-light rounded-pill px-3" style={{ fontSize: '12px' }}>← Back</button>
                        <div className="small">
                            <span className="text-danger fw-bold">"all"</span> 
                            <span className="text-muted ms-1">154 items found</span>
                        </div>
                    </div>
                    <button className="btn btn-sm btn-light rounded px-3 d-flex align-items-center gap-2" style={{ backgroundColor: '#f0f2f5', border: 'none', color: '#555' }}>
                        <span>🔍</span> Filter
                    </button>
                </div>

                {/* Product Grid */}
                <div className="row g-2 g-md-4">
                    {allProducts.map(product => (
                        <div key={product.id} className="col-6 col-md-4 col-lg-3 col-xl-2">
                            <ProductCard product={product} />
                        </div>
                    ))}
                </div>

                {/* Pagination (Matches Screenshot) */}
                <div className="d-flex justify-content-between align-items-center mt-5 flex-wrap gap-3">
                    <div className="small text-muted">Showing 1 to 12 of 154 results</div>
                    <nav>
                        <ul className="pagination mb-0 gap-2">
                            <li className="page-item"><button className="page-link rounded-3 border-0 active" style={{ backgroundColor: '#e91e63' }}>1</button></li>
                            <li className="page-item"><button className="page-link rounded-3 border bg-white text-dark">2</button></li>
                            <li className="page-item"><button className="page-link rounded-3 border bg-white text-dark">3</button></li>
                            <li className="page-item"><span className="p-2">...</span></li>
                            <li className="page-item"><button className="page-link rounded-3 border bg-white text-dark">13</button></li>
                            <li className="page-item"><button className="page-link rounded-3 border bg-white text-dark">→</button></li>
                        </ul>
                    </nav>
                </div>
            </div>

            <style>{`
                .page-link { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px; }
                .page-link.active { background-color: #ff4d4d !important; color: #fff !important; }
            `}</style>
        </MasterLayout>
    );
};

export default Products;
