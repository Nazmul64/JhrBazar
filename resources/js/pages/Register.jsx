import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import axios from 'axios';
import { toast } from 'react-hot-toast';

const Register = () => {
    const navigate = useNavigate();
    const mainColor = '#57b500';
    const [showPassword, setShowPassword] = useState(false);
    const [loading, setLoading] = useState(false);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: ''
    });

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!formData.name || !formData.email || !formData.password) {
            toast.error("অনুগ্রহ করে সব তথ্য দিন");
            return;
        }

        setLoading(true);
        try {
            const res = await axios.post('/api/register', formData);
            if (res.data.success) {
                localStorage.setItem('auth_token', res.data.access_token);
                localStorage.setItem('user', JSON.stringify(res.data.user));
                toast.success("নিবন্ধন সফল হয়েছে!");
                navigate('/customer/dashboard');
            }
        } catch (err) {
            toast.error(err.response?.data?.message || "নিবন্ধন ব্যর্থ হয়েছে। আবার চেষ্টা করুন।");
        } finally {
            setLoading(false);
        }
    };

    return (
        <MasterLayout>
            <div className="container py-5 my-md-4">
                <div className="row justify-content-center">
                    <div className="col-12 col-md-8 col-lg-5">
                        <div className="card border-0 shadow-lg" style={{ borderRadius: '15px', overflow: 'hidden' }}>
                            <div className="card-body p-4 p-md-5">
                                <div className="text-center mb-4">
                                    <h3 className="fw-bold" style={{ color: '#333' }}>অ্যাকাউন্ট তৈরি করুন</h3>
                                    <p className="text-muted small">সেরা অফার পেতে আমাদের সাথে যুক্ত হন!</p>
                                </div>

                                <form onSubmit={handleSubmit}>
                                    <div className="mb-3">
                                        <label className="form-label small fw-bold text-muted">পূর্ণ নাম</label>
                                        <input
                                            type="text"
                                            name="name"
                                            value={formData.name}
                                            onChange={handleChange}
                                            className="form-control shadow-none"
                                            placeholder="আপনার পূর্ণ নাম লিখুন"
                                            style={{ padding: '12px 15px', borderRadius: '8px', border: '1px solid #e0e0e0' }}
                                        />
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label small fw-bold text-muted">ইমেইল অথবা ফোন নম্বর</label>
                                        <input
                                            type="text"
                                            name="email"
                                            value={formData.email}
                                            onChange={handleChange}
                                            className="form-control shadow-none"
                                            placeholder="আপনার ইমেইল বা ফোন নম্বর"
                                            style={{ padding: '12px 15px', borderRadius: '8px', border: '1px solid #e0e0e0' }}
                                        />
                                    </div>

                                    <div className="mb-4 position-relative">
                                        <label className="form-label small fw-bold text-muted">পাসওয়ার্ড</label>
                                        <input
                                            type={showPassword ? "text" : "password"}
                                            name="password"
                                            value={formData.password}
                                            onChange={handleChange}
                                            className="form-control shadow-none"
                                            placeholder="একটি শক্তিশালী পাসওয়ার্ড দিন"
                                            style={{ padding: '12px 15px', borderRadius: '8px', border: '1px solid #e0e0e0' }}
                                        />
                                        <span
                                            onClick={() => setShowPassword(!showPassword)}
                                            style={{ position: 'absolute', right: '15px', top: '38px', cursor: 'pointer', color: '#888' }}
                                        >
                                            {showPassword ? '👁️' : '👁️‍🗨️'}
                                        </span>
                                    </div>

                                    <div className="form-check mb-4">
                                        <input className="form-check-input" type="checkbox" id="terms" style={{ accentColor: mainColor }} required />
                                        <label className="form-check-label small text-muted" htmlFor="terms">
                                            আমি <Link to="/terms" style={{ color: mainColor }} className="text-decoration-none">শর্তাবলী</Link> এবং <Link to="/privacy-policy" style={{ color: mainColor }} className="text-decoration-none">প্রাইভেসি পলিসি</Link> এর সাথে একমত।
                                        </label>
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={loading}
                                        className="btn w-100 fw-bold text-white mb-4"
                                        style={{ backgroundColor: mainColor, padding: '12px', borderRadius: '8px', fontSize: '15px', transition: 'all 0.3s' }}
                                    >
                                        {loading ? 'প্রসেস হচ্ছে...' : 'অ্যাকাউন্ট তৈরি করুন'}
                                    </button>

                                    <div className="text-center">
                                        <span className="text-muted small">আগে থেকেই অ্যাকাউন্ট আছে? </span>
                                        <Link to="/customer/login" className="fw-bold text-decoration-none" style={{ color: mainColor }}>এখানে লগইন করুন</Link>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MasterLayout>
    );
};

export default Register;
