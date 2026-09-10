@extends('admin.master')

@section('title', 'Local Purchases & Supplier History')

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
</style>

<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ── Header ── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-cart-check-fill text-primary"></i> Local Purchases & Vendor History
            </h4>
            <p class="text-muted small mb-0">Record vendor purchases, update product stock in real-time, and manage payables.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.supplier.index') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold">
                <i class="bi bi-truck me-1"></i> Manage Suppliers
            </a>
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> New Purchase Entry
            </a>
        </div>
    </div>

    {{-- ── KPI Summary Cards ── --}}
    <div class="row g-3 mb-4">
        {{-- Total Purchases --}}
        <div class="col-6 col-md-3">
            <div class="stat-card-custom shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Purchases</div>
                        <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalPurchasesCount) }}</h3>
                    </div>
                    <div class="stat-icon-wrap bg-primary-subtle text-primary">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Amount --}}
        <div class="col-6 col-md-3">
            <div class="stat-card-custom shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Cost (৳)</div>
                        <h3 class="fw-bold text-dark mb-0 mt-1">৳{{ number_format($totalAmountSum, 2) }}</h3>
                    </div>
                    <div class="stat-icon-wrap bg-info-subtle text-info">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Paid --}}
        <div class="col-6 col-md-3">
            <div class="stat-card-custom shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Paid</div>
                        <h3 class="fw-bold text-success mb-0 mt-1">৳{{ number_format($totalPaidSum, 2) }}</h3>
                    </div>
                    <div class="stat-icon-wrap bg-success-subtle text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Due --}}
        <div class="col-6 col-md-3">
            <div class="stat-card-custom shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Due</div>
                        <h3 class="fw-bold text-danger mb-0 mt-1">৳{{ number_format($totalDueSum, 2) }}</h3>
                    </div>
                    <div class="stat-icon-wrap bg-danger-subtle text-danger">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filter Toolbar ── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.purchases.index') }}" class="row g-2 align-items-center">
                {{-- Search Invoice / Supplier --}}
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Invoice # or Supplier..." value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Supplier Selector --}}
                <div class="col-6 col-md-3">
                    <select name="supplier_id" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- From Date --}}
                <div class="col-6 col-md-2">
                    <input type="date" name="from_date" class="form-control bg-light border-0" placeholder="From Date" value="{{ request('from_date') }}" title="From Date">
                </div>

                {{-- To Date --}}
                <div class="col-6 col-md-2">
                    <input type="date" name="to_date" class="form-control bg-light border-0" placeholder="To Date" value="{{ request('to_date') }}" title="To Date">
                </div>

                {{-- Submit & Reset --}}
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-3">Filter</button>
                    @if(request()->hasAny(['search', 'supplier_id', 'from_date', 'to_date']))
                        <a href="{{ route('admin.purchases.index') }}" class="btn btn-light rounded-3" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── Purchases Table ── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table-premium">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Invoice No</th>
                        <th>Supplier / Vendor</th>
                        <th class="text-center">Items Count</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-end">Paid Amount</th>
                        <th class="text-end">Due Amount</th>
                        <th class="text-center">Payment Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $item)
                        <tr>
                            <td class="ps-4 text-nowrap">
                                <span class="fw-semibold text-dark">{{ $item->purchase_date ? $item->purchase_date->format('d M, Y') : '—' }}</span>
                                <div class="small text-muted">{{ $item->created_at ? $item->created_at->format('h:i A') : '' }}</div>
                            </td>
                            <td>
                                <a href="{{ route('admin.purchases.show', $item->id) }}" class="fw-bold text-primary font-monospace text-decoration-none">
                                    {{ $item->invoice_no }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $item->supplier?->name ?? '—' }}</div>
                                @if($item->supplier?->phone)
                                    <small class="text-muted"><i class="bi bi-telephone"></i> {{ $item->supplier->phone }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    {{ $item->items->count() }} item{{ $item->items->count() == 1 ? '' : 's' }}
                                </span>
                            </td>
                            <td class="text-end font-monospace fw-bold text-dark">
                                ৳{{ number_format($item->total_amount, 2) }}
                            </td>
                            <td class="text-end font-monospace text-success fw-semibold">
                                ৳{{ number_format($item->paid_amount, 2) }}
                            </td>
                            <td class="text-end font-monospace text-danger fw-semibold">
                                @if($item->due_amount > 0)
                                    ৳{{ number_format($item->due_amount, 2) }}
                                @else
                                    <span class="text-muted">৳0.00</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->payment_status === 'paid')
                                    <span class="badge bg-success-subtle text-success py-1 px-2 rounded-pill fw-semibold">Paid</span>
                                @elseif($item->payment_status === 'partial')
                                    <span class="badge bg-warning-subtle text-warning-emphasis py-1 px-2 rounded-pill fw-semibold">Partial</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger py-1 px-2 rounded-pill fw-semibold">Due</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('admin.purchases.show', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-2.5 py-1 me-1" title="View & Print Invoice">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <form action="{{ route('admin.purchases.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this purchase record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-cart-x fs-1 d-block mb-2 text-secondary"></i>
                                No purchase records found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
