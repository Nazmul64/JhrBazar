import React from 'react';
import { Link } from 'react-router-dom';

const MobileBottomNav = () => {
    const mainColor = '#57b500';

    const navStyle = {
        position: 'fixed',
        bottom: 0,
        left: 0,
        width: '100%',
        backgroundColor: '#fff',
        boxShadow: '0 -2px 10px rgba(0,0,0,0.1)',
        display: 'flex',
        justifyContent: 'space-around',
        padding: '10px 0',
        zIndex: 10001,
        borderTop: '1px solid #eee'
    };

    const itemStyle = {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        textDecoration: 'none',
        color: '#666',
        fontSize: '11px'
    };

    return (
        <div className="d-md-none" style={navStyle}>
            <Link to="/" style={{ ...itemStyle, color: mainColor }}>
                <span style={{ fontSize: '20px' }}>🏠</span>
                <span>HOME</span>
            </Link>
            <Link to="/" style={itemStyle}>
                <span style={{ fontSize: '20px' }}>📦</span>
                <span>সব পণ্য</span>
            </Link>
            <Link to="/" style={itemStyle}>
                <span style={{ fontSize: '20px' }}>🚚</span>
                <span>ট্র্যাক</span>
            </Link>
            <Link to="/" style={itemStyle}>
                <div style={{ position: 'relative' }}>
                    <span style={{ fontSize: '20px' }}>🛒</span>
                    <span style={{ position: 'absolute', top: '-5px', right: '-8px', backgroundColor: mainColor, color: '#fff', fontSize: '9px', padding: '1px 4px', borderRadius: '50%' }}>0</span>
                </div>
                <span>CART</span>
            </Link>
            <Link to="/" style={itemStyle}>
                <span style={{ fontSize: '20px' }}>👤</span>
                <span>LOGIN</span>
            </Link>
        </div>
    );
};

export default MobileBottomNav;
