@extends('admin.master')

@section('title', 'Tracking Settings')

@section('content')
<style>
    .tracking-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .tracking-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
    }
    .master-switch-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #9333ea 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 28px;
        box-shadow: 0 10px 25px rgba(124, 58, 237, 0.25);
    }
    .pixel-header-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-right: 14px;
    }
    .bg-gtm { background: rgba(37, 99, 235, 0.12); color: #2563eb; }
    .bg-ga4 { background: rgba(245, 158, 11, 0.12); color: #d97706; }
    .bg-fb  { background: rgba(14, 116, 144, 0.12); color: #0284c7; }
    .bg-tt  { background: rgba(15, 23, 42, 0.12); color: #0f172a; }

    .switch-lg .form-check-input {
        width: 3.2rem;
        height: 1.7rem;
        cursor: pointer;
    }
    .switch-lg .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }
    .event-pill-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        transition: all 0.2s;
    }
    .event-pill-card:hover {
        background: #ffffff;
        border-color: #6366f1;
    }
    .badge-event {
        font-family: monospace;
        font-size: 13px;
        background: #e0e7ff;
        color: #4338ca;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
    }
    .pulse-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
        animation: pulseAnimation 2s infinite;
    }
    @keyframes pulseAnimation {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>

<div class="container-fluid py-3">

    {{-- Breadcrumb & Title --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1e293b;">
                <i class="bi bi-sliders text-primary me-2"></i>Tracking & Pixel Settings
            </h4>
            <p class="text-muted mb-0 small">
                Manage Google Tag Manager, GA4 eCommerce, Facebook Pixel, TikTok Pixel, DataLayer events & 90-day UTM attribution engine.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.tracking.attribution') }}" class="btn btn-outline-primary px-3 py-2 fw-semibold rounded-pill shadow-sm">
                <i class="bi bi-pie-chart-fill me-1"></i> View Attribution Reports
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.tracking.settings.update') }}" method="POST">
        @csrf

        {{-- 1. Master Switch Banner --}}
        <div class="master-switch-banner d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                    <i class="bi bi-shield-check fs-2"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h5 class="fw-bold text-white mb-0">Master Tracking Engine</h5>
                        <span class="badge {{ $setting->is_active ? 'bg-success text-white' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                            <span class="pulse-dot {{ $setting->is_active ? 'bg-white' : 'bg-dark' }}"></span>
                            {{ $setting->is_active ? 'SYSTEM ACTIVE' : 'INACTIVE' }}
                        </span>
                    </div>
                    <p class="mb-0 text-white text-opacity-80 small">
                        Globally enable or pause all tracking pixels, Google Tag Manager container, data layer event broadcasts & cookie attribution.
                    </p>
                </div>
            </div>
            <div class="form-check form-switch switch-lg mb-0">
                <input class="form-check-input" type="checkbox" name="is_active" id="masterSwitch" value="1" {{ $setting->is_active ? 'checked' : '' }}>
            </div>
        </div>

        {{-- 2. Pixels & GTM Grid (2 Columns) --}}
        <div class="row">
            {{-- Google Tag Manager --}}
            <div class="col-lg-6">
                <div class="tracking-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="pixel-header-icon bg-gtm">
                                <i class="bi bi-tags-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Google Tag Manager (GTM)</h6>
                                <small class="text-muted">Dynamic container script injection</small>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="gtm_enabled" id="gtm_enabled" value="1" {{ $setting->gtm_enabled ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">GTM Container ID</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-hash"></i></span>
                            <input type="text" class="form-control border-start-0 font-monospace" name="gtm_id" value="{{ old('gtm_id', $setting->gtm_id) }}" placeholder="e.g. GTM-T82G7W9F">
                        </div>
                        <div class="form-text text-muted small">Injected into head and body tags automatically.</div>
                    </div>
                </div>
            </div>

            {{-- Google Analytics 4 --}}
            <div class="col-lg-6">
                <div class="tracking-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="pixel-header-icon bg-ga4">
                                <i class="bi bi-bar-chart-line-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Google Analytics 4 (GA4)</h6>
                                <small class="text-muted">Direct gtag.js / measurement integration</small>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ga4_enabled" id="ga4_enabled" value="1" {{ $setting->ga4_enabled ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">GA4 Measurement ID</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-graph-up"></i></span>
                            <input type="text" class="form-control border-start-0 font-monospace" name="ga4_measurement_id" value="{{ old('ga4_measurement_id', $setting->ga4_measurement_id) }}" placeholder="e.g. G-ABC123XYZ">
                        </div>
                        <div class="form-text text-muted small">Tracks enhanced eCommerce conversions and audience data.</div>
                    </div>
                </div>
            </div>

            {{-- Facebook / Meta Pixel --}}
            <div class="col-lg-6">
                <div class="tracking-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="pixel-header-icon bg-fb">
                                <i class="bi bi-facebook"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Meta / Facebook Pixel</h6>
                                <small class="text-muted">Client-side & CAPI tracking</small>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="fb_pixel_enabled" id="fb_pixel_enabled" value="1" {{ $setting->fb_pixel_enabled ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Facebook Pixel ID</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-meta"></i></span>
                            <input type="text" class="form-control border-start-0 font-monospace" name="fb_pixel_id" value="{{ old('fb_pixel_id', $setting->fb_pixel_id) }}" placeholder="e.g. 123456789012345">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary small">Conversions API Access Token (Optional)</label>
                        <textarea class="form-control font-monospace small" name="fb_access_token" rows="2" placeholder="EAA... (Meta CAPI Server Token)">{{ old('fb_access_token', $setting->fb_access_token) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- TikTok Pixel --}}
            <div class="col-lg-6">
                <div class="tracking-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="pixel-header-icon bg-tt">
                                <i class="bi bi-tiktok"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">TikTok Pixel</h6>
                                <small class="text-muted">TikTok Ads conversion tracking</small>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="tiktok_pixel_enabled" id="tiktok_pixel_enabled" value="1" {{ $setting->tiktok_pixel_enabled ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">TikTok Pixel ID</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-film"></i></span>
                            <input type="text" class="form-control border-start-0 font-monospace" name="tiktok_pixel_id" value="{{ old('tiktok_pixel_id', $setting->tiktok_pixel_id) }}" placeholder="e.g. C9ABCDEF1234567">
                        </div>
                        <div class="form-text text-muted small">Fires TikTok CompletePayment, AddToCart, and ViewContent events.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. DataLayer eCommerce Events Configuration --}}
        <div class="tracking-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-layers-fill text-indigo me-2" style="color: #6366f1;"></i>DataLayer eCommerce Events (GA4 Schema)
                    </h5>
                    <p class="text-muted small mb-0">
                        Choose which standardized eCommerce events to automatically dispatch to <code>window.dataLayer</code> on user interactions.
                    </p>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="toggleAllEvents(true)">Enable All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="toggleAllEvents(false)">Disable All</button>
                </div>
            </div>

            @php
                $eventsList = [
                    'page_view' => [
                        'title' => 'Page View',
                        'desc' => 'Fires on every route navigation and initial load with page metadata.',
                        'badge' => 'page_view'
                    ],
                    'view_item_list' => [
                        'title' => 'View Item List (Category/Search)',
                        'desc' => 'Dispatches category and shop product listing impression arrays.',
                        'badge' => 'view_item_list'
                    ],
                    'view_item' => [
                        'title' => 'View Item Details',
                        'desc' => 'Triggers when user views a single product page with full SKU, price, & category data.',
                        'badge' => 'view_item'
                    ],
                    'add_to_cart' => [
                        'title' => 'Add to Cart',
                        'desc' => 'Pushes product details, price, quantity, and selected variant to dataLayer.',
                        'badge' => 'add_to_cart'
                    ],
                    'remove_from_cart' => [
                        'title' => 'Remove from Cart',
                        'desc' => 'Dispatched when an item is deleted or quantity reduced to 0.',
                        'badge' => 'remove_from_cart'
                    ],
                    'view_cart' => [
                        'title' => 'View Cart',
                        'desc' => 'Fired when the sliding cart drawer or cart page is opened with full cart items.',
                        'badge' => 'view_cart'
                    ],
                    'begin_checkout' => [
                        'title' => 'Begin Checkout',
                        'desc' => 'Triggers when user reaches checkout page with subtotal and item list.',
                        'badge' => 'begin_checkout'
                    ],
                    'add_shipping_info' => [
                        'title' => 'Add Shipping Info',
                        'desc' => 'Pushes selected shipping tier (Inside/Outside Dhaka) and delivery charge.',
                        'badge' => 'add_shipping_info'
                    ],
                    'purchase' => [
                        'title' => 'Purchase (With Customer Info)',
                        'desc' => 'Dispatches final order ID, total, tax, shipping, items, and customer info (Name, Phone, Email, Address, Area).',
                        'badge' => 'purchase'
                    ],
                    'lead' => [
                        'title' => 'Landing Page Lead / Form Submit',
                        'desc' => 'Fires when an order or lead is submitted from single-product landing pages.',
                        'badge' => 'lead'
                    ],
                ];
                $activeEvents = $setting->datalayer_events ?? \App\Models\TrackingSetting::defaultDataLayerEvents();
            @endphp

            <div class="row g-3">
                @foreach($eventsList as $eventKey => $eventInfo)
                    <div class="col-md-6 col-lg-6">
                        <div class="event-pill-card h-100 d-flex align-items-start justify-content-between">
                            <div class="me-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge-event">{{ $eventInfo['badge'] }}</span>
                                    <span class="fw-semibold text-dark small">{{ $eventInfo['title'] }}</span>
                                </div>
                                <div class="text-muted" style="font-size: 12px;">{{ $eventInfo['desc'] }}</div>
                            </div>
                            <div class="form-check form-switch pt-1">
                                <input class="form-check-input dl-event-toggle" type="checkbox" name="datalayer_events[{{ $eventKey }}]" value="1" {{ !empty($activeEvents[$eventKey]) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 4. Attribution & Cookie Lifetime Configuration --}}
        <div class="tracking-card p-4">
            <h5 class="fw-bold text-dark mb-1">
                <i class="bi bi-clock-history text-success me-2"></i>Multi-Touch Attribution & Cookie Settings
            </h5>
            <p class="text-muted small mb-3">
                Configure how long traffic attribution cookies persist and which channels the attribution detector captures.
            </p>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">Attribution Cookie Lifetime (Days)</label>
                    <div class="input-group">
                        <input type="number" name="cookie_lifetime_days" class="form-control" value="{{ old('cookie_lifetime_days', $setting->cookie_lifetime_days ?? 90) }}" min="1" max="365">
                        <span class="input-group-text bg-light text-muted">Days</span>
                    </div>
                    <div class="form-text text-muted small">Standard eCommerce attribution window is 90 days.</div>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold text-secondary small mb-2">Enabled Attribution Trackers</label>
                    <div class="d-flex flex-wrap gap-3 pt-1">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="track_utm" id="track_utm" value="1" {{ $setting->track_utm ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="track_utm">UTM Parameters (utm_source, etc.)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="track_click_ids" id="track_click_ids" value="1" {{ $setting->track_click_ids ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="track_click_ids">Ad Click IDs (fbclid, gclid, ttclid)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="track_referrer" id="track_referrer" value="1" {{ $setting->track_referrer ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="track_referrer">Referrer Detection</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="track_direct" id="track_direct" value="1" {{ $setting->track_direct ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="track_direct">Direct Visits</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Custom Header & Footer Scripts --}}
        <div class="tracking-card p-4">
            <h5 class="fw-bold text-dark mb-1">
                <i class="bi bi-code-slash text-warning me-2"></i>Custom Head & Body Tracking Scripts
            </h5>
            <p class="text-muted small mb-3">
                Inject custom JavaScript or third-party verification tags directly.
            </p>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary small">Custom Head Code (&lt;head&gt;)</label>
                    <textarea name="custom_head_script" rows="4" class="form-control font-monospace small" placeholder="<!-- Paste custom verification meta tags or header JS here -->">{{ old('custom_head_script', $setting->custom_head_script) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary small">Custom Body Code (&lt;body&gt;)</label>
                    <textarea name="custom_body_script" rows="4" class="form-control font-monospace small" placeholder="<!-- Paste custom body tracking noscript or chat widgets here -->">{{ old('custom_body_script', $setting->custom_body_script) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Save Button Bar --}}
        <div class="d-flex align-items-center justify-content-end gap-3 mb-5">
            <button type="reset" class="btn btn-light px-4 py-2 fw-semibold rounded-pill">Reset</button>
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-pill shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
                <i class="bi bi-check2-circle me-1"></i> Save Tracking Settings
            </button>
        </div>

    </form>

</div>

<script>
    function toggleAllEvents(state) {
        document.querySelectorAll('.dl-event-toggle').forEach(el => {
            el.checked = state;
        });
    }
</script>
@endsection
