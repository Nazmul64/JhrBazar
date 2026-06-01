import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import axios from 'axios';
import { toast } from 'react-hot-toast';

const Login = () => {
    const navigate = useNavigate();
    const mainColor = '#57b500';
    const [showPassword, setShowPassword] = useState(false);
    const [loading, setLoading] = useState(false);
    const [formData, setFormData] = useState({
        email: '',
        password: ''
    });

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!formData.email || !formData.password) {
            toast.error("Please enter all fields");
            return;
        }

        setLoading(true);
        try {
            const res = await axios.post('/api/login', formData);
            if (res.data.success) {
                localStorage.setItem('auth_token', res.data.access_token);
                localStorage.setItem('user', JSON.stringify(res.data.user));
                toast.success("Login successful!");
                navigate('/customer/dashboard');
            }
        } catch (err) {
            toast.error(err.response?.data?.message || "Login failed. Please check your credentials.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <MasterLayout>
            <div className="container py-5 my-md-5">
                <div className="row justify-content-center">
                    <div className="col-12 col-md-8 col-lg-5">
                        <div className="card border-0 shadow-lg" style={{ borderRadius: '15px', overflow: 'hidden' }}>
                            <div className="card-body p-4 p-md-5">
                                <div className="text-center mb-4">
                                    <h3 className="fw-bold" style={{ color: '#333' }}>Welcome!</h3>
                                    <p className="text-muted small">Please login to your account.</p>
                                </div>

                                <form onSubmit={handleSubmit}>
                                    <div className="mb-3">
                                        <label className="form-label small fw-bold text-muted">Email or Phone Number</label>
                                        <input
                                            type="text"
                                            name="email"
                                            value={formData.email}
                                            onChange={handleChange}
                                            className="form-control shadow-none"
                                            placeholder="Enter your email or phone number"
                                            style={{ padding: '12px 15px', borderRadius: '8px', border: '1px solid #e0e0e0' }}
                                        />
                                    </div>

                                    <div className="mb-4 position-relative">
                                        <label className="form-label small fw-bold text-muted">Password</label>
                                        <input
                                            type={showPassword ? "text" : "password"}
                                            name="password"
                                            value={formData.password}
                                            onChange={handleChange}
                                            className="form-control shadow-none"
                                            placeholder="Enter your password"
                                            style={{ padding: '12px 15px', borderRadius: '8px', border: '1px solid #e0e0e0' }}
                                        />
                                        <span
                                            onClick={() => setShowPassword(!showPassword)}
                                            style={{ position: 'absolute', right: '15px', top: '38px', cursor: 'pointer', color: '#888' }}
                                        >
                                            {showPassword ? '👁️' : '👁️‍🗨️'}
                                        </span>
                                    </div>

                                    <div className="d-flex justify-content-between align-items-center mb-4">
                                        <div className="form-check">
                                            <input className="form-check-input" type="checkbox" id="rememberMe" style={{ accentColor: mainColor }} />
                                            <label className="form-check-label small text-muted" htmlFor="rememberMe">
                                                Remember me
                                            </label>
                                        </div>
                                        <a href="/password/reset" className="small fw-bold text-decoration-none" style={{ color: mainColor }}>Forgot Password?</a>
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={loading}
                                        className="btn w-100 fw-bold text-white mb-4"
                                        style={{ backgroundColor: mainColor, padding: '12px', borderRadius: '8px', fontSize: '15px', transition: 'all 0.3s' }}
                                    >
                                        {loading ? 'Processing...' : 'Login'}
                                    </button>

                                    <div className="text-center">
                                        <span className="text-muted small">Don't have an account? </span>
                                        <Link to="/customer/register" className="fw-bold text-decoration-none" style={{ color: mainColor }}>Create a new account</Link>
                                    </div>
                                </form>

                                {/* Social Login */}
                                <div className="mt-4 pt-4 border-top text-center">
                                    <p className="text-muted small mb-3">Or login with</p>
                                    <div className="d-flex justify-content-center gap-3">
                                        <button className="btn btn-outline-light text-dark shadow-sm d-flex align-items-center justify-content-center" style={{ width: '40px', height: '40px', borderRadius: '50%' }}>
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google" style={{ width: '18px' }} />
                                        </button>
                                        <button className="btn btn-outline-light text-dark shadow-sm d-flex align-items-center justify-content-center" style={{ width: '40px', height: '40px', borderRadius: '50%' }}>
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg" alt="Facebook" style={{ width: '18px' }} />
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MasterLayout>
    );
};
export default Login;
