<style>
    #sidebar {
        background: #ffffff !important;
        box-shadow: 10px 0 40px rgba(0,0,0,0.03) !important;
    }
    .sidebar-brand {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        margin: 15px !important;
        border-radius: 15px !important;
        padding: 20px !important;
        border: none !important;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3) !important;
    }
    .brand-icon {
        background: rgba(255,255,255,0.2) !important;
        backdrop-filter: blur(5px);
        border-radius: 10px !important;
    }
    .brand-name {
        color: white !important;
        font-weight: 800 !important;
        font-family: 'Sora', sans-serif !important;
    }
    .nav-item-custom {
        margin: 4px 15px !important;
        padding: 12px 18px !important;
        border-radius: 12px !important;
        transition: all 0.3s ease !important;
        text-decoration: none !important;
        display: flex !important;
        align-items: center !important;
    }
    .nav-item-custom:hover {
        background: #f8fafc !important;
        color: #6366f1 !important;
        transform: translateX(5px);
    }
    .nav-item-custom.active {
        background: rgba(16, 185, 129, 0.08) !important;
        color: #10b981 !important;
        font-weight: 700 !important;
        border-left: 4px solid #10b981 !important;
    }
    .nav-section-title {
        padding: 20px 30px 10px !important;
        font-size: 11px !important;
        letter-spacing: 1.5px !important;
        color: #94a3b8 !important;
        text-transform: uppercase !important;
        font-weight: 700 !important;
    }
</style>

<div id="sidebar-overlay"></div>

<aside id="sidebar">

    {{-- ── Brand ── --}}
    <a class="sidebar-brand" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'manager' ? route('manager.dashboard') : route('employee.dashboard')) }}">
        <div class="brand-icon"><i class="bi bi-person-badge-fill"></i></div>
        <div class="brand-name">STAFF<br><span>PORTAL</span></div>
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
            <i class="bi bi-bag-fill" style="color: #3b82f6;"></i>
            <span style="font-weight: 600; margin-left: 8px;">Orders Hub</span>
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-orders-hub">
            <a class="nav-item-custom {{ request()->is('admin/orders/all') ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'all') }}">
                <i class="bi bi-basket"></i> All Orders
            </a>
            <a class="nav-item-custom {{ request()->is('admin/orders/pending') ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'pending') }}">
                <i class="bi bi-hourglass-split"></i> Pending
            </a>
            <a class="nav-item-custom {{ request()->is('admin/orders/processing') ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'processing') }}">
                <i class="bi bi-arrow-repeat"></i> Processing
            </a>
            <a class="nav-item-custom {{ request()->is('admin/orders/shipped') ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'shipped') }}">
                <i class="bi bi-truck"></i> Shipped
            </a>
            <a class="nav-item-custom {{ request()->is('admin/orders/delivered') ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'delivered') }}">
                <i class="bi bi-check-circle"></i> Delivered
            </a>
            <a class="nav-item-custom {{ request()->is('admin/orders/cancelled') ? 'active' : '' }}"
               href="{{ route('admin.orders.index', 'cancelled') }}">
                <i class="bi bi-x-circle"></i> Cancelled
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.orders.incomplete') ? 'active' : '' }}"
               href="{{ route('admin.orders.incomplete') }}">
                <i class="bi bi-clipboard-x"></i> Incomplete Orders
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.customer-detector.*') ? 'active' : '' }}" href="{{ route('admin.customer-detector.index') }}">
                <i class="bi bi-eye-fill"></i> Customer Detector 🚨
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
        @endif

        {{-- Fraud Components (Protected) --}}



        {{-- Blacklist --}}
        @if(auth()->user()->hasPermission('fraud.blacklist'))
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
        @if(auth()->user()->hasPermission('subscription.list'))
        <a class="nav-item-custom" href="#"><i class="bi bi-journal-bookmark"></i> Subscription Management</a>
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
        <form id="logout-form" action="{{ route('employee.logout') }}" method="POST" style="display:none;">
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
