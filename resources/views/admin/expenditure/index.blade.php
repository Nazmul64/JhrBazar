@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e; font-size:22px;">Company Expenditures</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Monitor business expenses, office costs, utility bills, and salary outflows.</p>
        </div>
        <button class="btn-hrm-add" data-bs-toggle="modal" data-bs-target="#logExpenditureModal">
            <i class="fas fa-plus me-1"></i> Log Expenditure
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Month Outflow --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 text-white bg-gradient-premium">
                <div class="card-body p-3">
                    <span class="d-block mb-1 opacity-75" style="font-size:12px;">Total Month Outflow</span>
                    <h4 class="fw-bold mb-0" style="font-size:20px;">৳{{ number_format($totalExpenditure, 2) }}</h4>
                </div>
            </div>
        </div>

        {{-- Salaries Outflow --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3" style="background:#fff;">
                <div class="card-body p-3">
                    <span class="d-block mb-1 text-muted" style="font-size:12px;">Salaries Disbursed</span>
                    <h4 class="fw-bold mb-0" style="font-size:20px; color:#2e7d32;">৳{{ number_format($salaryExpense, 2) }}</h4>
                </div>
            </div>
        </div>

        {{-- Office Rent --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3" style="background:#fff;">
                <div class="card-body p-3">
                    <span class="d-block mb-1 text-muted" style="font-size:12px;">Office & Rent</span>
                    <h4 class="fw-bold mb-0" style="font-size:20px; color:#1a1a2e;">৳{{ number_format($officeExpense, 2) }}</h4>
                </div>
            </div>
        </div>

        {{-- Utility & Bills --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3" style="background:#fff;">
                <div class="card-body p-3">
                    <span class="d-block mb-1 text-muted" style="font-size:12px;">Utilities & Bills</span>
                    <h4 class="fw-bold mb-0" style="font-size:20px; color:#f59e0b;">৳{{ number_format($billsExpense, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.expenditure.index') }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
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
                <select name="category" class="form-select select-hrm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-hrm-active px-3" style="border-radius: 8px;">Load Expenses</button>
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
                            <th class="py-3 hrm-th">Expenditure Description</th>
                            <th class="py-3 hrm-th">Category</th>
                            <th class="py-3 hrm-th">Amount</th>
                            <th class="py-3 hrm-th">Date</th>
                            <th class="py-3 hrm-th">Logged By</th>
                            <th class="py-3 hrm-th text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenditures as $index => $exp)
                        <tr class="hrm-row">
                            <td class="ps-4 text-muted" style="font-size:13px;">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold" style="font-size:13.5px; color:#222;">{{ $exp->title }}</div>
                                @if($exp->description)
                                    <span class="text-muted" style="font-size:11.5px;">{{ $exp->description }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border p-2" style="font-size:11px;">
                                    {{ $exp->category }}
                                </span>
                            </td>
                            <td style="font-size:14px; font-weight:700; color:#1a1a2e;">
                                ৳{{ number_format($exp->amount, 2) }}
                            </td>
                            <td style="font-size:13px; color:#555;">
                                {{ date('d M, Y', strtotime($exp->date)) }}
                            </td>
                            <td style="font-size:13px; color:#666;">
                                {{ $exp->creator->name ?? 'System' }}
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Edit Button --}}
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editExpenditureModal_{{ $exp->id }}" title="Edit log">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- Delete Log --}}
                                    <form action="{{ route('admin.expenditure.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expenditure record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete log">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Expenditure Modal --}}
                        <div class="modal fade" id="editExpenditureModal_{{ $exp->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-3">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="fw-bold" style="color:#1a1a2e; font-size:18px;">Edit Expenditure log</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.expenditure.update', $exp->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="hrm-lbl">Expenditure Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control hrm-in" required value="{{ old('title', $exp->title) }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="hrm-lbl">Category <span class="text-danger">*</span></label>
                                                    <select name="category" class="form-select hrm-in" required>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat }}" {{ $exp->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="hrm-lbl">Amount (BDT) <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" name="amount" class="form-control hrm-in" required value="{{ old('amount', $exp->amount) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="hrm-lbl">Date <span class="text-danger">*</span></label>
                                                    <input type="date" name="date" class="form-control hrm-in" required value="{{ old('date', $exp->date) }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="hrm-lbl">Description / Extra Remarks</label>
                                                    <textarea name="description" rows="3" class="form-control hrm-in">{{ old('description', $exp->description) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="font-size:13.5px; border-radius:8px;">Close</button>
                                            <button type="submit" class="btn-hrm-save">Update Log</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-receipt fa-3x text-muted mb-3 d-block"></i>
                                <span class="text-muted" style="font-size:14px;">No expenditures logged for the selected period.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Log Expenditure Modal --}}
    <div class="modal fade" id="logExpenditureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold" style="color:#1a1a2e; font-size:18px;">Log Expenditure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.expenditure.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="hrm-lbl">Expenditure Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control hrm-in" required placeholder="Utility bills, office refreshments, etc.">
                            </div>
                            <div class="col-12">
                                <label class="hrm-lbl">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select hrm-in" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="hrm-lbl">Amount (BDT) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control hrm-in" required placeholder="Enter amount">
                            </div>
                            <div class="col-md-6">
                                <label class="hrm-lbl">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control hrm-in" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-12">
                                <label class="hrm-lbl">Description / Extra Remarks</label>
                                <textarea name="description" rows="3" class="form-control hrm-in" placeholder="Write reference descriptions..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="font-size:13.5px; border-radius:8px;">Close</button>
                        <button type="submit" class="btn-hrm-save">Log Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-premium {
    background: linear-gradient(135deg, #e91e63, #c2185b);
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
.btn-hrm-active {
    background: #e91e63; color: #fff !important; font-size: 12.5px; font-weight: 500; border-radius: 6px;
}
.select-hrm {
    width: 140px;
    font-size: 13.5px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}
.hrm-th {
    font-size: 13px; font-weight: 600; color: #888;
    border-bottom: 1px solid #f0f0f0 !important;
    text-transform: uppercase; letter-spacing: .4px;
}
.hrm-row { border-bottom: 1px solid #f7f7f7; transition: background .15s; }
.hrm-row:hover { background: #fafafa; }
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
