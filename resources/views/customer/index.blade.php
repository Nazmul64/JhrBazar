@extends('admin.master')

@section('content')
<div class="customer-dashboard-wrapper p-4">
    {{-- Header Section with Glassmorphism --}}
    <div class="customer-welcome-card mb-5">
        <div class="row align-items-center">
            <div class="col-md-auto text-center mb-3 mb-md-0">
                <div class="customer-avatar-wrapper">
                    <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('admin/images/default-avatar.png') }}" alt="Profile" class="customer-profile-img">
                    <div class="status-online-dot"></div>
                </div>
            </div>
            <div class="col-md ps-md-4">
                <h2 class="welcome-title">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                <p class="welcome-subtitle">You have <span class="highlight">3 active orders</span> and <span class="highlight">12 items</span> in your wishlist.</p>
                <div class="welcome-stats-row">
                    <div class="w-stat"><i class="bi bi-star-fill text-warning"></i> Gold Member</div>
                    <div class="w-stat"><i class="bi bi-geo-alt-fill text-danger"></i> Dhaka, Bangladesh</div>
                </div>
            </div>
            <div class="col-md-auto">
                <a href="{{ route('admin.profile.index') }}" class="btn-edit-profile">
                    <i class="bi bi-pencil-square"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    {{-- Main Stats - Unique Customer Style --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4 col-xl-3">
            <div class="customer-stat-box box-teal">
                <div class="icon-circle"><i class="bi bi-bag-check-fill"></i></div>
                <div class="stat-content">
                    <h3>08</h3>
                    <p>Total Orders</p>
                </div>
                <div class="box-bg-icon"><i class="bi bi-bag"></i></div>
            </div>
        </div>
        <div class="col-md-4 col-xl-3">
            <div class="customer-stat-box box-pink">
                <div class="icon-circle"><i class="bi bi-heart-fill"></i></div>
                <div class="stat-content">
                    <h3>24</h3>
                    <p>Wishlist Items</p>
                </div>
                <div class="box-bg-icon"><i class="bi bi-heart"></i></div>
            </div>
        </div>
        <div class="col-md-4 col-xl-3">
            <div class="customer-stat-box box-purple">
                <div class="icon-circle"><i class="bi bi-wallet2"></i></div>
                <div class="stat-content">
                    <h3>$1,240</h3>
                    <p>Total Spending</p>
                </div>
                <div class="box-bg-icon"><i class="bi bi-cash-stack"></i></div>
            </div>
        </div>
        <div class="col-md-4 col-xl-3">
            <div class="customer-stat-box box-orange">
                <div class="icon-circle"><i class="bi bi-ticket-perforated-fill"></i></div>
                <div class="stat-content">
                    <h3>05</h3>
                    <p>Active Coupons</p>
                </div>
                <div class="box-bg-icon"><i class="bi bi-ticket"></i></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Recent Activity --}}
        <div class="col-lg-8">
            <div class="glass-card">
                <div class="card-head d-flex justify-content-between align-items-center mb-4">
                    <h5 class="m-0 fw-bold"><i class="bi bi-clock-history me-2"></i> Recent Order Status</h5>
                    <button class="btn btn-sm btn-link text-decoration-none">View All</button>
                </div>
                <div class="order-timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker status-done"></div>
                        <div class="timeline-info">
                            <h6 class="mb-1">Order #ORD-9921 Delivered</h6>
                            <p class="text-muted small mb-0">Today at 12:45 PM • iPhone 15 Pro</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker status-active"></div>
                        <div class="timeline-info">
                            <h6 class="mb-1">Order #ORD-9945 Shipped</h6>
                            <p class="text-muted small mb-0">Yesterday • Wireless Keyboard</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Points Wallet --}}
        <div class="col-lg-4">
            <div class="points-wallet-card">
                <div class="wallet-top">
                    <span>Loyalty Points</span>
                    <h4>2,450 Pts</h4>
                </div>
                <div class="wallet-bottom">
                    <p class="mb-2 small">You're 550 pts away from next reward!</p>
                    <div class="progress rounded-pill" style="height: 6px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar bg-white" style="width: 75%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.customer-dashboard-wrapper { background: #f0f4f9; min-height: 100vh; font-family: 'Outfit', sans-serif; }

/* Welcome Card */
.customer-welcome-card {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    padding: 35px; border-radius: 24px; color: #fff;
    box-shadow: 0 20px 40px rgba(30, 41, 59, 0.15);
}
.customer-avatar-wrapper { position: relative; width: 100px; height: 100px; margin: 0 auto; }
.customer-profile-img { width: 100%; height: 100%; border-radius: 30px; object-fit: cover; border: 4px solid rgba(255,255,255,0.2); }
.status-online-dot { 
    position: absolute; bottom: 5px; right: 5px; width: 18px; height: 18px; 
    background: #10b981; border: 3px solid #1e293b; border-radius: 50%;
}
.welcome-title { font-weight: 800; font-size: 28px; letter-spacing: -0.5px; }
.welcome-subtitle { color: #94a3b8; font-size: 16px; margin-bottom: 15px; }
.highlight { color: #38bdf8; font-weight: 700; }
.welcome-stats-row { display: flex; gap: 20px; }
.w-stat { font-size: 14px; background: rgba(255,255,255,0.1); padding: 5px 15px; border-radius: 10px; }
.btn-edit-profile { 
    background: #38bdf8; color: #fff; border: none; padding: 12px 24px; 
    border-radius: 12px; font-weight: 600; text-decoration: none; transition: 0.3s;
}
.btn-edit-profile:hover { background: #0ea5e9; color: #fff; transform: translateY(-2px); }

/* Stat Boxes */
.customer-stat-box {
    position: relative; padding: 25px; border-radius: 20px; overflow: hidden;
    color: #fff; transition: 0.3s; cursor: pointer; height: 100%;
}
.customer-stat-box:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
.box-teal { background: #14b8a6; }
.box-pink { background: #ec4899; }
.box-purple { background: #8b5cf6; }
.box-orange { background: #f59e0b; }

.icon-circle { 
    width: 45px; height: 45px; background: rgba(255,255,255,0.2); 
    border-radius: 12px; display: flex; align-items: center; justify-content: center;
    font-size: 20px; margin-bottom: 15px;
}
.stat-content h3 { font-size: 32px; font-weight: 800; margin: 0; }
.stat-content p { font-size: 14px; opacity: 0.9; margin: 0; }
.box-bg-icon { 
    position: absolute; right: -10px; bottom: -10px; font-size: 80px; 
    opacity: 0.1; transform: rotate(-15deg); 
}

/* Glass Card */
.glass-card { background: #fff; padding: 25px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
.timeline-item { position: relative; padding-left: 30px; margin-bottom: 25px; }
.timeline-marker { 
    position: absolute; left: 0; top: 5px; width: 12px; height: 12px; 
    border-radius: 50%; background: #ddd; 
}
.timeline-marker.status-done { background: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2); }
.timeline-marker.status-active { background: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2); }
.timeline-item::before {
    content: ''; position: absolute; left: 5px; top: 20px; 
    width: 2px; height: calc(100% + 5px); background: #f1f5f9;
}
.timeline-item:last-child::before { display: none; }

/* Points Wallet */
.points-wallet-card {
    background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
    padding: 30px; border-radius: 24px; color: #fff;
    background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 86c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm66-3c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM37 17c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm63 85c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM82 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-56 8c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM49 82c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM3 54c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm40 5c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm25-27c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM6 8c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm22 90c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm53-2c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm28-88c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM2 30c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm35 57c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm58-77c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM30 46c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm51 52c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-19-64c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-9 37c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-48 48c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM25 73c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-10-10c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm65 1c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM12 25c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm76 26c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM39 57c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-8-34c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm18 9c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm27 54c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-33-2c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM41 20c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm52 29c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-25 25c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM10 16c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm35 1c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm26 46c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-4-14c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm11 33c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-66 3c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm91-8c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm18 40c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-27-15c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-62 33c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm14-41c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM31 18c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm42 31c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-30 41c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM1 1c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
}
.wallet-top span { font-size: 14px; opacity: 0.8; }
.wallet-top h4 { font-size: 32px; font-weight: 800; margin: 5px 0 20px; }
</style>
@endsection
