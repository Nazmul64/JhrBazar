import React, { useState, useEffect } from 'react';
import MasterLayout from '../layouts/MasterLayout';
import { useCart } from '../context/CartContext';
import { Link, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { toast } from 'react-hot-toast';

const generateCanvasFingerprint = () => {
    let fp = localStorage.getItem('device_fingerprint_secure');
    if (fp) return fp;

    try {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 200;
        canvas.height = 50;

        ctx.textBaseline = "top";
        ctx.font = "14px 'Arial'";
        ctx.textBaseline = "alphabetic";
        ctx.fillStyle = "#f60";
        ctx.fillRect(125, 1, 62, 20);
        ctx.fillStyle = "#069";
        ctx.fillText("JhrBazarSecureFingerprint!", 2, 15);
        ctx.fillStyle = "rgba(102, 204, 0, 0.7)";
        ctx.fillText("JhrBazarSecureFingerprint!", 4, 17);

        const canvasData = canvas.toDataURL();

        const screenWidth = window.screen.width || 0;
        const screenHeight = window.screen.height || 0;
        const colorDepth = window.screen.colorDepth || 0;
        const userAgent = navigator.userAgent || '';
        const language = navigator.language || navigator.userLanguage || '';
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
        const hardwareConcurrency = navigator.hardwareConcurrency || 'unknown';

        const rawString = `${canvasData}|${screenWidth}x${screenHeight}|${colorDepth}|${userAgent}|${language}|${timezone}|${hardwareConcurrency}`;

        let hash = 2166136261;
        for (let i = 0; i < rawString.length; i++) {
            hash ^= rawString.charCodeAt(i);
            hash += (hash << 1) + (hash << 4) + (hash << 7) + (hash << 8) + (hash << 24);
        }

        fp = 'FP-' + Math.abs(hash).toString(16).toUpperCase();
        localStorage.setItem('device_fingerprint_secure', fp);
        document.cookie = `device_fingerprint_secure=${fp}; max-age=31536000; path=/`;
        return fp;
    } catch (e) {
        const fallback = 'FP-FALLBACK-' + Math.random().toString(36).substring(2, 15).toUpperCase();
        localStorage.setItem('device_fingerprint_secure', fallback);
        document.cookie = `device_fingerprint_secure=${fallback}; max-age=31536000; path=/`;
        return fallback;
    }
};

const getBrowserName = () => {
    const userAgent = navigator.userAgent;
    if (userAgent.indexOf("Chrome") > -1) return "Chrome";
    if (userAgent.indexOf("Safari") > -1) return "Safari";
    if (userAgent.indexOf("Firefox") > -1) return "Firefox";
    if (userAgent.indexOf("MSIE") > -1 || !!document.documentMode === true) return "IE";
    if (userAgent.indexOf("Edge") > -1) return "Edge";
    return "Unknown Browser";
};

const getOSName = () => {
    const userAgent = navigator.userAgent;
    if (userAgent.indexOf("Windows NT 10.0") > -1) return "Windows 10/11";
    if (userAgent.indexOf("Windows NT 6.2") > -1) return "Windows 8";
    if (userAgent.indexOf("Windows NT 6.1") > -1) return "Windows 7";
    if (userAgent.indexOf("Macintosh") > -1) return "macOS";
    if (userAgent.indexOf("iPhone") > -1 || userAgent.indexOf("iPad") > -1) return "iOS";
    if (userAgent.indexOf("Android") > -1) return "Android";
    if (userAgent.indexOf("Linux") > -1) return "Linux";
    return "Unknown OS";
};

const getDeviceType = () => {
    const userAgent = navigator.userAgent;
    if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(userAgent)) {
        return "Tablet";
    }
    if (/Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(userAgent)) {
        return "Mobile";
    }
    return "Desktop";
};

const Checkout = () => {
    const navigate = useNavigate();
    const mainColor = '#57b500';
    const { cartItems, cartTotal, clearCart, removeFromCart, updateQuantity } = useCart();
    const [loading, setLoading] = useState(false);
    const [shippingCharges, setShippingCharges] = useState([]);
    const [availableGateways, setAvailableGateways] = useState([]);
    const [selectedShipping, setSelectedShipping] = useState(null);

    const [isBlocked, setIsBlocked] = useState(false);
    const [blockedData, setBlockedData] = useState(null);

    const [otpSent, setOtpSent] = useState(false);
    const [otpCode, setOtpCode] = useState('');

    useEffect(() => {
        const checkBlock = async () => {
            try {
                const fp = generateCanvasFingerprint();
                const res = await axios.get(`/api/check-ip-blocked?fingerprint=${fp}`);
                if (res.data.blocked) {
                    setIsBlocked(true);
                    setBlockedData(res.data.data);
                }
            } catch (err) {
                console.error("Block check error", err);
            }
        };
        checkBlock();
    }, []);

    useEffect(() => {
        const queryParams = new URLSearchParams(window.location.search);
        if (queryParams.get('payment_error')) {
            toast.error("পেমেন্ট ভ্যালিডেশন ব্যর্থ হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।");
        } else if (queryParams.get('payment_failed')) {
            toast.error("পেমেন্ট করতে ব্যর্থ হয়েছে। দয়া করে আবার চেষ্টা করুন।");
        } else if (queryParams.get('payment_cancelled')) {
            toast.warning("পেমেন্টটি বাতিল করা হয়েছে।");
        }
    }, []);

    const [couponCode, setCouponCode] = useState('');
    const [couponDiscount, setCouponDiscount] = useState(0);
    const [couponApplied, setCouponApplied] = useState(false);
    const [applyingCoupon, setApplyingCoupon] = useState(false);

    const [formData, setFormData] = useState({
        name: '',
        phone: '',
        email: '',
        address: '',
        shipping_id: '',
        payment_method: 'cod',
        online_gateway: ''
    });

    const [saveStatus, setSaveStatus] = useState(''); // '' | 'saving' | 'saved'

    useEffect(() => {
        const fetchShipping = async () => {
            try {
                const res = await axios.get('/api/shipping-charges');
                if (res.data.success) {
                    setShippingCharges(res.data.data);
                    // Set first one as default if exists
                    if (res.data.data.length > 0) {
                        const first = res.data.data[0];
                        setFormData(prev => ({ ...prev, shipping_id: first.id }));
                        setSelectedShipping(first);
                    }
                }
            } catch (err) {
                console.error("Error fetching shipping charges", err);
            }
        };

        const fetchGateways = async () => {
            try {
                const res = await axios.get('/api/payment-gateways');
                if (res.data.success) {
                    setAvailableGateways(res.data.data);
                    // No default gateway selection here to force user choice if multiple
                }
            } catch (err) {
                console.error("Error fetching gateways", err);
            }
        };

        fetchShipping();
        fetchGateways();
    }, []);

    // Data Layer: begin_checkout
    useEffect(() => {
        if (cartItems.length > 0) {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'begin_checkout',
                currency: 'BDT',
                value: Number(cartTotal),
                items: cartItems.map(item => ({
                    item_id: String(item.id),
                    item_name: item.name,
                    price: Number(item.price),
                    quantity: Number(item.qty)
                }))
            });
        }
    }, [cartItems, cartTotal]);

    // Lead Capture Logic (Incomplete Orders)
    useEffect(() => {
        if (!formData.phone || formData.phone.length < 11) return;

        // If exactly 11, save immediately
        if (formData.phone.length === 11) {
            saveLead();
            return;
        }

        const timer = setTimeout(() => {
            saveLead();
        }, 1500);

        return () => clearTimeout(timer);
    }, [formData.phone, formData.name]);

    const saveLead = async () => {
        try {
            setSaveStatus('saving');
            // Group items by shop/seller to save separate leads if necessary
            const shops = [...new Set(cartItems.map(item => item.seller_id))];

            for (const shopId of shops) {
                const shopTotal = cartItems
                    .filter(item => item.seller_id === shopId)
                    .reduce((sum, item) => sum + (item.price * item.qty), 0);

                await axios.post('/api/leads/save', {
                    name: formData.name,
                    phone: formData.phone,
                    email: formData.email,
                    address: formData.address,
                    payment_method: formData.payment_method,
                    area: formData.shipping_id, // This is the ID but usually represents area
                    cart_items: cartItems.filter(item => item.seller_id === shopId),
                    seller_id: shopId === 0 ? null : shopId, // 0 usually means admin
                    total: shopTotal,
                    url: window.location.href
                });
            }
            setSaveStatus('saved');
            toast.success("Information saved!", {
                duration: 3000,
                position: 'top-center',
                style: {
                    background: '#10b981',
                    color: '#fff',
                    borderRadius: '50px',
                    fontSize: '14px',
                    fontWeight: 'bold'
                }
            });
            // Hide status text after 5 seconds
            setTimeout(() => setSaveStatus(''), 5000);
        } catch (err) {
            console.error("Error saving lead", err);
            setSaveStatus('');
        }
    };

    useEffect(() => {
        const canCOD = cartItems.every(item => item.cash_on_delivery ?? true);
        const canOnline = cartItems.every(item => item.online_payment ?? true);

        if (formData.payment_method === 'cod' && !canCOD && canOnline) {
            setFormData(prev => ({ ...prev, payment_method: 'online' }));
        } else if (formData.payment_method === 'online' && !canOnline && canCOD) {
            setFormData(prev => ({ ...prev, payment_method: 'cod', online_gateway: '' }));
        }
    }, [cartItems]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });

        if (name === 'shipping_id') {
            const selected = shippingCharges.find(s => s.id == value);
            setSelectedShipping(selected);
        }
    };

    const handleApplyCoupon = async () => {
        if (!couponCode) {
            toast.error("Please enter a coupon code");
            return;
        }
        setApplyingCoupon(true);
        try {
            const res = await axios.post('/api/apply-coupon', {
                coupon_code: couponCode,
                subtotal: cartTotal,
                items: cartItems
            });
            if (res.data.success) {
                setCouponDiscount(res.data.discount);
                setCouponApplied(true);
                toast.success(res.data.message);
            } else {
                toast.error(res.data.message);
            }
        } catch (err) {
            toast.error("Invalid coupon code");
        } finally {
            setApplyingCoupon(false);
        }
    };

    const shippingAmount = selectedShipping ? Number(selectedShipping.charge) : 0;
    const finalTotal = (cartTotal + shippingAmount) - couponDiscount;
    const selectedGateway = availableGateways.find(gateway => gateway.key === formData.online_gateway);
    const submitButtonLabel = otpSent
        ? 'ওটিপি যাচাই করে অর্ডার নিশ্চিত করুন (Verify & Confirm)'
        : (formData.payment_method === 'online'
            ? (selectedGateway ? `Pay with ${selectedGateway.title}` : 'Select Online Gateway')
            : 'Confirm Order (Cash on Delivery)');
    const isSubmitDisabled = loading || (formData.payment_method === 'online' && !formData.online_gateway) || (otpSent && otpCode.length < 4);

    const handleResendOtp = async () => {
        setOtpCode('');
        setLoading(true);
        try {
            const res = await axios.post('/api/place-order', {
                ...formData,
                city: selectedShipping ? selectedShipping.area_name : 'N/A',
                items: cartItems,
                shipping_charge: shippingAmount,
                discount: couponDiscount,
                coupon_code: couponApplied ? couponCode : null,
                device_fingerprint: generateCanvasFingerprint(),
                browser: getBrowserName(),
                os: getOSName(),
                device_type: getDeviceType()
            });

            if (res.data.otp_required) {
                toast.success(res.data.message || "নতুন ওটিপি (OTP) পাঠানো হয়েছে!");
            } else if (res.data.success) {
                clearCart();
                navigate('/order-success', { state: { orders: res.data.orders } });
            } else {
                toast.error(res.data.message);
            }
        } catch (error) {
            toast.error(error.response?.data?.message || "OTP পাঠাতে সমস্যা হয়েছে।");
        } finally {
            setLoading(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (cartItems.length === 0) {
            toast.error("Your cart is empty!");
            return;
        }

        if (formData.payment_method === 'online' && !formData.online_gateway) {
            toast.error("Please select a payment gateway!");
            return;
        }

        setLoading(true);
        try {
            const res = await axios.post('/api/place-order', {
                ...formData,
                city: selectedShipping ? selectedShipping.area_name : 'N/A',
                items: cartItems,
                shipping_charge: shippingAmount,
                discount: couponDiscount,
                coupon_code: couponApplied ? couponCode : null,
                device_fingerprint: generateCanvasFingerprint(),
                browser: getBrowserName(),
                os: getOSName(),
                device_type: getDeviceType(),
                otp_code: otpSent ? otpCode : null
            });

            if (res.data.success) {
                if (res.data.payment_url) {
                    window.location.href = res.data.payment_url;
                } else {
                    clearCart();
                    navigate('/order-success', { state: { orders: res.data.orders } });
                }
            } else if (res.data.otp_required) {
                setOtpSent(true);
                toast.success(res.data.message || "ওটিপি (OTP) কোড পাঠানো হয়েছে!");
            } else {
                toast.error(res.data.message || "Failed to place order");
            }
        } catch (error) {
            console.error("Order error", error);
            const msg = error.response?.data?.message || "Something went wrong. Please try again.";
            toast.error(msg);
        } finally {
            setLoading(false);
        }
    };

    if (isBlocked && blockedData) {
        return (
            <div style={{
                background: '#070c14',
                color: '#ffffff',
                minHeight: '100vh',
                padding: '40px 20px',
                fontFamily: "'Hind Siliguri', 'Outfit', sans-serif",
                position: 'relative',
                overflowX: 'hidden'
            }}>
                {/* Visual Carbon/Grid lines background overlay */}
                <div style={{
                    position: 'absolute',
                    top: 0, left: 0, right: 0, bottom: 0,
                    backgroundImage: 'linear-gradient(rgba(18, 30, 49, 0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(18, 30, 49, 0.3) 1px, transparent 1px)',
                    backgroundSize: '20px 20px',
                    pointerEvents: 'none',
                    opacity: 0.8
                }}></div>

                <div style={{ maxWidth: '600px', margin: '0 auto', position: 'relative', zIndex: 2 }}>

                    {/* Pulsing Cancel Shield Logo */}
                    <div style={{ textAlign: 'center', marginBottom: '20px' }}>
                        <div className="pulsing-shield-container" style={{
                            display: 'inline-flex',
                            position: 'relative',
                            width: '90px',
                            height: '90px',
                            background: 'rgba(239, 68, 68, 0.1)',
                            border: '2px solid #ef4444',
                            borderRadius: '50%',
                            alignItems: 'center',
                            justifyContent: 'center',
                            boxShadow: '0 0 25px rgba(239, 68, 68, 0.3)',
                            animation: 'pulseShield 2s infinite ease-in-out'
                        }}>
                            <span style={{ fontSize: '40px', color: '#ef4444' }}>🚫</span>
                        </div>
                        <h2 style={{
                            marginTop: '20px',
                            fontWeight: 800,
                            fontSize: '1.8rem',
                            color: '#ffffff',
                            letterSpacing: '0.5px'
                        }}>আইপি ব্লক করা হয়েছে</h2>
                        <div style={{
                            color: '#ef4444',
                            fontSize: '0.85rem',
                            fontWeight: 700,
                            letterSpacing: '2px',
                            textTransform: 'uppercase',
                            marginTop: '4px'
                        }}>SECURITY SYSTEM VIOLATION</div>
                    </div>

                    {/* Red Warning Message Box */}
                    <div style={{
                        border: '1.5px solid #ef4444',
                        background: 'rgba(239, 68, 68, 0.05)',
                        borderRadius: '12px',
                        padding: '20px',
                        marginBottom: '25px',
                        boxShadow: '0 8px 20px rgba(239, 68, 68, 0.05)'
                    }}>
                        <p style={{
                            margin: 0,
                            fontSize: '0.92rem',
                            lineHeight: 1.6,
                            textAlign: 'justify',
                            color: '#f8fafc',
                            fontWeight: 500
                        }}>
                            আমাদের অটোমেটেড সিকিউরিটি সিস্টেম আপনাকে সনাক্ত করেছে! আপনি যদি ফেক, ট্রল বা উদ্দেশ্যপ্রণোদিত অর্ডার দিয়ে আমাদের ব্যবসার ক্ষতি করার চেষ্টা করেন, তবে আপনার বিরুদ্ধে আইনগত ব্যবস্থা গ্রহণ করা হবে। আপনার লোকেশন, আইপি অ্যাড্রেস এবং ইন্টারনেট প্রোভাইডারের (ওয়াইফাই) সকল তথ্য আমাদের ডাটাবেজে প্রমাণসহ সম্পূর্ণ সংরক্ষণ করা হয়েছে। আপনার কোনো প্রকার অসাধু চেষ্টা প্রমাণ হলে আপনার বিরুদ্ধে বাংলাদেশ সাইবার ক্রাইম আইন ও আইন প্রয়োগকারী সংস্থার মাধ্যমে আইনানুগ মামলা দিতে আমরা বাধ্য থাকিব।
                        </p>
                    </div>

                    {/* Terminal Connection Tracker box */}
                    <div style={{
                        background: '#09101d',
                        border: '1px solid #1e293b',
                        borderRadius: '12px',
                        padding: '20px',
                        fontFamily: "'Courier New', monospace",
                        boxShadow: '0 10px 30px rgba(0,0,0,0.5)',
                        marginBottom: '25px',
                        position: 'relative'
                    }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '15px', borderBottom: '1px solid #1e293b', paddingBottom: '8px' }}>
                            <div style={{ display: 'flex', gap: '6px' }}>
                                <div style={{ width: '10px', height: '10px', borderRadius: '50%', background: '#ef4444' }}></div>
                                <div style={{ width: '10px', height: '10px', borderRadius: '50%', background: '#f59e0b' }}></div>
                                <div style={{ width: '10px', height: '10px', borderRadius: '50%', background: '#10b981' }}></div>
                            </div>
                            <div style={{ fontSize: '0.75rem', color: '#64748b', fontWeight: 'bold' }}>CONNECTION_TRACKER.SH</div>
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', fontSize: '0.85rem' }}>
                            <div>
                                <span style={{ color: '#ef4444', marginRight: '10px' }}>[IP_ADDRESS]:</span>
                                <span style={{ color: '#22c55e', fontWeight: 'bold' }}>{blockedData.ip}</span>
                            </div>
                            <div>
                                <span style={{ color: '#ef4444', marginRight: '10px' }}>[WIFI_PROVIDER]:</span>
                                <span style={{ color: '#22c55e', fontWeight: 'bold' }}>{blockedData.wifi_provider}</span>
                            </div>
                            <div>
                                <span style={{ color: '#ef4444', marginRight: '10px' }}>[LOCATION]:</span>
                                <span style={{ color: '#22c55e', fontWeight: 'bold' }}>{blockedData.location}</span>
                            </div>
                            <div>
                                <span style={{ color: '#ef4444', marginRight: '10px' }}>[DEVICE_AGENT]:</span>
                                <span style={{ color: '#22c55e', fontSize: '0.75rem', wordBreak: 'break-all' }}>{blockedData.device_agent}</span>
                            </div>
                            <div>
                                <span style={{ color: '#ef4444', marginRight: '10px' }}>[TRACKING_TIME]:</span>
                                <span style={{ color: '#22c55e', fontWeight: 'bold' }}>{blockedData.time}</span>
                            </div>
                        </div>
                    </div>

                    {/* Styled Dark Map Box */}
                    <div style={{
                        position: 'relative',
                        border: '1.5px solid #ef4444',
                        borderRadius: '12px',
                        overflow: 'hidden',
                        boxShadow: '0 10px 35px rgba(239, 68, 68, 0.1)',
                        marginBottom: '20px'
                    }}>
                        <iframe
                            width="100%"
                            height="280"
                            frameBorder="0"
                            scrolling="no"
                            marginHeight="0"
                            marginWidth="0"
                            src={`https://maps.google.com/maps?q=${blockedData.lat},${blockedData.lon}&z=14&output=embed`}
                            style={{ border: 0, filter: 'invert(90%) hue-rotate(180deg) brightness(95%) contrast(90%)', display: 'block' }}
                        ></iframe>
                        <div style={{
                            position: 'absolute',
                            top: '50%',
                            left: '50%',
                            transform: 'translate(-50%, -50%)',
                            background: 'rgba(239, 68, 68, 0.9)',
                            border: '1.5px solid #ffffff',
                            color: '#ffffff',
                            padding: '6px 12px',
                            borderRadius: '8px',
                            fontSize: '0.75rem',
                            fontWeight: 'bold',
                            boxShadow: '0 5px 15px rgba(0,0,0,0.5)',
                            pointerEvents: 'none',
                            display: 'flex',
                            alignItems: 'center',
                            gap: '4px'
                        }}>
                            <span>⚠️</span> ফেক অর্ডারকারীর লোকেশন!
                        </div>
                    </div>

                    {/* Disclaimer Footer Text */}
                    <div style={{ textAlign: 'center', fontSize: '0.75rem', color: '#64748b', marginTop: '15px', lineHeight: 1.5 }}>
                        যদি এটি ভুলবশত হয়ে থাকে অথবা আপনার অর্ডার জেনুইন হয়ে থাকে, তবে তাৎক্ষণিকভাবে আমাদের কাস্টমার সার্ভিসের সাথে যোগাযোগ করুন।
                    </div>
                </div>

                <style>{`
                    @keyframes pulseShield {
                        0% { transform: scale(1); box-shadow: 0 0 20px rgba(239, 68, 68, 0.3); }
                        50% { transform: scale(1.05); box-shadow: 0 0 35px rgba(239, 68, 68, 0.6); }
                        100% { transform: scale(1); box-shadow: 0 0 20px rgba(239, 68, 68, 0.3); }
                    }
                `}</style>
            </div>
        );
    }

    if (cartItems.length === 0) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center">
                    <div style={{ fontSize: '60px', marginBottom: '20px' }}>🛒</div>
                    <h3>Your cart is empty</h3>
                    <p className="text-muted">Please add products to your cart before placing an order.</p>
                    <Link to="/" className="btn text-white px-5 py-2 mt-3" style={{ backgroundColor: mainColor, borderRadius: '30px' }}>
                        Continue Shopping
                    </Link>
                </div>
            </MasterLayout>
        );
    }

    return (
        <MasterLayout>
            <div className="bg-light min-vh-100 py-4 py-md-5">
                <div className="container">
                    <div className="row g-4">
                        {/* Shipping Information */}
                        <div className="col-lg-7">
                            <div className="card border-0 shadow-sm p-4 p-md-5" style={{ borderRadius: '20px' }}>
                                <div className="d-flex align-items-center gap-2 mb-4">
                                    <div style={{ width: '4px', height: '24px', backgroundColor: mainColor, borderRadius: '2px' }}></div>
                                    <h4 className="fw-bold m-0">Shipping Information</h4>
                                </div>

                                <form onSubmit={handleSubmit}>
                                    <div className="row g-3">
                                        <div className="col-12">
                                            <label className="form-label small fw-bold text-muted uppercase">Full Name</label>
                                            <div className="input-group border rounded-3 p-1 bg-light shadow-sm">
                                                <span className="input-group-text bg-transparent border-0"><i className="far fa-user text-muted"></i></span>
                                                <input
                                                    type="text" name="name" required className="form-control bg-transparent border-0"
                                                    placeholder="Enter your full name" value={formData.name} onChange={handleChange}
                                                />
                                            </div>
                                        </div>

                                        <div className="col-md-6">
                                            <label className="form-label small fw-bold text-muted uppercase">Mobile Number</label>
                                            <div className="input-group border rounded-3 p-1 bg-light shadow-sm">
                                                <span className="input-group-text bg-transparent border-0"><i className="fas fa-phone-alt text-muted"></i></span>
                                                <input
                                                    type="tel" name="phone" required className="form-control bg-transparent border-0"
                                                    placeholder="018XXXXXXXX" value={formData.phone} onChange={handleChange}
                                                />
                                            </div>
                                            {saveStatus === 'saved' && (
                                                <div className="mt-1 small text-success animation-slide-down">
                                                    <i className="bi bi-check-circle-fill me-1"></i> Information Saved
                                                </div>
                                            )}
                                            {saveStatus === 'saving' && (
                                                <div className="mt-1 small text-muted animation-slide-down">
                                                    <span className="spinner-border spinner-border-sm me-1" style={{ width: '10px', height: '10px' }}></span> Saving...
                                                </div>
                                            )}
                                        </div>

                                        <div className="col-md-6">
                                            <label className="form-label small fw-bold text-muted uppercase">Email (Optional)</label>
                                            <div className="input-group border rounded-3 p-1 bg-light shadow-sm">
                                                <span className="input-group-text bg-transparent border-0"><i className="far fa-envelope text-muted"></i></span>
                                                <input
                                                    type="email" name="email" className="form-control bg-transparent border-0"
                                                    placeholder="Your email address" value={formData.email} onChange={handleChange}
                                                />
                                            </div>
                                        </div>

                                        <div className="col-12">
                                            <label className="form-label small fw-bold text-muted uppercase">Select Delivery Area</label>
                                            <div className="input-group border rounded-3 p-1 bg-light shadow-sm">
                                                <span className="input-group-text bg-transparent border-0"><i className="fas fa-truck text-muted"></i></span>
                                                <select name="shipping_id" required className="form-select bg-transparent border-0" value={formData.shipping_id} onChange={handleChange}>
                                                    {shippingCharges.map(charge => (
                                                        <option key={charge.id} value={charge.id}>{charge.area_name} (৳{Number(charge.charge).toLocaleString()})</option>
                                                    ))}
                                                </select>
                                            </div>
                                        </div>

                                        <div className="col-12">
                                            <label className="form-label small fw-bold text-muted uppercase">Detailed Address</label>
                                            <textarea
                                                name="address" required rows="2" className="form-control border bg-light p-3 shadow-sm"
                                                placeholder="House no, Road no, Area details..." style={{ borderRadius: '12px' }}
                                                value={formData.address} onChange={handleChange}
                                            ></textarea>
                                        </div>

                                        {otpSent && (
                                            <div className="col-12 mt-3 p-3 border border-success rounded-4 bg-light shadow-sm animate-fade-in">
                                                <label className="form-label small fw-bold text-success uppercase"><i className="fas fa-lock me-1"></i> ওটিপি (OTP) কোড লিখুন</label>
                                                <div className="input-group border border-success rounded-3 p-1 bg-white shadow-inner">
                                                    <span className="input-group-text bg-transparent border-0"><i className="fas fa-key text-success"></i></span>
                                                    <input
                                                        type="text" required className="form-control bg-transparent border-0 fw-bold text-center"
                                                        placeholder="৬ ডিজিটের ওটিপি কোড দিন" style={{ letterSpacing: '4px', fontSize: '16px' }}
                                                        value={otpCode} onChange={(e) => setOtpCode(e.target.value)}
                                                    />
                                                </div>
                                                <div className="d-flex justify-content-between mt-2 px-1">
                                                    <span className="small text-muted">আপনার মোবাইলে কোড পাঠানো হয়েছে</span>
                                                    <button type="button" onClick={handleResendOtp} className="btn btn-link btn-sm text-decoration-none p-0 text-success fw-bold">
                                                        আবার পাঠান (Resend OTP)
                                                    </button>
                                                </div>
                                            </div>
                                        )}
                                    </div>

                                    <div className="mt-5">
                                        <div className="d-flex align-items-center gap-2 mb-4">
                                            <div style={{ width: '4px', height: '24px', backgroundColor: mainColor, borderRadius: '2px' }}></div>
                                            <h4 className="fw-bold m-0">Payment Method</h4>
                                        </div>

                                        <div className="row g-3">
                                            {/* COD Option */}
                                            {cartItems.every(item => item.cash_on_delivery ?? true) ? (
                                                <div className="col-md-6">
                                                    <label className={`card border p-3 h-100 transition-all ${formData.payment_method === 'cod' ? 'border-success bg-light' : ''}`} style={{ borderRadius: '15px', cursor: 'pointer' }}>
                                                        <div className="d-flex align-items-center gap-3">
                                                            <input
                                                                type="radio" name="payment_method" value="cod"
                                                                checked={formData.payment_method === 'cod'} onChange={handleChange}
                                                                className="form-check-input mt-0"
                                                            />
                                                            <div>
                                                                <div className="fw-bold">Cash On Delivery</div>
                                                                <div className="small text-muted">Pay when you receive the product</div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            ) : (
                                                <div className="col-md-6">
                                                    <div className="card border p-3 h-100 bg-light opacity-75" style={{ borderRadius: '15px', borderStyle: 'dashed' }}>
                                                        <div className="d-flex align-items-center gap-3">
                                                            <div className="text-muted"><i className="fas fa-ban"></i></div>
                                                            <div>
                                                                <div className="fw-bold text-muted">Cash On Delivery</div>
                                                                <div className="small text-danger" style={{ fontSize: '10px' }}>Not available for some items</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            )}

                                            {/* Online Payment Option */}
                                            {cartItems.every(item => item.online_payment ?? true) ? (
                                                <div className="col-md-6">
                                                    <label className={`card border p-3 h-100 transition-all ${formData.payment_method === 'online' ? 'border-success bg-light' : ''}`} style={{ borderRadius: '15px', cursor: 'pointer' }}>
                                                        <div className="d-flex align-items-center gap-3">
                                                            <input
                                                                type="radio" name="payment_method" value="online"
                                                                checked={formData.payment_method === 'online'} onChange={handleChange}
                                                                className="form-check-input mt-0"
                                                            />
                                                            <div>
                                                                <div className="fw-bold">Online Payment</div>
                                                                <div className="small text-muted">Pay via Mobile Banking or Card</div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            ) : (
                                                <div className="col-md-6">
                                                    <div className="card border p-3 h-100 bg-light opacity-75" style={{ borderRadius: '15px', borderStyle: 'dashed' }}>
                                                        <div className="d-flex align-items-center gap-3">
                                                            <div className="text-muted"><i className="fas fa-ban"></i></div>
                                                            <div>
                                                                <div className="fw-bold text-muted">Online Payment</div>
                                                                <div className="small text-danger" style={{ fontSize: '10px' }}>Not available for some items</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            )}
                                        </div>

                                        {formData.payment_method === 'online' && (
                                            <div className="mt-4 p-3 border rounded-4 bg-light shadow-inner animation-slide-down">
                                                <div className="small fw-bold text-muted mb-3 uppercase">Select Payment Gateway</div>
                                                <div className="d-flex flex-wrap gap-2">
                                                    {availableGateways.map(gateway => (
                                                        <label key={gateway.key} className={`gateway-box ${formData.online_gateway === gateway.key ? 'active' : ''}`}>
                                                            <input
                                                                type="radio" name="online_gateway" value={gateway.key}
                                                                checked={formData.online_gateway === gateway.key} onChange={handleChange}
                                                                className="d-none"
                                                            />
                                                            <div className="gateway-content">
                                                                {gateway.logo ? (
                                                                    <img src={gateway.logo} alt={gateway.title} style={{ height: '30px' }} />
                                                                ) : (
                                                                    <span className="fw-bold small">{gateway.title}</span>
                                                                )}
                                                            </div>
                                                        </label>
                                                    ))}
                                                    {availableGateways.length === 0 && (
                                                        <div className="text-danger small">No online payment gateway is active.</div>
                                                    )}
                                                </div>
                                            </div>
                                        )}
                                    </div>

                                    <button
                                        type="submit" disabled={isSubmitDisabled}
                                        className="btn btn-lg w-100 text-white fw-bold py-3 mt-5 shadow confirm-btn"
                                        style={{ backgroundColor: mainColor, borderRadius: '15px' }}
                                    >
                                        {loading ? (
                                            <span className="spinner-border spinner-border-sm me-2"></span>
                                        ) : (
                                            <i className="fas fa-shopping-bag me-2"></i>
                                        )}
                                        {submitButtonLabel}
                                    </button>
                                    {formData.payment_method === 'online' && !formData.online_gateway && (
                                        <div className="small text-danger mt-2">
                                            অনুগ্রহ করে প্রথমে একটি অনলাইন পেমেন্ট গেটওয়ে নির্বাচন করুন।
                                        </div>
                                    )}
                                </form>
                            </div>
                        </div>

                        {/* Order Summary */}
                        <div className="col-lg-5">
                            <div className="card border-0 shadow-sm sticky-top" style={{ borderRadius: '20px', top: '100px' }}>
                                <div className="card-body p-4">
                                    <h5 className="fw-bold mb-4">Order Summary</h5>

                                    <div className="order-items-list mb-4 overflow-auto" style={{ maxHeight: '300px' }}>
                                        {cartItems.map(item => (
                                            <div key={item.uid} className="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom border-light">
                                                <div className="rounded border overflow-hidden position-relative" style={{ width: '50px', height: '50px', flexShrink: 0 }}>
                                                    <img
                                                        src={item.image?.startsWith('http') ? item.image : (item.image?.startsWith('/') ? item.image : (item.image?.startsWith('uploads/') ? `/${item.image}` : `/uploads/product/${item.image}`))}
                                                        alt={item.title}
                                                        className="w-100 h-100 object-fit-cover"
                                                        onError={(e) => { e.target.src = '/assets/admin/images/no-image.png'; }}
                                                    />
                                                    <span className="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark border border-light" style={{ fontSize: '10px' }}>
                                                        {item.qty}
                                                    </span>
                                                </div>
                                                <div className="flex-grow-1">
                                                    <div className="fw-bold small text-dark text-truncate" style={{ maxWidth: '180px' }}>{item.title}</div>
                                                    <div className="text-muted small">৳{Number(item.price).toLocaleString()}</div>
                                                    {(item.color || item.size) && (
                                                        <div className="text-muted small mt-1" style={{ fontSize: '10px' }}>
                                                            {item.color && <span className="me-2">Color: {item.color}</span>}
                                                            {item.size && <span>Size: {item.size}</span>}
                                                        </div>
                                                    )}
                                                </div>
                                                <div className="d-flex flex-column align-items-end gap-2">
                                                    <div className="fw-bold small">
                                                        ৳{Number(item.price * item.qty).toLocaleString()}
                                                    </div>
                                                    <div className="d-flex align-items-center bg-light border rounded" style={{ height: '32px' }}>
                                                        <button
                                                            type="button"
                                                            className="btn btn-sm btn-light border-0 px-2 d-flex align-items-center justify-content-center"
                                                            onClick={() => updateQuantity(item.uid, -1)}
                                                            disabled={item.qty <= 1}
                                                            style={{ height: '100%' }}
                                                        >
                                                            <i className="fas fa-minus" style={{ fontSize: '10px' }}></i>
                                                        </button>
                                                        <span className="fw-bold px-2 small">{item.qty}</span>
                                                        <button
                                                            type="button"
                                                            className="btn btn-sm btn-light border-0 px-2 d-flex align-items-center justify-content-center"
                                                            onClick={() => updateQuantity(item.uid, 1)}
                                                            style={{ height: '100%' }}
                                                        >
                                                            <i className="fas fa-plus" style={{ fontSize: '10px' }}></i>
                                                        </button>
                                                    </div>
                                                    <button
                                                        type="button"
                                                        className="btn btn-sm text-danger border-0 bg-transparent p-0"
                                                        onClick={() => removeFromCart(item.uid)}
                                                    >
                                                        <i className="fas fa-trash-alt me-1"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        ))}
                                    </div>

                                    {/* Coupon Section */}
                                    <div className="mb-4">
                                        <label className="form-label small fw-bold text-muted uppercase">Have a coupon?</label>
                                        <div className="input-group">
                                            <input
                                                type="text" className="form-control border shadow-none"
                                                placeholder="Enter code" value={couponCode}
                                                disabled={couponApplied}
                                                onChange={(e) => setCouponCode(e.target.value)}
                                            />
                                            <button
                                                className="btn btn-dark fw-bold px-3"
                                                type="button"
                                                disabled={couponApplied || applyingCoupon}
                                                onClick={handleApplyCoupon}
                                            >
                                                {applyingCoupon ? <span className="spinner-border spinner-border-sm"></span> : 'Apply'}
                                            </button>
                                        </div>
                                        {couponApplied && (
                                            <div className="mt-2 small text-success fw-bold d-flex justify-content-between">
                                                <span>Coupon Applied Successfully!</span>
                                                <span className="cursor-pointer text-danger" onClick={() => { setCouponApplied(false); setCouponDiscount(0); setCouponCode('') }}>Remove</span>
                                            </div>
                                        )}
                                    </div>

                                    <div className="bg-light p-3 rounded-4 mb-4 border">
                                        <div className="d-flex justify-content-between mb-2">
                                            <span className="text-muted small">Subtotal</span>
                                            <span className="fw-bold small text-dark">৳{Number(cartTotal).toLocaleString()}</span>
                                        </div>
                                        <div className="d-flex justify-content-between mb-2">
                                            <span className="text-muted small">Shipping Charge ({selectedShipping?.area_name})</span>
                                            <span className="fw-bold small text-dark">৳{Number(shippingAmount).toLocaleString()}</span>
                                        </div>
                                        {couponDiscount > 0 && (
                                            <div className="d-flex justify-content-between mb-2 text-danger">
                                                <span className="small">Discount</span>
                                                <span className="fw-bold small">- ৳{Number(couponDiscount).toLocaleString()}</span>
                                            </div>
                                        )}
                                        <div className="d-flex justify-content-between pt-2 border-top mt-2">
                                            <span className="fw-bold">Total Amount</span>
                                            <span className="fw-bold h4 mb-0" style={{ color: mainColor }}>৳{Number(finalTotal).toLocaleString()}</span>
                                        </div>
                                    </div>

                                    <div className="text-center">
                                        <div className="small text-muted mb-2"><i className="fas fa-shield-alt me-1 text-success"></i> 100% Secure Checkout</div>
                                        <p className="text-muted px-3" style={{ fontSize: '11px' }}>
                                            Your information is safe. Call support for any issues.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>{`
                .form-control:focus, .form-select:focus {
                    box-shadow: none;
                    background-color: #fff !important;
                    border-color: ${mainColor} !important;
                }
                .transition-all { transition: all 0.3s; }
                .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
                .object-fit-cover { object-fit: cover; }
                .gateway-box {
                    border: 2px solid #eee; border-radius: 10px; padding: 5px 10px; background: #fff; cursor: pointer; transition: all 0.2s;
                }
                .gateway-box.active { border-color: ${mainColor}; background: #f0fff4; }
                .cursor-pointer { cursor: pointer; }
                .confirm-btn:hover { background-color: #4a9a00 !important; transform: translateY(-2px); }
            `}</style>
        </MasterLayout>
    );
};

export default Checkout;
