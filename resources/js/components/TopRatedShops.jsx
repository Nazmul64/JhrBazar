import React from 'react';
import { Link } from 'react-router-dom';

const shops = [
    { 
        id: 1, name: "JHR Tech World", products: 122, rating: 5.0, 
        banner: "https://images.unsplash.com/photo-1531297484001-80022131f5a1?q=80&w=600&auto=format&fit=crop",
        logo: "https://images.unsplash.com/photo-1614850523296-d8c1af93d400?q=80&w=100&auto=format&fit=crop"
    },
    { 
        id: 2, name: "Easy Life Gadgets", products: 14, rating: 5.0, 
        banner: "https://images.unsplash.com/photo-1498049794561-7780e7231661?q=80&w=600&auto=format&fit=crop",
        logo: "https://images.unsplash.com/photo-1560179707-f14e90ef3623?q=80&w=100&auto=format&fit=crop"
    },
    { 
        id: 3, name: "Fashion Hub", products: 8, rating: 5.0, 
        banner: "https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=600&auto=format&fit=crop",
        logo: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=100&auto=format&fit=crop"
    },
    { 
        id: 4, name: "Style Haven", products: 11, rating: 5.0, 
        banner: "https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=600&auto=format&fit=crop",
        logo: "https://images.unsplash.com/photo-1554151228-14d9def656e4?q=80&w=100&auto=format&fit=crop"
    }
];

const TopRatedShops = () => {
    const mainColor = '#57b500';

    return (
        <section className="container mb-5">
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div className="d-flex align-items-center gap-2">
                    <div style={{ width: '5px', height: '25px', backgroundColor: mainColor, borderRadius: '10px' }}></div>
                    <h3 className="fw-bold mb-0">Top Rated Shops</h3>
                </div>
                <Link to="/" className="text-decoration-none small" style={{ color: mainColor }}>View All ›</Link>
            </div>

            <div className="row g-3">
                {shops.map(shop => (
                    <div key={shop.id} className="col-lg-3 col-md-6">
                        <div style={{ 
                            borderRadius: '15px', 
                            overflow: 'hidden', 
                            backgroundColor: mainColor, 
                            position: 'relative',
                            boxShadow: '0 4px 15px rgba(0,0,0,0.1)'
                        }}>
                            {/* Shop Banner */}
                            <div style={{ height: '120px', overflow: 'hidden' }}>
                                <img src={shop.banner} alt={shop.name} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                            </div>

                            {/* Shop Logo Overlay */}
                            <div style={{ 
                                position: 'absolute', 
                                top: '90px', 
                                left: '15px', 
                                width: '50px', 
                                height: '50px', 
                                borderRadius: '50%', 
                                border: '3px solid #fff', 
                                overflow: 'hidden',
                                backgroundColor: '#fff',
                                zIndex: 2
                            }}>
                                <img src={shop.logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                            </div>

                            {/* Shop Info */}
                            <div style={{ padding: '35px 15px 15px 15px', color: '#fff' }}>
                                <h6 style={{ fontWeight: 'bold', marginBottom: '5px', fontSize: '15px' }}>{shop.name}</h6>
                                <div style={{ fontSize: '12px', opacity: 0.9 }}>
                                    {shop.products} items | ⭐ {shop.rating}
                                </div>
                                <Link to="/shop-details" style={{ 
                                    display: 'inline-block', 
                                    marginTop: '10px', 
                                    color: '#fff', 
                                    fontSize: '12px', 
                                    textDecoration: 'none',
                                    borderBottom: '1px solid #fff'
                                }}>
                                    Visit Store →
                                </Link>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </section>
    );
};

export default TopRatedShops;
