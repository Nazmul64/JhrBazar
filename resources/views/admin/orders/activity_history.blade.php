@extends('admin.master')

@section('content')
<style>
    :root {
        --primary: #4361ee;
        --secondary: #7209b7;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --dark: #1e293b;
        --light: #f8fafc;
        --gray: #64748b;
        --shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        --radius: 16px;
    }

    .analytics-page {
        padding: 24px;
        background: #f1f5f9;
        min-height: 100vh;
        font-family: 'Inter', system-ui, sans-serif;
    }

    .staff-card {
        background: #ffffff;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid #e2e8f0;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .staff-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .card-header-bg {
        height: 80px;
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        position: relative;
    }

    .profile-img-wrap {
        position: absolute;
        bottom: -25px;
        left: 20px;
        width: 64px;
        height: 64px;
        border-radius: 50%;
        padding: 4px;
        background: #ffffff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .profile-img-wrap img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .role-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(5px);
        color: #fff;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .staff-details {
        padding: 35px 20px 20px;
    }

    .staff-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 2px;
    }

    .staff-email {
        font-size: 12px;
        color: var(--gray);
        margin-bottom: 15px;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 15px;
        margin-bottom: 15px;
    }

    .metric-box {
        background: #f8fafc;
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        border: 1px solid #f1f5f9;
    }

    .metric-value {
        font-size: 20px;
        font-weight: 800;
        color: var(--dark);
        line-height: 1;
        margin-bottom: 4px;
    }

    .metric-label {
        font-size: 11px;
        color: var(--gray);
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .metric-box.total { background: #eff6ff; border-color: #bfdbfe; }
    .metric-box.total .metric-value { color: #1d4ed8; }

    .metric-box.delivered { background: #f0fdf4; border-color: #bbf7d0; }
    .metric-box.delivered .metric-value { color: #15803d; }

    .metric-box.pending { background: #fffbeb; border-color: #fde68a; }
    .metric-box.pending .metric-value { color: #b45309; }

    .metric-box.processing { background: #faf5ff; border-color: #e9d5ff; }
    .metric-box.processing .metric-value { color: #7e22ce; }

    .progress-section {
        margin-top: auto;
        padding: 0 20px 20px;
    }

    .progress {
        height: 6px;
        border-radius: 50px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .progress-bar {
        background: linear-gradient(90deg, #10b981, #34d399);
        border-radius: 50px;
    }

    .progress-text {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        color: var(--gray);
        margin-bottom: 5px;
        font-weight: 600;
    }

    .view-btn {
        display: block;
        width: 100%;
        text-align: center;
        padding: 10px;
        background: #f8fafc;
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
        border-top: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .view-btn:hover {
        background: var(--primary);
        color: white;
    }
</style>

<div class="analytics-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold m-0" style="color:var(--dark); font-size:24px;">{{ $title ?? 'Staff Activity Analytics' }}</h1>
            <p class="text-muted m-0 mt-1">Track order processing performance across your team.</p>
        </div>
        <a href="{{ route('admin.orders.staff_assignments') }}" class="btn btn-primary" style="border-radius: 50px; padding: 10px 20px; font-weight: 600;">
            <i class="bi bi-card-list me-2"></i> View Assignment List
        </a>
    </div>

    <div class="row g-4">
        @forelse($staffs as $staff)
            @php
                $total = $staff->total_orders ?? 0;
                $delivered = $staff->delivered_orders ?? 0;
                $pending = $staff->pending_orders ?? 0;
                $processing = $staff->processing_orders ?? 0;
                
                $completionRate = $total > 0 ? round(($delivered / $total) * 100) : 0;
            @endphp
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="staff-card">
                    <div class="card-header-bg">
                        <span class="role-badge">{{ $staff->role }}</span>
                        <div class="profile-img-wrap">
                            <img src="{{ $staff->profile_image_url }}" alt="{{ $staff->name }}">
                        </div>
                    </div>
                    
                    <div class="staff-details">
                        <h3 class="staff-name">{{ $staff->name }}</h3>
                        <div class="staff-email"><i class="bi bi-envelope me-1"></i>{{ $staff->email }}</div>
                        
                        <div class="metrics-grid">
                            <div class="metric-box total">
                                <div class="metric-value">{{ $total }}</div>
                                <div class="metric-label">Total Handled</div>
                            </div>
                            <div class="metric-box delivered">
                                <div class="metric-value">{{ $delivered }}</div>
                                <div class="metric-label">Delivered</div>
                            </div>
                            <div class="metric-box pending">
                                <div class="metric-value">{{ $pending }}</div>
                                <div class="metric-label">Pending</div>
                            </div>
                            <div class="metric-box processing">
                                <div class="metric-value">{{ $processing }}</div>
                                <div class="metric-label">Processing</div>
                            </div>
                        </div>
                    </div>

                    <div class="progress-section">
                        <div class="progress-text">
                            <span>Completion Rate</span>
                            <span class="text-success">{{ $completionRate }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $completionRate }}%;" aria-valuenow="{{ $completionRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <a href="{{ route('admin.orders.staff_assignments', ['staff_id' => $staff->id]) }}" class="view-btn">
                        View Assigned Orders <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-3 text-muted">No staff members found.</h4>
            </div>
        @endforelse
    </div>
</div>
@endsection
