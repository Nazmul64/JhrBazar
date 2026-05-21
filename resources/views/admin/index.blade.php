@extends('admin.master')
@section('content')
<script>
    window.adminChartMonths = @json($chartMonths);
    window.adminChartData = @json($chartOrderData);
    window.adminUserOverviewData = @json($userOverviewData);
</script>
<style>
    :root {
        --accent: #6366f1;
        --accent-hover: #4f46e5;
        --pink: #e91e63;
        --blue: #3b82f6;
        --red: #ef4444;
        --green: #10b981;
        --orange: #f59e0b;
        --purple: #8b5cf6;
    }

    #main {
        background: var(--bg-body) !important;
        min-height: 100vh;
        padding: 32px !important;
        transition: background 0.4s ease;
    }

    /* ── Premium Cards ── */
    .card-premium {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 50px -10px rgba(0,0,0,0.1);
    }

    /* ── Stat Cards ── */
    .stat-card-premium {
        background: var(--bg-card);
        padding: 28px;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        position: relative;
    }

    .stat-card-premium::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 4px;
        background: transparent;
        border-radius: 0 0 24px 24px;
        transition: background 0.3s;
    }

    .stat-card-premium:hover::after {
        background: var(--accent);
    }

    .stat-icon-premium {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        transition: transform 0.3s ease;
    }

    .stat-card-premium:hover .stat-icon-premium {
        transform: scale(1.1) rotate(5deg);
    }

    /* ── Mini Order Stats ── */
    .order-mini-premium {
        background: var(--bg-card);
        padding: 20px;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        text-align: center;
        transition: all 0.2s;
    }

    .order-mini-premium:hover {
        border-color: var(--accent);
        background: var(--glass-bg);
    }

    /* ── Wallet Cards ── */
    .wallet-card-premium {
        background: var(--bg-card);
        padding: 24px;
        border-radius: 22px;
        border: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .wallet-card-premium.main {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white !important;
        border: none;
        box-shadow: 0 15px 30px -5px rgba(15, 23, 42, 0.4);
    }

    .wallet-card-premium.main * { color: white !important; }

    /* ── Table Styling ── */
    .table-card-premium {
        background: var(--bg-card);
        border-radius: 28px;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .table-header-premium {
        padding: 28px 32px;
        border-bottom: 1px solid var(--border-color);
        background: rgba(0,0,0,0.01);
    }

    .custom-table-premium {
        width: 100%;
        border-collapse: collapse;
    }

    .custom-table-premium thead th {
        background: var(--bg-body);
        padding: 20px 28px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: var(--text-muted);
        border: none;
    }

    .custom-table-premium tbody td {
        padding: 22px 28px;
        border-bottom: 1px solid var(--border-color);
        font-size: 14px;
        color: var(--text-main);
    }

    /* ── Typography & Anim ── */
    .section-title-premium {
        font-family: 'Sora', sans-serif;
        font-weight: 800;
        font-size: 20px;
        margin-bottom: 24px;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title-premium::before {
        content: '';
        width: 6px;
        height: 24px;
        background: var(--accent);
        border-radius: 10px;
    }

    .anim { opacity: 0; transform: translateY(20px); animation: fadeInUp 0.7s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    
    [data-theme="dark"] .card-premium, 
    [data-theme="dark"] .stat-card-premium, 
    [data-theme="dark"] .table-card-premium {
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.4);
    }

    /* ── Editable Status Badge for Select ── */
    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        outline: none;
        cursor: pointer;
    }

    .status-badge.status-pending   { background: #fff4e5 !important; color: #ff9800 !important; }
    .status-badge.status-processing{ background: #e8f0fe !important; color: #1a73e8 !important; }
    .status-badge.status-shipped   { background: #e6fcf5 !important; color: #0ca678 !important; }
    .status-badge.status-delivered { background: #f0fdf4 !important; color: #15803d !important; }
    .status-badge.status-cancelled { background: #fff5f5 !important; color: #fa5252 !important; }
    .status-badge.status-confirmed { background: #e8f0fe !important; color: #1a73e8 !important; }
    .status-badge.status-pickup    { background: #f1f3f9 !important; color: #475569 !important; }

    /* ── Recent Orders Highlight Animation ── */
    @keyframes highlightGlow {
        0%   { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4); }
        40%  { box-shadow: 0 0 20px 8px rgba(99, 102, 241, 0.3); }
        100% { box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05); }
    }
    .highlight-section {
        animation: highlightGlow 1.5s ease-out;
    }
</style>

  <!-- ── WELCOME BANNER ── -->
  <div class="row mb-4 anim anim-1">
    <div class="col-12">
        <div class="card-premium border-0 overflow-hidden" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #9333ea 100%); color: white; position: relative;">
            <div style="position: absolute; top: -50px; right: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            <div class="card-body p-4 p-lg-5 d-flex align-items-center justify-content-between position-relative" style="z-index: 1;">
                <div>
                    <span class="badge mb-3 px-3 py-2 rounded-pill fw-bold" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); color: white; border: 1px solid rgba(255,255,255,0.2); letter-spacing: 1px; font-size: 10px; text-transform: uppercase;">
                        <i class="bi bi-shield-check-fill me-1 text-info"></i> System Live: Marketplace Intelligence
                    </span>
                    <h1 class="fw-bold mb-3" style="font-family: 'Sora', sans-serif; font-size: clamp(24px, 5vw, 36px);">Jhr Bazar <span class="text-white-50">Command Center</span></h1>
                    <p class="opacity-75 mb-4 fw-medium" style="max-width: 580px; font-size: 15px; line-height: 1.6;">Monitor your ecosystem in real-time. Manage sellers, analyze customer behavior, and optimize your revenue streams with our unified dashboard.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <button class="btn btn-light rounded-pill px-4 py-2 fw-bold text-primary shadow-sm border-0 transition-all hover-scale" style="font-size: 14px;">
                            <i class="bi bi-graph-up-arrow me-2"></i> Analytics Hub
                        </button>
                        <button onclick="scrollToRecentOrders()" class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold hover-bg-white hover-text-primary" style="font-size: 14px;">
                            Recent Activity
                        </button>
                    </div>
                </div>
                <div class="d-none d-xl-block">
                    <div class="glass-icon-wrap" style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 40px; backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
                        <i class="bi bi-rocket-takeoff-fill" style="font-size: 90px; color: white;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- ── STAT CARDS ── -->
  <div class="row g-4 mb-5">
    <div class="col-6 col-lg-3 anim anim-1">
      <div class="stat-card-premium" style="border-top: 4px solid var(--pink);">
        <div>
          <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500;">Total Sellers</div>
          <div class="fw-bold h3 mb-0">{{ $totalSellers }}</div>
        </div>
        <div class="stat-icon-premium" style="background: rgba(233, 30, 99, 0.1); color: var(--pink);"><i class="bi bi-shop-window"></i></div>
      </div>
    </div>
    <div class="col-6 col-lg-3 anim anim-2">
      <div class="stat-card-premium" style="border-top: 4px solid var(--green);">
        <div>
          <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500;">Total Customers</div>
          <div class="fw-bold h3 mb-0">{{ $totalCustomers }}</div>
        </div>
        <div class="stat-icon-premium" style="background: rgba(16, 185, 129, 0.1); color: var(--green);"><i class="bi bi-people-fill"></i></div>
      </div>
    </div>
    <div class="col-6 col-lg-3 anim anim-3">
      <div class="stat-card-premium" style="border-top: 4px solid var(--accent);">
        <div>
          <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500;">Total Products</div>
          <div class="fw-bold h3 mb-0">{{ $totalProducts }}</div>
        </div>
        <div class="stat-icon-premium" style="background: rgba(99, 102, 241, 0.1); color: var(--accent);"><i class="bi bi-box-seam-fill"></i></div>
      </div>
    </div>
    <div class="col-6 col-lg-3 anim anim-4">
      <div class="stat-card-premium" style="border-top: 4px solid var(--purple); background: linear-gradient(135deg, #fdfcfb 0%, #e2d1c3 100%);">
        <div>
          <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500;">Total Commission</div>
          <div class="fw-bold h3 mb-0">৳{{ number_format($totalCommission ?? 0, 2) }}</div>
        </div>
        <div class="stat-icon-premium" style="background: rgba(139, 92, 246, 0.1); color: var(--purple);"><i class="bi bi-wallet2"></i></div>
      </div>
    </div>
  <!-- ── TIME-BASED ORDER STATS ── -->
  <div class="row g-4 mb-5">
    <div class="col-6 col-lg-3 anim anim-1">
      <div class="stat-card-premium" style="border-top: 4px solid var(--blue);">
        <div>
          <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500;">Orders (Last 30 Days)</div>
          <div class="fw-bold h3 mb-0">{{ $lastMonthOrders }}</div>
        </div>
        <div class="stat-icon-premium" style="background: rgba(59, 130, 246, 0.1); color: var(--blue);"><i class="bi bi-calendar-check"></i></div>
      </div>
    </div>
    <div class="col-6 col-lg-3 anim anim-2">
      <div class="stat-card-premium" style="border-top: 4px solid var(--orange);">
        <div>
          <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500;">Orders (Current Year)</div>
          <div class="fw-bold h3 mb-0">{{ $thisYearOrders }}</div>
        </div>
        <div class="stat-icon-premium" style="background: rgba(245, 158, 11, 0.1); color: var(--orange);"><i class="bi bi-calendar-event"></i></div>
      </div>
    </div>
    <div class="col-6 col-lg-3 anim anim-3">
      <div class="stat-card-premium" style="border-top: 4px solid var(--purple);">
        <div>
          <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500;">Total Orders</div>
          <div class="fw-bold h3 mb-0">{{ $totalOrders }}</div>
        </div>
        <div class="stat-icon-premium" style="background: rgba(139, 92, 246, 0.1); color: var(--purple);"><i class="bi bi-bag-check-fill"></i></div>
      </div>
    </div>
    <div class="col-6 col-lg-3 anim anim-4">
      <div class="stat-card-premium" style="border-top: 4px solid var(--green);">
        <div>
          <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500;">Confirmed Orders</div>
          <div class="fw-bold h3 mb-0">{{ $confirmedCount }}</div>
        </div>
        <div class="stat-icon-premium" style="background: rgba(16, 185, 129, 0.1); color: var(--green);"><i class="bi bi-check-all"></i></div>
      </div>
    </div>
  </div>

  <!-- ── ORDER ANALYTICS ── -->
  <div class="mb-5 anim anim-5">
    <div class="section-title-premium">Order Analytics</div>
    <div class="row g-3">
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <a href="{{ route('admin.orders.index', 'pending') }}" class="text-decoration-none d-block">
          <div class="order-mini-premium">
            <div class="text-muted small mb-1"><i class="bi bi-clock text-warning"></i> Pending</div>
            <div class="fw-bold h4 mb-0" style="color:var(--orange)">{{ $pendingCount }}</div>
          </div>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <a href="{{ route('admin.orders.index', 'confirmed') }}" class="text-decoration-none d-block">
          <div class="order-mini-premium">
            <div class="text-muted small mb-1"><i class="bi bi-check2-circle text-primary"></i> Confirm</div>
            <div class="fw-bold h4 mb-0" style="color:var(--blue)">{{ $confirmedCount }}</div>
          </div>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <a href="{{ route('admin.orders.index', 'processing') }}" class="text-decoration-none d-block">
          <div class="order-mini-premium">
            <div class="text-muted small mb-1"><i class="bi bi-gear text-info"></i> Processing</div>
            <div class="fw-bold h4 mb-0" style="color:#0ea5e9">{{ $processingCount }}</div>
          </div>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <a href="{{ route('admin.orders.index', 'pickup') }}" class="text-decoration-none d-block">
          <div class="order-mini-premium">
            <div class="text-muted small mb-1"><i class="bi bi-bag-check text-purple"></i> Pickup</div>
            <div class="fw-bold h4 mb-0" style="color:var(--purple)">{{ $pickupCount }}</div>
          </div>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <a href="{{ route('admin.orders.index', 'shipped') }}" class="text-decoration-none d-block">
          <div class="order-mini-premium">
            <div class="text-muted small mb-1"><i class="bi bi-truck text-secondary"></i> On The Way</div>
            <div class="fw-bold h4 mb-0" style="color:var(--muted)">{{ $onthewayCount }}</div>
          </div>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <a href="{{ route('admin.orders.index', 'delivered') }}" class="text-decoration-none d-block">
          <div class="order-mini-premium">
            <div class="text-muted small mb-1"><i class="bi bi-check-circle-fill text-success"></i> Delivered</div>
            <div class="fw-bold h4 mb-0" style="color:var(--green)">{{ $deliveredCount }}</div>
          </div>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <a href="{{ route('admin.orders.index', 'cancelled') }}" class="text-decoration-none d-block">
          <div class="order-mini-premium">
            <div class="text-muted small mb-1"><i class="bi bi-x-circle text-danger"></i> Cancelled</div>
            <div class="fw-bold h4 mb-0" style="color:var(--red)">{{ $cancelledCount }}</div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <!-- ── WALLET ── -->
  <div class="mb-5 anim anim-6">
    <div class="section-title-premium">Admin Wallet</div>
    <div class="row g-4">
      <div class="col-12 col-md-6 col-xl-3">
        <div class="wallet-card-premium main">
          <div>
            <div class="h2 fw-bold mb-1">৳{{ number_format($totalCommission ?? 0, 2) }}</div>
            <div class="opacity-75 small">Total Earning</div>
            <div class="mt-2 small px-2 py-1 bg-white bg-opacity-25 rounded-pill d-inline-block"><i class="bi bi-arrow-up-short"></i> +18.53%</div>
          </div>
          <div class="stat-icon-premium" style="background:rgba(255,255,255,.15); backdrop-filter: blur(5px);">
            <i class="bi bi-wallet2"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-6 col-xl-3">
        <div class="wallet-card-premium">
          <div>
            <div class="h3 fw-bold mb-0">৳{{ number_format($alreadyWithdraw ?? 0, 2) }}</div>
            <div class="text-muted small">Already Withdraw</div>
          </div>
          <div class="stat-icon-premium" style="background:#fff0f5;color:var(--pink);">
            <i class="bi bi-bank"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-6 col-xl-3">
        <div class="wallet-card-premium">
          <div>
            <div class="h3 fw-bold mb-0">৳{{ number_format($pendingWithdraw ?? 0, 2) }}</div>
            <div class="text-muted small">Pending Withdraw</div>
          </div>
          <div class="stat-icon-premium" style="background:#fff7ed;color:var(--orange);">
            <i class="bi bi-hourglass-split"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-6 col-xl-3">
        <div class="wallet-card-premium">
          <div>
            <div class="h3 fw-bold mb-0">৳{{ number_format($totalCommission ?? 0, 2) }}</div>
            <div class="text-muted small">Total Commission</div>
          </div>
          <div class="stat-icon-premium" style="background:#f0fdf4;color:var(--green);">
            <i class="bi bi-graph-up-arrow"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── STATISTICS + DONUT ── -->
  <div class="row g-4 mb-5 anim anim-7">
    <!-- Line + Bar Chart -->
    <div class="col-lg-7">
      <div class="table-card-premium p-4 h-100" style="background: white;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
          <div>
            <div class="text-muted small mb-1">Total Orders</div>
            <div class="fw-bold h2 mb-0" style="font-family: 'Sora', sans-serif;">{{ $totalOrders }}</div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <div class="btn-group btn-group-sm rounded-pill overflow-hidden border">
                <button class="btn btn-light px-3 fw-bold">Daily</button>
                <button class="btn btn-light px-3 fw-bold">Monthly</button>
                <button class="btn btn-primary px-3 fw-bold">Yearly</button>
            </div>
          </div>
        </div>
        <canvas id="ordersChart" height="220"></canvas>
      </div>
    </div>
    <!-- Donut -->
    <div class="col-lg-5">
      <div class="table-card-premium p-4 h-100" style="background: white;">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div>
            <div class="text-muted small mb-1">User Overview</div>
            <div class="fw-bold h2 mb-0" style="font-family: 'Sora', sans-serif;">{{ $totalCustomers + $totalSellers }}</div>
          </div>
        </div>
        <div class="donut-wrap mb-4" style="position: relative; height: 200px;">
          <canvas id="donutChart" height="200"></canvas>
          <div class="position-absolute top-50 start-50 translate-middle text-center">
            <div class="h2 fw-bold mb-0">{{ $totalCustomers + $totalSellers }}</div>
            <div class="text-muted small">Users</div>
          </div>
        </div>
        <div class="d-flex justify-content-center gap-4 flex-wrap" style="font-size:12px;">
          <div class="d-flex align-items-center gap-2"><span style="width:10px;height:10px;border-radius:50%;background:var(--pink);"></span>Customer</div>
          <div class="d-flex align-items-center gap-2"><span style="width:10px;height:10px;border-radius:50%;background:var(--blue);"></span>Shop</div>
          <div class="d-flex align-items-center gap-2"><span style="width:10px;height:10px;border-radius:50%;background:#ef4444);"></span>Rider</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── ORDER SUMMARY TABLE ── -->
  <div id="recentOrdersSection" class="row mb-5 anim anim-8">
    <div class="col-12">
      <div class="table-card-premium">
        <div class="table-header-premium">
          <div>
            <h5 class="fw-bold mb-1">Order Summary</h5>
            <p class="text-muted mb-0 small">Overview of the latest 5 orders</p>
          </div>
          <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold border">View All →</a>
        </div>
        <div class="table-responsive">
          <table class="custom-table-premium">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Qty</th>
                <th>Amount</th>
                <th>Shop</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentOrders as $invoice)
              <tr>
                <td><strong>#{{ $invoice->invoice_number }}</strong></td>
                <td>{{ $invoice->total_qty }}</td>
                <td class="fw-bold">৳{{ number_format($invoice->grand_total, 2) }}</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-light overflow-hidden" style="width: 24px; height: 24px; display:flex; align-items:center; justify-content:center; font-size: 10px;">
                      @if($invoice->seller && $invoice->seller->shop && $invoice->seller->shop->logo_url)
                        <img src="{{ $invoice->seller->shop->logo_url }}" alt="{{ $invoice->seller->shop->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                      @else
                        🛍️
                      @endif
                    </div>
                    {{ $invoice->seller?->shop?->name ?? 'System/Admin' }}
                  </div>
                </td>
                <td>{{ $invoice->created_at->format('d M, Y') }}</td>
                <td>
                  @php $s = $invoice->order?->status ?? 'pending'; @endphp
                  <select class="form-select form-select-sm status-badge status-{{ $s }}" onchange="updateDashboardStatus({{ $invoice->id }}, this.value)" style="font-size: 13px; font-weight: 700; border-radius: 50px; border: none; padding: 0.4rem 0.8rem; width: auto; display: inline-block;">
                      <option value="pending" {{ $s == 'pending' ? 'selected' : '' }}>Pending</option>
                      <option value="confirmed" {{ $s == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                      <option value="processing" {{ $s == 'processing' ? 'selected' : '' }}>Processing</option>
                      <option value="pickup" {{ $s == 'pickup' ? 'selected' : '' }}>Pickup</option>
                      <option value="shipped" {{ $s == 'shipped' ? 'selected' : '' }}>Shipped</option>
                      <option value="delivered" {{ $s == 'delivered' ? 'selected' : '' }}>Delivered</option>
                      <option value="cancelled" {{ $s == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                  </select>
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="{{ route('admin.orders.show', $invoice->id) }}" class="btn btn-sm btn-light border" title="View Order Details"><i class="bi bi-eye"></i></a>
                    <a href="{{ route('admin.pointofsalepos.invoice', $invoice->id) }}" target="_blank" class="btn btn-sm btn-light border" title="Download Invoice"><i class="bi bi-download"></i></a>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No recent orders found.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ── BOTTOM LISTS ── -->
  <div class="row g-4">
    <!-- Top Trending Shops -->
    <div class="col-12 col-md-4">
      <div class="table-card-premium p-4 h-100" style="background: white;">
        <h6 class="section-title-premium">Top Trending Shops</h6>
        <div class="d-flex flex-column gap-3 mt-3">
          @foreach($trendingShops as $shop)
            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg" style="transition: background 0.2s;">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; overflow: hidden; border: 1px solid var(--border-color);">
                  @if($shop->image_url)
                    <img src="{{ $shop->image_url }}" alt="{{ $shop->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                  @else
                    <i class="bi bi-shop text-muted" style="font-size: 20px;"></i>
                  @endif
                </div>
                <div>
                  <div class="fw-bold small">{{ $shop->name }}</div>
                  <div class="text-warning small"><i class="bi bi-star-fill"></i> {{ $shop->rating ?? '5.0' }}</div>
                </div>
              </div>
              <div class="text-muted small fw-bold">{{ $shop->order_count ?? 0 }} Orders</div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Most Favorite Products -->
    <div class="col-12 col-md-4">
      <div class="table-card-premium p-4 h-100" style="background: white;">
        <h6 class="section-title-premium">Favorite Products</h6>
        <div class="d-flex flex-column gap-3 mt-3">
          @forelse($favoriteProducts as $product)
            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg" style="transition: background 0.2s;">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; overflow: hidden;">
                    <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.parentNode.innerHTML='📷'">
                </div>
                <div>
                  <div class="fw-bold small text-truncate" style="max-width: 150px;">{{ $product->name }}</div>
                  <div class="text-danger small"><i class="bi bi-heart-fill"></i> {{ rand(5, 50) }} Likes</div>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted small p-2">No favorite products yet.</div>
          @endforelse
        </div>
      </div>
    </div>

    <!-- Top Selling Products -->
    <div class="col-12 col-md-4">
      <div class="table-card-premium p-4 h-100" style="background: white;">
        <h6 class="section-title-premium">Top Selling</h6>
        <div class="d-flex flex-column gap-3 mt-3">
          @forelse($topSellingProducts as $product)
            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg" style="transition: background 0.2s;">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; overflow: hidden;">
                    <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.parentNode.innerHTML='📦'">
                </div>
                <div>
                  <div class="fw-bold small text-truncate" style="max-width: 150px;">{{ $product->name }}</div>
                  <div class="text-success small fw-bold">{{ rand(10, 100) }} Sold</div>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted small p-2">No top selling products yet.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <script>
      function scrollToRecentOrders() {
          const section = document.getElementById('recentOrdersSection');
          if (section) {
              section.scrollIntoView({ behavior: 'smooth', block: 'start' });
              // Add highlight glow after scroll completes
              setTimeout(() => {
                  section.querySelector('.table-card-premium').classList.add('highlight-section');
                  setTimeout(() => {
                      section.querySelector('.table-card-premium').classList.remove('highlight-section');
                  }, 1500);
              }, 500);
          }
      }

      function updateDashboardStatus(id, status) {
          fetch(`{{ url('admin/orders/status') }}/${id}`, {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({ status: status })
          })
          .then(res => res.json())
          .then(data => {
              if (data.success) {
                  // Find the select element and update its classes
                  const select = event.target;
                  select.className = `form-select form-select-sm status-badge status-${status}`;
                  
                  // Use premium SweetAlert toast
                  if (typeof Toast !== 'undefined') {
                      Toast.fire({
                          icon: 'success',
                          title: 'Success',
                          text: data.message
                      });
                  } else {
                      Swal.fire({
                          icon: 'success',
                          title: 'Success',
                          text: data.message,
                          toast: true,
                          position: 'top-end',
                          showConfirmButton: false,
                          timer: 3000
                      });
                  }
              } else {
                  Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: data.message || 'Failed to update order status'
                  });
              }
          })
          .catch(err => {
              Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Something went wrong!'
              });
          });
      }
  </script>
@endsection
