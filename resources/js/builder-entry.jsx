/**
 * Admin Landing Page Builder Entry Point
 * Mounted inside the admin panel (admin.master layout).
 * Gets the page ID from window.__BUILDER_PAGE_ID__ injected by the blade view.
 */
import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import AdminLandingPageBuilder from './pages/AdminLandingPageBuilder';
import '../css/frontend.css';

const el = document.getElementById('landing-builder-root');

if (el) {
    const root = ReactDOM.createRoot(el);
    root.render(
        <React.StrictMode>
            <Toaster position="top-right" reverseOrder={false} />
            <AdminLandingPageBuilder
                pageId={window.__BUILDER_PAGE_ID__}
                pageSlug={window.__BUILDER_PAGE_SLUG__}
                pageTitle={window.__BUILDER_PAGE_TITLE__}
            />
        </React.StrictMode>
    );
}
