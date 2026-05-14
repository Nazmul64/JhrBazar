import React, { useState, useEffect } from 'react';
import axios from 'axios';
import MasterLayout from '../layouts/MasterLayout';
import SEO from '../components/SEO';

const About = () => {
    const [about, setAbout] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        axios.get('/api/about-company')
            .then(res => {
                if (res.data.success) {
                    setAbout(res.data.data);
                }
                setLoading(false);
            })
            .catch(err => {
                console.error("Error fetching about info", err);
                setLoading(false);
            });
    }, []);

    if (loading) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center">
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">Loading...</span>
                    </div>
                </div>
            </MasterLayout>
        );
    }

    if (!about) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center">
                    <h2 className="text-muted">About Information Not Found</h2>
                </div>
            </MasterLayout>
        );
    }

    return (
        <MasterLayout>
            <SEO
                title={about.meta_title || about.title}
                description={about.meta_description}
                keywords={about.meta_keywords}
            />
            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-lg-10">
                        <div className="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div className="card-body p-4 p-md-5">
                                <div className="row align-items-center mb-5">
                                    <div className="col-md-7">
                                        <h1 className="fw-bold mb-3" style={{ color: '#2c3e50', fontSize: '2.5rem' }}>
                                            {about.title}
                                        </h1>
                                        <div className="mb-4" style={{ height: '4px', width: '60px', backgroundColor: '#ff4d4d', borderRadius: '2px' }}></div>
                                        <p className="text-muted lead">JHR Bazar — আপনার বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম</p>
                                    </div>
                                    {about.image && (
                                        <div className="col-md-5">
                                            <img src={about.image} alt={about.title} className="img-fluid rounded-4 shadow-sm" />
                                        </div>
                                    )}
                                </div>

                                <div
                                    className="about-content"
                                    style={{ lineHeight: '1.9', color: '#444', fontSize: '1.1rem' }}
                                    dangerouslySetInnerHTML={{ __html: about.content }}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .about-content h2, .about-content h3, .about-content h4, .about-content h5 { color: #333; margin-top: 30px; margin-bottom: 15px; font-weight: 700; }
                .about-content p { margin-bottom: 20px; }
                .about-content ul, .about-content ol { margin-bottom: 20px; padding-left: 20px; }
                .about-content li { margin-bottom: 10px; }
            `}</style>
        </MasterLayout>
    );
};

export default About;
