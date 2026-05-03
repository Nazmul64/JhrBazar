import React from 'react';
import { Link } from 'react-router-dom';

const Footer = () => {
    const mainColor = '#57b500';

    const socialIconStyle = {
        width: '38px',
        height: '38px',
        backgroundColor: '#f5f5f5',
        borderRadius: '50%',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '18px',
        color: mainColor,
        textDecoration: 'none',
        transition: 'all 0.3s ease',
        boxShadow: '0 2px 5px rgba(0,0,0,0.05)'
    };

    return (
        <footer style={{ backgroundColor: '#ffffff', borderTop: `4px solid ${mainColor}`, color: '#333', padding: '60px 0 20px 0', fontSize: '14px' }}>
            <div className="container">
                <div className="row g-4">
                    {/* Column 1: Brand Info */}
                    <div className="col-lg-3 col-md-6">
                        <img src="https://demo.readyecommerce.app/public/assets/front-end/img/logo.png" alt="JHR Bazar" style={{ maxHeight: '50px', marginBottom: '20px' }} />
                        <p style={{ color: '#666', lineHeight: '1.6' }}>
                            JHR Bazar is your ultimate destination for premium quality products. We ensure the best shopping experience with fast delivery and secure payments.
                        </p>
                        {/* Real Social Icons using FontAwesome */}
                        <div className="d-flex gap-2 mt-4">
                            <a href="#" style={socialIconStyle} className="social-hover" title="Facebook">
                                <i className="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" style={socialIconStyle} className="social-hover" title="Twitter">
                                <i className="fab fa-twitter"></i>
                            </a>
                            <a href="#" style={socialIconStyle} className="social-hover" title="WhatsApp">
                                <i className="fab fa-whatsapp"></i>
                            </a>
                            <a href="#" style={socialIconStyle} className="social-hover" title="YouTube">
                                <i className="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>

                    {/* Column 2: Useful Links */}
                    <div className="col-lg-2 col-md-6">
                        <h6 style={{ fontWeight: 'bold', marginBottom: '25px' }}>Quick Links</h6>
                        <ul className="list-unstyled d-flex flex-column gap-2">
                            <li><Link to="/" className="text-decoration-none text-muted">Flash Sale</Link></li>
                            <li><Link to="/" className="text-decoration-none text-muted">All Brands</Link></li>
                            <li><Link to="/" className="text-decoration-none text-muted">Best Selling</Link></li>
                            <li><Link to="/" className="text-decoration-none text-muted">New Arrivals</Link></li>
                        </ul>
                    </div>

                    {/* Column 3: Support */}
                    <div className="col-lg-2 col-md-6">
                        <h6 style={{ fontWeight: 'bold', marginBottom: '25px' }}>Support</h6>
                        <ul className="list-unstyled d-flex flex-column gap-2">
                            <li><Link to="/" className="text-decoration-none text-muted">Help Center</Link></li>
                            <li><Link to="/" className="text-decoration-none text-muted">Track Order</Link></li>
                            <li><Link to="/" className="text-decoration-none text-muted">Return Policy</Link></li>
                            <li><Link to="/" className="text-decoration-none text-muted">Terms & Conditions</Link></li>
                        </ul>
                    </div>

                    {/* Column 4: Contact & App */}
                    <div className="col-lg-3 col-md-6">
                        <h6 style={{ fontWeight: 'bold', marginBottom: '25px' }}>Contact Us</h6>
                        <div className="d-flex flex-column gap-3">
                            <div className="d-flex align-items-center gap-2">
                                <span style={{ color: mainColor }}><i className="fas fa-phone-alt"></i></span>
                                <span>+880 1711 257498</span>
                            </div>
                            <div className="d-flex align-items-center gap-2">
                                <span style={{ color: mainColor }}><i className="fas fa-envelope"></i></span>
                                <span>support@jhrbazar.com</span>
                            </div>
                            <div className="mt-2">
                                <h6 style={{ fontSize: '13px', fontWeight: 'bold', marginBottom: '15px' }}>Download Our App</h6>
                                <div className="d-flex gap-2">
                                    <img src="https://demo.readyecommerce.app/public/assets/front-end/img/google_play.png" alt="Play Store" style={{ width: '110px', cursor: 'pointer' }} />
                                    <img src="https://demo.readyecommerce.app/public/assets/front-end/img/app_store.png" alt="App Store" style={{ width: '110px', cursor: 'pointer' }} />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Column 5: Payment Methods */}
                    <div className="col-lg-2 col-md-12 text-lg-end">
                        <h6 style={{ fontWeight: 'bold', marginBottom: '25px' }}>Secure Payment</h6>
                        <div className="d-flex flex-wrap gap-2 justify-content-lg-end">
                            <div style={{ backgroundColor: '#f9f9f9', padding: '6px 12px', borderRadius: '4px', border: '1px solid #eee', fontSize: '12px' }}>
                                <i className="fab fa-cc-visa me-1"></i> Visa
                            </div>
                            <div style={{ backgroundColor: '#f9f9f9', padding: '6px 12px', borderRadius: '4px', border: '1px solid #eee', fontSize: '12px' }}>
                                <i className="fab fa-cc-mastercard me-1"></i> Master
                            </div>
                            <div style={{ backgroundColor: '#f9f9f9', padding: '6px 12px', borderRadius: '4px', border: '1px solid #eee', fontSize: '12px' }}>
                                <i className="fas fa-money-bill-wave me-1"></i> bKash
                            </div>
                            <div style={{ backgroundColor: '#f9f9f9', padding: '6px 12px', borderRadius: '4px', border: '1px solid #eee', fontSize: '12px' }}>
                                <i className="fas fa-wallet me-1"></i> Nagad
                            </div>
                        </div>
                    </div>
                </div>

                <hr style={{ margin: '40px 0 20px 0', borderColor: '#eee' }} />

                <div className="row align-items-center">
                    <div className="col-md-6 text-center text-md-start">
                        <p className="mb-0 text-muted">© 2026 JHR Bazar. All rights reserved.</p>
                    </div>
                    <div className="col-md-6 text-center text-md-end mt-3 mt-md-0">
                        <div className="d-flex gap-3 justify-content-center justify-content-md-end">
                            <Link to="/" className="text-decoration-none text-muted small">Privacy Policy</Link>
                            <Link to="/" className="text-decoration-none text-muted small">Cookies Settings</Link>
                        </div>
                    </div>
                </div>
            </div>
            
            <style>{`
                .social-hover:hover {
                    background-color: ${mainColor} !important;
                    color: #fff !important;
                    transform: translateY(-5px);
                }
            `}</style>
        </footer>
    );
};

export default Footer;
