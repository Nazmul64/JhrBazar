import React, { useState } from 'react';
import MasterLayout from '../layouts/MasterLayout';

const Checkout = () => {
    const mainColor = '#57b500';
    const [paymentMethod, setPaymentMethod] = useState('cod');

    const paymentGateways = [
        { id: 'bkash', name: 'bKash', logo: 'https://www.logo.wine/a/logo/BKash/BKash-Logo.wine.svg' },
        { id: 'nagad', name: 'Nagad', logo: 'https://download.logo.wine/logo/Nagad/Nagad-Logo.wine.png' },
        { id: 'visa', name: 'Visa', logo: 'https://www.logo.wine/a/logo/Visa_Inc./Visa_Inc.-Logo.wine.svg' },
        { id: 'mastercard', name: 'Mastercard', logo: 'https://www.logo.wine/a/logo/Mastercard/Mastercard-Logo.wine.svg' },
        { id: 'paypal', name: 'PayPal', logo: 'https://www.logo.wine/a/logo/PayPal/PayPal-Logo.wine.svg' },
        { id: 'stripe', name: 'Stripe', logo: 'https://www.logo.wine/a/logo/Stripe_(company)/Stripe_(company)-Logo.wine.svg' }
    ];

    return (
        <MasterLayout>
            <div className="container py-5">
                {/* Breadcrumbs */}
                <nav className="mb-4">
                    <ol className="breadcrumb small" style={{ fontSize: '12px' }}>
                        <li className="breadcrumb-item"><a href="/" className="text-decoration-none text-muted">Home</a></li>
                        <li className="breadcrumb-item active text-dark fw-bold">Checkout</li>
                    </ol>
                </nav>

                <h3 className="fw-bold mb-5" style={{ letterSpacing: '-1px' }}>Checkout</h3>

                <div className="row g-5">
                    {/* Left: Form Sections */}
                    <div className="col-lg-8">
                        {/* Shipping Address Section - With Visible Borders */}
                        <div className="card border-0 shadow-sm mb-4" style={{ borderRadius: '25px', border: '1px solid #eee' }}>
                            <div className="card-body p-4 p-md-5">
                                <div className="d-flex align-items-center gap-2 mb-5">
                                    <div style={{ width: '12px', height: '28px', backgroundColor: mainColor, borderRadius: '6px' }}></div>
                                    <h5 className="fw-bold mb-0" style={{ color: '#333' }}>Shipping Address</h5>
                                </div>
                                
                                <div className="row g-4">
                                    {/* Full Name */}
                                    <div className="col-md-6">
                                        <label className="form-label small fw-bold text-muted mb-2">FULL NAME</label>
                                        <div className="position-relative">
                                            <span className="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted" style={{ fontSize: '18px' }}>👤</span>
                                            <input type="text" className="form-control custom-input py-3 ps-5 border" placeholder="e.g. John Doe" style={{ borderRadius: '15px', fontSize: '14px', borderColor: '#e0e0e0', backgroundColor: '#fff' }} />
                                        </div>
                                    </div>

                                    {/* Email Address */}
                                    <div className="col-md-6">
                                        <label className="form-label small fw-bold text-muted mb-2">EMAIL ADDRESS</label>
                                        <div className="position-relative">
                                            <span className="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted" style={{ fontSize: '18px' }}>📧</span>
                                            <input type="email" className="form-control custom-input py-3 ps-5 border" placeholder="john@example.com" style={{ borderRadius: '15px', fontSize: '14px', borderColor: '#e0e0e0', backgroundColor: '#fff' }} />
                                        </div>
                                    </div>

                                    {/* Phone Number */}
                                    <div className="col-md-6">
                                        <label className="form-label small fw-bold text-muted mb-2">PHONE NUMBER</label>
                                        <div className="position-relative">
                                            <span className="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted" style={{ fontSize: '18px' }}>📞</span>
                                            <input type="text" className="form-control custom-input py-3 ps-5 border" placeholder="+880 1XXX XXXXXX" style={{ borderRadius: '15px', fontSize: '14px', borderColor: '#e0e0e0', backgroundColor: '#fff' }} />
                                        </div>
                                    </div>

                                    {/* Area / City */}
                                    <div className="col-md-6">
                                        <label className="form-label small fw-bold text-muted mb-2">AREA / CITY</label>
                                        <div className="position-relative">
                                            <span className="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted" style={{ fontSize: '18px' }}>📍</span>
                                            <select className="form-select custom-input py-3 ps-5 border" style={{ borderRadius: '15px', fontSize: '14px', appearance: 'none', borderColor: '#e0e0e0', backgroundColor: '#fff' }}>
                                                <option>Dhaka, Bangladesh</option>
                                                <option>Chittagong</option>
                                                <option>Sylhet</option>
                                                <option>Rajshahi</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    {/* Live Google Map */}
                                    <div className="col-12 mt-5">
                                        <div className="d-flex justify-content-between align-items-center mb-3">
                                            <label className="form-label small fw-bold text-muted mb-0">SELECT LOCATION ON MAP</label>
                                            <span className="badge bg-white text-success border border-success-subtle px-3 py-2 rounded-pill small">LIVE MAP</span>
                                        </div>
                                        <div style={{ height: '380px', borderRadius: '25px', overflow: 'hidden', border: '1px solid #ddd' }} className="shadow-sm">
                                            <iframe 
                                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d116833.8318788484!2d90.33728801977114!3d23.780887457121653!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8b087026b81%3A0x8fa563bbdd5904c2!2sDhaka!5e0!3m2!1sen!2sbd!4v1714750000000!5m2!1sen!2sbd" 
                                                width="100%" 
                                                height="100%" 
                                                style={{ border: 0 }} 
                                                allowFullScreen="" 
                                                loading="lazy" 
                                            ></iframe>
                                        </div>
                                    </div>

                                    <div className="col-12 mt-4">
                                        <label className="form-label small fw-bold text-muted mb-2">DETAILED ADDRESS LINE</label>
                                        <div className="position-relative">
                                            <span className="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted" style={{ fontSize: '18px' }}>🏠</span>
                                            <input type="text" className="form-control custom-input py-3 ps-5 border" placeholder="House #, Street #, Area Name..." style={{ borderRadius: '15px', fontSize: '14px', borderColor: '#e0e0e0', backgroundColor: '#fff' }} />
                                        </div>
                                    </div>
                                    
                                    <div className="col-12 mt-4">
                                        <label className="form-label small fw-bold text-muted d-block mb-3">ADDRESS TAG</label>
                                        <div className="d-flex gap-3">
                                            {['HOME', 'OFFICE', 'OTHER'].map(tag => (
                                                <button key={tag} className={`btn px-5 py-2 fw-bold small transition-all ${tag === 'HOME' ? 'btn-success text-white shadow-sm' : 'btn-light text-muted border'}`} style={{ backgroundColor: tag === 'HOME' ? mainColor : '#fff', borderRadius: '30px', border: tag === 'HOME' ? 'none' : '1px solid #ddd' }}>
                                                    {tag}
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Payment Method Section */}
                        <div className="card border-0 shadow-sm mb-4" style={{ borderRadius: '25px', border: '1px solid #eee' }}>
                            <div className="card-body p-4 p-md-5">
                                <div className="d-flex align-items-center gap-2 mb-4">
                                    <div style={{ width: '12px', height: '28px', backgroundColor: mainColor, borderRadius: '6px' }}></div>
                                    <h5 className="fw-bold mb-0">Payment Method</h5>
                                </div>
                                
                                <div className="row g-4">
                                    <div className="col-md-6">
                                        <div 
                                            onClick={() => setPaymentMethod('cod')}
                                            className="p-4 rounded-4 text-center cursor-pointer transition-all border"
                                            style={{ 
                                                border: `2px solid ${paymentMethod === 'cod' ? mainColor : '#eee'}`,
                                                backgroundColor: paymentMethod === 'cod' ? '#f0fff0' : '#fff'
                                            }}
                                        >
                                            <div style={{ fontSize: '32px', marginBottom: '10px' }}>💵</div>
                                            <div className="fw-bold">Cash On Delivery</div>
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div 
                                            onClick={() => setPaymentMethod('card')}
                                            className="p-4 rounded-4 text-center cursor-pointer transition-all border"
                                            style={{ 
                                                border: `2px solid ${paymentMethod === 'card' ? mainColor : '#eee'}`,
                                                backgroundColor: paymentMethod === 'card' ? '#f0fff0' : '#fff'
                                            }}
                                        >
                                            <div style={{ fontSize: '32px', marginBottom: '10px' }}>💳</div>
                                            <div className="fw-bold">Online Payment</div>
                                        </div>
                                    </div>
                                </div>

                                {paymentMethod === 'card' && (
                                    <div className="mt-5 animate-fade-in">
                                        <p className="text-muted fw-bold small mb-4">SECURE PAYMENT GATEWAYS</p>
                                        <div className="row g-3">
                                            {paymentGateways.map(gate => (
                                                <div key={gate.id} className="col-4 col-md-3 col-lg-2">
                                                    <div className="gateway-card text-center shadow-sm p-3 bg-white border border-light-subtle rounded-3 d-flex align-items-center justify-content-center" style={{ height: '70px', transition: 'all 0.3s' }}>
                                                        <img src={gate.logo} alt={gate.name} style={{ maxWidth: '100%', maxHeight: '35px', objectFit: 'contain' }} />
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Right: Order Summary Sidebar */}
                    <div className="col-lg-4">
                        <div className="card border-0 shadow-sm sticky-top" style={{ borderRadius: '30px', top: '100px', border: '1px solid #eee' }}>
                            <div className="card-body p-4 p-md-5">
                                <h5 className="fw-bold mb-4">Order Summary</h5>
                                <div className="d-flex flex-column gap-3 border-bottom pb-4 mb-4">
                                    <div className="d-flex justify-content-between">
                                        <span className="text-muted">Subtotal</span>
                                        <span className="fw-bold">$121.00</span>
                                    </div>
                                    <div className="d-flex justify-content-between">
                                        <span className="text-muted">Shipping</span>
                                        <span className="text-success fw-bold">FREE</span>
                                    </div>
                                    <div className="d-flex justify-content-between">
                                        <span className="text-muted">Taxes & VAT</span>
                                        <span className="fw-bold">$6.05</span>
                                    </div>
                                </div>
                                <div className="d-flex justify-content-between align-items-center mb-5">
                                    <h4 className="fw-bold mb-0">Total</h4>
                                    <h4 className="fw-bold mb-0" style={{ color: mainColor }}>$127.05</h4>
                                </div>
                                <button className="btn btn-lg w-100 text-white fw-bold py-3 shadow-lg confirm-btn" style={{ backgroundColor: mainColor, borderRadius: '20px', fontSize: '18px', transition: 'all 0.3s' }}>
                                    Confirm Order
                                </button>
                                <div className="text-center mt-4">
                                    <span className="text-muted small">🔒 Secure SSL Encryption</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>{`
                .custom-input { transition: all 0.3s ease; }
                .custom-input:focus { border: 1px solid ${mainColor} !important; box-shadow: 0 0 0 0.25rem rgba(87, 181, 0, 0.1) !important; outline: none; }
                .gateway-card:hover { transform: translateY(-5px); border-color: ${mainColor} !important; box-shadow: 0 8px 20px rgba(0,0,0,0.05) !important; }
                .confirm-btn:hover { background-color: #4a9a00 !important; transform: scale(1.02); }
                .cursor-pointer { cursor: pointer; }
                .transition-all { transition: all 0.3s ease; }
                .animate-fade-in { animation: fadeIn 0.5s ease; }
                @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            `}</style>
        </MasterLayout>
    );
};

export default Checkout;
