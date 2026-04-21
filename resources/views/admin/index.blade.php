@extends('admin.master')
@section('content')
  <!-- ── STAT CARDS ── -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-xl-3 anim anim-1">
      <div class="stat-card pink">
        <div>
          <div class="stat-label">Total Shops</div>
          <div class="stat-value">10</div>
        </div>
        <div class="stat-icon"><i class="bi bi-shop-window"></i></div>
      </div>
    </div>
    <div class="col-6 col-xl-3 anim anim-2">
      <div class="stat-card blue">
        <div>
          <div class="stat-label">Total Products</div>
          <div class="stat-value">166</div>
        </div>
        <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
      </div>
    </div>
    <div class="col-6 col-xl-3 anim anim-3">
      <div class="stat-card red">
        <div>
          <div class="stat-label">Total Orders</div>
          <div class="stat-value">109</div>
        </div>
        <div class="stat-icon"><i class="bi bi-cart-check-fill"></i></div>
      </div>
    </div>
    <div class="col-6 col-xl-3 anim anim-4">
      <div class="stat-card green">
        <div>
          <div class="stat-label">Total Customers</div>
          <div class="stat-value">26</div>
        </div>
        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
      </div>
    </div>
  </div>

  <!-- ── ORDER ANALYTICS ── -->
  <div class="mb-4 anim anim-5">
    <div class="section-title">Order Analytics</div>
    <div class="row g-2">
      <div class="col-6 col-sm-4 col-md-3 col-lg-auto flex-grow-1">
        <div class="order-mini">
          <div class="label"><i class="bi bi-clock text-warning"></i> Pending</div>
          <div class="value" style="color:var(--orange)">87</div>
        </div>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-lg-auto flex-grow-1">
        <div class="order-mini">
          <div class="label"><i class="bi bi-check2-circle text-primary"></i> Confirm</div>
          <div class="value" style="color:var(--blue)">4</div>
        </div>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-lg-auto flex-grow-1">
        <div class="order-mini">
          <div class="label"><i class="bi bi-gear text-info"></i> Processing</div>
          <div class="value" style="color:#0ea5e9">0</div>
        </div>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-lg-auto flex-grow-1">
        <div class="order-mini">
          <div class="label"><i class="bi bi-bag-check text-purple"></i> Pickup</div>
          <div class="value" style="color:var(--purple)">1</div>
        </div>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-lg-auto flex-grow-1">
        <div class="order-mini">
          <div class="label"><i class="bi bi-truck text-secondary"></i> On The Way</div>
          <div class="value" style="color:var(--muted)">0</div>
        </div>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-lg-auto flex-grow-1">
        <div class="order-mini">
          <div class="label"><i class="bi bi-check-circle-fill text-success"></i> Delivered</div>
          <div class="value" style="color:var(--green)">13</div>
        </div>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-lg-auto flex-grow-1">
        <div class="order-mini">
          <div class="label"><i class="bi bi-x-circle text-danger"></i> Cancelled</div>
          <div class="value" style="color:var(--red)">4</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── WALLET ── -->
  <div class="mb-4 anim anim-6">
    <div class="section-title">Admin Wallet</div>
    <div class="row g-3">
      <div class="col-12 col-md-6 col-xl-3">
        <div class="wallet-card main">
          <div>
            <div class="w-value">$80</div>
            <div class="w-label">Total Earning</div>
            <div class="w-badge"><i class="bi bi-arrow-up-short"></i> +18.53%</div>
          </div>
          <div class="w-icon" style="background:rgba(255,255,255,.2);color:#fff;font-size:24px;">
            <i class="bi bi-wallet2"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="wallet-card">
          <div>
            <div class="w-value">$2621</div>
            <div class="w-label">Already Withdraw</div>
          </div>
          <div class="w-icon" style="background:#fff0f5;color:var(--pink);">
            <i class="bi bi-bank"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="wallet-card">
          <div>
            <div class="w-value">$10</div>
            <div class="w-label">Pending Withdraw</div>
          </div>
          <div class="w-icon" style="background:#fff7ed;color:var(--orange);">
            <i class="bi bi-hourglass-split"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="wallet-card">
          <div>
            <div class="w-value">$80</div>
            <div class="w-label">Total Commission</div>
          </div>
          <div class="w-icon" style="background:var(--green-light);color:var(--green);">
            <i class="bi bi-graph-up-arrow"></i>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="wallet-card">
          <div>
            <div class="w-value">$0</div>
            <div class="w-label">Rejected Withdraw</div>
          </div>
          <div class="w-icon" style="background:#fef2f2;color:var(--red);">
            <i class="bi bi-x-circle"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── STATISTICS + DONUT ── -->
  <div class="row g-3 mb-4 anim anim-7">
    <!-- Line + Bar Chart -->
    <div class="col-lg-7">
      <div class="chart-card h-100">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
          <div>
            <div style="font-size:13px;color:var(--muted);">Total Orders</div>
            <div style="font-family:'Sora',sans-serif;font-size:26px;font-weight:700;">109</div>
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="chart-tab-group">
              <button class="chart-tab">Daily</button>
              <button class="chart-tab">Monthly</button>
              <button class="chart-tab active">Yearly</button>
            </div>
            <input type="date" class="form-control form-control-sm" style="max-width:140px;font-size:12px;">
          </div>
        </div>
        <canvas id="ordersChart" height="180"></canvas>
      </div>
    </div>
    <!-- Donut -->
    <div class="col-lg-5">
      <div class="chart-card h-100">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <div style="font-size:13px;color:var(--muted);">User Overview</div>
            <div style="font-family:'Sora',sans-serif;font-size:26px;font-weight:700;">39</div>
          </div>
        </div>
        <div class="donut-wrap mb-3">
          <canvas id="donutChart" height="200" style="max-height:200px;"></canvas>
          <div class="donut-center">
            <div class="d-num">39</div>
            <div class="d-lbl">Users</div>
          </div>
        </div>
        <div class="d-flex justify-content-center gap-3 flex-wrap" style="font-size:12px;">
          <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:var(--pink);margin-right:4px;"></span>Customer</span>
          <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:var(--blue);margin-right:4px;"></span>Shop</span>
          <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#ef4444;margin-right:4px;"></span>Rider</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ── ORDER SUMMARY TABLE ── -->
  <div class="row g-3 mb-4 anim anim-8">
    <div class="col-12">
      <div class="table-card">
        <div class="table-header">
          <div>
            <div class="title">Order Summary</div>
            <div class="subtitle">Latest 5 Orders</div>
          </div>
          <a href="#" style="font-size:12.5px;color:var(--pink);font-weight:600;text-decoration:none;">View All →</a>
        </div>
        <div class="table-responsive">
          <table class="custom-table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Qty</th>
                <th>Shop</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>#RC000167</strong></td>
                <td>3</td>
                <td>Razin Shop</td>
                <td>28 Jan, 2026</td>
                <td><span class="badge-status status-pending"><span class="dot"></span>Pending</span></td>
                <td><button class="action-btn"><i class="bi bi-eye"></i></button> <button class="action-btn"><i class="bi bi-download"></i></button></td>
              </tr>
              <tr>
                <td><strong>#RC000166</strong></td>
                <td>1</td>
                <td>Razin Shop</td>
                <td>28 Jan, 2026</td>
                <td><span class="badge-status status-pending"><span class="dot"></span>Pending</span></td>
                <td><button class="action-btn"><i class="bi bi-eye"></i></button> <button class="action-btn"><i class="bi bi-download"></i></button></td>
              </tr>
              <tr>
                <td><strong>#RC000165</strong></td>
                <td>1</td>
                <td>Razin Shop</td>
                <td>28 Jan, 2026</td>
                <td><span class="badge-status status-pending"><span class="dot"></span>Pending</span></td>
                <td><button class="action-btn"><i class="bi bi-eye"></i></button> <button class="action-btn"><i class="bi bi-download"></i></button></td>
              </tr>
              <tr>
                <td><strong>#RC000164</strong></td>
                <td>1</td>
                <td>Easy Life</td>
                <td>13 Jan, 2026</td>
                <td><span class="badge-status status-delivered"><span class="dot"></span>Delivered</span></td>
                <td><button class="action-btn"><i class="bi bi-eye"></i></button> <button class="action-btn"><i class="bi bi-download"></i></button></td>
              </tr>
              <tr>
                <td><strong>#RC000163</strong></td>
                <td>2</td>
                <td>Easy Life</td>
                <td>13 Jan, 2026</td>
                <td><span class="badge-status status-delivered"><span class="dot"></span>Delivered</span></td>
                <td><button class="action-btn"><i class="bi bi-eye"></i></button> <button class="action-btn"><i class="bi bi-download"></i></button></td>
              </tr>
              <tr>
                <td><strong>#RC000162</strong></td>
                <td>1</td>
                <td>Razin Shop</td>
                <td>17 Dec, 2025</td>
                <td><span class="badge-status status-pending"><span class="dot"></span>Pending</span></td>
                <td><button class="action-btn"><i class="bi bi-eye"></i></button> <button class="action-btn"><i class="bi bi-download"></i></button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ── BOTTOM LISTS ── -->
  <div class="row g-3">
    <!-- Top Trending Shops -->
    <div class="col-12 col-md-4">
      <div class="chart-card h-100">
        <div class="section-title">Top Trending Shops</div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=80&h=80&fit=crop&auto=format" alt="Razin Shop" onerror="this.parentNode.innerHTML='🛍️'">
          </div>
          <div>
            <div class="item-name">Razin Shop</div>
            <div class="item-meta"><span class="star">★★★★</span>☆ 5</div>
          </div>
          <div class="item-count">Order: 87</div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=80&h=80&fit=crop&auto=format" alt="Easy Life" onerror="this.parentNode.innerHTML='😊'">
          </div>
          <div>
            <div class="item-name">Easy Life</div>
            <div class="item-meta"><span class="star">★★★★</span>☆ 5</div>
          </div>
          <div class="item-count">Order: 16</div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=80&h=80&fit=crop&auto=format" alt="Echo Mart" onerror="this.parentNode.innerHTML='🛒'">
          </div>
          <div>
            <div class="item-name">Echo Mart</div>
            <div class="item-meta"><span class="star">★★★★</span>☆ 5</div>
          </div>
          <div class="item-count">Order: 6</div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=80&h=80&fit=crop&auto=format" alt="Style Haven" onerror="this.parentNode.innerHTML='🏪'">
          </div>
          <div>
            <div class="item-name">Style Haven</div>
            <div class="item-meta"><span class="star">★★★★</span>☆ 5</div>
          </div>
          <div class="item-count">Order: 0</div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=80&h=80&fit=crop&auto=format" alt="Alibaba Express" onerror="this.parentNode.innerHTML='🌐'">
          </div>
          <div>
            <div class="item-name">Alibaba Express dot com</div>
            <div class="item-meta"><span class="star">★★★★</span>☆ 5</div>
          </div>
          <div class="item-count">Order: 0</div>
        </div>
      </div>
    </div>

    <!-- Most Favorite Products -->
    <div class="col-12 col-md-4">
      <div class="chart-card h-100">
        <div class="section-title">Most Favorite Products</div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=80&h=80&fit=crop&auto=format" alt="Sony A6400" onerror="this.parentNode.innerHTML='📷'">
          </div>
          <div>
            <div class="item-name">Sony A6400 Mirrorless Camera</div>
            <div class="item-meta" style="color:var(--red);">❤ 2</div>
          </div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1575311373937-040b8e1fd6b0?w=80&h=80&fit=crop&auto=format" alt="Fitbit" onerror="this.parentNode.innerHTML='⌚'">
          </div>
          <div>
            <div class="item-name">Fitbit Charge 6 Fitness Tracker</div>
            <div class="item-meta" style="color:var(--red);">❤ 2</div>
          </div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=80&h=80&fit=crop&auto=format" alt="Smart Watch" onerror="this.parentNode.innerHTML='⌚'">
          </div>
          <div>
            <div class="item-name">Smart Watch</div>
            <div class="item-meta" style="color:var(--red);">❤ 2</div>
          </div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=80&h=80&fit=crop&auto=format" alt="Orange Juice" onerror="this.parentNode.innerHTML='🍊'">
          </div>
          <div>
            <div class="item-name">Orange Juice</div>
            <div class="item-meta" style="color:var(--red);">❤ 2</div>
          </div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1544161513-0179fe746fd5?w=80&h=80&fit=crop&auto=format" alt="Polar H10" onerror="this.parentNode.innerHTML='💓'">
          </div>
          <div>
            <div class="item-name">Polar H10 Heart Rate Monitor</div>
            <div class="item-meta" style="color:var(--red);">❤ 1</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Selling Products -->
    <div class="col-12 col-md-4">
      <div class="chart-card h-100">
        <div class="section-title">Top Selling Products</div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1575311373937-040b8e1fd6b0?w=80&h=80&fit=crop&auto=format" alt="Fitbit" onerror="this.parentNode.innerHTML='⌚'">
          </div>
          <div>
            <div class="item-name">Fitbit Charge 6 Fitness Tracker</div>
            <div class="item-meta"><span class="star">★★★★</span>☆ 0.0 (0)</div>
          </div>
          <div class="item-count">Sold: 16</div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=80&h=80&fit=crop&auto=format" alt="Freeman Beauty" onerror="this.parentNode.innerHTML='🧴'">
          </div>
          <div>
            <div class="item-name">Freeman Beauty Korean Cica Soo...</div>
            <div class="item-meta"><span class="star">★★★★</span>☆ 0.0 (0)</div>
          </div>
          <div class="item-count">Sold: 13</div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=80&h=80&fit=crop&auto=format" alt="Smart Watch" onerror="this.parentNode.innerHTML='⌚'">
          </div>
          <div>
            <div class="item-name">Smart Watch</div>
            <div class="item-meta"><span class="star">★★★★</span>☆ 0.0 (0)</div>
          </div>
          <div class="item-count">Sold: 12</div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1593359677879-a4bb92f4834e?w=80&h=80&fit=crop&auto=format" alt="QLED TV" onerror="this.parentNode.innerHTML='📺'">
          </div>
          <div>
            <div class="item-name">HDR 4K UHD Smart QLED TV</div>
            <div class="item-meta"><span class="star">★★★★</span>☆ 0.0 (0)</div>
          </div>
          <div class="item-count">Sold: 11</div>
        </div>

        <div class="list-item-card">
          <div class="item-logo">
            <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=80&h=80&fit=crop&auto=format" alt="Sony Camera" onerror="this.parentNode.innerHTML='📷'">
          </div>
          <div>
            <div class="item-name">Sony A6400 Mirrorless Camera W...</div>
            <div class="item-meta"><span class="star">★★★★</span>☆ 0.0 (0)</div>
          </div>
          <div class="item-count">Sold: 6</div>
        </div>
      </div>
    </div>
  </div>
@endsection
