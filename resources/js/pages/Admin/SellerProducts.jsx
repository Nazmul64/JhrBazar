import React, { useState, useEffect } from 'react';

const SellerProducts = () => {
    const [products, setProducts] = useState([]);
    const [statusFilter, setStatusFilter] = useState('');
    const [loading, setLoading] = useState(true);
    const [selectedProduct, setSelectedProduct] = useState(null);
    const [rejectReason, setRejectReason] = useState('');
    const [showRejectModal, setShowRejectModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [editForm, setEditForm] = useState({ name: '', selling_price: '', discount_price: '', stock_quantity: '', is_active: false, admin_status: 'pending' });

    const fetchProducts = async () => {
        setLoading(true);
        try {
            const url = statusFilter ? `/api/admin/seller-products?status=${statusFilter}` : '/api/admin/seller-products';
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            setProducts(data.data || data.products?.data || []);
        } catch (err) {
            console.error('Failed to fetch seller products', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchProducts();
    }, [statusFilter]);

    const handleStatusUpdate = async (id, status, reason = null) => {
        try {
            const res = await fetch(`/api/admin/seller-products/${id}/status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ admin_status: status, rejection_reason: reason })
            });
            const data = await res.json();
            if (data.success || data.product) {
                setShowRejectModal(false);
                setRejectReason('');
                fetchProducts();
            }
        } catch (err) {
            alert('Failed to update status');
        }
    };

    const handleSaveEdit = async (e) => {
        e.preventDefault();
        try {
            const res = await fetch(`/api/admin/seller-products/${selectedProduct.id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(editForm)
            });
            const data = await res.json();
            if (data.success || data.product) {
                setShowEditModal(false);
                fetchProducts();
            }
        } catch (err) {
            alert('Failed to update product');
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm('Are you sure you want to delete this seller product?')) return;
        try {
            await fetch(`/api/admin/seller-products/${id}`, { method: 'DELETE' });
            fetchProducts();
        } catch (err) {
            alert('Failed to delete product');
        }
    };

    return (
        <div className="p-6 bg-gray-50 min-h-screen">
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-800">Seller Products Management</h1>
                    <p className="text-gray-500 text-sm">Review, approve, reject or edit vendor uploaded products</p>
                </div>
            </div>

            {/* Filter Tabs */}
            <div className="flex gap-2 border-b border-gray-200 mb-6 pb-2">
                {[
                    { key: '', label: 'All Products' },
                    { key: 'pending', label: 'Pending Review' },
                    { key: 'approved', label: 'Approved' },
                    { key: 'rejected', label: 'Rejected' },
                ].map(tab => (
                    <button
                        key={tab.key}
                        onClick={() => setStatusFilter(tab.key)}
                        className={`px-4 py-2 rounded-lg text-sm font-semibold transition ${
                            statusFilter === tab.key ? 'bg-indigo-600 text-white shadow' : 'bg-white text-gray-600 hover:bg-gray-100 border'
                        }`}
                    >
                        {tab.label}
                    </button>
                ))}
            </div>

            {/* Product Table */}
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table className="w-full text-left border-collapse">
                    <thead className="bg-gray-50 text-gray-600 text-xs uppercase font-bold border-b">
                        <tr>
                            <th className="p-4">Product</th>
                            <th className="p-4">Seller</th>
                            <th className="p-4">Price</th>
                            <th className="p-4">Stock</th>
                            <th className="p-4">Status</th>
                            <th className="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y text-sm">
                        {loading ? (
                            <tr><td colSpan="6" className="p-8 text-center text-gray-400">Loading products...</td></tr>
                        ) : products.length === 0 ? (
                            <tr><td colSpan="6" className="p-8 text-center text-gray-400">No seller products found.</td></tr>
                        ) : products.map(prod => (
                            <tr key={prod.id} className="hover:bg-gray-50 transition">
                                <td className="p-4 flex items-center gap-3">
                                    <img src={prod.thumbnail || '/placeholder.png'} alt={prod.name} className="w-12 h-12 rounded object-cover border" />
                                    <div>
                                        <div className="font-semibold text-gray-800">{prod.name}</div>
                                        <div className="text-xs text-gray-400">SKU: {prod.sku || '—'}</div>
                                    </div>
                                </td>
                                <td className="p-4">
                                    <div className="font-medium text-gray-700">{prod.seller?.name || 'Seller'}</div>
                                    <div className="text-xs text-gray-400">{prod.seller?.email}</div>
                                </td>
                                <td className="p-4 font-bold text-gray-900">৳{Number(prod.selling_price).toFixed(2)}</td>
                                <td className="p-4">
                                    <span className={`px-2 py-1 rounded text-xs font-bold ${prod.stock_quantity > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                                        {prod.stock_quantity} in stock
                                    </span>
                                </td>
                                <td className="p-4">
                                    {prod.admin_status === 'pending' && <span className="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending Review</span>}
                                    {prod.admin_status === 'approved' && <span className="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Approved</span>}
                                    {prod.admin_status === 'rejected' && <span className="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800" title={prod.rejection_reason}>Rejected</span>}
                                </td>
                                <td className="p-4 text-right space-x-2">
                                    {prod.admin_status !== 'approved' && (
                                        <button onClick={() => handleStatusUpdate(prod.id, 'approved')} className="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold">Approve</button>
                                    )}
                                    {prod.admin_status !== 'rejected' && (
                                        <button onClick={() => { setSelectedProduct(prod); setShowRejectModal(true); }} className="px-3 py-1 bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 rounded text-xs font-bold">Reject</button>
                                    )}
                                    <button onClick={() => {
                                        setSelectedProduct(prod);
                                        setEditForm({ name: prod.name, selling_price: prod.selling_price, discount_price: prod.discount_price || '', stock_quantity: prod.stock_quantity, is_active: prod.is_active, admin_status: prod.admin_status });
                                        setShowEditModal(true);
                                    }} className="px-3 py-1 bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 rounded text-xs font-bold">Edit</button>
                                    <button onClick={() => handleDelete(prod.id)} className="px-2 py-1 text-gray-400 hover:text-rose-600 text-xs">Delete</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Reject Modal */}
            {showRejectModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
                    <div className="bg-white rounded-xl max-w-md w-full p-6 shadow-xl">
                        <h3 className="text-lg font-bold text-gray-900 mb-2">Reject Product</h3>
                        <p className="text-sm text-gray-500 mb-4">Please provide a reason for rejecting "{selectedProduct?.name}"</p>
                        <textarea
                            value={rejectReason}
                            onChange={(e) => setRejectReason(e.target.value)}
                            rows="3"
                            className="w-full border rounded-lg p-3 text-sm focus:ring-2 focus:ring-rose-500 outline-none mb-4"
                            placeholder="Reason for rejection (e.g. invalid pricing, prohibited item)..."
                            required
                        />
                        <div className="flex justify-end gap-2">
                            <button onClick={() => setShowRejectModal(false)} className="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                            <button onClick={() => handleStatusUpdate(selectedProduct.id, 'rejected', rejectReason)} className="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-semibold">Confirm Rejection</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default SellerProducts;
