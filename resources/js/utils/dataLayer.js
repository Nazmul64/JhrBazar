/**
 * Standard GA4 eCommerce DataLayer & Pixel Dispatcher Engine
 * Adheres strictly to GA4 eCommerce Specification without breaking schema nesting.
 * Includes complete customer details on `purchase` events.
 */
import axios from 'axios';
import { initAttribution, getAttributionData } from './attribution';

let trackingConfig = null;
let isInitialized = false;

// Ensure window.dataLayer exists
window.dataLayer = window.dataLayer || [];

/**
 * Fetch tracking configuration from backend and dynamically inject tags
 */
export async function initTrackingSystem() {
    if (isInitialized) return trackingConfig;

    try {
        const response = await axios.get('/api/tracking-config');
        trackingConfig = response.data;
        isInitialized = true;

        if (!trackingConfig || !trackingConfig.is_active) {
            return trackingConfig;
        }

        // 1. Initialize Visitor Attribution Engine
        initAttribution(trackingConfig.cookie_lifetime_days || 90);

        // 2. Inject Google Tag Manager (GTM)
        if (trackingConfig.gtm_enabled && trackingConfig.gtm_id && !document.getElementById('gtm-script-tag')) {
            const gtmId = trackingConfig.gtm_id.trim();
            const script = document.createElement('script');
            script.id = 'gtm-script-tag';
            script.async = true;
            script.src = `https://www.googletagmanager.com/gtm.js?id=${gtmId}`;
            document.head.appendChild(script);

            window.dataLayer.push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js',
            });
        }

        // 3. Inject Google Analytics 4 (GA4 direct gtag)
        if (trackingConfig.ga4_enabled && trackingConfig.ga4_measurement_id && !document.getElementById('ga4-script-tag')) {
            const gaId = trackingConfig.ga4_measurement_id.trim();
            const script = document.createElement('script');
            script.id = 'ga4-script-tag';
            script.async = true;
            script.src = `https://www.googletagmanager.com/gtag/js?id=${gaId}`;
            document.head.appendChild(script);

            window.dataLayer.push({
                event: 'config',
                target: gaId,
            });
        }

        // 4. Inject Meta / Facebook Pixel
        if (trackingConfig.fb_pixel_enabled && trackingConfig.fb_pixel_id && !window.fbq) {
            const pixelId = trackingConfig.fb_pixel_id.trim();
            (function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s);
            })(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

            window.fbq('init', pixelId);
            window.fbq('track', 'PageView');
        }

        // 5. Inject TikTok Pixel
        if (trackingConfig.tiktok_pixel_enabled && trackingConfig.tiktok_pixel_id && !window.ttq) {
            const ttPixelId = trackingConfig.tiktok_pixel_id.trim();
            (function (w, d, t) {
                w.TiktokAnalyticsObject = t;
                var ttq = (w[t] = w[t] || []);
                ttq.methods = [
                    'page',
                    'track',
                    'identify',
                    'instances',
                    'debug',
                    'on',
                    'off',
                    'once',
                    'ready',
                    'alias',
                    'group',
                    'enableCookie',
                    'disableCookie',
                ];
                ttq.setAndDefer = function (t, e) {
                    t[e] = function () {
                        t.push([e].concat(Array.prototype.slice.call(arguments, 0)));
                    };
                };
                for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
                ttq.instance = function (t) {
                    for (var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++)
                        ttq.setAndDefer(e, ttq.methods[n]);
                    return e;
                };
                ttq.load = function (e, n) {
                    var i = 'https://analytics.tiktok.com/i18n/pixel/events.js';
                    (ttq._i = ttq._i || {}),
                        (ttq._i[e] = []),
                        (ttq._i[e]._u = i),
                        (ttq._t = ttq._t || {}),
                        (ttq._t[e] = +new Date()),
                        (ttq._o = ttq._o || {}),
                        (ttq._o[e] = n || {});
                    var o = document.createElement('script');
                    (o.type = 'text/javascript'), (o.async = !0), (o.src = i + '?sdkid=' + e + '&lib=' + t);
                    var a = document.getElementsByTagName('script')[0];
                    a.parentNode.insertBefore(o, a);
                };
                ttq.load(ttPixelId);
                ttq.page();
            })(window, document, 'ttq');
        }

        return trackingConfig;
    } catch (err) {
        console.warn('Failed to load tracking settings:', err);
        return null;
    }
}

/**
 * Check if a specific DataLayer event is enabled in admin settings
 */
function isEventAllowed(eventName) {
    if (!trackingConfig) return true; // default allow
    if (!trackingConfig.is_active) return false;
    if (!trackingConfig.datalayer_events) return true;
    return Boolean(trackingConfig.datalayer_events[eventName] !== false);
}

/**
 * Helper to normalize item object to GA4 schema
 */
export function formatItem(product, index = 0) {
    if (!product) return {};
    const price = parseFloat(product.price || product.sale_price || product.regular_price || 0);
    return {
        item_id: String(product.id || product.product_id || product.sku || ''),
        item_name: product.name || product.title || product.product_name || 'Product',
        price: price,
        quantity: parseInt(product.quantity || product.qty || 1, 10),
        item_category: product.category_name || product.category?.name || 'General',
        item_brand: product.brand_name || product.brand?.name || 'JhrBazar',
        item_variant: product.variant || product.selected_color || product.selected_size || undefined,
        index: index,
    };
}

/**
 * 1. page_view Event
 */
export function trackPageView(pageTitle, pagePath) {
    if (!isEventAllowed('page_view')) return;

    window.dataLayer.push({
        event: 'page_view',
        page_title: pageTitle || document.title,
        page_location: window.location.href,
        page_path: pagePath || window.location.pathname,
    });
}

/**
 * 2. view_item_list Event (Category, Shop, or Homepage Sections)
 */
export function trackViewItemList(items = [], itemListName = 'Product List') {
    if (!isEventAllowed('view_item_list') || !items.length) return;

    const formattedItems = items.map((item, idx) => formatItem(item, idx));

    window.dataLayer.push({ ecommerce: null }); // Clear previous ecommerce object
    window.dataLayer.push({
        event: 'view_item_list',
        ecommerce: {
            item_list_name: itemListName,
            items: formattedItems,
        },
    });
}

/**
 * 3. view_item Event (Product Details Page)
 */
export function trackViewItem(product) {
    if (!isEventAllowed('view_item') || !product) return;

    const item = formatItem(product, 0);
    const value = item.price * (item.quantity || 1);

    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
        event: 'view_item',
        ecommerce: {
            currency: 'BDT',
            value: value,
            items: [item],
        },
    });

    // Meta Pixel & TikTok
    if (window.fbq) {
        window.fbq('track', 'ViewContent', {
            content_name: item.item_name,
            content_ids: [item.item_id],
            content_type: 'product',
            value: value,
            currency: 'BDT',
        });
    }
    if (window.ttq) {
        window.ttq.track('ViewContent', {
            content_name: item.item_name,
            content_id: item.item_id,
            content_type: 'product',
            value: value,
            currency: 'BDT',
        });
    }
}

/**
 * 4. add_to_cart Event
 */
export function trackAddToCart(product, quantity = 1) {
    if (!isEventAllowed('add_to_cart') || !product) return;

    const item = formatItem({ ...product, quantity }, 0);
    const value = item.price * quantity;

    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
        event: 'add_to_cart',
        ecommerce: {
            currency: 'BDT',
            value: value,
            items: [item],
        },
    });

    if (window.fbq) {
        window.fbq('track', 'AddToCart', {
            content_name: item.item_name,
            content_ids: [item.item_id],
            content_type: 'product',
            value: value,
            currency: 'BDT',
        });
    }
    if (window.ttq) {
        window.ttq.track('AddToCart', {
            content_name: item.item_name,
            content_id: item.item_id,
            value: value,
            currency: 'BDT',
        });
    }
}

/**
 * 5. remove_from_cart Event
 */
export function trackRemoveFromCart(product, quantity = 1) {
    if (!isEventAllowed('remove_from_cart') || !product) return;

    const item = formatItem({ ...product, quantity }, 0);
    const value = item.price * quantity;

    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
        event: 'remove_from_cart',
        ecommerce: {
            currency: 'BDT',
            value: value,
            items: [item],
        },
    });
}

/**
 * 6. view_cart Event
 */
export function trackViewCart(cartItems = [], totalValue = 0) {
    if (!isEventAllowed('view_cart') || !cartItems.length) return;

    const formattedItems = cartItems.map((item, idx) => formatItem(item, idx));

    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
        event: 'view_cart',
        ecommerce: {
            currency: 'BDT',
            value: parseFloat(totalValue || 0),
            items: formattedItems,
        },
    });
}

/**
 * 7. begin_checkout Event
 */
export function trackBeginCheckout(cartItems = [], totalValue = 0) {
    if (!isEventAllowed('begin_checkout') || !cartItems.length) return;

    const formattedItems = cartItems.map((item, idx) => formatItem(item, idx));
    const value = parseFloat(totalValue || 0);

    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
        event: 'begin_checkout',
        ecommerce: {
            currency: 'BDT',
            value: value,
            items: formattedItems,
        },
    });

    if (window.fbq) {
        window.fbq('track', 'InitiateCheckout', {
            content_ids: formattedItems.map(i => i.item_id),
            num_items: formattedItems.length,
            value: value,
            currency: 'BDT',
        });
    }
    if (window.ttq) {
        window.ttq.track('InitiateCheckout', {
            value: value,
            currency: 'BDT',
            num_items: formattedItems.length,
        });
    }
}

/**
 * 8. add_shipping_info Event
 */
export function trackAddShippingInfo(shippingTier, shippingCost = 0, cartItems = [], totalValue = 0) {
    if (!isEventAllowed('add_shipping_info')) return;

    const formattedItems = cartItems.map((item, idx) => formatItem(item, idx));

    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
        event: 'add_shipping_info',
        ecommerce: {
            currency: 'BDT',
            value: parseFloat(totalValue || 0),
            shipping_tier: shippingTier || 'Standard Delivery',
            shipping: parseFloat(shippingCost || 0),
            items: formattedItems,
        },
    });
}

/**
 * 9. purchase Event (Includes customer information without breaking schema)
 */
export function trackPurchase(orderData, customerData = {}, cartItems = []) {
    if (!isEventAllowed('purchase') || !orderData) return;

    const transactionId = String(orderData.id || orderData.order_id || orderData.invoice_id || Date.now());
    const totalRevenue = parseFloat(orderData.total || orderData.total_amount || orderData.grand_total || orderData.price || 0);
    const shipping = parseFloat(orderData.shipping_charge || orderData.delivery_charge || 0);
    const tax = parseFloat(orderData.tax || orderData.vat || 0);
    const coupon = orderData.coupon_code || orderData.promocode || undefined;

    const items = (cartItems.length ? cartItems : (orderData.items || [orderData])).map((item, idx) => formatItem(item, idx));
    const attribution = getAttributionData();

    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
        event: 'purchase',
        ecommerce: {
            transaction_id: transactionId,
            value: totalRevenue,
            tax: tax,
            shipping: shipping,
            currency: 'BDT',
            coupon: coupon,
            items: items,
        },
        // Customer Information Payload for Enhanced Conversions & Audience CRM
        customer_info: {
            customer_name: customerData.name || customerData.customer_name || '',
            customer_phone: customerData.phone || customerData.mobile || customerData.phone_number || '',
            customer_email: customerData.email || '',
            delivery_address: customerData.address || customerData.delivery_address || '',
            district: customerData.district || customerData.city || customerData.area || '',
            payment_method: orderData.payment_method || 'Cash on Delivery',
        },
        // Attribution info
        attribution: {
            source: attribution.utm_source || 'direct',
            medium: attribution.utm_medium || 'none',
            campaign: attribution.utm_campaign || 'none',
            detected_platform: attribution.detected_platform || 'Direct Visit',
            session_id: attribution.session_id,
        },
    });

    // Meta Pixel Purchase
    if (window.fbq) {
        window.fbq('track', 'Purchase', {
            content_type: 'product',
            content_ids: items.map(i => i.item_id),
            value: totalRevenue,
            currency: 'BDT',
            num_items: items.length,
        });
    }

    // TikTok Pixel CompletePayment
    if (window.ttq) {
        window.ttq.track('CompletePayment', {
            content_id: transactionId,
            value: totalRevenue,
            currency: 'BDT',
            quantity: items.reduce((sum, i) => sum + (i.quantity || 1), 0),
        });
    }
}

/**
 * 10. lead Event (Landing Page form submission)
 */
export function trackLead(formName, leadData = {}) {
    if (!isEventAllowed('lead')) return;

    window.dataLayer.push({
        event: 'lead',
        form_name: formName || 'Landing Page Order Form',
        lead_data: {
            name: leadData.name || '',
            phone: leadData.phone || '',
            address: leadData.address || '',
        },
    });

    if (window.fbq) {
        window.fbq('track', 'Lead', {
            content_name: formName,
        });
    }
    if (window.ttq) {
        window.ttq.track('SubmitForm', {
            content_name: formName,
        });
    }
}
