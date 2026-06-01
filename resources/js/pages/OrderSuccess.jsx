import React, { useEffect } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import { useSettings } from '../context/SettingsContext';
import confetti from 'canvas-confetti';
import { CheckCircle, Truck, ShoppingBag, ArrowRight, Package, Calendar, MapPin } from 'lucide-react';
import { useCart } from '../context/CartContext';

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

    // Redirect to home if no order data and accessed directly
    useEffect(() => {
        if (orderData.length === 0 && !location.state?.fromCheckout && !new URLSearchParams(location.search).get('invoice')) {
            // navigate('/');
        } else if (orderData.length > 0) {
            clearCart();
        }
    }, [orderData, navigate, location.state, location.search, clearCart]);

    useEffect(() => {
        // Trigger fireworks effect
        const duration = 4 * 1000;
        const animationEnd = Date.now() + duration;
        const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 1000 };

        const randomInRange = (min, max) => Math.random() * (max - min) + min;

        const interval = setInterval(function() {
            const timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            const particleCount = 40 * (timeLeft / duration);
            confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } });
            confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } });
        }, 250);

        confetti({
            particleCount: 150,
            spread: 70,
            origin: { y: 0.6 },
            colors: [mainColor, '#ffffff', '#ffd700']
        });

        return () => clearInterval(interval);
    }, [mainColor]);

    // Data Layer: purchase
    useEffect(() => {
        if (orderData.length > 0) {
            window.dataLayer = window.dataLayer || [];
            
            // Collect all items from all orders
            const allItems = [];
            let totalValue = 0;
            // Use the first invoice number as the primary transaction ID for the event
            const transactionId = orderData[0].invoice_number || orderData[0].id; 
            
            orderData.forEach(order => {
                totalValue += Number(order.total_amount);
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

    return (
        <MasterLayout>
            <div className="order-success-page" style={{ 
                background: 'linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%)',
                minHeight: '80vh',
                display: 'flex',
                alignItems: 'center',
                padding: '40px 0'
            }}>
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-lg-8">
                            <div className="success-card shadow-lg border-0 overflow-hidden" style={{
                                background: '#fff',
                                borderRadius: '24px',
                                position: 'relative'
                            }}>
                                {/* Decorative top bar */}
                                <div style={{ height: '6px', background: mainColor }}></div>

                                <div className="card-body p-4 p-md-5">
                                    <div className="text-center mb-5">
                                        <div className="success-icon-wrapper mb-4">
                                            <div className="success-icon-bg"></div>
                                            <CheckCircle size={80} color={mainColor} strokeWidth={1.5} className="success-icon-main" />
                                        </div>
                                        
                                        <h1 className="display-6 fw-800 mb-2" style={{ color: '#0f172a' }}>অর্ডার সফলভাবে সম্পন্ন হয়েছে!</h1>
                                        <p className="text-muted fs-5">আপনার অর্ডারের জন্য ধন্যবাদ। আপনার কেনাকাটা আমাদের ধন্য করেছে।</p>
                                    </div>

                                    <div className="row g-4">
                                        {/* Order Info Column */}
                                        <div className="col-md-6">
                                            <div className="info-box p-4 h-100" style={{ background: '#f8fafc', borderRadius: '18px', border: '1px solid #e2e8f0' }}>
                                                <h5 className="fw-700 mb-4 d-flex align-items-center gap-2">
                                                    <Package size={20} color={mainColor} />
                                                    অর্ডার তথ্য
                                                </h5>
                                                
                                                {orderData.length > 0 ? (
                                                    orderData.map((order, idx) => (
                                                        <div key={idx} className="order-item-summary mb-3 pb-3 border-bottom border-light last-child-no-border">
                                                            <div className="d-flex justify-content-between mb-2">
                                                                <span className="text-secondary small">ইনভয়েস নম্বর</span>
                                                                <span className="fw-700 text-dark">#{order.invoice_number || order.id}</span>
                                                            </div>
                                                            <div className="d-flex justify-content-between">
                                                                <span className="text-secondary small">মোট পরিশোধযোগ্য</span>
                                                                <span className="fw-800 fs-5" style={{ color: mainColor }}>৳{Number(order.grand_total).toLocaleString('en-BD')}</span>
                                                            </div>
                                                        </div>
                                                    ))
                                                ) : (
                                                    <div className="text-center py-3">
                                                        <span className="text-muted small">কোনো অর্ডার তথ্য পাওয়া যায়নি</span>
                                                    </div>
                                                )}

                                                <div className="mt-4 p-3 bg-white rounded-3 border border-dashed border-primary" style={{ fontSize: '13px' }}>
                                                    <div className="d-flex gap-2">
                                                        <Calendar size={16} className="text-primary flex-shrink-0" />
                                                        <span>আমরা আপনার অর্ডারটি আগামী <strong>২-৫ কর্মদিবসের</strong> মধ্যে ডেলিভারি করার চেষ্টা করব।</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Next Steps Column */}
                                        <div className="col-md-6">
                                            <div className="info-box p-4 h-100" style={{ background: '#fff', borderRadius: '18px', border: '1px solid #e2e8f0' }}>
                                                <h5 className="fw-700 mb-4 d-flex align-items-center gap-2">
                                                    <MapPin size={20} color={mainColor} />
                                                    পরবর্তী ধাপ
                                                </h5>
                                                
                                                <ul className="list-unstyled d-flex flex-column gap-4">
                                                    <li className="d-flex gap-3">
                                                        <div className="step-num" style={{ background: '#f0fdf4', color: '#22c55e' }}>১</div>
                                                        <div>
                                                            <p className="mb-0 fw-600 small">অর্ডার নিশ্চিতকরণ</p>
                                                            <span className="text-muted extra-small">আমাদের প্রতিনিধি আপনাকে ফোন করে অর্ডারটি নিশ্চিত করবেন।</span>
                                                        </div>
                                                    </li>
                                                    <li className="d-flex gap-3">
                                                        <div className="step-num" style={{ background: '#eff6ff', color: '#3b82f6' }}>২</div>
                                                        <div>
                                                            <p className="mb-0 fw-600 small">প্যাকিং এবং শিপিং</p>
                                                            <span className="text-muted extra-small">আপনার পণ্যটি সুন্দরভাবে প্যাক করে কুরিয়ারে হস্তান্তর করা হবে।</span>
                                                        </div>
                                                    </li>
                                                    <li className="d-flex gap-3">
                                                        <div className="step-num" style={{ background: '#fff7ed', color: '#f97316' }}>৩</div>
                                                        <div>
                                                            <p className="mb-0 fw-600 small">ডেলিভারি</p>
                                                            <span className="text-muted extra-small">কুরিয়ার ম্যান আপনার ঠিকানায় পণ্যটি পৌঁছে দেবে।</span>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Actions */}
                                    <div className="mt-5 pt-4 border-top border-light d-flex flex-sm-row flex-column gap-3 justify-content-center">
                                        <Link
                                            to="/order-tracking"
                                            className="btn btn-lg d-flex align-items-center justify-content-center gap-2 px-5 py-3 shadow-sm hover-up"
                                            style={{ 
                                                backgroundColor: mainColor, 
                                                color: '#fff', 
                                                borderRadius: '16px',
                                                fontWeight: '700',
                                                border: 'none'
                                            }}
                                        >
                                            <Truck size={20} />
                                            অর্ডার ট্র্যাক করুন
                                            <ArrowRight size={18} />
                                        </Link>
                                        <Link
                                            to="/"
                                            className="btn btn-lg btn-light d-flex align-items-center justify-content-center gap-2 px-5 py-3 border hover-up"
                                            style={{ 
                                                borderRadius: '16px',
                                                fontWeight: '600',
                                                color: '#475569'
                                            }}
                                        >
                                            <ShoppingBag size={20} />
                                            আরও কেনাকাটা করুন
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            
                            {/* Help text */}
                            <div className="text-center mt-4">
                                <p className="text-muted small">কোনো জিজ্ঞাসা আছে? আমাদের কল করুন: <strong>{settings?.phone || '01XXXXXXXXX'}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
                
                .order-success-page {
                    font-family: 'Hind Siliguri', sans-serif !important;
                }
                
                .fw-800 { font-weight: 800; }
                .fw-700 { font-weight: 700; }
                .fw-600 { font-weight: 600; }
                
                .extra-small { font-size: 11px; }
                
                .success-icon-wrapper {
                    position: relative;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .success-icon-bg {
                    position: absolute;
                    width: 140px;
                    height: 140px;
                    background: ${mainColor}10;
                    border-radius: 50%;
                    animation: pulseSuccess 2s infinite;
                }
                
                .success-icon-main {
                    position: relative;
                    animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
                }
                
                .step-num {
                    width: 32px;
                    height: 32px;
                    border-radius: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 800;
                    font-size: 14px;
                    flex-shrink: 0;
                }
                
                .hover-up {
                    transition: all 0.3s ease;
                }
                .hover-up:hover {
                    transform: translateY(-3px);
                    filter: brightness(1.1);
                }
                
                .last-child-no-border:last-child {
                    border-bottom: none !important;
                    margin-bottom: 0 !important;
                    padding-bottom: 0 !important;
                }
                
                @keyframes pulseSuccess {
                    0% { transform: scale(1); opacity: 1; }
                    100% { transform: scale(1.3); opacity: 0; }
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
