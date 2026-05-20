@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e; font-size:22px;">HRM Analytics Dashboard</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Executive overview of team capacity, attendance metrics, leaves, and payroll costs.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline-secondary px-3 py-2" style="font-size: 13px; border-radius: 8px;">
                <i class="fas fa-calendar-check me-1"></i> Shift Register
            </a>
            <a href="{{ route('admin.employees.create') }}" class="btn-hrm-add px-3 py-2">
                <i class="fas fa-user-plus me-1"></i> Recruit Staff
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Headcount --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 bg-gradient-deepblue text-white h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="d-block mb-1 opacity-75" style="font-size:13px; font-weight:500;">Active Headcount</span>
                        <h3 class="fw-bold mb-0" style="font-size:28px;">{{ $totalEmployees }}</h3>
                    </div>
                    <i class="fas fa-users-cog fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- Pending Leaves --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 bg-gradient-magenta text-white h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="d-block mb-1 opacity-75" style="font-size:13px; font-weight:500;">Pending Leaves</span>
                        <h3 class="fw-bold mb-0" style="font-size:28px;">{{ $pendingLeaves }}</h3>
                    </div>
                    <i class="fas fa-plane-departure fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- Outstanding Advances --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 bg-gradient-amber text-white h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="d-block mb-1 opacity-75" style="font-size:13px; font-weight:500;">Loans Outstanding</span>
                        <h3 class="fw-bold mb-0" style="font-size:28px;">৳{{ number_format($netOutstanding, 2) }}</h3>
                    </div>
                    <i class="fas fa-hand-holding-usd fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- Month Payroll Disbursed --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 bg-gradient-emerald text-white h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="d-block mb-1 opacity-75" style="font-size:13px; font-weight:500;">Payroll Paid (Month)</span>
                        <h3 class="fw-bold mb-0" style="font-size:28px;">৳{{ number_format($totalSalaryPaidThisMonth, 2) }}</h3>
                    </div>
                    <i class="fas fa-receipt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Middle Analytical Charts Grid --}}
    <div class="row g-4 mb-4">
        {{-- Department Staffing Ratio (Horizontal Bar Indicators) --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color:#1a1a2e; font-size:16px;">Department Distribution</h5>
                    
                    <div class="department-stats-list">
                        @forelse($departments as $dept)
                            @php
                                $percent = $totalEmployees > 0 ? round(($dept->employees_count / $totalEmployees) * 100, 1) : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1" style="font-size:12.5px;">
                                    <span class="fw-bold text-dark">{{ $dept->name }}</span>
                                    <span class="text-muted">{{ $dept->employees_count }} Staff ({{ $percent }}%)</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar progress-bar-premium" role="progressbar" style="width: {{ $percent }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-sitemap fa-2x mb-2 opacity-50"></i>
                                <p style="font-size:13px;">No departments configured yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Today's Attendance Overview --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color:#1a1a2e; font-size:16px;">Today's Attendance Status</h5>
                    
                    <div class="d-flex justify-content-around align-items-center py-4">
                        <div class="text-center">
                            <span class="badge bg-success rounded-circle p-3 mb-2" style="font-size:16px;"><i class="fas fa-check-circle text-white"></i></span>
                            <h4 class="fw-bold mb-0" style="color:#222;">{{ $todayPresent }}</h4>
                            <span class="text-muted" style="font-size:12px;">Present</span>
                        </div>
                        <div class="text-center">
                            <span class="badge bg-warning rounded-circle p-3 mb-2" style="font-size:16px;"><i class="fas fa-user-clock text-white"></i></span>
                            <h4 class="fw-bold mb-0" style="color:#222;">{{ $todayLate }}</h4>
                            <span class="text-muted" style="font-size:12px;">Late Punch</span>
                        </div>
                        <div class="text-center">
                            <span class="badge bg-danger rounded-circle p-3 mb-2" style="font-size:16px;"><i class="fas fa-times-circle text-white"></i></span>
                            <h4 class="fw-bold mb-0" style="color:#222;">{{ $todayAbsent }}</h4>
                            <span class="text-muted" style="font-size:12px;">Absent</span>
                        </div>
                    </div>

                    <div class="alert alert-light border rounded-3 py-2 px-3 mt-2 text-center" style="font-size:12px; color:#555;">
                        <i class="fas fa-clock me-1"></i> Shift Hours: <strong>09:00 AM - 05:00 PM</strong> (Lateness calculated past 09:00 AM)
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Grid: Recent clockpunches & Payroll timeline --}}
    <div class="row g-4">
        {{-- Today's Clock Punch Logs --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color:#1a1a2e; font-size:16px;">Live Punch Feed (Today)</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                            <thead>
                                <tr style="background:#f8f9fa;">
                                    <th>Employee</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Late Minutes</th>
                                    <th>Device IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPunches as $punch)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $punch->employee->name }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $punch->employee->designation->name ?? 'Staff' }}</div>
                                        </td>
                                        <td class="text-success fw-bold">{{ $punch->clock_in ? date('h:i A', strtotime($punch->clock_in)) : '—' }}</td>
                                        <td class="text-primary fw-bold">{{ $punch->clock_out ? date('h:i A', strtotime($punch->clock_out)) : '—' }}</td>
                                        <td>
                                            @if($punch->late_minutes > 0)
                                                <span class="badge bg-soft-danger">{{ $punch->late_minutes }}m Late</span>
                                            @else
                                                <span class="badge bg-soft-success">On Time</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $punch->device_ip }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            No clock punch activities logged today.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Salary Cost trends --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color:#1a1a2e; font-size:16px;">Salary Outflow Timeline (Last 6 Cycles)</h5>
                    
                    <div class="d-flex align-items-end justify-content-between pt-5 pb-3 px-2 h-75" style="border-bottom:1px solid #eee;">
                        @forelse($payrollStats as $stat)
                            @php
                                $monthName = date('M', mktime(0, 0, 0, $stat->month, 10));
                                // Let's scale visual height of chart bar based on salary amount (max out at 150px)
                                $maxCost = max($payrollStats->pluck('total_net')->toArray()) ?: 1;
                                $barHeight = round(($stat->total_net / $maxCost) * 120);
                            @endphp
                            <div class="text-center d-flex flex-column align-items-center" style="flex:1;">
                                <span class="d-block fw-bold text-premium" style="font-size:10.5px;">৳{{ number_format($stat->total_net / 1000, 1) }}k</span>
                                <div class="bg-gradient-premium rounded-top mt-2" style="width:24px; height:{{ $barHeight }}px; transition: height .3s ease;"></div>
                                <span class="text-muted mt-2" style="font-size:11.5px;">{{ $monthName }} '{{ substr($stat->year, -2) }}</span>
                            </div>
                        @empty
                            <div class="text-center w-100 py-4 text-muted">
                                <i class="fas fa-wallet fa-2x mb-2 opacity-50"></i>
                                <p style="font-size:13px;">No payroll disbursements logged yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.bg-gradient-deepblue {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
}
.bg-gradient-magenta {
    background: linear-gradient(135deg, #e91e63, #c2185b);
}
.bg-gradient-amber {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}
.bg-gradient-emerald {
    background: linear-gradient(135deg, #10b981, #059669);
}
.btn-hrm-add {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff !important; border: none; border-radius: 8px;
    padding: 9px 20px; font-size: 13px; font-weight: 500;
    text-decoration: none; transition: all .2s ease;
    box-shadow: 0 3px 10px rgba(233,30,99,.35);
}
.btn-hrm-add:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(233,30,99,.4);
}
.progress-bar-premium {
    background: linear-gradient(135deg, #e91e63, #c2185b);
}
.bg-gradient-premium {
    background: linear-gradient(135deg, #e91e63, #c2185b);
}
.text-premium {
    color: #c2185b;
}
.bg-soft-danger {
    background: rgba(220,53,69,0.1);
    color: #dc3545;
}
.bg-soft-success {
    background: rgba(40,167,69,0.1);
    color: #28a745;
}
</style>
@endsection
