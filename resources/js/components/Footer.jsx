import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

const Footer = () => {
    const [footerData, setFooterData] = useState({
        product_categories: [],
        page_categories: [],
        settings: null,
        social_links: []
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

    const getIconClass = (name) => {
        const platform = name.toLowerCase();
        if (platform.includes('facebook')) return 'fab fa-facebook-f';
        if (platform.includes('twitter') || platform.includes('x')) return 'fab fa-twitter';
        if (platform.includes('instagram')) return 'fab fa-instagram';
        if (platform.includes('linkedin')) return 'fab fa-linkedin-in';
        if (platform.includes('youtube')) return 'fab fa-youtube';
        if (platform.includes('whatsapp')) return 'fab fa-whatsapp';
        if (platform.includes('telegram')) return 'fab fa-telegram-plane';
        if (platform.includes('google')) return 'fab fa-google-plus-g';
        return 'fas fa-link';
    };

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

    const headingStyle = { color: 'var(--footer-text-color, #333)', fontWeight: 'bold', marginBottom: '20px', fontSize: '16px' };
    const linkStyle = { textDecoration: 'none', color: 'var(--footer-text-color, #666)', opacity: 0.85, fontSize: '13px', transition: 'all 0.2s' };

    return (
        <footer className="custom-footer" style={{ backgroundColor: 'var(--footer-bg, #fff)', color: 'var(--footer-text-color, #333)', padding: '80px 0 30px 0', borderTop: '1px solid rgba(0,0,0,0.08)', fontFamily: "'Poppins', sans-serif" }}>
            <div className="container">
                <div className="row g-4 mb-5">
                    {/* Column 1: Brand Info */}
                    <div className="col-lg-4 col-md-12">
                        <div className="mb-3">
                            <img
                                src={footerData.settings?.footer_logo || footerData.settings?.logo || "https://ghorerbazar.com/wp-content/uploads/2020/10/Ghorer-Bazar-Logo.png"}
                                alt={footerData.settings?.website_name || "Ghorer Bazar"}
                                style={{ maxHeight: '55px' }}
                            />
                        </div>
                        <div>
                        <p style={{ color: 'var(--footer-text-color, #333)', opacity: 0.8, lineHeight: '1.8', fontSize: '13px', marginBottom: '25px', maxWidth: '340px' }}>
                            {footerData.settings?.footer_text || "Ghorer Bazar is an e-commerce platform dedicated to providing safe and reliable food to every home."}
                        </p>

                        {(footerData.settings?.trade_license_number || footerData.settings?.dbid_number) && (
                            <div style={{ marginBottom: '20px', maxWidth: '340px' }}>
                                {footerData.settings?.trade_license_number && (
                                    <p className="mb-1" style={{ fontSize: '13px', color: 'var(--footer-text-color, #333)' }}>
                                        <strong>Trade License:</strong> {footerData.settings.trade_license_number}
                                    </p>
                                )}
                                {footerData.settings?.dbid_number && (
                                    <p className="mb-0" style={{ fontSize: '13px', color: 'var(--footer-text-color, #333)' }}>
                                        <strong>DBID:</strong> {footerData.settings.dbid_number}
                                    </p>
                                )}
                            </div>
                        )}

                        <div className="d-flex flex-column gap-3 mb-4" style={{ fontSize: '13px', color: 'var(--footer-text-color, #333)', opacity: 0.85 }}>
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
                            {footerData.social_links && footerData.social_links.length > 0 ? (
                                footerData.social_links.map((social) => (
                                    <a
                                        key={social.id}
                                        href={social.link || '#'}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        style={socialIconStyle}
                                        className="social-icon-hover"
                                        title={social.name}
                                    >
                                        <i className={getIconClass(social.name)}></i>
                                    </a>
                                ))
                            ) : (
                                <>
                                    <a href="#" style={socialIconStyle} className="social-icon-hover"><i className="fab fa-facebook-f"></i></a>
                                    <a href="#" style={socialIconStyle} className="social-icon-hover"><i className="fab fa-twitter"></i></a>
                                    <a href="#" style={socialIconStyle} className="social-icon-hover"><i className="fab fa-instagram"></i></a>
                                </>
                            )}
                        </div>

                        {footerData.settings?.show_download_app == 1 && (footerData.settings.google_playstore_link || footerData.settings.apple_store_link) && (
                            <div className="mt-4">
                                <p className="mb-3 fw-bold" style={{ fontSize: '13px', color: 'var(--footer-text-color, #333)' }}>Download App on Mobile :</p>
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
                        </div> {/* end inner brand div */}
                    </div> {/* end col-lg-4 */}

                    {/* Dynamic Page Categories (Information, Support, Policy, etc.) */}
                    {footerData.page_categories.map((cat) => (
                        <div key={cat.id} className="col-lg-2 col-md-6 col-6">
                            <h6 style={headingStyle}>{cat.name}</h6>
                            <ul className="list-unstyled d-flex flex-column gap-3">
                                {cat.pages.map(page => (
                                    <li key={page.id}>
                                        <Link to={`/page/${page.slug || page.id}`} style={linkStyle} className="footer-link">
                                            {page.name}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>

                {/* Membership Section */}
                {footerData.settings?.show_membership_section && (
                    (footerData.membership_logos && footerData.membership_logos.length > 0) ||
                    footerData.settings?.payment_methods_logo
                ) && (
                    <div className="row mb-5 py-4 border-top border-bottom align-items-center">
                        {footerData.membership_logos && footerData.membership_logos.length > 0 && (
                            <div className="col-lg-5 text-center text-lg-start mb-4 mb-lg-0">
                                <p className="mb-3 fw-bold text-muted small text-uppercase">We Are a Member of</p>
                                <div className="d-flex gap-4 justify-content-center justify-content-lg-start align-items-center flex-wrap">
                                    {footerData.membership_logos.map((logo) => (
                                        <img
                                            key={logo.id}
                                            src={logo.image}
                                            alt={logo.name || "Member"}
                                            style={{ height: '40px', maxWidth: '120px', objectFit: 'contain' }}
                                        />
                                    ))}
                                </div>
                            </div>
                        )}
                        {footerData.settings?.payment_methods_logo && (
                            <div className={footerData.membership_logos && footerData.membership_logos.length > 0 ? "col-lg-7 text-center text-lg-end" : "col-lg-12 text-center"}>
                                <div className={`d-flex align-items-center gap-3 flex-wrap ${footerData.membership_logos && footerData.membership_logos.length > 0 ? "justify-content-center justify-content-lg-end" : "justify-content-center"}`}>
                                    <span style={{ fontSize: '13px', color: 'var(--footer-text-color, #333)', fontWeight: 'bold' }}>Pay With</span>
                                    <div className="d-flex gap-1 flex-wrap justify-content-center">
                                        <img
                                            src={footerData.settings.payment_methods_logo}
                                            alt="Payment Methods"
                                            style={{ maxHeight: '70px', width: 'auto', maxWidth: '100%' }}
                                        />
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                )}

                <div style={{ borderTop: '0px solid #f0f0f0', paddingTop: '10px' }}>
                    <div className="row justify-content-center">
                        <div className="col-12 text-center">
                            <p className="mb-0" style={{ fontSize: '13px', color: 'var(--footer-text-color, #333)', opacity: 0.6 }}>
                                {footerData.settings?.footer_copyright_text || `Copyright © ${new Date().getFullYear()} ${footerData.settings?.website_name || "JhrBazar"}`}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                @media (max-width: 767.98px) {
                    footer.custom-footer {
                        padding-bottom: 120px !important;
                    }
                }
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

