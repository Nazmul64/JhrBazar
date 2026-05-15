<style>
    #sidebar {
        background: #ffffff !important;
        width: 280px !important;
        border-right: 1px solid #f1f5f9;
        transition: all 0.3s ease;
        box-shadow: 4px 0 24px rgba(0,0,0,0.02);
    }
    .sidebar-brand {
        padding: 25px 24px !important;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 10px !important;
    }
    .brand-name {
        color: #0f172a !important;
        font-size: 1.25rem !important;
        letter-spacing: -0.5px;
    }
    .sidebar-inner { 
        height: calc(100% - 100px); 
        overflow-y: auto;
        padding: 10px 0 80px 0;
    }
    .sidebar-inner::-webkit-scrollbar { width: 4px; }
    .sidebar-inner::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

    .nav-item-custom {
        display: flex;
        align-items: center;
        margin: 4px 16px !important;
        padding: 12px 16px !important;
        border-radius: 12px !important;
        color: #475569 !important;
        font-weight: 500 !important;
        font-size: 14px !important;
        text-decoration: none !important;
        transition: all 0.2s ease;
        border: none !important;
        cursor: pointer;
    }
    .nav-item-custom:hover {
        background: #f8fafc !important;
        color: #e11d48 !important;
    }
    .nav-item-custom i:not(.ms-auto) {
        font-size: 1.2rem !important;
        margin-right: 12px !important;
        color: #94a3b8;
    }
    .nav-item-custom.active {
        background: #fff1f2 !important;
        color: #e11d48 !important;
        font-weight: 600 !important;
    }
    .nav-item-custom.active i:not(.ms-auto) {
        color: #e11d48 !important;
    }

    .nav-submenu {
        display: none;
        padding-left: 35px !important;
        margin-bottom: 10px;
        position: relative;
    }
    .nav-submenu.open {
        display: block !important;
    }
    
    .nav-submenu .nav-item-custom {
        margin: 2px 10px 2px 5px !important;
        padding: 10px 15px !important;
        font-size: 13px !important;
        background: #f8fafc !important;
        border-radius: 8px !important;
        position: relative;
    }
    .nav-submenu::before {
        content: '';
        position: absolute;
        left: 28px;
        top: 0;
        bottom: 20px;
        width: 1px;
        background: #e2e8f0;
    }
    .nav-submenu .nav-item-custom::before {
        content: '';
        position: absolute;
        left: -12px;
        top: 50%;
        width: 12px;
        height: 1px;
        background: #e2e8f0;
    }

    .nav-item-custom.has-sub .bi-chevron-down {
        transition: transform 0.3s ease;
        font-size: 11px !important;
        color: #94a3b8;
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
        justify-content: space-around;
        background: #fff;
        z-index: 100;
    }
    .footer-icon {
        color: #64748b;
        font-size: 18px;
        cursor: pointer;
        padding: 8px;
        border-radius: 8px;
    }
    .footer-icon:hover { color: #e11d48; background: #fff1f2; }

    /* ── Orders Hub Custom Styles for Seller ── */
    .orders-hub-icon {
        background: #3b82f6 !important;
        color: white !important;
        width: 30px !important;
        height: 30px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 8px !important;
        margin-right: 12px !important;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3) !important;
    }
    .nav-item-custom.has-sub.open .orders-hub-icon {
        background: #2563eb !important;
    }
    .nav-submenu-orders {
        position: relative;
        margin-left: 30px !important;
        padding-left: 0 !important;
        border-left: 1px solid #e2e8f0 !important;
        display: none;
    }
    .nav-submenu-orders.open {
        display: block !important;
    }
    .nav-submenu-orders .nav-item-custom {
        margin: 2px 10px 2px 0 !important;
        padding: 8px 15px !important;
        font-size: 13px !important;
        background: transparent !important;
        color: #64748b !important;
    }
    .nav-submenu-orders .nav-item-custom::before {
        content: '';
        display: inline-block;
        width: 12px;
        height: 1px;
        background: #e2e8f0;
        margin-right: 10px;
        vertical-align: middle;
    }
    .nav-submenu-orders .nav-item-custom:hover {
        color: #3b82f6 !important;
        transform: translateX(3px) !important;
    }
    .nav-submenu-orders .nav-item-custom i {
        font-size: 14px !important;
        margin-right: 8px !important;
        color: #94a3b8 !important;
    }
    .nav-submenu-orders .nav-item-custom:hover i {
        color: #3b82f6 !important;
    }
</style>

<aside id="sidebar">
    <div class="sidebar-brand text-center">
        <div class="brand-name fw-bold">SELLER <span class="text-danger">PORTAL</span></div>
    </div>

    <div class="sidebar-inner">
        {{-- Dashboard --}}
        <a class="nav-item-custom {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}" href="{{ route('seller.dashboard') }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>

        {{-- My Profile --}}
        <a class="nav-item-custom {{ request()->routeIs('seller.profile.*') ? 'active' : '' }}" href="{{ route('seller.profile.index') }}">
            <i class="bi bi-person-bounding-box"></i> My Profile
        </a>

        {{-- Orders Hub --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.orders.*') ? 'active' : '' }}" data-sub="orders-hub">
            <div class="orders-hub-icon">
                <i class="bi bi-bag-fill" style="margin-right: 0 !important; color: white !important;"></i>
            </div>
            <span style="font-weight: 600;">Orders Hub</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>
        <div class="nav-submenu nav-submenu-orders" id="sub-orders-hub">
            <a class="nav-item-custom" href="{{ route('seller.orders.index', 'all') }}">
                <i class="bi bi-basket"></i> All Orders
            </a>
            <a class="nav-item-custom" href="{{ route('seller.orders.index', 'pending') }}">
                <i class="bi bi-hourglass-split"></i> Pending
            </a>
            <a class="nav-item-custom" href="{{ route('seller.orders.index', 'processing') }}">
                <i class="bi bi-arrow-repeat"></i> Processing
            </a>
            <a class="nav-item-custom" href="{{ route('seller.orders.index', 'shipped') }}">
                <i class="bi bi-truck"></i> Shipped
            </a>
            <a class="nav-item-custom" href="{{ route('seller.orders.index', 'delivered') }}">
                <i class="bi bi-check-circle"></i> Delivered
            </a>
            <a class="nav-item-custom" href="{{ route('seller.orders.index', 'cancelled') }}">
                <i class="bi bi-x-circle"></i> Cancelled
            </a>
            <a class="nav-item-custom" href="{{ route('seller.pos.index') }}">
                <i class="bi bi-plus-lg"></i> Create Order
            </a>
            <a class="nav-item-custom" href="#">
                <i class="bi bi-person-gear"></i> Staff Assignments
            </a>
            <a class="nav-item-custom" href="#">
                <i class="bi bi-clock-history"></i> Activity History
            </a>
        </div>

        {{-- POS Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.pos.*') ? 'active' : '' }}" data-sub="pos">
            <i class="bi bi-pc-display-horizontal"></i> POS Management
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-pos">
            <a class="nav-item-custom {{ request()->routeIs('seller.pos.index') ? 'active' : '' }}" href="{{ route('seller.pos.index') }}">POS</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.customers.*') ? 'active' : '' }}" href="{{ route('seller.customers.index') }}">Manage Customers</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.pos.sales-history') ? 'active' : '' }}" href="{{ route('seller.pos.sales-history') }}">POS Sales History</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.pos.drafts') ? 'active' : '' }}" href="{{ route('seller.pos.drafts') }}">POS Draft</a>
        </div>

        {{-- Refund Management --}}
        <div class="nav-item-custom has-sub" data-sub="refund">
            <i class="bi bi-arrow-return-left"></i> Refund Management
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-refund">
            <a class="nav-item-custom" href="#">Refund Requests</a>
        </div>

        {{-- Messages --}}
        <a class="nav-item-custom {{ request()->routeIs('seller.messages.*') ? 'active' : '' }}" href="{{ route('seller.messages.index') }}">
            <i class="bi bi-chat-left-dots-fill"></i> Messages
        </a>

        {{-- Admin Chat --}}
        <a class="nav-item-custom {{ request()->routeIs('seller.admin_chat.*') ? 'active' : '' }}" href="{{ route('seller.admin_chat.index') }}">
            <i class="bi bi-headset"></i> Chat with Admin
            @php 
                $unreadAdminMsg = \App\Models\ChatSession::where('user_id', auth()->id())
                    ->where('is_read_by_user', false)
                    ->where(function($q) { $q->whereNull('receiver_id')->orWhere('receiver_id', 0); })
                    ->count();
            @endphp
            @if($unreadAdminMsg > 0)
                <span class="badge bg-danger ms-auto rounded-pill" style="font-size: 10px;">{{ $unreadAdminMsg }}</span>
            @endif
        </a>

        {{-- Category Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.categories.*') || request()->routeIs('seller.subcategories.*') ? 'active' : '' }}" data-sub="category">
            <i class="bi bi-layers-fill"></i> Category Management
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-category">
            <a class="nav-item-custom {{ request()->routeIs('seller.categories.index') ? 'active' : '' }}" href="{{ route('seller.categories.index') }}">Category</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.subcategories.index') ? 'active' : '' }}" href="{{ route('seller.subcategories.index') }}">Sub Category</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.childcategories.index') ? 'active' : '' }}" href="{{ route('seller.childcategories.index') }}">Child Category</a>
        </div>

        {{-- Product Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.product.*') ? 'active' : '' }}" data-sub="product">
            <i class="bi bi-box-seam-fill"></i> Product Management
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-product">
            <a class="nav-item-custom {{ request()->routeIs('seller.product.index') ? 'active' : '' }}" href="{{ route('seller.product.index') }}">All Product</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.product.create') ? 'active' : '' }}" href="{{ route('seller.product.create') }}">Add Product</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.digital_product.index') ? 'active' : '' }}" href="{{ route('seller.digital_product.index') }}">All Digital Product</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.digital_product.create') ? 'active' : '' }}" href="{{ route('seller.digital_product.create') }}">Add Digital Product</a>
        </div>

        {{-- Product Variant --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.brands.*') || request()->routeIs('seller.colors.*') || request()->routeIs('seller.sizes.*') || request()->routeIs('seller.units.*') ? 'active' : '' }}" data-sub="variant">
            <i class="bi bi-tags-fill"></i> Product Variant
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-variant">
            <a class="nav-item-custom {{ request()->routeIs('seller.brands.index') ? 'active' : '' }}" href="{{ route('seller.brands.index') }}">Brand</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.colors.index') ? 'active' : '' }}" href="{{ route('seller.colors.index') }}">Color</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.sizes.index') ? 'active' : '' }}" href="{{ route('seller.sizes.index') }}">Size</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.units.index') ? 'active' : '' }}" href="{{ route('seller.units.index') }}">Unit</a>
        </div>

        {{-- Purchase Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.purchase.*') ? 'active' : '' }}" data-sub="purchase">
            <i class="bi bi-bag-check-fill"></i> 
            <span class="me-auto ms-2">Purchase</span>
            <i class="bi bi-gift text-primary me-2"></i>
            <i class="bi bi-chevron-down"></i>
        </div>
        <div class="nav-submenu" id="sub-purchase">
            <a class="nav-item-custom {{ request()->routeIs('seller.purchase.stock-report') ? 'active' : '' }}" href="{{ route('seller.purchase.stock-report') }}">Stock Report</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.purchase.create') ? 'active' : '' }}" href="{{ route('seller.purchase.create') }}">Add New Purchase</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.purchase.index') ? 'active' : '' }}" href="{{ route('seller.purchase.index') }}">Purchase Invoices</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.purchase.summary') ? 'active' : '' }}" href="{{ route('seller.purchase.summary') }}">Purchase Summary</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.purchase.returns') ? 'active' : '' }}" href="{{ route('seller.purchase.returns') }}">Purchase Returns</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.purchase.return-create') ? 'active' : '' }}" href="{{ route('seller.purchase.return-create') }}">Add Purchase Return</a>
        </div>

        {{-- Promotion Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.flashsales.*') || request()->routeIs('seller.promocode.*') || request()->routeIs('seller.banner.*') ? 'active' : '' }}" data-sub="promo">
            <i class="bi bi-megaphone-fill"></i> Promotion Management
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-promo">
            <a class="nav-item-custom {{ request()->routeIs('seller.flashsales.*') ? 'active' : '' }}" href="{{ route('seller.flashsales.index') }}">Flash Deals</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.banner.*') ? 'active' : '' }}" href="{{ route('seller.banner.index') }}">Banner Setup</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.promocode.*') ? 'active' : '' }}" href="{{ route('seller.promocode.index') }}">Promo Code</a>
        </div>

        {{-- Employee Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.employeeseller.*') ? 'active' : '' }}" data-sub="employee">
            <i class="bi bi-people-fill"></i> Employee Management
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-employee">
            <a class="nav-item-custom {{ request()->routeIs('seller.employeeseller.index') ? 'active' : '' }}" href="{{ route('seller.employeeseller.index') }}">List Of Employees</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.employeeseller.create') ? 'active' : '' }}" href="{{ route('seller.employeeseller.create') }}">Add New Employee</a>
        </div>

        {{-- Suppliers --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.supplier.*') ? 'active' : '' }}" data-sub="suppliers">
            <i class="bi bi-truck"></i> Suppliers
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-suppliers">
            <a class="nav-item-custom {{ request()->routeIs('seller.supplier.index') ? 'active' : '' }}" href="{{ route('seller.supplier.index') }}">List Of Suppliers</a>
            <a class="nav-item-custom {{ request()->routeIs('seller.supplier.create') ? 'active' : '' }}" href="{{ route('seller.supplier.create') }}">Add New Supplier</a>
        </div>

        {{-- My Shop --}}
        <a class="nav-item-custom {{ request()->routeIs('seller.shop.*') ? 'active' : '' }}" href="{{ route('seller.shop.index') }}">
            <i class="bi bi-shop-fill"></i> My Shop
        </a>

        {{-- Customer Management --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.customers.*') && !request()->routeIs('seller.pos.*') ? 'active' : '' }}" data-sub="customers">
            <i class="bi bi-person-heart"></i> Customer Management
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-customers">
            <a class="nav-item-custom" href="{{ route('seller.customers.index') }}">List Of Customers</a>
            <a class="nav-item-custom" href="{{ route('seller.customers.create') }}">Add New Customer</a>
        </div>

        {{-- Withdraws --}}
        <a class="nav-item-custom {{ request()->routeIs('seller.withdraws.*') ? 'active' : '' }}" href="{{ route('seller.withdraws.index') }}">
            <i class="bi bi-wallet-fill"></i> Withdraws
        </a>

        {{-- Import/Export --}}
        <div class="nav-item-custom has-sub {{ request()->routeIs('seller.import-export.*') ? 'active' : '' }}" data-sub="import">
            <i class="bi bi-file-earmark-arrow-up-fill"></i> Import/Export
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-import">
            <a class="nav-item-custom" href="{{ route('seller.import-export.product-export') }}">Product Export</a>
            <a class="nav-item-custom" href="{{ route('seller.import-export.product-import') }}">Product Import</a>
            <a class="nav-item-custom" href="{{ route('seller.import-export.gallery-import') }}">Gallery Import</a>
        </div>

    </div>

    <div class="sidebar-footer">
        <i class="bi bi-fullscreen footer-icon"></i>
        <a href="{{ route('seller.profile.index') }}"><i class="bi bi-person-circle footer-icon"></i></a>
        <i class="bi bi-box-arrow-right footer-icon text-danger" onclick="document.getElementById('logout-form').submit();"></i>
    </div>
    <form id="logout-form" action="{{ route('seller.logout') }}" method="POST" style="display:none;">@csrf</form>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    document.querySelectorAll('.nav-item-custom.has-sub').forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            var key     = this.dataset.sub;
            var submenu = document.getElementById('sub-' + key);
            if (!submenu) return;

            var isOpen = submenu.classList.contains('open');

            // Accordion: Close others
            document.querySelectorAll('.nav-submenu.open').forEach(function (el) {
                if (el !== submenu) {
                    el.classList.remove('open');
                    var otherTrigger = document.querySelector('.nav-item-custom.has-sub.open[data-sub="' + el.id.replace('sub-', '') + '"]');
                    if (otherTrigger) otherTrigger.classList.remove('open');
                }
            });

            if (isOpen) {
                submenu.classList.remove('open');
                this.classList.remove('open');
            } else {
                submenu.classList.add('open');
                this.classList.add('open');
            }
        });
    });

    // Auto-open active path
    const activeChild = document.querySelector('.nav-submenu .nav-item-custom.active');
    if (activeChild) {
        const submenu = activeChild.closest('.nav-submenu');
        if (submenu) {
            submenu.classList.add('open');
            const key = submenu.id.replace('sub-', '');
            const trigger = document.querySelector('.nav-item-custom.has-sub[data-sub="' + key + '"]');
            if (trigger) trigger.classList.add('open');
        }
    }
});
</script>
