import React from 'react';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';

const shopProducts = [
    { id: 1, title: "Sony A6400 Mirrorless Camera", price: 1800.00, oldPrice: 2000.00, discount: 10, sold: 6, image: "https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=500&auto=format&fit=crop" },
    { id: 2, title: "Samsung Galaxy S22 Ultra 5G", price: 950.00, oldPrice: 1050.00, discount: 9, sold: 1, image: "https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?q=80&w=500&auto=format&fit=crop" },
    { id: 3, title: "Microsoft Surface Laptop 4", price: 1600.00, oldPrice: 1700.00, discount: 6, sold: 1, image: "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=500&auto=format&fit=crop" },
    { id: 4, title: "Microlab M100BT Bluetooth Speaker", price: 35.00, discount: 0, sold: 0, image: "https://images.unsplash.com/photo-1608156639585-34a0a56ee6c9?q=80&w=500&auto=format&fit=crop" },
    { id: 5, title: "iPhone 14 Pro Max 128GB Midnight", price: 1150.00, discount: 0, sold: 1, image: "https://images.unsplash.com/photo-1663499482523-1c0c1bae4ce1?q=80&w=500&auto=format&fit=crop" },
    { id: 6, title: "Canon EOS Rebel T3i Digital SLR", price: 375.00, oldPrice: 380.00, discount: 1, sold: 1, image: "https://images.unsplash.com/photo-1512790182412-b19e6d62bc39?q=80&w=500&auto=format&fit=crop" }
];

const ShopDetails = () => {
    const mainColor = '#57b500';

    return (
        <MasterLayout>
            {/* Shop Header Section */}
            <div style={{ backgroundColor: '#fff' }}>
                {/* Banner */}
                <div style={{ height: '250px', overflow: 'hidden' }}>
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1500&auto=format&fit=crop" alt="Shop Banner" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                </div>

                {/* Profile Bar */}
                <div className="container py-4">
                    <div className="row align-items-center">
                        <div className="col-md-8 d-flex align-items-center gap-4">
                            <img src="https://images.unsplash.com/photo-1614850523296-d8c1af93d400?q=80&w=150&auto=format&fit=crop" alt="Logo" style={{ width: '100px', height: '100px', borderRadius: '50%', border: '4px solid #f5f5f5' }} />
                            <div>
                                <h3 className="fw-bold mb-1">JHR Tech World <span className="badge bg-info small" style={{ fontSize: '10px' }}>ONLINE</span></h3>
                                <p className="text-muted small mb-1">14+ Items</p>
                                <p className="text-muted small d-none d-md-block" style={{ maxWidth: '500px' }}>Welcome to JHR Tech World, your go-to electronics shop! We offer the latest gadgets and accessories to simplify your life.</p>
                            </div>
                        </div>
                        <div className="col-md-4 text-md-end">
                            <div className="d-flex flex-column align-items-md-end">
                                <div className="text-warning mb-1">⭐⭐⭐⭐⭐ <span className="text-dark fw-bold">5.0 (0)</span></div>
                                <button className="btn btn-outline-secondary btn-sm rounded-circle">💬</button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Tabs & Search */}
                <div className="border-top border-bottom">
                    <div className="container d-flex justify-content-between align-items-center py-2">
                        <div className="d-flex gap-3">
                            <button className="btn text-white px-4" style={{ backgroundColor: mainColor, borderRadius: '20px' }}>All Products</button>
                            <button className="btn btn-link text-decoration-none text-dark">Reviews</button>
                        </div>
                        <div className="d-none d-md-block">
                            <div className="input-group" style={{ width: '300px' }}>
                                <input type="text" className="form-control rounded-pill-start border-end-0 bg-light" placeholder="Search product" />
                                <button className="btn bg-light border-start-0 rounded-pill-end">🔍</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Shop Content */}
            <div className="container py-5">
                {/* Promo Banners */}
                <div className="row g-4 mb-5">
                    <div className="col-md-6">
                        <div style={{ borderRadius: '15px', overflow: 'hidden', height: '200px' }}>
                            <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=800&auto=format&fit=crop" alt="Promo 1" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                        </div>
                    </div>
                    <div className="col-md-6">
                        <div style={{ borderRadius: '15px', overflow: 'hidden', height: '200px' }}>
                            <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=800&auto=format&fit=crop" alt="Promo 2" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                        </div>
                    </div>
                </div>

                {/* Products Grid */}
                <div className="row g-3 g-md-4">
                    {shopProducts.map(product => (
                        <div key={product.id} className="col-6 col-md-4 col-lg-2">
                            <ProductCard product={product} />
                        </div>
                    ))}
                </div>

                {/* Pagination Placeholder */}
                <div className="d-flex justify-content-center mt-5">
                    <nav>
                        <ul className="pagination">
                            <li className="page-item active"><span className="page-link" style={{ backgroundColor: mainColor, borderColor: mainColor }}>1</span></li>
                            <li className="page-item"><span className="page-link text-dark">2</span></li>
                            <li className="page-item"><span className="page-link text-dark">›</span></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </MasterLayout>
    );
};

export default ShopDetails;
