@extends('admin.master')
@section('content')
<style>
    :root {
        --brand:       #e8174a;
        --brand-hover: #c9113e;
        --dark:        #1a1d23;
        --muted:       #6b7280;
        --border:      #e5e7eb;
        --surface:     #f8f9fc;
        --r-lg: 14px; --r-md: 10px; --r-sm: 7px;
    }

    .form-card {
        background:#fff; border-radius:var(--r-lg);
        box-shadow:0 1px 4px rgba(0,0,0,.06); border:1px solid var(--border);
        padding:24px;
    }
    .form-title { font-size:16px; font-weight:700; color:var(--dark); margin-bottom:20px; display:flex; align-items:center; gap:8px; }
    .form-label { font-size:13px; font-weight:600; color:var(--dark); margin-bottom:6px; }
    .text-danger { color:#ef4444; }
    .form-control, .form-select {
        border-radius:var(--r-sm); border:1px solid var(--border);
        padding:10px 14px; font-size:14px; color:var(--dark);
        box-shadow:none;
    }
    .form-control:focus, .form-select:focus {
        border-color:var(--brand); box-shadow:0 0 0 3px rgba(232,23,74,.1);
    }
    .btn-submit {
        background:var(--brand); color:#fff; border:none;
        border-radius:var(--r-md); padding:10px 24px;
        font-size:14px; font-weight:600; cursor:pointer;
    }
    .btn-submit:hover { background:var(--brand-hover); }
    .btn-cancel {
        background:transparent; color:var(--muted); border:1px solid var(--border);
        border-radius:var(--r-md); padding:10px 24px;
        font-size:14px; font-weight:600; text-decoration:none;
    }
    .btn-cancel:hover { background:var(--surface); color:var(--dark); }
</style>

<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('seller.promocode.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0 fw-bold" style="color:var(--dark);">Add New Voucher</h4>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <div class="form-title">
            <i class="bi bi-plus-circle"></i> Add New Voucher
        </div>

        <form action="{{ route('seller.promocode.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Voucher Code <span class="text-danger">*</span></label>
                    <input type="text" name="voucher_code" class="form-control" placeholder="Voucher Code" value="{{ old('voucher_code') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                    <select name="discount_type" class="form-select" required>
                        <option value="amount" {{ old('discount_type') == 'amount' ? 'selected' : '' }}>Amount</option>
                        <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Discount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="discount" class="form-control" placeholder="Discount" value="{{ old('discount') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Minimum Order Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="minimum_order_amount" class="form-control" placeholder="Minimum Order Amount" value="{{ old('minimum_order_amount', 0) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Limit For Single User</label>
                    <input type="number" name="limit_for_single_user" class="form-control" placeholder="ex: 5" value="{{ old('limit_for_single_user') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Maximum Discount Amount</label>
                    <input type="number" step="0.01" name="maximum_discount_amount" class="form-control" placeholder="ex: 300" value="{{ old('maximum_discount_amount') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Expired Date <span class="text-danger">*</span></label>
                    <input type="date" name="expired_date" class="form-control" value="{{ old('expired_date') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Expired Time <span class="text-danger">*</span></label>
                    <input type="time" name="expired_time" class="form-control" value="{{ old('expired_time') }}" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-5 pt-3" style="border-top:1px solid var(--border);">
                <a href="{{ route('seller.promocode.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection
