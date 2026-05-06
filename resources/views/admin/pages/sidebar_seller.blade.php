<style>
    #sidebar {
        background: #ffffff !important;
        width: 260px !important;
    }
    .sidebar-brand {
        padding: 20px !important;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 10px !important;
        background: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
    }
    .brand-name {
        color: #334155 !important;
        font-size: 1.2rem !important;
    }
    .nav-item-custom {
        margin: 5px 15px !important;
        padding: 10px 15px !important;
        border-radius: 8px !important;
        color: #64748b !important;
        font-weight: 500 !important;
        border: none !important;
    }
    .nav-item-custom:hover {
        background: #f8fafc !important;
        color: #ef4444 !important;
    }
    .nav-item-custom.active {
        background: #fff1f2 !important; /* Pinkish background */
        color: #f43f5e !important;    /* Pinkish text */
        border: none !important;
    }
    .nav-item-custom i {
        font-size: 18px !important;
        margin-right: 12px !important;
    }
    .nav-submenu {
        background: #fdfdfd !important;
        margin-left: 20px !important;
        display: none; /* Hidden by default */
    }
    .nav-submenu.open {
        display: block !important; /* Show when open */
    }
    .nav-item-custom.has-sub .bi-chevron-down {
        transition: transform 0.3s ease;
    }
    .nav-item-custom.has-sub.open .bi-chevron-down {
        transform: rotate(180deg);
    }
    .sidebar-footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 15px 20px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
    }
    .footer-icon {
        color: #64748b;
        font-size: 18px;
        cursor: pointer;
    }
    .footer-icon:hover { color: #f43f5e; }
    .sidebar-inner { height: calc(100% - 150px); overflow-y: auto; }
</style>

<div id="sidebar-overlay"></div>

<aside id="sidebar">

    <div class="sidebar-brand">
        <div class="brand-name fw-bold">SELLER <span class="text-danger">PORTAL</span></div>
    </div>

    <div class="sidebar-inner">

        <a class="nav-item-custom {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}"
           href="{{ route('seller.dashboard') }}">
            <i class="bi bi-house-door-fill"></i> Dashboard
        </a>

        {{-- All Orders --}}
        <div class="nav-item-custom has-sub" data-sub="orders">
            <i class="bi bi-cart3"></i> All Orders
            <i class="bi bi-chevron-down ms-auto small"></i>
        </div>
        <div class="nav-submenu" id="sub-orders">
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Pending Orders</a>
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Confirmed Orders</a>
        </div>

        {{-- POS Management --}}
        <div class="nav-item-custom has-sub" data-sub="pos">
            <i class="bi bi-display"></i> POS Management
            <i class="bi bi-chevron-down ms-auto small"></i>
        </div>

        {{-- Refund Management --}}
        <div class="nav-item-custom has-sub" data-sub="refund">
            <i class="bi bi-arrow-return-left"></i> Refund Management
            <i class="bi bi-chevron-down ms-auto small"></i>
        </div>

        {{-- Messages --}}
        <a class="nav-item-custom" href="#">
            <i class="bi bi-chat-left-dots"></i> Messages
        </a>

        {{-- Category Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.categories.*') || request()->routeIs('seller.subcategories.*') || request()->routeIs('seller.childcategories.*') ? 'active' : '' }}" 
             data-sub="category">
            <i class="bi bi-grid-3x3-gap"></i> Category Management
            <i class="bi bi-chevron-down ms-auto small"></i>
        </div>
        <div class="nav-submenu" id="sub-category">
            <a class="nav-item-custom {{ request()->routeIs('seller.categories.index') ? 'active' : '' }}" 
               href="{{ route('seller.categories.index') }}">
                <i class="bi bi-dot"></i> Category
            </a>
            <a class="nav-item-custom {{ request()->routeIs('seller.subcategories.index') ? 'active' : '' }}" 
               href="{{ route('seller.subcategories.index') }}">
                <i class="bi bi-dot"></i> Sub Category
            </a>
            <a class="nav-item-custom {{ request()->routeIs('seller.childcategories.index') ? 'active' : '' }}" 
               href="{{ route('seller.childcategories.index') }}">
                <i class="bi bi-dot"></i> Child Category
            </a>
        </div>

        {{-- Product Management --}}
        <div class="nav-item-custom has-sub" data-sub="product">
            <i class="bi bi-box-seam"></i> Product Management
            <i class="bi bi-chevron-down ms-auto small"></i>
        </div>

        {{-- Product Variant --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.brands.*') || request()->routeIs('seller.colors.*') || request()->routeIs('seller.sizes.*') || request()->routeIs('seller.units.*') ? 'active' : '' }}" 
             data-sub="variant">
            <i class="bi bi-layers"></i> Product Variant Management
            <i class="bi bi-chevron-down ms-auto small"></i>
        </div>
        <div class="nav-submenu" id="sub-variant">
            <a class="nav-item-custom {{ request()->routeIs('seller.brands.index') ? 'active' : '' }}" 
               href="{{ route('seller.brands.index') }}">
                <i class="bi bi-dot"></i> Brand
            </a>
            <a class="nav-item-custom {{ request()->routeIs('seller.colors.index') ? 'active' : '' }}" 
               href="{{ route('seller.colors.index') }}">
                <i class="bi bi-dot"></i> Color
            </a>
            <a class="nav-item-custom {{ request()->routeIs('seller.sizes.index') ? 'active' : '' }}" 
               href="{{ route('seller.sizes.index') }}">
                <i class="bi bi-dot"></i> Size
            </a>
            <a class="nav-item-custom {{ request()->routeIs('seller.units.index') ? 'active' : '' }}" 
               href="{{ route('seller.units.index') }}">
                <i class="bi bi-dot"></i> Unit
            </a>
        </div>

        <a class="nav-item-custom" href="#">
            <i class="bi bi-journal-text"></i> Purchase
            <i class="bi bi-gift ms-auto small"></i>
        </a>

        {{-- Promotion Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.flashsales.*') || request()->routeIs('seller.promocode.*') || request()->routeIs('seller.banner.*') ? 'active' : '' }}" data-sub="promo">
            <i class="bi bi-megaphone"></i> Promotion Management
            <i class="bi bi-chevron-down ms-auto small"></i>
        </div>
        <div class="nav-submenu" id="sub-promo">
            <a class="nav-item-custom {{ request()->routeIs('seller.flashsales.index') || request()->routeIs('seller.flashsales.show') ? 'active' : '' }}"
               href="{{ route('seller.flashsales.index') }}">
                <i class="bi bi-dot"></i> Flash Deals
            </a>
            <a class="nav-item-custom {{ request()->routeIs('seller.banner.*') ? 'active' : '' }}"
               href="{{ route('seller.banner.index') }}">
                <i class="bi bi-dot"></i> Banner Setup
            </a>
            <a class="nav-item-custom {{ request()->routeIs('seller.promocode.*') ? 'active' : '' }}"
               href="{{ route('seller.promocode.index') }}">
                <i class="bi bi-dot"></i> Promo Code
            </a>
        </div>

        {{-- Employee Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.employeeseller.*') ? 'active' : '' }}" data-sub="employee">
            <i class="bi bi-person-badge"></i> Employee Management
            <i class="bi bi-chevron-down ms-auto small"></i>
        </div>
        <div class="nav-submenu" id="sub-employee">
            <a class="nav-item-custom {{ request()->routeIs('seller.employeeseller.index') ? 'active' : '' }}"
               href="{{ route('seller.employeeseller.index') }}">
                <i class="bi bi-dot"></i> List Of Employees
            </a>
            <a class="nav-item-custom {{ request()->routeIs('seller.employeeseller.create') ? 'active' : '' }}"
               href="{{ route('seller.employeeseller.create') }}">
                <i class="bi bi-dot"></i> Add New Employee
            </a>
        </div>

        {{-- Suppliers --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.supplier.*') ? 'active' : '' }}" data-sub="suppliers">
            <i class="bi bi-box"></i> Suppliers
            <i class="bi bi-gift ms-auto small text-danger me-2"></i>
            <i class="bi bi-chevron-down small"></i>
        </div>
        <div class="nav-submenu" id="sub-suppliers">
            <a class="nav-item-custom {{ request()->routeIs('seller.supplier.index') ? 'active' : '' }}" 
               href="{{ route('seller.supplier.index') }}">
                List Of Suppliers
            </a>
            <a class="nav-item-custom {{ request()->routeIs('seller.supplier.create') ? 'active' : '' }}" 
               href="{{ route('seller.supplier.create') }}">
                Add New Supplier
            </a>
        </div>

        <a class="nav-item-custom" href="#">
            <i class="bi bi-shop"></i> My Shop
        </a>

        <a class="nav-item-custom" href="#">
            <i class="bi bi-wallet2"></i> Withdraws
        </a>

        <div class="nav-item-custom has-sub" data-sub="import">
            <i class="bi bi-download"></i> Import/Export
            <i class="bi bi-chevron-down ms-auto small"></i>
        </div>

    </div>{{-- /.sidebar-inner --}}

    <div class="sidebar-footer">
        <i class="bi bi-fullscreen footer-icon"></i>
        <i class="bi bi-person-circle footer-icon"></i>
        <i class="bi bi-box-arrow-right footer-icon text-danger" onclick="document.getElementById('logout-form').submit();"></i>
        <span class="small text-muted">4.2.5</span>
    </div>

    <form id="logout-form" action="{{ route('seller.logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
</aside>

{{-- ══ JavaScript ══ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /* ── Submenu toggle ── */
    document.querySelectorAll('.nav-item-custom.has-sub').forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent default if it's an <a> tag
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

});
</script>
