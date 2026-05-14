import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import MasterLayout from '../layouts/MasterLayout';
import SEO from '../components/SEO';

const PageView = (props) => {
    const { slug: urlSlug } = useParams();
    const slug = props.slug || urlSlug;
    const [page, setPage] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        axios.get(`/api/page/${slug}`)
            .then(res => {
                if (res.data.success) {
                    setPage(res.data.data);
                }
                setLoading(false);
            })
            .catch(err => {
                console.error("Error fetching page:", err);
                setLoading(false);
            });
    }, [slug]);

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

    if (!page) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center">
                    <h2 className="text-muted">Page Not Found</h2>
                </div>
            </MasterLayout>
        );
    }

    return (
        <MasterLayout>
            <SEO 
                title={page.meta_title || page.name}
                description={page.meta_description}
                keywords={page.meta_keywords}
            />
            
            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-lg-10 col-md-12">
                        <div className="card border-0 shadow-sm rounded-4" style={{ overflow: 'hidden' }}>
                            <div className="card-body p-4 p-md-5">
                                <h1 className="fw-bold mb-3" style={{ color: '#2c3e50', fontSize: '2.5rem' }}>
                                    {page.name}
                                </h1>
                                <div className="mb-4" style={{ height: '4px', width: '60px', backgroundColor: '#ff4d4d', borderRadius: '2px' }}></div>

                                <div
                                    className="page-content"
                                    style={{ lineHeight: '1.8', color: '#444', fontSize: '1.1rem' }}
                                    dangerouslySetInnerHTML={{ __html: page.description }}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .page-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 15px 0; }
                .page-content h2, .page-content h3, .page-content h4 { color: #333; margin-top: 30px; margin-bottom: 15px; font-weight: 700; }
                .page-content p { margin-bottom: 20px; }
                .page-content ul, .page-content ol { margin-bottom: 20px; padding-left: 20px; }
                .page-content li { margin-bottom: 10px; }
            `}</style>
        </MasterLayout>
    );
};

export default PageView;
