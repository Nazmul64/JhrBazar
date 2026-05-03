import React from 'react';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';

const bestDealProducts = [
    { id: 1, title: "Fitbit Charge 6 Fitness Tracker with GPS", price: 1000.00, oldPrice: 1200.00, discount: 16, sold: 19, rating: '0.0', image: "https://images.unsplash.com/photo-1575311373937-040b8e1fd5b6?q=80&w=300&auto=format&fit=crop" },
    { id: 2, title: "Freeman Beauty Korean Cica Soothing Mask", price: 60.00, sold: 14, rating: '0.0', image: "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?q=80&w=300&auto=format&fit=crop" },
    { id: 3, title: "Smart Watch Ultra High Performance", price: 121.00, oldPrice: 129.00, discount: 6, sold: 13, rating: '0.0', image: "https://images.unsplash.com/photo-1544117518-30dd057a1bb2?q=80&w=300&auto=format&fit=crop" },
    { id: 4, title: "HDR 4K UHD Smart QLED TV 55 Inch", price: 319.00, sold: 12, rating: '0.0', image: "https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?q=80&w=300&auto=format&fit=crop" },
    { id: 5, title: "Sony A6400 Mirrorless Camera With Lens", price: 1800.00, sold: 6, rating: '0.0', image: "https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=300&auto=format&fit=crop" },
    { id: 6, title: "Polar H10 Heart Rate Monitor Chest Strap", price: 2300.00, oldPrice: 2600.00, discount: 11, sold: 4, rating: '0.0', image: "https://images.unsplash.com/photo-1510017803434-a899398421b3?q=80&w=300&auto=format&fit=crop" },
    { id: 7, title: "HP 15s du3039TX 11th Gen i5-1135G7", price: 805.00, oldPrice: 820.00, discount: 2, sold: 4, rating: '0.0', image: "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=300&auto=format&fit=crop" },
    { id: 8, title: "SHEGLAM Matte Allure Liquid Lipstick", price: 101624.00, sold: 3, rating: '0.0', image: "https://images.unsplash.com/photo-1596462502278-27bfdc4033c8?q=80&w=300&auto=format&fit=crop" },
    { id: 9, title: "Luxury Mirror with Gold Frame", price: 10.00, sold: 3, rating: '0.0', image: "https://images.unsplash.com/photo-1618220179428-22790b461013?q=80&w=300&auto=format&fit=crop" },
    { id: 10, title: "Shuttlecock - Professional (Pack of 12)", price: 105.00, sold: 3, rating: '0.0', image: "https://images.unsplash.com/photo-1613918108466-292b78a8ef95?q=80&w=300&auto=format&fit=crop" },
    { id: 11, title: "Fresh Sweet Bell Pepper - Mixed Colors", price: 49.00, sold: 2, rating: '0.0', image: "https://images.unsplash.com/photo-1566232392379-afd9298e6a46?q=80&w=300&auto=format&fit=crop" },
    { id: 12, title: "Vernonle Super Slide Rush Hour Game", price: 29.00, sold: 2, rating: '0.0', image: "https://images.unsplash.com/photo-1610819013583-6997842a6288?q=80&w=300&auto=format&fit=crop" }
];

const BestDeal = () => {
    return (
        <MasterLayout>
            <div className="container py-5">
                {/* Title Section (Matches Screenshot) */}
                <h2 className="fw-bold mb-5" style={{ letterSpacing: '-1px' }}>Best Deal</h2>

                {/* Product Grid */}
                <div className="row g-2 g-md-4">
                    {bestDealProducts.map(product => (
                        <div key={product.id} className="col-6 col-md-4 col-lg-3 col-xl-2">
                            <ProductCard product={product} />
                        </div>
                    ))}
                </div>

                {/* Pagination Placeholder */}
                <div className="d-flex justify-content-between align-items-center mt-5 flex-wrap gap-3">
                    <div className="small text-muted">Showing 1 to 12 of 86 results</div>
                    <nav>
                        <ul className="pagination mb-0 gap-2">
                            <li className="page-item"><button className="page-link rounded-3 border-0 active" style={{ backgroundColor: '#ff4d4d', color: '#fff' }}>1</button></li>
                            <li className="page-item"><button className="page-link rounded-3 border bg-white text-dark">2</button></li>
                            <li className="page-item"><button className="page-link rounded-3 border bg-white text-dark">→</button></li>
                        </ul>
                    </nav>
                </div>
            </div>
            <style>{`
                .page-link { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px; }
            `}</style>
        </MasterLayout>
    );
};

export default BestDeal;
