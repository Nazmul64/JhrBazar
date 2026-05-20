import React, { useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, useLocation } from 'react-router-dom';
import axios from 'axios';
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
import BlogDetail from './pages/BlogDetail';
import CategoryProducts from './pages/CategoryProducts';
import AllProducts from './pages/AllProducts';
import Login from './pages/Login';
import Register from './pages/Register';
import Terms from './pages/Terms';
import About from './pages/About';
import PrivacyPolicy from './pages/PrivacyPolicy';
import SearchResults from './pages/SearchResults';
import PageView from './pages/PageView';
import OrderSuccess from './pages/OrderSuccess';
import OrderTracking from './pages/OrderTracking';
import UserDashboard from './pages/UserDashboard';
import { CartProvider } from './context/CartContext';
import { SettingsProvider } from './context/SettingsContext';
import { WishlistProvider } from './context/WishlistContext';
import { Toaster } from 'react-hot-toast';
import LiveChatWidget from './components/LiveChatWidget';

const RouteTracker = () => {
    const location = useLocation();

    useEffect(() => {
        let pageName = 'Home Page';
        const path = location.pathname;

        if (path === '/') pageName = 'Home Page';
        else if (path.startsWith('/product/')) pageName = 'Product Details';
        else if (path.startsWith('/shop/')) pageName = 'Shop Page';
        else if (path === '/checkout') pageName = 'Checkout Page';
        else if (path === '/cart') pageName = 'Cart Page';
        else if (path === '/wishlist') pageName = 'Wishlist Page';
        else if (path.startsWith('/category/')) pageName = 'Category Page';
        else if (path.startsWith('/subcategory/')) pageName = 'Subcategory Page';
        else if (path === '/search') pageName = 'Search Page';
        else if (path === '/customer/dashboard') pageName = 'Customer Dashboard';
        else pageName = path;

        axios.post('/api/track-visit', { page: pageName })
            .catch(err => console.log('Tracking error:', err));
    }, [location]);

    return null;
};

const MainApp = () => {
    return (
        <SettingsProvider>
            <WishlistProvider>
                <CartProvider>
                    <Router>
                        <RouteTracker />
                        <Toaster position="top-right" reverseOrder={false} />
                        <LiveChatWidget />
                        <Routes>
                            {/* Main Routes */}
                            <Route path="/" element={<Home />} />
                            <Route path="/products" element={<Products />} />
                            <Route path="/best-deal" element={<BestDeal />} />
                            <Route path="/contact" element={<Contact />} />
                            <Route path="/blogs" element={<Blogs />} />
                            <Route path="/blog/:slug" element={<BlogDetail />} />
                            <Route path="/customer/login" element={<Login />} />
                            <Route path="/customer/register" element={<Register />} />
                            <Route path="/about" element={<About />} />
                            <Route path="/terms" element={<PrivacyPolicy />} />
                            <Route path="/privacy-policy" element={<PrivacyPolicy />} />
                            <Route path="/category/:id" element={<CategoryProducts />} />
                            <Route path="/subcategory/:id" element={<CategoryProducts />} />
                            <Route path="/products-all/:type" element={<AllProducts />} />
                            <Route path="/shop/:id" element={<ShopDetails />} />
                            <Route path="/product/:slug" element={<ProductDetails />} />
                            <Route path="/search" element={<SearchResults />} />
                            <Route path="/page/:slug" element={<PageView />} />

                            {/* Shopping Routes */}
                            <Route path="/cart" element={<Cart />} />
                            <Route path="/checkout" element={<Checkout />} />
                            <Route path="/order-success" element={<OrderSuccess />} />
                            <Route path="/order-tracking" element={<OrderTracking />} />
                            <Route path="/customer/dashboard" element={<UserDashboard />} />
                            <Route path="/wishlist" element={<Wishlist />} />

                            {/* Catch-all Route */}
                            <Route path="*" element={<Home />} />
                        </Routes>
                    </Router>
                </CartProvider>
            </WishlistProvider>
        </SettingsProvider>
    );
};

export default MainApp;
