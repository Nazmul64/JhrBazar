import React from 'react';
import MasterLayout from '../layouts/MasterLayout';

const blogs = [
    {
        id: 1,
        category: "Clothing, Shoes & Jewelry",
        title: "Top Clothing Trends to Elevate Your Style In 2024",
        desc: "Fashion is ever-evolving, reflecting shifts in culture, technology, and sustainability...",
        author: "Admin",
        date: "27 Jan, 2024",
        views: 6,
        image: "https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=500&auto=format&fit=crop"
    },
    {
        id: 2,
        category: "Gadgets",
        title: "Boosting Productivity with Smartwatches In Today's World",
        desc: "Boosting Productivity with Smartwatches in today's fast-paced world is very essential...",
        author: "Admin",
        date: "27 Jan, 2024",
        views: 3,
        image: "https://images.unsplash.com/photo-1544117518-30dd057a1bb2?q=80&w=500&auto=format&fit=crop"
    },
    {
        id: 3,
        category: "Business",
        title: "Using a Complete Solution to Grow Your E-commerce Business",
        desc: "What Does Scaling an eCommerce Business Actually mean? In simple words...",
        author: "Admin",
        date: "27 Jan, 2024",
        views: 5,
        image: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=500&auto=format&fit=crop"
    }
];

const Blogs = () => {
    const mainColor = '#ff4d4d';

    return (
        <MasterLayout>
            <div className="container py-5">
                {/* Blog Header & Categories (Matches Screenshot) */}
                <div className="bg-white p-4 shadow-sm mb-5" style={{ borderRadius: '15px' }}>
                    <h3 className="fw-bold mb-4">Ready Blogs</h3>
                    <div className="d-flex flex-wrap gap-3">
                        <button className="btn px-4 py-2 rounded-3 border" style={{ borderColor: mainColor, color: mainColor, backgroundColor: '#fff', fontWeight: 'bold' }}>All Blogs</button>
                        {['Clothing, Shoes & Jewelry', 'Gadgets', 'Business'].map(cat => (
                            <button key={cat} className="btn px-4 py-2 rounded-3 border-0 bg-light text-muted fw-bold" style={{ fontSize: '14px' }}>{cat}</button>
                        ))}
                    </div>
                </div>

                {/* Blog Cards Grid */}
                <div className="row g-4 mb-5">
                    {blogs.map(blog => (
                        <div key={blog.id} className="col-md-6 col-lg-4">
                            <div className="card h-100 border-0 shadow-sm" style={{ borderRadius: '20px', overflow: 'hidden' }}>
                                <div className="position-relative">
                                    <img src={blog.image} alt={blog.title} style={{ height: '220px', width: '100%', objectFit: 'cover' }} />
                                    <span className="position-absolute top-0 left-0 m-3 badge bg-danger" style={{ backgroundColor: mainColor, borderRadius: '5px', padding: '5px 12px' }}>New</span>
                                </div>
                                <div className="card-body p-4">
                                    <span className="small fw-bold" style={{ color: mainColor }}>{blog.category}</span>
                                    <h5 className="fw-bold mt-2 mb-3" style={{ fontSize: '18px', lineHeight: '1.4' }}>{blog.title}</h5>
                                    <p className="text-muted small mb-4" style={{ height: '40px', overflow: 'hidden' }}>{blog.desc}</p>
                                    <div className="d-flex justify-content-between align-items-center border-top pt-3">
                                        <div className="small text-muted fw-bold">By {blog.author} <span className="ms-2">{blog.date}</span></div>
                                        <div className="small text-muted">👁️ {blog.views}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                {/* Pagination */}
                <div className="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div className="small text-muted">Showing 1 to 3 of 3 results</div>
                    <button className="btn text-white fw-bold" style={{ backgroundColor: mainColor, width: '40px', height: '40px', borderRadius: '10px' }}>1</button>
                </div>
            </div>
        </MasterLayout>
    );
};

export default Blogs;
