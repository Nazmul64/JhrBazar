import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useCart } from '../context/CartContext';

const MobileBottomNav = () => {
    const mainColor = '#57b500';
    const location  = useLocation();
    const { cartCount } = useCart();

    const itemStyle = (path) => ({
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        textDecoration: 'none',
        color: location.pathname === path ? mainColor : '#666',
        fontSize: '10px',
        fontWeight: 'bold',
        flex: 1,
        padding: '6px 0',
    });

    return (
        <div className="d-md-none" style={{
            position: 'fixed', bottom: 0, left: 0, width: '100%',
            backgroundColor: '#fff',
            boxShadow: '0 -2px 15px rgba(0,0,0,0.12)',
            display: 'flex', justifyContent: 'space-around',
            zIndex: 10001, borderTop: '1px solid #eee',
            paddingBottom: 'env(safe-area-inset-bottom)'
        }}>
            <Link to="/" style={itemStyle('/')}>
                <span style={{ fontSize: '22px' }}>🏠</span>
                <span>HOME</span>
            </Link>
            <Link to="/products-all/all" style={itemStyle('/products-all/all')}>
                <span style={{ fontSize: '22px' }}>📦</span>
                <span>সব পণ্য</span>
            </Link>
            <Link to="/products-all/best-deal" style={itemStyle('/products-all/best-deal')}>
                <span style={{ fontSize: '22px' }}>🏷️</span>
                <span>ডিল</span>
            </Link>
            <Link to="/cart" style={itemStyle('/cart')}>
                <div style={{ position: 'relative' }}>
                    <span style={{ fontSize: '22px' }}>🛒</span>
                    {cartCount > 0 && (
                        <span style={{
                            position: 'absolute', top: '-5px', right: '-8px',
                            backgroundColor: '#ff4d4d', color: '#fff',
                            fontSize: '9px', padding: '1px 5px',
                            borderRadius: '50%', fontWeight: 'bold',
                            minWidth: '16px', textAlign: 'center'
                        }}>{cartCount}</span>
                    )}
                </div>
                <span>CART</span>
            </Link>
            <Link to="/login" style={itemStyle('/login')}>
                <span style={{ fontSize: '22px' }}>👤</span>
                <span>LOGIN</span>
            </Link>
        </div>
    );
};

export default MobileBottomNav;
