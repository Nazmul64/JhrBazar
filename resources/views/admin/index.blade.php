@extends('admin.master')
@section('content')
<style>
    :root {
        --glass: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.3);
        --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        --accent: #6366f1;
        --pink: #e91e63;
        --blue: #3b82f6;
        --red: #ef4444;
        --green: #10b981;
        --orange: #f59e0b;
        --purple: #8b5cf6;
        --muted: #64748b;
    }

    #main {
        background: #f1f5f9;
        min-height: 100vh;
        padding: 30px !important;
    }

    .card-premium {
        background: var(--glass);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
    }

    .stat-card-premium {
        background: white;
        padding: 24px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .stat-card-premium:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
    }

    .stat-icon-premium {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .order-mini-premium {
        background: white;
        padding: 15px 20px;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        gap: 5px;
        transition: all 0.2s;
        text-align: center;
    }

    .order-mini-premium:hover {
        background: #f8fafc;
        border-color: var(--accent);
    }

    .wallet-card-premium {
        background: white;
        padding: 24px;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .wallet-card-premium.main {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
    }

    .table-card-premium {
        background: white;
        border-radius: 24px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .table-header-premium {
        padding: 25px 30px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fafafa;
    }

    .custom-table-premium {
        width: 100%;
        margin-bottom: 0;
    }

    .custom-table-premium thead th {
        background: #f8fafc;
        padding: 18px 25px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        border: none;
    }

    .custom-table-premium tbody td {
        padding: 18px 25px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #1e293b;
    }

    .status-pill {
        padding: 6px 12px;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .status-pending { background: #fff7ed; color: #f59e0b; }
    .status-delivered { background: #f0fdf4; color: #10b981; }

    .anim { opacity: 0; transform: translateY(20px); animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
    .anim-1 { animation-delay: 0.1s; }
    .anim-2 { animation-delay: 0.2s; }
    .anim-3 { animation-delay: 0.3s; }
    .anim-4 { animation-delay: 0.4s; }
    .anim-5 { animation-delay: 0.5s; }
    .anim-6 { animation-delay: 0.6s; }

    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }

    .section-title-premium {
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 20px;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title-premium::before {
        content: '';
        width: 4px;
        height: 20px;
        background: var(--accent);
        border-radius: 10px;
    }
</style>

  <!-- ── WELCOME BANNER ── -->
  <div class="row mb-4 anim anim-1">
    <div class="col-12">
        <div class="card-premium border-0 overflow-hidden" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white;">
            <div class="card-body p-4 p-lg-5 d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="fw-bold mb-2">Jhr Bazar Control Center</h2>
                    <p class="opacity-75 mb-4" style="max-width: 600px;">Welcome back, Admin! Monitor your marketplace performance and manage your business efficiently with our advanced analytics tools.</p>
                    <div class="d-flex gap-3">
                        <button class="btn btn-light rounded-pill px-4 fw-bold text-primary shadow-sm border-0">View Analytics</button>
                        <button class="btn btn-outline-light rounded-pill px-4 fw-bold">Recent Orders</button>
                    </div>
                </div>
                <div class="d-none d-lg-block">
                    <div class="glass-icon-wrap" style="background: rgba(255,255,255,0.1); padding: 30px; border-radius: 30px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                        <i class="bi bi-rocket-takeoff" style="font-size: 80px; color: white;"></i>
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
  </div>

  <!-- ── ORDER ANALYTICS ── -->
  <div class="mb-5 anim anim-5">
    <div class="section-title-premium">Order Analytics</div>
    <div class="row g-3">
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <div class="order-mini-premium">
          <div class="text-muted small mb-1"><i class="bi bi-clock text-warning"></i> Pending</div>
          <div class="fw-bold h4 mb-0" style="color:var(--orange)">{{ $pendingCount }}</div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <div class="order-mini-premium">
          <div class="text-muted small mb-1"><i class="bi bi-check2-circle text-primary"></i> Confirm</div>
          <div class="fw-bold h4 mb-0" style="color:var(--blue)">{{ $confirmedCount }}</div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <div class="order-mini-premium">
          <div class="text-muted small mb-1"><i class="bi bi-gear text-info"></i> Processing</div>
          <div class="fw-bold h4 mb-0" style="color:#0ea5e9">{{ $processingCount }}</div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <div class="order-mini-premium">
          <div class="text-muted small mb-1"><i class="bi bi-bag-check text-purple"></i> Pickup</div>
          <div class="fw-bold h4 mb-0" style="color:var(--purple)">{{ $pickupCount }}</div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <div class="order-mini-premium">
          <div class="text-muted small mb-1"><i class="bi bi-truck text-secondary"></i> On The Way</div>
          <div class="fw-bold h4 mb-0" style="color:var(--muted)">{{ $onthewayCount }}</div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <div class="order-mini-premium">
          <div class="text-muted small mb-1"><i class="bi bi-check-circle-fill text-success"></i> Delivered</div>
          <div class="fw-bold h4 mb-0" style="color:var(--green)">{{ $deliveredCount }}</div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-3 col-xl">
        <div class="order-mini-premium">
          <div class="text-muted small mb-1"><i class="bi bi-x-circle text-danger"></i> Cancelled</div>
          <div class="fw-bold h4 mb-0" style="color:var(--red)">{{ $cancelledCount }}</div>
        </div>
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
  <div class="row mb-5 anim anim-8">
    <div class="col-12">
      <div class="table-card-premium">
        <div class="table-header-premium">
          <div>
            <h5 class="fw-bold mb-1">Order Summary</h5>
            <p class="text-muted mb-0 small">Overview of the latest 5 orders</p>
          </div>
          <a href="#" class="btn btn-light btn-sm rounded-pill px-3 fw-bold border">View All →</a>
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
                  @php
                    $status = $invoice->order?->status ?? 'pending';
                    $statusClass = match($status) {
                        'pending' => 'status-pending',
                        'delivered' => 'status-delivered',
                        'cancelled' => 'bg-danger bg-opacity-10 text-danger',
                        'confirmed' => 'bg-primary bg-opacity-10 text-primary',
                        'processing' => 'bg-info bg-opacity-10 text-info',
                        'pickup' => 'bg-secondary bg-opacity-10 text-secondary',
                        'shipped' => 'bg-dark bg-opacity-10 text-dark',
                        default => 'bg-secondary bg-opacity-10 text-secondary'
                    };
                  @endphp
                  <span class="status-pill {{ $statusClass }}">{{ ucfirst($status) }}</span>
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></button>
                    <button class="btn btn-sm btn-light border"><i class="bi bi-download"></i></button>
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
                <div class="rounded-3 bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; overflow: hidden;">
                  <img src="{{ $shop->image_url ?? 'https://via.placeholder.com/48' }}" alt="{{ $shop->name }}" style="width: 100%; height: 100%; object-fit: cover;">
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
@endsection
