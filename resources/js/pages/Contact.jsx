import React from 'react';
import MasterLayout from '../layouts/MasterLayout';

const Contact = () => {
    const mainColor = '#ff4d4d'; // Using the pinkish-red from screenshot

    return (
        <MasterLayout>
            <div className="container py-5">
                <div className="row align-items-center g-5">
                    {/* Left: Contact Form */}
                    <div className="col-lg-7">
                        <h2 className="fw-bold mb-3" style={{ fontSize: '32px' }}>Can't find the answer you are looking for?</h2>
                        <p className="text-muted mb-5" style={{ fontSize: '18px' }}>Our friendly assistant is here to assist you 24 hours a day!</p>
                        
                        <form className="row g-4">
                            <div className="col-md-6">
                                <label className="form-label small fw-bold text-muted mb-2">Full Name <span className="text-danger">*</span></label>
                                <input type="text" className="form-control custom-input py-3 px-4 border" placeholder="Enter full name" style={{ borderRadius: '10px', borderColor: '#e0e0e0', backgroundColor: '#fff', fontSize: '14px' }} />
                            </div>
                            <div className="col-md-6">
                                <label className="form-label small fw-bold text-muted mb-2">Phone Number <span className="text-danger">*</span></label>
                                <input type="text" className="form-control custom-input py-3 px-4 border" placeholder="Enter phone number" style={{ borderRadius: '10px', borderColor: '#e0e0e0', backgroundColor: '#fff', fontSize: '14px' }} />
                            </div>
                            <div className="col-12">
                                <label className="form-label small fw-bold text-muted mb-2">Subject <span className="text-danger">*</span></label>
                                <input type="text" className="form-control custom-input py-3 px-4 border" placeholder="Enter subject line" style={{ borderRadius: '10px', borderColor: '#e0e0e0', backgroundColor: '#fff', fontSize: '14px' }} />
                            </div>
                            <div className="col-12">
                                <label className="form-label small fw-bold text-muted mb-2">Message <span className="text-danger">*</span></label>
                                <textarea className="form-control custom-input py-3 px-4 border" rows="5" placeholder="Write your message ..." style={{ borderRadius: '10px', borderColor: '#e0e0e0', backgroundColor: '#fff', fontSize: '14px' }}></textarea>
                            </div>
                            <div className="col-12 mt-5">
                                <button type="button" className="btn btn-lg text-white px-5 py-3 fw-bold shadow-sm send-btn" style={{ backgroundColor: mainColor, borderRadius: '10px', fontSize: '16px', transition: 'all 0.3s' }}>
                                    Send
                                </button>
                            </div>
                        </form>
                    </div>

                    {/* Right: Support Image (Matching Screenshot) */}
                    <div className="col-lg-5 d-none d-lg-block">
                        <div style={{ borderRadius: '30px', overflow: 'hidden' }} className="shadow-lg border">
                            <img 
                                src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=600&auto=format&fit=crop" 
                                alt="Customer Support" 
                                style={{ width: '100%', height: 'auto', display: 'block' }} 
                            />
                        </div>
                    </div>
                </div>
            </div>
            <style>{`
                .custom-input { transition: all 0.3s ease; }
                .custom-input:focus { border-color: ${mainColor} !important; box-shadow: 0 0 0 0.25rem rgba(255, 77, 77, 0.1) !important; outline: none; }
                .send-btn:hover { background-color: #e60000 !important; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255, 77, 77, 0.2) !important; }
            `}</style>
        </MasterLayout>
    );
};

export default Contact;
