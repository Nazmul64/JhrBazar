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
            const res = await axios.get('/api/wishlist', {
                headers: { 'X-Session-Id': sessionId }
            });
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
            const res = await axios.post('/api/wishlist/toggle', {
                product_id: product.id,
                product_type: product.product_type || 'admin'
            }, {
                headers: { 'X-Session-Id': sessionId }
            });

            if (res.data.success) {
                fetchWishlist();
                if (res.data.action === 'added') {
                    toast.success('Added to wishlist!');
                } else {
                    toast.success('Removed from wishlist!');
                }
                return res.data.action;
            }
        } catch (error) {
            console.error("Error toggling wishlist:", error);
            toast.error('Something went wrong!');
        }
        return null;
    };

    const isInWishlist = (productId, productType = 'admin') => {
        return wishlist.some(item => item.id === productId && item.product_type === productType);
    };

    const syncWishlist = async () => {
        try {
            await axios.post('/api/wishlist/sync', {}, {
                headers: { 'X-Session-Id': sessionId }
            });
            fetchWishlist();
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
