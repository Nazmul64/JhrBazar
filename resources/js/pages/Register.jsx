import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import axios from 'axios';
import { toast } from 'react-hot-toast';

const countriesList = [
    { name: 'Bangladesh (বাংলাদেশ)', code: 'BD', dialCode: '+880', flag: '🇧🇩' },
    { name: 'Saudi Arabia (সৌদি আরব)', code: 'SA', dialCode: '+966', flag: '🇸🇦' },
    { name: 'UAE (সংযুক্ত আরব আমিরাত)', code: 'AE', dialCode: '+971', flag: '🇦🇪' },
    { name: 'Qatar (কাতার)', code: 'QA', dialCode: '+974', flag: '🇶🇦' },
    { name: 'Oman (オমান)', code: 'OM', dialCode: '+968', flag: '🇴🇲' },
    { name: 'Kuwait (কুয়েত)', code: 'KW', dialCode: '+965', flag: '🇰🇼' },
    { name: 'Bahrain (বাহরাইন)', code: 'BH', dialCode: '+973', flag: '🇧🇭' },
    { name: 'Malaysia (মালয়েশিয়া)', code: 'MY', dialCode: '+60', flag: '🇲🇾' },
    { name: 'Singapore (সিঙ্গাপুর)', code: 'SG', dialCode: '+65', flag: '🇸🇬' },
    { name: 'United States (যুক্তরাষ্ট্র)', code: 'US', dialCode: '+1', flag: '🇺🇸' },
    { name: 'United Kingdom (যুক্তরাজ্য)', code: 'GB', dialCode: '+44', flag: '🇬🇧' },
    { name: 'Canada (কানাডা)', code: 'CA', dialCode: '+1', flag: '🇨🇦' },
    { name: 'Australia (অস্ট্রেলিয়া)', code: 'AU', dialCode: '+61', flag: '🇦🇺' },
    { name: 'Italy (ইতালি)', code: 'IT', dialCode: '+39', flag: '🇮🇹' },
    { name: 'India (ভারত)', code: 'IN', dialCode: '+91', flag: '🇮🇳' },
    { name: 'Pakistan (পাকিস্তান)', code: 'PK', dialCode: '+92', flag: '🇵🇰' },
    { name: 'Nepal (নেপাল)', code: 'NP', dialCode: '+977', flag: '🇳🇵' },
    { name: 'South Africa (দক্ষিণ আফ্রিকা)', code: 'ZA', dialCode: '+27', flag: '🇿🇦' },
    { name: 'Germany (জার্মানি)', code: 'DE', dialCode: '+49', flag: '🇩🇪' },
    { name: 'France (ফ্রান্স)', code: 'FR', dialCode: '+33', flag: '🇫🇷' },
    { name: 'Japan (জাপান)', code: 'JP', dialCode: '+81', flag: '🇯🇵' },
    { name: 'China (চীন)', code: 'CN', dialCode: '+86', flag: '🇨🇳' },
    { name: 'South Korea (দক্ষিণ কোরিয়া)', code: 'KR', dialCode: '+82', flag: '🇰🇷' },
    { name: 'Maldives (মালদ্বীপ)', code: 'MV', dialCode: '+960', flag: '🇲🇻' },
    { name: 'Spain (স্পেন)', code: 'ES', dialCode: '+34', flag: '🇪🇸' },
    { name: 'Brazil (ব্রাজিল)', code: 'BR', dialCode: '+55', flag: '🇧🇷' },
    { name: 'Turkey (তুরস্ক)', code: 'TR', dialCode: '+90', flag: '🇹🇷' },
    { name: 'Switzerland (সুইজারল্যান্ড)', code: 'CH', dialCode: '+41', flag: '🇨🇭' },
    { name: 'Sweden (সুইডেন)', code: 'SE', dialCode: '+46', flag: '🇸🇪' }
];

const Register = () => {
    const navigate = useNavigate();
    const mainColor = '#57b500';
    const [showPassword, setShowPassword] = useState(false);
    const [loading, setLoading] = useState(false);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        password: '',
        password_confirmation: ''
    });

    const [showCountryDropdown, setShowCountryDropdown] = useState(false);
    const [selectedCountry, setSelectedCountry] = useState(countriesList[0]);
    const [countrySearch, setCountrySearch] = useState('');

    useEffect(() => {
        const handleOutsideClick = (e) => {
            if (!e.target.closest('.country-dropdown-container')) {
                setShowCountryDropdown(false);
            }
        };
        document.addEventListener('click', handleOutsideClick);
        return () => document.removeEventListener('click', handleOutsideClick);
    }, []);

    const filteredCountries = countriesList.filter(c => 
        c.name.toLowerCase().includes(countrySearch.toLowerCase()) || 
        c.dialCode.includes(countrySearch)
    );

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!formData.name || !formData.email || !formData.phone || !formData.password || !formData.password_confirmation) {
            toast.error("অনুগ্রহ করে সব তথ্য দিন");
            return;
        }

        if (formData.password !== formData.password_confirmation) {
            toast.error("পাসওয়ার্ড দুটি মেলেনি");
            return;
        }

        setLoading(true);
        try {
            // Strip leading zero from phone number if present, and prepend selected country dialCode
            const cleanPhoneNum = formData.phone.trim().replace(/^[0]/, '');
            const fullPhone = `${selectedCountry.dialCode}${cleanPhoneNum}`;

            const payload = {
                ...formData,
                phone: fullPhone
            };

            const res = await axios.post('/api/register', payload);
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
                                            required
                                            value={formData.name}
                                            onChange={handleChange}
                                            className="form-control shadow-none"
                                            placeholder="আপনার পূর্ণ নাম লিখুন"
                                            style={{ padding: '12px 15px', borderRadius: '8px', border: '1px solid #e0e0e0' }}
                                        />
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label small fw-bold text-muted">ইমেইল অ্যাড্রেস</label>
                                        <input
                                            type="email"
                                            name="email"
                                            required
                                            value={formData.email}
                                            onChange={handleChange}
                                            className="form-control shadow-none"
                                            placeholder="আপনার ইমেইল অ্যাড্রেস লিখুন"
                                            style={{ padding: '12px 15px', borderRadius: '8px', border: '1px solid #e0e0e0' }}
                                        />
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label small fw-bold text-muted">ফোন নম্বর</label>
                                        <div className="d-flex align-items-center position-relative country-dropdown-container" style={{ border: '1px solid #e0e0e0', borderRadius: '8px', overflow: 'visible', padding: '2px', backgroundColor: '#fff' }}>
                                            {/* Country Code Selection Dropdown Button */}
                                            <div 
                                                className="d-flex align-items-center px-3 justify-content-center cursor-pointer"
                                                onClick={() => setShowCountryDropdown(!showCountryDropdown)}
                                                style={{ borderRight: '1px solid #e0e0e0', height: '38px', userSelect: 'none' }}
                                            >
                                                <span style={{ fontSize: '18px', marginRight: '5px' }}>{selectedCountry.flag}</span>
                                                <span className="fw-semibold small" style={{ color: '#333' }}>{selectedCountry.dialCode}</span>
                                                <span style={{ fontSize: '10px', marginLeft: '5px', color: '#888' }}>▼</span>
                                            </div>

                                            {/* Phone Input Box */}
                                            <input
                                                type="text"
                                                name="phone"
                                                required
                                                value={formData.phone}
                                                onChange={handleChange}
                                                className="form-control border-0 shadow-none flex-grow-1"
                                                placeholder="1XXX XXXXXX"
                                                style={{ padding: '10px 15px', borderRadius: '0 8px 8px 0', fontSize: '15px' }}
                                            />

                                            {/* Country Codes Dropdown Menu List */}
                                            {showCountryDropdown && (
                                                <div 
                                                    className="position-absolute shadow-lg bg-white" 
                                                    style={{ 
                                                        top: '46px', 
                                                        left: '0', 
                                                        zIndex: 1000, 
                                                        width: '280px', 
                                                        maxHeight: '200px', 
                                                        overflowY: 'auto', 
                                                        borderRadius: '8px', 
                                                        border: '1px solid #e0e0e0',
                                                        padding: '5px 0'
                                                    }}
                                                >
                                                    {/* Country Search Bar */}
                                                    <div className="px-2 pb-2 pt-1" style={{ borderBottom: '1px solid #f0f0f0' }}>
                                                        <input 
                                                            type="text" 
                                                            placeholder="দেশ সার্চ করুন..." 
                                                            value={countrySearch}
                                                            onChange={(e) => setCountrySearch(e.target.value)}
                                                            className="form-control form-control-sm shadow-none"
                                                            style={{ fontSize: '12px' }}
                                                            onClick={(e) => e.stopPropagation()}
                                                        />
                                                    </div>
                                                    
                                                    {/* Country List Rows */}
                                                    {filteredCountries.map((c) => (
                                                        <div 
                                                            key={c.code}
                                                            className="d-flex align-items-center justify-content-between px-3 py-2 cursor-pointer"
                                                            onClick={() => {
                                                                setSelectedCountry(c);
                                                                setShowCountryDropdown(false);
                                                                setCountrySearch('');
                                                            }}
                                                            style={{ transition: 'background 0.2s', fontSize: '13px', color: '#333' }}
                                                            onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#f5f5f5'}
                                                            onMouseLeave={(e) => e.currentTarget.style.backgroundColor = 'transparent'}
                                                        >
                                                            <div className="d-flex align-items-center">
                                                                <span style={{ fontSize: '18px', marginRight: '8px' }}>{c.flag}</span>
                                                                <span className="fw-medium">{c.name}</span>
                                                            </div>
                                                            <span className="text-muted small fw-semibold">{c.dialCode}</span>
                                                        </div>
                                                    ))}
                                                    {filteredCountries.length === 0 && (
                                                        <div className="text-center text-muted py-2 small">কোনো দেশ পাওয়া যায়নি</div>
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    <div className="mb-3 position-relative">
                                        <label className="form-label small fw-bold text-muted">পাসওয়ার্ড</label>
                                        <input
                                            type={showPassword ? "text" : "password"}
                                            name="password"
                                            required
                                            value={formData.password}
                                            onChange={handleChange}
                                            className="form-control shadow-none"
                                            placeholder="একটি শক্তিশালী পাসওয়ার্ড দিন (কমপক্ষে ৮ অক্ষর)"
                                            style={{ padding: '12px 15px', borderRadius: '8px', border: '1px solid #e0e0e0' }}
                                        />
                                        <span
                                            onClick={() => setShowPassword(!showPassword)}
                                            style={{ position: 'absolute', right: '15px', top: '38px', cursor: 'pointer', color: '#888' }}
                                        >
                                            {showPassword ? '👁️' : '👁️‍🗨️'}
                                        </span>
                                    </div>

                                    <div className="mb-4 position-relative">
                                        <label className="form-label small fw-bold text-muted">পাসওয়ার্ড নিশ্চিত করুন</label>
                                        <input
                                            type={showPassword ? "text" : "password"}
                                            name="password_confirmation"
                                            required
                                            value={formData.password_confirmation}
                                            onChange={handleChange}
                                            className="form-control shadow-none"
                                            placeholder="পাসওয়ার্ডটি আবার লিখুন"
                                            style={{ padding: '12px 15px', borderRadius: '8px', border: '1px solid #e0e0e0' }}
                                        />
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
