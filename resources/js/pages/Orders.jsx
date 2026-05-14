import React, { useEffect, useState } from 'react';
import MasterLayout from '../layouts/MasterLayout';
import axios from 'axios';
import { useSettings } from '../context/SettingsContext';
import { toast } from 'react-hot-toast';

const Orders = () => {
  const { settings } = useSettings();
  const mainColor = settings?.primary_color || '#001fcc';
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchOrders = async () => {
      try {
        const res = await axios.get('/api/orders');
        if (res.data.success) setOrders(res.data.data);
      } catch (e) {
        toast.error('অর্ডার লোড করতে সমস্যাঃ ' + e.message);
      } finally {
        setLoading(false);
      }
    };
    fetchOrders();
  }, []);

  if (loading) {
    return (
      <MasterLayout>
        <div className="container py-5 text-center">
          <div className="spinner-border text-success" role="status">
            <span className="visually-hidden">লোড হচ্ছে...</span>
          </div>
        </div>
      </MasterLayout>
    );
  }

  return (
    <MasterLayout>
      <div className="container py-5">
        <h2 className="fw-bold mb-4" style={{ color: mainColor }}>অর্ডার ম্যানেজমেন্ট</h2>
        {orders.length === 0 ? (
          <p className="text-muted">কোনো অর্ডার পাওয়া যায়নি।</p>
        ) : (
          <table className="table table-hover" style={{ backgroundColor: '#fff', borderRadius: '8px' }}>
            <thead style={{ backgroundColor: '#f8f9fa' }}>
              <tr>
                <th>#</th>
                <th>ইনভয়েস নং</th>
                <th>স্ট্যাটাস</th>
                <th>গ্র্যান্ড টোটাল</th>
                <th>শিপিং</th>
                <th>পেমেন্ট মেথড</th>
              </tr>
            </thead>
            <tbody>
              {orders.map((order, idx) => (
                <tr key={order.id} className="align-middle">
                  <td>{idx + 1}</td>
                  <td>{order.invoice?.invoice_number || order.id}</td>
                  <td>{order.status}</td>
                  <td>{Number(order.grand_total).toLocaleString('en-BD')}</td>
                  <td>{order.delivery_charge}</td>
                  <td>{order.payment_method}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </MasterLayout>
  );
};

export default Orders;
