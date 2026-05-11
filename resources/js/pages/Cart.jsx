import React from 'react';
import MasterLayout from '../layouts/MasterLayout';
import { Link } from 'react-router-dom';
import { useCart } from '../context/CartContext';

const Cart = () => {
    const mainColor = '#57b500';
    const { cartItems, removeFromCart, updateQuantity, cartTotal } = useCart();

    const shipping = 0;
    const total = cartTotal + shipping;

    return (
        <MasterLayout>
            <div className="container py-4 py-md-5">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <h3 className="fw-bold m-0" style={{ letterSpacing: '-0.5px' }}>
                        শপিং কার্ট <span className="text-muted" style={{ fontSize: '16px' }}>({cartItems.length})</span>
                    </h3>
                    <Link to="/" className="btn btn-sm text-decoration-none fw-bold" style={{ color: mainColor }}>
                        ← কেনাকাটা চালিয়ে যান
                    </Link>
                </div>

                {cartItems.length === 0 ? (
                    <div className="text-center py-5 bg-white rounded shadow-sm border">
                        <div style={{ fontSize: '60px', marginBottom: '20px' }}>🛒</div>
                        <h5 className="text-muted mb-3">আপনার কার্টে কোনো পণ্য নেই</h5>
                        <Link to="/" className="btn btn-lg text-white fw-bold px-5" style={{ backgroundColor: mainColor, borderRadius: '30px' }}>
                            কেনাকাটা শুরু করুন
                        </Link>
                    </div>
                ) : (
                    <div className="row g-4">
                        <div className="col-lg-8">
                            {/* Desktop View Table */}
                            <div className="d-none d-md-block card border-0 shadow-sm overflow-hidden" style={{ borderRadius: '15px' }}>
                                <table className="table align-middle mb-0">
                                    <thead className="bg-light">
                                        <tr>
                                            <th className="p-4 small text-muted fw-bold">পণ্য</th>
                                            <th className="p-4 small text-muted fw-bold text-center">পরিমাণ</th>
                                            <th className="p-4 small text-muted fw-bold text-end">মোট</th>
                                            <th className="p-4 small text-muted fw-bold text-end"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {cartItems.map(item => (
                                            <tr key={item.uid} className="border-bottom">
                                                <td className="p-4">
                                                    <div className="d-flex align-items-center gap-3">
                                                        <div className="rounded border overflow-hidden" style={{ width: '70px', height: '70px' }}>
                                                            <img src={item.image} alt={item.title} className="w-100 h-100 object-fit-cover" />
                                                        </div>
                                                        <div>
                                                            <div className="fw-bold text-dark mb-1" style={{ fontSize: '14px' }}>{item.title}</div>
                                                            <div className="text-muted small">৳{Number(item.price).toLocaleString()}</div>
                                                            {(item.color || item.size) && (
                                                                <div className="text-muted small mt-1" style={{ fontSize: '10px' }}>
                                                                    {item.color && <span className="me-2">কালার: {item.color}</span>}
                                                                    {item.size && <span>সাইজ: {item.size}</span>}
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="p-4 text-center">
                                                    <div className="d-inline-flex align-items-center bg-light rounded-pill border">
                                                        <button onClick={() => updateQuantity(item.uid, -1)} className="btn btn-sm border-0 qty-btn">-</button>
                                                        <span className="fw-bold px-3">{item.qty}</span>
                                                        <button onClick={() => updateQuantity(item.uid, 1)} className="btn btn-sm border-0 qty-btn">+</button>
                                                    </div>
                                                </td>
                                                <td className="p-4 text-end fw-bold">
                                                    ৳{Number(item.price * item.qty).toLocaleString()}
                                                </td>
                                                <td className="p-4 text-end">
                                                    <button onClick={() => removeFromCart(item.uid)} className="btn btn-sm text-muted hover-danger">🗑️</button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Mobile View Cards */}
                            <div className="d-md-none d-flex flex-column gap-3">
                                {cartItems.map(item => (
                                    <div key={item.uid} className="card border-0 shadow-sm p-3" style={{ borderRadius: '15px' }}>
                                        <div className="d-flex gap-3">
                                            <div className="rounded border overflow-hidden" style={{ width: '80px', height: '80px', flexShrink: 0 }}>
                                                <img src={item.image} alt={item.title} className="w-100 h-100 object-fit-cover" />
                                            </div>
                                            <div className="flex-grow-1">
                                                <div className="d-flex justify-content-between align-items-start gap-2">
                                                    <div className="fw-bold text-dark" style={{ fontSize: '14px', lineHeight: '1.4' }}>{item.title}</div>
                                                    <button onClick={() => removeFromCart(item.uid)} className="btn btn-sm p-0 text-muted">✕</button>
                                                </div>
                                                <div className="text-muted small mb-1">৳{Number(item.price).toLocaleString()} / ইউনিট</div>
                                                {(item.color || item.size) && (
                                                    <div className="text-muted small mb-2" style={{ fontSize: '10px' }}>
                                                        {item.color && <span className="me-2">কালার: {item.color}</span>}
                                                        {item.size && <span>সাইজ: {item.size}</span>}
                                                    </div>
                                                )}
                                                
                                                <div className="d-flex justify-content-between align-items-center mt-2">
                                                    <div className="d-flex align-items-center bg-light rounded-pill border">
                                                        <button onClick={() => updateQuantity(item.uid, -1)} className="btn btn-sm border-0 py-0 px-2 fw-bold" style={{ fontSize: '18px' }}>-</button>
                                                        <span className="fw-bold px-2" style={{ fontSize: '14px' }}>{item.qty}</span>
                                                        <button onClick={() => updateQuantity(item.uid, 1)} className="btn btn-sm border-0 py-0 px-2 fw-bold" style={{ fontSize: '18px' }}>+</button>
                                                    </div>
                                                    <div className="fw-bold" style={{ color: mainColor }}>
                                                        ৳{Number(item.price * item.qty).toLocaleString()}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="col-lg-4">
                            <div className="card border-0 shadow-sm sticky-top" style={{ borderRadius: '15px', top: '100px' }}>
                                <div className="card-body p-4">
                                    <h5 className="fw-bold mb-4">সারসংক্ষেপ</h5>
                                    <div className="d-flex justify-content-between mb-3 text-muted">
                                        <span>সাব-টোটাল</span>
                                        <span className="fw-bold text-dark">৳{Number(cartTotal).toLocaleString()}</span>
                                    </div>

                                    <div className="d-flex justify-content-between mb-4">
                                        <h4 className="fw-bold mb-0">সর্বমোট</h4>
                                        <h4 className="fw-bold mb-0" style={{ color: mainColor }}>৳{Number(total).toLocaleString()}</h4>
                                    </div>
                                    <Link to="/checkout" className="btn btn-lg w-100 text-white fw-bold py-3 shadow-sm mb-3" style={{ backgroundColor: mainColor, borderRadius: '12px' }}>
                                        চেকআউট-এ যান
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
            <style>{`
                .qty-btn { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-weight: bold; }
                .qty-btn:hover { background-color: #eee !important; }
                .hover-danger:hover { color: #ff4d4d !important; transform: scale(1.1); }
                .object-fit-cover { object-fit: cover; }
            `}</style>
        </MasterLayout>
    );
};

export default Cart;
