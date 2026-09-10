@extends('admin.master')

@section('title', 'Purchase Invoice #' . $purchase->invoice_no)

@section('content')
<style>
    @media print {
        body { background: #fff !important; }
        #header, #sidebar, .no-print { display: none !important; }
        .main-content, #main { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .invoice-card { box-shadow: none !important; border: none !important; }
    }
</style>

<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ── Action Toolbar ── --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Back to Purchases List
        </a>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold shadow-sm" onclick="window.print()">
                <i class="bi bi-printer-fill me-1"></i> Print Invoice
            </button>
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-outline-primary rounded-3 px-3 py-2 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> New Purchase
            </a>
        </div>
    </div>

    {{-- ── Invoice Card ── --}}
    <div class="card border-0 shadow-sm rounded-4 invoice-card mx-auto" style="max-width: 900px;">
        <div class="card-body p-4 p-md-5">

            {{-- Header with Company & Invoice Details --}}
            <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">{{ $settings->website_name ?? 'Jhr Bazar' }}</h3>
                    <p class="text-muted small mb-0">{{ $settings->address ?? 'Corporate Office' }}</p>
                    <p class="text-muted small mb-0">Phone: {{ $settings->phone ?? '—' }} | Email: {{ $settings->email ?? '—' }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary fs-6 px-3 py-1.5 rounded-pill mb-2">LOCAL PURCHASE</span>
                    <h5 class="fw-bold font-monospace text-dark mb-1">{{ $purchase->invoice_no }}</h5>
                    <div class="small text-muted">Date: <strong>{{ $purchase->purchase_date ? $purchase->purchase_date->format('d M, Y') : '—' }}</strong></div>
                </div>
            </div>

            {{-- Supplier & Transaction Meta --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-6">
                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Supplier / Vendor Details</h6>
                    <h5 class="fw-bold text-dark mb-1">{{ $purchase->supplier?->name ?? 'Local Vendor' }}</h5>
                    @if($purchase->supplier?->phone)
                        <div class="small text-muted"><i class="bi bi-telephone me-1"></i> {{ $purchase->supplier->phone }}</div>
                    @endif
                    @if($purchase->supplier?->email)
                        <div class="small text-muted"><i class="bi bi-envelope me-1"></i> {{ $purchase->supplier->email }}</div>
                    @endif
                    @if($purchase->supplier?->address)
                        <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $purchase->supplier->address }}</div>
                    @endif
                </div>
                <div class="col-6 col-md-6 text-md-end">
                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Payment Info</h6>
                    <div class="small text-muted">Payment Method: <strong>{{ $purchase->payment_method ?? 'Cash' }}</strong></div>
                    <div class="small text-muted">Payment Status: 
                        @if($purchase->payment_status === 'paid')
                            <span class="badge bg-success-subtle text-success fw-bold">Paid</span>
                        @elseif($purchase->payment_status === 'partial')
                            <span class="badge bg-warning-subtle text-warning-emphasis fw-bold">Partial</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger fw-bold">Due</span>
                        @endif
                    </div>
                    @if($purchase->creator)
                        <div class="small text-muted">Recorded By: <strong>{{ $purchase->creator->name }}</strong></div>
                    @endif
                </div>
            </div>

            {{-- Items Table --}}
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 13px;">
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Item Description</th>
                            <th>SKU</th>
                            <th class="text-center" style="width: 100px;">Quantity</th>
                            <th class="text-end" style="width: 130px;">Unit Cost</th>
                            <th class="text-end" style="width: 150px;">Total (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $item->product?->name ?? 'Product ID: '.$item->product_id }}</span>
                                </td>
                                <td>
                                    <code class="text-primary">{{ $item->product?->sku ?? '—' }}</code>
                                </td>
                                <td class="text-center font-monospace fw-semibold">{{ $item->quantity }}</td>
                                <td class="text-end font-monospace">৳{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end font-monospace fw-bold text-dark">৳{{ number_format($item->subtotal ?? $item->sub_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Grand Total:</td>
                            <td class="text-end fw-bold font-monospace fs-6">৳{{ number_format($purchase->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-semibold text-success">Paid Amount:</td>
                            <td class="text-end fw-bold font-monospace text-success">৳{{ number_format($purchase->paid_amount, 2) }}</td>
                        </tr>
                        @if($purchase->due_amount > 0)
                            <tr class="table-danger">
                                <td colspan="5" class="text-end fw-bold text-danger">Due Amount:</td>
                                <td class="text-end fw-bold font-monospace text-danger fs-6">৳{{ number_format($purchase->due_amount, 2) }}</td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>

            {{-- Notes & Attachment --}}
            <div class="row g-3 pt-2">
                <div class="col-12 col-md-8">
                    @if($purchase->note)
                        <div class="p-3 bg-light rounded-3">
                            <h6 class="small fw-bold text-muted text-uppercase mb-1">Remarks / Note:</h6>
                            <p class="small text-dark mb-0">{{ $purchase->note }}</p>
                        </div>
                    @endif
                </div>
                <div class="col-12 col-md-4 text-md-end no-print">
                    @if($purchase->purchase_slip)
                        <a href="{{ asset($purchase->purchase_slip) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-3">
                            <i class="bi bi-paperclip me-1"></i> View Attached Voucher / Slip
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
