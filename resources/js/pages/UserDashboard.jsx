import React, { useEffect, useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import axios from 'axios';
import { toast } from 'react-hot-toast';

const UserDashboard = () => {
    const navigate = useNavigate();
    const [user, setUser] = useState(null);
    const [stats, setStats] = useState({ order_count: 0, wishlist_count: 0 });
    const [orders, setOrders] = useState([]);
    const [wishlist, setWishlist] = useState([]);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState('overview');
    
    // Profile Update State
    const [profileData, setProfileData] = useState({ name: '', email: '', phone: '' });
    const [updatingProfile, setUpdatingProfile] = useState(false);

    // Password Update State
    const [passwordData, setPasswordData] = useState({ current_password: '', password: '', password_confirmation: '' });
    const [updatingPassword, setUpdatingPassword] = useState(false);

    // Review Modal State
    const [showReviewModal, setShowReviewModal] = useState(false);
    const [reviewProduct, setReviewProduct] = useState(null);
    const [reviewRating, setReviewRating] = useState(5);
    const [reviewComment, setReviewComment] = useState('');
    const [submittingReview, setSubmittingReview] = useState(false);

    useEffect(() => {
        const token = localStorage.getItem('auth_token');
        if (!token) {
            navigate('/customer/login');
            return;
        }

        const fetchData = async () => {
            try {
                const headers = { Authorization: `Bearer ${token}` };
                
                // Fetch User
                const userRes = await axios.get('/api/user', { headers });
                setUser(userRes.data);
                setProfileData({
                    name: userRes.data.name || '',
                    email: userRes.data.email || '',
                    phone: userRes.data.phone || ''
                });

                // Fetch Full Dashboard Data (Stats + Recent Orders)
                const dashRes = await axios.get('/api/customer/dashboard', { headers });
                if (dashRes.data.success) {
                    setStats({
                        order_count: dashRes.data.data.order_count,
                        wishlist_count: dashRes.data.data.wishlist_count
                    });
                }

                // Fetch Full Order History
                const ordersRes = await axios.get('/api/customer/orders', { headers });
                if (ordersRes.data.success) {
                    setOrders(ordersRes.data.data);
                }

                // Fetch Wishlist
                const wishlistRes = await axios.get('/api/customer/wishlist', { headers });
                if (wishlistRes.data.success) {
                    setWishlist(wishlistRes.data.data);
                }

            } catch (err) {
                console.error("Dashboard error:", err);
                if (err.response?.status === 401) {
                    localStorage.removeItem('auth_token');
                    navigate('/customer/login');
                }
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, [navigate]);

    const handleLogout = async () => {
        const token = localStorage.getItem('auth_token');
        try {
            await axios.post('/api/logout', {}, {
                headers: { Authorization: `Bearer ${token}` }
            });
        } catch (err) {
            console.error(err);
        } finally {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            toast.success("সফলভাবে লগআউট করা হয়েছে");
            navigate('/customer/login');
        }
    };

    const handleUpdateProfile = async (e) => {
        e.preventDefault();
        setUpdatingProfile(true);
        try {
            const token = localStorage.getItem('auth_token');
            const res = await axios.post('/api/customer/update-profile', profileData, {
                headers: { Authorization: `Bearer ${token}` }
            });
            if (res.data.success) {
                setUser(res.data.user);
                toast.success(res.data.message);
            }
        } catch (err) {
            toast.error(err.response?.data?.message || "প্রোফাইল আপডেট করতে সমস্যা হয়েছে");
        } finally {
            setUpdatingProfile(false);
        }
    };

    const handleUpdatePassword = async (e) => {
        e.preventDefault();
        setUpdatingPassword(true);
        try {
            const token = localStorage.getItem('auth_token');
            const res = await axios.post('/api/customer/update-password', passwordData, {
                headers: { Authorization: `Bearer ${token}` }
            });
            if (res.data.success) {
                toast.success(res.data.message);
                setPasswordData({ current_password: '', password: '', password_confirmation: '' });
            }
        } catch (err) {
            toast.error(err.response?.data?.message || "পাসওয়ার্ড পরিবর্তন করতে সমস্যা হয়েছে");
        } finally {
            setUpdatingPassword(false);
        }
    };

    const openReviewModal = (product) => {
        if (!product) {
            toast.error("প্রোডাক্ট তথ্য পাওয়া যায়নি");
            return;
        }
        setReviewProduct(product);
        setShowReviewModal(true);
    };

    const handleSubmitReview = async () => {
        if (!reviewComment) {
            toast.error("দয়া করে আপনার মন্তব্য লিখুন");
            return;
        }
        setSubmittingReview(true);
        try {
            const token = localStorage.getItem('auth_token');
            const res = await axios.post('/api/reviews', {
                product_id: reviewProduct.id,
                product_type: reviewProduct.product_type || 'admin',
                rating: reviewRating,
                comment: reviewComment
            }, {
                headers: { Authorization: `Bearer ${token}` }
            });
            if (res.data.success) {
                toast.success(res.data.message);
                setShowReviewModal(false);
                setReviewComment('');
            }
        } catch (err) {
            toast.error(err.response?.data?.message || "রিভিউ জমা দিতে সমস্যা হয়েছে");
        } finally {
            setSubmittingReview(false);
        }
    };

    if (loading) return (
        <MasterLayout>
            <div className="container py-5 text-center">
                <div className="spinner-border text-primary" role="status"></div>
                <p className="mt-3">লোড হচ্ছে...</p>
            </div>
        </MasterLayout>
    );

    return (
        <MasterLayout>
            <div className="container py-4 py-lg-5">
                <div className="row g-4">
                    {/* Sidebar */}
                    <div className="col-lg-3">
                        <div className="card border border-light-subtle shadow-sm overflow-hidden" style={{ borderRadius: '20px' }}>
                            <div className="bg-primary p-4 text-center text-white shadow-sm">
                                <div
                                    className="rounded-circle bg-white mx-auto mb-3 d-flex align-items-center justify-content-center text-primary fw-bold shadow-sm"
                                    style={{ width: '70px', height: '70px', fontSize: '28px', border: '4px solid rgba(255,255,255,0.3)' }}
                                >
                                    {user?.name?.charAt(0).toUpperCase()}
                                </div>
                                <h6 className="fw-bold mb-0">{user?.name}</h6>
                                <p className="small mb-0 opacity-75">{user?.email}</p>
                            </div>
                            <div className="p-3">
                                <nav className="nav flex-column dashboard-nav gap-2">
                                    <button 
                                        type="button"
                                        onClick={() => setActiveTab('overview')} 
                                        className={`nav-link text-start border-0 py-3 px-4 rounded-4 transition-all ${activeTab === 'overview' ? 'active bg-primary text-white shadow' : 'text-dark bg-light-subtle hover-bg-light border'}`}
                                        style={{ fontWeight: '600' }}
                                    >
                                        <i className="bi bi-grid-fill me-2"></i> ড্যাশবোর্ড
                                    </button>
                                    <button 
                                        type="button"
                                        onClick={() => setActiveTab('orders')} 
                                        className={`nav-link text-start border-0 py-3 px-4 rounded-4 transition-all ${activeTab === 'orders' ? 'active bg-primary text-white shadow' : 'text-dark bg-light-subtle hover-bg-light border'}`}
                                        style={{ fontWeight: '600' }}
                                    >
                                        <i className="bi bi-bag-check-fill me-2"></i> আমার অর্ডারসমূহ
                                    </button>
                                    <button 
                                        type="button"
                                        onClick={() => setActiveTab('wishlist')} 
                                        className={`nav-link text-start border-0 py-3 px-4 rounded-4 transition-all ${activeTab === 'wishlist' ? 'active bg-primary text-white shadow' : 'text-dark bg-light-subtle hover-bg-light border'}`}
                                        style={{ fontWeight: '600' }}
                                    >
                                        <i className="bi bi-heart-fill me-2"></i> উইশলিস্ট
                                    </button>
                                    <button 
                                        type="button"
                                        onClick={() => setActiveTab('profile')} 
                                        className={`nav-link text-start border-0 py-3 px-4 rounded-4 transition-all ${activeTab === 'profile' ? 'active bg-primary text-white shadow' : 'text-dark bg-light-subtle hover-bg-light border'}`}
                                        style={{ fontWeight: '600' }}
                                    >
                                        <i className="bi bi-person-bounding-box me-2"></i> প্রোফাইল আপডেট
                                    </button>
                                    <button 
                                        type="button"
                                        onClick={() => setActiveTab('password')} 
                                        className={`nav-link text-start border-0 py-3 px-4 rounded-4 transition-all ${activeTab === 'password' ? 'active bg-primary text-white shadow' : 'text-dark bg-light-subtle hover-bg-light border'}`}
                                        style={{ fontWeight: '600' }}
                                    >
                                        <i className="bi bi-key-fill me-2"></i> পাসওয়ার্ড পরিবর্তন
                                    </button>
                                    <hr className="my-2 border-light-subtle" />
                                    <button 
                                        type="button"
                                        onClick={handleLogout} 
                                        className="nav-link text-start border py-3 px-4 rounded-4 text-danger bg-danger-subtle hover-bg-danger-light"
                                        style={{ fontWeight: '600' }}
                                    >
                                        <i className="bi bi-power me-2"></i> লগআউট
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>

                    {/* Main Content Area */}
                    <div className="col-lg-9">
                        {/* Tab Content: Overview */}
                        {activeTab === 'overview' && (
                            <div className="animate-fade-in">
                                <div className="row g-4 mb-4">
                                    <div className="col-md-6 col-xl-4">
                                        <div className="card border-0 shadow-sm p-4 text-center" style={{ borderRadius: '20px', background: 'linear-gradient(135deg, #6a11cb 0%, #2575fc 100%)', color: '#fff' }}>
                                            <div className="display-5 fw-bold mb-1">{stats.order_count}</div>
                                            <div className="small opacity-75">মোট অর্ডার</div>
                                        </div>
                                    </div>
                                    <div className="col-md-6 col-xl-4">
                                        <div className="card border-0 shadow-sm p-4 text-center" style={{ borderRadius: '20px', background: 'linear-gradient(135deg, #ff9966 0%, #ff5e62 100%)', color: '#fff' }}>
                                            <div className="display-5 fw-bold mb-1">{stats.wishlist_count}</div>
                                            <div className="small opacity-75">উইশলিস্ট</div>
                                        </div>
                                    </div>
                                </div>

                                <div className="card border-0 shadow-sm" style={{ borderRadius: '20px' }}>
                                    <div className="card-header bg-transparent border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                                        <h5 className="fw-bold mb-0 text-dark">সাম্প্রতিক অর্ডারসমূহ</h5>
                                        <button onClick={() => setActiveTab('orders')} className="btn btn-sm btn-light rounded-pill px-3 fw-bold">সবগুলো দেখুন</button>
                                    </div>
                                    <div className="card-body p-4">
                                        {orders.length > 0 ? (
                                            <div className="table-responsive">
                                                <table className="table table-hover align-middle">
                                                    <thead className="table-light">
                                                        <tr className="small">
                                                            <th>ইনভয়েস</th>
                                                            <th>তারিখ</th>
                                                            <th>টোটাল</th>
                                                            <th>অবস্থা</th>
                                                            <th className="text-end">অ্যাকশন</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {orders.slice(0, 5).map(order => (
                                                            <tr key={order.id}>
                                                                <td className="fw-bold text-primary">#{order.invoice?.invoice_number}</td>
                                                                <td className="small">{new Date(order.created_at).toLocaleDateString()}</td>
                                                                <td className="fw-bold">৳{order.grand_total}</td>
                                                                <td>
                                                                    <span className={`badge rounded-pill bg-opacity-10 py-2 px-3 text-${order.status === 'completed' ? 'success bg-success' : order.status === 'cancelled' ? 'danger bg-danger' : 'warning bg-warning'}`}>
                                                                        {order.status}
                                                                    </span>
                                                                </td>
                                                                <td className="text-end">
                                                                    <button onClick={() => navigate(`/order-tracking`)} className="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">ট্র্যাক</button>
                                                                </td>
                                                            </tr>
                                                        ))}
                                                    </tbody>
                                                </table>
                                            </div>
                                        ) : (
                                            <div className="text-center py-5">
                                                <div className="display-4 mb-3">🛍️</div>
                                                <p className="text-muted mb-4">আপনার কোনো অর্ডার নেই।</p>
                                                <Link to="/products" className="btn btn-primary fw-bold px-4 rounded-pill">কেনাকাটা শুরু করুন</Link>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Tab Content: Orders History */}
                        {activeTab === 'orders' && (
                            <div className="animate-fade-in card border-0 shadow-sm" style={{ borderRadius: '20px' }}>
                                <div className="card-header bg-transparent border-0 p-4">
                                    <h5 className="fw-bold mb-0 text-dark">আমার সব অর্ডার</h5>
                                </div>
                                <div className="card-body p-4 pt-0">
                                    <div className="table-responsive">
                                        <table className="table table-hover align-middle">
                                            <thead className="table-light">
                                                <tr className="small">
                                                    <th>ইনভয়েস</th>
                                                    <th>তারিখ</th>
                                                    <th>পেমেন্ট</th>
                                                    <th>টোটাল</th>
                                                    <th>অবস্থা</th>
                                                    <th className="text-end">রিভিউ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {orders.map(order => (
                                                    <tr key={order.id}>
                                                        <td className="fw-bold text-primary">#{order.invoice?.invoice_number}</td>
                                                        <td className="small">{new Date(order.created_at).toLocaleDateString()}</td>
                                                        <td className="small text-muted">{order.payment_method}</td>
                                                        <td className="fw-bold">৳{order.grand_total}</td>
                                                        <td>
                                                            <span className={`badge rounded-pill bg-opacity-10 py-2 px-3 text-${order.status === 'completed' ? 'success bg-success' : order.status === 'cancelled' ? 'danger bg-danger' : 'warning bg-warning'}`}>
                                                                {order.status}
                                                            </span>
                                                        </td>
                                                        <td className="text-end">
                                                            {order.status === 'completed' && (
                                                                <button 
                                                                    onClick={() => openReviewModal(order.items?.[0])}
                                                                    className="btn btn-warning btn-sm fw-bold rounded-pill px-3"
                                                                >
                                                                    রিভিউ
                                                                </button>
                                                            )}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                    {orders.length === 0 && <div className="text-center py-5 text-muted">কোনো অর্ডার পাওয়া যায়নি।</div>}
                                </div>
                            </div>
                        )}

                        {/* Tab Content: Wishlist */}
                        {activeTab === 'wishlist' && (
                            <div className="animate-fade-in card border border-light-subtle shadow-sm" style={{ borderRadius: '20px' }}>
                                <div className="card-header bg-transparent border-0 p-4">
                                    <h5 className="fw-bold mb-0 text-dark">আমার পছন্দের প্রোডাক্টসমূহ</h5>
                                </div>
                                <div className="card-body p-4 pt-0">
                                    <div className="row g-3">
                                        {wishlist.map(item => (
                                            <div key={item.id} className="col-md-6 col-xl-4">
                                                <div className="card h-100 border border-light-subtle rounded-4 overflow-hidden shadow-hover transition-all">
                                                    <img src={item.thumbnail} className="card-img-top" style={{ height: '180px', objectFit: 'cover' }} alt={item.name} />
                                                    <div className="card-body p-3">
                                                        <h6 className="fw-bold text-truncate mb-1">{item.name}</h6>
                                                        <div className="text-primary fw-bold mb-3">৳{item.price}</div>
                                                        <div className="d-flex gap-2">
                                                            <button 
                                                                onClick={() => navigate(`/product/${item.id}?type=${item.product_type}`)}
                                                                className="btn btn-primary btn-sm w-100 rounded-pill fw-bold"
                                                            >
                                                                দেখুন
                                                            </button>
                                                            <button 
                                                                className="btn btn-outline-danger btn-sm rounded-circle"
                                                                title="মুছে ফেলুন"
                                                                onClick={() => toast.success("উইশলিস্ট থেকে সরানো হয়েছে (ডাইনামিক রিমুভ শীঘ্রই আসবে)")}
                                                            >
                                                                <i className="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                    {wishlist.length === 0 && (
                                        <div className="text-center py-5">
                                            <i className="bi bi-heart text-muted display-1 opacity-25"></i>
                                            <p className="mt-3 text-muted">আপনার উইশলিস্ট খালি।</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Tab Content: Profile Update */}
                        {activeTab === 'profile' && (
                            <div className="animate-fade-in card border border-light-subtle shadow-sm" style={{ borderRadius: '20px' }}>
                                <div className="card-header bg-transparent border-0 p-4">
                                    <h5 className="fw-bold mb-0 text-dark">প্রোফাইল আপডেট</h5>
                                </div>
                                <div className="card-body p-4 pt-0">
                                    <form onSubmit={handleUpdateProfile}>
                                        <div className="row g-4 mb-4">
                                            <div className="col-md-6">
                                                <label className="form-label small fw-bold text-muted mb-2">আপনার নাম</label>
                                                <input 
                                                    type="text" 
                                                    className="form-control form-control-lg border-secondary-subtle px-3" 
                                                    style={{ borderRadius: '12px', fontSize: '15px' }}
                                                    value={profileData.name} 
                                                    onChange={(e) => setProfileData({...profileData, name: e.target.value})}
                                                    required
                                                />
                                            </div>
                                            <div className="col-md-6">
                                                <label className="form-label small fw-bold text-muted mb-2">ইমেইল ঠিকানা</label>
                                                <input 
                                                    type="email" 
                                                    className="form-control form-control-lg border-secondary-subtle px-3" 
                                                    style={{ borderRadius: '12px', fontSize: '15px' }}
                                                    value={profileData.email} 
                                                    onChange={(e) => setProfileData({...profileData, email: e.target.value})}
                                                    required
                                                />
                                            </div>
                                            <div className="col-12">
                                                <label className="form-label small fw-bold text-muted mb-2">ফোন নাম্বার</label>
                                                <input 
                                                    type="text" 
                                                    className="form-control form-control-lg border-secondary-subtle px-3" 
                                                    style={{ borderRadius: '12px', fontSize: '15px' }}
                                                    value={profileData.phone} 
                                                    onChange={(e) => setProfileData({...profileData, phone: e.target.value})}
                                                />
                                            </div>
                                        </div>
                                        <button type="submit" className="btn btn-primary btn-lg px-5 fw-bold rounded-pill shadow-sm" disabled={updatingProfile}>
                                            {updatingProfile ? 'আপডেট হচ্ছে...' : 'সেভ করুন'}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        )}

                        {/* Tab Content: Password Change */}
                        {activeTab === 'password' && (
                            <div className="animate-fade-in card border border-light-subtle shadow-sm" style={{ borderRadius: '20px' }}>
                                <div className="card-header bg-transparent border-0 p-4">
                                    <h5 className="fw-bold mb-0 text-dark">পাসওয়ার্ড পরিবর্তন</h5>
                                </div>
                                <div className="card-body p-4 pt-0">
                                    <form onSubmit={handleUpdatePassword}>
                                        <div className="mb-4">
                                            <label className="form-label small fw-bold text-muted mb-2">বর্তমান পাসওয়ার্ড</label>
                                            <input 
                                                type="password" 
                                                className="form-control form-control-lg border-secondary-subtle px-3" 
                                                style={{ borderRadius: '12px', fontSize: '15px' }}
                                                value={passwordData.current_password} 
                                                onChange={(e) => setPasswordData({...passwordData, current_password: e.target.value})}
                                                required
                                            />
                                        </div>
                                        <div className="mb-4">
                                            <label className="form-label small fw-bold text-muted mb-2">নতুন পাসওয়ার্ড</label>
                                            <input 
                                                type="password" 
                                                className="form-control form-control-lg border-secondary-subtle px-3" 
                                                style={{ borderRadius: '12px', fontSize: '15px' }}
                                                value={passwordData.password} 
                                                onChange={(e) => setPasswordData({...passwordData, password: e.target.value})}
                                                required
                                            />
                                        </div>
                                        <div className="mb-5">
                                            <label className="form-label small fw-bold text-muted mb-2">পাসওয়ার্ড নিশ্চিত করুন</label>
                                            <input 
                                                type="password" 
                                                className="form-control form-control-lg border-secondary-subtle px-3" 
                                                style={{ borderRadius: '12px', fontSize: '15px' }}
                                                value={passwordData.password_confirmation} 
                                                onChange={(e) => setPasswordData({...passwordData, password_confirmation: e.target.value})}
                                                required
                                            />
                                        </div>
                                        <button type="submit" className="btn btn-primary btn-lg px-5 fw-bold rounded-pill shadow-sm" disabled={updatingPassword}>
                                            {updatingPassword ? 'পরিবর্তন হচ্ছে...' : 'আপডেট করুন'}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Review Modal */}
            {showReviewModal && (
                <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.7)', backdropFilter: 'blur(5px)' }}>
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content border-0 overflow-hidden" style={{ borderRadius: '25px' }}>
                            <div className="modal-header border-0 bg-light p-4">
                                <h5 className="fw-bold mb-0">আপনার মতামত দিন</h5>
                                <button type="button" className="btn-close" onClick={() => setShowReviewModal(false)}></button>
                            </div>
                            <div className="modal-body p-4">
                                <div className="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-4">
                                    <img src={reviewProduct?.thumbnail} style={{ width: '60px', height: '60px', objectFit: 'cover', borderRadius: '12px' }} alt="" />
                                    <div className="overflow-hidden">
                                        <h6 className="fw-bold mb-0 text-truncate">{reviewProduct?.name}</h6>
                                        <div className="small text-muted">রেটিং দিন</div>
                                    </div>
                                </div>
                                
                                <div className="text-center mb-4">
                                    <div className="d-flex justify-content-center gap-2 h2">
                                        {[1, 2, 3, 4, 5].map(star => (
                                            <span 
                                                key={star} 
                                                onClick={() => setReviewRating(star)}
                                                style={{ cursor: 'pointer', color: star <= reviewRating ? '#ffc107' : '#e0e0e0', transition: 'all 0.2s' }}
                                                className="star-hover"
                                            >
                                                ★
                                            </span>
                                        ))}
                                    </div>
                                    <div className="small fw-bold text-warning mt-1">
                                        {reviewRating === 5 ? 'চমৎকার!' : reviewRating === 4 ? 'খুব ভালো' : reviewRating === 3 ? 'মোটামুটি' : reviewRating === 2 ? 'খারাপ' : 'খুবই খারাপ'}
                                    </div>
                                </div>

                                <div className="mb-4">
                                    <textarea 
                                        className="form-control border-light bg-light" 
                                        rows="4" 
                                        placeholder="আপনার অভিজ্ঞতা বিস্তারিত লিখুন..."
                                        style={{ borderRadius: '15px' }}
                                        value={reviewComment}
                                        onChange={(e) => setReviewComment(e.target.value)}
                                    ></textarea>
                                </div>

                                <button 
                                    className="btn btn-primary w-100 fw-bold py-3 rounded-pill" 
                                    onClick={handleSubmitReview}
                                    disabled={submittingReview}
                                >
                                    {submittingReview ? 'জমা হচ্ছে...' : 'রিভিউ জমা দিন'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <style>{`
                .hover-bg-light:hover { background-color: #f8f9fa !important; }
                .hover-bg-danger-light:hover { background-color: #fff5f5 !important; }
                .animate-fade-in { animation: fadeIn 0.4s ease-out; }
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .star-hover:hover { transform: scale(1.2); }
                .dashboard-nav .nav-link { transition: all 0.2s; font-weight: 600; }
                @media (max-width: 991px) {
                    .dashboard-nav { flex-direction: row !important; overflow-x: auto; flex-wrap: nowrap; padding-bottom: 10px; }
                    .dashboard-nav .nav-link { white-space: nowrap; padding: 10px 20px !important; margin-right: 10px; font-size: 13px; }
                }
            `}</style>
        </MasterLayout>
    );
};

export default UserDashboard;
