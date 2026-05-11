import React, { useState } from 'react';
import MasterLayout from '../layouts/MasterLayout';
import { useSettings } from '../context/SettingsContext';
import axios from 'axios';
import { toast } from 'react-hot-toast';

const OrderTracking = () => {
    const { settings } = useSettings();
    const mainColor = settings?.primary_color || '#001fcc';
    const [invoiceNo, setInvoiceNo] = useState('');
    const [orderInfo, setOrderInfo] = useState(null);
    const [loading, setLoading] = useState(false);

    const handleTrack = async (e) => {
        e.preventDefault();
        if (!invoiceNo) {
            toast.error("অনুগ্রহ করে ইনভয়েস নম্বর লিখুন");
            return;
        }

        setLoading(true);
        try {
            const res = await axios.get(`/api/track-order/${invoiceNo}`);
            if (res.data.success) {
                setOrderInfo(res.data.data);
            } else {
                toast.error(res.data.message);
                setOrderInfo(null);
            }
        } catch (error) {
            toast.error("অর্ডার ট্র্যাক করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।");
            setOrderInfo(null);
        } finally {
            setLoading(false);
        }
    };

    const getStatusText = (status) => {
        switch (status) {
            case 'completed': return 'সম্পন্ন হয়েছে';
            case 'draft': return 'পেন্ডিং (অপেক্ষমান)';
            case 'cancelled': return 'বাতিল করা হয়েছে';
            default: return status;
        }
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'completed': return '#22c55e';
            case 'draft': return '#f59e0b';
            case 'cancelled': return '#ef4444';
            default: return '#6b7280';
        }
    };

    return (
        <MasterLayout>
            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-md-8 col-lg-6">
                        <div className="card border-0 shadow-lg" style={{ borderRadius: '20px', overflow: 'hidden' }}>
                            <div className="p-4 text-white text-center" style={{ backgroundColor: mainColor }}>
                                <h4 className="fw-bold mb-0">অর্ডার ট্র্যাকিং</h4>
                                <p className="small mb-0 opacity-75">আপনার ইনভয়েস নম্বর দিয়ে অর্ডারের অবস্থা জানুন</p>
                            </div>
                            
                            <div className="card-body p-4 p-md-5">
                                <form onSubmit={handleTrack} className="mb-4">
                                    <div className="mb-3">
                                        <label className="fw-bold mb-2 small">ইনভয়েস নম্বর (যেমন: RC000001)</label>
                                        <div className="input-group shadow-sm" style={{ borderRadius: '12px', overflow: 'hidden' }}>
                                            <input 
                                                type="text" 
                                                className="form-control border-0 bg-light p-3"
                                                placeholder="আপনার ইনভয়েস নম্বরটি লিখুন..."
                                                value={invoiceNo}
                                                onChange={(e) => setInvoiceNo(e.target.value)}
                                                style={{ fontSize: '15px' }}
                                            />
                                            <button 
                                                className="btn px-4 fw-bold text-white"
                                                type="submit"
                                                disabled={loading}
                                                style={{ backgroundColor: mainColor, transition: 'all 0.3s' }}
                                            >
                                                {loading ? 'লোড হচ্ছে...' : 'সার্চ করুন'}
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                {orderInfo && (
                                    <div className="order-result-animate mt-5 pt-4 border-top">
                                        <h6 className="fw-bold mb-4 d-flex align-items-center gap-2">
                                            <span className="badge p-2 rounded-circle" style={{ backgroundColor: mainColor + '20', color: mainColor }}>📋</span>
                                            অর্ডার ডিটেইলস
                                        </h6>
                                        
                                        <div className="bg-light p-4 rounded-3 shadow-sm border">
                                            <div className="row g-3">
                                                <div className="col-6">
                                                    <div className="small text-muted mb-1">ইনভয়েস:</div>
                                                    <div className="fw-bold text-dark">{orderInfo.invoice_number}</div>
                                                </div>
                                                <div className="col-6">
                                                    <div className="small text-muted mb-1">অর্ডার তারিখ:</div>
                                                    <div className="fw-bold text-dark">{orderInfo.created_at}</div>
                                                </div>
                                                <div className="col-12">
                                                    <div className="small text-muted mb-1">বর্তমান অবস্থা:</div>
                                                    <div className="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill fw-bold text-white shadow-sm" style={{ backgroundColor: getStatusColor(orderInfo.status), fontSize: '13px' }}>
                                                        <span className="dot"></span> {getStatusText(orderInfo.status)}
                                                    </div>
                                                </div>
                                                <div className="col-6">
                                                    <div className="small text-muted mb-1">পেমেন্ট পদ্ধতি:</div>
                                                    <div className="fw-bold text-dark text-uppercase">{orderInfo.payment_method}</div>
                                                </div>
                                                <div className="col-6">
                                                    <div className="small text-muted mb-1">মোট টাকা:</div>
                                                    <div className="fw-bold" style={{ color: '#ff4d4d', fontSize: '18px' }}>৳{Number(orderInfo.grand_total).toLocaleString('en-BD')}</div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Simple Progress Bar */}
                                        <div className="mt-5">
                                            <h6 className="fw-bold mb-3 small text-muted text-uppercase tracking-wider">অর্ডার প্রগ্রেস:</h6>
                                            <div className="tracking-steps">
                                                <div className={`step ${['draft', 'completed'].includes(orderInfo.status) ? 'active' : ''}`}>
                                                    <div className="step-icon">📦</div>
                                                    <div className="step-label">পেন্ডিং</div>
                                                </div>
                                                <div className={`step ${orderInfo.status === 'completed' ? 'active' : ''}`}>
                                                    <div className="step-icon">🚚</div>
                                                    <div className="step-label">ডেলিভারি চলছে</div>
                                                </div>
                                                <div className={`step ${orderInfo.status === 'completed' ? 'active' : ''}`}>
                                                    <div className="step-icon">✅</div>
                                                    <div className="step-label">সম্পন্ন</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .order-result-animate {
                    animation: fadeInDown 0.5s ease-out;
                }
                @keyframes fadeInDown {
                    from { opacity: 0; transform: translateY(-20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .tracking-steps {
                    display: flex;
                    justify-content: space-between;
                    position: relative;
                    margin-top: 30px;
                }
                .tracking-steps::before {
                    content: '';
                    position: absolute;
                    top: 20px;
                    left: 0;
                    right: 0;
                    height: 4px;
                    background-color: #e5e7eb;
                    z-index: 1;
                }
                .step {
                    position: relative;
                    z-index: 2;
                    text-align: center;
                    width: 80px;
                }
                .step-icon {
                    width: 40px;
                    height: 40px;
                    background-color: #fff;
                    border: 4px solid #e5e7eb;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 10px;
                    font-size: 18px;
                    transition: all 0.3s;
                }
                .step.active .step-icon {
                    border-color: ${mainColor};
                    background-color: ${mainColor}10;
                    transform: scale(1.1);
                }
                .step-label {
                    font-size: 11px;
                    font-weight: bold;
                    color: #9ca3af;
                }
                .step.active .step-label {
                    color: ${mainColor};
                }
                .dot {
                    width: 8px;
                    height: 8px;
                    background-color: #fff;
                    border-radius: 50%;
                    display: inline-block;
                    animation: pulse 1.5s infinite;
                }
                @keyframes pulse {
                    0% { opacity: 1; transform: scale(1); }
                    50% { opacity: 0.5; transform: scale(1.2); }
                    100% { opacity: 1; transform: scale(1); }
                }
            `}</style>
        </MasterLayout>
    );
};

export default OrderTracking;
