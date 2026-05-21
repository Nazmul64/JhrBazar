@extends('admin.master')
@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap');

    :root {
        --seller-glass: rgba(255, 255, 255, 0.85);
        --seller-border: rgba(255, 255, 255, 0.4);
        --seller-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --danger-gradient:  linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        --info-gradient:    linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        --text-main: #1e293b;
        --text-muted: #64748b;
    }

    body { background: #f8fafc; font-family: 'Inter', sans-serif; }
    h1, h2, h3, h4, h5, h6 { font-family: 'Sora', sans-serif; }

    /* ── Animated Entrance ── */
    .fade-up { animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(20px); }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }

    /* ── Top Stat Cards ── */
    .seller-stats-card {
        background: var(--seller-glass);
        backdrop-filter: blur(16px);
        border: 1px solid var(--seller-border);
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
        z-index: 1;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .seller-stats-card::before {
        content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: inherit; z-index: -1; transition: opacity 0.3s ease; opacity: 0;
    }
    .seller-stats-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px -10px rgba(99, 102, 241, 0.15);
    }
    .card-total-products { border-bottom: 4px solid #7c3aed; }
    .card-total-orders { border-bottom: 4px solid #2563eb; }
    .card-total-categories { border-bottom: 4px solid #e11d48; }
    .card-total-brands { border-bottom: 4px solid #16a34a; }

    .icon-box {
        width: 55px; height: 55px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px; transition: transform 0.3s ease;
    }
    .seller-stats-card:hover .icon-box { transform: scale(1.15) rotate(5deg); }
    
    .card-total-products .icon-box { background: rgba(124, 58, 237, 0.1); color: #7c3aed; }
    .card-total-orders .icon-box { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
    .card-total-categories .icon-box { background: rgba(225, 29, 72, 0.1); color: #e11d48; }
    .card-total-brands .icon-box { background: rgba(22, 163, 74, 0.1); color: #16a34a; }

    /* ── Order Analytics Boxes ── */
    .analytics-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 20px;
        border: 1px solid rgba(0,0,0,0.04);
        display: flex; justify-content: space-between; align-items: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
    }
    .analytics-box:hover {
        border-color: #6366f1;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.1);
    }
    .analytics-box i.bi-box-arrow-up-right { transition: transform 0.3s; }
    .analytics-box:hover i.bi-box-arrow-up-right { transform: translateX(3px) translateY(-3px); color: #6366f1 !important; }

    /* ── Wallet Section ── */
    .wallet-card {
        background: var(--primary-gradient);
        color: white;
        border-radius: 24px;
        padding: 40px 30px;
        border: none;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px -10px rgba(99, 102, 241, 0.5);
    }
    .wallet-card::after {
        content: '\F615'; font-family: bootstrap-icons; position: absolute;
        right: -10%; bottom: -20%; font-size: 180px; opacity: 0.1; line-height: 1;
    }
    .wallet-card .text-muted, .wallet-card .text-success { color: rgba(255,255,255,0.8) !important; }
    
    .wallet-stat-item {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        display: flex; justify-content: space-between; align-items: center;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .wallet-stat-item:hover {
        transform: scale(1.03);
        box-shadow: 0 12px 30px -8px rgba(0,0,0,0.08);
    }
    .wallet-stat-item i { font-size: 28px; opacity: 0.2; transition: opacity 0.3s, transform 0.3s; }
    .wallet-stat-item:hover i { opacity: 0.8; transform: scale(1.1); }

    .btn-withdraw {
        background: #ffffff;
        color: #6366f1;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 700;
        font-family: 'Sora', sans-serif;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .btn-withdraw:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        color: #4f46e5;
    }

    /* ── Tables ── */
    .card { border-radius: 20px !important; border: 1px solid rgba(0,0,0,0.04) !important; box-shadow: var(--seller-shadow) !important; }
    .card-header { background: transparent !important; border-bottom: 1px solid rgba(0,0,0,0.04) !important; padding: 20px 24px !important; }
    
    .table-modern thead { background: #f8fafc; }
    .table-modern th { font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: 700; padding: 18px 24px; letter-spacing: 0.5px; border-bottom: none; }
    .table-modern td { padding: 18px 24px; font-size: 14.5px; color: #1e293b; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .table-modern tr { transition: background 0.2s; }
    .table-modern tbody tr:hover { background: #f8fafc; }
    
    .status-badge { padding: 6px 14px; border-radius: 30px; font-size: 12px; font-weight: 700; letter-spacing: 0.3px; display: inline-block; }
    
    .btn-light { background: #f1f5f9; border: none; transition: all 0.2s; }
    .btn-light:hover { background: #e2e8f0; transform: translateY(-2px); }
</style>

<div class="container-fluid py-4">

    {{-- ── Top Stat Cards ── --}}
    <div class="row g-4 mb-4 fade-up delay-1">
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
    <div class="card border-0 shadow-sm mb-4 fade-up delay-2" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0">Order Analytics</h6>
        </div>
        <div class="card-body pt-0">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-clock me-1"></i> Pending</p>
                            <h5 class="fw-bold mb-0">{{ $pendingOrders ?? 0 }}</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-check2-circle me-1"></i> Confirm</p>
                            <h5 class="fw-bold mb-0">{{ $confirmedOrders ?? 0 }}</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-gear me-1"></i> Processing</p>
                            <h5 class="fw-bold mb-0">{{ $processingOrders ?? 0 }}</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-truck me-1"></i> Shipped</p>
                            <h5 class="fw-bold mb-0">{{ $shippedOrders ?? 0 }}</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-box-seam me-1"></i> Delivered</p>
                            <h5 class="fw-bold mb-0">{{ $deliveredOrders ?? 0 }}</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="analytics-box">
                        <div>
                            <p class="small text-muted mb-1"><i class="bi bi-x-circle me-1"></i> Rejected</p>
                            <h5 class="fw-bold mb-0">{{ $rejectedOrders ?? 0 }}</h5>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted small"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Shop Wallet & Stats ── --}}
    <div class="row g-4 mb-4 fade-up delay-3">
        <div class="col-md-6">
            <div class="wallet-card shadow-sm h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-wallet2 text-muted me-2"></i>
                    <span class="text-muted small fw-bold">Shop Wallet</span>
                </div>
                <h1 class="fw-bold mb-1">৳{{ number_format($totalEarnings, 2) }}</h1>
                <p class="text-success small mb-4"><i class="bi bi-graph-up-arrow me-1"></i> Available Balance</p>
                <div>
                    <a href="{{ route('seller.withdraws.index') }}" class="btn btn-withdraw">Withdraw Now</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">৳{{ number_format($pendingWithdraw, 2) }}</h5>
                            <p class="small text-muted mb-0">Pending Withdraw</p>
                        </div>
                        <i class="bi bi-credit-card text-muted"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">৳{{ number_format($alreadyWithdraw, 2) }}</h5>
                            <p class="small text-muted mb-0">Already Withdraw</p>
                        </div>
                        <i class="bi bi-cash-stack text-muted"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">৳{{ number_format($rejectedWithdraw, 2) }}</h5>
                            <p class="small text-muted mb-0">Rejected Withdraw</p>
                        </div>
                        <i class="bi bi-x-square text-muted text-danger"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">৳{{ number_format($totalWithdraw, 2) }}</h5>
                            <p class="small text-muted mb-0">Total Withdraw</p>
                        </div>
                        <i class="bi bi-pie-chart text-muted"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">{{ $totalReviews }}</h5>
                            <p class="small text-muted mb-0">Total Reviews</p>
                        </div>
                        <i class="bi bi-star text-muted"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="wallet-stat-item shadow-sm">
                        <div>
                            <h5 class="fw-bold mb-0">৳{{ number_format($totalPosSales, 2) }}</h5>
                            <p class="small text-muted mb-0">Total POS Sales</p>
                        </div>
                        <i class="bi bi-display text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Recent Orders Table ── --}}
    <div class="card border-0 shadow-sm mb-4 fade-up delay-1" style="border-radius: 15px;">
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
                        @forelse($recentOrders ?? [] as $inv)
                        <tr>
                            <td class="fw-bold">#{{ $inv->invoice_number }}</td>
                            <td>{{ count($inv->items ?? []) }}</td>
                            <td>{{ $inv->created_at->format('d M, Y') }}</td>
                            <td>
                                @php
                                    $status = $inv->order->status ?? 'pending';
                                    $badgeClass = match($status) {
                                        'pending'    => 'bg-warning',
                                        'confirmed'  => 'bg-info',
                                        'processing' => 'bg-primary',
                                        'shipped'    => 'bg-dark',
                                        'delivered'  => 'bg-success',
                                        'cancelled', 'rejected' => 'bg-danger',
                                        default      => 'bg-secondary'
                                    };
                                @endphp
                                <span class="status-badge {{ $badgeClass }} text-white">{{ ucfirst($status) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('seller.orders.show', $inv->id) }}" class="btn btn-sm btn-light text-primary"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('seller.pos.invoice', $inv->id) }}" class="btn btn-sm btn-light text-dark"><i class="bi bi-download"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No recent orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Bottom Stats ── --}}
    <div class="row g-4 fade-up delay-2">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-shop me-2 text-primary"></i> Top Selling Products</h6>
                </div>
                <div class="card-body pt-0">
                    @forelse($topProducts ?? [] as $product)
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-2 rounded me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            @if($product->thumbnail)
                                <img src="{{ asset($product->thumbnail) }}" alt="" style="max-width: 100%; max-height: 100%; border-radius: 4px;">
                            @else
                                <i class="bi bi-image text-muted"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <p class="small fw-bold mb-0 text-truncate" style="max-width: 150px;">{{ $product->name }}</p>
                            <p class="text-danger small mb-0">৳{{ number_format($product->discount_price > 0 ? $product->discount_price : $product->selling_price, 2) }}</p>
                        </div>
                        <span class="badge bg-light text-primary">Sold: {{ $product->sold_count ?? 0 }}</span>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <p class="text-muted small mb-0">No data available</p>
                    </div>
                    @endforelse
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
