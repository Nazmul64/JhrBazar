import React, { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-hot-toast';

const WishlistContext = createContext();

const generateSessionId = () => {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
};

export const WishlistProvider = ({ children }) => {
    const [wishlist, setWishlist] = useState([]);
    const [loading, setLoading] = useState(true);
    const [sessionId, setSessionId] = useState(localStorage.getItem('wishlist_session_id'));

    useEffect(() => {
        if (!sessionId) {
            const newId = generateSessionId();
            localStorage.setItem('wishlist_session_id', newId);
            setSessionId(newId);
        }
    }, []);

    const fetchWishlist = async () => {
        try {
            const token = localStorage.getItem('auth_token');
            const headers = { 'X-Session-Id': sessionId };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const res = await axios.get('/api/wishlist', { headers });
            if (res.data.success) {
                setWishlist(res.data.data);
            }
        } catch (error) {
            console.error("Error fetching wishlist:", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        if (sessionId) {
            fetchWishlist();
        }
    }, [sessionId]);

    const toggleWishlist = async (product) => {
        try {
            const token = localStorage.getItem('auth_token');
            const headers = { 'X-Session-Id': sessionId };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const res = await axios.post('/api/wishlist/toggle', {
                product_id: product.id,
                product_type: product.product_type || 'admin'
            }, { headers });

            if (res.data.success) {
                fetchWishlist();
                if (res.data.action === 'added') {
                    toast.success('উইশলিস্টে যোগ হয়েছে!');
                } else {
                    toast.success('উইশলিস্ট থেকে সরানো হয়েছে!');
                }
                return res.data.action;
            }
        } catch (error) {
            console.error("Error toggling wishlist:", error);
            toast.error('কিছু একটা সমস্যা হয়েছে!');
        }
        return null;
    };

    const isInWishlist = (productId, productType = 'admin') => {
        return wishlist.some(item => item.id === productId && item.product_type === productType);
    };

    const syncWishlist = async () => {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) return; // can only sync if logged in
            const headers = {
                'X-Session-Id': sessionId,
                'Authorization': `Bearer ${token}`
            };
            await axios.post('/api/wishlist/sync', {}, { headers });
            fetchWishlist(); // refresh after sync
        } catch (error) {
            console.error("Error syncing wishlist:", error);
        }
    };

    return (
        <WishlistContext.Provider value={{ wishlist, loading, toggleWishlist, isInWishlist, syncWishlist }}>
            {children}
        </WishlistContext.Provider>
    );
};

export const useWishlist = () => useContext(WishlistContext);
