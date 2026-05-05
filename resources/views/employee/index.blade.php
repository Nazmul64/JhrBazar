@extends('admin.master')

@section('content')
<div class="employee-dashboard-wrapper p-4">
    
    {{-- Header with Task Focus --}}
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md">
            <h4 class="fw-bold m-0 text-slate-800">Staff Portal <span class="badge bg-soft-indigo ms-2">Operational</span></h4>
            <p class="text-muted small m-0">Manage your daily tasks and track performance metrics.</p>
        </div>
        <div class="col-md-auto">
            <div class="d-flex gap-2">
                <div class="header-date-box">
                    <i class="bi bi-calendar3 me-2"></i> {{ date('D, M d Y') }}
                </div>
                <button class="btn btn-indigo shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> New Log
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Stats & Performance --}}
        <div class="col-xl-8">
            {{-- Professional Grid Stats --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="staff-metric-card">
                        <div class="metric-icon bg-soft-blue text-blue"><i class="bi bi-check2-circle"></i></div>
                        <div class="metric-info">
                            <span class="label">Tasks Finished</span>
                            <h4 class="value">12/15</h4>
                        </div>
                        <div class="metric-progress" style="width: 80%; background: #3b82f6;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="staff-metric-card">
                        <div class="metric-icon bg-soft-amber text-amber"><i class="bi bi-clock-history"></i></div>
                        <div class="metric-info">
                            <span class="label">Hours Worked</span>
                            <h4 class="value">38.5h</h4>
                        </div>
                        <div class="metric-progress" style="width: 70%; background: #f59e0b;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="staff-metric-card">
                        <div class="metric-icon bg-soft-emerald text-emerald"><i class="bi bi-graph-up-arrow"></i></div>
                        <div class="metric-info">
                            <span class="label">Efficiency</span>
                            <h4 class="value">94%</h4>
                        </div>
                        <div class="metric-progress" style="width: 94%; background: #10b981;"></div>
                    </div>
                </div>
            </div>

            {{-- Main Activity Feed or Task Table --}}
            <div class="white-card">
                <div class="card-header-staff mb-4">
                    <h5 class="m-0 fw-bold">Assigned Assignments</h5>
                </div>
                <div class="table-responsive">
                    <table class="table staff-table">
                        <thead>
                            <tr>
                                <th>Assignment Name</th>
                                <th>Priority</th>
                                <th>Deadline</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-bold">Update Inventory Logs</div>
                                    <span class="text-muted smaller">Section: Electronics</span>
                                </td>
                                <td><span class="prio-badge high">High</span></td>
                                <td>May 10</td>
                                <td><span class="status-pill in-progress">In Progress</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="fw-bold">Customer Support Tickets</div>
                                    <span class="text-muted smaller">Urgent cases</span>
                                </td>
                                <td><span class="prio-badge med">Medium</span></td>
                                <td>May 08</td>
                                <td><span class="status-pill completed">Completed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: Profile & Calendar --}}
        <div class="col-xl-4">
            {{-- Mini Profile Card --}}
            <div class="staff-profile-card mb-4">
                <div class="profile-header-bg"></div>
                <div class="profile-body text-center">
                    <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('admin/images/default-avatar.png') }}" class="staff-avatar-large">
                    <h5 class="mt-3 mb-1 fw-bold">{{ Auth::user()->name }}</h5>
                    <p class="text-muted small">{{ ucfirst(Auth::user()->role) }} • Employee ID: #7721</p>
                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <div class="p-stat"><span>Joining</span><strong>Jan 2024</strong></div>
                        <div class="p-stat"><span>Rating</span><strong>4.8/5</strong></div>
                    </div>
                </div>
            </div>

            {{-- Announcements --}}
            <div class="announcement-card">
                <h6 class="fw-bold mb-3"><i class="bi bi-megaphone me-2"></i> Announcements</h6>
                <div class="announce-item">
                    <div class="a-date">May 05</div>
                    <div class="a-content">Monthly staff meeting at 10 AM in Room 4B.</div>
                </div>
                <div class="announce-item">
                    <div class="a-date">May 03</div>
                    <div class="a-content">New software update deployed. Please check logs.</div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.employee-dashboard-wrapper { background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif; }
.text-slate-800 { color: #1e293b; }

/* Metrics */
.staff-metric-card {
    background: #fff; padding: 20px; border-radius: 16px; 
    border: 1px solid #e2e8f0; position: relative; overflow: hidden;
}
.metric-icon { 
    width: 40px; height: 40px; border-radius: 10px; 
    display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 12px;
}
.bg-soft-blue { background: #eff6ff; color: #3b82f6; }
.bg-soft-amber { background: #fffbeb; color: #f59e0b; }
.bg-soft-emerald { background: #ecfdf5; color: #10b981; }
.bg-soft-indigo { background: rgba(99, 102, 241, 0.1); color: #6366f1; }

.metric-info .label { font-size: 13px; color: #64748b; font-weight: 500; }
.metric-info .value { font-size: 22px; font-weight: 700; margin: 4px 0 0; color: #1e293b; }
.metric-progress { position: absolute; bottom: 0; left: 0; height: 3px; }

/* Cards & Tables */
.white-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 25px; }
.staff-table th { font-size: 12px; font-weight: 600; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border: none; padding: 12px 15px; }
.staff-table td { padding: 15px; border-color: #f1f5f9; vertical-align: middle; }
.smaller { font-size: 12px; }

.prio-badge { padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.prio-badge.high { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.prio-badge.med { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

.status-pill { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.status-pill.in-progress { background: #6366f1; color: #fff; }
.status-pill.completed { background: #10b981; color: #fff; }

/* Profile Card */
.staff-profile-card { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
.profile-header-bg { height: 80px; background: linear-gradient(to right, #6366f1, #8b5cf6); }
.staff-avatar-large { width: 90px; height: 90px; border-radius: 25px; border: 4px solid #fff; margin-top: -45px; object-fit: cover; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
.p-stat { padding: 0 15px; }
.p-stat span { display: block; font-size: 11px; color: #94a3b8; }
.p-stat strong { font-size: 14px; color: #1e293b; }

/* Announcements */
.announcement-card { background: #1e293b; color: #fff; padding: 25px; border-radius: 20px; }
.announce-item { margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.announce-item:last-child { border: none; margin: 0; padding: 0; }
.a-date { font-size: 10px; font-weight: 700; color: #6366f1; text-transform: uppercase; margin-bottom: 4px; }
.a-content { font-size: 13px; color: #cbd5e1; line-height: 1.5; }

.header-date-box { background: #fff; padding: 8px 15px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 13px; font-weight: 500; color: #64748b; }
.btn-indigo { background: #6366f1; color: #fff; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; }
.btn-indigo:hover { background: #4f46e5; color: #fff; }
</style>
@endsection
