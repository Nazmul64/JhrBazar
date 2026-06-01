@extends('admin.master')
@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e;font-size:22px;">Edit Office Expense</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Modify office operational expense details.</p>
        </div>
        <a href="{{ route('admin.hrm.expense.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3" style="max-width:680px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.hrm.expense.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="hrm-lbl">Title *</label>
                        <input type="text" name="title" class="form-control hrm-in" value="{{ old('title', $expense->title) }}" required placeholder="e.g. Office Rent - May">
                    </div>
                    <div class="col-md-4">
                        <label class="hrm-lbl">Amount (৳) *</label>
                        <input type="number" name="amount" class="form-control hrm-in" step="0.01" min="0.01"
                               value="{{ old('amount', $expense->amount) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="hrm-lbl">Category *</label>
                        <select name="category_id" class="form-select hrm-in" required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $expense->category_id)==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="hrm-lbl">Expense Date *</label>
                        <input type="date" name="expense_date" class="form-control hrm-in"
                               value="{{ old('expense_date', $expense->expense_date) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="hrm-lbl">Payment Method *</label>
                        <select name="payment_method" class="form-select hrm-in" required>
                            @foreach(['Cash','Bank','bKash','Nagad','Other'] as $pm)
                                <option value="{{ $pm }}" {{ old('payment_method', $expense->payment_method)==$pm?'selected':'' }}>{{ $pm }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="hrm-lbl">Paid By</label>
                        <input type="text" name="paid_by" class="form-control hrm-in"
                               value="{{ old('paid_by', $expense->paid_by) }}" placeholder="Person name or account">
                    </div>
                    <div class="col-md-6">
                        <label class="hrm-lbl">Reference / Receipt No</label>
                        <input type="text" name="reference" class="form-control hrm-in"
                               value="{{ old('reference', $expense->reference) }}" placeholder="e.g. TXN-12345">
                    </div>
                    <div class="col-md-6">
                        <label class="hrm-lbl">Attachment</label>
                        <input type="file" name="attachment" class="form-control hrm-in" accept="image/*,.pdf">
                        @if($expense->attachment)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="text-decoration-none" style="font-size: 12px; color: #e91e63;">
                                    <i class="fas fa-paperclip me-1"></i> View Current Attachment
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="hrm-lbl">Note</label>
                        <textarea name="note" class="form-control hrm-in" rows="2" placeholder="Additional notes...">{{ old('note', $expense->note) }}</textarea>
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-hrm-save px-4">
                            <i class="fas fa-save me-1"></i> Update Expense
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
