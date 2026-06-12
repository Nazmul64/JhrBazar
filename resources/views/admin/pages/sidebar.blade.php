<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

:root {
    --sb-width: 272px;
    --sb-bg: #0a0d14;
    --sb-surface: #111520;
    --sb-surface-hover: #161b29;
    --sb-border: rgba(255,255,255,0.07);
    --sb-text: #8892a4;
    --sb-text-hover: #dde3ef;
    --sb-section-color: #3d4760;
    --sb-accent: #6366f1;
    --sb-accent-glow: rgba(99,102,241,0.15);
    --sb-accent-border: rgba(99,102,241,0.25);
    --sb-danger: #ef4444;
    --brand-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
    --icon-size: 32px;
    --item-radius: 8px;
    --font: 'Plus Jakarta Sans', sans-serif;
}

[data-theme="light"] {
    --sb-bg: #f8f9fc;
    --sb-surface: #ffffff;
    --sb-surface-hover: #f1f3f9;
    --sb-border: rgba(0,0,0,0.07);
    --sb-text: #64748b;
    --sb-text-hover: #1e293b;
    --sb-section-color: #94a3b8;
    --sb-accent-glow: rgba(99,102,241,0.08);
}

#sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.65);
    backdrop-filter: blur(4px);
    z-index: 1029;
}
#sidebar-overlay.show { display: block; }

#sidebar {
    width: var(--sb-width) !important;
    background: var(--sb-bg) !important;
    border-right: 1px solid var(--sb-border) !important;
    font-family: var(--font) !important;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 1030;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ── Scrollable inner ── */
.sb-scroll {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 8px 12px 16px;
}
.sb-scroll::-webkit-scrollbar { width: 3px; }
.sb-scroll::-webkit-scrollbar-track { background: transparent; }
.sb-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 99px; }
.sb-scroll::-webkit-scrollbar-thumb:hover { background: var(--sb-accent); }

/* ── Brand ── */
.sb-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 16px 16px;
    text-decoration: none;
    border-bottom: 1px solid var(--sb-border);
    flex-shrink: 0;
}
.sb-logo {
    width: 40px; height: 40px;
    background: var(--brand-gradient);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: white;
    flex-shrink: 0;
}
.sb-brand-text { flex: 1; min-width: 0; }
.sb-brand-name {
    display: block;
    color: var(--sb-text-hover);
    font-weight: 700;
    font-size: 15px;
    letter-spacing: -0.3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sb-brand-status {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 3px;
}
.sb-status-dot {
    width: 5px; height: 5px;
    background: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 6px #10b981;
    animation: pulse-dot 2.5s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%,100% { opacity:1; transform:scale(1); }
    50% { opacity:0.4; transform:scale(1.3); }
}
.sb-brand-tag {
    font-size: 10px;
    font-weight: 600;
    color: var(--sb-section-color);
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

/* ── Section labels ── */
.sb-section {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 20px 8px 6px;
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--sb-section-color);
}
.sb-section::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--sb-border);
}

/* ── Nav items ── */
.sb-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    margin-bottom: 2px;
    border-radius: var(--item-radius);
    color: var(--sb-text);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.18s ease;
    border: 1px solid transparent;
    cursor: pointer;
    position: relative;
}
.sb-item:hover {
    background: var(--sb-surface-hover);
    color: var(--sb-text-hover);
    border-color: var(--sb-border);
    text-decoration: none;
}
.sb-item.active {
    background: var(--sb-accent-glow);
    color: var(--sb-accent);
    border-color: var(--sb-accent-border);
}
.sb-item.danger {
    color: #f87171;
}
.sb-item.danger:hover {
    background: rgba(239,68,68,0.08);
    border-color: rgba(239,68,68,0.15);
    color: var(--sb-danger);
}

/* ── Icons ── */
.sb-icon {
    width: var(--icon-size);
    height: var(--icon-size);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    background: rgba(255,255,255,0.04);
    font-size: 14px;
    color: inherit;
    flex-shrink: 0;
    transition: all 0.18s;
}
.sb-item:hover .sb-icon {
    background: rgba(99,102,241,0.1);
    color: var(--sb-accent);
}
.sb-item.active .sb-icon {
    background: var(--sb-accent);
    color: white;
}
.sb-item.danger .sb-icon {
    background: rgba(239,68,68,0.1);
    color: #f87171;
}
.sb-item.danger:hover .sb-icon {
    background: rgba(239,68,68,0.15);
    color: var(--sb-danger);
}

/* Special gradient icon for Orders Hub */
.sb-icon.gradient {
    background: var(--brand-gradient) !important;
    color: white !important;
}

/* ── Arrow & collapse ── */
.sb-arrow {
    margin-left: auto;
    font-size: 10px;
    color: var(--sb-section-color);
    transition: transform 0.25s cubic-bezier(0.4,0,0.2,1);
    flex-shrink: 0;
}
.sb-item.open > .sb-arrow {
    transform: rotate(90deg);
}

/* ── Badge ── */
.sb-badge {
    margin-left: auto;
    background: var(--sb-danger);
    color: white;
    font-size: 9px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 20px;
    line-height: 1.4;
}

/* ── Submenu ── */
.sb-sub {
    display: none;
    margin: 2px 0 4px 17px;
    padding-left: 12px;
    border-left: 1px solid var(--sb-accent-border);
    overflow: hidden;
}
.sb-sub.open {
    display: block;
    animation: sb-drop 0.2s ease-out;
}
@keyframes sb-drop {
    from { opacity:0; transform:translateY(-4px); }
    to   { opacity:1; transform:translateY(0); }
}
.sb-sub .sb-item {
    padding: 7px 10px;
    font-size: 12.5px;
    margin-bottom: 1px;
}
.sb-sub .sb-icon {
    width: 22px; height: 22px;
    font-size: 11px;
    background: transparent !important;
    border-radius: 4px;
}
.sb-sub .sb-item:hover .sb-icon {
    background: rgba(99,102,241,0.08) !important;
}
.sb-sub .sb-item.active .sb-icon {
    background: rgba(99,102,241,0.12) !important;
    color: var(--sb-accent) !important;
}

/* ── Footer ── */
.sb-footer {
    border-top: 1px solid var(--sb-border);
    padding: 12px;
    flex-shrink: 0;
}
.sb-profile {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: var(--sb-surface);
    border-radius: 10px;
    border: 1px solid var(--sb-border);
    text-decoration: none;
    transition: all 0.18s;
    margin-bottom: 8px;
}
.sb-profile:hover {
    background: var(--sb-surface-hover);
    border-color: var(--sb-accent-border);
    text-decoration: none;
}
.sb-avatar {
    width: 36px; height: 36px;
    border-radius: 8px;
    background: var(--brand-gradient);
    display: flex; align-items: center; justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 13px;
    flex-shrink: 0;
}
.sb-profile-info { flex: 1; min-width: 0; }
.sb-profile-name {
    display: block;
    color: var(--sb-text-hover);
    font-size: 12.5px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sb-profile-role {
    display: block;
    color: var(--sb-section-color);
    font-size: 11px;
    font-weight: 500;
    text-transform: capitalize;
}
.sb-profile-gear {
    color: var(--sb-section-color);
    font-size: 15px;
}

/* ── Header & Main Layout ── */
#header {
    position: fixed;
    top: 0; right: 0;
    left: var(--sb-width);
    height: 64px;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(0,0,0,0.06);
    display: flex;
    align-items: center;
    padding: 0 28px;
    z-index: 1020;
    gap: 12px;
    transition: left 0.3s ease;
}
[data-theme="dark"] #header {
    background: rgba(10,13,20,0.95);
    border-bottom-color: rgba(255,255,255,0.06);
}
#main {
    margin-left: var(--sb-width) !important;
    margin-top: 64px !important;
    min-height: calc(100vh - 64px);
    padding: 28px !important;
    background: #f0f2f8;
    transition: margin-left 0.3s ease;
}
[data-theme="dark"] #main { background: #070a10; }

.header-toggle {
    background: transparent;
    border: 1px solid rgba(0,0,0,0.08);
    width: 38px; height: 38px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: #64748b;
    cursor: pointer;
    transition: all 0.18s;
}
.header-toggle:hover { background: #f1f5f9; color: #4f46e5; }

.header-title h6 { margin: 0; font-weight: 700; font-size: 14px; color: #1e293b; font-family: var(--font); }
.header-title small { font-size: 11px; color: #94a3b8; font-weight: 400; }

.header-action {
    background: transparent;
    border: none;
    width: 38px; height: 38px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; color: #64748b;
    transition: all 0.18s;
    position: relative;
    cursor: pointer;
}
.header-action:hover { background: #f1f5f9; color: #4f46e5; }
.header-notif-badge {
    position: absolute;
    top: 6px; right: 6px;
    width: 8px; height: 8px;
    background: #ef4444;
    border-radius: 50%;
    border: 2px solid white;
}
.header-divider { width: 1px; height: 24px; background: rgba(0,0,0,0.08); }
.header-avatar {
    width: 36px; height: 36px;
    background: var(--brand-gradient, linear-gradient(135deg,#4f46e5,#a855f7));
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 700; font-size: 12px; cursor: pointer;
}
.avatar-info .name { display: block; font-size: 13px; font-weight: 600; color: #1e293b; line-height: 1.2; }
.avatar-info .role { font-size: 11px; color: #94a3b8; }

/* ── Mobile ── */
@media (max-width: 991px) {
    #sidebar { left: -300px !important; transition: left 0.3s cubic-bezier(0.4,0,0.2,1) !important; }
    #sidebar.show { left: 0 !important; }
    #main, #header { margin-left: 0 !important; left: 0 !important; }
}
</style>

<div id="sidebar-overlay"></div>

@php $setting = \App\Models\GenaralSetting::first(); @endphp

<aside id="sidebar">

    {{-- ══ BRAND ══ --}}
    <a class="sb-brand" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'employee' ? route('employee.dashboard') : '#') }}">
        <div class="sb-logo">
            @if($setting && $setting->logo_url)
                <img src="{{ $setting->logo_url }}" style="width:100%;height:100%;object-fit:contain;border-radius:8px;">
            @else
                <i class="bi bi-rocket-takeoff-fill"></i>
            @endif
        </div>
        <div class="sb-brand-text">
            <span class="sb-brand-name">{{ $setting->website_name ?? 'JHR BAZAR' }}</span>
            <div class="sb-brand-status">
                <span class="sb-status-dot"></span>
                <span class="sb-brand-tag">System Live</span>
            </div>
        </div>
    </a>

    <div class="sb-scroll">

        {{-- ══════════════════════════════════════════
             SECTION 1 · MAIN
        ══════════════════════════════════════════ --}}
        <div class="sb-section">Main</div>

        <a class="sb-item {{ request()->routeIs('admin.dashboard') || request()->routeIs('employee.dashboard') ? 'active' : '' }}"
           href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('employee.dashboard') }}">
            <span class="sb-icon"><i class="bi bi-grid-fill"></i></span>
            Dashboard
        </a>

        <a class="sb-item {{ request()->routeIs('admin.ourbrands.*') ? 'active' : '' }}"
           href="{{ route('admin.ourbrands.index') }}">
            <span class="sb-icon"><i class="bi bi-image"></i></span>
            Our Brand Slider
        </a>


        {{-- ══════════════════════════════════════════
             SECTION 2 · ORDERS
        ══════════════════════════════════════════ --}}
        @if(auth()->user()->hasPermission('order.list'))
        <div class="sb-section">Orders</div>

        {{-- Orders Hub (parent) --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.orders.*') ? 'active open' : '' }}" data-sub="orders-hub">
            <span class="sb-icon gradient"><i class="bi bi-bag-fill" style="color:white;"></i></span>
            <span style="font-weight:600;">Orders Hub</span>
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.orders.*') ? 'open' : '' }}" id="sub-orders-hub">
            <a class="sb-item {{ request()->routeIs('admin.orders.index') && request()->route('status') == 'all' ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'all') }}">
                <span class="sb-icon"><i class="bi bi-basket"></i></span> All Orders
            </a>
            <a class="sb-item {{ request()->routeIs('admin.orders.index') && request()->route('status') == 'pending' ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'pending') }}">
                <span class="sb-icon"><i class="bi bi-hourglass-split"></i></span> Pending
            </a>
            <a class="sb-item {{ request()->routeIs('admin.orders.index') && request()->route('status') == 'processing' ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'processing') }}">
                <span class="sb-icon"><i class="bi bi-arrow-repeat"></i></span> Processing
            </a>
            <a class="sb-item {{ request()->routeIs('admin.orders.index') && request()->route('status') == 'shipped' ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'shipped') }}">
                <span class="sb-icon"><i class="bi bi-truck"></i></span> Shipped
            </a>
            <a class="sb-item {{ request()->routeIs('admin.orders.index') && request()->route('status') == 'delivered' ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'delivered') }}">
                <span class="sb-icon"><i class="bi bi-check-circle"></i></span> Delivered
            </a>
            <a class="sb-item {{ request()->routeIs('admin.orders.index') && request()->route('status') == 'cancelled' ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'cancelled') }}">
                <span class="sb-icon"><i class="bi bi-x-circle"></i></span> Cancelled
            </a>
            <a class="sb-item {{ request()->routeIs('admin.orders.incomplete') ? 'active' : '' }}"
               href="{{ route('admin.orders.incomplete') }}">
                <span class="sb-icon"><i class="bi bi-clipboard-x"></i></span> Incomplete Orders
            </a>
        </div>

        {{-- Order Tools --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.orders.staff_assignments') || request()->routeIs('admin.orders.activity_history') || request()->routeIs('admin.pointofsalepos.index') ? 'open' : '' }}" data-sub="order-tools">
            <span class="sb-icon"><i class="bi bi-tools"></i></span>
            <span>Order Tools</span>
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.orders.staff_assignments') || request()->routeIs('admin.orders.activity_history') || request()->routeIs('admin.pointofsalepos.index') ? 'open' : '' }}" id="sub-order-tools">
            <a class="sb-item {{ request()->routeIs('admin.pointofsalepos.index') ? 'active' : '' }}"
               href="{{ route('admin.pointofsalepos.index') }}">
                <span class="sb-icon"><i class="bi bi-plus-circle"></i></span> Create Order
            </a>
            <a class="sb-item {{ request()->routeIs('admin.orders.staff_assignments') ? 'active' : '' }}"
               href="{{ route('admin.orders.staff_assignments') }}">
                <span class="sb-icon"><i class="bi bi-person-gear"></i></span> Staff Assignments
            </a>
            <a class="sb-item {{ request()->routeIs('admin.orders.activity_history') ? 'active' : '' }}"
               href="{{ route('admin.orders.activity_history') }}">
                <span class="sb-icon"><i class="bi bi-clock-history"></i></span> Activity History
            </a>
        </div>
        @endif

        {{-- Refund Management --}}
        @if(auth()->user()->hasPermission('return_order.list'))
        <a class="sb-item {{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}"
           href="{{ route('admin.refunds.index') }}">
            <span class="sb-icon"><i class="bi bi-arrow-return-left"></i></span>
            Refund Management
        </a>
        @endif

        {{-- Courier Management --}}
        @if(auth()->user()->hasPermission('courier.list'))
        <a class="sb-item {{ request()->routeIs('admin.courier.*') ? 'active' : '' }}"
           href="{{ route('admin.courier.index') }}">
            <span class="sb-icon"><i class="bi bi-truck-flatbed"></i></span>
            Courier Management
        </a>
        @endif


        {{-- ══════════════════════════════════════════
             SECTION 3 · POINT OF SALE
        ══════════════════════════════════════════ --}}
        @if(auth()->user()->hasPermission('pos.list'))
        <div class="sb-section">Point of Sale</div>

        <div class="sb-item has-sub {{ request()->routeIs('admin.pointofsalepos.*') ? 'active open' : '' }}" data-sub="pos">
            <span class="sb-icon"><i class="bi bi-display"></i></span>
            POS Management
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.pointofsalepos.*') ? 'open' : '' }}" id="sub-pos">
            <a class="sb-item {{ request()->routeIs('admin.pointofsalepos.index') ? 'active' : '' }}"
               href="{{ route('admin.pointofsalepos.index') }}">
                <span class="sb-icon"><i class="bi bi-display"></i></span> POS Terminal
            </a>
            <a class="sb-item {{ request()->routeIs('admin.pointofsalepos.sales.*') ? 'active' : '' }}"
               href="{{ route('admin.pointofsalepos.sales.index') }}">
                <span class="sb-icon"><i class="bi bi-receipt"></i></span> Sales History
            </a>
            <a class="sb-item {{ request()->routeIs('admin.pointofsalepos.draft.*') ? 'active' : '' }}"
               href="{{ route('admin.pointofsalepos.draft.index') }}">
                <span class="sb-icon"><i class="bi bi-file-earmark-text"></i></span> Sales Drafts
            </a>
        </div>
        @endif


        {{-- ══════════════════════════════════════════
             SECTION 4 · COMMUNICATIONS
        ══════════════════════════════════════════ --}}
        @if(auth()->user()->hasPermission('chat.list'))
        <div class="sb-section">Communications</div>

        <a class="sb-item {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}"
           href="{{ route('admin.chat.index') }}">
            <span class="sb-icon"><i class="bi bi-chat-left-dots-fill"></i></span>
            Customer Chat
            @php $unreadChats = \App\Models\ChatSession::where('is_read_by_admin', false)->whereNotNull('user_id')->whereNull('receiver_id')->count(); @endphp
            @if($unreadChats > 0)
                <span class="sb-badge">{{ $unreadChats }}</span>
            @endif
        </a>

        <a class="sb-item {{ request()->routeIs('admin.seller_chat.*') ? 'active' : '' }}"
           href="{{ route('admin.seller_chat.index') }}">
            <span class="sb-icon"><i class="bi bi-chat-square-dots-fill"></i></span>
            Seller Chat
            @php $unreadSellerChats = \App\Models\ChatSession::where('is_read_by_admin', false)->whereHas('user', function($q) { $q->where('role', 'seller'); })->count(); @endphp
            @if($unreadSellerChats > 0)
                <span class="sb-badge">{{ $unreadSellerChats }}</span>
            @endif
        </a>

        @if(auth()->user()->hasPermission('contact.list'))
        <a class="sb-item {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}"
           href="{{ route('admin.contact.index') }}">
            <span class="sb-icon"><i class="bi bi-envelope-fill"></i></span>
            Contact Messages
        </a>
        @endif

        {{-- Firebase Push Notifications --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.notifications.*') || request()->routeIs('admin.firebase.settings') ? 'active open' : '' }}" data-sub="firebase-notif">
            <span class="sb-icon"><i class="bi bi-bell-fill"></i></span>
            <span>Push Notifications</span>
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.notifications.*') || request()->routeIs('admin.firebase.settings') ? 'open' : '' }}" id="sub-firebase-notif">
            <a class="sb-item {{ request()->routeIs('admin.notifications.create') ? 'active' : '' }}"
               href="{{ route('admin.notifications.create') }}">
                <span class="sb-icon"><i class="bi bi-send"></i></span> Send Notification
            </a>
            <a class="sb-item {{ request()->routeIs('admin.notifications.index') ? 'active' : '' }}"
               href="{{ route('admin.notifications.index') }}">
                <span class="sb-icon"><i class="bi bi-clock-history"></i></span> History Logs
            </a>
            <a class="sb-item {{ request()->routeIs('admin.firebase.settings') ? 'active' : '' }}"
               href="{{ route('admin.firebase.settings') }}">
                <span class="sb-icon"><i class="bi bi-gear"></i></span> FCM Config
            </a>
        </div>
        @endif


        {{-- ══════════════════════════════════════════
             SECTION 5 · CATALOG
        ══════════════════════════════════════════ --}}
        @if(auth()->user()->hasPermission('product.list'))
        <div class="sb-section">Catalog</div>

        {{-- Categories --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategory.*') ? 'active open' : '' }}" data-sub="category">
            <span class="sb-icon"><i class="bi bi-grid-3x3-gap"></i></span>
            Categories
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategory.*') ? 'open' : '' }}" id="sub-category">
            <a class="sb-item {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}"
               href="{{ route('admin.categories.index') }}">
                <span class="sb-icon"><i class="bi bi-list-ul"></i></span> All Categories
            </a>
            <a class="sb-item {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}"
               href="{{ route('admin.categories.create') }}">
                <span class="sb-icon"><i class="bi bi-plus"></i></span> Add Category
            </a>
            <a class="sb-item {{ request()->routeIs('admin.subcategory.index') ? 'active' : '' }}"
               href="{{ route('admin.subcategory.index') }}">
                <span class="sb-icon"><i class="bi bi-list-nested"></i></span> All SubCategories
            </a>
            <a class="sb-item {{ request()->routeIs('admin.subcategory.create') ? 'active' : '' }}"
               href="{{ route('admin.subcategory.create') }}">
                <span class="sb-icon"><i class="bi bi-plus"></i></span> Add SubCategory
            </a>
        </div>

        {{-- Products --}}
        <div class="sb-item has-sub {{ request()->routeIs('products.*') ? 'active open' : '' }}" data-sub="product">
            <span class="sb-icon"><i class="bi bi-box-seam"></i></span>
            Products
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('products.*') ? 'open' : '' }}" id="sub-product">
            <a class="sb-item {{ request()->routeIs('products.index') ? 'active' : '' }}"
               href="{{ route('products.index') }}">
                <span class="sb-icon"><i class="bi bi-boxes"></i></span> All Products
            </a>
            <a class="sb-item {{ request()->routeIs('products.create') ? 'active' : '' }}"
               href="{{ route('products.create') }}">
                <span class="sb-icon"><i class="bi bi-plus-circle"></i></span> Add Product
            </a>
            <a class="sb-item {{ request()->routeIs('admin.digital_product.*') ? 'active' : '' }}"
               href="{{ route('admin.digital_product.index') }}">
                <span class="sb-icon"><i class="bi bi-file-earmark-zip"></i></span> Digital Products
            </a>
        </div>
        @endif

        {{-- Product Variants --}}
        @if(auth()->user()->hasPermission('brand.list') || auth()->user()->hasPermission('color.list') || auth()->user()->hasPermission('size.list') || auth()->user()->hasPermission('unit.list'))
        <div class="sb-item has-sub {{ request()->routeIs('admin.productbrands.*') || request()->routeIs('admin.colors.*') || request()->routeIs('admin.sizes.*') || request()->routeIs('admin.units.*') ? 'active open' : '' }}" data-sub="variant">
            <span class="sb-icon"><i class="bi bi-layers"></i></span>
            Product Variants
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.productbrands.*') || request()->routeIs('admin.colors.*') || request()->routeIs('admin.sizes.*') || request()->routeIs('admin.units.*') ? 'open' : '' }}" id="sub-variant">
            @if(auth()->user()->hasPermission('brand.list'))
            <a class="sb-item {{ request()->routeIs('admin.productbrands.*') ? 'active' : '' }}"
               href="{{ route('admin.productbrands.index') }}">
                <span class="sb-icon"><i class="bi bi-bookmark"></i></span> Brands
            </a>
            @endif
            @if(auth()->user()->hasPermission('brand.list'))
            <a class="sb-item {{ request()->routeIs('admin.ourbrands.*') ? 'active' : '' }}"
               href="{{ route('admin.ourbrands.index') }}">
                <span class="sb-icon"><i class="bi bi-image"></i></span> Our Brand Slider
            </a>
            @endif
            @if(auth()->user()->hasPermission('color.list'))
            <a class="sb-item {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}"
               href="{{ route('admin.colors.index') }}">
                <span class="sb-icon"><i class="bi bi-palette"></i></span> Colors
            </a>
            @endif
            @if(auth()->user()->hasPermission('size.list'))
            <a class="sb-item {{ request()->routeIs('admin.sizes.*') ? 'active' : '' }}"
               href="{{ route('admin.sizes.index') }}">
                <span class="sb-icon"><i class="bi bi-rulers"></i></span> Sizes
            </a>
            @endif
            @if(auth()->user()->hasPermission('unit.list'))
            <a class="sb-item {{ request()->routeIs('admin.units.*') ? 'active' : '' }}"
               href="{{ route('admin.units.index') }}">
                <span class="sb-icon"><i class="bi bi-123"></i></span> Units
            </a>
            @endif
        </div>
        @endif


        {{-- ══════════════════════════════════════════
             SECTION 6 · SHOP & PROMOTIONS
        ══════════════════════════════════════════ --}}
        @if(auth()->user()->hasPermission('seller_approval.list') || auth()->user()->hasPermission('bank.list') || auth()->user()->hasPermission('shop.list') || auth()->user()->hasPermission('promo_code.list') || auth()->user()->hasPermission('flash_sale.list') || auth()->user()->hasPermission('banner.list'))
        <div class="sb-section">Shop & Promotions</div>

        {{-- Seller Approvals --}}
        @if(auth()->user()->hasPermission('seller_approval.list'))
        <a class="sb-item {{ request()->routeIs('admin.sellers.approvals') ? 'active' : '' }}"
           href="{{ route('admin.sellers.approvals') }}">
            <span class="sb-icon"><i class="bi bi-person-check-fill"></i></span>
            Seller Approvals
            @php $pendingCount = \App\Models\User::where('role','seller')->where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="sb-badge">{{ $pendingCount }}</span>
            @endif
        </a>
        @endif

        {{-- Shop Management --}}
        @if(auth()->user()->hasPermission('shop.list'))
        <div class="sb-item has-sub {{ request()->routeIs('admin.shops.*') ? 'active open' : '' }}" data-sub="shop">
            <span class="sb-icon"><i class="bi bi-shop"></i></span>
            Shop Management
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.shops.*') ? 'open' : '' }}" id="sub-shop">
            <a class="sb-item {{ request()->routeIs('admin.shops.index') ? 'active' : '' }}"
               href="{{ route('admin.shops.index') }}">
                <span class="sb-icon"><i class="bi bi-list-ul"></i></span> All Shops
            </a>
            <a class="sb-item {{ request()->routeIs('admin.shops.create') ? 'active' : '' }}"
               href="{{ route('admin.shops.create') }}">
                <span class="sb-icon"><i class="bi bi-plus"></i></span> Add Shop
            </a>
        </div>
        @endif

        {{-- Bank Information --}}
        @if(auth()->user()->hasPermission('bank.list'))
        <a class="sb-item {{ request()->routeIs('admin.banks.*') ? 'active' : '' }}"
           href="{{ route('admin.banks.index') }}">
            <span class="sb-icon"><i class="bi bi-bank2"></i></span>
            Bank Information
        </a>
        @endif

        {{-- Promotions --}}
        @if(auth()->user()->hasPermission('promo_code.list') || auth()->user()->hasPermission('flash_sale.list') || auth()->user()->hasPermission('banner.list'))
        <div class="sb-item has-sub {{ request()->routeIs('admin.flashsale.*') || request()->routeIs('admin.banner.*') || request()->routeIs('admin.promocode.*') ? 'active open' : '' }}" data-sub="promo">
            <span class="sb-icon"><i class="bi bi-gift-fill"></i></span>
            Promotions
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.flashsale.*') || request()->routeIs('admin.banner.*') || request()->routeIs('admin.promocode.*') ? 'open' : '' }}" id="sub-promo">
            @if(auth()->user()->hasPermission('promo_code.list'))
            <a class="sb-item {{ request()->routeIs('admin.promocode.*') ? 'active' : '' }}"
               href="{{ route('admin.promocode.index') }}">
                <span class="sb-icon"><i class="bi bi-ticket-perforated"></i></span> Coupon Codes
            </a>
            @endif
            @if(auth()->user()->hasPermission('flash_sale.list'))
            <a class="sb-item {{ request()->routeIs('admin.flashsale.*') ? 'active' : '' }}"
               href="{{ route('admin.flashsale.index') }}">
                <span class="sb-icon"><i class="bi bi-lightning-fill"></i></span> Flash Sales
            </a>
            @endif
            @if(auth()->user()->hasPermission('banner.list'))
            <a class="sb-item {{ request()->routeIs('admin.banner.*') ? 'active' : '' }}"
               href="{{ route('admin.banner.index') }}">
                <span class="sb-icon"><i class="bi bi-image"></i></span> Banners
            </a>
            @endif
        </div>
        @endif

        {{-- Withdrawal Management --}}
        @if(auth()->user()->hasPermission('withdrawal.list'))
        <a class="sb-item {{ request()->routeIs('admin.withdraws.*') ? 'active' : '' }}"
           href="{{ route('admin.withdraws.index') }}">
            <span class="sb-icon"><i class="bi bi-wallet2"></i></span>
            Withdrawal Management
        </a>
        @endif
        @endif


        {{-- ══════════════════════════════════════════
             SECTION 7 · FRAUD & SECURITY
        ══════════════════════════════════════════ --}}
        @if(auth()->user()->hasPermission('fraud.dashboard') || auth()->user()->hasPermission('fraud.blacklist'))
        <div class="sb-section">Fraud & Security</div>

        {{-- Customer Detector --}}
        <a class="sb-item {{ request()->routeIs('admin.customer-detector.*') ? 'active' : '' }}"
           href="{{ route('admin.customer-detector.index') }}">
            <span class="sb-icon"><i class="bi bi-eye-fill"></i></span>
            Customer Detector
        </a>

        {{-- Cyber Alerts --}}
        <a class="sb-item {{ request()->routeIs('admin.cyber-alerts') ? 'active' : '' }}"
           href="{{ route('admin.cyber-alerts') }}">
            <span class="sb-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
            Cyber Alerts (Live)
        </a>

        @if(auth()->user()->hasPermission('fraud.dashboard'))
        {{-- Fraud Checks --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.fraud.checks.*') || request()->routeIs('admin.fraud.index') || request()->routeIs('admin.fraud.dashboard') ? 'active open' : '' }}" data-sub="fraud-checks">
            <span class="sb-icon"><i class="bi bi-shield-exclamation"></i></span>
            Fraud Checks
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.fraud.checks.*') || request()->routeIs('admin.fraud.dashboard') ? 'open' : '' }}" id="sub-fraud-checks">

            <a class="sb-item {{ request()->routeIs('admin.fraud.apis.index') ? 'active' : '' }}"
               href="{{ route('admin.fraud.apis.index') }}">
                <span class="sb-icon"><i class="bi bi-gear-fill"></i></span> Manage Fraud APIs
            </a>


        </div>
        @endif

        {{-- Blacklist & IP Block --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.fraud.blacklist.*') || request()->routeIs('admin.Ipblockmanage.*') ? 'active open' : '' }}" data-sub="blocklist">
            <span class="sb-icon"><i class="bi bi-ban"></i></span>
            Blocklist & IP Block
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.fraud.blacklist.*') || request()->routeIs('admin.Ipblockmanage.*') ? 'open' : '' }}" id="sub-blocklist">
            @if(auth()->user()->hasPermission('fraud.blacklist'))
            <a class="sb-item {{ request()->routeIs('admin.fraud.blacklist.*') ? 'active' : '' }}"
               href="{{ route('admin.fraud.blacklist.index') }}">
                <span class="sb-icon"><i class="bi bi-slash-circle"></i></span> Fraud Blacklist
            </a>
            @endif
            <a class="sb-item {{ request()->routeIs('admin.Ipblockmanage.*') ? 'active' : '' }}"
               href="{{ route('admin.Ipblockmanage.index') }}">
                <span class="sb-icon"><i class="bi bi-shield-slash-fill"></i></span> IP Block Manage
            </a>
        </div>
        @endif


        {{-- ══════════════════════════════════════════
             SECTION 8 · HRM
        ══════════════════════════════════════════ --}}
        <div class="sb-section">HRM</div>

        {{-- HRM Dashboard --}}
        <a class="sb-item {{ request()->routeIs('admin.hrm.dashboard') ? 'active' : '' }}"
           href="{{ route('admin.hrm.dashboard') }}">
            <span class="sb-icon"><i class="bi bi-people-fill"></i></span>
            HRM Dashboard
        </a>

        {{-- Attendance --}}
        <a class="sb-item {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}"
           href="{{ route('admin.attendance.index') }}">
            <span class="sb-icon"><i class="bi bi-calendar-check-fill"></i></span>
            Attendance
        </a>

        {{-- Leave Management --}}
        <a class="sb-item {{ request()->routeIs('admin.hrm.leave.*') ? 'active' : '' }}"
           href="{{ route('admin.hrm.leave.index') }}">
            <span class="sb-icon"><i class="bi bi-calendar-x-fill"></i></span>
            Leave Management
            @php $pendingLeaveCount = \App\Models\Leave::where('status','Pending')->count(); @endphp
            @if($pendingLeaveCount > 0)
                <span class="sb-badge">{{ $pendingLeaveCount }}</span>
            @endif
        </a>

        {{-- Salary Advance --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.hrm.salary-advance.*') ? 'active open' : '' }}" data-sub="salary-advance">
            <span class="sb-icon"><i class="bi bi-cash-coin"></i></span>
            Salary Advance
            @php $pendingAdvCount = \App\Models\SalaryAdvance::where('status','Pending')->count(); @endphp
            @if($pendingAdvCount > 0)
                <span class="sb-badge">{{ $pendingAdvCount }}</span>
            @endif
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.hrm.salary-advance.*') ? 'open' : '' }}" id="sub-salary-advance">
            <a class="sb-item {{ request()->routeIs('admin.hrm.salary-advance.index') ? 'active' : '' }}"
               href="{{ route('admin.hrm.salary-advance.index') }}">
                <span class="sb-icon"><i class="bi bi-list-ul"></i></span> All Advances
            </a>
            <a class="sb-item {{ request()->routeIs('admin.hrm.salary-advance.create') ? 'active' : '' }}"
               href="{{ route('admin.hrm.salary-advance.create') }}">
                <span class="sb-icon"><i class="bi bi-plus-circle"></i></span> New Advance
            </a>
        </div>

        {{-- Office Expenses --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.hrm.expense.*') ? 'active open' : '' }}" data-sub="office-expense">
            <span class="sb-icon"><i class="bi bi-receipt-cutoff"></i></span>
            Office Expenses
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.hrm.expense.*') ? 'open' : '' }}" id="sub-office-expense">
            <a class="sb-item {{ request()->routeIs('admin.hrm.expense.index') ? 'active' : '' }}"
               href="{{ route('admin.hrm.expense.index') }}">
                <span class="sb-icon"><i class="bi bi-list-ul"></i></span> All Expenses
            </a>
            <a class="sb-item {{ request()->routeIs('admin.hrm.expense.create') ? 'active' : '' }}"
               href="{{ route('admin.hrm.expense.create') }}">
                <span class="sb-icon"><i class="bi bi-plus-circle"></i></span> Add Expense
            </a>
            <a class="sb-item {{ request()->routeIs('admin.hrm.expense.categories') ? 'active' : '' }}"
               href="{{ route('admin.hrm.expense.categories') }}">
                <span class="sb-icon"><i class="bi bi-tags"></i></span> Categories
            </a>
        </div>

        {{-- Payroll --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.hrm.payroll.*') ? 'active open' : '' }}" data-sub="payroll">
            <span class="sb-icon"><i class="bi bi-wallet2"></i></span>
            Payroll
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.hrm.payroll.*') ? 'open' : '' }}" id="sub-payroll">
            <a class="sb-item {{ request()->routeIs('admin.hrm.payroll.index') ? 'active' : '' }}"
               href="{{ route('admin.hrm.payroll.index') }}">
                <span class="sb-icon"><i class="bi bi-list-ul"></i></span> Payroll List
            </a>
            <a class="sb-item" href="#"
               onclick="event.preventDefault(); document.getElementById('openGenerateModal')?.click();">
                <span class="sb-icon"><i class="bi bi-gear"></i></span> Generate Payroll
            </a>
        </div>


        {{-- ══════════════════════════════════════════
             SECTION 9 · PEOPLE & ACCESS
        ══════════════════════════════════════════ --}}
        @if(auth()->user()->hasPermission('customer.list') || auth()->user()->hasPermission('user.list') || auth()->user()->hasPermission('role.list') || auth()->user()->hasPermission('employee.list') || auth()->user()->hasPermission('supplier.list'))
        <div class="sb-section">People & Access</div>

        {{-- Customer Management --}}
        @if(auth()->user()->hasPermission('customer.list'))
        <div class="sb-item has-sub {{ request()->routeIs('admin.customers.*') ? 'active open' : '' }}" data-sub="customer">
            <span class="sb-icon"><i class="bi bi-person-heart"></i></span>
            Customers
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.customers.*') ? 'open' : '' }}" id="sub-customer">
            <a class="sb-item {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}"
               href="{{ route('admin.customers.index') }}">
                <span class="sb-icon"><i class="bi bi-list-ul"></i></span> All Customers
            </a>
        </div>
        @endif

        {{-- User Management --}}
        @if(auth()->user()->hasPermission('user.list'))
        <div class="sb-item has-sub {{ request()->routeIs('admin.users.*') ? 'active open' : '' }}" data-sub="all-users">
            <span class="sb-icon"><i class="bi bi-people"></i></span>
            Administrator Management
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.users.*') ? 'open' : '' }}" id="sub-all-users">
            <a class="sb-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}"
               href="{{ route('admin.users.index') }}">
                <span class="sb-icon"><i class="bi bi-list-ul"></i></span> Users List
            </a>
            <a class="sb-item {{ request()->routeIs('admin.users.create') ? 'active' : '' }}"
               href="{{ route('admin.users.create') }}">
                <span class="sb-icon"><i class="bi bi-person-plus"></i></span> Add New User
            </a>
        </div>
        @endif

        {{-- Roles & Permissions --}}
        @if(auth()->user()->hasPermission('role.list'))
        <div class="sb-item has-sub {{ request()->routeIs('admin.role.*') ? 'active open' : '' }}" data-sub="roles">
            <span class="sb-icon"><i class="bi bi-shield-lock-fill"></i></span>
            Roles & Permissions
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.role.*') ? 'open' : '' }}" id="sub-roles">
            <a class="sb-item {{ request()->routeIs('admin.role.index') ? 'active' : '' }}"
               href="{{ route('admin.role.index') }}">
                <span class="sb-icon"><i class="bi bi-list-ul"></i></span> All Roles
            </a>
        </div>
        @endif

        {{-- Employee Management --}}
        @if(auth()->user()->hasPermission('employee.list'))
        <div class="sb-item has-sub {{ request()->routeIs('admin.employees.*') ? 'active open' : '' }}" data-sub="employee">
            <span class="sb-icon"><i class="bi bi-person-badge-fill"></i></span>
            Employees
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.employees.*') ? 'open' : '' }}" id="sub-employee">
            <a class="sb-item {{ request()->routeIs('admin.employees.index') ? 'active' : '' }}"
               href="{{ route('admin.employees.index') }}">
                <span class="sb-icon"><i class="bi bi-list-ul"></i></span> All Employees
            </a>
            <a class="sb-item {{ request()->routeIs('admin.employees.create') ? 'active' : '' }}"
               href="{{ route('admin.employees.create') }}">
                <span class="sb-icon"><i class="bi bi-person-plus"></i></span> Add Employee
            </a>
        </div>
        @endif

        {{-- Supplier Management --}}
        @if(auth()->user()->hasPermission('supplier.list'))
        <div class="sb-item has-sub {{ request()->routeIs('admin.supplier.*') ? 'active open' : '' }}" data-sub="supplier">
            <span class="sb-icon"><i class="bi bi-building"></i></span>
            Suppliers
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.supplier.*') ? 'open' : '' }}" id="sub-supplier">
            <a class="sb-item {{ request()->routeIs('admin.supplier.index') ? 'active' : '' }}"
               href="{{ route('admin.supplier.index') }}">
                <span class="sb-icon"><i class="bi bi-list-ul"></i></span> All Suppliers
            </a>
            <a class="sb-item {{ request()->routeIs('admin.supplier.create') ? 'active' : '' }}"
               href="{{ route('admin.supplier.create') }}">
                <span class="sb-icon"><i class="bi bi-plus-circle"></i></span> Add Supplier
            </a>
        </div>
        @endif
        @endif


        {{-- ══════════════════════════════════════════
             SECTION 10 · CONTENT
        ══════════════════════════════════════════ --}}
        @if(auth()->user()->hasPermission('landing_page.list'))
        <div class="sb-section">Content</div>

        {{-- Landing Pages --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.landingpages.*') ? 'active open' : '' }}" data-sub="landing">
            <span class="sb-icon"><i class="bi bi-layout-text-window-reverse"></i></span>
            Landing Pages
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.landingpages.*') ? 'open' : '' }}" id="sub-landing">
            <a class="sb-item {{ request()->routeIs('admin.landingpages.create') ? 'active' : '' }}"
               href="{{ route('admin.landingpages.create') }}">
                <span class="sb-icon"><i class="bi bi-plus"></i></span> Create Landing Page
            </a>
            <a class="sb-item {{ request()->routeIs('admin.landingpages.index') ? 'active' : '' }}"
               href="{{ route('admin.landingpages.index') }}">
                <span class="sb-icon"><i class="bi bi-list-ul"></i></span> Campaign List
            </a>
        </div>

        {{-- Footer Management --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.page_categories.*') || request()->routeIs('admin.pages.*') ? 'active open' : '' }}" data-sub="footer">
            <span class="sb-icon"><i class="bi bi-layout-text-sidebar-reverse"></i></span>
            Footer Management
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.page_categories.*') || request()->routeIs('admin.pages.*') ? 'open' : '' }}" id="sub-footer">
            <a class="sb-item {{ request()->routeIs('admin.page_categories.*') ? 'active' : '' }}"
               href="{{ route('admin.page_categories.index') }}">
                <span class="sb-icon"><i class="bi bi-folder"></i></span> Footer Categories
            </a>
            <a class="sb-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}"
               href="{{ route('admin.pages.index') }}">
                <span class="sb-icon"><i class="bi bi-file-earmark-text"></i></span> Footer Pages
            </a>
        </div>
        @endif

        {{-- Blog Management --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.blog*') ? 'active open' : '' }}" data-sub="blog">
            <span class="sb-icon"><i class="bi bi-newspaper"></i></span>
            Blog Management
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.blog*') ? 'open' : '' }}" id="sub-blog">
            <a class="sb-item {{ request()->routeIs('admin.blog_categories.*') ? 'active' : '' }}"
               href="{{ route('admin.blog_categories.index') }}">
                <span class="sb-icon"><i class="bi bi-tags"></i></span> Blog Categories
            </a>
            <a class="sb-item {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}"
               href="{{ route('admin.blog.index') }}">
                <span class="sb-icon"><i class="bi bi-file-earmark-richtext"></i></span> All Blogs
            </a>
        </div>

        {{-- Company Pages --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.about.*') || request()->routeIs('admin.privacy.*') ? 'active open' : '' }}" data-sub="company-pages">
            <span class="sb-icon"><i class="bi bi-file-earmark-check"></i></span>
            Company Pages
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.about.*') || request()->routeIs('admin.privacy.*') ? 'open' : '' }}" id="sub-company-pages">
            <a class="sb-item {{ request()->routeIs('admin.about.*') ? 'active' : '' }}"
               href="{{ route('admin.about.index') }}">
                <span class="sb-icon"><i class="bi bi-building-fill"></i></span> About Company
            </a>
            <a class="sb-item {{ request()->routeIs('admin.privacy.*') ? 'active' : '' }}"
               href="{{ route('admin.privacy.index') }}">
                <span class="sb-icon"><i class="bi bi-shield-check"></i></span> Privacy Policy
            </a>
        </div>

        {{-- Membership Logos --}}
        <a class="sb-item {{ request()->routeIs('admin.membership_logos.*') ? 'active' : '' }}"
           href="{{ route('admin.membership_logos.index') }}">
            <span class="sb-icon"><i class="bi bi-award"></i></span>
            Membership Logos
        </a>


        {{-- ══════════════════════════════════════════
             SECTION 11 · PAYMENT & GATEWAYS
        ══════════════════════════════════════════ --}}
        @if(auth()->user()->hasPermission('third_party.list'))
        <div class="sb-section">Payment & Gateways</div>

        {{-- BD Gateways --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.settings.gateways.bd') ? 'active open' : '' }}" data-sub="bd-gateways">
            <span class="sb-icon"><i class="bi bi-cash-stack"></i></span>
            BD Gateways
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.settings.gateways.bd') ? 'open' : '' }}" id="sub-bd-gateways">
            <a class="sb-item {{ request()->routeIs('admin.settings.gateways.bd') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways.bd') }}">
                <span class="sb-icon"><i class="bi bi-grid-1x2"></i></span> All BD Gateways
            </a>

        </div>

        {{-- International Gateways --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.settings.gateways.international') ? 'active open' : '' }}" data-sub="intl-gateways">
            <span class="sb-icon"><i class="bi bi-globe2"></i></span>
            International Gateways
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.settings.gateways.international') ? 'open' : '' }}" id="sub-intl-gateways">
            <a class="sb-item" href="{{ route('admin.settings.gateways.international') }}">
                <span class="sb-icon"><i class="bi bi-grid-1x2"></i></span> All Gateways
            </a>

        </div>

        {{-- SMS & Mail Config --}}
        <div class="sb-item has-sub {{ request()->routeIs('admin.sms.*') || request()->routeIs('admin.smsgatewaysetup.*') || request()->routeIs('admin.mailconfiguration.*') ? 'active open' : '' }}" data-sub="sms-mail">
            <span class="sb-icon"><i class="bi bi-chat-left-text-fill"></i></span>
            SMS & Mail Config
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.sms.*') || request()->routeIs('admin.smsgatewaysetup.*') || request()->routeIs('admin.mailconfiguration.*') ? 'open' : '' }}" id="sub-sms-mail">
            <a class="sb-item {{ request()->routeIs('admin.smsgatewaysetup.*') ? 'active' : '' }}"
               href="{{ route('admin.smsgatewaysetup.index') }}">
                <span class="sb-icon"><i class="bi bi-phone"></i></span> SMS Configuration
            </a>
            <a class="sb-item {{ request()->routeIs('admin.mailconfiguration.*') ? 'active' : '' }}"
               href="{{ route('admin.mailconfiguration.index') }}">
                <span class="sb-icon"><i class="bi bi-envelope-at"></i></span> Mail Configuration
            </a>
        </div>


        @endif


        {{-- ══════════════════════════════════════════
             SECTION 12 · SYSTEM SETTINGS
        ══════════════════════════════════════════ --}}
        @if(auth()->user()->hasPermission('business_setting.list') || auth()->user()->hasPermission('site_setting.list'))
        <div class="sb-section">System Settings</div>

        {{-- Business Settings --}}
        @if(auth()->user()->hasPermission('business_setting.list'))
        <div class="sb-item has-sub {{ request()->routeIs('admin.generalsettings.*') || request()->routeIs('admin.businesssettings.*') || request()->routeIs('admin.verificationotpsettings.*') || request()->routeIs('admin.aiprompt.*') || request()->routeIs('admin.currencies.*') || request()->routeIs('admin.alltaxes.*') || request()->routeIs('admin.sociallinkList.*') || request()->routeIs('admin.themecolorssettings.*') ? 'active open' : '' }}" data-sub="biz-settings">
            <span class="sb-icon"><i class="bi bi-gear-wide-connected"></i></span>
            Business Settings
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.generalsettings.*') || request()->routeIs('admin.businesssettings.*') || request()->routeIs('admin.verificationotpsettings.*') || request()->routeIs('admin.aiprompt.*') || request()->routeIs('admin.currencies.*') || request()->routeIs('admin.alltaxes.*') || request()->routeIs('admin.sociallinkList.*') || request()->routeIs('admin.themecolorssettings.*') ? 'open' : '' }}" id="sub-biz-settings">
            <a class="sb-item {{ request()->routeIs('admin.generalsettings.*') ? 'active' : '' }}"
               href="{{ route('admin.generalsettings.index') }}">
                <span class="sb-icon"><i class="bi bi-sliders"></i></span> General Settings
            </a>
            <a class="sb-item {{ request()->routeIs('admin.businesssettings.*') ? 'active' : '' }}"
               href="{{ route('admin.businesssettings.index') }}">
                <span class="sb-icon"><i class="bi bi-building-gear"></i></span> Business Setup
            </a>
            <a class="sb-item {{ request()->routeIs('admin.verificationotpsettings.*') ? 'active' : '' }}"
               href="{{ route('admin.verificationotpsettings.index') }}">
                <span class="sb-icon"><i class="bi bi-phone-vibrate"></i></span> Verification / OTP
            </a>
            <a class="sb-item {{ request()->routeIs('admin.aiprompt.*') ? 'active' : '' }}"
               href="{{ route('admin.aiprompt.index') }}">
                <span class="sb-icon"><i class="bi bi-robot"></i></span> AI Prompt
            </a>
            <a class="sb-item {{ request()->routeIs('admin.currencies.*') ? 'active' : '' }}"
               href="{{ route('admin.currencies.index') }}">
                <span class="sb-icon"><i class="bi bi-currency-dollar"></i></span> Currencies
            </a>
            <a class="sb-item {{ request()->routeIs('admin.alltaxes.*') ? 'active' : '' }}"
               href="{{ route('admin.alltaxes.index') }}">
                <span class="sb-icon"><i class="bi bi-percent"></i></span> VAT & Taxes
            </a>
            <a class="sb-item {{ request()->routeIs('admin.sociallinkList.*') ? 'active' : '' }}"
               href="{{ route('admin.sociallinkList.index') }}">
                <span class="sb-icon"><i class="bi bi-share-fill"></i></span> Social Links
            </a>
            <a class="sb-item {{ request()->routeIs('admin.themecolorssettings.*') ? 'active' : '' }}"
               href="{{ route('admin.themecolorssettings.index') }}">
                <span class="sb-icon"><i class="bi bi-palette2"></i></span> Theme Colors
            </a>
        </div>
        @endif

        {{-- Site Settings --}}
        @if(auth()->user()->hasPermission('site_setting.list'))
        <div class="sb-item has-sub {{ request()->routeIs('admin.shippingcharge.*') || request()->routeIs('admin.duplicateordersetting.*') || request()->routeIs('admin.support.*') ? 'active open' : '' }}" data-sub="site-settings">
            <span class="sb-icon"><i class="bi bi-toggles"></i></span>
            Site Settings
            <i class="bi bi-chevron-right sb-arrow"></i>
        </div>
        <div class="sb-sub {{ request()->routeIs('admin.shippingcharge.*') || request()->routeIs('admin.duplicateordersetting.*') || request()->routeIs('admin.support.*') ? 'open' : '' }}" id="sub-site-settings">
            <a class="sb-item {{ request()->routeIs('admin.shippingcharge.*') ? 'active' : '' }}"
               href="{{ route('admin.shippingcharge.index') }}">
                <span class="sb-icon"><i class="bi bi-truck-front"></i></span> Shipping Charges
            </a>
            <a class="sb-item {{ request()->routeIs('admin.duplicateordersetting.*') ? 'active' : '' }}"
               href="{{ route('admin.duplicateordersetting.index') }}">
                <span class="sb-icon"><i class="bi bi-copy"></i></span> Duplicate Order Setting
            </a>
            <a class="sb-item {{ request()->routeIs('admin.support.*') ? 'active' : '' }}"
               href="{{ route('admin.support.index') }}">
                <span class="sb-icon"><i class="bi bi-headset"></i></span> Admin Support
            </a>
        </div>
        @endif
        @endif


        {{-- ══════════════════════════════════════════
             SECTION 13 · ACCOUNT
        ══════════════════════════════════════════ --}}
        @if(auth()->user()->hasPermission('profile.list'))
        <div class="sb-section">Account</div>
        <a class="sb-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
           href="{{ route('admin.profile.index') }}">
            <span class="sb-icon"><i class="bi bi-person-circle"></i></span>
            My Profile
        </a>
        @endif

    </div>{{-- /.sb-scroll --}}


    {{-- ══ FOOTER ══ --}}
    <div class="sb-footer">
        <a href="{{ route('admin.profile.index') }}" class="sb-profile">
            <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div class="sb-profile-info">
                <span class="sb-profile-name">{{ auth()->user()->name }}</span>
                <span class="sb-profile-role">{{ auth()->user()->role }}</span>
            </div>
            <i class="bi bi-gear sb-profile-gear"></i>
        </a>

        <a class="sb-item danger" href="#"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="sb-icon"><i class="bi bi-box-arrow-right"></i></span>
            <span style="font-weight:600;">Sign Out</span>
        </a>
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>

</aside>


{{-- ══ JavaScript ══ --}}
<script>
(function () {
    'use strict';

    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');

    /* ── Submenu toggle ── */
    document.querySelectorAll('.sb-item.has-sub').forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            var key = this.dataset.sub;
            var sub = document.getElementById('sub-' + key);
            if (!sub) return;

            var isOpen = sub.classList.contains('open');
            sub.classList.toggle('open', !isOpen);
            this.classList.toggle('open', !isOpen);
        });
    });

    /* ── Auto-open active submenus ── */
    document.querySelectorAll('.sb-sub .sb-item.active').forEach(function (activeLink) {
        var sub = activeLink.closest('.sb-sub');
        if (!sub) return;
        sub.classList.add('open');
        var trigger = sub.previousElementSibling;
        while (trigger && !trigger.classList.contains('has-sub')) {
            trigger = trigger.previousElementSibling;
        }
        if (trigger) trigger.classList.add('open');
    });

    /* ── Mobile overlay ── */
    if (overlay && sidebar) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    /* ── Global toggle ── */
    window.sidebarToggle = function () {
        if (!sidebar || !overlay) return;
        var open = sidebar.classList.toggle('show');
        overlay.classList.toggle('show', open);
    };

    var toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', window.sidebarToggle);
    }

})();
</script>
