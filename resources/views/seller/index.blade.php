@extends('admin.master')
@section('content')

<style>
    .seller-stats-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
    }
    .seller-stats-card:hover {
        transform: translateY(-5px);
    }
    .icon-box {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .card-total-products { background: #f5f3ff; color: #7c3aed; border-left: 5px solid #7c3aed; }
    .card-total-orders { background: #eff6ff; color: #2563eb; border-left: 5px solid #2563eb; }
    .card-total-categories { background: #fff1f2; color: #e11d48; border-left: 5px solid #e11d48; }
    .card-total-brands { background: #f0fdf4; color: #16a34a; border-left: 5px solid #16a34a; }

    .analytics-box {
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        border: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
    }
    .analytics-box:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .wallet-card {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #e2e8f0;
    }
    .wallet-stat-item {
        background: #f8fafc;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn-withdraw {
        background: #f43f5e;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 10px;
        font-weight: 600;
    }

    .table-modern thead {
        background: #f8fafc;
    }
    .table-modern th {
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 700;
        padding: 15px;
    }
    .table-modern td {
        padding: 15px;
        font-size: 14px;
        color: #1e293b;
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="container-fluid py-4">

    {{-- ── Top Stat Cards ── --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card seller-stats-card card-total-products shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-0">{{ $totalProducts ?? 0 }}</h3>
                        <p class="text-muted small mb-0">Total Products</p>
                    </div>
                    <div class="icon-box bg-white shadow-sm"><i class="bi bi-box-seam"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card seller-stats-card card-total-orders shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-0">{{ $totalOrders ?? 0 }}</h3>
                        <p class="text-muted small mb-0">Total Orders</p>
                    </div>
                    <div class="icon-box bg-white shadow-sm"><i class="bi bi-cart3"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card seller-stats-card card-total-categories shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-0">{{ $totalCategories ?? 0 }}</h3>
                        <p class="text-muted small mb-0">Total Categories</p>
                    </div>
                    <div class="icon-box bg-white shadow-sm"><i class="bi bi-grid-3x3-gap"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card seller-stats-card card-total-brands shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-0">{{ $totalBrands ?? 0 }}</h3>
                        <p class="text-muted small mb-0">Total Brands</p>
                    </div>
                    <div class="icon-box bg-white shadow-sm"><i class="bi bi-patch-check"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Order Analytics ── --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0">Order Analytics</h6>
        </div>
        <div class="card-body pt-0">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-clock me-1"></i> Pending</p>
                            <h5 class="fw-bold mb-0">4</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-check2-circle me-1"></i> Confirm</p>
                            <h5 class="fw-bold mb-0">2</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-gear me-1"></i> Processing</p>
                            <h5 class="fw-bold mb-0">0</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-truck me-1"></i> Pickup</p>
                            <h5 class="fw-bold mb-0">1</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-geo-alt me-1"></i> On The Way</p>
                            <h5 class="fw-bold mb-0">0</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-box-seam me-1"></i> Delivered</p>
                            <h5 class="fw-bold mb-0">8</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-x-circle me-1"></i> Cancelled</p>
                            <h5 class="fw-bold mb-0">1</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Shop Wallet & Stats ── --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="wallet-card shadow-sm h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-wallet2 text-muted me-2"></i>
                    <span class="text-muted small fw-bold">Shop Wallet</span>
                </div>
                <h1 class="fw-bold mb-1">$5248.45</h1>
                <p class="text-success small mb-4"><i class="bi bi-graph-up-arrow me-1"></i> +18.53% Available Balance</p>
                <div>
                    <button class="btn btn-withdraw">Withdraw</button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">$10</h5>
                            <p class="small text-muted mb-0">Pending Withdraw</p>
                        </div>
                        <i class="bi bi-credit-card text-muted"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">$2621</h5>
                            <p class="small text-muted mb-0">Already Withdraw</p>
                        </div>
                        <i class="bi bi-cash-stack text-muted"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">$0</h5>
                            <p class="small text-muted mb-0">Rejected Withdraw</p>
                        </div>
                        <i class="bi bi-x-square text-muted text-danger"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">$2631</h5>
                            <p class="small text-muted mb-0">Total Withdraw</p>
                        </div>
                        <i class="bi bi-pie-chart text-muted"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">$104</h5>
                            <p class="small text-muted mb-0">Delivery Charge</p>
                        </div>
                        <i class="bi bi-truck text-muted"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">$32604</h5>
                            <p class="small text-muted mb-0">Total POS Sales</p>
                        </div>
                        <i class="bi bi-display text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Recent Orders Table ── --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Qty</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">#RC000164</td>
                            <td>1</td>
                            <td>13 Jan, 2026</td>
                            <td><span class="status-badge bg-success text-white">Delivered</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-light text-dark"><i class="bi bi-download"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">#RC000163</td>
                            <td>2</td>
                            <td>13 Jan, 2026</td>
                            <td><span class="status-badge bg-success text-white">Delivered</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-light text-dark"><i class="bi bi-download"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">#RC000128</td>
                            <td>1</td>
                            <td>05 Oct, 2025</td>
                            <td><span class="status-badge bg-primary text-white">Confirm</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-light text-dark"><i class="bi bi-download"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Bottom Stats ── --}}
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-shop me-2 text-primary"></i> Top Selling Products</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-2 rounded me-3"><i class="bi bi-image text-muted"></i></div>
                        <div class="flex-grow-1">
                            <p class="small fw-bold mb-0">Sony A6400 Mirrorless...</p>
                            <p class="text-danger small mb-0">Rating: 0.0</p>
                        </div>
                        <span class="badge bg-light text-primary">Sold: 6</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-3"><i class="bi bi-image text-muted"></i></div>
                        <div class="flex-grow-1">
                            <p class="small fw-bold mb-0">HP 15s-du3039TX 11th Gen...</p>
                            <p class="text-danger small mb-0">Rating: 0.0</p>
                        </div>
                        <span class="badge bg-light text-primary">Sold: 4</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-star me-2 text-warning"></i> Top Rating Products</h6>
                </div>
                <div class="card-body pt-0 text-center py-5">
                    <i class="bi bi-stars text-muted display-4"></i>
                    <p class="text-muted small mt-2">No rating data yet</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-heart me-2 text-danger"></i> Most Favorite Products</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-2 rounded me-3"><i class="bi bi-image text-muted"></i></div>
                        <div class="flex-grow-1">
                            <p class="small fw-bold mb-0">Sony A6400 Mirrorless...</p>
                            <p class="text-danger small mb-0">Sold: 6 | Rating: 0.0</p>
                        </div>
                        <span class="text-danger"><i class="bi bi-heart-fill me-1"></i> 2</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
