
import React from 'react';
import { useLocation, Link } from 'react-router-dom';
import MasterLayout from '../layouts/MasterLayout';
import { Toaster, toast } from 'react-hot-toast';

const OrderManagement = () => {
  const { state } = useLocation();
  const orders = state?.orders || [];

  // Show a toast when component mounts
  React.useEffect(() => {
    if (orders.length) {
      toast.success('অর্ডার সফলভাবে তৈরি হয়েছে!');
    }
  }, [orders]);

  return (
    <MasterLayout>
      <div className="container py-5">
        <h2 className="mb-4" style={{ color: '#57b500' }}>অর্ডার সফল</h2>
        {orders.length === 0 ? (
          <p>কোনো অর্ডার তথ্য পাওয়া যায়নি। দয়া করে আবার চেষ্টা করুন।</p>
        ) : (
          <div className="card p-4" style={{ borderRadius: '15px' }}>
            <h4 className="mb-3">ইনভয়েস নং: {orders[0].invoice_number || orders[0].id}</h4>
            <p>আপনার অর্ডারটি এখন প্রক্রিয়াকরণে রয়েছে। আপনি <Link to="/order-tracking" className="text-primary">অর্ডার ট্র্যাকিং</Link> পেজে গিয়ে অর্ডারের অগ্রগতি দেখতে পারেন।</p>
          </div>
        )}
        <div className="mt-4">
          <Link to="/" className="btn btn-primary" style={{ backgroundColor: '#57b500', borderColor: '#57b500' }}>
            শপিং চালিয়ে যান
          </Link>
        </div>
      </div>
      <Toaster position="top-right" reverseOrder={false} />
    </MasterLayout>
  );
};

export default OrderManagement;
