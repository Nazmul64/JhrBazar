import React, { useState, useEffect } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import axios from 'axios';
import MasterLayout from '../layouts/MasterLayout';
import SEO from '../components/SEO';

const Blogs = () => {
    const [searchParams] = useSearchParams();
    const categorySlug = searchParams.get('category');
    
    const [blogs, setBlogs] = useState([]);
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [pagination, setPagination] = useState(null);
    const mainColor = '#ff4d4d';

    useEffect(() => {
        // Fetch Categories
        axios.get('/api/blog-categories')
            .then(res => {
                if (res.data.success) {
                    setCategories(res.data.data);
                }
            });
    }, []);

    useEffect(() => {
        setLoading(true);
        const url = categorySlug ? `/api/blogs?category_slug=${categorySlug}` : '/api/blogs';
        axios.get(url)
            .then(res => {
                if (res.data.success) {
                    setBlogs(res.data.data.data);
                    setPagination(res.data.data);
                }
                setLoading(false);
            })
            .catch(err => {
                console.error("Error fetching blogs:", err);
                setLoading(false);
            });
    }, [categorySlug]);

    return (
        <MasterLayout>
            <SEO 
                title="Blog - JhrBazar" 
                description="Read latest updates, fashion trends and electronics gadgets reviews on JhrBazar blog."
            />
            
            <div className="container py-5">
                {/* Blog Header & Categories */}
                <div className="bg-white p-4 shadow-sm mb-5" style={{ borderRadius: '15px' }}>
                    <h3 className="fw-bold mb-4">Latest Blogs</h3>
                    <div className="d-flex flex-wrap gap-3">
                        <Link 
                            to="/blogs" 
                            className={`btn px-4 py-2 rounded-3 border ${!categorySlug ? 'active' : ''}`} 
                            style={!categorySlug ? { backgroundColor: mainColor, color: '#fff', fontWeight: 'bold', borderColor: mainColor } : { borderColor: '#eee', color: '#666', backgroundColor: '#fff' }}
                        >
                            All Blogs
                        </Link>
                        {categories.map(cat => (
                            <Link 
                                key={cat.id} 
                                to={`/blogs?category=${cat.slug}`}
                                className={`btn px-4 py-2 rounded-3 border ${categorySlug === cat.slug ? 'active' : ''}`}
                                style={categorySlug === cat.slug ? { backgroundColor: mainColor, color: '#fff', fontWeight: 'bold', borderColor: mainColor } : { borderColor: '#eee', color: '#666', backgroundColor: '#f9f9f9' }}
                            >
                                {cat.name}
                            </Link>
                        ))}
                    </div>
                </div>

                {/* Blog Cards Grid */}
                {loading ? (
                    <div className="text-center py-5">
                        <div className="spinner-border text-danger" role="status"></div>
                    </div>
                ) : (
                    <div className="row g-4 mb-5">
                        {blogs.length > 0 ? blogs.map(blog => (
                            <div key={blog.id} className="col-md-6 col-lg-4">
                                <Link to={`/blog/${blog.slug}`} className="text-decoration-none">
                                    <div className="card h-100 border-0 shadow-sm blog-card" style={{ borderRadius: '20px', overflow: 'hidden', transition: 'transform 0.3s' }}>
                                        <div className="position-relative">
                                            <img 
                                                src={blog.thumbnail || '/placeholder-blog.jpg'} 
                                                alt={blog.title} 
                                                style={{ height: '220px', width: '100%', objectFit: 'cover' }} 
                                            />
                                            <span className="position-absolute top-0 left-0 m-3 badge" style={{ backgroundColor: mainColor, borderRadius: '5px', padding: '5px 12px' }}>
                                                {blog.category?.name}
                                            </span>
                                        </div>
                                        <div className="card-body p-4 text-dark">
                                            <h5 className="fw-bold mt-2 mb-3" style={{ fontSize: '18px', lineHeight: '1.4' }}>{blog.title}</h5>
                                            <div 
                                                className="text-muted small mb-4" 
                                                style={{ height: '40px', overflow: 'hidden' }}
                                                dangerouslySetInnerHTML={{ __html: blog.content.substring(0, 100) + '...' }}
                                            />
                                            <div className="d-flex justify-content-between align-items-center border-top pt-3">
                                                <div className="small text-muted fw-bold">
                                                    {new Date(blog.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                </div>
                                                <div className="small" style={{ color: mainColor }}>Read More →</div>
                                            </div>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        )) : (
                            <div className="col-12 text-center py-5">
                                <h4 className="text-muted">No blogs found in this category.</h4>
                            </div>
                        )}
                    </div>
                )}

                {/* Pagination */}
                {pagination && pagination.last_page > 1 && (
                    <div className="d-flex justify-content-center mt-4">
                        <nav>
                            <ul className="pagination">
                                {[...Array(pagination.last_page).keys()].map(i => (
                                    <li key={i+1} className={`page-item ${pagination.current_page === i+1 ? 'active' : ''}`}>
                                        <button 
                                            className="page-link" 
                                            style={pagination.current_page === i+1 ? { backgroundColor: mainColor, borderColor: mainColor } : { color: mainColor }}
                                            onClick={() => {/* Handle page change */}}
                                        >
                                            {i+1}
                                        </button>
                                    </li>
                                ))}
                            </ul>
                        </nav>
                    </div>
                )}
            </div>

            <style>{`
                .blog-card:hover { transform: translateY(-10px); }
            `}</style>
        </MasterLayout>
    );
};

export default Blogs;
