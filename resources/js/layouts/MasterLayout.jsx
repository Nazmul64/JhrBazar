import React from 'react';
import Header from '../components/Header';
import Footer from '../components/Footer';
import MobileBottomNav from '../components/MobileBottomNav';

const MasterLayout = ({ children }) => {
    return (
        <div style={{ display: 'flex', flexDirection: 'column', minHeight: '100vh', fontFamily: 'Arial, sans-serif' }}>
            {/* Header Part */}
            <Header />

            {/* Main Content (The Dynamic Part) */}
            <main style={{ flexGrow: 1, backgroundColor: '#f9f9f9', paddingBottom: '70px' }}>
                {children}
            </main>

            {/* Mobile Bottom Navigation (Only on mobile) */}
            <MobileBottomNav />

            {/* Footer Part */}
            <Footer />
        </div>
    );
};

export default MasterLayout;
