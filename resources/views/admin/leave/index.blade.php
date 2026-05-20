@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e; font-size:22px;">Leave Logs</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Monitor employee leave requests and approvals.</p>
        </div>
        <button class="btn-hrm-add" data-bs-toggle="modal" data-bs-target="#logLeaveModal">
            <i class="fas fa-plus me-1"></i> Log Leave
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <span class="text-muted fw-bold" style="font-size:13px;"><i class="fas fa-filter me-1"></i> Filter:</span>
                <a href="{{ route('admin.leave.index') }}" class="btn btn-sm {{ !$status ? 'btn-hrm-active' : 'btn-hrm-outline' }}">All</a>
                <a href="{{ route('admin.leave.index', ['status' => 'Pending']) }}" class="btn btn-sm {{ $status == 'Pending' ? 'btn-hrm-active' : 'btn-hrm-outline' }}">Pending</a>
                <a href="{{ route('admin.leave.index', ['status' => 'Approved']) }}" class="btn btn-sm {{ $status == 'Approved' ? 'btn-hrm-active' : 'btn-hrm-outline' }}">Approved</a>
                <a href="{{ route('admin.leave.index', ['status' => 'Rejected']) }}" class="btn btn-sm {{ $status == 'Rejected' ? 'btn-hrm-active' : 'btn-hrm-outline' }}">Rejected</a>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th class="ps-4 py-3 hrm-th">SL</th>
                            <th class="py-3 hrm-th">Employee</th>
                            <th class="py-3 hrm-th">Leave Type</th>
                            <th class="py-3 hrm-th">Duration</th>
                            <th class="py-3 hrm-th">Days</th>
                            <th class="py-3 hrm-th">Reason</th>
                            <th class="py-3 hrm-th text-center">Status</th>
                            <th class="py-3 hrm-th text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $index => $leave)
                        <tr class="hrm-row">
                            <td class="ps-4 text-muted" style="font-size:13px;">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $leave->employee->profile_image ? asset($leave->employee->profile_image) : asset('admin/images/default-avatar.png') }}"
                                         class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold" style="font-size:13.5px; color:#222;">{{ $leave->employee->name }}</div>
                                        <span class="text-muted" style="font-size:11px;">{{ $leave->employee->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark" style="font-size:11px;">{{ $leave->leave_type }}</span>
                            </td>
                            <td style="font-size:13px; color:#555;">
                                {{ date('d M, Y', strtotime($leave->start_date)) }} - {{ date('d M, Y', strtotime($leave->end_date)) }}
                            </td>
                            <td style="font-size:13px; font-weight: 600; color:#444;">
                                @php
                                    $start = \Carbon\Carbon::parse($leave->start_date);
                                    $end = \Carbon\Carbon::parse($leave->end_date);
                                    $days = $start->diffInDays($end) + 1;
                                @endphp
                                {{ $days }} Days
                            </td>
                            <td style="font-size:13px; color:#666; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $leave->reason }}">
                                {{ $leave->reason ?? '—' }}
                            </td>
                            <td class="text-center">
                                @if($leave->status === 'Pending')
                                    <span class="badge bg-warning text-dark px-3 py-2" style="font-size:11px;">Pending</span>
                                @elseif($leave->status === 'Approved')
                                    <span class="badge bg-success px-3 py-2" style="font-size:11px;">Approved</span>
                                @else
                                    <span class="badge bg-danger px-3 py-2" style="font-size:11px;">Rejected</span>
                                @endif
                                @if($leave->approver)
                                    <div class="text-muted mt-1" style="font-size:9px;">By: {{ $leave->approver->name }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @if($leave->status === 'Pending')
                                        {{-- Approve --}}
                                        <form action="{{ route('admin.leave.status', $leave->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="Approved">
                                            <button type="submit" class="btn btn-sm btn-success-hrm" title="Approve Leave">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        {{-- Reject --}}
                                        <form action="{{ route('admin.leave.status', $leave->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="Rejected">
                                            <button type="submit" class="btn btn-sm btn-danger-hrm" title="Reject Leave">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    {{-- Delete --}}
                                    <form action="{{ route('admin.leave.destroy', $leave->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this leave record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Log">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-plane-departure fa-3x text-muted mb-3 d-block"></i>
                                <span class="text-muted" style="font-size:14px;">No leave logs recorded.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Log Leave Modal --}}
    <div class="modal fade" id="logLeaveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold" style="color:#1a1a2e; font-size:18px;">Log Leave Records</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.leave.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="hrm-lbl">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select hrm-in" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="hrm-lbl">Leave Type <span class="text-danger">*</span></label>
                                <select name="leave_type" class="form-select hrm-in" required>
                                    <option value="Casual Leave">Casual Leave</option>
                                    <option value="Sick Leave">Sick Leave</option>
                                    <option value="Annual Leave">Annual Leave</option>
                                    <option value="Maternity/Paternity Leave">Maternity/Paternity Leave</option>
                                    <option value="Unpaid Leave">Unpaid Leave</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="hrm-lbl">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control hrm-in" required>
                            </div>
                            <div class="col-md-6">
                                <label class="hrm-lbl">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control hrm-in" required>
                            </div>
                            <div class="col-12">
                                <label class="hrm-lbl">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select hrm-in" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="hrm-lbl">Reason</label>
                                <textarea name="reason" rows="3" class="form-control hrm-in" placeholder="Enter reason here..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="font-size:13.5px; border-radius:8px;">Close</button>
                        <button type="submit" class="btn-hrm-save">Save Log</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.btn-hrm-add {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff !important; border: none; border-radius: 8px;
    padding: 9px 20px; font-size: 14px; font-weight: 500;
    text-decoration: none; transition: all .2s ease;
    box-shadow: 0 3px 10px rgba(233,30,99,.35);
}
.btn-hrm-add:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(233,30,99,.4);
}
.btn-hrm-active {
    background: #e91e63; color: #fff !important; font-size: 12.5px; font-weight: 500; border-radius: 6px;
}
.btn-hrm-outline {
    background: #fff; color: #555; border: 1px solid #ddd; font-size: 12.5px; font-weight: 500; border-radius: 6px;
}
.btn-hrm-outline:hover { background: #fafafa; color: #222; }
.hrm-th {
    font-size: 13px; font-weight: 600; color: #888;
    border-bottom: 1px solid #f0f0f0 !important;
    text-transform: uppercase; letter-spacing: .4px;
}
.hrm-row { border-bottom: 1px solid #f7f7f7; transition: background .15s; }
.hrm-row:hover { background: #fafafa; }
.btn-success-hrm { background: rgba(46,125,50,0.1); color: #2e7d32; border: none; padding: 5px 10px; border-radius: 6px; }
.btn-success-hrm:hover { background: #2e7d32; color: #fff; }
.btn-danger-hrm { background: rgba(198,40,40,0.1); color: #c62828; border: none; padding: 5px 10px; border-radius: 6px; }
.btn-danger-hrm:hover { background: #c62828; color: #fff; }
.hrm-lbl { font-size: 12.5px; font-weight: 500; color: #333; margin-bottom: 4px; display: block; }
.hrm-in { font-size: 13.5px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 8px 12px; }
.btn-hrm-save {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff; border: none; border-radius: 8px;
    padding: 8px 24px; font-size: 13.5px; font-weight: 500;
}
.btn-hrm-save:hover { opacity: .95; }
</style>
@endsection
