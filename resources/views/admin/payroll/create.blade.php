@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1" style="color:#1a1a2e; font-size:22px;">Generate Payroll</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Calculate structural salaries, allowances, provident funds, TDS and loan repayments.</p>
    </div>

    {{-- Alert --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Step 1: Select Employee --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color:#1a1a2e; font-size:16px;">1. Select Employee target</h5>
                    
                    <form action="{{ route('admin.hrm.payroll.generate') }}" method="GET">
                        <div class="mb-3">
                            <label class="hrm-lbl">Employee</label>
                            <select name="employee_id" class="form-select hrm-in" required onchange="this.form.submit()">
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ $employee && $employee->id == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="hrm-lbl">Month</label>
                                <select name="month" class="form-select hrm-in" required onchange="this.form.submit()">
                                    @for ($m=1; $m<=12; $m++)
                                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="hrm-lbl">Year</label>
                                <select name="year" class="form-select hrm-in" required onchange="this.form.submit()">
                                    @for ($y=date('Y')-2; $y<=date('Y')+2; $y++)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-sm w-100 btn-outline-hrm py-2">
                            <i class="fas fa-sync-alt me-1"></i> Refresh details
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Step 2: Payroll Form --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4" style="color:#1a1a2e; font-size:16px;">2. Structural Payroll Computations</h5>

                    @if($employee)
                        <form action="{{ route('admin.hrm.payroll.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">

                            {{-- Selected Target Info --}}
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light border mb-4">
                                <img src="{{ $employee->profile_image ? asset($employee->profile_image) : asset('admin/images/default-avatar.png') }}"
                                     class="rounded-circle" width="45" height="45" style="object-fit: cover;">
                                <div>
                                    <h6 class="fw-bold mb-0" style="color:#222;">{{ $employee->name }}</h6>
                                    <span class="text-muted" style="font-size:12px;">
                                        {{ $employee->department->name ?? 'No Dept' }} • {{ $employee->designation->name ?? 'No Title' }}
                                    </span>
                                </div>
                            </div>

                            @if($activeAdvance > 0)
                                <div class="alert alert-warning border-0 rounded-3 py-3 mb-4 shadow-sm">
                                    <div class="d-flex gap-2">
                                        <i class="fas fa-exclamation-triangle mt-1"></i>
                                        <div>
                                            <strong class="d-block" style="font-size:13.5px;">Outstanding Advance Alert!</strong>
                                            <span style="font-size:12.5px;">This employee has <strong>৳{{ number_format($activeAdvance, 2) }}</strong> in active unpaid advances. Suggested auto-deduction is <strong>৳{{ number_format($suggestedDeduction, 2) }}</strong>.</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Form Tabs --}}
                            <ul class="nav nav-pills mb-4" id="payrollTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="earnings-tab" data-bs-toggle="pill" data-bs-target="#earnings" type="button" role="tab">
                                        <i class="fas fa-plus-circle me-1"></i> 1. Salary & Allowances
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="deductions-tab" data-bs-toggle="pill" data-bs-target="#deductions" type="button" role="tab">
                                        <i class="fas fa-minus-circle me-1"></i> 2. Statutory Deductions
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="payrollTabsContent">
                                {{-- 1. Earnings Tab --}}
                                <div class="tab-pane fade show active" id="earnings" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="hrm-lbl">Base Basic Salary (BDT) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control hrm-in" required value="{{ old('basic_salary', $employee->salary ?? 0) }}" oninput="calculateTotal()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="hrm-lbl">House Rent Allowance (HRA) (BDT)</label>
                                            <input type="number" step="0.01" name="house_rent_allowance" id="house_rent_allowance" class="form-control hrm-in" required value="{{ old('house_rent_allowance', $suggestedHRA) }}" oninput="calculateTotal()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="hrm-lbl">Medical Allowance (BDT)</label>
                                            <input type="number" step="0.01" name="medical_allowance" id="medical_allowance" class="form-control hrm-in" required value="{{ old('medical_allowance', $suggestedMedical) }}" oninput="calculateTotal()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="hrm-lbl">Conveyance / Transport (BDT)</label>
                                            <input type="number" step="0.01" name="conveyance_allowance" id="conveyance_allowance" class="form-control hrm-in" required value="{{ old('conveyance_allowance', $suggestedConveyance) }}" oninput="calculateTotal()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="hrm-lbl">Bonuses (BDT)</label>
                                            <input type="number" step="0.01" name="bonuses" id="bonuses" class="form-control hrm-in" required value="0.00" oninput="calculateTotal()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="hrm-lbl">Performance / Extra Incentives (BDT)</label>
                                            <input type="number" step="0.01" name="extra_incentives" id="extra_incentives" class="form-control hrm-in" required value="0.00" oninput="calculateTotal()">
                                        </div>
                                        <div class="col-12">
                                            <label class="hrm-lbl">Other Perks / Miscellaneous (BDT)</label>
                                            <input type="number" step="0.01" name="allowances" id="allowances" class="form-control hrm-in" required value="0.00" oninput="calculateTotal()">
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. Deductions Tab --}}
                                <div class="tab-pane fade" id="deductions" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="hrm-lbl">Provident Fund (PF) (BDT)</label>
                                            <input type="number" step="0.01" name="provident_fund" id="provident_fund" class="form-control hrm-in" required value="{{ old('provident_fund', $suggestedPF) }}" oninput="calculateTotal()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="hrm-lbl">Professional Tax / TDS (BDT)</label>
                                            <input type="number" step="0.01" name="professional_tax" id="professional_tax" class="form-control hrm-in" required value="{{ old('professional_tax', $suggestedTax) }}" oninput="calculateTotal()">
                                        </div>
                                        <div class="col-12">
                                            <label class="hrm-lbl">Salary Advance Deduction (BDT)</label>
                                            <input type="number" step="0.01" name="advances_deduction" id="advances_deduction" class="form-control hrm-in" required value="{{ old('advances_deduction', $suggestedDeduction) }}" oninput="calculateTotal()">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Calculations Summary Box --}}
                            <div class="col-12 mt-4">
                                <div class="p-4 rounded-3 border-0 text-white shadow-sm bg-net-salary-preview">
                                    <div class="row text-center text-md-start">
                                        <div class="col-md-4 border-end-dashed mb-3 mb-md-0">
                                            <span class="d-block mb-1 opacity-75" style="font-size:12px; font-weight:500;">Gross Earnings</span>
                                            <h3 class="fw-bold mb-0" style="font-size:24px;">৳<span id="gross_salary_val">0.00</span></h3>
                                        </div>
                                        <div class="col-md-4 border-end-dashed mb-3 mb-md-0">
                                            <span class="d-block mb-1 opacity-75" style="font-size:12px; font-weight:500;">Total Deductions</span>
                                            <h3 class="fw-bold mb-0" style="font-size:24px; color:#f87171;">৳<span id="deductions_val">0.00</span></h3>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <span class="d-block mb-1 opacity-75" style="font-size:12px; font-weight:500;">Net Payout</span>
                                            <h2 class="fw-bold mb-0" style="font-size:32px; color:#4ade80;">৳<span id="net_salary_val">0.00</span></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="hrm-lbl">Remarks Note</label>
                                <textarea name="note" rows="2" class="form-control hrm-in" placeholder="Enter payroll disbursement reference..."></textarea>
                            </div>

                            <div class="mt-4 text-end">
                                <a href="{{ route('admin.hrm.payroll.index') }}" class="btn btn-light px-4 py-2 me-2" style="font-size:13.5px; border-radius:8px;">Cancel</a>
                                <button type="submit" class="btn-hrm-save px-4 py-2">Generate Payroll Sheet</button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-calculator fa-3x mb-3 opacity-50"></i>
                            <p style="font-size:14px;">Select an employee on the left sidebar to load their timecard ledger.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-outline-hrm {
    background: #fff;
    color: #1a1a2e;
    border: 1px solid #1a1a2e;
    font-size: 13px;
    font-weight: 500;
    border-radius: 8px;
    transition: all .2s;
}
.btn-outline-hrm:hover {
    background: #1a1a2e;
    color: #fff;
}
.hrm-lbl { font-size: 12.5px; font-weight: 500; color: #333; margin-bottom: 4px; display: block; }
.hrm-in { font-size: 13.5px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 8px 12px; }
.btn-hrm-save {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff; border: none; border-radius: 8px;
    padding: 8px 24px; font-size: 13.5px; font-weight: 500;
    box-shadow: 0 3px 10px rgba(233,30,99,.35);
}
.btn-hrm-save:hover { opacity: .95; }
.bg-net-salary-preview {
    background: linear-gradient(135deg, #1a1a2e, #162447);
}
.border-end-dashed {
    border-right: 1.5px dashed rgba(255,255,255,0.15);
}
@media (max-width: 767.98px) {
    .border-end-dashed { border-right: none; }
}
.nav-pills .nav-link {
    font-size: 13px; font-weight: 600; border-radius: 8px; color: #555; background: #f8fafc; border: 1px solid #e2e8f0;
    margin-right: 8px; padding: 10px 18px;
}
.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #e91e63, #c2185b); color: #fff; border: none;
}
</style>

@if($employee)
<script>
function calculateTotal() {
    // Earnings
    const basic = parseFloat(document.getElementById('basic_salary').value) || 0;
    const hra = parseFloat(document.getElementById('house_rent_allowance').value) || 0;
    const medical = parseFloat(document.getElementById('medical_allowance').value) || 0;
    const conveyance = parseFloat(document.getElementById('conveyance_allowance').value) || 0;
    const extraIncentives = parseFloat(document.getElementById('extra_incentives').value) || 0;
    const bonuses = parseFloat(document.getElementById('bonuses').value) || 0;
    const miscAllowances = parseFloat(document.getElementById('allowances').value) || 0;

    const gross = basic + hra + medical + conveyance + extraIncentives + bonuses + miscAllowances;

    // Deductions
    const pf = parseFloat(document.getElementById('provident_fund').value) || 0;
    const tax = parseFloat(document.getElementById('professional_tax').value) || 0;
    const advances = parseFloat(document.getElementById('advances_deduction').value) || 0;

    const totalDeductions = pf + tax + advances;
    const net = Math.max(0, gross - totalDeductions);

    // Update nodes
    document.getElementById('gross_salary_val').textContent = gross.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('deductions_val').textContent = totalDeductions.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('net_salary_val').textContent = net.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Auto run on DOM ready
document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
@endif
@endsection
