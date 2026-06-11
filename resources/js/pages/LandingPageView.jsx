import React, { useState, useEffect, useRef } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axios from 'axios';
import toast from 'react-hot-toast';
import {
  Check, X, Shield, Phone, MapPin, Truck, ShoppingCart,
  ChevronRight, Star, AlertTriangle, Info, Clock, Award
} from 'lucide-react';

const getProductImageUrl = (url) => {
  if (!url) return '/assets/admin/images/no-image.png';
  if (url.startsWith('http')) return url;
  if (url.startsWith('/')) return url;
  if (url.startsWith('uploads/')) return '/' + url;
  return '/uploads/product/' + url;
};

const LandingPageView = () => {
  const { slug } = useParams();
  const navigate = useNavigate();
  const checkoutFormRef = useRef(null);

  const [loading, setLoading] = useState(true);
  const [pageData, setPageData] = useState(null);
  const [shippingCharges, setShippingCharges] = useState([]);

  // Checkout form state
  const [name, setName] = useState('');
  const [phone, setPhone] = useState('');
  const [address, setAddress] = useState('');
  const [city, setCity] = useState('');
  const [selectedShipping, setSelectedShipping] = useState(null);

  // Products purchase state
  const [selectedProducts, setSelectedProducts] = useState([]); // Array of { id, qty, title, price, thumbnail, product_type }
  const [submitting, setSubmitting] = useState(false);
  const [otpSent, setOtpSent] = useState(false);
  const [otpCode, setOtpCode] = useState('');

  // Countdown timer state
  const [timeLeft, setTimeLeft] = useState(3600); // 1 hour countdown
  const [lightboxImage, setLightboxImage] = useState(null);

  useEffect(() => {
    // Fetch landing page configs
    axios.get(`/api/landingpage/${slug}`)
      .then(res => {
        if (res.data.success) {
          const d = res.data.data;
          setPageData(d);

          // Initial selected products: primary product + all additional combo products (checked by default as a bundle offer!)
          const bundle = [];
          if (d.primary_product) {
            bundle.push({
              id: d.primary_product.id,
              qty: 1,
              title: d.primary_product.title,
              price: d.primary_product.price,
              thumbnail: d.primary_product.image,
              product_type: 'admin'
            });
          }
          if (d.additional_products && Array.isArray(d.additional_products)) {
            d.additional_products.forEach(p => {
              bundle.push({
                id: p.id,
                qty: 1,
                title: p.title,
                price: p.price,
                thumbnail: p.image,
                product_type: 'admin'
              });
            });
          }
          setSelectedProducts(bundle);
        } else {
          toast.error('Landing page not found.');
        }
        setLoading(false);
      })
      .catch(err => {
        console.error(err);
        toast.error('Error fetching landing page.');
        setLoading(false);
      });

    // Fetch active shipping charges
    axios.get('/api/shipping-charges')
      .then(res => {
        if (res.data.success && res.data.data.length > 0) {
          setShippingCharges(res.data.data);
          setSelectedShipping(res.data.data[0]); // default to first zone
        }
      })
      .catch(err => console.error("Error fetching shipping charges:", err));

    // Countdown interval
    const timer = setInterval(() => {
      setTimeLeft(prev => (prev > 0 ? prev - 1 : 3600));
    }, 1000);

    return () => clearInterval(timer);
  }, [slug]);

  // Scroll to checkout form helper
  const scrollToCheckout = () => {
    if (checkoutFormRef.current) {
      checkoutFormRef.current.scrollIntoView({ behavior: 'smooth' });
    }
  };

  // Adjust product quantity
  const handleQtyChange = (productId, change) => {
    const updated = selectedProducts.map(p => {
      if (p.id === productId) {
        const nextQty = Math.max(1, p.qty + change);
        return { ...p, qty: nextQty };
      }
      return p;
    });
    setSelectedProducts(updated);
  };

  // Toggle products checkbox
  const toggleProductSelect = (product) => {
    const exists = selectedProducts.find(p => p.id === product.id);
    if (exists) {
      setSelectedProducts(selectedProducts.filter(p => p.id !== product.id));
    } else {
      setSelectedProducts([
        ...selectedProducts,
        {
          id: product.id,
          qty: 1,
          title: product.title,
          price: product.price,
          thumbnail: product.image,
          product_type: 'admin'
        }
      ]);
    }
  };

  // Resend OTP on Landing Page
  const handleResendOtp = () => {
    setOtpCode('');
    setSubmitting(true);
    const payload = {
      name,
      phone,
      address,
      city: city || selectedShipping.name,
      shipping_id: selectedShipping.id,
      items: selectedProducts.map(p => ({
        id: p.id,
        qty: p.qty,
        product_type: 'admin',
        uid: `admin_${p.id}`
      })),
      payment_method: 'cod'
    };

    axios.post('/api/place-order', payload)
      .then(res => {
        if (res.data.otp_required) {
          toast.success(res.data.message || 'নতুন ওটিপি (OTP) পাঠানো হয়েছে!');
        } else if (res.data.success) {
          setName('');
          setPhone('');
          setAddress('');
          setOtpSent(false);
          setOtpCode('');
          navigate(`/order-success?invoice=${res.data.invoice_no || res.data.order_id}`, {
            state: { orders: [res.data.order || res.data.invoice], fromCheckout: true }
          });
        } else {
          toast.error(res.data.message);
        }
        setSubmitting(false);
      })
      .catch(err => {
        console.error(err);
        toast.error(err.response?.data?.message || 'ওটিপি পাঠাতে সমস্যা হয়েছে।');
        setSubmitting(false);
      });
  };

  // Place COD Order
  const handlePlaceOrder = (e) => {
    if (e && e.preventDefault) e.preventDefault();
    if (!name.trim()) return toast.error('দয়া করে আপনার নাম লিখুন।');
    if (!phone.trim() || phone.length < 11) return toast.error('দয়া করে ১১ ডিজিটের সঠিক মোবাইল নম্বর দিন।');
    if (!address.trim()) return toast.error('দয়া করে আপনার পূর্ণাঙ্গ ঠিকানা লিখুন।');
    if (!selectedShipping) return toast.error('ডেলিভারি এরিয়া নির্বাচন করুন।');
    if (selectedProducts.length === 0) return toast.error('দয়া করে অন্তত একটি প্রোডাক্ট সিলেক্ট করুন।');
    if (otpSent && (!otpCode.trim() || otpCode.length < 4)) return toast.error('দয়া করে সঠিক ওটিপি (OTP) কোড দিন।');

    setSubmitting(true);

    const payload = {
      name,
      phone,
      address,
      city: city || selectedShipping.name,
      shipping_id: selectedShipping.id,
      items: selectedProducts.map(p => ({
        id: p.id,
        qty: p.qty,
        product_type: 'admin',
        uid: `admin_${p.id}`
      })),
      payment_method: 'cod',
      otp_code: otpSent ? otpCode : null
    };

    axios.post('/api/place-order', payload)
      .then(res => {
        if (res.data.success) {
          toast.success('আপনার অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে!');

          // Clear forms
          setName('');
          setPhone('');
          setAddress('');
          setOtpSent(false);
          setOtpCode('');

          // Redirect to the unified order success page using navigate
          navigate(`/order-success?invoice=${res.data.invoice_no || res.data.order_id}`, {
            state: { orders: [res.data.order || res.data.invoice], fromCheckout: true }
          });
        } else if (res.data.otp_required) {
          setOtpSent(true);
          toast.success(res.data.message || 'আপনার মোবাইলে ওটিপি (OTP) কোড পাঠানো হয়েছে!');
        } else {
          toast.error(res.data.message || 'অর্ডার করতে সমস্যা হয়েছে। দয়া করে আবার চেষ্টা করুন।');
        }
        setSubmitting(false);
      })
      .catch(err => {
        console.error(err);
        toast.error(err.response?.data?.message || 'অর্ডার করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
        setSubmitting(false);
      });
  };

  // Format countdown timer
  const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  };

  // Calculate totals
  const subtotal = selectedProducts.reduce((sum, p) => sum + (p.price * p.qty), 0);
  const deliveryCharge = selectedShipping ? parseFloat(selectedShipping.charge) : 0;
  const grandTotal = subtotal + deliveryCharge;

  if (loading) {
    return (
      <div className="d-flex align-items-center justify-content-center" style={{ minHeight: '100vh', background: '#f8fafc' }}>
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  if (!pageData) {
    return (
      <div className="container py-5 text-center">
        <h2 className="fw-bold text-danger">Landing page not found or inactive.</h2>
      </div>
    );
  }

  // Dynamic colors customization from Page Settings
  const primaryBg = pageData.bg_color || '#ffffff';
  const accentColor = pageData.button_color || '#e7567c';

  return (
    <div className="visitor-landing-page" style={{ background: primaryBg, fontFamily: "'Hind Siliguri', sans-serif", color: '#1e293b', minHeight: '100vh', paddingBottom: '80px' }}>

      {/* Dynamic Styling Injection */}
      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
        .glowing-btn {
          animation: glow 1.8s infinite;
          background: linear-gradient(135deg, ${accentColor}, #be123c);
          border: none;
        }
        @keyframes glow {
          0% { box-shadow: 0 0 0 0 rgba(231, 86, 124, 0.6); }
          70% { box-shadow: 0 0 0 15px rgba(231, 86, 124, 0); }
          100% { box-shadow: 0 0 0 0 rgba(231, 86, 124, 0); }
        }
        .visitor-landing-page h1, .visitor-landing-page h2, .visitor-landing-page h3, .visitor-landing-page h4, .visitor-landing-page h5 {
          font-family: 'Hind Siliguri', sans-serif;
          font-weight: 700;
        }
        .hover-shadow:hover {
          transform: translateY(-4px);
          box-shadow: 0 10px 20px rgba(0,0,0,0.12) !important;
        }
        .group:hover .group-hover-opacity-100 {
          opacity: 1 !important;
        }
        .gallery-hover-zoom:hover {
          transform: scale(1.08);
        }
      `}</style>

      {/* ── SECTIONS LOOP ── */}
      <div className="sections-container container max-w-4xl py-4">
        {pageData.sections && pageData.sections.length > 0 ? (
          pageData.sections.map((block) => {
            if (block.type === 'two_column_features') {
              const b = block.data;
              return (
                <div key={block.id} className="two-column-features-block my-5">
                  {/* Main Header */}
                  {b.main_title && (
                    <h2 className="text-center text-dark display-6 mb-5 px-3 fw-extrabold" style={{ borderBottom: `3px solid ${accentColor}`, paddingBottom: '12px', display: 'inline-block', left: '50%', transform: 'translateX(-50%)', position: 'relative' }}>
                      {b.main_title}
                    </h2>
                  )}

                  {/* Side-by-side Columns */}
                  <div className="row g-4 mt-2">
                    {/* Left Column - Problems */}
                    <div className="col-md-6">
                      <div className="bg-white rounded-4 border-2 border-danger shadow-sm p-4 h-100" style={{ borderLeft: '6px solid #ef4444' }}>
                        {b.left_title && (
                          <h4 className="text-danger fw-bold mb-4 d-flex align-items-center gap-2">
                            <span className="fs-3">❌</span> {b.left_title}
                          </h4>
                        )}
                        <ul className="list-unstyled d-flex flex-column gap-3 mb-0">
                          {b.left_items && b.left_items.map((item, idx) => item.trim() && (
                            <li key={idx} className="d-flex align-items-start gap-3 p-2 bg-light rounded-3">
                              <span className="text-danger fw-bold mt-1">✕</span>
                              <span className="text-secondary small fw-medium">{item}</span>
                            </li>
                          ))}
                        </ul>
                      </div>
                    </div>

                    {/* Right Column - Advantages */}
                    <div className="col-md-6">
                      <div className="bg-white rounded-4 border-2 border-success shadow-sm p-4 h-100" style={{ borderLeft: '6px solid #22c55e' }}>
                        {b.right_title && (
                          <h4 className="text-success fw-bold mb-4 d-flex align-items-center gap-2">
                            <span className="fs-3">✔</span> {b.right_title}
                          </h4>
                        )}
                        <ul className="list-unstyled d-flex flex-column gap-3 mb-0">
                          {b.right_items && b.right_items.map((item, idx) => item.trim() && (
                            <li key={idx} className="d-flex align-items-start gap-3 p-2 bg-light rounded-3">
                              <span className="text-success fw-bold mt-1">✓</span>
                              <span className="text-secondary small fw-medium">{item}</span>
                            </li>
                          ))}
                        </ul>
                      </div>
                    </div>
                  </div>

                  {/* Optional Bottom Image + Bullet Section */}
                  {b.bottom_enabled && (b.bottom_image || b.bottom_title) && (
                    <div className="bg-white rounded-4 border shadow-sm p-4 mt-5">
                      <div className={`row g-4 align-items-center ${b.bottom_layout === 'image_right' ? 'flex-row-reverse' : ''}`}>
                        {b.bottom_image && (
                          <div className="col-md-5 text-center">
                            <img
                              src={b.bottom_image.startsWith('http') ? b.bottom_image : '/' + b.bottom_image}
                              alt="Highlight"
                              className="img-fluid rounded-4 shadow"
                              style={{ maxHeight: '320px', objectFit: 'cover' }}
                            />
                          </div>
                        )}
                        <div className="col-md-7">
                          {b.bottom_title && <h3 className="fw-bold text-dark mb-4">{b.bottom_title}</h3>}
                          <ul className="list-unstyled d-flex flex-column gap-3 mb-0">
                            {b.bottom_bullets && b.bottom_bullets.map((bullet, idx) => bullet.trim() && (
                              <li key={idx} className="d-flex align-items-center gap-3">
                                <span className="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style={{ width: '24px', height: '24px', flexShrink: 0 }}>
                                  ✓
                                </span>
                                <span className="fw-semibold text-secondary">{bullet}</span>
                              </li>
                            ))}
                          </ul>
                        </div>
                      </div>
                    </div>
                  )}

                  {/* Optional Additional Section / Consumption Rules */}
                  {b.extra_enabled && (b.extra_title || b.extra_desc) && (
                    <div className="bg-light rounded-4 border p-4 mt-5" style={{ borderLeft: `6px solid ${accentColor}` }}>
                      {b.extra_title && (
                        <h4 className="text-dark fw-bold mb-3 d-flex align-items-center gap-2">
                          <Info size={22} className="text-primary" />
                          {b.extra_title}
                        </h4>
                      )}
                      {b.extra_desc && (
                        <div className="text-secondary fw-semibold whitespace-pre-wrap" style={{ lineHeight: '1.8', fontSize: '15px' }}>
                          {b.extra_desc.split('\n').map((line, idx) => (
                            <p key={idx} className="mb-2">{line}</p>
                          ))}
                        </div>
                      )}
                    </div>
                  )}
                </div>
              );
            } else if (block.type === 'video_section') {
              const b = block.data;
              const getYouTubeId = (url) => {
                if (!url) return null;
                const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                const match = url.match(regExp);
                return (match && match[2].length === 11) ? match[2] : null;
              };
              const videoId = getYouTubeId(b?.video_url);

              return (
                <div key={block.id} className="video-section-block my-5 text-center px-3">
                  {b?.title && (
                    <h2 className="text-center text-dark display-6 mb-4 px-3 fw-extrabold" style={{ borderBottom: `3px solid ${accentColor}`, paddingBottom: '12px', display: 'inline-block' }}>
                      {b.title}
                    </h2>
                  )}
                  {videoId ? (
                    <div className="mx-auto w-100" style={{ maxWidth: '100%' }}>
                      <div className="ratio ratio-16x9 shadow-lg rounded-4 overflow-hidden border" style={{ backgroundColor: '#000' }}>
                        <iframe
                          src={`https://www.youtube.com/embed/${videoId}`}
                          title={b.title || 'YouTube video player'}
                          frameBorder="0"
                          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                          allowFullScreen
                          style={{ width: '100%', height: '100%', border: 'none' }}
                        ></iframe>
                      </div>
                    </div>
                  ) : (
                    <div className="alert alert-warning text-center mx-auto" style={{ maxWidth: '600px', borderRadius: '12px' }}>
                      ইউটিউব ভিডিও ইউআরএল (YouTube Video URL) সেট করা হয়নি। অনুগ্রহ করে বিল্ডার থেকে ভিডিও ইউআরএল দিন।
                    </div>
                  )}
                </div>
              );
            } else if (block.type === 'image_gallery') {
              const b = block.data;
              return (
                <div key={block.id} className="image-gallery-block my-5 px-3">
                  {b?.title && (
                    <h2 className="text-center text-dark display-6 mb-5 px-3 fw-extrabold" style={{ borderBottom: `3px solid ${accentColor}`, paddingBottom: '12px', display: 'inline-block', left: '50%', transform: 'translateX(-50%)', position: 'relative' }}>
                      {b.title}
                    </h2>
                  )}
                  {b?.gallery && b.gallery.length > 0 ? (
                    <div className="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 mt-3">
                      {b.gallery.map((img, idx) => (
                        <div key={idx} className="col">
                          <div 
                            onClick={() => setLightboxImage(img)}
                            className="bg-white rounded-3 border overflow-hidden shadow-sm hover-shadow cursor-pointer transition position-relative group"
                            style={{ 
                              cursor: 'zoom-in',
                              transition: 'all 0.3s ease'
                            }}
                          >
                            <div className="ratio ratio-1x1 overflow-hidden">
                              <img 
                                src={img.startsWith('http') ? img : '/' + img} 
                                alt={`gallery-${idx}`} 
                                className="img-fluid object-cover w-100 h-100 gallery-hover-zoom" 
                                style={{
                                  objectFit: 'cover',
                                  transition: 'transform 0.5s ease'
                                }}
                              />
                            </div>
                            <div 
                              className="position-absolute inset-0 d-flex align-items-center justify-content-center opacity-0 group-hover-opacity-100 transition" 
                              style={{ 
                                backgroundColor: 'rgba(0,0,0,0.15)',
                                top: 0,
                                left: 0,
                                right: 0,
                                bottom: 0,
                                transition: 'opacity 0.3s ease'
                              }}
                            >
                              <span className="bg-white text-dark rounded-circle shadow p-2 d-flex align-items-center justify-content-center" style={{ width: '36px', height: '36px' }}>
                                🔍
                              </span>
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <div className="alert alert-warning text-center mx-auto" style={{ maxWidth: '600px', borderRadius: '12px' }}>
                      গ্যালারিতে কোনো ছবি আপলোড করা হয়নি। অনুগ্রহ করে বিল্ডার থেকে ছবি আপলোড করুন।
                    </div>
                  )}
                </div>
              );
            }
            return (
              <div key={block.id} className="text-center py-5 border rounded bg-white my-4 shadow-sm">
                <h4 className="text-muted fw-bold">{block.title || 'Dynamic Page Section'}</h4>
                <p className="text-secondary small font-monospace">Section block of type {block.type} goes here.</p>
              </div>
            );
          })
        ) : (
          /* Fallback view if no builder sections are defined yet */
          <div className="text-center py-5">
            <Award size={48} className="text-secondary mb-3 d-block mx-auto" />
            <h2 className="fw-bold">পণ্য বিবরণী লোড হচ্ছে...</h2>
          </div>
        )}
      </div>

      {/* ── HIGH CONVERTING CHECKOUT ORDER FORM ── */}
      <div ref={checkoutFormRef} className="container max-w-4xl mt-5">

        {/* Urgent header timer bar (Screenshot 4) */}
        <div className="text-center text-white py-3 fw-bold rounded-top shadow" style={{ background: '#1e3a8a', fontSize: '18px' }}>
          <span className="d-inline-flex align-items-center gap-2">
            <Clock size={20} className="animate-pulse" />
            🔥 অফারটি শেষ হওয়ার আগে অর্ডার করুন!
            <span className="badge bg-danger p-2 fs-6 font-monospace">{formatTime(timeLeft)}</span>
          </span>
        </div>

        {/* Pricing bar (Screenshot 4) */}
        <div className="bg-dark text-white text-center py-3 fw-extrabold shadow" style={{ fontSize: '24px', letterSpacing: '0.5px' }}>
          আজকের বিশেষ দাম: ৳{subtotal > 0 ? subtotal : '০.০০'}
        </div>

        {/* Trust Badges Checkmarks grid (Screenshot 4) */}
        <div className="bg-light border shadow-sm p-4 d-grid" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '16px' }}>
          {[
            'কোয়ালিটি নিশ্চিত করে ডেলিভারি',
            'সারা বাংলাদেশে হোম ডেলিভারি',
            'পণ্য চেক করে টাকা দেওয়ার সুযোগ',
            'দ্রুত কাস্টমার সাপোর্ট'
          ].map((checkText, i) => (
            <div key={i} className="d-flex align-items-center gap-2">
              <span className="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style={{ width: '20px', height: '20px', fontSize: '11px', flexShrink: 0 }}>✓</span>
              <span className="fw-bold text-secondary small">{checkText}</span>
            </div>
          ))}
        </div>

        {/* Main Side-by-Side Panels (Screenshot 5) */}
        <div className="row g-4 mt-3 bg-white p-4 rounded-bottom shadow border">
          <h2 className="text-center text-primary fw-extrabold mb-4 display-6">অর্ডার নিশ্চিত করতে ফর্মটি পূরণ করুন</h2>

          {/* Left panel: Customer details */}
          <div className="col-lg-7">
            <form onSubmit={handlePlaceOrder}>
              <div className="card border-light shadow-sm mb-4">
                <div className="card-header bg-light fw-bold text-dark d-flex align-items-center gap-2">
                  <span className="fs-5">👤</span> আপনার তথ্য দিন
                </div>
                <div className="card-body p-4 d-flex flex-column gap-3">
                  <div>
                    <label className="form-label fw-bold text-secondary">আপনার নাম লিখুন <span>*</span></label>
                    <input
                      type="text"
                      className="form-control py-2 shadow-none"
                      placeholder="উদা: আব্দুল্লাহ"
                      value={name}
                      onChange={(e) => setName(e.target.value)}
                      required
                    />
                  </div>
                  <div>
                    <label className="form-label fw-bold text-secondary">আপনার মোবাইল নাম্বার <span>*</span></label>
                    <input
                      type="tel"
                      className="form-control py-2 shadow-none"
                      placeholder="উদা: 017XXXXXXXX"
                      value={phone}
                      onChange={(e) => setPhone(e.target.value)}
                      required
                    />
                  </div>
                  <div>
                    <label className="form-label fw-bold text-secondary">আপনার বিস্তারিত ঠিকানা লিখুন <span>*</span></label>
                    <input
                      type="text"
                      className="form-control py-2 shadow-none"
                      placeholder="গ্রাম/মহল্লা, থানা, জেলা"
                      value={address}
                      onChange={(e) => setAddress(e.target.value)}
                      required
                    />
                  </div>
                  <div>
                    <label className="form-label fw-bold text-secondary">ডেলিভারি এরিয়া নির্বাচন করুন <span>*</span></label>
                    <select
                      className="form-select py-2 shadow-none"
                      value={selectedShipping ? selectedShipping.id : ''}
                      onChange={(e) => {
                        const zone = shippingCharges.find(c => c.id === parseInt(e.target.value));
                        setSelectedShipping(zone);
                      }}
                      required
                    >
                      {shippingCharges.map(charge => (
                        <option key={charge.id} value={charge.id}>{charge.name} - ৳{parseFloat(charge.charge)}</option>
                      ))}
                    </select>
                  </div>

                  {otpSent && (
                    <div className="border border-success rounded-3 p-3 bg-light animate-fade-in mt-3">
                      <label className="form-label fw-bold text-success mb-2"><i className="fas fa-lock me-1"></i> মোবাইলে পাঠানো ওটিপি (OTP) কোড দিন *</label>
                      <input
                        type="text"
                        className="form-control py-2 shadow-none border-success fw-bold text-center mb-2"
                        style={{ fontSize: '18px', letterSpacing: '4px' }}
                        placeholder="------"
                        value={otpCode}
                        onChange={(e) => setOtpCode(e.target.value)}
                        required
                      />
                      <div className="d-flex justify-content-between mt-2 px-1">
                        <span className="small text-muted">কোড পাননি?</span>
                        <button type="button" onClick={handleResendOtp} className="btn btn-link btn-sm text-decoration-none p-0 text-success fw-bold">
                          আবার ওটিপি পাঠান (Resend OTP)
                        </button>
                      </div>
                    </div>
                  )}
                </div>
              </div>

              {/* Payment details panel */}
              <div className="card border-light shadow-sm">
                <div className="card-header bg-light fw-bold text-dark d-flex align-items-center gap-2">
                  <span className="fs-5">💳</span> পেমেন্ট পদ্ধতি
                </div>
                <div className="card-body p-4">
                  <div className="border border-primary rounded-3 p-3 bg-light d-flex justify-content-between align-items-center">
                    <div className="d-flex align-items-center gap-2">
                      <span className="fs-4">💵</span>
                      <div>
                        <div className="fw-bold text-dark">Cash on Delivery</div>
                        <span className="text-secondary extra-small">পণ্য হাতে পেয়ে টাকা পরিশোধ করুন</span>
                      </div>
                    </div>
                    <span className="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style={{ width: '22px', height: '22px' }}>✓</span>
                  </div>
                </div>
              </div>
            </form>
          </div>

          {/* Right panel: Order summary details */}
          <div className="col-lg-5">
            <div className="card border-light shadow-sm h-100" style={{ background: '#f8fafc' }}>
              <div className="card-header bg-light fw-bold text-dark d-flex align-items-center gap-2">
                <span className="fs-5">🛒</span> অর্ডার সামারি
              </div>
              <div className="card-body p-4 d-flex flex-column justify-content-between">

                {/* List of checked bundle products */}
                <div className="d-flex flex-column gap-3 mb-4">
                  {pageData.primary_product && (
                    <div className="d-flex justify-content-between align-items-center p-2 bg-white rounded shadow-sm border border-light">
                      <div className="d-flex align-items-center gap-2">
                        <input
                          type="checkbox"
                          className="form-check-input"
                          checked={!!selectedProducts.find(p => p.id === pageData.primary_product.id)}
                          onChange={() => toggleProductSelect(pageData.primary_product)}
                        />
                        {pageData.primary_product.image && (
                          <img 
                            src={getProductImageUrl(pageData.primary_product.image)} 
                            alt={pageData.primary_product.title}
                            className="rounded"
                            style={{ width: '40px', height: '40px', objectFit: 'cover' }}
                          />
                        )}
                        <span className="fw-bold text-secondary small">{pageData.primary_product.title}</span>
                      </div>
                      <div className="d-flex align-items-center gap-2">
                        <div className="btn-group btn-group-sm">
                          <button onClick={() => handleQtyChange(pageData.primary_product.id, -1)} className="btn btn-outline-secondary">-</button>
                          <span className="btn disabled text-dark bg-white font-monospace">{selectedProducts.find(p => p.id === pageData.primary_product.id)?.qty || 1}</span>
                          <button onClick={() => handleQtyChange(pageData.primary_product.id, 1)} className="btn btn-outline-secondary">+</button>
                        </div>
                        <span className="fw-bold text-primary small">৳{pageData.primary_product.price}</span>
                      </div>
                    </div>
                  )}

                  {pageData.additional_products && pageData.additional_products.map(p => (
                    <div key={p.id} className="d-flex justify-content-between align-items-center p-2 bg-white rounded shadow-sm border border-light">
                      <div className="d-flex align-items-center gap-2">
                        <input
                          type="checkbox"
                          className="form-check-input"
                          checked={!!selectedProducts.find(prod => prod.id === p.id)}
                          onChange={() => toggleProductSelect(p)}
                        />
                        {p.image && (
                          <img 
                            src={getProductImageUrl(p.image)} 
                            alt={p.title}
                            className="rounded"
                            style={{ width: '40px', height: '40px', objectFit: 'cover' }}
                          />
                        )}
                        <span className="fw-bold text-secondary small">{p.title}</span>
                      </div>
                      <div className="d-flex align-items-center gap-2">
                        <div className="btn-group btn-group-sm">
                          <button onClick={() => handleQtyChange(p.id, -1)} className="btn btn-outline-secondary">-</button>
                          <span className="btn disabled text-dark bg-white font-monospace">{selectedProducts.find(prod => prod.id === p.id)?.qty || 1}</span>
                          <button onClick={() => handleQtyChange(p.id, 1)} className="btn btn-outline-secondary">+</button>
                        </div>
                        <span className="fw-bold text-primary small">৳{p.price}</span>
                      </div>
                    </div>
                  ))}
                </div>

                {/* Subtotal, delivery fee, grand total details */}
                <div className="border-top pt-3">
                  <div className="d-flex justify-content-between mb-2">
                    <span className="text-secondary small">সাবটোটাল</span>
                    <span className="fw-bold text-dark font-monospace">৳{subtotal.toLocaleString()}</span>
                  </div>
                  <div className="d-flex justify-content-between mb-3 pb-2 border-bottom">
                    <span className="text-secondary small">ডেলিভারি চার্জ</span>
                    <span className="fw-bold text-dark font-monospace">৳{deliveryCharge.toLocaleString()}</span>
                  </div>
                  <div className="d-flex justify-content-between mb-4">
                    <span className="fw-extrabold text-dark">সর্বমোট</span>
                    <span className="fw-extrabold font-monospace text-primary fs-4">৳{grandTotal.toLocaleString()}</span>
                  </div>

                  {/* Submission Glowing Action Button */}
                  <button
                    onClick={handlePlaceOrder}
                    disabled={submitting || (otpSent && otpCode.length < 4)}
                    className="btn btn-lg w-100 text-white glowing-btn fw-bold py-3 fs-5 shadow"
                    style={{ borderRadius: '14px' }}
                  >
                    {submitting ? 'অর্ডার প্রসেস হচ্ছে...' : (otpSent ? 'ওটিপি যাচাই করে অর্ডার কনফার্ম করুন' : `অর্ডার নিশ্চিত করুন ৳${grandTotal.toLocaleString()}`)}
                  </button>

                  <div className="text-center mt-3 small text-muted d-flex align-items-center justify-content-center gap-2">
                    <Shield size={16} className="text-success" />
                    <span>১০০% অরিজিনাল পণ্য ও সহজ রিটার্ন পলিসি</span>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

      </div>

      {/* ── FLOATING ACTION FOOTER BUTTON FOR MOBILE ── */}
      <div className="fixed-bottom p-3 d-lg-none bg-white border-top shadow-lg" style={{ zIndex: 9999 }}>
        <button
          onClick={scrollToCheckout}
          className="btn btn-lg w-100 glowing-btn text-white fw-bold py-3 fs-5"
          style={{ borderRadius: '12px' }}
        >
          অর্ডার করতে এখানে ক্লিক করুন
        </button>
      </div>

      {/* ── LIGHTBOX DIALOG OVERLAY ── */}
      {lightboxImage && (
        <div 
          onClick={() => setLightboxImage(null)}
          className="fixed inset-0 d-flex align-items-center justify-content-center cursor-zoom-out animate-fade-in" 
          style={{ 
            backgroundColor: 'rgba(0, 0, 0, 0.85)', 
            backdropFilter: 'blur(8px)',
            zIndex: 99999, 
            position: 'fixed', 
            top: 0, 
            left: 0, 
            right: 0, 
            bottom: 0,
            animation: 'fadeIn 0.3s ease-out'
          }}
        >
          <div className="position-relative p-2" style={{ maxWidth: '90%', maxHeight: '90%' }} onClick={e => e.stopPropagation()}>
            <img 
              src={lightboxImage.startsWith('http') ? lightboxImage : '/' + lightboxImage} 
              alt="Lightbox Zoomed" 
              className="img-fluid rounded-3 shadow-lg select-none" 
              style={{ maxHeight: '85vh', objectFit: 'contain' }} 
            />
            <button 
              onClick={() => setLightboxImage(null)}
              className="position-absolute btn btn-light rounded-circle shadow d-flex align-items-center justify-content-center"
              style={{ 
                top: '-20px', 
                right: '-20px', 
                width: '40px', 
                height: '40px', 
                fontSize: '20px', 
                fontWeight: 'bold', 
                color: '#000', 
                zIndex: 100000 
              }}
            >
              ✕
            </button>
          </div>
        </div>
      )}

    </div>
  );
};

export default LandingPageView;
