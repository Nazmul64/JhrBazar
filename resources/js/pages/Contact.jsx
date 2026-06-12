import React, { useState, useEffect } from 'react';
import axios from 'axios';
import MasterLayout from '../layouts/MasterLayout';

const Contact = () => {
    const mainColor = '#ff4d4d'; // Dynamic look pinkish-red

    // State for Contact Info (Email, Phone, WhatsApp, Messenger, Map)
    const [contactInfo, setContactInfo] = useState(null);
    const [loadingInfo, setLoadingInfo] = useState(true);

    // Form inputs state
    const [formData, setFormData] = useState({
        full_name: '',
        phone_number: '',
        subject: '',
        message: ''
    });

    // Form submission states
    const [submitting, setSubmitting] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');
    const [errorMessage, setErrorMessage] = useState('');

    // Fetch contact details from API
    useEffect(() => {
        axios.get('/api/contact-info')
            .then(res => {
                if (res.data.success && res.data.contact) {
                    setContactInfo(res.data.contact);
                }
            })
            .catch(err => console.error("Error fetching contact details:", err))
            .finally(() => setLoadingInfo(false));
    }, []);

    // Handle input change
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    // Handle form submit
    const handleSubmit = async (e) => {
        e.preventDefault();
        setSuccessMessage('');
        setErrorMessage('');

        if (!formData.full_name || !formData.phone_number || !formData.subject || !formData.message) {
            setErrorMessage('All fields are required.');
            return;
        }

        setSubmitting(true);
        try {
            const res = await axios.post('/api/contact/submit', formData);
            if (res.data.success) {
                setSuccessMessage(res.data.message || 'Your message has been sent successfully!');
                setFormData({
                    full_name: '',
                    phone_number: '',
                    subject: '',
                    message: ''
                });
            } else {
                setErrorMessage(res.data.message || 'Failed to send message. Please try again.');
            }
        } catch (error) {
            console.error("Error submitting contact form:", error);
            if (error.response?.data?.message) {
                setErrorMessage(error.response.data.message);
            } else {
                setErrorMessage('An error occurred. Please check your network and try again.');
            }
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <MasterLayout>
            <div className="container py-5">
                <div className="row g-5">
                    {/* Left: Contact Form */}
                    <div className="col-lg-7">
                        <h2 className="fw-bold mb-3" style={{ fontSize: '32px' }}>Can't find the answer you are looking for?</h2>
                        <p className="text-muted mb-4" style={{ fontSize: '18px' }}>Our friendly assistant is here to assist you 24 hours a day!</p>
                        
                        {successMessage && (
                            <div className="alert alert-success border-0 shadow-sm p-3 mb-4 d-flex align-items-center gap-2" style={{ borderRadius: '10px', backgroundColor: '#e6fffa', color: '#0984e3' }}>
                                <i className="fas fa-check-circle fs-5"></i>
                                <span>{successMessage}</span>
                            </div>
                        )}

                        {errorMessage && (
                            <div className="alert alert-danger border-0 shadow-sm p-3 mb-4 d-flex align-items-center gap-2" style={{ borderRadius: '10px', backgroundColor: '#fff5f5', color: '#e17055' }}>
                                <i className="fas fa-exclamation-circle fs-5"></i>
                                <span>{errorMessage}</span>
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="row g-4">
                            <div className="col-md-6">
                                <label className="form-label small fw-bold text-muted mb-2">Full Name <span className="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    name="full_name"
                                    value={formData.full_name}
                                    onChange={handleChange}
                                    className="form-control custom-input py-3 px-4 border" 
                                    placeholder="Enter full name" 
                                    style={{ borderRadius: '10px', borderColor: '#e0e0e0', backgroundColor: '#fff', fontSize: '14px' }} 
                                    required 
                                />
                            </div>
                            <div className="col-md-6">
                                <label className="form-label small fw-bold text-muted mb-2">Phone Number <span className="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    name="phone_number"
                                    value={formData.phone_number}
                                    onChange={handleChange}
                                    className="form-control custom-input py-3 px-4 border" 
                                    placeholder="Enter phone number" 
                                    style={{ borderRadius: '10px', borderColor: '#e0e0e0', backgroundColor: '#fff', fontSize: '14px' }} 
                                    required 
                                />
                            </div>
                            <div className="col-12">
                                <label className="form-label small fw-bold text-muted mb-2">Subject <span className="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    name="subject"
                                    value={formData.subject}
                                    onChange={handleChange}
                                    className="form-control custom-input py-3 px-4 border" 
                                    placeholder="Enter subject line" 
                                    style={{ borderRadius: '10px', borderColor: '#e0e0e0', backgroundColor: '#fff', fontSize: '14px' }} 
                                    required 
                                />
                            </div>
                            <div className="col-12">
                                <label className="form-label small fw-bold text-muted mb-2">Message <span className="text-danger">*</span></label>
                                <textarea 
                                    name="message"
                                    value={formData.message}
                                    onChange={handleChange}
                                    className="form-control custom-input py-3 px-4 border" 
                                    rows="5" 
                                    placeholder="Write your message ..." 
                                    style={{ borderRadius: '10px', borderColor: '#e0e0e0', backgroundColor: '#fff', fontSize: '14px' }}
                                    required 
                                ></textarea>
                            </div>
                            <div className="col-12 mt-5">
                                <button 
                                    type="submit" 
                                    disabled={submitting}
                                    className="btn btn-lg text-white px-5 py-3 fw-bold shadow-sm send-btn" 
                                    style={{ backgroundColor: mainColor, borderRadius: '10px', fontSize: '16px', transition: 'all 0.3s' }}
                                >
                                    {submitting ? (
                                        <><span className="spinner-border spinner-border-sm me-2"></span>Sending...</>
                                    ) : 'Send'}
                                </button>
                            </div>
                        </form>
                    </div>

                    {/* Right: Support Image & Dynamic Info */}
                    <div className="col-lg-5">
                        <div style={{ borderRadius: '20px', overflow: 'hidden' }} className="shadow-sm border mb-4">
                            <img 
                                src={contactInfo?.contact_image_url || "https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=600&auto=format&fit=crop"} 
                                alt="Customer Support" 
                                style={{ width: '100%', height: 'auto', display: 'block', maxHeight: '300px', objectFit: 'cover' }} 
                            />
                        </div>

                        {contactInfo && (
                            <div className="card border-0 shadow-sm p-4" style={{ borderRadius: '15px', backgroundColor: '#fff' }}>
                                <h5 className="fw-bold mb-4" style={{ color: '#333' }}>Contact Information</h5>
                                <div className="d-flex flex-column gap-3">
                                    {contactInfo.phone_number && (
                                        <div className="d-flex align-items-center gap-3">
                                            <div className="rounded-circle d-flex align-items-center justify-content-center text-white" style={{ width: '40px', height: '40px', backgroundColor: '#2ecc71' }}>
                                                <i className="fas fa-phone-alt"></i>
                                            </div>
                                            <div>
                                                <small className="text-muted d-block">Phone Number</small>
                                                <a href={`tel:${contactInfo.phone_number}`} className="text-dark fw-bold text-decoration-none">{contactInfo.phone_number}</a>
                                            </div>
                                        </div>
                                    )}

                                    {contactInfo.whatsapp_number && (
                                        <div className="d-flex align-items-center gap-3">
                                            <div className="rounded-circle d-flex align-items-center justify-content-center text-white" style={{ width: '40px', height: '40px', backgroundColor: '#25D366' }}>
                                                <i className="fab fa-whatsapp"></i>
                                            </div>
                                            <div>
                                                <small className="text-muted d-block">WhatsApp</small>
                                                <a href={`https://wa.me/${contactInfo.whatsapp_number}`} target="_blank" rel="noopener noreferrer" className="text-dark fw-bold text-decoration-none">{contactInfo.whatsapp_number}</a>
                                            </div>
                                        </div>
                                    )}

                                    {contactInfo.email_address && (
                                        <div className="d-flex align-items-center gap-3">
                                            <div className="rounded-circle d-flex align-items-center justify-content-center text-white" style={{ width: '40px', height: '40px', backgroundColor: '#3498db' }}>
                                                <i className="fas fa-envelope"></i>
                                            </div>
                                            <div>
                                                <small className="text-muted d-block">Email Address</small>
                                                <a href={`mailto:${contactInfo.email_address}`} className="text-dark fw-bold text-decoration-none">{contactInfo.email_address}</a>
                                            </div>
                                        </div>
                                    )}

                                    {contactInfo.messenger_link && (
                                        <div className="d-flex align-items-center gap-3">
                                            <div className="rounded-circle d-flex align-items-center justify-content-center text-white" style={{ width: '40px', height: '40px', backgroundColor: '#006AFF' }}>
                                                <i className="fab fa-facebook-messenger"></i>
                                            </div>
                                            <div>
                                                <small className="text-muted d-block">Messenger</small>
                                                <a href={contactInfo.messenger_link} target="_blank" rel="noopener noreferrer" className="text-dark fw-bold text-decoration-none">Chat on Messenger</a>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                {/* Google Map Embed */}
                {contactInfo?.map_embed_code && (
                    <div className="row mt-5">
                        <div className="col-12">
                            <h4 className="fw-bold mb-4" style={{ color: '#333' }}>Find Us on Map</h4>
                            <div 
                                className="shadow-sm border rounded-4 overflow-hidden" 
                                style={{ height: '400px' }}
                                dangerouslySetInnerHTML={{ __html: contactInfo.map_embed_code.replace(/width="[0-9]+"/g, 'width="100%"').replace(/height="[0-9]+"/g, 'height="100%"') }}
                            />
                        </div>
                    </div>
                )}
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
