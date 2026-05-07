@extends('admin.master')

@section('content')
<div class="container-fluid py-4">
    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body">
            <form action="{{ route('seller.purchase.summary') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Supplier</label>
                    <select name="supplier_id" class="form-select border-0 bg-light" style="border-radius: 10px;">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="date" name="start_date" class="form-control border-0 bg-light" value="{{ request('start_date') }}" style="border-radius: 10px;">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="date" name="end_date" class="form-control border-0 bg-light" value="{{ request('end_date') }}" style="border-radius: 10px;">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 10px;">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('seller.purchase.summary') }}" class="btn btn-light" style="border-radius: 10px;">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats cards for summary --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white" style="border-radius: 15px;">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Purchases</p>
                            <h3 class="fw-bold mb-0">${{ number_format($purchases->sum('total_amount'), 2) }}</h3>
                        </div>
                        <i class="bi bi-cart-check display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white" style="border-radius: 15px;">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Paid</p>
                            <h3 class="fw-bold mb-0">${{ number_format($purchases->sum('paid_amount'), 2) }}</h3>
                        </div>
                        <i class="bi bi-cash-stack display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-danger text-white" style="border-radius: 15px;">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Due</p>
                            <h3 class="fw-bold mb-0">${{ number_format($purchases->sum('due_amount'), 2) }}</h3>
                        </div>
                        <i class="bi bi-exclamation-octagon display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Invoice</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end pe-4">Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $purchase->invoice_no }}</td>
                            <td>{{ $purchase->supplier->name }}</td>
                            <td>{{ date('d M, Y', strtotime($purchase->purchase_date)) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($purchase->total_amount, 2) }}</td>
                            <td class="text-end text-success fw-semibold">{{ number_format($purchase->paid_amount, 2) }}</td>
                            <td class="text-end text-danger fw-semibold pe-4">{{ number_format($purchase->due_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No data matches your filters.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($purchases->count() > 0)
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="3" class="ps-4 text-uppercase">Total</td>
                            <td class="text-end">${{ number_format($purchases->sum('total_amount'), 2) }}</td>
                            <td class="text-end text-success">${{ number_format($purchases->sum('paid_amount'), 2) }}</td>
                            <td class="text-end text-danger pe-4">${{ number_format($purchases->sum('due_amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
