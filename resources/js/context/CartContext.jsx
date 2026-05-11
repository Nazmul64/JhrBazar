import React, { createContext, useContext, useState, useEffect } from 'react';
import { toast } from 'react-hot-toast';

const CartContext = createContext(null);

export const CartProvider = ({ children }) => {
    const [cartItems, setCartItems] = useState(() => {
        try {
            const saved = localStorage.getItem('jhrbazar_cart');
            return saved ? JSON.parse(saved) : [];
        } catch {
            return [];
        }
    });

    // Save to localStorage whenever cart changes
    useEffect(() => {
        localStorage.setItem('jhrbazar_cart', JSON.stringify(cartItems));
    }, [cartItems]);

    const addToCart = (product, quantity = 1, color = null, size = null) => {
        setCartItems(prev => {
            const existing = prev.find(item => item.uid === product.uid && item.color === color && item.size === size);
            if (existing) {
                toast.success('Quantity updated in cart!');
                return prev.map(item =>
                    (item.uid === product.uid && item.color === color && item.size === size)
                        ? { ...item, qty: item.qty + quantity }
                        : item
                );
            }
            toast.success('Added to cart!');
            return [...prev, {
                uid: product.uid,
                id: product.id,
                product_type: product.product_type,
                seller_id: product.seller_id || 0,
                title: product.title,
                price: product.price,
                image: product.image,
                qty: quantity,
                color: color,
                size: size,
                cash_on_delivery: product.cash_on_delivery,
                online_payment: product.online_payment,
            }];
        });
    };

    const removeFromCart = (uid) => {
        setCartItems(prev => prev.filter(item => item.uid !== uid));
        toast.success('Removed from cart!');
    };

    const updateQuantity = (uid, delta) => {
        setCartItems(prev =>
            prev.map(item =>
                item.uid === uid
                    ? { ...item, qty: Math.max(1, item.qty + delta) }
                    : item
            )
        );
    };

    const clearCart = () => setCartItems([]);

    const cartCount = cartItems.reduce((acc, item) => acc + item.qty, 0);
    const cartTotal = cartItems.reduce((acc, item) => acc + item.price * item.qty, 0);

    return (
        <CartContext.Provider value={{
            cartItems,
            addToCart,
            removeFromCart,
            updateQuantity,
            clearCart,
            cartCount,
            cartTotal,
        }}>
            {children}
        </CartContext.Provider>
    );
};

export const useCart = () => {
    const ctx = useContext(CartContext);
    if (!ctx) throw new Error('useCart must be used within CartProvider');
    return ctx;
};

export default CartContext;
