@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e; font-size:22px;">Leave Management</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Review, approve and track all employee leave requests.</p>
        </div>
        <a href="{{ route('admin.hrm.leave.create') }}" class="btn-hrm-add px-3 py-2">
            <i class="fas fa-plus me-1"></i> Add Leave Request
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                <div class="card-body p-4 d-flex justify-content-between align-items-center text-white">
                    <div>
                        <span class="d-block opacity-75" style="font-size:13px;">Pending</span>
                        <h3 class="fw-bold mb-0">{{ $pendingCount }}</h3>
                    </div>
                    <i class="fas fa-hourglass-half fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="background:linear-gradient(135deg,#10b981,#059669);">
                <div class="card-body p-4 d-flex justify-content-between align-items-center text-white">
                    <div>
                        <span class="d-block opacity-75" style="font-size:13px;">Approved</span>
                        <h3 class="fw-bold mb-0">{{ $approvedCount }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                <div class="card-body p-4 d-flex justify-content-between align-items-center text-white">
                    <div>
                        <span class="d-block opacity-75" style="font-size:13px;">Rejected</span>
                        <h3 class="fw-bold mb-0">{{ $rejectedCount }}</h3>
                    </div>
                    <i class="fas fa-times-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.hrm.leave.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="hrm-lbl">Employee</label>
                    <select name="employee_id" class="form-select hrm-in">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="hrm-lbl">Leave Type</label>
                    <select name="leave_type" class="form-select hrm-in">
                        <option value="">All Types</option>
                        @foreach(['Sick','Casual','Annual','Maternity','Unpaid','Other'] as $t)
                            <option value="{{ $t }}" {{ request('leave_type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="hrm-lbl">Status</label>
                    <select name="status" class="form-select hrm-in">
                        <option value="">All</option>
                        @foreach(['Pending','Approved','Rejected'] as $s)
                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="hrm-lbl">Month</label>
                    <select name="month" class="form-select hrm-in">
                        <option value="">Any</option>
                        @for($m=1;$m<=12;$m++)
                            <option value="{{ sprintf('%02d',$m) }}" {{ request('month')==sprintf('%02d',$m)?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,10)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="hrm-lbl">Year</label>
                    <select name="year" class="form-select hrm-in">
                        @for($y=date('Y')-2;$y<=date('Y')+1;$y++)
                            <option value="{{ $y }}" {{ request('year')==$y?'selected':'' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn w-100" style="background:linear-gradient(135deg,#1a1a2e,#162447);color:#fff;border-radius:8px;">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" style="font-size:13px;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th class="ps-4 py-3">#</th>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                        <tr>
                            <td class="ps-4">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $leave->employee->name ?? '—' }}</div>
                                <small class="text-muted">{{ $leave->employee->department->name ?? '' }}</small>
                            </td>
                            <td><span class="badge bg-info text-dark">{{ $leave->leave_type }}</span></td>
                            <td>{{ $leave->from_date?->format('d M Y') }}</td>
                            <td>{{ $leave->to_date?->format('d M Y') }}</td>
                            <td class="fw-bold">{{ $leave->total_days }} day(s)</td>
                            <td style="max-width:150px;">
                                <span title="{{ $leave->reason }}">{{ Str::limit($leave->reason, 40) }}</span>
                            </td>
                            <td>
                                @php $sc = ['Pending'=>'warning','Approved'=>'success','Rejected'=>'danger']; @endphp
                                <span class="badge bg-{{ $sc[$leave->status] ?? 'secondary' }}">{{ $leave->status }}</span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    @if($leave->status === 'Pending')
                                    <form action="{{ route('admin.hrm.leave.approve', $leave->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" style="font-size:11px;border-radius:6px;"
                                            onclick="return confirm('Approve this leave?')">
                                            <i class="fas fa-check me-1"></i>Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.hrm.leave.reject', $leave->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" style="font-size:11px;border-radius:6px;"
                                            onclick="return confirm('Reject this leave?')">
                                            <i class="fas fa-times me-1"></i>Reject
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('admin.hrm.leave.destroy', $leave->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size:11px;border-radius:6px;"
                                            onclick="return confirm('Delete?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-3 d-block opacity-25"></i>
                                No leave records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $leaves->links() }}</div>
        </div>
    </div>

</div>
<style>
.hrm-lbl{font-size:12.5px;font-weight:500;color:#333;margin-bottom:4px;display:block;}
.hrm-in{font-size:13px;border-radius:8px;border:1px solid #e2e8f0;padding:8px 12px;}
.btn-hrm-add{background:linear-gradient(135deg,#e91e63,#c2185b);color:#fff!important;border:none;border-radius:8px;padding:9px 20px;font-size:13px;font-weight:500;text-decoration:none;box-shadow:0 3px 10px rgba(233,30,99,.35);}
</style>
@endsection
