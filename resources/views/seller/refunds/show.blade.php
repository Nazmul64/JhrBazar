@extends('admin.master')

@section('title', 'Refund Details')

@section('content')
<div class="page-heading">
    <h3>Refund Details</h3>
</div>

<div class="page-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Refund #{{ $refund->id }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Order ID:</strong>
                                <p>{{ $refund->order->order_number ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Requested At:</strong>
                                <p>{{ $refund->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Product:</strong>
                                <p>{{ $refund->product_name }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Quantity:</strong>
                                <p>{{ $refund->quantity }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Total Amount:</strong>
                                <p>{{ format_price($refund->total_amount) }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Cancel Reason:</strong>
                                <p>{{ $refund->getCancelReasonDisplay() }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Courier:</strong>
                                <p>{{ $refund->courier->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($refund->cancel_reason_description)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <strong>Reason Details:</strong>
                                    <p>{{ $refund->cancel_reason_description }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <p>{!! $refund->getStatusBadge() !!}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Seller Note:</strong>
                                <p>{{ $refund->seller_note ?? 'No additional seller note.' }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Admin Note:</strong>
                                <p>{{ $refund->admin_note ?? 'No admin note yet.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Add Seller Note</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('seller.refunds.note', $refund->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="seller_note" class="form-label">Note</label>
                                <textarea id="seller_note" name="seller_note" class="form-control" rows="5" placeholder="Add a note for the refund team">{{ old('seller_note', $refund->seller_note) }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Note</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h4>Refund Timeline</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Requested:</strong> {{ $refund->created_at->format('d M Y, h:i A') }}</p>
                        @if($refund->refund_date)
                            <p><strong>Processed:</strong> {{ $refund->refund_date->format('d M Y, h:i A') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
