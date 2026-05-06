@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">POS Sales History</h4>
        <a href="{{ route('seller.pos.index') }}" class="btn btn-primary" style="background: #e7567c; border:none;">Go to POS</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Order ID</th>
                            <th>Order Date</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                @if($invoice->customer)
                                    <div>{{ $invoice->customer->first_name }} {{ $invoice->customer->last_name }}</div>
                                    <small class="text-muted">{{ $invoice->customer->user->phone ?? '' }}</small>
                                @else
                                    <span class="text-muted">Walk-in Customer</span>
                                @endif
                            </td>
                            <td><span class="fw-bold">৳{{ number_format($invoice->grand_total, 2) }}</span></td>
                            <td>
                                <span class="badge bg-info text-dark px-3">{{ ucfirst($invoice->payment_method) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success px-3">Paid</span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-success"><i class="bi bi-download"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No sales history found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($invoices->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
