@extends('admin.master')

@section('content')
<div class="seller-dashboard-wrapper p-4">
    
    {{-- Shop Status & Stats --}}
    <div class="seller-hero mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="shop-name-badge mb-2"><i class="bi bi-shop me-2"></i> Official Store</div>
                <h2 class="seller-shop-title">{{ Auth::user()->name }}'s Shop</h2>
                <p class="text-white-50 mb-4">You have <span class="text-white fw-bold">12 new sales</span> today. Keep up the great work!</p>
                <div class="d-flex gap-3">
                    <button class="btn btn-orange px-4 py-2 fw-bold">Manage Products</button>
                    <button class="btn btn-outline-white px-4 py-2">Withdraw Earnings</button>
                </div>
            </div>
            <div class="col-lg-6 text-lg-end mt-4 mt-lg-0">
                <div class="earnings-display">
                    <span class="small">Pending Balance</span>
                    <h3>$4,890.50</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Business Metrics --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="biz-metric">
                <div class="m-val">$12,400</div>
                <div class="m-lab">Total Revenue</div>
                <div class="m-trend text-success"><i class="bi bi-arrow-up"></i> 15%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="biz-metric">
                <div class="m-val">1,245</div>
                <div class="m-lab">Total Sales</div>
                <div class="m-trend text-success"><i class="bi bi-arrow-up"></i> 8%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="biz-metric">
                <div class="m-val">420</div>
                <div class="m-lab">Active Products</div>
                <div class="m-trend text-muted">Stable</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="biz-metric">
                <div class="m-val">4.8</div>
                <div class="m-lab">Shop Rating</div>
                <div class="m-trend text-warning"><i class="bi bi-star-fill"></i> Top Rated</div>
            </div>
        </div>
    </div>

    {{-- Top Products & Inventory --}}
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="store-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0">Top Selling Products</h5>
                    <a href="#" class="small text-orange fw-bold text-decoration-none">View All Products</a>
                </div>
                <div class="table-responsive">
                    <table class="table store-table align-middle">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Sales</th>
                                <th>Stock</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="p-img"></div>
                                        <div class="ms-3 fw-bold">Premium Leather Watch</div>
                                    </div>
                                </td>
                                <td>Accessories</td>
                                <td>450</td>
                                <td><span class="stock-pill low">Low Stock (12)</span></td>
                                <td>$9,000</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="p-img"></div>
                                        <div class="ms-3 fw-bold">Wireless Headphones</div>
                                    </div>
                                </td>
                                <td>Electronics</td>
                                <td>320</td>
                                <td><span class="stock-pill ok">In Stock (145)</span></td>
                                <td>$12,800</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="store-card h-100">
                <h5 class="fw-bold mb-4">Customer Sentiment</h5>
                <div class="sentiment-row mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold">Positive</span>
                        <span class="small fw-bold">85%</span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                </div>
                <div class="sentiment-row mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold">Neutral</span>
                        <span class="small fw-bold">10%</span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar bg-warning" style="width: 10%"></div>
                    </div>
                </div>
                <div class="sentiment-row">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold">Negative</span>
                        <span class="small fw-bold">5%</span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar bg-danger" style="width: 5%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.seller-dashboard-wrapper { background: #fdfdfd; min-height: 100vh; font-family: 'Sora', sans-serif; }

/* Hero Section */
.seller-hero { 
    background: #1e1b4b; border-radius: 30px; padding: 50px; color: #fff;
    background-image: radial-gradient(circle at top right, #3730a3, transparent);
    box-shadow: 0 20px 40px rgba(30, 27, 75, 0.2);
}
.shop-name-badge { background: rgba(255,255,255,0.1); padding: 5px 15px; border-radius: 10px; font-size: 12px; font-weight: 600; display: inline-block; }
.seller-shop-title { font-weight: 800; font-size: 38px; margin: 10px 0; }
.btn-orange { background: #f97316; color: #fff; border: none; }
.btn-orange:hover { background: #ea580c; color: #fff; transform: scale(1.02); }
.btn-outline-white { border: 1px solid rgba(255,255,255,0.3); color: #fff; }
.btn-outline-white:hover { background: rgba(255,255,255,0.1); color: #fff; }

.earnings-display span { font-size: 14px; opacity: 0.7; }
.earnings-display h3 { font-size: 42px; font-weight: 800; margin-top: 5px; color: #fbbf24; }

/* Biz Metrics */
.biz-metric { background: #fff; padding: 30px; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.m-val { font-size: 28px; font-weight: 800; color: #0f172a; }
.m-lab { font-size: 13px; color: #64748b; font-weight: 600; margin: 5px 0 10px; }
.m-trend { font-size: 12px; font-weight: 700; }

/* Cards & Tables */
.store-card { background: #fff; border-radius: 24px; border: 1px solid #f1f5f9; padding: 30px; }
.store-table th { background: transparent; border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 12px; text-transform: uppercase; }
.p-img { width: 40px; height: 40px; background: #f8fafc; border-radius: 10px; }
.text-orange { color: #f97316; }

.stock-pill { padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 600; }
.stock-pill.low { background: #fef2f2; color: #ef4444; }
.stock-pill.ok { background: #f0fdf4; color: #22c55e; }
</style>
@endsection
