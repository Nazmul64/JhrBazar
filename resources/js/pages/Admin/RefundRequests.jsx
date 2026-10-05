import React, { useState, useEffect } from 'react';

const RefundRequests = () => {
    const [refunds, setRefunds] = useState([]);
    const [statusFilter, setStatusFilter] = useState('');
    const [loading, setLoading] = useState(true);
    const [selectedRefund, setSelectedRefund] = useState(null);
    const [showReviewModal, setShowReviewModal] = useState(false);
    const [refundAction, setRefundAction] = useState('approved');
    const [refundMethod, setRefundMethod] = useState('gateway');
    const [adminNote, setAdminNote] = useState('');
    const [transactionId, setTransactionId] = useState('');

    const fetchRefunds = async () => {
        setLoading(true);
        try {
            const url = statusFilter ? `/api/admin/refunds?status=${statusFilter}` : '/api/admin/refunds';
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            setRefunds(data.data || []);
        } catch (err) {
            console.error('Failed to fetch refunds', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchRefunds();
    }, [statusFilter]);

    const handleExecuteRefund = async (e) => {
        e.preventDefault();
        try {
            const res = await fetch(`/api/admin/refunds/${selectedRefund.id}/action`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    action: refundAction,
                    refund_method: refundMethod,
                    admin_note: adminNote,
                    transaction_id: transactionId
                })
            });
            const data = await res.json();
            if (data.message) {
                alert(data.message);
                setShowReviewModal(false);
                fetchRefunds();
            }
        } catch (err) {
            alert('Failed to process refund');
        }
    };

    return (
        <div className="p-6 bg-gray-50 min-h-screen">
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-800">Refund & Return Management</h1>
                    <p className="text-gray-500 text-sm">Review customer returns, seller comments, and execute atomic refunds</p>
                </div>
            </div>

            {/* Filter Tabs */}
            <div className="flex gap-2 border-b border-gray-200 mb-6 pb-2">
                {[
                    { key: '', label: 'All Requests' },
                    { key: 'pending', label: 'Pending' },
                    { key: 'completed', label: 'Completed' },
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

            {/* Refunds Table */}
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table className="w-full text-left border-collapse">
                    <thead className="bg-gray-50 text-gray-600 text-xs uppercase font-bold border-b">
                        <tr>
                            <th className="p-4">Order #</th>
                            <th className="p-4">Product</th>
                            <th className="p-4">Customer</th>
                            <th className="p-4">Seller Approval</th>
                            <th className="p-4">Amount</th>
                            <th className="p-4">Status</th>
                            <th className="p-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y text-sm">
                        {loading ? (
                            <tr><td colSpan="7" className="p-8 text-center text-gray-400">Loading refund requests...</td></tr>
                        ) : refunds.length === 0 ? (
                            <tr><td colSpan="7" className="p-8 text-center text-gray-400">No refund requests found.</td></tr>
                        ) : refunds.map(ref => (
                            <tr key={ref.id} className="hover:bg-gray-50 transition">
                                <td className="p-4 font-bold text-indigo-600">#{ref.order?.invoice?.invoice_number || ref.order_id}</td>
                                <td className="p-4">
                                    <div className="font-semibold text-gray-800">{ref.product_name}</div>
                                    <div className="text-xs text-gray-400">Qty: {ref.quantity}</div>
                                </td>
                                <td className="p-4">
                                    <div className="font-medium text-gray-700">{ref.customer?.name || 'Customer'}</div>
                                    <div className="text-xs text-gray-400">{ref.customer?.email}</div>
                                </td>
                                <td className="p-4">
                                    {ref.seller_approval === 'approved' && <span className="text-xs px-2 py-0.5 rounded bg-green-100 text-green-700 font-semibold">Accepted</span>}
                                    {ref.seller_approval === 'rejected' && <span className="text-xs px-2 py-0.5 rounded bg-red-100 text-red-700 font-semibold">Declined</span>}
                                    {ref.seller_approval === 'pending' && <span className="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600 font-semibold">Pending Note</span>}
                                </td>
                                <td className="p-4 font-bold text-gray-900">৳{Number(ref.total_amount).toFixed(2)}</td>
                                <td className="p-4">
                                    <span className={`px-2.5 py-1 rounded-full text-xs font-bold ${
                                        ref.refund_status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                                        ref.refund_status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800'
                                    }`}>
                                        {ref.refund_status.toUpperCase()}
                                    </span>
                                </td>
                                <td className="p-4 text-right">
                                    {ref.refund_status === 'pending' ? (
                                        <button
                                            onClick={() => { setSelectedRefund(ref); setShowReviewModal(true); }}
                                            className="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow"
                                        >
                                            Review & Pay
                                        </button>
                                    ) : (
                                        <span className="text-xs text-gray-400 font-medium">Processed</span>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Review & Pay Action Modal */}
            {showReviewModal && selectedRefund && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
                    <div className="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
                        <h3 className="text-lg font-bold text-gray-900 mb-1">Process Refund Request #{selectedRefund.id}</h3>
                        <p className="text-xs text-gray-500 mb-4">Atomic Restock + Seller Account Debit</p>

                        <div className="bg-gray-50 p-3 rounded-lg text-xs space-y-1 mb-4 border">
                            <div><strong>Product:</strong> {selectedRefund.product_name} (Qty: {selectedRefund.quantity})</div>
                            <div><strong>Reason:</strong> {selectedRefund.cancel_reason} ({selectedRefund.cancel_reason_description || 'No description'})</div>
                            <div><strong>Seller Note:</strong> {selectedRefund.seller_note || 'None'}</div>
                            <div><strong>Refund Amount:</strong> ৳{Number(selectedRefund.total_amount).toFixed(2)}</div>
                        </div>

                        <form onSubmit={handleExecuteRefund} className="space-y-3 text-sm">
                            <div>
                                <label className="block text-xs font-bold text-gray-700 mb-1">Action</label>
                                <select value={refundAction} onChange={(e) => setRefundAction(e.target.value)} className="w-full border rounded-lg p-2 text-sm">
                                    <option value="approved">Approve & Refund Money</option>
                                    <option value="rejected">Reject Request</option>
                                </select>
                            </div>

                            {refundAction === 'approved' && (
                                <>
                                    <div>
                                        <label className="block text-xs font-bold text-gray-700 mb-1">Refund Method</label>
                                        <select value={refundMethod} onChange={(e) => setRefundMethod(e.target.value)} className="w-full border rounded-lg p-2 text-sm">
                                            <option value="gateway">Original Gateway (bKash / SSL / Stripe API)</option>
                                            <option value="wallet">Customer Wallet (Add to Balance)</option>
                                            <option value="manual">Manual Transfer (Bank / bKash Manual)</option>
                                        </select>
                                    </div>
                                    {refundMethod === 'manual' && (
                                        <div>
                                            <label className="block text-xs font-bold text-gray-700 mb-1">Manual Transaction ID / Note</label>
                                            <input
                                                type="text"
                                                value={transactionId}
                                                onChange={(e) => setTransactionId(e.target.value)}
                                                className="w-full border rounded-lg p-2 text-sm"
                                                placeholder="e.g. TRX-BKASH-88219"
                                                required
                                            />
                                        </div>
                                    )}
                                </>
                            )}

                            <div>
                                <label className="block text-xs font-bold text-gray-700 mb-1">Admin Note</label>
                                <textarea
                                    value={adminNote}
                                    onChange={(e) => setAdminNote(e.target.value)}
                                    rows="2"
                                    className="w-full border rounded-lg p-2 text-sm"
                                    placeholder="Note for record..."
                                />
                            </div>

                            <div className="flex justify-end gap-2 pt-2 border-t">
                                <button type="button" onClick={() => setShowReviewModal(false)} className="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                                <button type="submit" className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow">
                                    Execute Refund
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
};

export default RefundRequests;
