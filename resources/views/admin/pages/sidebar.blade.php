<div id="sidebar-overlay"></div>

<aside id="sidebar">

    {{-- ── Brand ── --}}
    <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">
        <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
        <div class="brand-name">Jhr<br><span>Bazar</span></div>
    </a>

    <div class="sidebar-inner">

        {{-- ══════════════ MAIN ══════════════ --}}
        <div class="nav-section-title">Main</div>

        <a class="nav-item-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           href="{{ route('admin.dashboard') }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>

        <a class="nav-item-custom {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
           href="#">
            <i class="bi bi-bag-check"></i> Order Management
        </a>

        {{-- POS Management --}}
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

        <a class="nav-item-custom" href="#">
            <i class="bi bi-arrow-return-left"></i> Refund Management
        </a>

        <a class="nav-item-custom" href="#">
            <i class="bi bi-chat-left-dots"></i> Conversations
            <span class="badge-count">9+</span>
        </a>

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
        </div>

        {{-- Product Variant Management --}}
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

        {{-- ══════════════ COMMERCE ══════════════ --}}
        <div class="nav-section-title">Commerce</div>

        {{-- Purchase --}}
        <div class="nav-item-custom has-sub" data-sub="purchase">
            <i class="bi bi-cart3"></i> Purchase
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-purchase">
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Purchase Orders</a>
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Suppliers</a>
        </div>

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

        <a class="nav-item-custom" href="#">
            <i class="bi bi-bell"></i> Push Notification
        </a>

        {{-- ══════════════ MANAGEMENT ══════════════ --}}
        <div class="nav-section-title">Management</div>

        {{-- Blog Management --}}
        <div class="nav-item-custom has-sub" data-sub="blog">
            <i class="bi bi-pencil-square"></i> Blog Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-blog">
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> All Posts</a>
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Add Post</a>
        </div>

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

        {{-- Shop Management --}}
        <div class="nav-item-custom has-sub" data-sub="shop">
            <i class="bi bi-shop"></i> Shop Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-shop">
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> All Shops</a>
        </div>

        {{-- ══════════════ SYSTEM ══════════════ --}}
        <div class="nav-section-title">System</div>

        <a class="nav-item-custom" href="#"><i class="bi bi-person-circle"></i> My Profile</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-box"></i> Shop Product Management</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-journal-bookmark"></i> Subscription Management</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-headset"></i> Support Management</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-wallet2"></i> Withdrawal Management</a>
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
        <div class="nav-item-custom has-sub {{ request()->routeIs('admin.pixels.*') || request()->routeIs('admin.shippingcharge.*') ? 'active' : '' }}"
             data-sub="site-settings">
            <i class="bi bi-sliders"></i> Site Settings
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-site-settings">
            <a class="nav-item-custom {{ request()->routeIs('admin.pixels.*') ? 'active' : '' }}"
               href="{{ route('admin.pixels.index') }}">
                <i class="bi bi-dot"></i> Pixels Manage
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.shippingcharge.*') ? 'active' : '' }}"
               href="{{ route('admin.shippingcharge.index') }}">
                <i class="bi bi-dot"></i> Shipping Charge
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

            {{-- Payment Gateways --}}
            <a class="nav-item-custom {{ request()->routeIs('admin.settings.gateways') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}">
                <i class="bi bi-dot"></i> Payment Gateways
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.stripe.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#stripe">
                <i class="bi bi-dot"></i> Stripe
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.paypal.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#paypal">
                <i class="bi bi-dot"></i> PayPal
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.razorpay.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#razorpay">
                <i class="bi bi-dot"></i> Razorpay
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.paystack.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#paystack">
                <i class="bi bi-dot"></i> Paystack
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.aamarpay.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#aamarpay">
                <i class="bi bi-dot"></i> AamarPay
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.bkash.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#bkash">
                <i class="bi bi-dot"></i> BKash
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.paytabs.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#paytabs">
                <i class="bi bi-dot"></i> PayTabs
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.qicard.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#qicard">
                <i class="bi bi-dot"></i> QiCard
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.jazzcash.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#jazzcash">
                <i class="bi bi-dot"></i> JazzCash
            </a>

            {{-- Courier --}}
            <a class="nav-item-custom {{ request()->routeIs('admin.steadfast.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#steadfast">
                <i class="bi bi-dot"></i> Steadfast Courier
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.pathao.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#pathao">
                <i class="bi bi-dot"></i> Pathao Courier
            </a>

            {{-- BD Payment --}}
            <a class="nav-item-custom {{ request()->routeIs('admin.bkash-pay.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#bkash-pay">
                <i class="bi bi-dot"></i> Bkash Payment
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.shurjopay.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#shurjopay">
                <i class="bi bi-dot"></i> Shurjopay
            </a>

            {{-- SMS --}}
            <a class="nav-item-custom {{ request()->routeIs('admin.sms.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.gateways') }}#sms">
                <i class="bi bi-dot"></i> SMS Gateway
            </a>
            <a class="nav-item-custom {{ request()->routeIs('admin.sms.configuration') ? 'active' : '' }}"
               href="{{ route('admin.sms.configuration') }}">
                <i class="bi bi-dot"></i> SMS Configuration Settings
            </a>

            {{-- Mail --}}
            <a class="nav-item-custom {{ request()->routeIs('admin.mailconfiguration.*') ? 'active' : '' }}"
               href="{{ route('admin.mailconfiguration.index') }}">
                <i class="bi bi-dot"></i> Mail Configuration
            </a>
        </div>

        {{-- Contact Us --}}
        <a class="nav-item-custom {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}"
           href="{{ route('admin.contact.index') }}">
            <i class="bi bi-envelope"></i> Contact Us
        </a>

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
