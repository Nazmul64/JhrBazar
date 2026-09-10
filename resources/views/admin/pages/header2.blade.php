<header id="header">
  <button class="header-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
  <div class="header-title">
    <h6>{{ Auth::user()->name }}</h6>
  </div>


  @php
    $lowStockCountNav = \App\Models\Product::where('is_unlimited', false)
        ->where('stock_quantity', '<=', \Illuminate\Support\Facades\DB::raw('COALESCE(low_stock_threshold, 3)'))
        ->count();
    $lowStockProductsNav = \App\Models\Product::where('is_unlimited', false)
        ->where('stock_quantity', '<=', \Illuminate\Support\Facades\DB::raw('COALESCE(low_stock_threshold, 3)'))
        ->take(6)
        ->get();
  @endphp

  {{-- ── Low Stock Alert Dropdown ── --}}
  <div class="dropdown me-2">
    <button class="header-action position-relative border-0 bg-transparent text-dark p-2 rounded-circle" data-bs-toggle="dropdown" aria-expanded="false" title="Low Stock Alerts" style="cursor: pointer;">
        <i class="bi bi-bell-fill fs-5 {{ $lowStockCountNav > 0 ? 'text-warning' : 'text-secondary' }}"></i>
        @if($lowStockCountNav > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 10px; padding: 3px 6px;">
                {{ $lowStockCountNav > 99 ? '99+' : $lowStockCountNav }}
                <span class="visually-hidden">low stock alerts</span>
            </span>
        @endif
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 py-0" style="min-width: 320px; max-width: 360px; border-radius: 14px; overflow: hidden;">
        <li class="px-3 py-2.5 bg-light border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold small text-dark d-flex align-items-center gap-1.5">
                <i class="bi bi-exclamation-triangle-fill text-warning"></i> Low Stock Alerts
            </h6>
            <span class="badge bg-warning-subtle text-warning-emphasis fw-bold" style="font-size: 11px;">
                {{ $lowStockCountNav }} Item{{ $lowStockCountNav === 1 ? '' : 's' }}
            </span>
        </li>
        <div style="max-height: 280px; overflow-y: auto;">
            @forelse($lowStockProductsNav as $p)
                <li>
                    <a class="dropdown-item py-2 px-3 border-bottom d-flex align-items-start gap-2.5" href="{{ route('admin.inventory.index', ['search' => $p->sku ?: $p->name]) }}">
                        <img src="{{ $p->thumbnail ? asset($p->thumbnail) : 'https://placehold.co/36x36?text=No+Img' }}"
                             alt="" style="width: 36px; height: 36px; border-radius: 8px; object-fit: cover; flex-shrink: 0;"
                             onerror="this.src='https://placehold.co/36x36?text=No+Img'">
                        <div style="min-width: 0; flex: 1;">
                            <div class="fw-semibold text-truncate small text-dark">{{ $p->name }}</div>
                            <div class="small {{ $p->stock_quantity <= 0 ? 'text-danger' : 'text-warning-emphasis' }} fw-medium">
                                @if($p->stock_quantity <= 0)
                                    <i class="bi bi-x-circle-fill"></i> Out of stock (0 items left)
                                @else
                                    <i class="bi bi-exclamation-circle-fill"></i> Only <strong>{{ $p->stock_quantity }}</strong> left (≤ {{ $p->low_stock_threshold }})
                                @endif
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="py-4 text-center text-muted small">
                    <i class="bi bi-check-circle-fill text-success fs-4 d-block mb-1"></i>
                    All products have sufficient stock!
                </li>
            @endforelse
        </div>
        @if($lowStockCountNav > 0)
            <li class="p-2 text-center bg-light border-top">
                <a href="{{ route('admin.inventory.index', ['status' => 'low_stock']) }}" class="small fw-semibold text-primary text-decoration-none d-block py-1">
                    View All Stock in Ledger <i class="bi bi-arrow-right"></i>
                </a>
            </li>
        @endif
    </ul>
  </div>

  <button class="header-action" id="darkToggle" title="Toggle Theme">
    <i class="bi {{ (settings() && settings()->admin_theme === 'dark') ? 'bi-sun-fill' : 'bi-moon-stars-fill' }}"></i>
  </button>
  <button class="lang-btn">
    <i class="bi bi-globe2"></i>
    <span>English</span>
    <i class="bi bi-chevron-down" style="font-size:10px;"></i>
  </button>
  <div class="avatar-wrap dropdown">
    <div class="d-flex align-items-center gap-2 cursor-pointer dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
        <div class="avatar">
            @if(Auth::user()->profile_image)
                <img src="{{ asset(Auth::user()->profile_image) }}" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
            @else
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            @endif
        </div>
        <div class="avatar-info d-none d-md-block">
            <span class="name">{{ Auth::user()->name }}</span>
            <span class="role">{{ ucfirst(Auth::user()->role) }}</span>
        </div>
    </div>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 py-2" style="min-width: 200px; border-radius: 12px;">
        <li class="px-3 py-2 border-bottom mb-2">
            <h6 class="mb-0 fw-bold">{{ Auth::user()->name }}</h6>
            <small class="text-muted">{{ Auth::user()->email }}</small>
        </li>
        <li>
            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="{{ route('admin.profile.edit') }}">
                <i class="bi bi-person text-primary"></i> Profile
            </a>
        </li>

        <li>
            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="{{ route('admin.profile.index') }}#change-password">
                <i class="bi bi-shield-lock text-warning"></i> Password
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <form action="{{
                Auth::user()->role === 'admin' ? route('admin.logout') :
                (Auth::user()->role === 'manager' ? route('manager.logout') :
                (Auth::user()->role === 'seller' ? route('seller.logout') : route('employee.logout')))
            }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 text-danger">
                    <i class="bi bi-box-arrow-right"></i> Sign Out
                </button>
            </form>
        </li>
    </ul>
  </div>
</header>
