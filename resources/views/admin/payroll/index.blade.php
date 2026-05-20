@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e; font-size:22px;">Salaries & Payroll</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Manage monthly employee payrolls and payslip generation.</p>
        </div>
        <a href="{{ route('admin.payroll.generate') }}" class="btn-hrm-add">
            <i class="fas fa-calculator me-1"></i> Generate Payroll
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 text-white bg-success-gradient">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 opacity-75" style="font-size:13px; font-weight:500;">Total Paid (Selected Month)</span>
                            <h3 class="fw-bold mb-0" style="font-size:26px;">৳{{ number_format($totalPaid, 2) }}</h3>
                        </div>
                        <i class="fas fa-check-double fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 text-white bg-warning-gradient">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 opacity-75" style="font-size:13px; font-weight:500;">Total Unpaid Outstanding</span>
                            <h3 class="fw-bold mb-0" style="font-size:26px;">৳{{ number_format($totalUnpaid, 2) }}</h3>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.payroll.index') }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                <span class="text-muted fw-bold me-2" style="font-size:13px;"><i class="fas fa-filter me-1"></i> Selection Range:</span>
                <select name="month" class="form-select select-hrm">
                    @for ($m=1; $m<=12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="form-select select-hrm">
                    @for ($y=date('Y')-2; $y<=date('Y')+2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-sm btn-hrm-active px-3" style="border-radius: 8px;">Load Sheet</button>
            </form>
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
                            <th class="py-3 hrm-th">Employee Name</th>
                            <th class="py-3 hrm-th">Base Salary</th>
                            <th class="py-3 hrm-th">Allowances</th>
                            <th class="py-3 hrm-th">Bonuses</th>
                            <th class="py-3 hrm-th">Loan Deductions</th>
                            <th class="py-3 hrm-th">Net Pay</th>
                            <th class="py-3 hrm-th text-center">Status</th>
                            <th class="py-3 hrm-th text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $index => $payroll)
                        <tr class="hrm-row">
                            <td class="ps-4 text-muted" style="font-size:13px;">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $payroll->employee->profile_image ? asset($payroll->employee->profile_image) : asset('admin/images/default-avatar.png') }}"
                                         class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold" style="font-size:13.5px; color:#222;">{{ $payroll->employee->name }}</div>
                                        <span class="text-muted" style="font-size:11px;">Join: {{ date('d M, Y', strtotime($payroll->employee->joining_date ?? now())) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:13.5px; color:#444;">৳{{ number_format($payroll->basic_salary, 2) }}</td>
                            <td style="font-size:13.5px; color:#2e7d32;">+৳{{ number_format($payroll->allowances, 2) }}</td>
                            <td style="font-size:13.5px; color:#2e7d32;">+৳{{ number_format($payroll->bonuses, 2) }}</td>
                            <td style="font-size:13.5px; color:#c62828;">-৳{{ number_format($payroll->advances_deduction, 2) }}</td>
                            <td style="font-size:14px; font-weight:700; color:#1a1a2e;">
                                ৳{{ number_format($payroll->net_salary, 2) }}
                            </td>
                            <td class="text-center">
                                @if($payroll->payment_status === 'Paid')
                                    <span class="badge bg-success px-3 py-2" style="font-size:11px;">Paid</span>
                                    <div class="text-muted mt-1" style="font-size:10px;">Via: {{ $payroll->payment_method }}</div>
                                @else
                                    <span class="badge bg-danger px-3 py-2" style="font-size:11px;">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @if($payroll->payment_status === 'Unpaid')
                                        {{-- Pay Button --}}
                                        <button class="btn btn-sm btn-success-hrm py-1 px-3" data-bs-toggle="modal" data-bs-target="#payModal_{{ $payroll->id }}">
                                            <i class="fas fa-money-bill-wave me-1"></i> Disburse Pay
                                        </button>
                                    @else
                                        {{-- Print Payslip --}}
                                        <a href="{{ route('admin.payroll.slip', $payroll->id) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-3">
                                            <i class="fas fa-print me-1"></i> Pay Slip
                                        </a>
                                    @endif
                                    
                                    {{-- Delete Log --}}
                                    <form action="{{ route('admin.payroll.destroy', $payroll->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payroll sheet?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Record">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Disburse Payment Modal --}}
                        <div class="modal fade" id="payModal_{{ $payroll->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-3">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="fw-bold" style="color:#1a1a2e; font-size:18px;">Disburse Employee Salary</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.payroll.pay', $payroll->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <div class="alert alert-info rounded-3 border-0 py-3 mb-3">
                                                <div style="font-size:13px;">Disbursing net salary of <strong>৳{{ number_format($payroll->net_salary, 2) }}</strong> to <strong>{{ $payroll->employee->name }}</strong>.</div>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="hrm-lbl">Payment Method <span class="text-danger">*</span></label>
                                                    <select name="payment_method" class="form-select hrm-in" required>
                                                        <option value="Cash">Cash</option>
                                                        <option value="Bank Transfer">Bank Transfer</option>
                                                        <option value="bKash">bKash</option>
                                                        <option value="Nagad">Nagad</option>
                                                        <option value="Rocket">Rocket</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="hrm-lbl">Notes / Payment Reference</label>
                                                    <textarea name="note" rows="2" class="form-control hrm-in" placeholder="Enter transaction reference details..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="font-size:13.5px; border-radius:8px;">Close</button>
                                            <button type="submit" class="btn-hrm-save">Disburse Salary</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-wallet fa-3x text-muted mb-3 d-block"></i>
                                <span class="text-muted" style="font-size:14px;">No payrolls generated for the selected month and year.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.bg-success-gradient {
    background: linear-gradient(135deg, #2e7d32, #1b5e20);
}
.bg-warning-gradient {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}
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
.select-hrm {
    width: 140px;
    font-size: 13.5px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}
.btn-hrm-active {
    background: #e91e63; color: #fff !important; font-size: 12.5px; font-weight: 500; border-radius: 6px;
}
.hrm-th {
    font-size: 13px; font-weight: 600; color: #888;
    border-bottom: 1px solid #f0f0f0 !important;
    text-transform: uppercase; letter-spacing: .4px;
}
.hrm-row { border-bottom: 1px solid #f7f7f7; transition: background .15s; }
.hrm-row:hover { background: #fafafa; }
.btn-success-hrm { background: rgba(46,125,50,0.1); color: #2e7d32; border: none; padding: 5px 10px; border-radius: 6px; }
.btn-success-hrm:hover { background: #2e7d32; color: #fff; }
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
