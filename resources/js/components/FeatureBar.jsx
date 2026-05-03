import React from 'react';

const FeatureBar = () => {
    const features = [
        { id: 1, title: 'Secure Payment Gateways', desc: '48+ gateways to ensure your security.', icon: '🛡️', color: '#eef2ff' },
        { id: 2, title: 'Genuine Customer Reviews', desc: 'Find verified reviews showcased on our platforms.', icon: '⭐', color: '#fffbeb' },
        { id: 3, title: '24/7 Customer Support', desc: 'Always our support team is available for you.', icon: '🎧', color: '#f0fdf4' },
        { id: 4, title: 'Easy Return Policy', desc: "If you're not satisfied, return it hassle-free.", icon: '🔄', color: '#eff6ff' }
    ];

    return (
        <section className="container my-4">
            <div className="row g-3">
                {features.map(f => (
                    <div key={f.id} className="col-12 col-md-6 col-lg-3">
                        <div style={{
                            backgroundColor: '#fff',
                            border: '1px solid #f0f0f0',
                            borderRadius: '12px',
                            padding: '20px',
                            display: 'flex',
                            alignItems: 'center',
                            gap: '15px',
                            height: '100%',
                            boxShadow: '0 2px 5px rgba(0,0,0,0.02)'
                        }}>
                            <div style={{
                                width: '50px',
                                height: '50px',
                                borderRadius: '10px',
                                backgroundColor: f.color,
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                fontSize: '24px'
                            }}>
                                {f.icon}
                            </div>
                            <div>
                                <h6 style={{ fontWeight: 'bold', margin: 0, fontSize: '14px' }}>{f.title}</h6>
                                <p style={{ margin: 0, fontSize: '12px', color: '#777', marginTop: '3px' }}>{f.desc}</p>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </section>
    );
};

export default FeatureBar;
