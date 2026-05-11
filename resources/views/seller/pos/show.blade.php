@extends('admin.master')

@section('content')
@php
    $cur = $settings->default_currency ?? '৳';
    $order = $invoice->order;
@endphp

<style>
:root {
    --primary: #4361ee;
    --success: #22c55e;
    --warning: #f59e0b;
    --danger:  #ef4444;
    --text:    #1e293b;
    --muted:   #64748b;
    --bg:      #f8fafc;
    --white:   #ffffff;
    --border:  #e2e8f0;
    --radius:  12px;
}

.order-detail-page {
    padding: 24px;
    background: var(--bg);
    min-height: 100vh;
    font-family: 'Inter', system-ui, sans-serif;
}

.top-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text);
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-back:hover {
    background: #f1f5f9;
}

.detail-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}

.card {
    background: var(--white);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 24px;
    overflow: hidden;
}
.card-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fafafa;
}
.card-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
}

.info-item {
    padding: 12px 24px;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #f8fafc;
}
.info-label { color: var(--muted); font-size: 13px; font-weight: 500; }
.info-value { color: var(--text); font-size: 13px; font-weight: 600; }

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}
.badge-pending    { background: #fef9c3; color: #a16207; }
.badge-processing { background: #dbeafe; color: #1e40af; }
.badge-shipped    { background: #ede9fe; color: #5b21b6; }
.badge-delivered  { background: #dcfce7; color: #166534; }
.badge-cancelled  { background: #fee2e2; color: #991b1b; }

.prod-table { width: 100%; border-collapse: collapse; }
.prod-table th {
    padding: 14px 24px;
    background: #f8fafc;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
}
.prod-table td {
    padding: 16px 24px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}
.prod-cell { display: flex; align-items: center; gap: 12px; }
.prod-img { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; background: #f1f5f9; }
.prod-img-ph { width: 48px; height: 48px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.prod-name { font-weight: 700; font-size: 14px; color: var(--text); margin-bottom: 2px; }
.prod-sku { font-size: 11px; color: var(--muted); font-family: monospace; }

.summary-row {
    display: flex;
    justify-content: flex-end;
    padding: 12px 24px;
    font-size: 14px;
}
.summary-label { width: 120px; color: var(--muted); text-align: right; margin-right: 20px; }
.summary-value { width: 100px; text-align: right; font-weight: 700; color: var(--text); }

.total-row {
    background: #f8fafc;
    border-top: 1px solid var(--border);
    padding: 16px 24px;
    font-size: 18px;
    font-weight: 800;
    color: var(--primary);
}

@media (max-width: 992px) {
    .detail-grid { grid-template-columns: 1fr; }
}
</style>

<div class="order-detail-page">
    <div class="top-nav">
        <a href="{{ route('seller.orders.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Orders
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('seller.pos.invoice', $invoice->id) }}" target="_blank" class="btn btn-dark" style="border-radius:8px; font-weight:600;">
                <i class="bi bi-printer"></i> Print Invoice
            </a>
        </div>
    </div>

    <div class="detail-grid">
        {{-- Left Column: Items & Details --}}
        <div class="left-col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Items Ordered</h3>
                    <span class="badge bg-light text-dark border">{{ count($invoice->items) }} items</span>
                </div>
                <div class="card-body p-0">
                    <table class="prod-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $index => $item)
                                @php
                                    $price      = (float)($item['price'] ?? 0);
                                    $qty        = (int)($item['qty'] ?? 1);
                                    $lineTotal  = (float)($item['line_total'] ?? ($price * $qty));
                                    $bcDisplay  = !empty($item['barcode']) ? $item['barcode'] : ($item['sku'] ?? '');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="prod-cell">
                                            @php
                                                $thumb = $item['thumbnail'] ?? $item['image'] ?? '';
                                                $name = $item['name'] ?? $item['title'] ?? 'Product';
                                            @endphp
                                            @if(!empty($thumb))
                                                <img class="prod-img" 
                                                     src="{{ asset($thumb) }}" 
                                                     alt="{{ $name }}"
                                                     onerror="this.outerHTML='<div class=\'prod-img-ph\'>📦</div>'">
                                            @else
                                                <div class="prod-img-ph">📦</div>
                                            @endif
                                            <div>
                                                <div class="prod-name">{{ $name }}</div>
                                                @if($bcDisplay)
                                                    <div class="prod-sku">{{ $bcDisplay }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $cur }}{{ number_format($price, 2) }}</td>
                                    <td class="text-center">{{ $qty }}</td>
                                    <td class="text-end fw-bold">{{ $cur }}{{ number_format($lineTotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="py-3">
                        <div class="summary-row">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value">{{ $cur }}{{ number_format($invoice->sub_total, 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Discount</span>
                            <span class="summary-value text-danger">-{{ $cur }}{{ number_format($invoice->discount, 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Tax</span>
                            <span class="summary-value">{{ $cur }}{{ number_format($invoice->tax_amount, 2) }}</span>
                        </div>
                        <div class="summary-row total-row">
                            <span class="summary-label" style="color:var(--text)">Total</span>
                            <span class="summary-value">{{ $cur }}{{ number_format($invoice->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($invoice->note))
            <div class="card">
                <div class="card-header"><h3 class="card-title">Order Note</h3></div>
                <div class="card-body">
                    <p class="mb-0 text-muted" style="font-size:14px; line-height:1.6;">{{ $invoice->note }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Customer & Status --}}
        <div class="right-col">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Customer Information</h3></div>
                <div class="card-body">
                    @if($invoice->customer)
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div style="width:48px; height:48px; border-radius:50%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; font-weight:800;">
                                {{ strtoupper(substr($invoice->customer->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $invoice->customer->first_name }} {{ $invoice->customer->last_name }}</div>
                                <div class="text-muted small">Customer ID: #{{ $invoice->customer->id }}</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label mb-1">Email Address</div>
                            <div class="info-value">{{ $invoice->customer->user->email ?? 'N/A' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label mb-1">Phone Number</div>
                            <div class="info-value">{{ $invoice->customer->user->phone ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="info-label mb-1">Shipping Address</div>
                            <div class="info-value" style="line-height:1.5;">{{ $invoice->customer->address ?? 'N/A' }}</div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-person-x" style="font-size:32px; color:var(--muted);"></i>
                            <p class="mt-2 text-muted">Walk-in Customer</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Order Status</h3></div>
                <div class="card-body p-0">
                    <div class="info-item">
                        <span class="info-label">Order ID</span>
                        <span class="info-value">#{{ $invoice->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Order Date</span>
                        <span class="info-value">{{ $invoice->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Assigned Staff</span>
                        <span class="info-value text-primary">{{ $order->assigned_staff_name ?? 'Not Assigned' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Payment Status</span>
                        <span class="info-value">
                            <span class="badge {{ $order && $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}" style="border-radius:4px;">
                                {{ strtoupper($order->payment_status ?? 'PAID') }}
                            </span>
                        </span>
                    </div>
                    <div class="info-item" style="border-bottom:none;">
                        <span class="info-label">Current Status</span>
                        <span class="info-value">
                            @php $status = $order->status ?? 'pending'; @endphp
                            <span class="status-badge badge-{{ $status }}">
                                {{ $status }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
