import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom/client';
import MainApp from './MainApp';
import '../css/frontend.css';

/**
 * JhrBazar Professional Entry Point
 * Renamed router import to MainApp to avoid Windows filename conflicts.
 */

import { HelmetProvider } from 'react-helmet-async';

if (document.getElementById('react-app')) {
    const root = ReactDOM.createRoot(document.getElementById("react-app"));
    root.render(
        <React.StrictMode>
            <HelmetProvider>
                <MainApp />
            </HelmetProvider>
        </React.StrictMode>
    );
}
