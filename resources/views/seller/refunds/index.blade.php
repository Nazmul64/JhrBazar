@extends('admin.master')

@section('title', 'Refund Requests')

@section('content')
<div class="page-heading">
    <h3>Refund Requests</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Refund Requests</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Requested At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($refunds as $refund)
                                <tr>
                                    <td>{{ $refund->id }}</td>
                                    <td>#{{ $refund->order->invoice->invoice_number ?? $refund->order_id }}</td>
                                    <td>{{ $refund->product_name }}</td>
                                    <td>{{ $refund->quantity }}</td>
                                    <td>{{ format_price($refund->total_amount) }}</td>
                                    <td>{{ $refund->getCancelReasonDisplay() }}</td>
                                    <td>{!! $refund->getStatusBadge() !!}</td>
                                    <td>{{ $refund->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('seller.refunds.show', $refund->id) }}" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No refund requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $refunds->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
