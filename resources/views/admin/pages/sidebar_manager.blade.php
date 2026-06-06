<style>
    :root {
        --sidebar-bg: #ffffff;
        --sidebar-active-bg: rgba(245, 158, 11, 0.08);
        --sidebar-active-color: #f59e0b;
        --sidebar-text: #64748b;
        --sidebar-hover-bg: #f8fafc;
        --sidebar-shadow: 10px 0 40px rgba(0,0,0,0.03);
        --brand-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.3);
    }

    #sidebar {
        background: var(--sidebar-bg) !important;
        box-shadow: var(--sidebar-shadow) !important;
        border-right: 1px solid rgba(0,0,0,0.05) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .sidebar-brand {
        background: var(--brand-gradient) !important;
        margin: 20px !important;
        border-radius: 16px !important;
        padding: 18px !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.4) !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        text-decoration: none !important;
    }

    .brand-icon {
        background: rgba(255,255,255,0.15) !important;
        backdrop-filter: blur(8px);
        border-radius: 12px !important;
        width: 42px !important;
        height: 42px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
    }

    .brand-name {
        color: white !important;
        font-weight: 800 !important;
        font-family: 'Sora', sans-serif !important;
        font-size: 14px !important;
        line-height: 1.2 !important;
        letter-spacing: 0.5px !important;
    }

    .brand-name span {
        font-weight: 400 !important;
        opacity: 0.8 !important;
        font-size: 11px !important;
        text-transform: uppercase !important;
    }

    .sidebar-inner {
        padding: 0 10px !important;
    }

    .nav-section-title {
        padding: 24px 20px 8px !important;
        font-size: 11px !important;
        letter-spacing: 1.5px !important;
        color: #94a3b8 !important;
        text-transform: uppercase !important;
        font-weight: 700 !important;
    }

    .nav-item-custom {
        margin: 2px 10px !important;
        padding: 12px 16px !important;
        border-radius: 12px !important;
        transition: all 0.25s ease !important;
        text-decoration: none !important;
        display: flex !important;
        align-items: center !important;
        color: var(--sidebar-text) !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .nav-item-custom i:not(.arrow) {
        font-size: 20px !important;
        margin-right: 14px !important;
        opacity: 0.7 !important;
        transition: all 0.25s ease !important;
    }

    .nav-item-custom:hover {
        background: var(--sidebar-hover-bg) !important;
        color: var(--sidebar-active-color) !important;
        transform: translateX(4px) !important;
    }

    .nav-item-custom:hover i:not(.arrow) {
        opacity: 1 !important;
        transform: scale(1.1) !important;
    }

    .nav-item-custom.active {
        background: white !important;
        color: var(--sidebar-active-color) !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03) !important;
        border: 1px solid rgba(245, 158, 11, 0.1) !important;
    }

    .nav-item-custom.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 20%;
        bottom: 20%;
        width: 4px;
        background: var(--sidebar-active-color);
        border-radius: 0 4px 4px 0;
    }

    .nav-item-custom.active i:not(.arrow) {
        color: var(--sidebar-active-color) !important;
        opacity: 1 !important;
    }

    /* Submenu Tree Style */
    .nav-submenu {
        display: none;
        padding-left: 20px !important;
        margin: 5px 10px !important;
        border-left: 1px dashed #cbd5e1 !important;
        margin-left: 30px !important;
    }

    .nav-submenu.open {
        display: block;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .nav-submenu .nav-item-custom {
        margin: 2px 0 !important;
        padding: 8px 16px !important;
        font-size: 13px !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }

    .nav-submenu .nav-item-custom:hover {
        background: rgba(0,0,0,0.02) !important;
        transform: translateX(4px) !important;
    }

    .nav-submenu .nav-item-custom.active {
        background: white !important;
        border-radius: 8px !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05) !important;
        border: 1px solid rgba(245, 158, 11, 0.1) !important;
    }

    .nav-item-custom.has-sub.open .arrow {
        transform: rotate(90deg) !important;
        color: var(--sidebar-active-color) !important;
    }

    .arrow {
        transition: transform 0.3s !important;
        font-size: 12px !important;
        opacity: 0.5 !important;
    }

    /* Orders Hub Special */
    .orders-hub-icon {
        background: var(--brand-gradient) !important;
        color: white !important;
        width: 32px !important;
        height: 32px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 10px !important;
        margin-right: 12px !important;
        box-shadow: 0 4px 10px -2px rgba(245, 158, 11, 0.4) !important;
    }

    .nav-submenu-orders {
        position: relative;
        margin-left: 30px !important;
        padding-left: 0 !important;
        border-left: 1px dashed #cbd5e1 !important;
    }
    .nav-submenu-orders .nav-item-custom {
        margin: 2px 0 !important;
        padding: 8px 16px !important;
        font-size: 13px !important;
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
    }
    .nav-submenu-orders .nav-item-custom:hover {
        background: rgba(0,0,0,0.02) !important;
        transform: translateX(4px) !important;
    }
    .nav-submenu-orders .nav-item-custom i {
        font-size: 14px !important;
        margin-right: 8px !important;
        opacity: 0.7 !important;
    }
    .nav-submenu-orders .nav-item-custom:hover i {
        opacity: 1 !important;
    }

    /* Custom Scrollbar */
    #sidebar::-webkit-scrollbar {
        width: 5px;
    }
    #sidebar::-webkit-scrollbar-track {
        background: transparent;
    }
    #sidebar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    #sidebar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>

<div id="sidebar-overlay"></div>

<aside id="sidebar">

    {{-- ── Brand ── --}}
    <a class="sidebar-brand" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'manager' ? route('manager.dashboard') : route('employee.dashboard')) }}">
        <div class="brand-icon"><i class="bi bi-person-workspace"></i></div>
        <div class="brand-name">MANAGER<br><span>PORTAL</span></div>
    </a>

    <div class="sidebar-inner">

        {{-- ══════════════ MAIN ══════════════ --}}
        <div class="nav-section-title">Main</div>

        <a class="nav-item-custom {{ request()->routeIs('admin.dashboard') || request()->routeIs('manager.dashboard') || request()->routeIs('employee.dashboard') ? 'active' : '' }}"
           href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'manager' ? route('manager.dashboard') : route('employee.dashboard')) }}">
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
            <a class="nav-item-custom {{ request()->routeIs('admin.orders.incomplete') ? 'active' : '' }}" href="{{ route('admin.orders.incomplete') }}">
                <i class="bi bi-clipboard-x"></i> Incomplete Orders
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.customer-detector.*') ? 'active' : '' }}" href="{{ route('admin.customer-detector.index') }}">
                <i class="bi bi-eye-fill"></i> Customer Detector 🚨
            </a>
            <a class="nav-item-custom" href="{{ route('admin.pointofsalepos.index') }}">
                <i class="bi bi-plus-lg"></i> Create Order
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.orders.staff_assignments') ? 'active' : '' }}" href="{{ route('admin.orders.staff_assignments') }}">
                <i class="bi bi-person-gear"></i> Staff Assignments
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.orders.activity_history') ? 'active' : '' }}" href="{{ route('admin.orders.activity_history') }}">
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
        <a class="nav-item-custom {{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}" href="{{ route('admin.refunds.index') }}">
            <i class="bi bi-arrow-return-left"></i> Refund Management
        </a>
        @endif

        @if(auth()->user()->hasPermission('chat.list'))
        <a class="nav-item-custom {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}" href="{{ route('admin.chat.index') }}">
            <i class="bi bi-chat-left-dots"></i> Conversations
            @php $unreadChats = \App\Models\ChatSession::where('is_read_by_admin', false)->count(); @endphp
            @if($unreadChats > 0)
                <span class="badge bg-danger ms-auto rounded-pill" style="font-size: 10px;">{{ $unreadChats }}</span>
            @endif
        </a>
        @endif

        {{-- ══════════════ FRAUD (Permission Based) ══════════════ --}}
        @if(auth()->user()->hasPermission('fraud.dashboard') || auth()->user()->hasPermission('fraud.rules') || auth()->user()->hasPermission('fraud.alerts') || auth()->user()->hasPermission('fraud.blacklist'))
        <div class="nav-section-title">Fraud</div>

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
        @endif

        {{-- Catalog Section (Protected) --}}
        @if(auth()->user()->hasPermission('product.list'))
        {{-- ══════════════ CATALOG ══════════════ --}}
        <div class="nav-section-title">Catalog</div>

        {{-- Category Management --}}
        @if(auth()->user()->hasPermission('category.list') || auth()->user()->hasPermission('subcategory.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategory.*') ? 'active' : '' }}"
             data-sub="category">
            <i class="bi bi-grid-3x3-gap"></i> Category Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-category">
            @if(auth()->user()->hasPermission('category.list'))
            <a class="nav-item-custom {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}"
               href="{{ route('admin.categories.index') }}">
                <i class="bi bi-dot"></i> All Categories
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}"
               href="{{ route('admin.categories.create') }}">
                <i class="bi bi-dot"></i> Add Category
            </a>
            @endif
            @if(auth()->user()->hasPermission('subcategory.list'))
            <a class="nav-item-custom {{ request()->routeIs('admin.subcategory.index') ? 'active' : '' }}"
               href="{{ route('admin.subcategory.index') }}">
                <i class="bi bi-dot"></i> All SubCategories
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.subcategory.create') ? 'active' : '' }}"
               href="{{ route('admin.subcategory.create') }}">
                <i class="bi bi-dot"></i> Add SubCategory
            </a>
            @endif
        </div>
        @endif

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
            @if(auth()->user()->hasPermission('brand.list'))
            <a class="nav-item-custom {{ request()->routeIs('admin.ourbrands.*') ? 'active' : '' }}"
               href="{{ route('admin.ourbrands.index') }}">
                <i class="bi bi-dot"></i> Our Brand Slider
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

        @if(auth()->user()->hasPermission('shop.list'))
        {{-- ══════════════ SHOP MANAGEMENT ══════════════ --}}
        <div class="nav-section-title">Shop Management</div>

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

        @if(auth()->user()->hasPermission('flash_sale.list') || auth()->user()->hasPermission('promo_code.list') || auth()->user()->hasPermission('banner.list'))
        {{-- Promotion Management --}}
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


        @if(auth()->user()->hasPermission('withdrawal.list'))
        <a class="nav-item-custom {{ request()->routeIs('admin.withdraws.*') ? 'active' : '' }}" href="{{ route('admin.withdraws.index') }}">
            <i class="bi bi-wallet2"></i> Withdrawal Management
        </a>
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
        </div>
        @endif

        {{-- Footer Management --}}
        @if(auth()->user()->hasPermission('landing_page.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.page_categories.*') || request()->routeIs('admin.pages.*') ? 'active' : '' }}"
             data-sub="footer-management">
            <i class="bi bi-layout-text-sidebar-reverse"></i> Footer Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-footer-management">
            <a class="nav-item-custom {{ request()->routeIs('admin.page_categories.*') ? 'active' : '' }}"
               href="{{ route('admin.page_categories.index') }}">
                <i class="bi bi-dot"></i> Footer Category
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}"
               href="{{ route('admin.pages.index') }}">
                <i class="bi bi-dot"></i> Footer Page
            </a>
        </div>
        @endif

        {{-- BD Management --}}
        @if(auth()->user()->hasPermission('third_party.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.settings.gateways') || request()->routeIs('admin.settings.gateways.bd') || request()->routeIs('admin.bkash.*') || request()->routeIs('admin.aamarpay.*') || request()->routeIs('admin.bkash-pay.*') || request()->routeIs('admin.shurjopay.*') || request()->routeIs('admin.sslcommerz.*') ? 'active' : '' }}"
             data-sub="bd-management">
            <i class="bi bi-cash-stack"></i> BD Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-bd-management">
            <a class="nav-item-custom {{ request()->routeIs('admin.settings.gateways.bd') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways.bd') }}">
                <i class="bi bi-dot"></i> BD Gateways
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.bd') }}#bkash">
                <i class="bi bi-dot"></i> BKash
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.bd') }}#nagad">
                <i class="bi bi-dot"></i> Nagad
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.bd') }}#shurjopay">
                <i class="bi bi-dot"></i> Shurjopay
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.bd') }}#sslcommerz">
                <i class="bi bi-dot"></i> SSLCommerz
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.international') }}">
                <i class="bi bi-dot"></i> International Gateways
            </a>
        </div>
        @endif

        {{-- 3rd Party Configuration --}}
        @if(auth()->user()->hasPermission('third_party.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.settings.gateways') || request()->routeIs('admin.settings.gateways.international') || request()->routeIs('admin.stripe.*') || request()->routeIs('admin.paypal.*') || request()->routeIs('admin.razorpay.*') || request()->routeIs('admin.paystack.*') || request()->routeIs('admin.paytabs.*') || request()->routeIs('admin.qicard.*') || request()->routeIs('admin.jazzcash.*') || request()->routeIs('admin.steadfast.*') || request()->routeIs('admin.pathao.*') || request()->routeIs('admin.sms.*') || request()->routeIs('admin.twilio.*') || request()->routeIs('admin.nexmo.*') || request()->routeIs('admin.messagebird.*') || request()->routeIs('admin.mailconfiguration.*') ? 'active' : '' }}"
             data-sub="third-party">
            <i class="bi bi-plug"></i> 3rd Party Configuration
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-third-party">
            <a class="nav-item-custom {{ request()->routeIs('admin.settings.gateways.international') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways.international') }}">
                <i class="bi bi-dot"></i> Payment Gateways
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.international') }}#stripe">
                <i class="bi bi-dot"></i> Stripe
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.international') }}#paypal">
                <i class="bi bi-dot"></i> PayPal
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.international') }}#razorpay">
                <i class="bi bi-dot"></i> Razorpay
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.international') }}#paystack">
                <i class="bi bi-dot"></i> Paystack
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.international') }}#paytabs">
                <i class="bi bi-dot"></i> PayTabs
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.international') }}#qicard">
                <i class="bi bi-dot"></i> QiCard
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways.international') }}#jazzcash">
                <i class="bi bi-dot"></i> JazzCash
            </a>
        </div>
        @endif

        @if(auth()->user()->hasPermission('third_party.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.settings.gateways.international') || request()->routeIs('admin.sms.configuration') || request()->routeIs('admin.mailconfiguration.*') ? 'active' : '' }}"
             data-sub="sms-management">
            <i class="bi bi-chat-left-text"></i> SMS Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-sms-management">
            <a class="nav-item-custom {{ request()->routeIs('admin.settings.gateways.international') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways.international') }}#sms">
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

        {{-- Logout --}}
        <a class="nav-item-custom text-danger mt-2" href="#"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('manager.logout') }}" method="POST" style="display:none;">
            @csrf
        </form>

    </div>{{-- /.sidebar-inner --}}
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

            /* Close every open submenu */
            document.querySelectorAll('.nav-submenu.open').forEach(function (el) {
                el.classList.remove('open');
            });
            document.querySelectorAll('.nav-item-custom.has-sub.open').forEach(function (el) {
                el.classList.remove('open');
            });

            /* Open the clicked one (if it was closed) */
            if (!isOpen) {
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

    /* ── Mobile toggle (called from topbar hamburger button) ── */
    window.sidebarToggle = function () {
        if (!sidebar || !overlay) return;
        var open = sidebar.classList.toggle('show');
        overlay.classList.toggle('show', open);
    };

})();
</script>
