import React, { useEffect } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import { useSettings } from '../context/SettingsContext';
import confetti from 'canvas-confetti';
import { CheckCircle, Truck, ShoppingBag, ArrowRight, Package, Calendar, MapPin } from 'lucide-react';
import { useCart } from '../context/CartContext';
import axios from 'axios';

const OrderSuccess = () => {
    const { settings } = useSettings();
    const { clearCart } = useCart();
    const mainColor = settings?.primary_color || '#ff4d4d';
    const location = useLocation();
    const navigate = useNavigate();
    
    const [fetchedOrders, setFetchedOrders] = React.useState([]);
    const orderData = location.state?.orders || fetchedOrders;

    useEffect(() => {
        const queryParams = new URLSearchParams(location.search);
        const invoice = queryParams.get('invoice');
        if (invoice && fetchedOrders.length === 0) {
            axios.get(`/api/order-details/${invoice}`)
                .then(res => {
                    if (res.data.success) {
                        setFetchedOrders(res.data.orders);
                    }
                })
                .catch(err => console.error("Error loading order details:", err));
        }
    }, [location.search, fetchedOrders.length]);

    // Clear cart on successful order landing
    useEffect(() => {
        if (orderData.length > 0) {
            clearCart();
        }
    }, [orderData, clearCart]);

    useEffect(() => {
        // Trigger fireworks effect safely
        const duration = 3.5 * 1000;
        const animationEnd = Date.now() + duration;
        const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 999 };

        const randomInRange = (min, max) => Math.random() * (max - min) + min;

        const interval = setInterval(function() {
            const timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            const particleCount = 35 * (timeLeft / duration);
            confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } });
            confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } });
        }, 250);

        confetti({
            particleCount: 120,
            spread: 70,
            origin: { y: 0.6 },
            colors: [mainColor, '#ffffff', '#ffd700'],
            zIndex: 999
        });

        return () => {
            clearInterval(interval);
            try {
                confetti.reset();
            } catch (e) {}
        };
    }, [mainColor]);

    // Data Layer: purchase
    useEffect(() => {
        if (orderData.length > 0) {
            window.dataLayer = window.dataLayer || [];
            
            const allItems = [];
            let totalValue = 0;
            const transactionId = orderData[0].invoice_number || orderData[0].id; 
            
            orderData.forEach(order => {
                totalValue += Number(order.total_amount || order.grand_total || 0);
                if (order.items) {
                    order.items.forEach(item => {
                        allItems.push({
                            item_id: String(item.product_id),
                            item_name: item.product_name,
                            price: Number(item.price),
                            quantity: Number(item.qty)
                        });
                    });
                }
            });

            window.dataLayer.push({
                event: 'purchase',
                currency: 'BDT',
                value: Number(totalValue),
                transaction_id: String(transactionId),
                items: allItems
            });
        }
    }, [orderData]);

    // Primary invoice number for tracking navigation
    const queryParams = new URLSearchParams(location.search);
    const primaryInvoice = orderData[0]?.invoice_number || queryParams.get('invoice') || '';

    return (
        <MasterLayout>
            <div className="order-success-page" style={{ 
                background: 'linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%)',
                minHeight: 'calc(100vh - 160px)',
                display: 'flex',
                alignItems: 'center',
                padding: '20px 0'
            }}>
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-lg-8 col-xl-7">
                            <div className="success-card shadow-sm border overflow-hidden" style={{
                                background: '#fff',
                                borderRadius: '18px',
                                borderColor: '#e2e8f0',
                                position: 'relative'
                            }}>
                                {/* Decorative top bar */}
                                <div style={{ height: '4px', background: mainColor }}></div>

                                <div className="card-body p-3 p-md-4">
                                    <div className="text-center mb-3">
                                        <div className="success-icon-wrapper mb-2">
                                            <div className="success-icon-bg"></div>
                                            <CheckCircle size={52} color={mainColor} strokeWidth={1.8} className="success-icon-main" />
                                        </div>
                                        
                                        <h2 className="fs-4 fw-800 mb-1" style={{ color: '#0f172a' }}>অর্ডার সফলভাবে সম্পন্ন হয়েছে!</h2>
                                        <p className="text-muted small mb-0">আপনার অর্ডারের জন্য ধন্যবাদ। আপনার কেনাকাটা আমাদের ধন্য করেছে।</p>
                                    </div>

                                    <div className="row g-3">
                                        {/* Order Info Column */}
                                        <div className="col-md-6">
                                             <div className="info-box p-3 h-100 d-flex flex-column justify-content-between" style={{ background: '#f8fafc', borderRadius: '14px', border: '1px solid #e2e8f0' }}>
                                                <div>
                                                    <h6 className="fw-700 mb-2.5 d-flex align-items-center gap-2 text-dark">
                                                        <Package size={17} color={mainColor} />
                                                        অর্ডার তথ্য
                                                    </h6>
                                                    
                                                    {orderData.length > 0 ? (
                                                        orderData.map((order, idx) => (
                                                            <div key={idx} className="order-item-summary mb-2 pb-2 border-bottom border-light last-child-no-border">
                                                                <div className="d-flex justify-content-between mb-1">
                                                                    <span className="text-secondary small">ইনভয়েস নম্বর</span>
                                                                    <span className="fw-700 text-dark small">#{order.invoice_number || order.id}</span>
                                                                </div>
                                                                <div className="d-flex justify-content-between align-items-center">
                                                                    <span className="text-secondary small">মোট পরিশোধযোগ্য</span>
                                                                    <span className="fw-800 fs-6" style={{ color: mainColor }}>৳{Number(order.grand_total || order.total_amount || 0).toLocaleString('en-BD')}</span>
                                                                </div>
                                                            </div>
                                                        ))
                                                    ) : (
                                                        <div className="text-center py-2">
                                                            <span className="text-muted small">কোনো অর্ডার তথ্য পাওয়া যায়নি</span>
                                                        </div>
                                                    )}
                                                </div>

                                                <div className="mt-2 p-2 bg-white rounded-3 border border-dashed border-primary" style={{ fontSize: '11.5px', lineHeight: '1.4' }}>
                                                    <div className="d-flex gap-2 align-items-center">
                                                        <Calendar size={14} className="text-primary flex-shrink-0" />
                                                        <span>আমরা আপনার অর্ডারটি আগামী <strong>২-৫ কর্মদিবসের</strong> মধ্যে ডেলিভারি করার চেষ্টা করব।</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Next Steps Column */}
                                        <div className="col-md-6">
                                            <div className="info-box p-3 h-100" style={{ background: '#fff', borderRadius: '14px', border: '1px solid #e2e8f0' }}>
                                                <h6 className="fw-700 mb-2.5 d-flex align-items-center gap-2 text-dark">
                                                    <MapPin size={17} color={mainColor} />
                                                    পরবর্তী ধাপ
                                                </h6>
                                                
                                                <ul className="list-unstyled d-flex flex-column gap-2 mb-0">
                                                    <li className="d-flex gap-2.5 align-items-start">
                                                        <div className="step-num" style={{ background: '#f0fdf4', color: '#22c55e' }}>১</div>
                                                        <div>
                                                            <p className="mb-0 fw-600" style={{ fontSize: '12.5px' }}>অর্ডার নিশ্চিতকরণ</p>
                                                            <span className="text-muted" style={{ fontSize: '11px', lineHeight: '1.3', display: 'block' }}>আমাদের প্রতিনিধি আপনাকে ফোন করে অর্ডারটি নিশ্চিত করবেন।</span>
                                                        </div>
                                                    </li>
                                                    <li className="d-flex gap-2.5 align-items-start">
                                                        <div className="step-num" style={{ background: '#eff6ff', color: '#3b82f6' }}>২</div>
                                                        <div>
                                                            <p className="mb-0 fw-600" style={{ fontSize: '12.5px' }}>প্যাকিং এবং শিপিং</p>
                                                            <span className="text-muted" style={{ fontSize: '11px', lineHeight: '1.3', display: 'block' }}>আপনার পণ্যটি সুন্দরভাবে প্যাক করে কুরিয়ারে হস্তান্তর করা হবে।</span>
                                                        </div>
                                                    </li>
                                                    <li className="d-flex gap-2.5 align-items-start">
                                                        <div className="step-num" style={{ background: '#fff7ed', color: '#f97316' }}>৩</div>
                                                        <div>
                                                            <p className="mb-0 fw-600" style={{ fontSize: '12.5px' }}>ডেলিভারি</p>
                                                            <span className="text-muted" style={{ fontSize: '11px', lineHeight: '1.3', display: 'block' }}>কুরিয়ার ম্যান আপনার ঠিকানায় পণ্যটি পৌঁছে দেবে।</span>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Action Buttons */}
                                    <div className="mt-3 pt-3 border-top border-light d-flex flex-sm-row flex-column gap-2.5 justify-content-center">
                                        <Link
                                            to={primaryInvoice ? `/order-tracking?invoice=${primaryInvoice}` : "/order-tracking"}
                                            state={{ invoice: primaryInvoice }}
                                            className="btn d-flex align-items-center justify-content-center gap-2 px-4 py-2.5 shadow-sm hover-up text-decoration-none"
                                            style={{ 
                                                backgroundColor: mainColor, 
                                                color: '#fff', 
                                                borderRadius: '12px',
                                                fontWeight: '700',
                                                fontSize: '14px',
                                                border: 'none'
                                            }}
                                        >
                                            <Truck size={17} />
                                            অর্ডার ট্র্যাক করুন
                                            <ArrowRight size={16} />
                                        </Link>
                                        <Link
                                            to="/"
                                            className="btn btn-light d-flex align-items-center justify-content-center gap-2 px-4 py-2.5 border hover-up text-decoration-none"
                                            style={{ 
                                                borderRadius: '12px',
                                                fontWeight: '600',
                                                fontSize: '14px',
                                                color: '#475569',
                                                backgroundColor: '#f8fafc',
                                                borderColor: '#cbd5e1'
                                            }}
                                        >
                                            <ShoppingBag size={17} />
                                            আরও কেনাকাটা করুন
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
                
                canvas {
                    pointer-events: none !important;
                }
                
                .order-success-page {
                    font-family: 'Hind Siliguri', sans-serif !important;
                }
                
                .fw-800 { font-weight: 800; }
                .fw-700 { font-weight: 700; }
                .fw-600 { font-weight: 600; }
                
                .success-icon-wrapper {
                    position: relative;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .success-icon-bg {
                    position: absolute;
                    width: 80px;
                    height: 80px;
                    background: ${mainColor}12;
                    border-radius: 50%;
                    animation: pulseSuccess 2s infinite;
                }
                
                .success-icon-main {
                    position: relative;
                    animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
                }
                
                .step-num {
                    width: 24px;
                    height: 24px;
                    border-radius: 7px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 700;
                    font-size: 12px;
                    flex-shrink: 0;
                }
                
                .hover-up {
                    transition: all 0.25s ease;
                }
                .hover-up:hover {
                    transform: translateY(-2px);
                    filter: brightness(1.05);
                }
                
                .last-child-no-border:last-child {
                    border-bottom: none !important;
                    margin-bottom: 0 !important;
                    padding-bottom: 0 !important;
                }
                
                @keyframes pulseSuccess {
                    0% { transform: scale(1); opacity: 1; }
                    100% { transform: scale(1.25); opacity: 0; }
                }
                
                @keyframes scaleIn {
                    from { transform: scale(0); opacity: 0; }
                    to { transform: scale(1); opacity: 1; }
                }
                
                .border-dashed {
                    border-style: dashed !important;
                }
            `}</style>
        </MasterLayout>
    );
};

export default OrderSuccess;
