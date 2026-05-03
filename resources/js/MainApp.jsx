import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Home from './pages/Home';
import ShopDetails from './pages/ShopDetails';
import ProductDetails from './pages/ProductDetails';
import Cart from './pages/Cart';
import Checkout from './pages/Checkout';
import Wishlist from './pages/Wishlist';
import Products from './pages/Products';
import BestDeal from './pages/BestDeal';
import Contact from './pages/Contact';
import Blogs from './pages/Blogs';

const MainApp = () => {
    return (
        <Router>
            <Routes>
                {/* Main Routes */}
                <Route path="/" element={<Home />} />
                <Route path="/products" element={<Products />} />
                <Route path="/best-deal" element={<BestDeal />} />
                <Route path="/contact" element={<Contact />} />
                <Route path="/blogs" element={<Blogs />} />
                <Route path="/shop-details" element={<ShopDetails />} />
                <Route path="/product-details" element={<ProductDetails />} />

                {/* Shopping Routes */}
                <Route path="/cart" element={<Cart />} />
                <Route path="/checkout" element={<Checkout />} />
                <Route path="/wishlist" element={<Wishlist />} />

                {/* Catch-all Route */}
                <Route path="*" element={<Home />} />
            </Routes>
        </Router>
    );
};

export default MainApp;
