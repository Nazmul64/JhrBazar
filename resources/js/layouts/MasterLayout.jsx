import React from 'react';
import Header from '../components/Header';
import Footer from '../components/Footer';
import MobileBottomNav from '../components/MobileBottomNav';
import { useSettings } from '../context/SettingsContext';
import FloatingCart from '../components/FloatingCart';

const MasterLayout = ({ children }) => {
    const { settings } = useSettings();

    // Ensure page starts at the top on refresh or navigation
    React.useEffect(() => {
        window.scrollTo(0, 0);
    }, []);

    const [showScrollBtn, setShowScrollBtn] = React.useState(false);

    React.useEffect(() => {
        const handleScroll = () => {
            if (window.scrollY > 300) {
                setShowScrollBtn(true);
            } else {
                setShowScrollBtn(false);
            }
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const scrollToTop = () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    };

    return (
        <div style={{ display: 'flex', flexDirection: 'column', minHeight: '100vh', fontFamily: 'var(--font-family, Arial, sans-serif)', fontSize: 'var(--font-size, 14px)' }}>
            
            <style>{`
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .scroll-to-top-btn {
                    bottom: 110px;
                    left: 40px;
                }
                .scroll-to-top-btn:hover {
                    transform: translateY(-3px);
                    background-color: #e05200 !important;
                }
                @media (max-width: 768px) {
                    .scroll-to-top-btn {
                        bottom: 165px;
                        left: 15px;
                    }
                }
            `}</style>

            {/* Offer Marquee */}
            {settings?.show_marquee && settings?.marquee_text && (
                <div style={{ backgroundColor: '#ffefc2', color: '#856404', padding: '8px 0', borderBottom: '1px solid #ffeeba', overflow: 'hidden' }}>
                    <marquee behavior="scroll" direction="left" scrollamount="6" style={{ fontWeight: 'bold', fontSize: '13px', margin: 0, padding: 0 }}>
                        {settings.marquee_text}
                    </marquee>
                </div>
            )}

            {/* Global Fluid Override */}
            {settings?.layout_style === 'fluid' && (
                <style>{`
                    .container {
                        max-width: 100% !important;
                        padding-left: 1.5rem !important;
                        padding-right: 1.5rem !important;
                    }
                `}</style>
            )}

            {/* ── Google Analytics (GA4) ── */}
            {settings?.enable_analytics && settings?.google_analytics_id && (
                <>
                    <script async src={`https://www.googletagmanager.com/gtag/js?id=${settings.google_analytics_id}`}></script>
                    <script dangerouslySetInnerHTML={{ __html: `
                        window.dataLayer = window.dataLayer || [];
                        function gtag(){dataLayer.push(arguments);}
                        gtag('js', new Date());
                        gtag('config', '${settings.google_analytics_id}');
                    `}} />
                </>
            )}

            {/* ── Facebook Pixel ── */}
            {settings?.enable_pixel && settings?.facebook_pixel_id && (
                <script dangerouslySetInnerHTML={{ __html: `
                    !function(f,b,e,v,n,t,s)
                    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                    n.queue=[];t=b.createElement(e);t.async=!0;
                    t.src=v;s=b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t,s)}(window, document,'script',
                    'https://connect.facebook.net/en_US/fbevents.js');
                    fbq('init', '${settings.facebook_pixel_id}');
                    fbq('track', 'PageView');
                `}} />
            )}

            {/* ── Google Tag Manager ── */}
            {settings?.enable_gtm && settings?.gtm_id && (
                <script dangerouslySetInnerHTML={{ __html: `
                    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;
                    j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;
                    f.parentNode.insertBefore(j,f);
                    })(window,document,'script','dataLayer','${settings.gtm_id}');
                `}} />
            )}

            {/* Header Part */}
            <Header />

            {/* Main Content (The Dynamic Part) */}
            <main style={{ flexGrow: 1, backgroundColor: '#f9f9f9', paddingBottom: '100px', minHeight: 'calc(100vh - 200px)' }}>
                <div style={{ padding: '0', animation: 'fadeIn 0.5s ease-in-out' }}>
                    {children}
                </div>
            </main>

            {/* Mobile Bottom Navigation (Only on mobile) */}
            <MobileBottomNav />

            {/* Floating Cart Widget (Only on desktop) */}
            <FloatingCart />

            {/* Footer Part */}
            <Footer />

            {showScrollBtn && (
                <button
                    onClick={scrollToTop}
                    className="scroll-to-top-btn position-fixed border-0 d-flex align-items-center justify-content-center"
                    style={{
                        zIndex: 9998,
                        backgroundColor: '#ff5c00',
                        color: '#fff',
                        width: '40px',
                        height: '40px',
                        borderRadius: '8px',
                        boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
                        cursor: 'pointer',
                        transition: 'all 0.3s ease',
                    }}
                >
                    <i className="fas fa-chevron-up" style={{ fontSize: '16px' }}></i>
                </button>
            )}
        </div>
    );
};

export default MasterLayout;
