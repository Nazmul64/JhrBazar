import React, { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';

const SettingsContext = createContext();

export const useSettings = () => useContext(SettingsContext);

export const SettingsProvider = ({ children }) => {
    const [settings, setSettings] = useState(null);
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchSettings = async () => {
            try {
                const res = await axios.get('/api/settings');
                if (res.data.success) {
                    const data = res.data.data;
                    setSettings(data);
                    setCategories(res.data.categories || []);

                    // Inject dynamic CSS variables to the root
                    const root = document.documentElement;
                    if (data.primary_color) {
                        root.style.setProperty('--main-color', data.primary_color);
                        root.style.setProperty('--button-color', data.button_color || data.primary_color);
                    }
                    if (data.header_color) root.style.setProperty('--header-color', data.header_color);
                    if (data.top_header_color) root.style.setProperty('--top-header-color', data.top_header_color);
                    if (data.footer_color) root.style.setProperty('--footer-color', data.footer_color);
                    if (data.footer_text_color) root.style.setProperty('--footer-text-color', data.footer_text_color);
                    if (data.button_hover_color) root.style.setProperty('--button-hover-color', data.button_hover_color || data.primary_color);
                    if (data.font_family) root.style.setProperty('--font-family', data.font_family);
                    if (data.font_size) root.style.setProperty('--font-size', data.font_size);

                    if (data.slider_height) root.style.setProperty('--slider-height', data.slider_height);
                    if (data.category_img_height) root.style.setProperty('--category-img-height', data.category_img_height);
                    if (data.category_img_width) root.style.setProperty('--category-img-width', data.category_img_width);

                    if (data.category_shape) {
                        let radius = '12px'; // default rounded
                        if (data.category_shape === 'circle') radius = '50%';
                        if (data.category_shape === 'square') radius = '0px';
                        root.style.setProperty('--category-border-radius', radius);
                    }
                    if (data.website_title) document.title = data.website_title;
                    if (data.favicon) {
                        let link = document.querySelector("link[rel~='icon']");
                        if (!link) {
                            link = document.createElement('link');
                            link.rel = 'icon';
                            document.getElementsByTagName('head')[0].appendChild(link);
                        }
                        link.href = data.favicon;
                    }
                }
            } catch (error) {
                console.error("Error fetching settings:", error);
            } finally {
                setLoading(false);
            }
        };
        fetchSettings();
    }, []);

    return (
        <SettingsContext.Provider value={{ settings, categories, loading }}>
            {/* Show a very minimal loader or just render children. Better to render children and let components handle loading if needed to prevent blank screens */}
            {children}
        </SettingsContext.Provider>
    );
};
