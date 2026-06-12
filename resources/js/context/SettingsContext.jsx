import React, { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';

const SettingsContext = createContext();

export const useSettings = () => useContext(SettingsContext);

export const SettingsProvider = ({ children }) => {
    const [settings, setSettings] = useState(window.initialSettings || null);
    const [categories, setCategories] = useState(window.initialHomeData?.data?.categories || []);
    const [loading, setLoading] = useState(!!(window.initialSettings?.loader_status && !window.initialHomeData));
    const [initialFetch, setInitialFetch] = useState(true);
    const [homeData, setHomeData] = useState(window.initialHomeData?.data || null);

    useEffect(() => {
        const fetchSettings = async () => {
            try {
                const res = await axios.get('/api/settings');
                if (res.data.success) {
                    const data = res.data.data;
                    setSettings(data);
                    setCategories(res.data.categories || []);

                    const root = document.documentElement;
                    if (data.primary_color) {
                        root.style.setProperty('--primary-color', data.primary_color);
                        root.style.setProperty('--main-color', data.primary_color);
                    }
                    // ... other property settings ...
                    if (data.button_color) root.style.setProperty('--button-color', data.button_color);
                    if (data.button_hover_color) root.style.setProperty('--primary-hover', data.button_hover_color);
                    if (data.header_color) root.style.setProperty('--header-bg', data.header_color);
                    if (data.top_header_color) root.style.setProperty('--top-header-bg', data.top_header_color);
                    if (data.footer_color) root.style.setProperty('--footer-bg', data.footer_color);
                    if (data.footer_text_color) root.style.setProperty('--footer-text-color', data.footer_text_color);
                    if (data.font_family) root.style.setProperty('--font-family', data.font_family);
                    if (data.font_size) root.style.setProperty('--base-font-size', data.font_size);
                    if (data.product_title_size_desktop) root.style.setProperty('--product-title-desktop', data.product_title_size_desktop);
                    if (data.product_title_size_mobile) root.style.setProperty('--product-title-mobile', data.product_title_size_mobile);
                    if (data.product_price_size) root.style.setProperty('--product-price-size', data.product_price_size);
                    if (data.product_old_price_size) root.style.setProperty('--product-old-price-size', data.product_old_price_size);
                    if (data.slider_height) root.style.setProperty('--slider-height', data.slider_height);
                    if (data.category_img_height) root.style.setProperty('--category-img-height', data.category_img_height);
                    if (data.category_img_width) root.style.setProperty('--category-img-width', data.category_img_width);
                    if (data.category_shape) {
                        let radius = '12px';
                        if (data.category_shape === 'circle') radius = '50%';
                        if (data.category_shape === 'square') radius = '0px';
                        root.style.setProperty('--category-border-radius', radius);
                    }
                    if (data.website_title || data.website_name) {
                        document.title = data.website_title || data.website_name;
                    }
                    if (data.favicon) {
                        let link = document.querySelector("link[rel~='icon']");
                        if (!link) {
                            link = document.createElement('link');
                            link.rel = 'icon';
                            document.getElementsByTagName('head')[0].appendChild(link);
                        }
                        link.href = data.favicon + '?v=' + new Date().getTime();
                    }
                }
            } catch (error) {
                console.error("Error fetching settings:", error);
            } finally {
                setInitialFetch(false);
                setLoading(false); // Hide loader immediately when fetch is done
            }
        };
        fetchSettings();
    }, []);

    return (
        <SettingsContext.Provider value={{ settings, categories, loading, homeData, setHomeData }}>
            <style>{`
                ${settings?.font_family && !['Arial', 'Times New Roman', 'Georgia', 'Verdana', 'SolaimanLipi'].some(f => settings.font_family.includes(f)) 
                    ? `@import url('https://fonts.googleapis.com/css2?family=${settings.font_family.split(',')[0].replace(/['"]/g, '').trim().replace(/ /g, '+')}:wght@300;400;500;600;700&display=swap');` 
                    : "@import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap');"
                }
                
                :root {
                    --main-font: ${settings?.font_family ? settings.font_family : "'Inter', 'Outfit', 'Poppins', 'Hind Siliguri', sans-serif"};
                    --loader-color: ${settings?.primary_color || '#c21a4e'};
                }

                *:not(i):not(.fas):not(.fab):not(.far):not(.fa):not(.bi) {
                    font-family: var(--main-font) !important;
                }

                .custom-loader-wrapper {
                    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                    background: #fff; display: flex; justify-content: center; align-items: center;
                    z-index: 99999999;
                }

                .custom-loader {
                    width: 55px; height: 55px;
                    border: 5px solid #f3f3f3;
                    border-top: 5px solid var(--loader-color);
                    border-radius: 50%;
                    animation: spin 0.8s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `}</style>

            {loading && (
                <div className="custom-loader-wrapper">
                    <div className="custom-loader"></div>
                </div>
            )}
            
            {children}
        </SettingsContext.Provider>
    );
};
