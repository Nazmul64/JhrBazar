import React from 'react';
import MasterLayout from '../layouts/MasterLayout';
import { useWishlist } from '../context/WishlistContext';
import { useCart } from '../context/CartContext';
import { Link } from 'react-router-dom';

const Wishlist = () => {
    const { wishlist, loading, toggleWishlist } = useWishlist();
    const { addToCart } = useCart();
    const mainColor = '#57b500';

    const handleRemove = (product) => {
        toggleWishlist(product);
    };

    const handleAddToCart = (product) => {
        addToCart(product, 1);
        handleRemove(product); // This will remove it from the wishlist
    };

    return (
        <MasterLayout>
            <div className="container py-5">
                {/* Header Section */}
                <div className="mb-5">
                    <h2 className="fw-bold mb-1" style={{ color: '#1a1a1a', fontSize: '32px' }}>উইশলিস্ট</h2>
                    <nav aria-label="breadcrumb">
                        <ol className="breadcrumb small mb-0">
                            <li className="breadcrumb-item"><Link to="/" className="text-decoration-none text-muted">হোম</Link></li>
                            <li className="breadcrumb-item"><span className="text-muted">পেজ</span></li>
                            <li className="breadcrumb-item active text-muted" aria-current="page">উইশলিস্ট</li>
                        </ol>
                    </nav>
                </div>

                {loading ? (
                    <div className="text-center py-5">
                        <div className="spinner-border text-success" role="status"></div>
                    </div>
                ) : (
                    wishlist.length > 0 ? (
                        <div className="card border-0 shadow-sm overflow-hidden" style={{ borderRadius: '15px', border: '1px solid #f0f0f0' }}>
                            <div className="table-responsive">
                                <table className="table align-middle mb-0">
                                    <thead className="bg-light">
                                        <tr>
                                            <th className="ps-4 py-3 text-muted fw-bold" style={{ fontSize: '14px', width: '40%' }}>পণ্য</th>
                                            <th className="py-3 text-muted fw-bold text-center" style={{ fontSize: '14px' }}>মূল্য</th>
                                            <th className="py-3 text-muted fw-bold text-center" style={{ fontSize: '14px' }}>স্টক স্ট্যাটাস</th>
                                            <th className="pe-4 py-3 text-muted fw-bold text-end" style={{ fontSize: '14px' }}>মুছে ফেলুন</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {wishlist.map(product => (
                                            <tr key={product.uid} className="wishlist-row">
                                                <td className="ps-4 py-4">
                                                    <div className="d-flex align-items-center gap-3">
                                                        <Link to={`/product-details/${product.product_type}/${product.slug}`}>
                                                            <div className="rounded-3 border overflow-hidden" style={{ width: '80px', height: '80px' }}>
                                                                <img
                                                                    src={product.image}
                                                                    alt={product.title}
                                                                    className="w-100 h-100"
                                                                    style={{ objectFit: 'cover' }}
                                                                />
                                                            </div>
                                                        </Link>
                                                        <div>
                                                            <Link
                                                                to={`/product-details/${product.product_type}/${product.slug}`}
                                                                className="text-decoration-none fw-bold text-dark hover-primary-text d-block mb-1"
                                                                style={{ fontSize: '15px' }}
                                                            >
                                                                {product.title}
                                                            </Link>
                                                            <div className="d-flex flex-wrap gap-2">
                                                                {product.brand && <span className="text-muted small"><strong>ব্র্যান্ড:</strong> {product.brand}</span>}
                                                                {product.size && <span className="text-muted small"><strong>সাইজ:</strong> {product.size}</span>}
                                                                {product.color && <span className="text-muted small"><strong>কালার:</strong> {product.color}</span>}
                                                                {product.unit && <span className="text-muted small"><strong>ইউনিট:</strong> {product.unit}</span>}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="py-4 text-center">
                                                    <span className="fw-bold" style={{ fontSize: '16px' }}>৳{Number(product.price).toLocaleString()}</span>
                                                </td>
                                                <td className="py-4 text-center">
                                                    <span
                                                        className="badge rounded-pill px-3 py-2"
                                                        style={{
                                                            backgroundColor: product.stock > 0 ? '#198754' : '#6c757d',
                                                            fontSize: '11px'
                                                        }}
                                                    >
                                                        <i className={`fas ${product.stock > 0 ? 'fa-check' : 'fa-times'} me-1`}></i>
                                                        {product.stock > 0 ? 'স্টকে আছে' : 'স্টক শেষ'}
                                                    </span>
                                                </td>
                                                <td className="pe-4 py-4 text-end">
                                                    <div className="d-flex align-items-center justify-content-end gap-3">
                                                        <button
                                                            onClick={() => handleAddToCart(product)}
                                                            className="btn text-white fw-bold px-4 py-2 d-flex align-items-center gap-2"
                                                            style={{
                                                                backgroundColor: '#e31e24',
                                                                borderRadius: '8px',
                                                                fontSize: '13px',
                                                                transition: 'all 0.3s'
                                                            }}
                                                        >
                                                            🛒 কার্টে যোগ করুন
                                                        </button>
                                                        <button
                                                            onClick={() => handleRemove(product)}
                                                            className="btn border-0 p-0 text-muted hover-danger"
                                                            title="Remove Item"
                                                            style={{ fontSize: '18px', transition: 'all 0.2s' }}
                                                        >
                                                            ✕
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    ) : (
                        <div className="text-center py-5 bg-white rounded shadow-sm border">
                            <div style={{ fontSize: '80px', marginBottom: '20px', opacity: 0.2 }}>🛒</div>
                            <h4 className="fw-bold text-dark">আপনার উইশ লিস্ট খালি</h4>
                            <p className="text-muted mb-4 px-3">পছন্দের পণ্যগুলো এখানে জমা করে রাখতে পারেন এবং পরে এক ক্লিকেই কার্টে যোগ করতে পারেন।</p>
                            <Link to="/" className="btn text-white px-5 py-3" style={{ backgroundColor: mainColor, borderRadius: '30px', fontWeight: 'bold', fontSize: '15px' }}>
                                কেনাকাটা চালিয়ে যান
                            </Link>
                        </div>
                    )
                )}
            </div>
            <style>{`
                .wishlist-row { transition: all 0.2s; }
                .wishlist-row:hover { background-color: #fcfcfc; }
                .hover-primary-text:hover { color: ${mainColor} !important; }
                .hover-danger:hover { color: #e31e24 !important; transform: scale(1.2); }
                .btn:active { transform: scale(0.95); }
                
                @media (max-width: 768px) {
                    .table thead { display: none; }
                    .table tbody tr { display: block; border-bottom: 1px solid #eee; padding: 15px 0; }
                    .table td { display: block; text-align: left !important; padding: 5px 20px !important; border: none; }
                    .table td:last-child { text-align: right !important; }
                }
            `}</style>
        </MasterLayout>
    );
};

export default Wishlist;
