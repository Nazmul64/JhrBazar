import React, { useEffect, useState } from 'react';
import MasterLayout from '../layouts/MasterLayout';
import axios from 'axios';
import { useSettings } from '../context/SettingsContext';
import { toast } from 'react-hot-toast';
import { useNavigate } from 'react-router-dom';

const CreateOrder = () => {
  const { settings } = useSettings();
  const mainColor = settings?.primary_color || window.initialSettings?.primary_color || '#57b500';
  const navigate = useNavigate();

  const [products, setProducts] = useState([]);
  const [selectedProductId, setSelectedProductId] = useState('');
  const [quantity, setQuantity] = useState(1);
  const [orderItems, setOrderItems] = useState([]);
  const [form, setForm] = useState({
    name: '',
    phone: '',
    address: '',
    city: '',
    payment_method: 'cod', // cash on delivery default
    shipping_charge: 0,
    discount: 0,
    coupon_code: '',
  });

  // Fetch all products for the dropdown
  useEffect(() => {
    const fetchProducts = async () => {
      try {
        const res = await axios.get('/api/all-products');
        if (res.data.success) setProducts(res.data.data);
      } catch (e) {
        toast.error('প্রোডাক্ট লোড করতে সমস্যা: ' + e.message);
      }
    };
    fetchProducts();
  }, []);

  const addItem = () => {
    if (!selectedProductId) return toast.error('প্রোডাক্ট সিলেক্ট করুন');
    const prod = products.find(p => p.id === Number(selectedProductId));
    if (!prod) return toast.error('প্রোডাক্ট পাওয়া যায়নি');
    const exists = orderItems.find(i => i.id === prod.id);
    if (exists) {
      // update quantity
      setOrderItems(prev =>
        prev.map(i => (i.id === prod.id ? { ...i, qty: i.qty + quantity } : i))
      );
    } else {
      setOrderItems(prev => [...prev, { id: prod.id, title: prod.title, price: prod.price, qty: quantity }]);
    }
    toast.success('প্রোডাক্ট যোগ করা হয়েছে');
    setSelectedProductId('');
    setQuantity(1);
  };

  const removeItem = id => {
    setOrderItems(prev => prev.filter(i => i.id !== id));
  };

  const handleChange = e => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const submitOrder = async () => {
    if (orderItems.length === 0) return toast.error('কোনো আইটেম যোগ করা হয়নি');
    try {
      const payload = {
        ...form,
        items: orderItems.map(i => ({
          id: i.id,
          title: i.title,
          price: i.price,
          qty: i.qty,
          seller_id: i.seller_id || 0,
        })),
      };
      const res = await axios.post('/api/place-order', payload);
      if (res.data.success) {
        toast.success('অর্ডার সফলভাবে তৈরি হয়েছে');
        navigate('/order-success', { state: { orders: res.data.orders } });
      } else {
        toast.error(res.data.message || 'অর্ডার তৈরি ব্যর্থ');
      }
    } catch (e) {
      toast.error('অর্ডার তৈরি করতে ত্রুটি: ' + e.message);
    }
  };

  return (
    <MasterLayout>
      <div className="container py-5">
        <h2 className="fw-bold mb-4" style={{ color: mainColor }}>
          নতুন অর্ডার তৈরি করুন
        </h2>
        {/* ---- Customer Info ---- */}
        <div className="row g-4 mb-4">
          <div className="col-md-6">
            <input
              type="text"
              name="name"
              placeholder="নাম"
              className="form-control"
              value={form.name}
              onChange={handleChange}
            />
          </div>
          <div className="col-md-6">
            <input
              type="text"
              name="phone"
              placeholder="ফোন নম্বর"
              className="form-control"
              value={form.phone}
              onChange={handleChange}
            />
          </div>
          <div className="col-12">
            <input
              type="text"
              name="address"
              placeholder="ঠিকানা"
              className="form-control"
              value={form.address}
              onChange={handleChange}
            />
          </div>
          <div className="col-md-6">
            <input
              type="text"
              name="city"
              placeholder="শহর"
              className="form-control"
              value={form.city}
              onChange={handleChange}
            />
          </div>
          <div className="col-md-6">
            <select
              name="payment_method"
              className="form-select"
              value={form.payment_method}
              onChange={handleChange}
            >
              <option value="cod">Cash on Delivery (COD)</option>
              <option value="online">Online Payment</option>
            </select>
          </div>
        </div>

        {/* ---- Product Selector ---- */}
        <div className="card mb-4 shadow-sm" style={{ borderRadius: '12px' }}>
          <div className="card-body">
            <h5 className="card-title mb-3" style={{ color: mainColor }}>
              পণ্য যোগ করুন
            </h5>
            <div className="row g-3 align-items-end">
              <div className="col-md-5">
                <select
                  className="form-select"
                  value={selectedProductId}
                  onChange={e => setSelectedProductId(e.target.value)}
                >
                  <option value="">প্রোডাক্ট সিলেক্ট করুন</option>
                  {products.map(p => (
                    <option key={p.id} value={p.id}>
                      {p.title} - ৳{Number(p.price).toLocaleString('en-BD')}
                    </option>
                  ))}
                </select>
              </div>
              <div className="col-md-3">
                <input
                  type="number"
                  min="1"
                  className="form-control"
                  value={quantity}
                  onChange={e => setQuantity(Number(e.target.value))}
                />
              </div>
              <div className="col-md-4">
                <button className="btn btn-primary w-100" onClick={addItem} style={{ backgroundColor: mainColor, borderColor: mainColor }}>
                  যোগ করুন
                </button>
              </div>
            </div>
          </div>
        </div>

        {/* ---- Selected Items Table ---- */}
        {orderItems.length > 0 && (
          <div className="table-responsive mb-4">
            <table className="table table-hover" style={{ backgroundColor: '#fff', borderRadius: '8px' }}>
              <thead className="table-light">
                <tr>
                  <th>#</th>
                  <th>প্রোডাক্ট নাম</th>
                  <th>মূল্য (৳)</th>
                  <th>পরিমাণ</th>
                  <th>মোট (৳)</th>
                  <th>কার্য</th>
                </tr>
              </thead>
              <tbody>
                {orderItems.map((item, idx) => (
                  <tr key={item.id}>
                    <td>{idx + 1}</td>
                    <td>{item.title}</td>
                    <td>{Number(item.price).toLocaleString('en-BD')}</td>
                    <td>{item.qty}</td>
                    <td>{Number(item.price * item.qty).toLocaleString('en-BD')}</td>
                    <td>
                      <button className="btn btn-sm btn-outline-danger" onClick={() => removeItem(item.id)}>
                        মুছে ফেলুন
                      </button>
                    </td>
                  </tr>
                ))}
                <tr className="table-light">
                  <td colSpan="4" className="text-end fw-bold">মোট টোটাল:</td>
                  <td className="fw-bold">
                    {Number(orderItems.reduce((sum, i) => sum + i.price * i.qty, 0)).toLocaleString('en-BD')}
                  </td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
        )}

        {/* ---- Misc Settings ---- */}
        <div className="row g-3 mb-4">
          <div className="col-md-4">
            <input
              type="number"
              min="0"
              name="shipping_charge"
              placeholder="শিপিং চার্জ (৳)"
              className="form-control"
              value={form.shipping_charge}
              onChange={handleChange}
            />
          </div>
          <div className="col-md-4">
            <input
              type="number"
              min="0"
              name="discount"
              placeholder="ডিসকাউন্ট (৳)"
              className="form-control"
              value={form.discount}
              onChange={handleChange}
            />
          </div>
          <div className="col-md-4">
            <input
              type="text"
              name="coupon_code"
              placeholder="কুপন কোড"
              className="form-control"
              value={form.coupon_code}
              onChange={handleChange}
            />
          </div>
        </div>

        <div className="d-grid gap-2">
          <button className="btn btn-success btn-lg" onClick={submitOrder} style={{ backgroundColor: mainColor, borderColor: mainColor }}>
            অর্ডার তৈরি করুন
          </button>
        </div>
      </div>
    </MasterLayout>
  );
};

export default CreateOrder;
