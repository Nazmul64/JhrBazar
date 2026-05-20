@extends('admin.master')

@section('content')
@php
    $cur = $settings->default_currency ?? '৳';
    $totalTopPageViews = $topPages->sum('total') ?: 1;
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap');

    :root {
        --cd-primary: #3b82f6;
        --cd-dark: #0f172a;
        --cd-card-bg: #ffffff;
        --cd-body-bg: #f8fafc;
        --cd-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        --cd-radius: 12px;
    }

    .cd-dashboard {
        font-family: 'Outfit', 'Hind Siliguri', sans-serif;
        background: var(--cd-body-bg);
        padding: 1.5rem;
    }

    /* Page Header */
    .cd-page-header {
        margin-bottom: 1.5rem;
    }
    .cd-page-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }
    .cd-breadcrumb {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }
    .cd-breadcrumb a {
        color: #64748b;
        text-decoration: none;
    }
    .cd-breadcrumb span {
        margin: 0 0.5rem;
    }

    /* Welcome Banner */
    .cd-banner {
        background: linear-gradient(135deg, #0b1329 0%, #1a233d 100%);
        border-radius: var(--cd-radius);
        padding: 1.75rem 2rem;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(11, 19, 41, 0.15);
    }
    .cd-banner-icon {
        font-size: 2.5rem;
        color: #f59e0b;
        animation: cdRadarPulse 2s infinite;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cd-banner-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #f59e0b;
        margin-bottom: 0.4rem;
    }
    .cd-banner-desc {
        font-size: 0.92rem;
        color: #cbd5e1;
        font-weight: 400;
        line-height: 1.5;
        margin: 0;
    }

    /* Stats Grid */
    .cd-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1200px) {
        .cd-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .cd-stats-grid {
            grid-template-columns: 1fr;
        }
    }

    .cd-stat-card {
        background: var(--cd-card-bg);
        border-radius: var(--cd-radius);
        padding: 1.5rem;
        box-shadow: var(--cd-shadow);
        border: 1px solid #e2e8f0;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
    }
    .cd-stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .cd-stat-label {
        font-size: 0.95rem;
        color: #475569;
        font-weight: 600;
    }
    .cd-stat-value {
        font-size: 2.2rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: 0.5rem;
        line-height: 1;
    }
    .cd-stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .cd-stat-card-blue { border-left: 4px solid #3b82f6; }
    .cd-stat-card-blue .cd-stat-icon-wrapper { background: #eff6ff; color: #3b82f6; }
    
    .cd-stat-card-green { border-left: 4px solid #10b981; }
    .cd-stat-card-green .cd-stat-icon-wrapper { background: #ecfdf5; color: #10b981; }
    
    .cd-stat-card-cyan { border-left: 4px solid #06b6d4; }
    .cd-stat-card-cyan .cd-stat-icon-wrapper { background: #ecfeff; color: #06b6d4; }

    .cd-stat-footer-badge {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        width: fit-content;
        margin-top: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .cd-stat-footer-badge-blue { background: #eff6ff; color: #3b82f6; }
    .cd-stat-footer-badge-green { background: #ecfdf5; color: #10b981; }
    .cd-stat-footer-badge-cyan { background: #ecfeff; color: #06b6d4; }

    /* Top Pages Card */
    .cd-top-pages-card {
        background: var(--cd-card-bg);
        border-radius: var(--cd-radius);
        padding: 1.25rem;
        box-shadow: var(--cd-shadow);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .cd-top-pages-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .cd-top-page-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .cd-top-page-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        color: #475569;
        font-weight: 600;
    }
    .cd-top-page-name {
        display: flex;
        align-items: center;
        gap: 4px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 170px;
    }
    .cd-top-page-progress-bg {
        height: 6px;
        background: #f1f5f9;
        border-radius: 3px;
        overflow: hidden;
    }
    .cd-top-page-progress-bar {
        height: 100%;
        background: #f59e0b;
        border-radius: 3px;
    }

    /* Advanced Filters Section */
    .cd-filters-box {
        background: #ffffff;
        border-radius: var(--cd-radius);
        padding: 1.25rem;
        box-shadow: var(--cd-shadow);
        border: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }
    .cd-filters-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .form-grid-custom {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.75rem;
    }
    @media (max-width: 992px) {
        .form-grid-custom {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 576px) {
        .form-grid-custom {
            grid-template-columns: 1fr;
        }
    }
    .filter-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.35rem;
    }
    .filter-input {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 0.5rem;
        font-size: 0.85rem;
        width: 100%;
        font-weight: 500;
        color: #334155;
    }
    .filter-input:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
    }
    .filter-btn-container {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1rem;
    }
    .btn-filter-reset {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-filter-reset:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .btn-filter-submit {
        background: #3b82f6;
        color: #ffffff;
        border: none;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-filter-submit:hover {
        background: #2563eb;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }

    /* Checklist Header */
    .cd-list-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .btn-delete-selected {
        background: #ef4444;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-delete-selected:hover {
        background: #dc2626;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }

    /* Visitor Cards Grid */
    .cd-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1100px) {
        .cd-cards-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .cd-cards-grid {
            grid-template-columns: 1fr;
        }
    }

    .cd-visitor-card {
        background: #ffffff;
        border-radius: var(--cd-radius);
        border: 1px solid #e2e8f0;
        box-shadow: var(--cd-shadow);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }
    .cd-visitor-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    }

    .cd-card-header {
        padding: 1rem;
        background: #fafbfd;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .cd-card-header-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cd-online-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        display: inline-block;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
    }
    .cd-online-text {
        font-size: 0.8rem;
        color: #10b981;
        font-weight: 700;
    }
    .cd-offline-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #94a3b8;
        display: inline-block;
    }
    .cd-offline-text {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
    }
    .cd-visit-count-badge {
        background: #334155;
        color: #ffffff;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .cd-card-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        flex-grow: 1;
    }

    /* Timestamps */
    .cd-time-row {
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .cd-period-row {
        display: flex;
        gap: 6px;
    }
    .cd-tag-period {
        background: #fefbeb;
        color: #d97706;
        border: 1px solid #fef3c7;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .cd-tag-page {
        background: #eff6ff;
        color: #3b82f6;
        border: 1px solid #dbeafe;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    /* Visitor Identity */
    .cd-visitor-info-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        padding: 0.75rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .cd-visitor-name {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .cd-visitor-phone {
        font-size: 0.85rem;
        color: #3b82f6;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .btn-call-phone {
        background: #10b981;
        color: white;
        border: none;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-call-phone:hover {
        background: #059669;
        color: white;
    }

    /* Purchase History */
    .cd-purchase-box {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .cd-purchase-header {
        background: #f8fafc;
        padding: 0.6rem 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
    }
    .cd-total-spent-badge {
        background: #0f172a;
        color: #ffffff;
        padding: 0.15rem 0.4rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .cd-product-list {
        padding: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        max-height: 150px;
        overflow-y: auto;
    }
    .cd-product-item {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .cd-product-img {
        width: 38px;
        height: 38px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
    }
    .cd-product-details {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .cd-product-title {
        font-size: 0.78rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .cd-product-price {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Order Control Box */
    .cd-control-box {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }
    .cd-control-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .cd-order-status-badge {
        padding: 0.15rem 0.5rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .cd-status-pending { background: #fffbeb; color: #d97706; }
    .cd-status-delivered { background: #ecfdf5; color: #10b981; }
    .cd-status-cancelled { background: #fef2f2; color: #ef4444; }

    .cd-status-select {
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        padding: 0.35rem 0.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #334155;
        width: 100%;
        background: #f8fafc;
    }
    .cd-courier-btn-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }
    .btn-courier-sf {
        background: #f59e0b;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.45rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: all 0.2s;
    }
    .btn-courier-sf:hover {
        background: #d97706;
        color: white;
    }
    .btn-courier-pathao {
        background: #06b6d4;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.45rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: all 0.2s;
    }
    .btn-courier-pathao:hover {
        background: #0891b2;
        color: white;
    }

    .cd-card-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }
    .btn-action-outline {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 0.4rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-action-outline:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    /* Card Footer Metadata */
    .cd-card-footer {
        padding: 0.75rem 1rem;
        background: #fafbfd;
        border-top: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.72rem;
        color: #94a3b8;
        font-weight: 500;
    }

    /* Custom CSS pulsing animations */
    @keyframes cdRadarPulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<div class="cd-dashboard">
    <!-- Breadcrumbs and Header -->
    <div class="cd-page-header">
        <h1 class="cd-page-title">Customer Site Visited (কাস্টমার ডিটেক্টর)</h1>
        <div class="cd-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span>&gt;</span>
            <a href="#">Customers</a>
            <span>&gt;</span>
            <span style="color: #0f172a; font-weight: 600;">Realtime Visitor Logs</span>
        </div>
    </div>

    <!-- Live returning banner -->
    <div class="cd-banner">
        <div class="cd-banner-icon">
            <i class="bi bi-broadcast"></i>
        </div>
        <div>
            <h2 class="cd-banner-title">লাইভ রিটার্নিং কাস্টমার ট্র্যাকার</h2>
            <p class="cd-banner-desc">পূর্বে অর্ডার করা যেকোনো কাস্টমার যখনই আপনার ওয়েবসাইট ব্রাউজ করবেন, সিস্টেম স্বয়ংক্রিয়ভাবে তার পরিচয় ও অর্ডার হিস্ট্রি সহ এখানে তালিকাভুক্ত করবে।</p>
        </div>
    </div>

    <!-- Analytics Stats Grid -->
    <div class="cd-stats-grid">
        <!-- Card 1 -->
        <div class="cd-stat-card cd-stat-card-blue">
            <div class="cd-stat-header">
                <div>
                    <div class="cd-stat-label">আজকের মোট ভিজিট</div>
                    <div class="cd-stat-value">{{ number_format($todayVisits) }}</div>
                </div>
                <div class="cd-stat-icon-wrapper">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
            </div>
            <div class="cd-stat-footer-badge cd-stat-footer-badge-blue">
                <i class="bi bi-clock-history"></i> Today
            </div>
        </div>

        <!-- Card 2 -->
        <div class="cd-stat-card cd-stat-card-green">
            <div class="cd-stat-header">
                <div>
                    <div class="cd-stat-label">গত ৭ দিনের ভিজিট</div>
                    <div class="cd-stat-value">{{ number_format($last7DaysVisits) }}</div>
                </div>
                <div class="cd-stat-icon-wrapper">
                    <i class="bi bi-calendar-week-fill"></i>
                </div>
            </div>
            <div class="cd-stat-footer-badge cd-stat-footer-badge-green">
                <i class="bi bi-graph-up-arrow"></i> Last 7 Days
            </div>
        </div>

        <!-- Card 3 -->
        <div class="cd-stat-card cd-stat-card-cyan">
            <div class="cd-stat-header">
                <div>
                    <div class="cd-stat-label">গত ৩০ দিনের ভিজিট</div>
                    <div class="cd-stat-value">{{ number_format($last30DaysVisits) }}</div>
                </div>
                <div class="cd-stat-icon-wrapper">
                    <i class="bi bi-calendar2-range-fill"></i>
                </div>
            </div>
            <div class="cd-stat-footer-badge cd-stat-footer-badge-cyan">
                <i class="bi bi-activity"></i> Last 30 Days
            </div>
        </div>

        <!-- Top Visited Pages (Progress Card) -->
        <div class="cd-top-pages-card">
            <div class="cd-top-pages-title">
                <i class="bi bi-bar-chart-fill text-warning"></i> আজকের টপ পেজ ভিজিট
            </div>
            @forelse($topPages as $top)
                @php
                    $percentage = round(($top->total / $totalTopPageViews) * 100);
                    // Map names
                    $pageDisplayName = $top->page_visited;
                    if (strpos($pageDisplayName, 'checkout') !== false || strtolower($pageDisplayName) === 'checkout') {
                        $pageDisplayName = 'চেকআউট পেজ';
                    } elseif ($pageDisplayName === '/' || strtolower($pageDisplayName) === 'home' || strtolower($pageDisplayName) === 'index') {
                        $pageDisplayName = 'হোম পেজ';
                    } elseif (strtolower($pageDisplayName) === 'cart') {
                        $pageDisplayName = 'কার্ট পেজ';
                    }
                @endphp
                <div class="cd-top-page-item">
                    <div class="cd-top-page-meta">
                        <span class="cd-top-page-name"><i class="bi bi-file-earmark-check-fill text-secondary"></i> {{ $pageDisplayName }}</span>
                        <span>{{ $top->total }}টি ({{ $percentage }}%)</span>
                    </div>
                    <div class="cd-top-page-progress-bg">
                        <div class="cd-top-page-progress-bar" style="width: {{ $percentage }}%;"></div>
                    </div>
                </div>
            @empty
                <span class="text-muted small py-2">No visits tracked yet.</span>
            @endforelse
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="cd-filters-box">
        <div class="cd-filters-title">
            <i class="bi bi-funnel-fill text-primary"></i> অ্যাডভান্সড ফিল্টারস (Advanced Visit Filters)
        </div>
        <form action="{{ route('admin.customer-detector.index') }}" method="GET">
            <div class="form-grid-custom">
                <div>
                    <label class="filter-label">শুরু তারিখ (Start Date)</label>
                    <input type="date" name="start_date" class="filter-input" value="{{ request('start_date') }}">
                </div>
                <div>
                    <label class="filter-label">শেষ তারিখ (End Date)</label>
                    <input type="date" name="end_date" class="filter-input" value="{{ request('end_date') }}">
                </div>
                <div>
                    <label class="filter-label">সপ্তাহের দিন (Day of Week)</label>
                    <select name="day_of_week" class="filter-input">
                        <option value="">সব দিন (All Days)</option>
                        <option value="1" {{ request('day_of_week') == '1' ? 'selected' : '' }}>Sunday</option>
                        <option value="2" {{ request('day_of_week') == '2' ? 'selected' : '' }}>Monday</option>
                        <option value="3" {{ request('day_of_week') == '3' ? 'selected' : '' }}>Tuesday</option>
                        <option value="4" {{ request('day_of_week') == '4' ? 'selected' : '' }}>Wednesday</option>
                        <option value="5" {{ request('day_of_week') == '5' ? 'selected' : '' }}>Thursday</option>
                        <option value="6" {{ request('day_of_week') == '6' ? 'selected' : '' }}>Friday</option>
                        <option value="7" {{ request('day_of_week') == '7' ? 'selected' : '' }}>Saturday</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">ভিজিট পিরিয়ড (Period)</label>
                    <select name="period" class="filter-input">
                        <option value="">সব সময় (All Time)</option>
                        <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today (আজ)</option>
                        <option value="yesterday" {{ request('period') === 'yesterday' ? 'selected' : '' }}>Yesterday (গতকাল)</option>
                        <option value="7days" {{ request('period') === '7days' ? 'selected' : '' }}>Last 7 Days (৭ দিন)</option>
                        <option value="30days" {{ request('period') === '30days' ? 'selected' : '' }}>Last 30 Days (৩০ দিন)</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">নির্দিষ্ট ঘণ্টা (Hour of Day)</label>
                    <select name="hour_of_day" class="filter-input">
                        <option value="">সব ঘণ্টা (All Hours)</option>
                        @for($h = 0; $h < 24; $h++)
                            @php
                                $hourLabel = date('h A', strtotime("$h:00"));
                            @endphp
                            <option value="{{ $h }}" {{ request('hour_of_day') == $h ? 'selected' : '' }}>{{ $hourLabel }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="filter-btn-container">
                <a href="{{ route('admin.customer-detector.index') }}" class="btn-filter-reset">
                    <i class="bi bi-x-circle"></i> ফিল্টার রিসেট
                </a>
                <button type="submit" class="btn-filter-submit">
                    <i class="bi bi-search"></i> ফিল্টার করুন
                </button>
            </div>
        </form>
    </div>

    <!-- Multi-selection and card streams -->
    <form id="cd-bulk-form" action="{{ route('admin.customer-detector.bulk-delete') ?? '#' }}" method="POST">
        @csrf
        <div class="cd-list-header">
            <div class="d-flex align-items-center gap-2">
                <input type="checkbox" id="cd-select-all" class="form-check-input" style="width:1.2rem; height:1.2rem; cursor:pointer;">
                <label for="cd-select-all" class="fw-bold text-dark mb-0" style="cursor:pointer; font-size: 0.95rem;">Select All</label>
            </div>
            <button type="button" class="btn-delete-selected" onclick="submitCdBulkDelete()">
                <i class="bi bi-trash-fill"></i> Delete Selected
            </button>
        </div>

        <div class="cd-cards-grid">
            @forelse($visits as $visit)
                @php
                    $isOnline = $visit->visited_at->gt(now()->subMinutes(5));
                    $visitDateStr = $visit->visited_at->format('l, d M Y - h:i A');
                    $visitPeriod = $visit->visited_at->format('H') >= 6 && $visit->visited_at->format('H') < 18 ? 'দিন (Day Visit)' : 'রাত (Night Visit)';
                    $visitPeriodIcon = $visit->visited_at->format('H') >= 6 && $visit->visited_at->format('H') < 18 ? 'bi-sun-fill' : 'bi-moon-stars-fill';
                    
                    // Display names for page
                    $pDisplay = $visit->page_visited;
                    if (strpos($pDisplay, 'checkout') !== false || strtolower($pDisplay) === 'checkout') {
                        $pDisplay = 'চেকআউট পেজ';
                    } elseif ($pDisplay === '/' || strtolower($pDisplay) === 'home' || strtolower($pDisplay) === 'index') {
                        $pDisplay = 'হোম পেজ';
                    } elseif (strtolower($pDisplay) === 'cart') {
                        $pDisplay = 'কার্ট পেজ';
                    }
                @endphp
                <div class="cd-visitor-card">
                    <!-- Card Top Header -->
                    <div class="cd-card-header">
                        <div class="cd-card-header-left">
                            <input type="checkbox" name="ids[]" value="{{ $visit->id }}" class="cd-checkbox form-check-input" style="cursor:pointer;">
                            @if($isOnline)
                                <span class="cd-online-dot"></span>
                                <span class="cd-online-text">অনলাইন (Active Now)</span>
                            @else
                                <span class="cd-offline-dot"></span>
                                <span class="cd-offline-text">শেষ একটিভ: {{ $visit->visited_at->diffForHumans() }}</span>
                            @endif
                        </div>
                        <div class="cd-visit-count-badge">মোট ভিজিট: {{ $visit->total_user_visits }} বার</div>
                    </div>

                    <!-- Card Body Content -->
                    <div class="cd-card-body">
                        <!-- Date & Period tags -->
                        <div class="cd-time-row">
                            <i class="bi bi-calendar3 text-danger"></i> {{ $visitDateStr }}
                        </div>
                        <div class="cd-period-row">
                            <span class="cd-tag-period">
                                <i class="bi {{ $visitPeriodIcon }}"></i> {{ $visitPeriod }}
                            </span>
                            <span class="cd-tag-page">
                                <i class="bi bi-file-earmark-check-fill"></i> {{ $pDisplay }}
                            </span>
                        </div>

                        <!-- Customer Identity Info -->
                        <div class="cd-visitor-info-box">
                            <div>
                                <div class="cd-visitor-name">{{ $visit->customer_name ?? 'Guest Visitor' }}</div>
                                <a href="tel:{{ $visit->phone_number }}" class="cd-visitor-phone">
                                    <i class="bi bi-telephone-fill"></i> {{ $visit->phone_number }}
                                </a>
                            </div>
                            <a href="tel:{{ $visit->phone_number }}" class="btn-call-phone">
                                <i class="bi bi-telephone-outbound-fill"></i> কল দিন
                            </a>
                        </div>

                        <!-- Purchase History Block -->
                        @php
                            $activeOrder = $visit->previous_orders ? $visit->previous_orders->first() : null;
                            $orderTotal = $activeOrder ? $activeOrder->grand_total : 0;
                        @endphp
                        <div class="cd-purchase-box">
                            <div class="cd-purchase-header">
                                <span><i class="bi bi-bag-check-fill text-success"></i> অর্ডারকৃত প্রোডাক্ট</span>
                                <span class="cd-total-spent-badge">Total: {{ $cur }}{{ number_format($orderTotal, 0) }}</span>
                            </div>
                            <div class="cd-product-list">
                                @php $hasItems = false; @endphp
                                @if($activeOrder && !empty($activeOrder->items) && is_array($activeOrder->items))
                                    @foreach($activeOrder->items as $item)
                                        @php $hasItems = true; @endphp
                                        <div class="cd-product-item">
                                            <img class="cd-product-img" src="{{ $item['image'] ?? '/assets/images/placeholder.png' }}" onerror="this.src='/assets/images/placeholder.png'">
                                            <div class="cd-product-details">
                                                <span class="cd-product-title">{{ $item['title'] ?? 'Product item' }}</span>
                                                <span class="cd-product-price">{{ $cur }}{{ number_format($item['price'] ?? 0, 0) }} &times; {{ $item['qty'] ?? 1 }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                
                                @if(!$hasItems)
                                    <span class="text-muted small p-2 text-center">No ordered items found.</span>
                                @endif
                            </div>
                        </div>

                        <!-- Order Control Panel (First active order) -->
                        @if($activeOrder)
                            <div class="cd-control-box">
                                <div class="cd-control-title">
                                    <span>⚙️ অর্ডার কন্ট্রোল (#{{ $activeOrder->invoice_number }})</span>
                                    <span class="cd-order-status-badge cd-status-{{ $activeOrder->status ?? 'pending' }}">
                                        {{ ucfirst($activeOrder->status ?? 'pending') }}
                                    </span>
                                </div>
                                <select class="cd-status-select" onchange="updateOrderStatus({{ $activeOrder->id }}, this.value)">
                                    <option value="pending" {{ $activeOrder->status === 'pending' ? 'selected' : '' }}>স্ট্যাটাস: ⌛ Pending</option>
                                    <option value="processing" {{ $activeOrder->status === 'processing' ? 'selected' : '' }}>স্ট্যাটাস: ⚙️ Processing</option>
                                    <option value="shipped" {{ $activeOrder->status === 'shipped' ? 'selected' : '' }}>স্ট্যাটাস: 🚚 Shipped</option>
                                    <option value="delivered" {{ $activeOrder->status === 'delivered' ? 'selected' : '' }}>স্ট্যাটাস: ✓ Delivered</option>
                                    <option value="cancelled" {{ $activeOrder->status === 'cancelled' ? 'selected' : '' }}>স্ট্যাটাস: ✗ Cancelled</option>
                                </select>
                                <div class="cd-courier-btn-row">
                                    <button type="button" class="btn-courier-sf" onclick="sendToSteadfast({{ $activeOrder->id }})">
                                        <i class="bi bi-box-seam"></i> Steadfast পাঠান
                                    </button>
                                    <button type="button" class="btn-courier-pathao" onclick="sendToPathao({{ $activeOrder->id }})">
                                        <i class="bi bi-bicycle"></i> Pathao পাঠান
                                    </button>
                                </div>
                                <div class="cd-card-actions">
                                    <a href="{{ route('admin.orders.show', $activeOrder->id) }}" target="_blank" class="btn-action-outline">
                                        <i class="bi bi-eye"></i> বিস্তারিত
                                    </a>
                                    <a href="{{ route('admin.orders.show', $activeOrder->id) }}" target="_blank" class="btn-action-outline">
                                        <i class="bi bi-pencil-square"></i> এডিট করুন
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="p-3 text-center rounded border" style="background:#fffbeb; border-color:#fde68a !important; font-size:0.8rem; font-weight:600; color:#d97706;">
                                ⚠️ No active control panel available for guests.
                            </div>
                        @endif
                    </div>

                    <!-- Card Footer Device / IP -->
                    <div class="cd-card-footer">
                        <span><i class="bi bi-laptop"></i> {{ Str::limit($visit->user_agent, 20) }}</span>
                        <span>IP: {{ $visit->ip_address }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-5 card rounded-3 border-0 shadow-sm">
                    <div class="display-1 text-muted"><i class="bi bi-eye-slash-fill"></i></div>
                    <h4 class="fw-bold text-dark mt-3">No Visitor Pathway Data Found</h4>
                    <p class="text-secondary small">Visitor paths will list here when order matches are identified.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $visits->appends(request()->query())->links() }}
        </div>
    </form>
</div>

{{-- Pathao Details Modal --}}
<div class="modal fade" id="pathaoModal" tabindex="-1" aria-hidden="true" style="font-family: 'Outfit', sans-serif;">
    <div class="modal-dialog modal-md">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom: 1px solid #edf2f7; background: #f8fafc; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title" style="font-weight: 700; color: #0f172a;">Pathao Courier Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pathaoForm">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600; font-size:0.85rem; color:#475569;">Store</label>
                        <select class="form-select" id="pathaoStore" required style="border-radius:8px;">
                            <option value="">Select Store</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600; font-size:0.85rem; color:#475569;">City</label>
                        <select class="form-select" id="pathaoCity" required style="border-radius:8px;">
                            <option value="">Select City</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600; font-size:0.85rem; color:#475569;">Zone</label>
                        <select class="form-select" id="pathaoZone" required disabled style="border-radius:8px;">
                            <option value="">Select Zone</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600; font-size:0.85rem; color:#475569;">Area</label>
                        <select class="form-select" id="pathaoArea" required disabled style="border-radius:8px;">
                            <option value="">Select Area</option>
                        </select>
                    </div>
                    <input type="hidden" id="pathaoOrderIds">
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #edf2f7;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:8px; font-weight:600;">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSubmitPathao" onclick="submitPathaoBulk()" style="border-radius:8px; background:#3b82f6; border:none; font-weight:700;">Send to Pathao</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Checklist multi select toggles
    $(document).ready(function() {
        $('#cd-select-all').on('click', function() {
            $('.cd-checkbox').prop('checked', this.checked);
        });
        $('.cd-checkbox').on('click', function() {
            if (!this.checked) {
                $('#cd-select-all').prop('checked', false);
            }
        });
    });

    // Delete selected records
    function submitCdBulkDelete() {
        const selected = Array.from(document.querySelectorAll('.cd-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            Swal.fire('Error', 'Please select at least one record to delete.', 'error');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selected.length} visitor logs. This cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Post to bulk delete route (using Laravel standard form submit or AJAX)
                const form = document.getElementById('cd-bulk-form');
                form.submit();
            }
        });
    }

    // Change order status
    function updateOrderStatus(orderId, newStatus) {
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to change this order status to ${newStatus}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, change status'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ route('admin.orders.bulk-action') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ action: 'status:' + newStatus, ids: [orderId] })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success || data.status === 'success') {
                        Swal.fire('Success', 'Order status updated successfully!', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'Failed to update status.', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'An error occurred.', 'error');
                });
            }
        });
    }

    // Steadfast Shipment Integration
    function sendToSteadfast(orderId) {
        Swal.fire({
            title: 'Confirm Shipment?',
            text: 'Send this order data directly to Steadfast Courier API?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Send to Steadfast'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ route('admin.orders.bulk-action') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ action: 'steadfast', ids: [orderId] })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success || data.status === 'success') {
                        Swal.fire('Success', 'Order sent to Steadfast successfully!', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'Failed to dispatch to Steadfast.', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'An error occurred during shipment.', 'error');
                });
            }
        });
    }

    /* Pathao Modal Integration */
    function sendToPathao(orderId) {
        document.getElementById('pathaoOrderIds').value = orderId;
        const modal = new bootstrap.Modal(document.getElementById('pathaoModal'));
        modal.show();
        
        // Load Stores & Cities
        fetchPathaoData('stores', 'pathaoStore');
        fetchPathaoData('cities', 'pathaoCity');
    }

    function fetchPathaoData(type, targetId, parentId = null) {
        let url = `{{ url('admin/orders/pathao') }}/${type}`;
        if (parentId) url = `{{ url('admin/orders/pathao') }}/${type}/${parentId}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById(targetId);
                select.innerHTML = `<option value="">Select ${type.charAt(0).toUpperCase() + type.slice(1, -1)}</option>`;
                data.forEach(item => {
                    select.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                });
                select.disabled = false;
            });
    }

    document.getElementById('pathaoCity').addEventListener('change', function() {
        if (this.value) fetchPathaoData('zones', 'pathaoZone', this.value);
        else document.getElementById('pathaoZone').disabled = true;
    });

    document.getElementById('pathaoZone').addEventListener('change', function() {
        if (this.value) fetchPathaoData('areas', 'pathaoArea', this.value);
        else document.getElementById('pathaoArea').disabled = true;
    });

    function submitPathaoBulk() {
        const ids = document.getElementById('pathaoOrderIds').value.split(',');
        const storeId = document.getElementById('pathaoStore').value;
        const cityId = document.getElementById('pathaoCity').value;
        const zoneId = document.getElementById('pathaoZone').value;
        const areaId = document.getElementById('pathaoArea').value;

        if (!storeId || !cityId || !zoneId || !areaId) {
            Swal.fire('Error', 'Please fill all fields.', 'error');
            return;
        }

        const btn = document.getElementById('btnSubmitPathao');
        btn.disabled = true;
        btn.innerHTML = 'Sending...';

        fetch(`{{ route('admin.orders.bulk-action') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                action: 'pathao',
                ids: ids,
                store_id: storeId,
                city_id: cityId,
                zone_id: zoneId,
                area_id: areaId
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Send to Pathao';
            
            // Hide modal
            bootstrap.Modal.getInstance(document.getElementById('pathaoModal')).hide();

            if (data.success || data.status === 'success') {
                Swal.fire('Success', 'Order sent to Pathao successfully!', 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Failed to dispatch to Pathao.', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = 'Send to Pathao';
            Swal.fire('Error', 'An error occurred during Pathao shipment.', 'error');
        });
    }
</script>
@endpush
@endsection
