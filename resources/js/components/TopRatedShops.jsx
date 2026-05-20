import React from 'react';
import { Link } from 'react-router-dom';

const TopRatedShops = ({ shops = [], loading = false }) => {
    const mainColor = '#57b500';

    if (!loading && (!shops || shops.length === 0)) return null;

    return (
        <section className="container mb-5">
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div className="d-flex align-items-center gap-2">
                    <div style={{ width: '5px', height: '25px', backgroundColor: mainColor, borderRadius: '10px' }}></div>
                    <h3 className="fw-bold mb-0">Top Rated Shops</h3>
                </div>
                <Link to="/" className="text-decoration-none small" style={{ color: mainColor }}>View All ›</Link>
            </div>

            <div className="row g-2 g-md-3">
                {loading ? (
                    [1, 2, 3, 4].map(i => (
                        <div key={i} className="col-lg-3 col-md-6 col-6">
                            <div className="placeholder-glow">
                                <div className="placeholder rounded-4 w-100" style={{ height: '180px' }}></div>
                            </div>
                        </div>
                    ))
                ) : (
                    shops.map(shop => (
                        <div key={shop.id} className="col-lg-3 col-md-6 col-6">
                            <div className="shop-card-main" style={{
                                borderRadius: '15px',
                                overflow: 'hidden',
                                backgroundColor: mainColor,
                                position: 'relative',
                                boxShadow: '0 4px 15px rgba(0,0,0,0.1)',
                                height: '100%',
                                transition: 'transform 0.3s'
                            }}>
                                {/* Shop Banner */}
                                <div className="shop-banner-container" style={{ height: '90px', overflow: 'hidden' }}>
                                    <img src={shop.banner} alt={shop.name} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                                </div>

                                {/* Shop Logo Overlay */}
                                <div className="shop-logo-container" style={{
                                    position: 'absolute',
                                    top: '70px',
                                    left: '10px',
                                    width: '40px',
                                    height: '40px',
                                    borderRadius: '50%',
                                    border: '2px solid #fff',
                                    overflow: 'hidden',
                                    backgroundColor: '#fff',
                                    zIndex: 2
                                }}>
                                    <img src={shop.logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                                </div>

                                {/* Shop Info */}
                                <div className="shop-info-container" style={{ padding: '25px 10px 15px 10px', color: '#fff' }}>
                                    <h6 className="text-truncate" style={{ fontWeight: 'bold', marginBottom: '3px', fontSize: '13px' }}>{shop.name}</h6>
                                    <div style={{ fontSize: '10px', opacity: 0.9 }}>
                                        {shop.item_count} items | ⭐ {shop.rating}
                                    </div>
                                    <Link to={`/shop/${shop.seller_id}`} style={{
                                        display: 'inline-block',
                                        marginTop: '8px',
                                        color: '#fff',
                                        fontSize: '11px',
                                        textDecoration: 'none',
                                        borderBottom: '1px solid #fff'
                                    }}>
                                        Visit Store →
                                    </Link>
                                </div>
                            </div>
                        </div>
                    ))
                )}
            </div>
            <style>{`
                .shop-card-main:hover { transform: translateY(-5px); }
                @media (min-width: 768px) {
                    .shop-banner-container { height: 120px !important; }
                    .shop-logo-container { width: 50px !important; height: 50px !important; top: 95px !important; left: 15px !important; }
                    .shop-info-container { padding: 35px 15px 15px 15px !important; }
                    .shop-info-container h6 { fontSize: 16px !important; }
                }
            `}</style>
        </section>
    );
};

export default TopRatedShops;
