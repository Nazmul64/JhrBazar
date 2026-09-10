@extends('admin.master')

@section('title', 'Income & Expense Tracker (Cashbook)')

@section('content')
<style>
    .stat-card-custom {
        border-radius: 16px;
        padding: 20px;
        transition: all 0.25s ease;
        border: 1px solid rgba(0,0,0,0.06);
        background: #fff;
    }
    .stat-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .badge-income {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-expense {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ── Header ── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-wallet2 text-primary"></i> Income & Expense Accounts Ledger
            </h4>
            <p class="text-muted small mb-0">Track all operational cash inflow and outflow with categorized voucher records and reports.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.accounts.categories') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold">
                <i class="bi bi-tags me-1"></i> Purpose Categories
            </a>
            <a href="{{ route('admin.accounts.report', request()->all()) }}" target="_blank" class="btn btn-outline-primary rounded-3 px-3 py-2 fw-semibold">
                <i class="bi bi-printer me-1"></i> Print Statement
            </a>
            <button type="button" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                <i class="bi bi-plus-circle"></i> Add Transaction
            </button>
        </div>
    </div>

    {{-- ── Monthly & Overall KPI Cards ── --}}
    <div class="row g-3 mb-4">
        {{-- Month Inflow --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card-custom shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">This Month Income</div>
                        <h3 class="fw-bold text-success mb-0 mt-1">৳{{ number_format($monthIncome, 2) }}</h3>
                        <small class="text-muted">Till date: ৳{{ number_format($allTimeIncome, 2) }}</small>
                    </div>
                    <div class="stat-icon-wrap bg-success-subtle text-success">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Month Outflow --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card-custom shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">This Month Expense</div>
                        <h3 class="fw-bold text-danger mb-0 mt-1">৳{{ number_format($monthExpense, 2) }}</h3>
                        <small class="text-muted">Till date: ৳{{ number_format($allTimeExpense, 2) }}</small>
                    </div>
                    <div class="stat-icon-wrap bg-danger-subtle text-danger">
                        <i class="bi bi-graph-down-arrow"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Month Net Balance --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card-custom shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Month Net Balance</div>
                        <h3 class="fw-bold mb-0 mt-1 {{ $monthBalance >= 0 ? 'text-primary' : 'text-danger' }}">
                            ৳{{ number_format($monthBalance, 2) }}
                        </h3>
                        <small class="text-muted">All-time: ৳{{ number_format($allTimeBalance, 2) }}</small>
                    </div>
                    <div class="stat-icon-wrap bg-primary-subtle text-primary">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtered Results Net --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card-custom shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Filtered Net Total</div>
                        <h3 class="fw-bold mb-0 mt-1 {{ $filteredBalance >= 0 ? 'text-dark' : 'text-danger' }}">
                            ৳{{ number_format($filteredBalance, 2) }}
                        </h3>
                        <small class="text-muted">In: ৳{{ number_format($filteredIncome, 0) }} | Out: ৳{{ number_format($filteredExpense, 0) }}</small>
                    </div>
                    <div class="stat-icon-wrap bg-info-subtle text-info">
                        <i class="bi bi-funnel"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filter Toolbar ── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.accounts.index') }}" class="row g-2 align-items-center">
                {{-- Search --}}
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Title, Voucher #, Ref..." value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Type (Income / Expense) --}}
                <div class="col-6 col-md-2">
                    <select name="transaction_type" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">All Types (In/Out)</option>
                        <option value="income" {{ request('transaction_type') === 'income' ? 'selected' : '' }}>Income (Inflow)</option>
                        <option value="expense" {{ request('transaction_type') === 'expense' ? 'selected' : '' }}>Expense (Outflow)</option>
                    </select>
                </div>

                {{-- Category --}}
                <div class="col-6 col-md-2">
                    <select name="category_id" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- From Date --}}
                <div class="col-6 col-md-2">
                    <input type="date" name="start_date" class="form-control bg-light border-0" placeholder="From Date" value="{{ request('start_date') }}" title="Start Date">
                </div>

                {{-- To Date --}}
                <div class="col-6 col-md-2">
                    <input type="date" name="end_date" class="form-control bg-light border-0" placeholder="To Date" value="{{ request('end_date') }}" title="End Date">
                </div>

                {{-- Submit & Reset --}}
                <div class="col-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-3" title="Filter"><i class="bi bi-filter"></i></button>
                    @if(request()->hasAny(['search', 'transaction_type', 'category_id', 'start_date', 'end_date', 'payment_method']))
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-light rounded-3" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── Transactions Table ── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Voucher #</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Title & Description</th>
                        <th>Payment Method</th>
                        <th class="text-end">Income (৳)</th>
                        <th class="text-end">Expense (৳)</th>
                        <th class="text-center">Doc</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgers as $item)
                        <tr>
                            <td class="ps-4 text-nowrap">
                                <span class="fw-semibold text-dark">{{ $item->transaction_date ? $item->transaction_date->format('d M, Y') : '—' }}</span>
                            </td>
                            <td>
                                <span class="font-monospace text-muted small fw-semibold">{{ $item->voucher_no ?? '—' }}</span>
                            </td>
                            <td>
                                @if($item->transaction_type === 'income')
                                    <span class="badge badge-income rounded-pill px-2.5 py-1">
                                        <i class="bi bi-arrow-down-left me-1"></i> Income
                                    </span>
                                @else
                                    <span class="badge badge-expense rounded-pill px-2.5 py-1">
                                        <i class="bi bi-arrow-up-right me-1"></i> Expense
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($item->category)
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background-color: {{ $item->category->color ?? '#3b82f6' }};"></span>
                                        {{ $item->category->name }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $item->title }}</div>
                                @if($item->description)
                                    <small class="text-muted d-block text-truncate" style="max-width: 250px;">{{ $item->description }}</small>
                                @endif
                                @if($item->reference)
                                    <small class="text-secondary"><i class="bi bi-link-45deg"></i> Ref: {{ $item->reference }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary text-uppercase border px-2 py-1">
                                    {{ str_replace('_', ' ', $item->payment_method) }}
                                </span>
                            </td>
                            <td class="text-end font-monospace fw-bold text-success">
                                @if($item->income_amount > 0)
                                    +৳{{ number_format($item->income_amount, 2) }}
                                @else
                                    <span class="text-muted opacity-50">—</span>
                                @endif
                            </td>
                            <td class="text-end font-monospace fw-bold text-danger">
                                @if($item->expense_amount > 0)
                                    -৳{{ number_format($item->expense_amount, 2) }}
                                @else
                                    <span class="text-muted opacity-50">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->document_file)
                                    <a href="{{ asset('storage/' . $item->document_file) }}" target="_blank" class="btn btn-sm btn-light rounded-circle" title="View Attachment">
                                        <i class="bi bi-paperclip text-primary"></i>
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.accounts.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this transaction entry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-wallet2 fs-1 d-block mb-2 text-secondary"></i>
                                No transaction entries found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ledgers->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $ledgers->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ── Add Transaction Modal ── --}}
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.accounts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="addTransactionModalLabel">
                        <i class="bi bi-plus-circle text-primary me-2"></i> Record New Transaction
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Type Selector (Income vs Expense) --}}
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Transaction Type <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="transaction_type" id="typeExpense" value="expense" checked>
                                <label class="btn btn-outline-danger w-100 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2" for="typeExpense">
                                    <i class="bi bi-arrow-up-right"></i> Expense (Outflow)
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="transaction_type" id="typeIncome" value="income">
                                <label class="btn btn-outline-success w-100 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2" for="typeIncome">
                                    <i class="bi bi-arrow-down-left"></i> Income (Inflow)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Title --}}
                        <div class="col-12 col-md-8">
                            <label class="form-label small fw-bold text-muted">Title / Purpose <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Office Electricity Bill, Shop Rent, Product Sale Cash" required>
                        </div>

                        {{-- Amount --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-muted">Amount (৳) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">৳</span>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control rounded-end-3 fw-bold" placeholder="0.00" required>
                            </div>
                        </div>

                        {{-- Category --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted">Category</label>
                            <select name="category_id" class="form-select rounded-3">
                                <option value="">-- Uncategorized --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }} ({{ ucfirst($cat->type) }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select rounded-3" required>
                                <option value="cash" selected>Cash</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="rocket">Rocket</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="card">Debit/Credit Card</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        {{-- Transaction Date --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted">Date <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>

                        {{-- Voucher No --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted">Voucher No <small class="text-muted">(Optional - Auto-generated if blank)</small></label>
                            <input type="text" name="voucher_no" class="form-control rounded-3" placeholder="e.g. EXP-202609-0001">
                        </div>

                        {{-- Reference --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted">Reference / Cheque No / TrxID</label>
                            <input type="text" name="reference" class="form-control rounded-3" placeholder="Optional reference">
                        </div>

                        {{-- Document File --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted">Attachment / Receipt (Image or PDF)</label>
                            <input type="file" name="document" class="form-control rounded-3" accept="image/*,.pdf,.doc,.docx">
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Note / Description</label>
                            <textarea name="description" rows="2" class="form-control rounded-3" placeholder="Additional details or remarks..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
