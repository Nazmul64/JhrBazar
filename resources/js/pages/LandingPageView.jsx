import React, { useState, useEffect, useRef } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axios from 'axios';
import toast from 'react-hot-toast';
import { trackPurchase, trackLead, trackViewItem } from '../utils/dataLayer';
import { getAttributionData } from '../utils/attribution';
import {
  Check, X, Shield, Phone, MapPin, Truck, ShoppingCart,
  ChevronRight, Star, AlertTriangle, Info, Clock, Award,
  ChevronLeft, Play, Heart, Percent
} from 'lucide-react';


const getProductImageUrl = (url) => {
  if (!url) return '/assets/admin/images/no-image.png';
  if (url.startsWith('http')) return url;
  if (url.startsWith('/')) return url;
  if (url.startsWith('uploads/')) return '/' + url;
  return '/uploads/product/' + url;
};

const getLandingPageImageUrl = (url) => {
  if (!url) return '';
  if (url.startsWith('http')) return url;
  if (url.startsWith('/')) return url;
  return '/' + url;
};

const CountdownPricingBlock = ({ data, subtotal, grandTotal }) => {
  const [timeLeft, setTimeLeft] = useState(data?.timer_duration_mins ? (data.timer_duration_mins * 60) : 3600);

  useEffect(() => {
    const timer = setInterval(() => {
      setTimeLeft(prev => (prev > 0 ? prev - 1 : (data?.timer_duration_mins ? (data.timer_duration_mins * 60) : 3600)));
    }, 1000);
    return () => clearInterval(timer);
  }, [data?.timer_duration_mins]);

  const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  };

  const displayPrice = data?.price_type === 'grand_total' ? grandTotal : subtotal;

  return (
    <div className="countdown-pricing-block mt-3 mb-0 px-3">
      {/* Urgent header timer bar */}
      <div className="text-center text-white py-3 fw-bold rounded-top shadow" style={{ background: '#1e3a8a', fontSize: '18px' }}>
        <span className="d-inline-flex align-items-center gap-2">
          <Clock size={20} className="animate-pulse" />
          {data?.timer_text || '🔥 অফারটি শেষ হওয়ার আগে অর্ডার করুন!'}
          <span className="badge bg-danger p-2 fs-6 font-monospace">{formatTime(timeLeft)}</span>
        </span>
      </div>

      {/* Pricing bar */}
      <div className="bg-dark text-white text-center py-3 fw-extrabold shadow rounded-bottom" style={{ fontSize: '24px', letterSpacing: '0.5px' }}>
        {data?.price_text || 'আজকের বিশেষ দাম: ৳'}{displayPrice > 0 ? displayPrice : '০.০০'}
      </div>
    </div>
  );
};

const ReviewSliderBlock = ({ data }) => {
  const [startIndex, setStartIndex] = useState(0);
  const slides = data?.slides || [];

  useEffect(() => {
    if (slides.length <= 1) return;
    const interval = setInterval(() => {
      setStartIndex(prev => (prev === slides.length - 1 ? 0 : prev + 1));
    }, 3000); // Auto slide every 3 seconds
    return () => clearInterval(interval);
  }, [slides.length]);

  if (slides.length === 0) {
    return (
      <div className="alert alert-light border text-center text-muted m-0">
        কোনো রিভিউ ছবি আপলোড করা হয়নি।
      </div>
    );
  }

  const handlePrev = () => {
    setStartIndex(prev => (prev === 0 ? slides.length - 1 : prev - 1));
  };

  const handleNext = () => {
    setStartIndex(prev => (prev === slides.length - 1 ? 0 : prev + 1));
  };

  // Get active items to display based on startIndex (looping around)
  const getVisibleSlides = () => {
    const visible = [];
    for (let i = 0; i < 3; i++) {
      const index = (startIndex + i) % slides.length;
      visible.push(slides[index]);
    }
    return visible;
  };

  const visibleSlides = getVisibleSlides();

  return (
    <div 
      className="review-slider-block my-5 py-5 px-4 rounded-4 shadow-sm" 
      style={{ backgroundColor: data.bg_color || '#ffffff', color: data.text_color || '#1e293b' }}
    >
      {/* Title */}
      {data.title && (
        <h3 className="text-center fw-extrabold mb-5 px-3" style={{ color: '#1e3a8a', fontSize: '26px', lineHeight: '1.4' }}>
          {data.title}
        </h3>
      )}

      {/* Grid Container */}
      <div className="position-relative px-md-5">
        <div className="row g-4 justify-content-center align-items-center">
          
          {/* Mobile view: show only 1 slide */}
          <div className="col-12 d-block d-md-none">
            <div className="bg-white rounded-3 shadow border overflow-hidden p-1 mx-auto" style={{ maxWidth: '350px' }}>
              <img 
                src={getLandingPageImageUrl(slides[startIndex])} 
                alt={`Review ${startIndex + 1}`} 
                className="img-fluid w-100 rounded-2" 
                style={{ objectFit: 'contain', maxHeight: '450px' }}
              />
            </div>
          </div>

          {/* Desktop/Tablet view: show 3 slides (or fallback if fewer slides) */}
          <div className="col-12 d-none d-md-flex gap-4 justify-content-center">
            {slides.length <= 3 ? (
              slides.map((slide, idx) => (
                <div key={idx} style={{ width: 'calc(33.333% - 16px)', maxWidth: '350px' }}>
                  <div className="bg-white rounded-3 shadow border overflow-hidden p-1 h-100">
                    <img 
                      src={getLandingPageImageUrl(slide)} 
                      alt={`Review ${idx + 1}`} 
                      className="img-fluid w-100 rounded-2" 
                      style={{ objectFit: 'contain', maxHeight: '450px' }}
                    />
                  </div>
                </div>
              ))
            ) : (
              visibleSlides.map((slide, idx) => (
                <div key={idx} style={{ width: 'calc(33.333% - 16px)', maxWidth: '350px' }} className="animate-fade-in">
                  <div className="bg-white rounded-3 shadow border overflow-hidden p-1 h-100">
                    <img 
                      src={getLandingPageImageUrl(slide)} 
                      alt={`Review-visible-${idx}`} 
                      className="img-fluid w-100 rounded-2" 
                      style={{ objectFit: 'contain', maxHeight: '450px' }}
                    />
                  </div>
                </div>
              ))
            )}
          </div>

        </div>

        {/* Navigation Arrows (Show only if there are more slides than visible) */}
        {slides.length > 1 && (
          <>
            <button 
              onClick={handlePrev}
              className="position-absolute btn btn-light rounded-circle shadow-sm border d-flex align-items-center justify-content-center cursor-pointer hover-shadow"
              style={{ left: '-15px', top: '50%', transform: 'translateY(-50%)', width: '44px', height: '44px', zIndex: 10, fontSize: '20px', fontWeight: 'bold' }}
            >
              &lt;
            </button>
            <button 
              onClick={handleNext}
              className="position-absolute btn btn-light rounded-circle shadow-sm border d-flex align-items-center justify-content-center cursor-pointer hover-shadow"
              style={{ right: '-15px', top: '50%', transform: 'translateY(-50%)', width: '44px', height: '44px', zIndex: 10, fontSize: '20px', fontWeight: 'bold' }}
            >
              &gt;
            </button>
          </>
        )}
      </div>

      {/* Dots Indicator */}
      {slides.length > 1 && (
        <div className="d-flex justify-content-center gap-2 mt-4">
          {slides.map((_, idx) => (
            <button
              key={idx}
              onClick={() => setStartIndex(idx)}
              className="rounded-circle border-0"
              style={{ 
                width: '10px', 
                height: '10px', 
                backgroundColor: startIndex === idx ? '#1e3a8a' : '#cbd5e1',
                transition: 'background-color 0.3s'
              }}
            />
          ))}
        </div>
      )}
    </div>
  );
};

const ProductHeroBlock = ({ data, scrollToCheckout }) => {
  const getYouTubeId = (url) => {
    if (!url) return null;
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
  };
  const videoId = getYouTubeId(data?.video_url);

  return (
    <div 
      className="product-hero-block my-5 py-5 px-4 rounded-4 shadow-sm"
      style={{ backgroundColor: data?.bg_color || '#ffffff', color: data?.text_color || '#1e293b' }}
    >
      <div className="text-center mb-5">
        {data?.title && <h1 className="fw-extrabold display-5 mb-3" style={{ color: '#1e3a8a' }}>{data.title}</h1>}
        {data?.subtitle && <p className="lead fw-medium text-secondary mx-auto" style={{ maxWidth: '750px' }}>{data.subtitle}</p>}
      </div>

      <div className="row g-5 align-items-center">
        {/* Left column: bullet highlights and button */}
        <div className="col-lg-6">
          <div className="d-flex flex-column gap-3 mb-4">
            {data?.bullets && data.bullets.map((bullet, idx) => bullet.trim() && (
              <div key={idx} className="d-flex align-items-center gap-3 py-2 border-bottom" style={{ borderColor: 'rgba(0,0,0,0.08)' }}>
                <span className="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow-sm" style={{ width: '24px', height: '24px', flexShrink: 0 }}>
                  <Check size={14} style={{ strokeWidth: '3.5px' }} />
                </span>
                <span className="fw-bold" style={{ fontSize: '16px' }}>{bullet}</span>
              </div>
            ))}
          </div>
          <div className="text-center text-lg-start">
            <button
              onClick={scrollToCheckout}
              className="btn btn-lg px-5 py-3 glowing-btn fw-bold text-white fs-5"
              style={{ borderRadius: '10px' }}
            >
              {data?.button_text || 'অর্ডার করুন'}
            </button>
          </div>
        </div>

        {/* Right column: Youtube player or Image */}
        <div className="col-lg-6 text-center">
          {videoId ? (
            <div className="ratio ratio-16x9 shadow-lg rounded-4 overflow-hidden border bg-black">
              <iframe
                src={`https://www.youtube.com/embed/${videoId}`}
                title={data?.title || 'Video Player'}
                allowFullScreen
                style={{ border: 'none' }}
              ></iframe>
            </div>
          ) : data?.image_path ? (
            <img
              src={getLandingPageImageUrl(data.image_path)}
              alt="Hero Block"
              className="img-fluid rounded-4 shadow-lg border"
              style={{ maxHeight: '380px', objectFit: 'contain' }}
            />
          ) : (
            <div className="alert alert-light border text-center text-muted m-0 p-5 rounded-4 shadow-sm">
              <Play size={40} className="text-secondary mb-3 d-block mx-auto" />
              কোনো ভিডিও বা ছবি যোগ করা হয়নি।
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

const PriceBoxBlock = ({ data, scrollToCheckout }) => {
  return (
    <div 
      className="price-box-block my-5 py-5 px-4 rounded-4 shadow-sm"
      style={{ backgroundColor: data?.bg_color || '#f8fafc', color: data?.text_color || '#1e293b' }}
    >
      <div className="card mx-auto border-0 shadow-lg overflow-hidden position-relative" style={{ maxWidth: '500px', borderRadius: '20px' }}>
        {/* Badge Ribbon */}
        {data?.badge_text && (
          <div 
            className="position-absolute bg-danger text-white text-center py-1 fw-bold text-uppercase" 
            style={{ 
              top: '25px', 
              right: '-45px', 
              width: '180px', 
              transform: 'rotate(45deg)', 
              fontSize: '13px', 
              letterSpacing: '1px',
              zIndex: 5,
              boxShadow: '0 2px 4px rgba(0,0,0,0.15)'
            }}
          >
            {data.badge_text}
          </div>
        )}

        <div className="card-body p-5 text-center bg-white">
          {data?.title && <h3 className="fw-extrabold text-primary mb-4" style={{ fontSize: '24px' }}>{data.title}</h3>}
          
          <div className="d-flex align-items-center justify-content-center gap-3 mb-3">
            {data?.original_price && (
              <span className="text-decoration-line-through text-muted fw-bold" style={{ fontSize: '20px' }}>
                ৳{data.original_price}
              </span>
            )}
            {data?.discounted_price && (
              <span className="text-danger fw-extrabold" style={{ fontSize: '36px' }}>
                ৳{data.discounted_price}
              </span>
            )}
          </div>

          {data?.save_amount && (
            <div className="d-inline-block bg-danger-subtle text-danger px-4 py-2 rounded-pill fw-bold mb-4" style={{ fontSize: '15px' }}>
              আপনার মোট সাশ্রয় ৳{data.save_amount}!
            </div>
          )}

          <div className="mt-2">
            <button
              onClick={scrollToCheckout}
              className="btn btn-lg px-5 py-3 w-100 text-white glowing-btn fw-bold fs-5"
              style={{ borderRadius: '12px' }}
            >
              <ShoppingCart className="me-2" size={20} />
              {data?.button_text || 'অর্ডার করুন'}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

const FeatureListBlock = ({ data }) => {
  const renderIcon = (iconName) => {
    switch (iconName) {
      case 'leaf':
        return <span className="fs-3">🌿</span>;
      case 'shield':
        return <Shield className="text-success" size={28} />;
      case 'clock':
        return <Clock className="text-warning" size={28} />;
      case 'star':
        return <Star className="text-warning" size={28} />;
      case 'award':
        return <Award className="text-primary" size={28} />;
      case 'heart':
        return <Heart className="text-danger" size={28} />;
      default:
        return <Check className="text-success" size={28} />;
    }
  };

  return (
    <div 
      className="feature-list-block my-5 py-5 px-4 rounded-4 shadow-sm"
      style={{ backgroundColor: data?.bg_color || '#ffffff', color: data?.text_color || '#1e293b' }}
    >
      {data?.title && (
        <h2 className="text-center fw-extrabold mb-5 display-6" style={{ color: '#1e3a8a' }}>
          {data.title}
        </h2>
      )}

      <div className="row g-4 justify-content-center">
        {data?.features && data.features.map((feat, idx) => (
          <div key={idx} className="col-md-4">
            <div className="bg-light rounded-4 p-4 border shadow-sm hover-shadow h-100 transition d-flex flex-column gap-3">
              <div className="d-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm border" style={{ width: '56px', height: '56px' }}>
                {renderIcon(feat.icon)}
              </div>
              <div>
                <h5 className="fw-bold text-dark mb-2">{feat.title || 'Feature Title'}</h5>
                <p className="text-secondary small mb-0" style={{ lineHeight: '1.6' }}>{feat.desc}</p>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

const BannerSliderBlock = ({ data }) => {
  const [startIndex, setStartIndex] = useState(0);
  const slides = data?.slides || [];
  const autoPlay = data?.auto_play !== false;

  useEffect(() => {
    if (slides.length <= 1 || !autoPlay) return;
    const interval = setInterval(() => {
      setStartIndex(prev => (prev === slides.length - 1 ? 0 : prev + 1));
    }, 3000);
    return () => clearInterval(interval);
  }, [slides.length, autoPlay]);

  if (slides.length === 0) {
    return (
      <div className="alert alert-light border text-center text-muted m-0 p-5 rounded-4 shadow-sm">
        কোনো ব্যানার ছবি আপলোড করা হয়নি।
      </div>
    );
  }

  const handlePrev = () => {
    setStartIndex(prev => (prev === 0 ? slides.length - 1 : prev - 1));
  };

  const handleNext = () => {
    setStartIndex(prev => (prev === slides.length - 1 ? 0 : prev + 1));
  };

  return (
    <div 
      className="banner-slider-block my-5 py-4 px-3 rounded-4 shadow-sm position-relative overflow-hidden" 
      style={{ backgroundColor: data?.bg_color || '#ffffff' }}
    >
      <div className="position-relative overflow-hidden rounded-3 shadow-lg mx-auto" style={{ maxWidth: '800px', aspectRatio: '16/9' }}>
        <img 
          src={getLandingPageImageUrl(slides[startIndex])} 
          alt={`Banner Slide ${startIndex + 1}`} 
          className="img-fluid w-100 h-100 object-fit-cover" 
          style={{ transition: 'all 0.5s ease-in-out' }}
        />

        {slides.length > 1 && (
          <>
            <button 
              onClick={handlePrev}
              className="position-absolute btn btn-light rounded-circle shadow-sm border d-flex align-items-center justify-content-center cursor-pointer hover-shadow"
              style={{ left: '15px', top: '50%', transform: 'translateY(-50%)', width: '40px', height: '40px', zIndex: 10 }}
            >
              <ChevronLeft size={20} />
            </button>
            <button 
              onClick={handleNext}
              className="position-absolute btn btn-light rounded-circle shadow-sm border d-flex align-items-center justify-content-center cursor-pointer hover-shadow"
              style={{ right: '15px', top: '50%', transform: 'translateY(-50%)', width: '40px', height: '40px', zIndex: 10 }}
            >
              <ChevronRight size={20} />
            </button>
          </>
        )}
      </div>

      {slides.length > 1 && (
        <div className="d-flex justify-content-center gap-2 mt-4">
          {slides.map((_, idx) => (
            <button
              key={idx}
              onClick={() => setStartIndex(idx)}
              className="rounded-circle border-0"
              style={{ 
                width: '10px', 
                height: '10px', 
                backgroundColor: startIndex === idx ? '#1e3a8a' : '#cbd5e1',
                transition: 'background-color 0.3s'
              }}
            />
          ))}
        </div>
      )}
    </div>
  );
};

const CustomHtmlBlock = ({ data }) => {
  return (
    <div className="custom-html-block my-5">
      <div dangerouslySetInnerHTML={{ __html: data?.html_content || '' }} />
    </div>
  );
};

const TextLeftImageRightBlock = ({ data }) => {
  return (
    <div 
      className="text-left-image-right-block my-5 py-5 px-4 rounded-4 shadow-sm"
      style={{ backgroundColor: data?.bg_color || '#ffffff', color: data?.text_color || '#1e293b' }}
    >
      <div className="row g-5 align-items-center justify-content-center">
        {/* Left Column: Title and Description */}
        <div className="col-lg-6 d-flex flex-column gap-4 text-start">
          {data?.title && (
            <h2 className="fw-extrabold m-0" style={{ color: '#2e4f40', fontSize: '28px', lineHeight: '1.4' }}>
              {data.title}
            </h2>
          )}

          {data?.description && (
            <p className="m-0 text-secondary fw-semibold" style={{ fontSize: '16px', lineHeight: '1.8' }}>
              {data.description}
            </p>
          )}
        </div>

        {/* Right Column: Image */}
        <div className="col-lg-6 text-center">
          {data?.image_path ? (
            <img
              src={getLandingPageImageUrl(data.image_path)}
              alt={data.title || 'Section Image'}
              className="img-fluid rounded-3 shadow"
              style={{ maxHeight: '350px', objectFit: 'contain' }}
            />
          ) : (
            <div className="alert alert-light border text-center text-muted m-0">
              কোনো ছবি আপলোড করা হয়নি।
            </div>
          )}
        </div>
      </div>
    </div>
  );
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

    const attribution = getAttributionData();
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
      otp_code: otpSent ? otpCode : null,
      // Attribution Data
      session_id: attribution.session_id,
      utm_source: attribution.utm_source,
      utm_medium: attribution.utm_medium,
      utm_campaign: attribution.utm_campaign,
      click_id: attribution.click_id,
      click_id_type: attribution.click_id_type,
      detected_platform: attribution.detected_platform,
    };

    // Track lead event
    trackLead(landingPage?.title || 'Landing Page Order', { name, phone, address });

    axios.post('/api/place-order', payload)
      .then(res => {
        if (res.data.success) {
          toast.success('আপনার অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে!');

          // Dispatch Purchase event with customer information
          const firstOrder = res.data.orders?.[0] || res.data.order || {
            id: res.data.invoice_no || res.data.order_id || Date.now(),
            total: grandTotal,
            shipping_charge: selectedShipping?.charge || 0,
            payment_method: 'cod',
          };
          trackPurchase(firstOrder, {
            name: name,
            phone: phone,
            address: address,
            district: city || selectedShipping?.name || 'Dhaka',
          }, selectedProducts);

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
                              src={getLandingPageImageUrl(b.bottom_image)}
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
            } else if (block.type === 'countdown_pricing') {
              const b = block.data;
              return (
                <CountdownPricingBlock 
                  key={block.id} 
                  data={b} 
                  subtotal={subtotal} 
                  grandTotal={grandTotal} 
                />
              );
            } else if (block.type === 'trust_badges') {
              const b = block.data;
              return (
                <div key={block.id} className="trust-badges-block mt-3 mb-0 px-3">
                  <div className="bg-light border rounded shadow-sm p-4 d-grid" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '16px' }}>
                    {[
                      b?.badge1,
                      b?.badge2,
                      b?.badge3,
                      b?.badge4
                    ].filter(Boolean).map((checkText, i) => (
                      <div key={i} className="d-flex align-items-center gap-2">
                        <span className="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style={{ width: '20px', height: '20px', fontSize: '11px', flexShrink: 0 }}>✓</span>
                        <span className="fw-bold text-secondary small">{checkText}</span>
                      </div>
                    ))}
                  </div>
                </div>
              );
            } else if (block.type === 'three_column_grid') {
              const b = block.data;
              return (
                <div key={block.id} className="three-column-grid-block mt-3 mb-0 px-3">
                  {/* Main Title Banner matching screenshot */}
                  {b.main_title && (
                    <div className="text-center text-white py-3 px-4 rounded shadow mb-3" style={{ background: '#0078d7', fontSize: '20px', fontWeight: 'bold' }}>
                      {b.main_title}
                    </div>
                  )}

                  {/* 3-column Grid (4-4-4) */}
                  <div className="row g-4 mt-2">
                    {b.cards && b.cards.map((card, idx) => (
                      <div key={idx} className="col-md-4">
                        <div className="bg-white rounded-3 shadow-sm h-100 border overflow-hidden transition hover-shadow">
                          {/* Question Blue Header band */}
                          <div className="text-white py-2 px-3 fw-bold text-center" style={{ background: '#0078d7', fontSize: '15px' }}>
                            <span className="text-danger fw-extrabold me-1">?</span> {card.title}
                          </div>
                          {/* Body with Emoji and Description */}
                          <div className="p-3 bg-white" style={{ color: '#be123c', fontSize: '14.5px', lineHeight: '1.6' }}>
                            <span className="me-2 fs-5">{card.icon_emoji}</span>
                            <span className="fw-semibold">{card.desc}</span>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
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
                                src={getLandingPageImageUrl(img)} 
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
            } else if (block.type === 'video_image_order') {
              const b = block.data;
              const getYouTubeId = (url) => {
                if (!url) return null;
                const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                const match = url.match(regExp);
                return (match && match[2].length === 11) ? match[2] : null;
              };
              const videoId = getYouTubeId(b?.video_url);

              return (
                <div key={block.id} className="video-image-order-block my-5 px-3">
                  {/* Top Title */}
                  {b?.top_title && (
                    <h3 className="text-center text-dark fw-extrabold mb-4" style={{ fontSize: '22px', lineHeight: '1.6' }}>
                      {b.top_title}
                    </h3>
                  )}

                  {/* Middle Row: Left Video, Right Image */}
                  <div className="row g-4 align-items-center justify-content-center mt-2">
                    {/* Left Column: Video */}
                    <div className="col-md-6 text-center">
                      {videoId ? (
                        <div className="ratio ratio-16x9 shadow rounded-3 overflow-hidden border" style={{ backgroundColor: '#000' }}>
                          <iframe
                            src={`https://www.youtube.com/embed/${videoId}`}
                            title={b.top_title || 'YouTube video player'}
                            frameBorder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowFullScreen
                            style={{ width: '100%', height: '100%', border: 'none' }}
                          ></iframe>
                        </div>
                      ) : (
                        <div className="alert alert-warning text-center">
                          ইউটিউব ভিডিও সেট করা হয়নি।
                        </div>
                      )}
                    </div>

                    {/* Right Column: Image */}
                    <div className="col-md-6 text-center">
                      {b?.image_path ? (
                        <img
                          src={getLandingPageImageUrl(b.image_path)}
                          alt={b.bottom_title || 'Section Image'}
                          className="img-fluid rounded-3 shadow"
                          style={{ maxHeight: '350px', objectFit: 'contain' }}
                        />
                      ) : (
                        <div className="alert alert-light border text-center text-muted">
                          কোনো ছবি আপলোড করা হয়নি।
                        </div>
                      )}
                    </div>
                  </div>

                  {/* Bottom Title */}
                  {b?.bottom_title && (
                    <div className="text-center mt-4 text-dark fw-bold" style={{ fontSize: '16px', lineHeight: '1.6' }}>
                      {b.bottom_title}
                    </div>
                  )}

                  {/* CTA Button */}
                  <div className="text-center mt-4">
                    <button
                      onClick={scrollToCheckout}
                      className="btn btn-lg px-5 text-white glowing-btn fw-bold py-3 fs-5"
                      style={{ borderRadius: '10px' }}
                    >
                      {b?.button_text || 'অর্ডার করুন'}
                    </button>
                  </div>
                </div>
              );
            } else if (block.type === 'features_image_right') {
              const b = block.data;
              const sectionBg = b?.bg_color || '#2e4f40';
              const sectionText = b?.text_color || '#ffffff';

              return (
                <div 
                  key={block.id} 
                  className="features-image-right-block my-5 py-5 px-4 rounded-4 shadow"
                  style={{ backgroundColor: sectionBg, color: sectionText }}
                >
                  {/* Section Title */}
                  {b?.title && (
                    <h2 className="text-center fw-extrabold mb-5 px-3 display-6" style={{ letterSpacing: '0.5px' }}>
                      {b.title}
                    </h2>
                  )}

                  {/* Columns Row */}
                  <div className="row g-5 align-items-center">
                    {/* Left Column: Text Items */}
                    <div className="col-lg-7">
                      <div className="d-flex flex-column">
                        {b?.items && b.items.map((item, idx) => item.trim() && (
                          <div 
                            key={idx} 
                            className="d-flex align-items-center gap-3 py-3 border-bottom animate-fade-in"
                            style={{ borderColor: 'rgba(255, 255, 255, 0.15)' }}
                          >
                            <span 
                              className="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm"
                              style={{ width: '24px', height: '24px', flexShrink: 0, color: sectionBg }}
                            >
                              <Check size={15} style={{ strokeWidth: '3.5px' }} />
                            </span>
                            <span className="fw-semibold text-lg" style={{ fontSize: '16px' }}>{item}</span>
                          </div>
                        ))}
                      </div>
                    </div>

                    {/* Right Column: Image */}
                    <div className="col-lg-5 text-center">
                      {b?.image_path ? (
                        <div className="p-2 bg-white rounded-4 shadow-sm" style={{ display: 'inline-block' }}>
                          <img
                            src={getLandingPageImageUrl(b.image_path)}
                            alt={b.title || 'Product Features'}
                            className="img-fluid rounded-3"
                            style={{ maxHeight: '400px', objectFit: 'contain' }}
                          />
                        </div>
                      ) : (
                        <div className="alert alert-light border text-center text-muted m-0">
                          কোনো ছবি আপলোড করা হয়নি।
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              );
            } else if (block.type === 'usage_rules') {
              const b = block.data;
              const sectionBg = b?.bg_color || '#ffffff';
              const sectionText = b?.text_color || '#1e293b';

              return (
                <div 
                  key={block.id} 
                  className="usage-rules-block my-5 py-5 px-4 rounded-4 shadow-sm"
                  style={{ backgroundColor: sectionBg, color: sectionText }}
                >
                  <div className="row g-5 align-items-center justify-content-center">
                    {/* Left Column: Title, Description and CTA Button */}
                    <div className="col-lg-6 text-center d-flex flex-column align-items-center gap-4 justify-content-center">
                      {b?.title && (
                        <h2 className="fw-extrabold m-0" style={{ color: '#2e4f40', fontSize: '28px' }}>
                          {b.title}
                        </h2>
                      )}

                      {b?.description && (
                        <p className="fw-bold m-0" style={{ fontSize: '18px', lineHeight: '1.8', maxWidth: '500px' }}>
                          {b.description}
                        </p>
                      )}

                      <div>
                        <button
                          onClick={scrollToCheckout}
                          className="btn btn-lg px-5 text-white glowing-btn fw-bold py-3 fs-5"
                          style={{ borderRadius: '10px' }}
                        >
                          {b?.button_text || 'অর্ডার করুন'}
                        </button>
                      </div>
                    </div>

                    {/* Right Column: Image */}
                    <div className="col-lg-6 text-center">
                      {b?.image_path ? (
                        <img
                          src={getLandingPageImageUrl(b.image_path)}
                          alt={b.title || 'Usage Rules'}
                          className="img-fluid rounded-3 shadow"
                          style={{ maxHeight: '350px', objectFit: 'contain' }}
                        />
                      ) : (
                        <div className="alert alert-light border text-center text-muted m-0">
                          কোনো ছবি আপলোড করা হয়নি।
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              );
            } else if (block.type === 'review_slider') {
              const b = block.data;
              return (
                <ReviewSliderBlock 
                  key={block.id} 
                  data={b} 
                />
              );
            } else if (block.type === 'product_hero') {
              const b = block.data;
              return (
                <ProductHeroBlock 
                  key={block.id} 
                  data={b} 
                  scrollToCheckout={scrollToCheckout}
                />
              );
            } else if (block.type === 'price_box') {
              const b = block.data;
              return (
                <PriceBoxBlock 
                  key={block.id} 
                  data={b} 
                  scrollToCheckout={scrollToCheckout}
                />
              );
            } else if (block.type === 'feature_list') {
              const b = block.data;
              return (
                <FeatureListBlock 
                  key={block.id} 
                  data={b} 
                />
              );
            } else if (block.type === 'banner_slider') {
              const b = block.data;
              return (
                <BannerSliderBlock 
                  key={block.id} 
                  data={b} 
                />
              );
            } else if (block.type === 'custom_html') {
              const b = block.data;
              return (
                <CustomHtmlBlock 
                  key={block.id} 
                  data={b} 
                />
              );
            } else if (block.type === 'text_left_image_right') {
              const b = block.data;
              return (
                <TextLeftImageRightBlock 
                  key={block.id} 
                  data={b} 
                />
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
      <div ref={checkoutFormRef} className="container max-w-4xl mt-2">

        {/* Trust Badges and Countdown Timer are now added dynamically from the Page Builder as section blocks */}

        {/* Main Side-by-Side Panels (Screenshot 5) */}
        <div className="row g-4 mt-2 bg-white p-4 rounded shadow border">
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

      {/* ── FLOATING ACTION FOOTER BUTTONS ── */}
      {/* Desktop View (Bottom-Center Float) */}
      <div className="d-none d-lg-block" style={{ position: 'fixed', left: '50%', transform: 'translateX(-50%)', bottom: '30px', zIndex: 9999 }}>
        <button
          onClick={scrollToCheckout}
          className="btn btn-lg glowing-btn text-white fw-extrabold px-4 py-3 rounded-pill shadow-lg d-flex align-items-center gap-2 border-0"
          style={{ fontSize: '18px', transition: 'all 0.3s ease' }}
        >
          <ShoppingCart size={22} className="animate-bounce" />
          <span>অর্ডার করুন</span>
        </button>
      </div>

      {/* Mobile View (Fixed Bottom Bar) */}
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
              src={getLandingPageImageUrl(lightboxImage)} 
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
