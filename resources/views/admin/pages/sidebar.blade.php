<style>
    #sidebar {
        background: #ffffff !important;
        box-shadow: 10px 0 40px rgba(0,0,0,0.03) !important;
    }
    .sidebar-brand {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        margin: 15px !important;
        border-radius: 15px !important;
        padding: 20px !important;
        border: none !important;
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3) !important;
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
        background: rgba(99, 102, 241, 0.08) !important;
        color: #6366f1 !important;
        font-weight: 700 !important;
        border-left: 4px solid #6366f1 !important;
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
    <a class="sidebar-brand" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'employee' ? route('employee.dashboard') : '#') }}">
        <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
        <div class="brand-name">JHR<br><span>BAZAR</span></div>
    </a>

    <div class="sidebar-inner">

        {{-- ══════════════ MAIN ══════════════ --}}
        <div class="nav-section-title">Main</div>

        <a class="nav-item-custom {{ request()->routeIs('admin.dashboard') || request()->routeIs('employee.dashboard') ? 'active' : '' }}"
           href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('employee.dashboard') }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>

        {{-- Order Management --}}
        @if(auth()->user()->hasPermission('order.list'))
        <a class="nav-item-custom" href="#">
            <i class="bi bi-bag-check"></i> Order Management
        </a>
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

        @if(auth()->user()->hasPermission('order.list'))
        <a class="nav-item-custom" href="#">
            <i class="bi bi-chat-left-dots"></i> Conversations
            <span class="badge-count">9+</span>
        </a>
        @endif

        {{-- ══════════════ FRAUD (Permission Based) ══════════════ --}}
        @if(auth()->user()->hasPermission('order.list')) {{-- Using order.list as a proxy for fraud if fraud_access is missing --}}
        <div class="nav-section-title">Fraud</div>

        <a class="nav-item-custom {{ request()->routeIs('admin.fraud.dashboard') ? 'active' : '' }}"
           href="{{ route('admin.fraud.dashboard') }}">
            <i class="bi bi-shield-exclamation"></i> Fraud Dashboard
        </a>
        @endif

        {{-- Fraud Components (Protected) --}}
        @if(auth()->user()->hasPermission('fraud.list'))
        {{-- Fraud Checks --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.fraud.index') || request()->routeIs('admin.fraud.create') || request()->routeIs('admin.fraud.edit') || request()->routeIs('admin.fraud.show') ? 'active' : '' }}"
             data-sub="fraud-checks">
            <i class="bi bi-search"></i> Fraud Checks
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-fraud-checks">
            <a class="nav-item-custom {{ request()->routeIs('admin.fraud.index') ? 'active' : '' }}"
               href="{{ route('admin.fraud.index') }}">
                <i class="bi bi-dot"></i> All Checks
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.fraud.create') ? 'active' : '' }}"
               href="{{ route('admin.fraud.create') }}">
                <i class="bi bi-dot"></i> Add Check
            </a>
            <a class="nav-item-custom" href="{{ route('admin.fraud.export') }}">
                <i class="bi bi-dot"></i> Export CSV
            </a>
        </div>

        {{-- Fraud Rules --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.fraud.rules.*') ? 'active' : '' }}"
             data-sub="fraud-rules">
            <i class="bi bi-sliders2"></i> Fraud Rules
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-fraud-rules">
            <a class="nav-item-custom {{ request()->routeIs('admin.fraud.rules.index') ? 'active' : '' }}"
               href="{{ route('admin.fraud.rules.index') }}">
                <i class="bi bi-dot"></i> All Rules
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.fraud.rules.create') ? 'active' : '' }}"
               href="{{ route('admin.fraud.rules.create') }}">
                <i class="bi bi-dot"></i> Add Rule
            </a>
        </div>

        {{-- Fraud Alerts --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.fraud.alerts.*') ? 'active' : '' }}"
             data-sub="fraud-alerts">
            <i class="bi bi-bell-fill"></i> Fraud Alerts
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-fraud-alerts">
            <a class="nav-item-custom {{ request()->routeIs('admin.fraud.alerts.index') ? 'active' : '' }}"
               href="{{ route('admin.fraud.alerts.index') }}">
                <i class="bi bi-dot"></i> All Alerts
            </a>
        </div>

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
        @if(auth()->user()->hasPermission('product.list'))
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.productbrands.*') || request()->routeIs('admin.colors.*') || request()->routeIs('admin.sizes.*') || request()->routeIs('admin.units.*') ? 'active' : '' }}"
             data-sub="variant">
            <i class="bi bi-layers"></i> Product Variant Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-variant">
            <a class="nav-item-custom {{ request()->routeIs('admin.productbrands.*') ? 'active' : '' }}"
               href="{{ route('admin.productbrands.index') }}">
                <i class="bi bi-dot"></i> Brand
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}"
               href="{{ route('admin.colors.index') }}">
                <i class="bi bi-dot"></i> Color
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.sizes.*') ? 'active' : '' }}"
               href="{{ route('admin.sizes.index') }}">
                <i class="bi bi-dot"></i> Size
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.units.*') ? 'active' : '' }}"
               href="{{ route('admin.units.index') }}">
                <i class="bi bi-dot"></i> Unit
            </a>
        </div>
        @endif

        {{-- ══════════════ SHOP MANAGEMENT ══════════════ --}}
        <div class="nav-section-title">Shop Management</div>

        @if(auth()->user()->isAdmin())
        <a class="nav-item-custom {{ request()->routeIs('admin.sellers.approvals') ? 'active' : '' }}"
           href="{{ route('admin.sellers.approvals') }}">
            <i class="bi bi-person-check"></i> Seller Approvals
            @php $pendingCount = \App\Models\User::where('role', 'seller')->where('status', 'pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="badge bg-danger ms-auto rounded-pill" style="font-size: 10px;">{{ $pendingCount }}</span>
            @endif
        </a>
        <a class="nav-item-custom {{ request()->routeIs('admin.banks.*') ? 'active' : '' }}"
           href="{{ route('admin.banks.index') }}">
            <i class="bi bi-bank"></i> BankInformation
        </a>
        @endif

        @if(auth()->user()->hasPermission('product.list'))
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
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.flashsale.*') || request()->routeIs('admin.banner.*') || request()->routeIs('admin.promocode.*') ? 'active' : '' }}"
             data-sub="promo">
            <i class="bi bi-gift"></i> Promotion Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-promo">
            <a class="nav-item-custom {{ request()->routeIs('admin.promocode.*') ? 'active' : '' }}"
               href="{{ route('admin.promocode.index') }}">
                <i class="bi bi-dot"></i> Coupons
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.flashsale.*') ? 'active' : '' }}"
               href="{{ route('admin.flashsale.index') }}">
                <i class="bi bi-dot"></i> Flash Sales
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.banner.*') ? 'active' : '' }}"
               href="{{ route('admin.banner.index') }}">
                <i class="bi bi-dot"></i> Banners
            </a>
        </div>

        @if(auth()->user()->hasPermission('profile.list'))
        {{-- ✅ Profile — সরাসরি admin.profile.index রুট ব্যবহার করা হচ্ছে --}}
        <a class="nav-item-custom {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
           href="{{ route('admin.profile.index') }}">
            <i class="bi bi-person-circle"></i> Profile
        </a>
        @endif

        {{-- Management Section (Protected) --}}
        @if(auth()->user()->hasPermission('employee.list'))
        {{-- ══════════════ MANAGEMENT ══════════════ --}}
        <div class="nav-section-title">Management</div>

        {{-- Customer Management --}}
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

        {{-- Unified User Management --}}
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

        {{-- Roles & Permissions --}}
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

        {{-- Employee Management --}}
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

        {{-- Supplier Management --}}
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

        @if(auth()->user()->isAdmin())
        {{-- ══════════════ SYSTEM ══════════════ --}}
        <div class="nav-section-title">System</div>

        <a class="nav-item-custom" href="#"><i class="bi bi-box"></i> Shop Product Management</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-journal-bookmark"></i> Subscription Management</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-headset"></i> Support Management</a>
        <a class="nav-item-custom {{ request()->routeIs('admin.withdraws.*') ? 'active' : '' }}" href="{{ route('admin.withdraws.index') }}">
            <i class="bi bi-wallet2"></i> Withdrawal Management
        </a>
        <a class="nav-item-custom" href="#"><i class="bi bi-arrow-left-right"></i> Import / Export</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-geo-alt"></i> Address</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-translate"></i> Languages</a>

        {{-- Business Settings --}}
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
            <a class="nav-item-custom {{ request()->routeIs('admin.themecolorssettings.*') ? 'active' : '' }}"
               href="{{ route('admin.themecolorssettings.index') }}">
                <i class="bi bi-dot"></i> Theme Colors
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.sociallinkList.*') ? 'active' : '' }}"
               href="{{ route('admin.sociallinkList.index') }}">
                <i class="bi bi-dot"></i> Social Links
            </a>
            <a class="nav-item-custom" href="#">
                <i class="bi bi-dot"></i> Ticket Issue Types
            </a>
        </div>

        {{-- Site Settings --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.pixels.*') || request()->routeIs('admin.googletagmanager.*') || request()->routeIs('admin.shippingcharge.*') || request()->routeIs('admin.duplicateordersetting.*') || request()->routeIs('admin.Ipblockmanage.*') ? 'active' : '' }}"
             data-sub="site-settings">
            <i class="bi bi-sliders"></i> Site Settings
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-site-settings">
            <a class="nav-item-custom {{ request()->routeIs('admin.pixels.*') ? 'active' : '' }}"
               href="{{ route('admin.pixels.index') }}">
                <i class="bi bi-dot"></i> Pixels Manage
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.googletagmanager.*') ? 'active' : '' }}"
               href="{{ route('admin.googletagmanager.index') }}">
                <i class="bi bi-dot"></i> Google Tag Manager
            </a>
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

        {{-- Landing Page Settings --}}
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
            <a class="nav-item-custom {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}"
               href="{{ route('admin.pages.index') }}">
                <i class="bi bi-dot"></i> Page Manage
            </a>
        </div>

        <a class="nav-item-custom" href="#"><i class="bi bi-file-code"></i> CMS</a>

        {{-- 3rd Party Configuration --}}
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
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#steadfast">
                <i class="bi bi-dot"></i> Steadfast Courier
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#pathao">
                <i class="bi bi-dot"></i> Pathao Courier
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#bkash-pay">
                <i class="bi bi-dot"></i> Bkash Payment
            </a>
            <a class="nav-item-custom" href="{{ route('admin.settings.gateways') }}#shurjopay">
                <i class="bi bi-dot"></i> Shurjopay
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

        <a class="nav-item-custom {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}"
           href="{{ route('admin.contact.index') }}">
            <i class="bi bi-envelope"></i> Contact Us
        </a>
        @endif

        {{-- Logout --}}
        <a class="nav-item-custom text-danger mt-2" href="#"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">
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
