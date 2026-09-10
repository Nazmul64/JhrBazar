@extends('admin.master')

@section('title', 'Attribution & Channel Reports')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    .report-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .kpi-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .platform-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .platform-facebook { background: #e0f2fe; color: #0284c7; }
    .platform-google { background: #fee2e2; color: #dc2626; }
    .platform-tiktok { background: #f1f5f9; color: #0f172a; }
    .platform-instagram { background: #fdf2f8; color: #db2777; }
    .platform-youtube { background: #ffe4e6; color: #e11d48; }
    .platform-organic { background: #ecfdf5; color: #059669; }
    .platform-direct { background: #f3f4f6; color: #4b5563; }
    .platform-referral { background: #fef3c7; color: #d97706; }

    .table th {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 16px;
    }
    .table td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 13.5px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }
    .date-filter-btn {
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 20px;
        color: #64748b;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        text-decoration: none;
        transition: all 0.2s;
    }
    .date-filter-btn.active, .date-filter-btn:hover {
        background: #4f46e5;
        color: #ffffff;
        border-color: #4f46e5;
    }
</style>

<div class="container-fluid py-3">

    {{-- Header & Range Filters --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1e293b;">
                <i class="bi bi-pie-chart-fill text-primary me-2"></i>Attribution & Channel Reports
            </h4>
            <p class="text-muted mb-0 small">
                Live performance tracking from {{ $start->format('M d, Y') }} to {{ $end->format('M d, Y') }}
            </p>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-1 bg-white p-1 rounded-pill border shadow-sm">
                <a href="{{ route('admin.tracking.attribution', ['range' => 'today']) }}" class="date-filter-btn {{ $range === 'today' ? 'active' : '' }}">Today</a>
                <a href="{{ route('admin.tracking.attribution', ['range' => '7_days']) }}" class="date-filter-btn {{ $range === '7_days' ? 'active' : '' }}">7 Days</a>
                <a href="{{ route('admin.tracking.attribution', ['range' => '30_days']) }}" class="date-filter-btn {{ $range === '30_days' ? 'active' : '' }}">30 Days</a>
                <a href="{{ route('admin.tracking.attribution', ['range' => 'this_month']) }}" class="date-filter-btn {{ $range === 'this_month' ? 'active' : '' }}">This Month</a>
                <a href="{{ route('admin.tracking.attribution', ['range' => 'last_month']) }}" class="date-filter-btn {{ $range === 'last_month' ? 'active' : '' }}">Last Month</a>
            </div>

            <a href="{{ route('admin.tracking.attribution.export-csv', ['range' => $range]) }}" class="btn btn-outline-secondary px-3 py-2 rounded-pill shadow-sm fw-semibold">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
            <a href="{{ route('admin.tracking.settings') }}" class="btn btn-primary px-3 py-2 rounded-pill shadow-sm fw-semibold" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
                <i class="bi bi-gear-fill me-1"></i> Tracking Settings
            </a>
        </div>
    </div>

    {{-- KPI Cards Row --}}
    <div class="row g-3 mb-4">
        {{-- Total Visitors --}}
        <div class="col-sm-6 col-lg-2">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Visitors</span>
                    <div class="kpi-icon" style="background: #e0e7ff; color: #4338ca;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalVisitors) }}</h3>
                <small class="text-muted">Unique sessions</small>
            </div>
        </div>

        {{-- Attributed Orders --}}
        <div class="col-sm-6 col-lg-2">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Orders</span>
                    <div class="kpi-icon" style="background: #dcfce7; color: #15803d;">
                        <i class="bi bi-bag-check-fill"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalOrders) }}</h3>
                <small class="text-success fw-semibold"><i class="bi bi-check2"></i> Attributed</small>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Total Revenue</span>
                    <div class="kpi-icon" style="background: #fef3c7; color: #b45309;">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark">৳ {{ number_format($totalRevenue, 2) }}</h3>
                <small class="text-muted">From attributed sales</small>
            </div>
        </div>

        {{-- Conversion Rate --}}
        <div class="col-sm-6 col-lg-2">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Conv. Rate</span>
                    <div class="kpi-icon" style="background: #f3e8ff; color: #7e22ce;">
                        <i class="bi bi-percent"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($conversionRate, 2) }}%</h3>
                <small class="text-muted">Orders / Visitors</small>
            </div>
        </div>

        {{-- Average Order Value --}}
        <div class="col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Avg Order Value (AOV)</span>
                    <div class="kpi-icon" style="background: #e0f2fe; color: #0369a1;">
                        <i class="bi bi-cart4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark">৳ {{ number_format($aov, 2) }}</h3>
                <small class="text-primary fw-semibold"><i class="bi bi-award-fill"></i> Top: {{ $topChannel }}</small>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="row mb-4">
        {{-- Daily Visitors & Revenue Spline Chart --}}
        <div class="col-lg-8">
            <div class="report-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Visitors vs. Attributed Revenue Timeline</h6>
                        <small class="text-muted">Daily session trend and sales performance</small>
                    </div>
                    <div>
                        <span class="badge bg-light text-secondary border">Realtime</span>
                    </div>
                </div>
                <div id="timelineChart" style="min-height: 320px;"></div>
            </div>
        </div>

        {{-- Channel Distribution Donut Chart --}}
        <div class="col-lg-4">
            <div class="report-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Traffic Channel Share</h6>
                        <small class="text-muted">Session breakdown by channel</small>
                    </div>
                </div>
                @if(count($donutSeries) > 0 && array_sum($donutSeries) > 0)
                    <div id="donutChart" style="min-height: 320px;"></div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-pie-chart fs-1 opacity-50 mb-2"></i>
                        <p class="mb-0 small">No visitor distribution data for this period yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 1. Top Traffic Sources Table --}}
    <div class="report-card mb-4">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold text-dark mb-1"><i class="bi bi-funnel-fill text-primary me-2"></i>Top Traffic Sources & Channels</h5>
                <p class="text-muted small mb-0">Attribution breakdown grouped by platform and source/medium</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Platform / Channel</th>
                        <th>Source / Medium</th>
                        <th class="text-center">Visitors (Sessions)</th>
                        <th class="text-center">Attributed Orders</th>
                        <th class="text-center">Conversion Rate</th>
                        <th class="text-end">Total Revenue (BDT)</th>
                        <th class="text-end">AOV (BDT)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trafficSources as $source)
                        @php
                            $platName = $source->detected_platform;
                            $srcMedLower = strtolower($source->source_medium ?? '');
                            if (!$platName || $platName === 'Direct Visit' || $platName === 'direct / (none)') {
                                if (str_contains($srcMedLower, 'google')) {
                                    $platName = str_contains($srcMedLower, 'cpc') || str_contains($srcMedLower, 'paid') ? 'Google Ads' : 'Google Search';
                                } elseif (str_contains($srcMedLower, 'facebook') || str_contains($srcMedLower, 'fb')) {
                                    $platName = str_contains($srcMedLower, 'cpc') || str_contains($srcMedLower, 'paid') ? 'Facebook Ads' : 'Facebook Organic';
                                } elseif (str_contains($srcMedLower, 'tiktok')) {
                                    $platName = 'TikTok Ads';
                                } elseif (str_contains($srcMedLower, 'instagram')) {
                                    $platName = 'Instagram Ads';
                                } elseif (str_contains($srcMedLower, 'youtube')) {
                                    $platName = 'YouTube';
                                } else {
                                    $platName = 'Direct Visit';
                                }
                            }
                            $platClass = 'platform-direct';
                            $platLower = strtolower($platName);
                            if (str_contains($platLower, 'facebook ads')) $platClass = 'platform-facebook';
                            elseif (str_contains($platLower, 'google ads') || str_contains($platLower, 'google search')) $platClass = 'platform-google';
                            elseif (str_contains($platLower, 'tiktok')) $platClass = 'platform-tiktok';
                            elseif (str_contains($platLower, 'instagram')) $platClass = 'platform-instagram';
                            elseif (str_contains($platLower, 'youtube')) $platClass = 'platform-youtube';
                            elseif (str_contains($platLower, 'organic')) $platClass = 'platform-organic';
                            elseif (str_contains($platLower, 'referral')) $platClass = 'platform-referral';
                        @endphp
                        <tr>
                            <td>
                                <span class="platform-badge {{ $platClass }}">
                                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i>
                                    {{ $platName }}
                                </span>
                            </td>
                            <td><span class="font-monospace fw-semibold text-secondary small">{{ $source->source_medium }}</span></td>
                            <td class="text-center fw-bold">{{ number_format($source->visitors) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $source->orders > 0 ? 'bg-success' : 'bg-light text-muted border' }} px-2 py-1">
                                    {{ number_format($source->orders) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold {{ $source->conversion_rate > 5 ? 'text-success' : 'text-dark' }}">
                                    {{ number_format($source->conversion_rate, 2) }}%
                                </span>
                            </td>
                            <td class="text-end fw-bold text-dark">৳ {{ number_format($source->revenue, 2) }}</td>
                            <td class="text-end text-secondary">৳ {{ number_format($source->aov, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-1 opacity-50"></i>
                                No traffic source data found for selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 2. Top Marketing Campaigns Table --}}
    @if(count($campaigns) > 0)
    <div class="report-card mb-4">
        <div class="p-4 border-bottom">
            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-megaphone-fill text-warning me-2"></i>Top Marketing Campaigns (UTM)</h5>
            <p class="text-muted small mb-0">Specific ad campaigns identified from <code>utm_campaign</code> query tags</p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Campaign Name</th>
                        <th>Source</th>
                        <th>Medium</th>
                        <th class="text-center">Visitors</th>
                        <th class="text-center">Orders</th>
                        <th class="text-center">Conv. Rate</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-end">AOV</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaigns as $camp)
                        <tr>
                            <td><strong class="text-primary">{{ $camp->utm_campaign }}</strong></td>
                            <td><span class="badge bg-light text-dark border">{{ $camp->utm_source }}</span></td>
                            <td><span class="badge bg-light text-secondary border">{{ $camp->utm_medium }}</span></td>
                            <td class="text-center fw-semibold">{{ number_format($camp->visitors) }}</td>
                            <td class="text-center fw-bold text-success">{{ number_format($camp->orders) }}</td>
                            <td class="text-center fw-bold">{{ number_format($camp->conversion_rate, 2) }}%</td>
                            <td class="text-end fw-bold">৳ {{ number_format($camp->revenue, 2) }}</td>
                            <td class="text-end text-muted">৳ {{ number_format($camp->aov, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- 3. Platform Breakdown Report Table with Real Order Statuses --}}
    <div class="report-card mb-4">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold text-dark mb-1"><i class="bi bi-grid-3x3-gap-fill text-info me-2"></i>Platform Breakdown & Order Delivery Status</h5>
                <p class="text-muted small mb-0">Full performance and order fulfillment status by platform</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Platform</th>
                        <th class="text-center">Visitors</th>
                        <th class="text-center">Orders</th>
                        <th class="text-center">Conv. Rate</th>
                        <th class="text-end">Total Revenue (BDT)</th>
                        <th class="text-end">AOV (BDT)</th>
                        <th class="text-center">Delivered Orders</th>
                        <th class="text-center">Pending Orders</th>
                        <th class="text-center">Cancelled Orders</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($platformBreakdown as $pb)
                        @php
                            $platClass = 'platform-direct';
                            $platLower = strtolower($pb->platform ?? '');
                            if (str_contains($platLower, 'facebook ads')) $platClass = 'platform-facebook';
                            elseif (str_contains($platLower, 'google ads') || str_contains($platLower, 'google search')) $platClass = 'platform-google';
                            elseif (str_contains($platLower, 'tiktok')) $platClass = 'platform-tiktok';
                            elseif (str_contains($platLower, 'instagram')) $platClass = 'platform-instagram';
                            elseif (str_contains($platLower, 'youtube')) $platClass = 'platform-youtube';
                            elseif (str_contains($platLower, 'organic')) $platClass = 'platform-organic';
                            elseif (str_contains($platLower, 'referral')) $platClass = 'platform-referral';
                        @endphp
                        <tr>
                            <td>
                                <span class="platform-badge {{ $platClass }}">
                                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i>
                                    {{ $pb->platform }}
                                </span>
                            </td>
                            <td class="text-center fw-semibold">{{ number_format($pb->visitors) }}</td>
                            <td class="text-center fw-bold">
                                <span class="badge {{ $pb->orders > 0 ? 'bg-primary' : 'bg-light text-muted border' }} px-2 py-1">
                                    {{ number_format($pb->orders) }}
                                </span>
                            </td>
                            <td class="text-center fw-bold">{{ number_format($pb->conversion_rate, 2) }}%</td>
                            <td class="text-end fw-bold text-dark">৳ {{ number_format($pb->revenue, 2) }}</td>
                            <td class="text-end text-secondary">৳ {{ number_format($pb->aov, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 fw-bold">
                                    <i class="bi bi-check-circle-fill me-1"></i>{{ $pb->delivered }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 fw-bold text-dark">
                                    <i class="bi bi-hourglass-split me-1"></i>{{ $pb->pending }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 fw-bold">
                                    <i class="bi bi-x-circle-fill me-1"></i>{{ $pb->cancelled }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No platform breakdown records available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Timeline Chart (Spline Area)
        var timelineCategories = @json($chartCategories);
        var timelineVisitors = @json($chartVisitors);
        var timelineRevenue = @json($chartRevenue);

        var timelineOptions = {
            series: [
                {
                    name: 'Visitors',
                    type: 'area',
                    data: timelineVisitors
                },
                {
                    name: 'Revenue (BDT)',
                    type: 'line',
                    data: timelineRevenue
                }
            ],
            chart: {
                height: 330,
                type: 'line',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            stroke: {
                curve: 'smooth',
                width: [2, 3]
            },
            colors: ['#6366f1', '#10b981'],
            fill: {
                type: ['gradient', 'solid'],
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            labels: timelineCategories,
            xaxis: {
                type: 'category',
                labels: {
                    rotate: -45,
                    style: { fontSize: '11px', colors: '#64748b' }
                }
            },
            yaxis: [
                {
                    title: { text: 'Visitors', style: { color: '#6366f1', fontWeight: 600 } },
                    labels: { style: { colors: '#6366f1' } }
                },
                {
                    opposite: true,
                    title: { text: 'Revenue (BDT)', style: { color: '#10b981', fontWeight: 600 } },
                    labels: {
                        formatter: function (val) {
                            return '৳' + Number(val).toLocaleString();
                        },
                        style: { colors: '#10b981' }
                    }
                }
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (y, { seriesIndex }) {
                        if (seriesIndex === 1) return '৳' + Number(y).toLocaleString();
                        return Number(y).toLocaleString() + ' sessions';
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            },
            grid: {
                borderColor: '#f1f5f9'
            }
        };

        var chart = new ApexCharts(document.querySelector("#timelineChart"), timelineOptions);
        chart.render();

        // 2. Channel Donut Chart
        var donutLabels = @json($donutLabels);
        var donutSeries = @json($donutSeries);

        if (donutSeries.length > 0 && donutSeries.reduce((a, b) => a + b, 0) > 0) {
            var donutOptions = {
                series: donutSeries,
                labels: donutLabels,
                chart: {
                    type: 'donut',
                    height: 330
                },
                colors: ['#3b82f6', '#ef4444', '#0f172a', '#ec4899', '#8b5cf6', '#10b981', '#f59e0b', '#64748b'],
                legend: {
                    position: 'bottom',
                    fontSize: '12px'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: { width: 280 },
                        legend: { position: 'bottom' }
                    }
                }],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Sessions',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                }
            };

            var donutChart = new ApexCharts(document.querySelector("#donutChart"), donutOptions);
            donutChart.render();
        }
    });
</script>
@endsection
