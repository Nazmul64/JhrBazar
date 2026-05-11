import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import axios from 'axios';
import { toast } from 'react-hot-toast';

const UserDashboard = () => {
    const navigate = useNavigate();
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const token = localStorage.getItem('auth_token');
        if (!token) {
            navigate('/customer/login');
            return;
        }

        const fetchUser = async () => {
            try {
                const res = await axios.get('/api/user', {
                    headers: { Authorization: `Bearer ${token}` }
                });
                setUser(res.data);
            } catch (err) {
                localStorage.removeItem('auth_token');
                navigate('/customer/login');
            } finally {
                setLoading(false);
            }
        };

        fetchUser();
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

    if (loading) return <div className="text-center py-5">লোড হচ্ছে...</div>;

    return (
        <MasterLayout>
            <div className="container py-5">
                <div className="row">
                    <div className="col-lg-4">
                        <div className="card border-0 shadow-sm mb-4" style={{ borderRadius: '15px' }}>
                            <div className="card-body text-center p-4">
                                <div
                                    className="rounded-circle bg-primary mx-auto mb-3 d-flex align-items-center justify-content-center text-white fw-bold"
                                    style={{ width: '80px', height: '80px', fontSize: '30px' }}
                                >
                                    {user?.name?.charAt(0).toUpperCase()}
                                </div>
                                <h5 className="fw-bold mb-1">{user?.name}</h5>
                                <p className="text-muted small mb-3">{user?.email}</p>
                                <button onClick={handleLogout} className="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold">লগআউট করুন</button>
                            </div>
                        </div>

                        <div className="list-group shadow-sm border-0 mb-4" style={{ borderRadius: '15px', overflow: 'hidden' }}>
                            <a href="#" className="list-group-item list-group-item-action active border-0 p-3 fw-bold">ড্যাশবোর্ড</a>
                            <a href="#" className="list-group-item list-group-item-action border-0 p-3">আমার অর্ডার</a>
                            <a href="#" className="list-group-item list-group-item-action border-0 p-3">প্রোফাইল আপডেট</a>
                            <a href="#" className="list-group-item list-group-item-action border-0 p-3 text-danger" onClick={handleLogout}>লগআউট</a>
                        </div>
                    </div>

                    <div className="col-lg-8">
                        <div className="row g-4">
                            <div className="col-md-6">
                                <div className="card border-0 shadow-sm p-4 text-white" style={{ borderRadius: '20px', backgroundColor: '#57b500' }}>
                                    <h6 className="opacity-75 mb-1">মোট অর্ডার</h6>
                                    <h2 className="fw-bold mb-0">০</h2>
                                </div>
                            </div>
                            <div className="col-md-6">
                                <div className="card border-0 shadow-sm p-4 text-white" style={{ borderRadius: '20px', backgroundColor: '#ff4d4d' }}>
                                    <h6 className="opacity-75 mb-1">উইশলিস্ট</h6>
                                    <h2 className="fw-bold mb-0">০</h2>
                                </div>
                            </div>
                        </div>

                        <div className="card border-0 shadow-sm mt-4" style={{ borderRadius: '20px' }}>
                            <div className="card-body p-4">
                                <h5 className="fw-bold mb-4">সাম্প্রতিক অর্ডারসমূহ</h5>
                                <div className="text-center py-5">
                                    <div className="mb-3" style={{ fontSize: '40px' }}>📦</div>
                                    <p className="text-muted">আপনার কোনো অর্ডার নেই।</p>
                                    <button onClick={() => navigate('/products')} className="btn btn-primary fw-bold px-4">এখনই কেনাকাটা করুন</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MasterLayout>
    );
};

export default UserDashboard;
