<!-- Sidebar overlay (mobile backdrop) -->
<div id="sidebar-overlay"></div>

<aside id="sidebar">

    {{-- Brand --}}
    <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">
        <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
        <div class="brand-name">Jhr<br><span>Bazar</span></div>
    </a>

    <div class="pt-1 pb-3 flex-grow-1">

        {{-- ─────────── MAIN ─────────── --}}
        <div class="nav-section-title">Main</div>

        <a class="nav-item-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           href="{{ route('admin.dashboard') }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>

        <a class="nav-item-custom" href="#">
            <i class="bi bi-bag-check"></i> Order Management
        </a>

        <div class="nav-item-custom has-sub" data-sub="pos">
            <i class="bi bi-display"></i> POS Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-pos">
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> POS Orders</a>
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> POS Settings</a>
        </div>

        <a class="nav-item-custom" href="#">
            <i class="bi bi-arrow-return-left"></i> Refund Management
        </a>

        <a class="nav-item-custom" href="#">
            <i class="bi bi-chat-left-dots"></i> Conversations
            <span class="badge-count">9+</span>
        </a>

        {{-- ─────────── CATALOG ─────────── --}}
        <div class="nav-section-title">Catalog</div>

        <div class="nav-item-custom has-sub" data-sub="category">
            <i class="bi bi-grid-3x3-gap"></i> Category Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-category">
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> All Categories</a>
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Add Category</a>
        </div>

        <div class="nav-item-custom has-sub" data-sub="product">
            <i class="bi bi-box-seam"></i> Product Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-product">
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> All Products</a>
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Add Product</a>
        </div>

        <a class="nav-item-custom" href="#">
            <i class="bi bi-layers"></i> Product Variant Management
        </a>

        {{-- ─────────── COMMERCE ─────────── --}}
        <div class="nav-section-title">Commerce</div>

        <div class="nav-item-custom has-sub" data-sub="purchase">
            <i class="bi bi-cart3"></i> Purchase
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-purchase">
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Purchase Orders</a>
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Suppliers</a>
        </div>

        <div class="nav-item-custom has-sub" data-sub="promo">
            <i class="bi bi-gift"></i> Promotion Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-promo">
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Coupons</a>
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Flash Sales</a>
        </div>

        <a class="nav-item-custom" href="#">
            <i class="bi bi-bell"></i> Push Notification
        </a>

        {{-- ─────────── MANAGEMENT ─────────── --}}
        <div class="nav-section-title">Management</div>

        <div class="nav-item-custom has-sub" data-sub="blog">
            <i class="bi bi-pencil-square"></i> Blog Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-blog">
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> All Posts</a>
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> Add Post</a>
        </div>

        {{-- Customer Management — ✅ route exists --}}
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

        {{-- Roles & Permissions — ✅ route exists --}}
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

        {{-- Employee Management — ✅ route exists --}}
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

        {{-- Supplier Management — ✅ route exists --}}
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

        <div class="nav-item-custom has-sub" data-sub="shop">
            <i class="bi bi-shop"></i> Shop Management
            <i class="bi bi-chevron-right arrow ms-auto"></i>
        </div>
        <div class="nav-submenu" id="sub-shop">
            <a class="nav-item-custom" href="#"><i class="bi bi-dot"></i> All Shops</a>
        </div>

        {{-- ─────────── SYSTEM ─────────── --}}
        <div class="nav-section-title">System</div>

        <a class="nav-item-custom" href="#"><i class="bi bi-person-circle"></i> My Profile</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-box"></i> Shop Product Management</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-journal-bookmark"></i> Subscription Management</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-headset"></i> Support Management</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-wallet2"></i> Withdrawal Management</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-arrow-left-right"></i> Import / Export</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-geo-alt"></i> Address</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-translate"></i> Languages</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-gear"></i> Business Settings</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-file-code"></i> CMS</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-plug"></i> 3rd Party Configuration</a>
        <a class="nav-item-custom" href="#"><i class="bi bi-envelope"></i> Contact Us</a>

        {{-- Logout — ✅ route exists --}}
        <a class="nav-item-custom text-danger" href="#"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">
            @csrf
        </form>

    </div>
</aside>
