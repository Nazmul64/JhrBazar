import React, { useState } from 'react';
import MasterLayout from '../layouts/MasterLayout';
import { Link } from 'react-router-dom';

const Cart = () => {
    const mainColor = '#57b500';
    const [cartItems, setCartItems] = useState([
        { id: 1, title: "iPhone 15 Pro Max", price: 1199.00, qty: 1, image: "https://images.unsplash.com/photo-1696446701796-da61225697cc?q=80&w=200&auto=format&fit=crop" },
        { id: 2, title: "Smart Watch Ultra", price: 121.00, qty: 1, image: "https://images.unsplash.com/photo-1544117518-30dd057a1bb2?q=80&w=200&auto=format&fit=crop" }
    ]);

    const updateQuantity = (id, delta) => {
        setCartItems(prevItems => 
            prevItems.map(item => {
                if (item.id === id) {
                    const newQty = Math.max(1, item.qty + delta);
                    return { ...item, qty: newQty };
                }
                return item;
            })
        );
    };

    const removeItem = (id) => {
        setCartItems(prevItems => prevItems.filter(item => item.id !== id));
    };

    const subtotal = cartItems.reduce((acc, item) => acc + (item.price * item.qty), 0);
    const shipping = 0;
    const total = subtotal + shipping;

    return (
        <MasterLayout>
            <div className="container py-5">
                <h3 className="fw-bold mb-5" style={{ letterSpacing: '-1px' }}>Shopping Cart ({cartItems.length} items)</h3>
                
                <div className="row g-4">
                    <div className="col-lg-8">
                        <div className="card border-0 shadow-sm" style={{ borderRadius: '20px', border: '1px solid #eee' }}>
                            <div className="card-body p-0">
                                <div className="table-responsive">
                                    <table className="table table-borderless align-middle mb-0">
                                        <thead className="bg-light border-bottom">
                                            <tr>
                                                <th className="p-4 small text-muted fw-bold">PRODUCT</th>
                                                <th className="p-4 small text-muted fw-bold text-center">QUANTITY</th>
                                                <th className="p-4 small text-muted fw-bold text-end">PRICE</th>
                                                <th className="p-4 small text-muted fw-bold text-end">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {cartItems.map(item => (
                                                <tr key={item.id} className="border-bottom">
                                                    <td className="p-4">
                                                        <div className="d-flex align-items-center gap-3">
                                                            {/* Full Width Thumbnail */}
                                                            <div style={{ width: '80px', height: '80px', backgroundColor: '#f9f9f9', borderRadius: '12px', overflow: 'hidden', border: '1px solid #f0f0f0' }}>
                                                                <img src={item.image} alt={item.title} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                                                            </div>
                                                            <div className="fw-bold" style={{ fontSize: '14px', color: '#333' }}>{item.title}</div>
                                                        </div>
                                                    </td>
                                                    <td className="p-4">
                                                        <div className="d-flex align-items-center justify-content-center">
                                                            <div className="d-flex align-items-center bg-light rounded-pill px-2 py-1 border" style={{ borderColor: '#ddd' }}>
                                                                <button onClick={() => updateQuantity(item.id, -1)} className="btn btn-sm border-0 rounded-circle qty-btn">-</button>
                                                                <span className="fw-bold px-3 text-center" style={{ minWidth: '40px' }}>{item.qty}</span>
                                                                <button onClick={() => updateQuantity(item.id, 1)} className="btn btn-sm border-0 rounded-circle qty-btn">+</button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="p-4 text-end fw-bold">${(item.price * item.qty).toFixed(2)}</td>
                                                    <td className="p-4 text-end">
                                                        <button onClick={() => removeItem(item.id)} className="btn btn-sm text-muted hover-danger" style={{ fontSize: '18px' }}>🗑️</button>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-lg-4">
                        <div className="card border-0 shadow-sm sticky-top" style={{ borderRadius: '20px', top: '100px', border: '1px solid #eee' }}>
                            <div className="card-body p-4 p-md-5">
                                <h5 className="fw-bold mb-4">Cart Summary</h5>
                                <div className="d-flex justify-content-between mb-3">
                                    <span className="text-muted">Subtotal</span>
                                    <span className="fw-bold">${subtotal.toFixed(2)}</span>
                                </div>
                                <div className="d-flex justify-content-between mb-4 pb-3 border-bottom">
                                    <span className="text-muted">Shipping</span>
                                    <span className="text-success fw-bold">FREE</span>
                                </div>
                                <div className="d-flex justify-content-between mb-5">
                                    <h4 className="fw-bold mb-0">Total</h4>
                                    <h4 className="fw-bold mb-0" style={{ color: mainColor }}>${total.toFixed(2)}</h4>
                                </div>
                                <Link to="/checkout" className="btn btn-lg w-100 text-white fw-bold py-3 shadow confirm-btn" style={{ backgroundColor: mainColor, borderRadius: '15px' }}>
                                    Proceed to Checkout
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>{`
                .qty-btn { width: 32px; height: 32px; font-weight: bold; transition: all 0.2s; }
                .qty-btn:hover { background-color: ${mainColor} !important; color: #fff !important; }
                .hover-danger:hover { color: #ff4d4d !important; transform: scale(1.2); }
                .confirm-btn:hover { background-color: #4a9a00 !important; transform: translateY(-2px); }
            `}</style>
        </MasterLayout>
    );
};

export default Cart;
