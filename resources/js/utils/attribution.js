/**
 * Attribution & Multi-Touch Campaign Tracking Engine
 * Captures UTMs, Click IDs (fbclid, gclid, ttclid), Referrers, and manages 90-day persistence.
 */
import axios from 'axios';

const COOKIE_NAME = 'jhr_marketing_attribution';
const SESSION_ID_KEY = 'jhr_visitor_session_id';

// Helper to get or create persistent session ID
export function getOrCreateSessionId() {
    let sid = localStorage.getItem(SESSION_ID_KEY);
    if (!sid) {
        sid = 'sess_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now().toString(36);
        localStorage.setItem(SESSION_ID_KEY, sid);
    }
    return sid;
}

// Cookie Helpers
function setCookie(name, value, days = 90) {
    let expires = '';
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + encodeURIComponent(JSON.stringify(value)) + expires + '; path=/; SameSite=Lax';
}

function getCookie(name) {
    const nameEQ = name + '=';
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) {
            try {
                return JSON.parse(decodeURIComponent(c.substring(nameEQ.length, c.length)));
            } catch (e) {
                return null;
            }
        }
    }
    return null;
}

/**
 * Detect marketing platform from UTMs, Click IDs, and Referrer
 */
export function detectPlatform(utmSource, utmMedium, clickIdType, referrerUrl) {
    const src = (utmSource || '').toLowerCase();
    const med = (utmMedium || '').toLowerCase();
    const ref = (referrerUrl || '').toLowerCase();
    const clk = (clickIdType || '').toLowerCase();

    // 1. Paid Ad Click IDs
    if (clk === 'fbclid' || (ref.includes('facebook.com') && ['cpc', 'paid', 'ads'].includes(med))) {
        return 'Facebook Ads';
    }
    if (clk === 'gclid' || (src.includes('google') && ['cpc', 'paid', 'ads'].includes(med))) {
        return 'Google Ads';
    }
    if (clk === 'ttclid' || (src.includes('tiktok') && ['cpc', 'paid', 'ads'].includes(med))) {
        return 'TikTok Ads';
    }

    // 2. Source / Medium matching
    if (src.includes('facebook') || src.includes('fb')) {
        return ['cpc', 'paid', 'ads'].includes(med) ? 'Facebook Ads' : 'Facebook Organic';
    }
    if (src.includes('instagram') || src.includes('ig')) {
        return 'Instagram Ads';
    }
    if (src.includes('tiktok')) {
        return 'TikTok Ads';
    }
    if (src.includes('google')) {
        return ['cpc', 'paid', 'ads'].includes(med) ? 'Google Ads' : 'Google Search';
    }
    if (src.includes('youtube') || ref.includes('youtube.com') || ref.includes('youtu.be')) {
        return 'YouTube';
    }

    // 3. Referrer parsing
    if (ref.includes('facebook.com') || ref.includes('fb.me') || ref.includes('m.facebook.com')) {
        return 'Facebook Organic';
    }
    if (ref.includes('instagram.com')) {
        return 'Instagram Ads';
    }
    if (ref.includes('tiktok.com')) {
        return 'TikTok Ads';
    }
    if (ref.includes('google.com') || ref.includes('google.com.bd')) {
        return 'Google Search';
    }

    if (ref && !ref.includes(window.location.hostname)) {
        return 'Referral';
    }

    return 'Direct Visit';
}

/**
 * Initializes and captures visitor attribution upon page entry
 */
export function initAttribution(cookieDays = 90) {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const utmSource = urlParams.get('utm_source');
        const utmMedium = urlParams.get('utm_medium');
        const utmCampaign = urlParams.get('utm_campaign');
        const utmTerm = urlParams.get('utm_term');
        const utmContent = urlParams.get('utm_content');

        // Check Click IDs
        let clickId = null;
        let clickIdType = null;
        if (urlParams.get('fbclid')) {
            clickId = urlParams.get('fbclid');
            clickIdType = 'fbclid';
        } else if (urlParams.get('gclid')) {
            clickId = urlParams.get('gclid');
            clickIdType = 'gclid';
        } else if (urlParams.get('ttclid')) {
            clickId = urlParams.get('ttclid');
            clickIdType = 'ttclid';
        }

        const referrerUrl = document.referrer || '';
        const sessionId = getOrCreateSessionId();
        const landingPage = window.location.href;

        // Existing cookie data
        const existingData = getCookie(COOKIE_NAME);

        // If new campaign params arrived, overwrite attribution (First-touch / Last-touch update)
        let attributionPayload;
        if (utmSource || clickId || !existingData) {
            const detectedPlatform = detectPlatform(utmSource, utmMedium, clickIdType, referrerUrl);
            attributionPayload = {
                session_id: sessionId,
                utm_source: utmSource || (existingData ? existingData.utm_source : null),
                utm_medium: utmMedium || (existingData ? existingData.utm_medium : null),
                utm_campaign: utmCampaign || (existingData ? existingData.utm_campaign : null),
                utm_term: utmTerm || (existingData ? existingData.utm_term : null),
                utm_content: utmContent || (existingData ? existingData.utm_content : null),
                click_id: clickId || (existingData ? existingData.click_id : null),
                click_id_type: clickIdType || (existingData ? existingData.click_id_type : null),
                referrer_url: referrerUrl || (existingData ? existingData.referrer_url : null),
                detected_platform: detectedPlatform,
                landing_page: landingPage,
                timestamp: Date.now(),
            };
            setCookie(COOKIE_NAME, attributionPayload, cookieDays);
            localStorage.setItem(COOKIE_NAME, JSON.stringify(attributionPayload));
        } else {
            attributionPayload = existingData;
        }

        // Sync with Backend in background
        axios.post('/api/track-visit', attributionPayload)
            .catch(() => { /* silent fail in background */ });

        return attributionPayload;
    } catch (err) {
        console.warn('Attribution init warning:', err);
        return null;
    }
}

/**
 * Retrieve current active attribution payload
 */
export function getAttributionData() {
    try {
        const fromCookie = getCookie(COOKIE_NAME);
        if (fromCookie) return fromCookie;
        const fromStorage = localStorage.getItem(COOKIE_NAME);
        if (fromStorage) return JSON.parse(fromStorage);
    } catch (e) {}
    return {
        session_id: getOrCreateSessionId(),
        detected_platform: 'Direct Visit',
    };
}
