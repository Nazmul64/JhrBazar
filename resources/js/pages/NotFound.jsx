import React from 'react';
import { Link } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import { useSettings } from '../context/SettingsContext';
import { Home, Search, ArrowLeft, ShoppingBag } from 'lucide-react';

const NotFound = () => {
    const { settings } = useSettings();
    const mainColor = settings?.primary_color || '#ff4d4d';

    return (
        <MasterLayout>
            <div style={{
                background: 'linear-gradient(135deg, #f8fafc 0%, #eff6ff 50%, #fef3f2 100%)',
                minHeight: '75vh',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                padding: '40px 20px',
                fontFamily: "'Hind Siliguri', sans-serif"
            }}>
                <div style={{ textAlign: 'center', maxWidth: '560px' }}>
                    {/* Animated 404 Number */}
                    <div style={{ position: 'relative', marginBottom: '20px' }}>
                        <h1 style={{
                            fontSize: 'clamp(100px, 20vw, 180px)',
                            fontWeight: '900',
                            background: `linear-gradient(135deg, ${mainColor}, ${mainColor}88, ${mainColor}44)`,
                            WebkitBackgroundClip: 'text',
                            WebkitTextFillColor: 'transparent',
                            lineHeight: 1,
                            margin: 0,
                            letterSpacing: '-6px',
                            animation: 'float404 3s ease-in-out infinite'
                        }}>
                            404
                        </h1>
                        <div style={{
                            position: 'absolute',
                            bottom: '10px',
                            left: '50%',
                            transform: 'translateX(-50%)',
                            width: '120px',
                            height: '8px',
                            background: 'rgba(0,0,0,0.06)',
                            borderRadius: '50%',
                            animation: 'shadow404 3s ease-in-out infinite'
                        }}></div>
                    </div>

                    {/* Icon */}
                    <div style={{
                        width: '80px',
                        height: '80px',
                        borderRadius: '24px',
                        background: `${mainColor}12`,
                        display: 'inline-flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        marginBottom: '24px',
                        animation: 'pulseIcon 2s ease-in-out infinite'
                    }}>
                        <Search size={36} color={mainColor} strokeWidth={1.8} />
                    </div>

                    {/* Text */}
                    <h2 style={{
                        fontSize: '26px',
                        fontWeight: '800',
                        color: '#0f172a',
                        marginBottom: '12px'
                    }}>
                        পেজটি খুঁজে পাওয়া যায়নি
                    </h2>
                    <p style={{
                        fontSize: '16px',
                        color: '#64748b',
                        lineHeight: '1.7',
                        marginBottom: '36px',
                        maxWidth: '420px',
                        margin: '0 auto 36px'
                    }}>
                        দুঃখিত! আপনি যে পেজটি খুঁজছেন সেটি পাওয়া যায়নি। পেজটি মুছে ফেলা হয়েছে, নাম পরিবর্তন করা হয়েছে, অথবা সাময়িকভাবে অনুপলব্ধ।
                    </p>

                    {/* Action Buttons */}
                    <div style={{
                        display: 'flex',
                        gap: '14px',
                        justifyContent: 'center',
                        flexWrap: 'wrap'
                    }}>
                        <Link
                            to="/"
                            style={{
                                display: 'inline-flex',
                                alignItems: 'center',
                                gap: '10px',
                                padding: '14px 32px',
                                background: mainColor,
                                color: '#fff',
                                borderRadius: '14px',
                                fontWeight: '700',
                                fontSize: '15px',
                                textDecoration: 'none',
                                boxShadow: `0 8px 24px ${mainColor}30`,
                                transition: 'all 0.3s ease'
                            }}
                            onMouseEnter={(e) => {
                                e.currentTarget.style.transform = 'translateY(-3px)';
                                e.currentTarget.style.boxShadow = `0 12px 32px ${mainColor}40`;
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.transform = 'translateY(0)';
                                e.currentTarget.style.boxShadow = `0 8px 24px ${mainColor}30`;
                            }}
                        >
                            <Home size={18} />
                            হোমপেজে ফিরুন
                        </Link>
                        <Link
                            to="/products"
                            style={{
                                display: 'inline-flex',
                                alignItems: 'center',
                                gap: '10px',
                                padding: '14px 32px',
                                background: '#fff',
                                color: '#334155',
                                borderRadius: '14px',
                                fontWeight: '600',
                                fontSize: '15px',
                                textDecoration: 'none',
                                border: '1.5px solid #e2e8f0',
                                transition: 'all 0.3s ease'
                            }}
                            onMouseEnter={(e) => {
                                e.currentTarget.style.transform = 'translateY(-3px)';
                                e.currentTarget.style.borderColor = mainColor;
                                e.currentTarget.style.color = mainColor;
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.transform = 'translateY(0)';
                                e.currentTarget.style.borderColor = '#e2e8f0';
                                e.currentTarget.style.color = '#334155';
                            }}
                        >
                            <ShoppingBag size={18} />
                            প্রোডাক্ট দেখুন
                        </Link>
                    </div>

                    {/* Browser back link */}
                    <button
                        onClick={() => window.history.back()}
                        style={{
                            marginTop: '28px',
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '6px',
                            background: 'none',
                            border: 'none',
                            color: '#94a3b8',
                            fontSize: '14px',
                            cursor: 'pointer',
                            fontWeight: '500',
                            transition: 'color 0.2s'
                        }}
                        onMouseEnter={(e) => e.currentTarget.style.color = mainColor}
                        onMouseLeave={(e) => e.currentTarget.style.color = '#94a3b8'}
                    >
                        <ArrowLeft size={16} />
                        অথবা আগের পেজে ফিরে যান
                    </button>
                </div>
            </div>

            <style>{`
                @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');

                @keyframes float404 {
                    0%, 100% { transform: translateY(0px); }
                    50% { transform: translateY(-15px); }
                }

                @keyframes shadow404 {
                    0%, 100% { width: 120px; opacity: 0.6; }
                    50% { width: 90px; opacity: 0.3; }
                }

                @keyframes pulseIcon {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.08); }
                }
            `}</style>
        </MasterLayout>
    );
};

export default NotFound;
