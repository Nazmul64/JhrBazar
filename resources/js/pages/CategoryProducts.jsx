import React, { useState, useEffect } from 'react';
import { useParams, Link, useLocation } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import ProductCard from '../components/ProductCard';
import axios from 'axios';
import SEO from '../components/SEO';

const CategoryProducts = () => {
    const { id } = useParams();
    const location = useLocation();
    const isSubCategory = location.pathname.includes('subcategory');
    const mainColor = '#57b500';

    const [categories, setCategories] = useState([]);
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [activeCategoryName, setActiveCategoryName] = useState("");
    const [priceRange, setPriceRange] = useState(5000);

    const formatImagePath = (path) => {
        if (!path) return '/placeholder.jpg';
        if (path.startsWith('http')) return path;
        return path.startsWith('/') ? path : '/' + path;
    };

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            try {
                // Fetch Categories for Sidebar
                const catRes = await axios.get('/api/categories');
                if (catRes.data.success) {
                    setCategories(catRes.data.data);
                }

                // Fetch current category/subcategory name
                const nameEndpoint = isSubCategory
                    ? `/api/subcategory/${id}/name`
                    : `/api/category/${id}/name`;

                const nameRes = await axios.get(nameEndpoint);
                if (nameRes.data.success) {
                    setActiveCategoryName(nameRes.data.name);
                }

                // Fetch Products
                const endpoint = isSubCategory
                    ? `/api/products/subcategory/${id}`
                    : `/api/products/category/${id}`;

                const prodRes = await axios.get(endpoint);
                if (prodRes.data.success) {
                    setProducts(prodRes.data.data);
                }
            } catch (error) {
                console.error("Error fetching category data:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
        window.scrollTo(0, 0);
    }, [id, isSubCategory]);

    // Data Layer: view_item_list
    useEffect(() => {
        if (products.length > 0) {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'view_item_list',
                item_list_id: String(id),
                item_list_name: activeCategoryName,
                items: products.map((product, index) => ({
                    item_id: String(product.id),
                    item_name: product.name || product.title,
                    price: Number(product.selling_price || product.price || 0),
                    index: index + 1
                }))
            });
        }
    }, [products, activeCategoryName, id]);

    return (
        <MasterLayout>
            <SEO title={activeCategoryName} url={window.location.href} />
            {/* Page Header */}
            <div className="bg-light py-4 mb-4 border-bottom">
                <div className="container">
                    <h2 className="fw-bold mb-1 text-dark">{activeCategoryName || (isSubCategory ? 'Sub-Category' : 'Category')}</h2>
                    <nav aria-label="breadcrumb">
                        <ol className="breadcrumb mb-0 small">
                            <li className="breadcrumb-item"><Link to="/" className="text-decoration-none text-muted">Home</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">{activeCategoryName}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div className="container pb-5">
                <div className="row g-4">
                    {/* Left Sidebar Filter */}
                    <div className="col-lg-3">
                        <div className="card border-0 shadow-sm rounded-3 mb-4">
                            <div className="card-header bg-white border-bottom fw-bold py-3">
                                📑 All Categories
                            </div>
                            <div className="card-body p-0">
                                <ul className="list-group list-group-flush border-0">
                                    {categories.map(cat => {
                                        const isActive = id == cat.id && !isSubCategory;
                                        const hasSub = cat.sub_categories?.length > 0 || cat.subCategories?.length > 0;
                                        const subCats = cat.sub_categories || cat.subCategories || [];
                                        
                                        return (
                                            <li key={cat.id} className="list-group-item border-0 p-0">
                                                <Link
                                                    to={`/category/${cat.id}`}
                                                    className={`d-flex justify-content-between align-items-center p-3 text-decoration-none ${isActive ? 'bg-light text-success fw-bold' : 'text-dark'}`}
                                                    style={{ transition: 'all 0.2s' }}
                                                >
                                                    <div className="d-flex align-items-center gap-2">
                                                        <img src={formatImagePath(cat.thumbnail)} alt="" style={{ width: '20px', height: '20px', objectFit: 'contain' }} />
                                                        <span>{cat.name}</span>
                                                    </div>
                                                    <i className={`fas ${isActive ? 'fa-chevron-down' : 'fa-chevron-right'} small`}></i>
                                                </Link>
                                                
                                                {/* Subcategories (Visible if this category is active or if we are in one of its subcategories) */}
                                                {(isActive || (isSubCategory && subCats.some(s => s.id == id))) && hasSub && (
                                                    <ul className="list-group list-group-flush ps-4 bg-light">
                                                        {subCats.map(sub => (
                                                            <li key={sub.id} className="list-group-item border-0 bg-transparent p-0">
                                                                <Link
                                                                    to={`/subcategory/${sub.id}`}
                                                                    className={`d-flex align-items-center gap-2 p-2 text-decoration-none small ${id == sub.id && isSubCategory ? 'text-success fw-bold' : 'text-muted'}`}
                                                                >
                                                                    <span>• {sub.name}</span>
                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </ul>
                                                )}
                                            </li>
                                        );
                                    })}
                                </ul>
                            </div>
                        </div>

                        {/* Price Filter */}
                        <div className="card border-0 shadow-sm rounded-3 mb-4">
                            <div className="card-header bg-white border-bottom fw-bold py-3">
                                💰 Filter by Price
                            </div>
                            <div className="card-body">
                                <input
                                    type="range"
                                    className="form-range"
                                    min="0" max="10000"
                                    value={priceRange}
                                    onChange={(e) => setPriceRange(e.target.value)}
                                    style={{ accentColor: mainColor }}
                                />
                                <div className="d-flex justify-content-between small text-muted mt-2">
                                    <span>$0</span>
                                    <span className="fw-bold text-dark">${priceRange}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right Product Grid */}
                    <div className="col-lg-9">
                        {loading ? (
                            <div className="py-5" style={{ minHeight: '60vh' }}></div>
                        ) : (
                            <>
                                {/* Top Bar */}
                                <div className="bg-white p-3 shadow-sm rounded-3 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                                    <div className="text-muted small">
                                        Showing <span className="fw-bold text-dark">{products.length}</span> results
                                    </div>
                                    <div className="d-flex align-items-center gap-2 small">
                                        <span className="text-muted">Sort by:</span>
                                        <select className="form-select form-select-sm border-0 bg-light fw-bold" style={{ width: '130px', boxShadow: 'none' }}>
                                            <option>Latest</option>
                                            <option>Price: Low to High</option>
                                            <option>Price: High to Low</option>
                                            <option>Top Rated</option>
                                        </select>
                                    </div>
                                </div>

                                {/* Products */}
                                <div className="row g-3 g-md-4">
                                    {products.length > 0 ? (
                                        products.map(product => (
                                            <div key={product.uid} className="col-6 col-md-4 col-xl-4">
                                                <ProductCard product={product} />
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center py-5 text-muted">
                                            No products found in this category.
                                        </div>
                                    )}
                                </div>

                                {/* Pagination (Placeholder) */}
                                {products.length > 0 && (
                                    <div className="d-flex justify-content-center mt-5">
                                        <nav>
                                            <ul className="pagination gap-2">
                                                <li className="page-item disabled"><span className="page-link rounded-3 border-0 bg-light text-muted">Prev</span></li>
                                                <li className="page-item"><button className="page-link rounded-3 border-0 active" style={{ backgroundColor: mainColor, color: '#fff' }}>1</button></li>
                                                <li className="page-item"><button className="page-link rounded-3 border-0 bg-light text-dark">Next</button></li>
                                            </ul>
                                        </nav>
                                    </div>
                                )}
                            </>
                        )}
                    </div>
                </div>
            </div>

            <style>{`
                .page-link { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; transition: all 0.2s; }
                .page-link:hover { background-color: #f8f9fa; }
                .page-item.disabled .page-link { width: auto; padding: 0 15px; }
                .page-item .page-link:not(.active):contains("Next") { width: auto; padding: 0 15px; }
                .page-item .page-link:not(.active):contains("Prev") { width: auto; padding: 0 15px; }
            `}</style>
        </MasterLayout>
    );
};

export default CategoryProducts;
