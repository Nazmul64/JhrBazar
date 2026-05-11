import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import { useSettings } from '../context/SettingsContext';

const OrderSuccess = () => {
    const { settings } = useSettings();
    const mainColor = settings?.primary_color || '#001fcc';
    const location = useLocation();
    const orderData = location.state?.orders || [];

    return (
        <MasterLayout>
            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-md-8 col-lg-6 text-center">
                        <div className="mb-4">
                            <div 
                                className="d-inline-flex align-items-center justify-content-center"
                                style={{
                                    width: '100px',
                                    height: '100px',
                                    borderRadius: '50%',
                                    backgroundColor: '#f0fdf4',
                                    color: '#22c55e',
                                    fontSize: '50px'
                                }}
                            >
                                ✓
                            </div>
                        </div>
                        
                        <h2 className="fw-bold mb-3">অর্ডার সফলভাবে সম্পন্ন হয়েছে!</h2>
                        <p className="text-muted mb-4">
                            আমাদের সাথে কেনাকাটা করার জন্য ধন্যবাদ। আপনার অর্ডারটি গ্রহণ করা হয়েছে এবং খুব শীঘ্রই প্রসেস করা হবে।
                        </p>

                        {orderData.length > 0 && (
                            <div className="card border-0 shadow-sm mb-4" style={{ borderRadius: '15px', backgroundColor: '#f8f9fa' }}>
                                <div className="card-body p-4 text-start">
                                    <h6 className="fw-bold mb-3">অর্ডার সারসংক্ষেপ:</h6>
                                    {orderData.map((order, idx) => (
                                        <div key={idx} className="mb-2 pb-2 border-bottom last-child-no-border">
                                            <div className="d-flex justify-content-between align-items-center">
                                                <span className="small text-muted">অর্ডার আইডি:</span>
                                                <span className="fw-bold text-dark">#{order.id}</span>
                                            </div>
                                            <div className="d-flex justify-content-between align-items-center">
                                                <span className="small text-muted">মোট টাকা:</span>
                                                <span className="fw-bold" style={{ color: '#ff4d4d' }}>৳{Number(order.grand_total).toLocaleString('en-BD')}</span>
                                            </div>
                                        </div>
                                    ))}
                                    <div className="mt-3 p-2 bg-white rounded border border-warning" style={{ fontSize: '13px' }}>
                                        <span className="me-2">ℹ️</span> 
                                        অনুগ্রহ করে অর্ডার আইডিটি সংরক্ষণ করুন। আপনি এটি ব্যবহার করে আপনার অর্ডার ট্র্যাক করতে পারবেন।
                                    </div>
                                </div>
                            </div>
                        )}

                        <div className="d-flex flex-column gap-3 mt-4">
                            <Link 
                                to="/order-tracking" 
                                className="btn btn-lg text-white fw-bold shadow-sm"
                                style={{ backgroundColor: mainColor, borderRadius: '10px' }}
                            >
                                অর্ডার ট্র্যাক করুন
                            </Link>
                            <Link 
                                to="/" 
                                className="btn btn-lg btn-outline-secondary fw-bold"
                                style={{ borderRadius: '10px' }}
                            >
                                আরও কেনাকাটা করুন
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .last-child-no-border:last-child { border-bottom: none !important; }
            `}</style>
        </MasterLayout>
    );
};

export default OrderSuccess;
