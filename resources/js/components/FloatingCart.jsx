import React from 'react';
import { Link } from 'react-router-dom';
import { useCart } from '../context/CartContext';
import { useSettings } from '../context/SettingsContext';

const FloatingCart = () => {
    const { cartCount, cartTotal } = useCart();
    const { settings } = useSettings();
    const mainColor = settings?.primary_color || window.initialSettings?.primary_color || '#57b500';

    return (
        <Link to="/cart" className="floating-cart-widget text-decoration-none">
            <div className="cart-top" style={{ backgroundColor: mainColor }}>
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="lucide lucide-shopping-bag text-white">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <span className="cart-count-text">{cartCount} {cartCount === 1 ? 'Item' : 'Items'}</span>
            </div>
            <div className="cart-bottom">
                <span className="cart-price-text">৳{Number(cartTotal).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
            </div>

            <style>{`
                .floating-cart-widget {
                    position: fixed;
                    right: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    z-index: 9999;
                    display: flex;
                    flex-direction: column;
                    width: 75px;
                    border-radius: 8px 0 0 8px;
                    box-shadow: -3px 0 12px rgba(0,0,0,0.15);
                    overflow: hidden;
                    transition: all 0.3s ease;
                    cursor: pointer;
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    border-right: none;
                }
                .floating-cart-widget:hover {
                    transform: translateY(-50%) translateX(-4px);
                    box-shadow: -5px 0 18px rgba(0,0,0,0.2);
                }
                .cart-top {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    padding: 10px 4px 6px 4px;
                    color: #fff;
                    gap: 3px;
                }
                .cart-count-text {
                    font-size: 11px;
                    font-weight: 600;
                    text-align: center;
                    white-space: nowrap;
                    text-transform: uppercase;
                    letter-spacing: 0.2px;
                }
                .cart-bottom {
                    background-color: #fff;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 8px 4px;
                    border-top: 1px solid rgba(0,0,0,0.05);
                }
                .cart-price-text {
                    font-size: 11px;
                    font-weight: 700;
                    color: #333;
                    text-align: center;
                }
                @media (max-width: 768px) {
                    .floating-cart-widget {
                        display: none !important;
                    }
                }
            `}</style>
        </Link>
    );
};

export default FloatingCart;
