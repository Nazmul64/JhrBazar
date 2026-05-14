import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import axios from 'axios';
import MasterLayout from '../layouts/MasterLayout';
import SEO from '../components/SEO';

const BlogDetail = () => {
    const { slug } = useParams();
    const [blog, setBlog] = useState(null);
    const [loading, setLoading] = useState(true);
    const mainColor = '#ff4d4d';

    useEffect(() => {
        setLoading(true);
        axios.get(`/api/blog/${slug}`)
            .then(res => {
                if (res.data.success) {
                    setBlog(res.data.data);
                }
                setLoading(false);
            })
            .catch(err => {
                console.error("Error fetching blog:", err);
                setLoading(false);
            });
    }, [slug]);

    if (loading) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center">
                    <div className="spinner-border text-danger" role="status"></div>
                </div>
            </MasterLayout>
        );
    }

    if (!blog) {
        return (
            <MasterLayout>
                <div className="container py-5 text-center">
                    <h2 className="text-muted">Blog Post Not Found</h2>
                    <Link to="/blogs" className="btn mt-3 text-white" style={{ backgroundColor: mainColor }}>Back to Blogs</Link>
                </div>
            </MasterLayout>
        );
    }

    return (
        <MasterLayout>
            <SEO
                title={blog.meta_title || blog.title}
                description={blog.meta_description || blog.title}
                keywords={blog.meta_keywords}
                image={blog.thumbnail}
            />

            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-lg-9">
                        <div className="bg-white p-0 shadow-sm rounded-4 overflow-hidden">
                            {blog.thumbnail && (
                                <img
                                    src={blog.thumbnail}
                                    alt={blog.title}
                                    className="w-100"
                                    style={{ maxHeight: '500px', objectFit: 'cover' }}
                                />
                            )}

                            <div className="p-4 p-md-5">
                                <nav aria-label="breadcrumb">
                                    <ol className="breadcrumb mb-4">
                                        <li className="breadcrumb-item"><Link to="/blogs" className="text-decoration-none" style={{ color: mainColor }}>Blogs</Link></li>
                                        <li className="breadcrumb-item active">{blog.category?.name}</li>
                                    </ol>
                                </nav>

                                <h1 className="fw-bold mb-3" style={{ color: '#2c3e50', fontSize: '2.5rem', lineHeight: '1.2' }}>
                                    {blog.title}
                                </h1>

                                <div className="d-flex align-items-center gap-3 mb-4 text-muted small fw-bold">
                                    <span>By Admin</span>
                                    <span>•</span>
                                    <span>{new Date(blog.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' })}</span>
                                </div>

                                <div className="mb-5" style={{ height: '4px', width: '60px', backgroundColor: mainColor, borderRadius: '2px' }}></div>

                                <div
                                    className="blog-content"
                                    style={{ lineHeight: '1.9', color: '#444', fontSize: '1.15rem' }}
                                    dangerouslySetInnerHTML={{ __html: blog.content }}
                                />
                            </div>
                        </div>

                        {/* Share / Navigation */}
                        <div className="mt-5 d-flex justify-content-between align-items-center">
                            <Link to="/blogs" className="btn btn-outline-dark rounded-pill px-4">
                                ← Back to All Blogs
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .blog-content img { max-width: 100%; height: auto; border-radius: 12px; margin: 25px 0; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
                .blog-content h2, .blog-content h3, .blog-content h4 { color: #2c3e50; margin-top: 40px; margin-bottom: 20px; font-weight: 700; }
                .blog-content p { margin-bottom: 25px; }
                .blog-content ul, .blog-content ol { margin-bottom: 25px; padding-left: 25px; }
                .blog-content li { margin-bottom: 12px; }
                .blog-content blockquote { border-left: 5px solid ${mainColor}; padding: 20px 30px; background: #fdf2f2; font-style: italic; margin: 30px 0; border-radius: 0 10px 10px 0; }
            `}</style>
        </MasterLayout>
    );
};

export default BlogDetail;
