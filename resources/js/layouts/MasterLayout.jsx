import React from 'react';
import Header from '../components/Header';
import Footer from '../components/Footer';
import MobileBottomNav from '../components/MobileBottomNav';
import { useSettings } from '../context/SettingsContext';

const MasterLayout = ({ children }) => {
    const { settings } = useSettings();

    return (
        <div style={{ display: 'flex', flexDirection: 'column', minHeight: '100vh', fontFamily: 'var(--font-family, Arial, sans-serif)', fontSize: 'var(--font-size, 14px)' }}>
            
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

            {/* Header Part */}
            <Header />

            {/* Main Content (The Dynamic Part) */}
            <main style={{ flexGrow: 1, backgroundColor: '#f9f9f9', paddingBottom: '70px' }}>
                <div style={{ padding: '0' }}>
                    {children}
                </div>
            </main>

            {/* Mobile Bottom Navigation (Only on mobile) */}
            <MobileBottomNav />

            {/* Footer Part */}
            <Footer />
        </div>
    );
};

export default MasterLayout;
