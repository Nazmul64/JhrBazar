@extends('admin.master')

@section('content')
<div class="manager-dashboard-wrapper p-4">
    
    {{-- High-Level Overview Header --}}
    <div class="manager-header mb-5">
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <h2 class="fw-black mb-1">Manager Overview</h2>
                <p class="text-secondary m-0">Strategic insights and staff management for <strong>JHR Bazar</strong></p>
            </div>
            <div class="header-actions">
                <button class="btn btn-dark px-4 py-2 rounded-3"><i class="bi bi-file-earmark-pdf me-2"></i> Monthly Report</button>
            </div>
        </div>
    </div>

    {{-- Analysis Grid --}}
    <div class="row g-4 mb-4">
        {{-- Revenue Chart Simulated --}}
        <div class="col-lg-8">
            <div class="analysis-card">
                <div class="d-flex justify-content-between mb-4">
                    <h5 class="fw-bold">Monthly Revenue Analysis</h5>
                    <select class="form-select form-select-sm border-0 bg-light w-auto">
                        <option>Last 30 Days</option>
                        <option>Last 6 Months</option>
                    </select>
                </div>
                {{-- Simulated Bars --}}
                <div class="simulated-chart d-flex align-items-end justify-content-between">
                    <div class="bar-group"><div class="bar" style="height: 120px"></div><span>Jan</span></div>
                    <div class="bar-group"><div class="bar" style="height: 180px"></div><span>Feb</span></div>
                    <div class="bar-group"><div class="bar active" style="height: 250px"></div><span>Mar</span></div>
                    <div class="bar-group"><div class="bar" style="height: 160px"></div><span>Apr</span></div>
                    <div class="bar-group"><div class="bar" style="height: 210px"></div><span>May</span></div>
                    <div class="bar-group"><div class="bar" style="height: 190px"></div><span>Jun</span></div>
                </div>
            </div>
        </div>

        {{-- Staff Activity Summary --}}
        <div class="col-lg-4">
            <div class="analysis-card h-100">
                <h5 class="fw-bold mb-4">Staff Presence</h5>
                <div class="presence-stat mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">Attendance Rate</span>
                        <span class="small fw-bold">92%</span>
                    </div>
                    <div class="progress" style="height: 8px">
                        <div class="progress-bar bg-primary" style="width: 92%"></div>
                    </div>
                </div>
                <div class="presence-list">
                    <div class="p-item d-flex align-items-center mb-3">
                        <div class="p-dot active"></div>
                        <div class="ms-3">
                            <div class="fw-bold small">Nazmul Hossain</div>
                            <div class="text-muted smaller">Logged in 2h ago</div>
                        </div>
                    </div>
                    <div class="p-item d-flex align-items-center mb-3">
                        <div class="p-dot active"></div>
                        <div class="ms-3">
                            <div class="fw-bold small">Staff Member 01</div>
                            <div class="text-muted smaller">Logged in 5m ago</div>
                        </div>
                    </div>
                    <div class="p-item d-flex align-items-center mb-3">
                        <div class="p-dot inactive"></div>
                        <div class="ms-3">
                            <div class="fw-bold small">Staff Member 02</div>
                            <div class="text-muted smaller">Offline</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Management Metrics --}}
    <div class="row g-4">
        <div class="col-md-4">
            <div class="metric-block card-blue">
                <div class="m-icon"><i class="bi bi-people"></i></div>
                <div class="m-data">
                    <span class="m-label">Total Staff</span>
                    <h3 class="m-value">24</h3>
                    <div class="m-badge">+2 this month</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-block card-emerald">
                <div class="m-icon"><i class="bi bi-check-all"></i></div>
                <div class="m-data">
                    <span class="m-label">Resolved Issues</span>
                    <h3 class="m-value">1,042</h3>
                    <div class="m-badge">98.2% Success</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-block card-purple">
                <div class="m-icon"><i class="bi bi-star"></i></div>
                <div class="m-data">
                    <span class="m-label">Avg Rating</span>
                    <h3 class="m-value">4.92</h3>
                    <div class="m-badge">Excellent</div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.manager-dashboard-wrapper { background: #f0f2f5; min-height: 100vh; font-family: 'Poppins', sans-serif; }
.fw-black { font-weight: 900; color: #0f172a; font-size: 36px; }

/* Analysis Card */
.analysis-card { background: #fff; padding: 30px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
.simulated-chart { height: 300px; padding: 20px 0; }
.bar-group { text-align: center; width: 12%; }
.bar { 
    background: #e2e8f0; border-radius: 8px 8px 0 0; 
    transition: 0.5s; cursor: pointer; position: relative;
}
.bar:hover { background: #6366f1; }
.bar.active { background: #6366f1; box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3); }
.bar-group span { display: block; font-size: 11px; margin-top: 15px; color: #94a3b8; font-weight: 600; }

/* Presence List */
.p-dot { width: 10px; height: 10px; border-radius: 50%; }
.p-dot.active { background: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
.p-dot.inactive { background: #cbd5e1; }
.smaller { font-size: 11px; }

/* Metric Blocks */
.metric-block { 
    background: #fff; padding: 25px; border-radius: 24px; 
    display: flex; align-items: center; gap: 20px; transition: 0.3s;
}
.metric-block:hover { transform: translateY(-5px); }
.m-icon { 
    width: 60px; height: 60px; border-radius: 18px; 
    display: flex; align-items: center; justify-content: center; font-size: 24px;
}

.card-blue .m-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.card-emerald .m-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.card-purple .m-icon { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

.m-label { font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.m-value { font-size: 28px; font-weight: 800; color: #1e293b; margin: 2px 0; }
.m-badge { font-size: 11px; font-weight: 700; color: #10b981; }
</style>
@endsection
