@extends('admin.master')
@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e;font-size:22px;">New Salary Advance Request</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Create a new advance request for an employee.</p>
        </div>
        <a href="{{ route('admin.hrm.salary-advance.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3" style="max-width:620px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.hrm.salary-advance.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="hrm-lbl">Employee *</label>
                        <select name="employee_id" class="form-select hrm-in" required>
                            <option value="">— Select Employee —</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id')==$emp->id?'selected':'' }}>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="hrm-lbl">Advance Amount (৳) *</label>
                        <input type="number" name="amount" class="form-control hrm-in" step="0.01" min="1"
                               value="{{ old('amount') }}" required placeholder="e.g. 5000">
                    </div>
                    <div class="col-md-6">
                        <label class="hrm-lbl">Installments (months) *</label>
                        <input type="number" name="installments" class="form-control hrm-in" min="1" max="24"
                               value="{{ old('installments', 1) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="hrm-lbl">Request Date *</label>
                        <input type="date" name="request_date" class="form-control hrm-in"
                               value="{{ old('request_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="hrm-lbl">Reason</label>
                        <textarea name="reason" class="form-control hrm-in" rows="3"
                                  placeholder="Reason for advance request...">{{ old('reason') }}</textarea>
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-hrm-save px-4">
                            <i class="fas fa-save me-1"></i> Submit Request
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
.hrm-lbl{font-size:12.5px;font-weight:500;color:#333;margin-bottom:4px;display:block;}
.hrm-in{font-size:13px;border-radius:8px;border:1px solid #e2e8f0;padding:8px 12px;}
.btn-hrm-save{background:linear-gradient(135deg,#e91e63,#c2185b);color:#fff;border:none;border-radius:8px;padding:9px 20px;font-size:13.5px;font-weight:500;}
</style>
@endsection
