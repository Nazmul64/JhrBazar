import React, { useState, useEffect } from 'react';
import axios from 'axios';
import MasterLayout from '../layouts/MasterLayout';
import SEO from '../components/SEO';

const PrivacyPolicy = () => {
    const [policy, setPolicy] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        axios.get('/api/privacy-policy')
            .then(res => {
                if (res.data.success) {
                    setPolicy(res.data.data);
                }
                setLoading(false);
            })
            .catch(err => {
                console.error("Error fetching privacy policy", err);
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

    if (!policy) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center">
                    <h2 className="text-muted">Privacy Policy Not Found</h2>
                </div>
            </MasterLayout>
        );
    }

    return (
        <MasterLayout>
            <SEO 
                title={policy.meta_title || policy.title}
                description={policy.meta_description}
                keywords={policy.meta_keywords}
            />
            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-lg-10">
                        <div className="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div className="card-body p-4 p-md-5">
                                <h1 className="fw-bold mb-3" style={{ color: '#2c3e50', fontSize: '2.5rem' }}>
                                    {policy.title}
                                </h1>
                                <div className="mb-4" style={{ height: '4px', width: '60px', backgroundColor: '#ff4d4d', borderRadius: '2px' }}></div>
                                
                                <div 
                                    className="policy-content"
                                    style={{ lineHeight: '1.9', color: '#444', fontSize: '1.1rem' }}
                                    dangerouslySetInnerHTML={{ __html: policy.content }}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .policy-content h2, .policy-content h3, .policy-content h4, .policy-content h5 { color: #333; margin-top: 30px; margin-bottom: 15px; font-weight: 700; }
                .policy-content p { margin-bottom: 20px; }
                .policy-content ul, .policy-content ol { margin-bottom: 20px; padding-left: 20px; }
                .policy-content li { margin-bottom: 10px; }
            `}</style>
        </MasterLayout>
    );
};

export default PrivacyPolicy;
