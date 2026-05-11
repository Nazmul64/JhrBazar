import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

const Footer = () => {
    const [footerData, setFooterData] = useState({
        product_categories: [],
        page_categories: [],
        settings: null
    });

    useEffect(() => {
        axios.get('/api/footer-data')
            .then(res => {
                if (res.data.success) {
                    setFooterData(res.data);
                }
            })
            .catch(err => console.error("Error fetching footer data", err));
    }, []);

    const socialIconStyle = {
        width: '32px',
        height: '32px',
        backgroundColor: '#fff',
        borderRadius: '50%',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '14px',
        color: '#e67e22',
        textDecoration: 'none',
        transition: 'all 0.3s ease',
        border: '1px solid #eee'
    };

    const headingStyle = { color: '#333', fontWeight: 'bold', marginBottom: '20px', fontSize: '16px' };
    const linkStyle = { textDecoration: 'none', color: '#666', fontSize: '13px', transition: 'color 0.2s' };

    return (
        <footer style={{ backgroundColor: '#fff', color: '#333', padding: '80px 0 30px 0', borderTop: '1px solid #f0f0f0', fontFamily: "'Poppins', sans-serif" }}>
            <div className="container">
                <div className="row g-4 mb-5">
                    {/* Column 1: Brand Info */}
                    <div className="col-lg-4 col-md-12">
                        <div className="mb-4">
                            <img 
                                src={footerData.settings?.footer_logo || footerData.settings?.logo || "https://ghorerbazar.com/wp-content/uploads/2020/10/Ghorer-Bazar-Logo.png"} 
                                alt={footerData.settings?.website_name || "Ghorer Bazar"} 
                                style={{ maxHeight: '55px' }} 
                            />
                        </div>
                        <p style={{ color: '#777', lineHeight: '1.8', fontSize: '13px', marginBottom: '25px', maxWidth: '340px' }}>
                            {footerData.settings?.footer_text || "Ghorer Bazar is an e-commerce platform dedicated to providing safe and reliable food to every home."}
                        </p>
                        
                        <div className="d-flex flex-column gap-3 mb-4" style={{ fontSize: '13px', color: '#666' }}>
                            {footerData.settings?.address && (
                                <div className="d-flex align-items-start gap-2">
                                    <i className="fas fa-map-marker-alt mt-1" style={{ color: '#e67e22' }}></i>
                                    <span>{footerData.settings.address}</span>
                                </div>
                            )}
                            {(footerData.settings?.mobile_number || footerData.settings?.hotline_number) && (
                                <div className="d-flex align-items-center gap-2">
                                    <i className="fas fa-phone-alt" style={{ color: '#e67e22' }}></i>
                                    <span>{footerData.settings.mobile_number || footerData.settings.hotline_number}</span>
                                </div>
                            )}
                            {footerData.settings?.email_address && (
                                <div className="d-flex align-items-center gap-2">
                                    <i className="fas fa-envelope" style={{ color: '#e67e22' }}></i>
                                    <span>{footerData.settings.email_address}</span>
                                </div>
                            )}
                        </div>

                        <div className="d-flex gap-2 mb-4">
                            <a href="#" style={socialIconStyle} className="social-icon-hover"><i className="fab fa-facebook-f"></i></a>
                            <a href="#" style={socialIconStyle} className="social-icon-hover"><i className="fab fa-twitter"></i></a>
                            <a href="#" style={socialIconStyle} className="social-icon-hover"><i className="fab fa-instagram"></i></a>
                        </div>

                        {footerData.settings?.show_download_app == 1 && (
                            <div className="mt-4">
                                <p className="mb-3 fw-bold" style={{ fontSize: '13px', color: '#333' }}>Download App on Mobile :</p>
                                <div className="d-flex gap-2">
                                    {footerData.settings.google_playstore_link && (
                                        <a href={footerData.settings.google_playstore_link} target="_blank" rel="noopener noreferrer" className="app-btn">
                                            <img src="https://ghorerbazar.com/wp-content/uploads/2021/04/google-play.png" alt="Google Play" style={{ height: '35px' }} />
                                        </a>
                                    )}
                                    {footerData.settings.apple_store_link && (
                                        <a href={footerData.settings.apple_store_link} target="_blank" rel="noopener noreferrer" className="app-btn">
                                            <img src="https://ghorerbazar.com/wp-content/uploads/2021/04/app-store.png" alt="App Store" style={{ height: '35px' }} />
                                        </a>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Dynamic Page Categories (Information, Support, Policy, etc.) */}
                    {footerData.page_categories.map((cat) => (
                        <div key={cat.id} className="col-lg-2 col-md-6 col-6">
                            <h6 style={headingStyle}>{cat.name}</h6>
                            <ul className="list-unstyled d-flex flex-column gap-3">
                                {cat.pages.map(page => (
                                    <li key={page.id}>
                                        <Link to={`/page/${page.id}`} style={linkStyle} className="footer-link">
                                            {page.name}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>

                <div style={{ borderTop: '1px solid #f0f0f0', paddingTop: '30px', marginTop: '50px' }}>
                    <div className="row align-items-center">
                        <div className="col-md-6 text-center text-md-start">
                            <p className="mb-0" style={{ fontSize: '13px', color: '#999' }}>
                                Copyright © 2026 GhorerBazar
                            </p>
                        </div>
                        <div className="col-md-6 text-center text-md-end mt-4 mt-md-0">
                            <div className="d-flex align-items-center justify-content-center justify-content-md-end gap-3 flex-wrap">
                                <span style={{ fontSize: '13px', color: '#333', fontWeight: 'bold' }}>Pay With</span>
                                <div className="d-flex gap-1 flex-wrap justify-content-center">
                                    <img src="https://ghorerbazar.com/wp-content/uploads/2021/04/bkash.png" alt="bkash" style={{ height: '28px', border: '1px solid #eee', borderRadius: '4px' }} />
                                    <img src="https://ghorerbazar.com/wp-content/uploads/2021/04/nagad.png" alt="nagad" style={{ height: '28px', border: '1px solid #eee', borderRadius: '4px' }} />
                                    <img src="https://ghorerbazar.com/wp-content/uploads/2021/04/rocket.png" alt="rocket" style={{ height: '28px', border: '1px solid #eee', borderRadius: '4px' }} />
                                    <img src="https://ghorerbazar.com/wp-content/uploads/2021/04/visa.png" alt="visa" style={{ height: '28px', border: '1px solid #eee', borderRadius: '4px' }} />
                                    <img src="https://ghorerbazar.com/wp-content/uploads/2021/04/mastercard.png" alt="mastercard" style={{ height: '28px', border: '1px solid #eee', borderRadius: '4px' }} />
                                    <img src="https://ghorerbazar.com/wp-content/uploads/2021/04/dbbl.png" alt="dbbl" style={{ height: '28px', border: '1px solid #eee', borderRadius: '4px' }} />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .footer-link:hover {
                    color: #e67e22 !important;
                    padding-left: 5px;
                }
                .social-icon-hover:hover {
                    background-color: #e67e22 !important;
                    color: #fff !important;
                    transform: translateY(-3px);
                    border-color: #e67e22 !important;
                }
                .app-btn {
                    transition: transform 0.2s;
                }
                .app-btn:hover {
                    transform: scale(1.05);
                }
            `}</style>
        </footer>
    );
};

export default Footer;

