<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    :root {
        --sb-bg:            var(--bg-card);
        --sb-surface:       var(--bg-body);
        --sb-border:        var(--border-color);
        --sb-text:          var(--text-muted);
        --sb-text-hover:    var(--text-main);
        --sb-active-color:  #6366f1;
        --sb-active-bg:     rgba(99,102,241,0.1);
        --sb-active-border: #6366f1;
        --sb-section-text:  var(--text-muted);
        --sb-hover-bg:      var(--border-color);
        --brand-start:      #4f46e5;
        --brand-end:        #8b5cf6;
        --danger-color:     #ef4444;
        --sb-width:         280px;
    }

    [data-theme="dark"] {
        --sb-bg:            #090b10;
        --sb-surface:       #151921;
    }

    #sidebar {
        width: var(--sb-width) !important;
        background: var(--sb-bg) !important;
        border-right: 1px solid var(--sb-border) !important;
        box-shadow: 10px 0 30px rgba(0,0,0,0.5) !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        font-family: 'Inter', sans-serif !important;
        transition: width 0.3s cubic-bezier(0.4,0,0.2,1) !important;
        position: fixed;
        top: 0; left: 0; bottom: 0;
        z-index: 1030;
        display: flex;
        flex-direction: column;
    }

    #sidebar::-webkit-scrollbar { width: 4px; }
    #sidebar::-webkit-scrollbar-track { background: transparent; }
    #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    #sidebar::-webkit-scrollbar-thumb:hover { background: var(--sb-active-color); }

    .sidebar-brand {
        display: flex !important;
        align-items: center !important;
        gap: 14px !important;
        padding: 28px 24px !important;
        text-decoration: none !important;
        transition: all 0.3s ease !important;
    }

    .brand-logo-wrap {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--brand-start), var(--brand-end));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: white;
        box-shadow: 0 8px 16px -4px rgba(79, 70, 229, 0.5);
        position: relative;
    }

    .brand-logo-wrap::after {
        content: '';
        position: absolute;
        inset: -2px;
        border: 2px solid rgba(255,255,255,0.1);
        border-radius: 14px;
    }

    .brand-text-wrap {
        display: flex;
        flex-direction: column;
    }

    .brand-name-main {
        color: var(--text-main);
        font-weight: 800;
        font-size: 18px;
        letter-spacing: -0.5px;
        line-height: 1;
    }

    [data-theme="dark"] .brand-name-main {
        color: #f8fafc;
    }

    .brand-status {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 8px #10b981;
        animation: pulseStatus 2s infinite;
    }

    @keyframes pulseStatus {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
        100% { opacity: 1; transform: scale(1); }
    }

    .status-text {
        color: #64748b;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .sidebar-inner {
        flex: 1;
        padding: 0 12px 30px !important;
    }

    .nav-section-title {
        padding: 24px 16px 8px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        letter-spacing: 1.5px !important;
        text-transform: uppercase !important;
        color: var(--sb-section-text) !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-section-title::after {
        content: '';
        height: 1px;
        background: var(--sb-border);
        flex: 1;
    }

    .nav-item-custom {
        display: flex !important;
        align-items: center !important;
        padding: 10px 14px !important;
        margin-bottom: 4px !important;
        border-radius: 12px !important;
        color: var(--sb-text) !important;
        text-decoration: none !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        position: relative;
        border: 1px solid transparent !important;
    }

    .nav-item-custom i:not(.arrow) {
        width: 34px !important;
        height: 34px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 10px !important;
        background: rgba(255,255,255,0.03) !important;
        margin-right: 12px !important;
        font-size: 16px !important;
        transition: all 0.2s !important;
        color: var(--sb-text) !important;
    }

    .nav-item-custom:hover {
        background: var(--sb-hover-bg) !important;
        color: var(--sb-text-hover) !important;
        transform: translateX(4px);
    }

    .nav-item-custom:hover i:not(.arrow) {
        background: rgba(99,102,241,0.1) !important;
        color: var(--sb-active-color) !important;
    }

    .nav-item-custom.active {
        background: var(--sb-active-bg) !important;
        color: var(--sb-active-color) !important;
        border-color: rgba(99,102,241,0.1) !important;
    }

    .nav-item-custom.active i:not(.arrow) {
        background: var(--sb-active-color) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(99,102,241,0.3) !important;
    }

    .orders-hub-icon {
        width: 34px !important;
        height: 34px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 10px !important;
        background: linear-gradient(135deg, #4f46e5, #8b5cf6) !important;
        margin-right: 12px !important;
        font-size: 16px !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4) !important;
    }

    .arrow {
        font-size: 10px !important;
        transition: transform 0.3s !important;
        opacity: 0.5 !important;
    }

    .nav-item-custom.open .arrow {
        transform: rotate(90deg) !important;
        opacity: 1 !important;
    }

    .nav-submenu {
        margin-left: 31px !important;
        padding-left: 15px !important;
        border-left: 1.5px solid var(--sb-border) !important;
        margin-bottom: 10px !important;
        display: none;
    }

    .nav-submenu.open { display: block; }

    .nav-submenu .nav-item-custom {
        padding: 8px 12px !important;
        font-size: 13.5px !important;
        background: transparent !important;
        margin-bottom: 2px !important;
    }

    .nav-submenu .nav-item-custom i:not(.arrow) {
        width: auto !important;
        height: auto !important;
        background: transparent !important;
        margin-right: 10px !important;
        font-size: 12px !important;
        opacity: 0.5;
    }

    .nav-submenu .nav-item-custom:hover i:not(.arrow) {
        opacity: 1;
    }

    /* ── Bottom Profile ── */
    .sidebar-footer {
        padding: 20px !important;
        border-top: 1px solid var(--sb-border) !important;
        background: rgba(0,0,0,0.2) !important;
    }

    .profile-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: var(--sb-surface);
        border-radius: 16px;
        border: 1px solid var(--sb-border);
        text-decoration: none;
        transition: all 0.2s;
    }

    .profile-card:hover {
        background: var(--sb-hover-bg);
        border-color: var(--sb-active-border);
    }

    .profile-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .profile-info {
        flex: 1;
        min-width: 0;
    }

    .profile-name {
        display: block;
        color: #f1f5f9;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-role {
        display: block;
        color: #64748b;
        font-size: 11px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .profile-action {
        color: var(--sb-text);
        font-size: 16px;
        opacity: 0.5;
    }

    /* ── Orders Hub special ── */
    .orders-hub-icon {
        width: 32px !important;
        height: 32px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 8px !important;
        background: linear-gradient(135deg, var(--brand-start), var(--brand-end)) !important;
        margin-right: 10px !important;
        font-size: 14px !important;
        flex-shrink: 0 !important;
        box-shadow: 0 3px 8px -2px rgba(99,102,241,0.5) !important;
    }

    /* ── Submenus ── */
    .nav-submenu {
        display: none;
        overflow: hidden;
        margin: 2px 4px 2px 24px !important;
        padding: 2px 0 !important;
        border-left: 1px solid rgba(99,102,241,0.15) !important;
    }
    .nav-submenu.open {
        display: block;
        animation: sbSlideDown 0.2s ease-out;
    }
    @keyframes sbSlideDown {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .nav-submenu .nav-item-custom {
        margin: 1px 0 1px 8px !important;
        padding: 8px 12px !important;
        font-size: 13px !important;
        border-radius: 8px !important;
        border: 1px solid transparent !important;
    }
    .nav-submenu .nav-item-custom i:not(.arrow) {
        width: 22px !important;
        height: 22px !important;
        font-size: 12px !important;
        background: transparent !important;
        border-radius: 4px !important;
        margin-right: 8px !important;
    }
    .nav-submenu .nav-item-custom:hover i:not(.arrow) {
        background: transparent !important;
    }
    .nav-submenu .nav-item-custom.active {
        background: rgba(99,102,241,0.1) !important;
        border-color: rgba(99,102,241,0.15) !important;
    }
    .nav-submenu .nav-item-custom.active::before {
        display: none;
    }

    /* ── Danger (logout) ── */
    .nav-item-custom.text-danger {
        color: var(--danger-color) !important;
    }
    .nav-item-custom.text-danger i:not(.arrow) {
        color: var(--danger-color) !important;
        background: rgba(244,63,94,0.08) !important;
    }
    .nav-item-custom.text-danger:hover {
        background: rgba(244,63,94,0.08) !important;
        border-color: rgba(244,63,94,0.15) !important;
    }

    /* ── Sidebar overlay (mobile) ── */
    #sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(2px);
        z-index: 1029;
    }
    #sidebar-overlay.show { display: block; }

    /* ── Layout Fixes ── */
    #header {
        position: fixed;
        top: 0; right: 0; left: 0;
        height: 70px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        padding: 0 30px 0 calc(var(--sb-width) + 30px);
        z-index: 1020;
        gap: 15px;
        transition: all 0.3s ease !important;
    }

    #main {
        margin-left: var(--sb-width) !important;
        margin-top: 70px !important;
        min-height: calc(100vh - 70px);
        padding: 30px !important;
        background: #f8fafc;
        transition: margin-left 0.3s ease !important;
    }

    .header-toggle {
        background: #f1f5f9;
        border: none;
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: var(--sb-text);
        transition: all 0.2s;
    }
    .header-toggle:hover { background: #e2e8f0; color: var(--sb-active-color); }

    .header-title h6 { margin: 0; font-weight: 700; color: #1e293b; font-size: 15px; }

    .header-action {
        background: transparent;
        border: none;
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: #64748b;
        transition: all 0.2s;
        margin-left: auto;
    }
    .header-action:hover { background: #f1f5f9; color: var(--sb-active-color); }

    .header-notif-badge {
        position: absolute;
        top: 8px; right: 8px;
        background: var(--danger-color);
        color: white;
        font-size: 9px;
        padding: 2px 5px;
        border-radius: 10px;
        font-weight: 700;
        border: 2px solid white;
    }

    .lang-btn {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 6px 12px;
        border-radius: 10px;
        display: flex; align-items: center; gap: 8px;
        font-size: 13px; font-weight: 600; color: #475569;
    }

    .avatar-wrap {
        position: relative;
    }

    .avatar-wrap .avatar {
        width: 38px; height: 38px;
        background: var(--sb-active-bg);
        color: var(--sb-active-color);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700;
        border: 1px solid rgba(99,102,241,0.1);
    }
    .avatar-info .name { display: block; font-size: 13px; font-weight: 700; color: #1e293b; line-height: 1; }
    .avatar-info .role { font-size: 11px; color: #94a3b8; font-weight: 500; }

    .avatar-wrap .dropdown-menu {
        top: 100% !important;
        margin-top: 5px !important;
        transform: none !important;
        right: 0 !important;
        left: auto !important;
    }

    #main {
        margin-top: 70px !important;
        min-height: calc(100vh - 70px);
        padding: 30px !important;
        background: #f8fafc;
    }

    /* ── Mobile ── */
    @media (max-width: 991px) {
        #sidebar {
            left: -302px !important;
            transform: none !important;
            transition: left 0.3s cubic-bezier(0.4,0,0.2,1) !important;
        }
        #sidebar.show {
            left: 0 !important;
        }
        #main, #header {
            margin-left: 0 !important;
            padding-left: 30px !important;
        }
    }
</style>

<div id="sidebar-overlay"></div>

@php $setting = \App\Models\GenaralSetting::first(); @endphp
<aside id="sidebar">

    {{-- ── Brand ── --}}
    <a class="sidebar-brand" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'employee' ? route('employee.dashboard') : '#') }}">
        <div class="brand-logo-wrap">
            @if($setting && $setting->logo_url)
                <img src="{{ $setting->logo_url }}" style="width:100%; height:100%; object-fit:contain; border-radius:10px;">
            @else
                <i class="bi bi-rocket-takeoff-fill"></i>
            @endif
        </div>
        <div class="brand-text-wrap">
            <span class="brand-name-main">{{ $setting->website_name ?? 'JHR BAZAR' }}</span>
            <div class="brand-status">
                <span class="status-dot"></span>
                <span class="status-text">System Live</span>
            </div>
        </div>
    </a>

    <div class="sidebar-inner">

        {{-- ══════════════ MAIN ══════════════ --}}
        <div class="nav-section-title">Main</div>

        <a class="nav-item-custom {{ request()->routeIs('admin.dashboard') || request()->routeIs('employee.dashboard') ? 'active' : '' }}"
           href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('employee.dashboard') }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>

        {{-- ══════════════ ORDERS HUB ══════════════ --}}
        @if(auth()->user()->hasPermission('order.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" data-sub="orders-hub">
            <div class="orders-hub-icon">
                <i class="bi bi-bag-fill" style="margin-right: 0 !important; color: white !important;"></i>
            </div>
            <span style="font-weight: 600;">Orders Hub</span>
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu nav-submenu-orders" id="sub-orders-hub">
            <a class="nav-item-custom" href="{{ route('admin.orders.index', 'all') }}">
                <i class="bi bi-basket"></i> All Orders
            </a>
            <a class="nav-item-custom" href="{{ route('admin.orders.index', 'pending') }}">
                <i class="bi bi-hourglass-split"></i> Pending
            </a>
            <a class="nav-item-custom" href="{{ route('admin.orders.index', 'processing') }}">
                <i class="bi bi-arrow-repeat"></i> Processing
            </a>
            <a class="nav-item-custom" href="{{ route('admin.orders.index', 'shipped') }}">
                <i class="bi bi-truck"></i> Shipped
            </a>
            <a class="nav-item-custom" href="{{ route('admin.orders.index', 'delivered') }}">
                <i class="bi bi-check-circle"></i> Delivered
            </a>
            <a class="nav-item-custom" href="{{ route('admin.orders.index', 'cancelled') }}">
                <i class="bi bi-x-circle"></i> Cancelled
            </a>
            <a class="nav-item-custom" href="{{ route('admin.orders.incomplete') }}">
                <i class="bi bi-clipboard-x"></i> Incomplete Orders
            </a>
            <a class="nav-item-custom" href="{{ route('admin.pointofsalepos.index') }}">
                <i class="bi bi-plus-lg"></i> Create Order
            </a>
            <a class="nav-item-custom" href="#">
                <i class="bi bi-person-gear"></i> Staff Assignments
            </a>
            <a class="nav-item-custom" href="#">
                <i class="bi bi-clock-history"></i> Activity History
            </a>
        </div>
        @endif

        {{-- POS Management (Permission Based) --}}
        @if(auth()->user()->hasPermission('pos.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.pointofsalepos.*') ? 'active' : '' }}"
             data-sub="pos">
            <i class="bi bi-display"></i> POS Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-pos">
            <a class="nav-item-custom {{ request()->routeIs('admin.pointofsalepos.index') ? 'active' : '' }}"
               href="{{ route('admin.pointofsalepos.index') }}">
                <i class="bi bi-dot"></i> POS
            </a>
            <a class="nav-item-custom" href="#">
                <i class="bi bi-dot"></i> POS Orders
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.pointofsalepos.sales.*') ? 'active' : '' }}"
               href="{{ route('admin.pointofsalepos.sales.index') }}">
                <i class="bi bi-dot"></i> POS Sales History
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.pointofsalepos.draft.*') ? 'active' : '' }}"
               href="{{ route('admin.pointofsalepos.draft.index') }}">
                <i class="bi bi-dot"></i> POS Sales Draft
            </a>
        </div>
        @endif

        @if(auth()->user()->hasPermission('return_order.list'))
        <a class="nav-item-custom" href="#">
            <i class="bi bi-arrow-return-left"></i> Refund Management
        </a>
        @endif

        {{-- ══════════════ COURIER ══════════════ --}}
        @if(auth()->user()->hasPermission('courier.list'))
        <a class="nav-item-custom {{ request()->routeIs('admin.courier.*') ? 'active' : '' }}" href="{{ route('admin.courier.index') }}">
            <i class="bi bi-truck-flatbed"></i> Courier Management
        </a>
        @endif

        @if(auth()->user()->hasPermission('chat.list'))
        <a class="nav-item-custom {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}" href="{{ route('admin.chat.index') }}">
            <i class="bi bi-chat-left-dots"></i> Conversations
            @php $unreadChats = \App\Models\ChatSession::where('is_read_by_admin', false)->whereNotNull('user_id')->whereNull('receiver_id')->count(); @endphp
            @if($unreadChats > 0)
                <span class="badge bg-danger ms-auto rounded-pill" style="font-size: 10px;">{{ $unreadChats }}</span>
            @endif
        </a>

        <a class="nav-item-custom {{ request()->routeIs('admin.seller_chat.*') ? 'active' : '' }}" href="{{ route('admin.seller_chat.index') }}">
            <i class="bi bi-chat-square-dots-fill"></i> Seller Chat
            @php 
                $unreadSellerChats = \App\Models\ChatSession::where('is_read_by_admin', false)
                    ->whereHas('user', function($q) { $q->where('role', 'seller'); })
                    ->count(); 
            @endphp
            @if($unreadSellerChats > 0)
                <span class="badge bg-danger ms-auto rounded-pill" style="font-size: 10px;">{{ $unreadSellerChats }}</span>
            @endif
        </a>
        @endif

        {{-- ══════════════ FRAUD (Permission Based) ══════════════ --}}
        @if(auth()->user()->hasPermission('fraud.dashboard'))
        <div class="nav-section-title">Fraud Management</div>


        <a class="nav-item-custom {{ request()->routeIs('admin.fraud.apis.index') ? 'active' : '' }}"
           href="{{ route('admin.fraud.apis.index') }}">
            <i class="bi bi-gear-fill"></i> Fraud APIs
        </a>
        @endif

        {{-- Fraud Components (Protected) --}}



        @if(auth()->user()->hasPermission('fraud.blacklist'))
        {{-- Blacklist --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.fraud.blacklist.*') ? 'active' : '' }}"
             data-sub="fraud-blacklist">
            <i class="bi bi-ban"></i> Blacklist
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-fraud-blacklist">
            <a class="nav-item-custom {{ request()->routeIs('admin.fraud.blacklist.index') ? 'active' : '' }}"
               href="{{ route('admin.fraud.blacklist.index') }}">
                <i class="bi bi-dot"></i> All Blacklists
            </a>
        </div>
        @endif

        {{-- Catalog Section (Protected) --}}
        @if(auth()->user()->hasPermission('product.list'))
        {{-- ══════════════ CATALOG ══════════════ --}}
        <div class="nav-section-title">Catalog</div>

        {{-- Category Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategory.*') ? 'active' : '' }}"
             data-sub="category">
            <i class="bi bi-grid-3x3-gap"></i> Category Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-category">
            <a class="nav-item-custom {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}"
               href="{{ route('admin.categories.index') }}">
                <i class="bi bi-dot"></i> All Categories
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}"
               href="{{ route('admin.categories.create') }}">
                <i class="bi bi-dot"></i> Add Category
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.subcategory.index') ? 'active' : '' }}"
               href="{{ route('admin.subcategory.index') }}">
                <i class="bi bi-dot"></i> All SubCategories
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.subcategory.create') ? 'active' : '' }}"
               href="{{ route('admin.subcategory.create') }}">
                <i class="bi bi-dot"></i> Add SubCategory
            </a>
        </div>

        {{-- Product Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('products.*') ? 'active' : '' }}"
             data-sub="product">
            <i class="bi bi-box-seam"></i> Product Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-product">
            <a class="nav-item-custom {{ request()->routeIs('products.index') ? 'active' : '' }}"
               href="{{ route('products.index') }}">
                <i class="bi bi-dot"></i> All Products
            </a>
            <a class="nav-item-custom {{ request()->routeIs('products.create') ? 'active' : '' }}"
               href="{{ route('products.create') }}">
                <i class="bi bi-dot"></i> Add Product
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.digital_product.*') ? 'active' : '' }}"
               href="{{ route('admin.digital_product.index') }}">
                <i class="bi bi-dot"></i> Digital Products
            </a>

        </div>
        @endif

        {{-- Product Variant Management --}}
        @if(auth()->user()->hasPermission('brand.list') || auth()->user()->hasPermission('color.list') || auth()->user()->hasPermission('size.list') || auth()->user()->hasPermission('unit.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.productbrands.*') || request()->routeIs('admin.colors.*') || request()->routeIs('admin.sizes.*') || request()->routeIs('admin.units.*') ? 'active' : '' }}"
             data-sub="variant">
            <i class="bi bi-layers"></i> Product Variant Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-variant">
            @if(auth()->user()->hasPermission('brand.list'))
            <a class="nav-item-custom {{ request()->routeIs('admin.productbrands.*') ? 'active' : '' }}"
               href="{{ route('admin.productbrands.index') }}">
                <i class="bi bi-dot"></i> Brand
            </a>
            @endif
            @if(auth()->user()->hasPermission('color.list'))
            <a class="nav-item-custom {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}"
               href="{{ route('admin.colors.index') }}">
                <i class="bi bi-dot"></i> Color
            </a>
            @endif
            @if(auth()->user()->hasPermission('size.list'))
            <a class="nav-item-custom {{ request()->routeIs('admin.sizes.*') ? 'active' : '' }}"
               href="{{ route('admin.sizes.index') }}">
                <i class="bi bi-dot"></i> Size
            </a>
            @endif
            @if(auth()->user()->hasPermission('unit.list'))
            <a class="nav-item-custom {{ request()->routeIs('admin.units.*') ? 'active' : '' }}"
               href="{{ route('admin.units.index') }}">
                <i class="bi bi-dot"></i> Unit
            </a>
            @endif
        </div>
        @endif

        {{-- ══════════════ SHOP MANAGEMENT ══════════════ --}}
        <div class="nav-section-title">Shop Management</div>

        @if(auth()->user()->hasPermission('seller_approval.list') || auth()->user()->hasPermission('bank.list'))
        @if(auth()->user()->hasPermission('seller_approval.list'))
        <a class="nav-item-custom {{ request()->routeIs('admin.sellers.approvals') ? 'active' : '' }}"
           href="{{ route('admin.sellers.approvals') }}">
            <i class="bi bi-person-check"></i> Seller Approvals
            @php $pendingCount = \App\Models\User::where('role', 'seller')->where('status', 'pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="badge bg-danger ms-auto rounded-pill" style="font-size: 10px;">{{ $pendingCount }}</span>
            @endif
        </a>
        @endif
        @if(auth()->user()->hasPermission('bank.list'))
        <a class="nav-item-custom {{ request()->routeIs('admin.banks.*') ? 'active' : '' }}"
           href="{{ route('admin.banks.index') }}">
            <i class="bi bi-bank"></i> BankInformation
        </a>
        @endif
        @endif

        @if(auth()->user()->hasPermission('shop.list'))
        {{-- Shops --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.shops.*') ? 'active' : '' }}"
             data-sub="shop">
            <i class="bi bi-shop"></i> Shop Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-shop">
            <a class="nav-item-custom {{ request()->routeIs('admin.shops.index') ? 'active' : '' }}"
               href="{{ route('admin.shops.index') }}">
                <i class="bi bi-dot"></i> All Shops
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.shops.create') ? 'active' : '' }}"
               href="{{ route('admin.shops.create') }}">
                <i class="bi bi-dot"></i> Add Shop
            </a>
        </div>
        @endif

        {{-- Promotion Management --}}
        @if(auth()->user()->hasPermission('promo_code.list') || auth()->user()->hasPermission('flash_sale.list') || auth()->user()->hasPermission('banner.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.flashsale.*') || request()->routeIs('admin.banner.*') || request()->routeIs('admin.promocode.*') ? 'active' : '' }}"
             data-sub="promo">
            <i class="bi bi-gift"></i> Promotion Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-promo">
            @if(auth()->user()->hasPermission('promo_code.list'))
            <a class="nav-item-custom {{ request()->routeIs('admin.promocode.*') ? 'active' : '' }}"
               href="{{ route('admin.promocode.index') }}">
                <i class="bi bi-dot"></i> Coupons
            </a>
            @endif
            @if(auth()->user()->hasPermission('flash_sale.list'))
            <a class="nav-item-custom {{ request()->routeIs('admin.flashsale.*') ? 'active' : '' }}"
               href="{{ route('admin.flashsale.index') }}">
                <i class="bi bi-dot"></i> Flash Sales
            </a>
            @endif
            @if(auth()->user()->hasPermission('banner.list'))
            <a class="nav-item-custom {{ request()->routeIs('admin.banner.*') ? 'active' : '' }}"
               href="{{ route('admin.banner.index') }}">
                <i class="bi bi-dot"></i> Banners
            </a>
            @endif
        </div>
        @endif

        @if(auth()->user()->hasPermission('profile.list'))
        {{-- ✅ Profile — সরাসরি admin.profile.index রুট ব্যবহার করা হচ্ছে --}}
        <a class="nav-item-custom {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
           href="{{ route('admin.profile.index') }}">
            <i class="bi bi-person-circle"></i> Profile
        </a>
        @endif

        {{-- Management Section (Protected) --}}
        @if(auth()->user()->hasPermission('customer.list') || auth()->user()->hasPermission('user.list') || auth()->user()->hasPermission('role.list') || auth()->user()->hasPermission('employee.list') || auth()->user()->hasPermission('supplier.list'))
        {{-- ══════════════ MANAGEMENT ══════════════ --}}
        <div class="nav-section-title">Management</div>

        {{-- Customer Management --}}
        @if(auth()->user()->hasPermission('customer.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
             data-sub="customer">
            <i class="bi bi-people"></i> Customer Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-customer">
            <a class="nav-item-custom {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}"
               href="{{ route('admin.customers.index') }}">
                <i class="bi bi-dot"></i> All Customers
            </a>
        </div>
        @endif

        {{-- Unified User Management --}}
        @if(auth()->user()->hasPermission('user.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
             data-sub="all-users">
            <i class="bi bi-people-fill"></i> User Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-all-users">
            <a class="nav-item-custom {{ request()->routeIs('admin.users.index') ? 'active' : '' }}"
               href="{{ route('admin.users.index') }}">
                <i class="bi bi-list-stars"></i> Users List
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.users.create') ? 'active' : '' }}"
               href="{{ route('admin.users.create') }}">
                <i class="bi bi-person-plus-fill"></i> Add New User
            </a>
        </div>
        @endif

        {{-- Roles & Permissions --}}
        @if(auth()->user()->hasPermission('role.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.role.*') ? 'active' : '' }}"
             data-sub="roles">
            <i class="bi bi-shield-lock"></i> Roles & Permissions
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-roles">
            <a class="nav-item-custom {{ request()->routeIs('admin.role.index') ? 'active' : '' }}"
               href="{{ route('admin.role.index') }}">
                <i class="bi bi-dot"></i> All Roles & Permissions
            </a>
        </div>
        @endif

        {{-- Employee Management --}}
        @if(auth()->user()->hasPermission('employee.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}"
             data-sub="employee">
            <i class="bi bi-person-badge"></i> Employee Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-employee">
            <a class="nav-item-custom {{ request()->routeIs('admin.employees.index') ? 'active' : '' }}"
               href="{{ route('admin.employees.index') }}">
                <i class="bi bi-dot"></i> All Employees
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.employees.create') ? 'active' : '' }}"
               href="{{ route('admin.employees.create') }}">
                <i class="bi bi-dot"></i> Add Employee
            </a>
        </div>
        @endif

        {{-- Supplier Management --}}
        @if(auth()->user()->hasPermission('supplier.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.supplier.*') ? 'active' : '' }}"
             data-sub="supplier">
            <i class="bi bi-truck"></i> Supplier Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-supplier">
            <a class="nav-item-custom {{ request()->routeIs('admin.supplier.index') ? 'active' : '' }}"
               href="{{ route('admin.supplier.index') }}">
                <i class="bi bi-dot"></i> All Suppliers
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.supplier.create') ? 'active' : '' }}"
               href="{{ route('admin.supplier.create') }}">
                <i class="bi bi-dot"></i> Add Supplier
            </a>
        </div>
        @endif
        @endif

        @if(auth()->user()->hasPermission('shop_product.list') || auth()->user()->hasPermission('subscription.list') || auth()->user()->hasPermission('support.list') || auth()->user()->hasPermission('withdrawal.list') || auth()->user()->hasPermission('import_export.list') || auth()->user()->hasPermission('business_setting.list') || auth()->user()->hasPermission('site_setting.list') || auth()->user()->hasPermission('landing_page.list') || auth()->user()->hasPermission('third_party.list') || auth()->user()->hasPermission('contact.list'))
        {{-- ══════════════ SYSTEM ══════════════ --}}
        <div class="nav-section-title">System</div>

        @if(auth()->user()->hasPermission('shop_product.list'))
        <a class="nav-item-custom" href="#"><i class="bi bi-box"></i> Shop Product Management</a>
        @endif
        @if(auth()->user()->hasPermission('subscription.list'))
        <a class="nav-item-custom" href="#"><i class="bi bi-journal-bookmark"></i> Subscription Management</a>
        @endif
        @if(auth()->user()->hasPermission('support.list'))
        <a class="nav-item-custom" href="#"><i class="bi bi-headset"></i> Support Management</a>
        @endif
        @if(auth()->user()->hasPermission('withdrawal.list'))
        <a class="nav-item-custom {{ request()->routeIs('admin.withdraws.*') ? 'active' : '' }}" href="{{ route('admin.withdraws.index') }}">
            <i class="bi bi-wallet2"></i> Withdrawal Management
        </a>
        @endif
        @if(auth()->user()->hasPermission('import_export.list'))
        <a class="nav-item-custom" href="#"><i class="bi bi-arrow-left-right"></i> Import / Export</a>
        @endif

        {{-- Business Settings --}}
        @if(auth()->user()->hasPermission('business_setting.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.generalsettings.*') || request()->routeIs('admin.businesssettings.*') || request()->routeIs('admin.verificationotpsettings.*') || request()->routeIs('admin.aiprompt.*') || request()->routeIs('admin.currencies.*') || request()->routeIs('admin.alltaxes.*') || request()->routeIs('admin.themecolorssettings.*') || request()->routeIs('admin.sociallinkList.*') ? 'active' : '' }}"
             data-sub="business-settings">
            <i class="bi bi-gear"></i> Business Settings
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-business-settings">
            <a class="nav-item-custom {{ request()->routeIs('admin.generalsettings.*') ? 'active' : '' }}"
               href="{{ route('admin.generalsettings.index') }}">
                <i class="bi bi-dot"></i> General Settings
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.businesssettings.*') ? 'active' : '' }}"
               href="{{ route('admin.businesssettings.index') }}">
                <i class="bi bi-dot"></i> Business Setup
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.verificationotpsettings.*') ? 'active' : '' }}"
               href="{{ route('admin.verificationotpsettings.index') }}">
                <i class="bi bi-dot"></i> Manage Verification
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.aiprompt.*') ? 'active' : '' }}"
               href="{{ route('admin.aiprompt.index') }}">
                <i class="bi bi-dot"></i> AI Prompt
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.currencies.*') ? 'active' : '' }}"
               href="{{ route('admin.currencies.index') }}">
                <i class="bi bi-dot"></i> Currency
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.alltaxes.*') ? 'active' : '' }}"
               href="{{ route('admin.alltaxes.index') }}">
                <i class="bi bi-dot"></i> VAT & Tax
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.sociallinkList.*') ? 'active' : '' }}"
               href="{{ route('admin.sociallinkList.index') }}">
                <i class="bi bi-dot"></i> Social Links
            </a>
        </div>
        @endif

        {{-- Site Settings --}}
        @if(auth()->user()->hasPermission('site_setting.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.pixels.*') || request()->routeIs('admin.googletagmanager.*') || request()->routeIs('admin.shippingcharge.*') || request()->routeIs('admin.duplicateordersetting.*') || request()->routeIs('admin.Ipblockmanage.*') ? 'active' : '' }}"
             data-sub="site-settings">
            <i class="bi bi-sliders"></i> Site Settings
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-site-settings">
            <a class="nav-item-custom {{ request()->routeIs('admin.shippingcharge.*') ? 'active' : '' }}"
               href="{{ route('admin.shippingcharge.index') }}">
                <i class="bi bi-dot"></i> Shipping Charge
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.duplicateordersetting.*') ? 'active' : '' }}"
               href="{{ route('admin.duplicateordersetting.index') }}">
                <i class="bi bi-dot"></i> Duplicate Order Setting
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.Ipblockmanage.*') ? 'active' : '' }}"
               href="{{ route('admin.Ipblockmanage.index') }}">
                <i class="bi bi-dot"></i> IP Block Manage
            </a>
        </div>
        @endif

        {{-- Landing Page Settings --}}
        @if(auth()->user()->hasPermission('landing_page.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.landingpages.*') || request()->routeIs('admin.pages.*') ? 'active' : '' }}"
             data-sub="landing-page-settings">
            <i class="bi bi-layout-text-window-reverse"></i> Landing Page Settings
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-landing-page-settings">
            <a class="nav-item-custom {{ request()->routeIs('admin.landingpages.create') ? 'active' : '' }}"
               href="{{ route('admin.landingpages.create') }}">
                <i class="bi bi-dot"></i> Create Landing Page
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.landingpages.index') ? 'active' : '' }}"
               href="{{ route('admin.landingpages.index') }}">
                <i class="bi bi-dot"></i> Campaign List
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.page_categories.*') ? 'active' : '' }}"
               href="{{ route('admin.page_categories.index') }}">
                <i class="bi bi-dot"></i> Page Categories
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}"
               href="{{ route('admin.pages.index') }}">
                <i class="bi bi-dot"></i> Page Manage
            </a>
        </div>
        @endif

        {{-- 3rd Party Configuration --}}
        @if(auth()->user()->hasPermission('third_party.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.settings.gateways') || request()->routeIs('admin.stripe.*') || request()->routeIs('admin.paypal.*') || request()->routeIs('admin.razorpay.*') || request()->routeIs('admin.paystack.*') || request()->routeIs('admin.aamarpay.*') || request()->routeIs('admin.bkash.*') || request()->routeIs('admin.paytabs.*') || request()->routeIs('admin.qicard.*') || request()->routeIs('admin.jazzcash.*') || request()->routeIs('admin.steadfast.*') || request()->routeIs('admin.pathao.*') || request()->routeIs('admin.bkash-pay.*') || request()->routeIs('admin.shurjopay.*') || request()->routeIs('admin.sms.*') || request()->routeIs('admin.twilio.*') || request()->routeIs('admin.nexmo.*') || request()->routeIs('admin.mailconfiguration.*') ? 'active' : '' }}"
             data-sub="third-party">
            <i class="bi bi-plug"></i> 3rd Party Configuration
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-third-party">
            <a class="nav-item-custom {{ request()->routeIs('admin.settings.gateways') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}">
                <i class="bi bi-dot"></i> Payment Gateways
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#stripe">
                <i class="bi bi-dot"></i> Stripe
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#paypal">
                <i class="bi bi-dot"></i> PayPal
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#razorpay">
                <i class="bi bi-dot"></i> Razorpay
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#paystack">
                <i class="bi bi-dot"></i> Paystack
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#aamarpay">
                <i class="bi bi-dot"></i> AamarPay
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#bkash">
                <i class="bi bi-dot"></i> BKash
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#paytabs">
                <i class="bi bi-dot"></i> PayTabs
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#qicard">
                <i class="bi bi-dot"></i> QiCard
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#jazzcash">
                <i class="bi bi-dot"></i> JazzCash
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#bkash-pay">
                <i class="bi bi-dot"></i> Bkash Payment
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#shurjopay">
                <i class="bi bi-dot"></i> Shurjopay
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#sslcommerz">
                <i class="bi bi-dot"></i> SSLCommerz
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#sms">
                <i class="bi bi-dot"></i> SMS Gateway
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.sms.configuration') ? 'active' : '' }}"
               href="{{ route('admin.sms.configuration') }}">
                <i class="bi bi-dot"></i> SMS Configuration
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.mailconfiguration.*') ? 'active' : '' }}"
               href="{{ route('admin.mailconfiguration.index') }}">
                <i class="bi bi-dot"></i> Mail Configuration
            </a>
        </div>
        @endif

        @if(auth()->user()->hasPermission('contact.list'))
        <a class="nav-item-custom {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}"
           href="{{ route('admin.contact.index') }}">
            <i class="bi bi-envelope"></i> Contact Us
        </a>
        @endif
        @endif

    </div>{{-- /.sidebar-inner --}}

    {{-- ── Sidebar Footer Profile ── --}}
    <div class="sidebar-footer">
        <a href="{{ route('admin.profile.index') }}" class="profile-card">
            <div class="profile-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="profile-info">
                <span class="profile-name">{{ auth()->user()->name }}</span>
                <span class="profile-role">{{ auth()->user()->role }}</span>
            </div>
            <div class="profile-action">
                <i class="bi bi-gear"></i>
            </div>
        </a>
        
        <a class="nav-item-custom text-danger mt-3" href="#"
           style="background: rgba(239, 68, 68, 0.05) !important;"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right" style="background: rgba(239, 68, 68, 0.1) !important;"></i> 
            <span style="font-weight: 600;">Sign Out</span>
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

    /* ── Submenu toggle ── */
    document.querySelectorAll('.nav-item-custom.has-sub').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            var key     = this.dataset.sub;
            var submenu = document.getElementById('sub-' + key);
            if (!submenu) return;

            var isOpen = submenu.classList.contains('open');

            /* Toggle the clicked one without closing others */
            if (isOpen) {
                submenu.classList.remove('open');
                this.classList.remove('open');
            } else {
                submenu.classList.add('open');
                this.classList.add('open');
            }
        });
    });

    /* ── Auto-open submenu when a child link is active ── */
    document.querySelectorAll('.nav-submenu .nav-item-custom.active').forEach(function (activeLink) {
        var submenu = activeLink.closest('.nav-submenu');
        if (!submenu) return;
        submenu.classList.add('open');
        var trigger = submenu.previousElementSibling;
        while (trigger && !trigger.classList.contains('has-sub')) {
            trigger = trigger.previousElementSibling;
        }
        if (trigger) trigger.classList.add('open');
    });

    /* ── Auto-open submenu when parent trigger has active class ── */
    document.querySelectorAll('.nav-item-custom.has-sub.active').forEach(function (trigger) {
        var key     = trigger.dataset.sub;
        var submenu = document.getElementById('sub-' + key);
        if (submenu) {
            submenu.classList.add('open');
            trigger.classList.add('open');
        }
    });

    /* ── Mobile: overlay closes sidebar ── */
    var overlay = document.getElementById('sidebar-overlay');
    var sidebar = document.getElementById('sidebar');

    if (overlay && sidebar) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    /* ── Mobile toggle ── */
    var toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (!sidebar || !overlay) return;
            var open = sidebar.classList.toggle('show');
            overlay.classList.toggle('show', open);
        });
    }

    /* ── Mobile toggle function (exposed globally) ── */
    window.sidebarToggle = function () {
        if (!sidebar || !overlay) return;
        var open = sidebar.classList.toggle('show');
        overlay.classList.toggle('show', open);
    };

})();
</script>
