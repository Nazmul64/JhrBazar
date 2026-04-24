@extends('admin.master')

@section('content')

@php
function posShowFmtAmt($num) {
    $n = (float) $num;
    if ($n == floor($n)) return number_format($n, 0);
    $one = round($n, 1);
    if ($one == round($n, 2)) return number_format($n, 1);
    return number_format($n, 2);
}
$cur = $settings->default_currency ?? '৳';
function posShowFmt($num, $cur) {
    return $cur . posShowFmtAmt($num);
}
@endphp

<style>
/* ════════════════════════════════════════════════════════════
   POS Sales — Order Detail
   ════════════════════════════════════════════════════════════ */
:root {
    --accent:    #e7567c;
    --accent-dk: #c93f65;
    --blue:      #4361ee;
    --green:     #22c55e;
    --green-dk:  #16a34a;
    --warning:   #f59e0b;
    --text:      #1a1f36;
    --muted:     #6b7a99;
    --border:    #e4e9f2;
    --bg:        #f0f2f5;
    --white:     #ffffff;
    --radius:    8px;
    --radius-sm: 5px;
    --shadow:    0 1px 4px rgba(0,0,0,.07);
}

.od-page { padding: 24px; background: var(--bg); min-height: 100vh; }

/* Page Header */
.od-page-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 22px; flex-wrap: wrap; gap: 12px;
}
.od-page-title { font-size: 22px; font-weight: 800; color: var(--text); margin: 0; }
.od-header-btns { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

.btn-hdr {
    height: 40px; padding: 0 16px; border: none; border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit;
    display: inline-flex; align-items: center; gap: 7px; transition: opacity .15s;
    text-decoration: none;
}
.btn-hdr:hover { opacity: .88; }
.btn-barcode  { background: #4361ee; color: #fff; }
.btn-slip     { background: #22c55e; color: #fff; }
.btn-download { background: var(--accent); color: #fff; }
.btn-back     { background: #f1f5f9; color: var(--muted); border: 1px solid var(--border); }
.btn-back:hover { background: #e2e8f0; color: var(--text); }

/* Layout Grid */
.od-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 20px;
    align-items: start;
}

/* Card */
.od-card {
    background: var(--white); border-radius: var(--radius);
    box-shadow: var(--shadow); overflow: hidden; margin-bottom: 20px;
}
.od-card-head {
    padding: 14px 20px; border-bottom: 1px solid var(--border);
}
.od-card-title { font-size: 14px; font-weight: 700; color: var(--text); margin: 0; }
.od-card-body  { padding: 20px; }

/* Order meta info */
.od-meta-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 0; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden;
    margin-bottom: 20px;
}
.od-meta-item {
    padding: 12px 16px;
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
}
.od-meta-item:nth-child(3n)  { border-right: none; }
.od-meta-item:nth-last-child(-n+3) { border-bottom: none; }
.od-meta-label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px; }
.od-meta-value { font-size: 13px; font-weight: 600; color: var(--text); }

/* Product Table */
.od-table { width: 100%; border-collapse: collapse; }
.od-table thead tr { background: #f8fafc; }
.od-table thead th {
    padding: 11px 14px; font-size: 12px; font-weight: 700;
    color: var(--muted); text-align: left; white-space: nowrap;
    text-transform: uppercase; letter-spacing: .4px;
    border-bottom: 2px solid var(--border);
}
.od-table thead th.center { text-align: center; }
.od-table thead th.right  { text-align: right; }
.od-table tbody tr { border-bottom: 1px solid #f0f2f5; }
.od-table tbody tr:last-child { border-bottom: none; }
.od-table tbody tr:hover { background: #fafbff; }
.od-table tbody td { padding: 13px 14px; font-size: 13px; color: var(--text); vertical-align: middle; }
.od-table tbody td.center { text-align: center; }
.od-table tbody td.right  { text-align: right; font-weight: 600; }

/* Product cell */
.prod-cell { display: flex; align-items: center; gap: 10px; }
.prod-img  { width: 40px; height: 40px; object-fit: cover; border-radius: 6px; flex-shrink: 0; background: #f1f5f9; }
.prod-img-ph { width: 40px; height: 40px; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.prod-name { font-size: 13px; font-weight: 600; color: var(--text); }
.prod-sku  { font-size: 11px; color: var(--blue); font-family: 'Courier New', monospace; margin-top: 2px; }

/* Totals */
.od-totals { display: flex; justify-content: flex-end; padding: 16px 0 0; }
.od-totals-inner { min-width: 280px; }
.totals-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 7px 0; font-size: 13px; border-bottom: 1px solid #f1f5f9;
}
.totals-row:last-child { border-bottom: none; }
.totals-row .tl { color: var(--muted); }
.totals-row .tv { color: var(--text); font-weight: 500; }
.totals-row.discount .tv { color: var(--green-dk); }
.totals-row.grand {
    border-top: 2px solid var(--border); margin-top: 4px; padding-top: 12px; border-bottom: none;
}
.totals-row.grand .tl,
.totals-row.grand .tv { font-size: 15px; font-weight: 800; color: var(--text); }

/* Customer Info */
.info-row { display: flex; gap: 8px; padding: 6px 0; font-size: 13px; }
.info-label { font-weight: 500; color: var(--muted); min-width: 70px; flex-shrink: 0; }
.info-value { color: var(--text); }

/* Right Sidebar */
.od-sidebar { display: flex; flex-direction: column; gap: 20px; }

.sid-section { font-size: 16px; font-weight: 800; color: var(--text); margin: 0 0 16px; }
.sid-card { background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); padding: 20px; }
.sid-label { font-size: 12px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 10px; }

/* Status select */
.status-select-wrap { margin-bottom: 16px; }
.status-select {
    width: 100%; height: 40px; border: 1.5px solid var(--border);
    border-radius: var(--radius-sm); padding: 0 32px 0 12px;
    font-size: 13px; font-weight: 600; color: var(--text);
    background: #f9fafb; outline: none; cursor: pointer; font-family: inherit;
    transition: border-color .15s; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7a99' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center;
}
.status-select:focus { border-color: var(--blue); }
.btn-update-status {
    width: 100%; height: 38px; background: var(--blue); color: #fff;
    border: none; border-radius: var(--radius-sm); font-size: 13px;
    font-weight: 600; cursor: pointer; font-family: inherit;
    display: flex; align-items: center; justify-content: center;
    gap: 6px; transition: opacity .15s;
}
.btn-update-status:hover { opacity: .88; }

/* Payment toggle */
.pay-toggle-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 0; border-bottom: 1px solid var(--border); margin-bottom: 10px;
}
.pay-toggle-label { font-size: 13px; font-weight: 600; color: var(--text); }
.pay-toggle {
    position: relative; width: 44px; height: 24px;
    background: var(--green); border-radius: 24px;
    display: flex; align-items: center; padding: 3px; cursor: pointer;
    transition: background .2s;
}
.pay-toggle::after {
    content: ''; width: 18px; height: 18px; background: #fff;
    border-radius: 50%; position: absolute; right: 3px; transition: all .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.pay-status-label {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 700;
    background: #f0fdf4; color: var(--green-dk);
    padding: 3px 10px; border-radius: 20px;
}

/* Shipping address */
.addr-item { display: flex; gap: 8px; padding: 5px 0; font-size: 13px; border-bottom: 1px solid #f8f9fa; }
.addr-item:last-child { border-bottom: none; }
.addr-label { color: var(--muted); min-width: 90px; font-weight: 500; flex-shrink: 0; }
.addr-value { color: var(--text); }
.addr-na    { color: #c8d0db; font-style: italic; }

/* Barcode Scanner Modal */
.scanner-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,.6);
    z-index: 50000; display: flex; align-items: center;
    justify-content: center; opacity: 0; pointer-events: none;
    transition: opacity .2s; padding: 16px;
}
.scanner-overlay.show { opacity: 1; pointer-events: all; }
.scanner-modal {
    background: var(--white); border-radius: 14px;
    width: 500px; max-width: 100%; max-height: 90vh; overflow-y: auto;
    box-shadow: 0 24px 64px rgba(0,0,0,.25);
    transform: scale(.95) translateY(10px); transition: transform .2s;
}
.scanner-overlay.show .scanner-modal { transform: scale(1) translateY(0); }
.scanner-head {
    padding: 16px 20px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.scanner-head h5 { font-size: 15px; font-weight: 700; color: var(--text); margin: 0; }
.scanner-close { background: none; border: none; font-size: 22px; cursor: pointer; color: #94a3b8; line-height: 1; }
.scanner-close:hover { color: var(--accent); }
.scanner-body { padding: 20px; }
.scan-input-wrap { position: relative; margin-bottom: 14px; }
.scan-prefix { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--accent); font-size: 16px; pointer-events: none; }
#scanBarcodeInput {
    width: 100%; height: 50px; border: 2px solid var(--accent);
    border-radius: var(--radius-sm); padding: 0 14px 0 40px;
    font-size: 15px; font-weight: 600; outline: none; font-family: 'Courier New', monospace;
    color: var(--text); letter-spacing: .5px; transition: box-shadow .15s;
}
#scanBarcodeInput:focus { box-shadow: 0 0 0 3px rgba(231,86,124,.12); }
.scan-hint {
    font-size: 12px; color: var(--muted); margin-bottom: 14px;
    display: flex; align-items: center; gap: 5px;
}
.scan-hint-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--green); animation: blink 1s infinite; flex-shrink: 0; }
@keyframes blink { 0%,100%{opacity:1;} 50%{opacity:.3;} }
.scan-results { margin-top: 16px; }
.scan-log-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border: 1px solid var(--border);
    border-radius: var(--radius-sm); margin-bottom: 8px;
    background: #f8fafc;
}
.scan-log-item.found { border-color: #bbf7d0; background: #f0fdf4; }
.scan-log-item.not-found { border-color: #fecdd3; background: #fff1f2; }
.scan-log-item i { font-size: 18px; flex-shrink: 0; }
.scan-log-item .sli-info { flex: 1; min-width: 0; }
.scan-log-item .sli-name { font-size: 13px; font-weight: 600; color: var(--text); }
.scan-log-item .sli-bc   { font-size: 11px; color: var(--blue); font-family: 'Courier New', monospace; }
.scan-footer { display: flex; gap: 10px; margin-top: 20px; }
.btn-scan-close {
    flex: 1; height: 44px; background: #f1f5f9; color: var(--muted);
    border: 1px solid var(--border); border-radius: var(--radius-sm);
    font-size: 13px; cursor: pointer; font-family: inherit;
}
.btn-scan-confirm {
    flex: 1; height: 44px; background: var(--accent); color: #fff;
    border: none; border-radius: var(--radius-sm); font-size: 13px;
    font-weight: 700; cursor: pointer; font-family: inherit;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-scan-confirm:hover { background: var(--accent-dk); }

/* Toast */
.toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
.pos-toast {
    background: #1e293b; color: #fff; border-radius: var(--radius);
    padding: 12px 18px; font-size: 13px; box-shadow: 0 8px 24px rgba(0,0,0,.2);
    display: flex; align-items: center; gap: 10px; min-width: 240px;
    animation: tIn .3s ease;
}
.pos-toast.t-success { background: #15803d; }
.pos-toast.t-error   { background: #be123c; }
.pos-toast.t-warning { background: #b45309; }
@keyframes tIn  { from{opacity:0;transform:translateX(40px);}to{opacity:1;transform:translateX(0);} }
@keyframes tOut { from{opacity:1;}to{opacity:0;transform:translateX(40px);} }

@media (max-width: 1024px) {
    .od-grid { grid-template-columns: 1fr; }
    .od-sidebar { order: -1; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .sid-section { grid-column: 1/-1; }
}
@media (max-width: 600px) {
    .od-page { padding: 14px; }
    .od-meta-grid { grid-template-columns: 1fr 1fr; }
    .od-meta-item:nth-child(2n) { border-right: none; }
    .od-meta-item:nth-child(3n) { border-right: 1px solid var(--border); }
    .od-sidebar { grid-template-columns: 1fr; }
    .od-header-btns { width: 100%; }
    .btn-hdr { flex: 1; justify-content: center; }
}
</style>

<div class="od-page">

    {{-- Header --}}
    <div class="od-page-header">
        <h1 class="od-page-title">Order Details</h1>
        <div class="od-header-btns">
            <button class="btn-hdr btn-barcode" onclick="openScanner()">
                <i class="bi bi-upc-scan"></i> Attach Product Barcode
            </button>
            <a href="{{ route('admin.pointofsalepos.invoice', $invoice->id) }}"
               target="_blank" class="btn-hdr btn-slip">
                <i class="bi bi-file-earmark-text"></i> Payment Slip
            </a>
            <a href="{{ route('admin.pointofsalepos.invoice', $invoice->id) }}"
               target="_blank" class="btn-hdr btn-download" id="btnDownload">
                <i class="bi bi-download"></i> Download Invoice
            </a>
        </div>
    </div>

    <div class="od-grid">

        {{-- LEFT: Main content --}}
        <div>

            {{-- Order Meta --}}
            <div class="od-card">
                <div class="od-card-body">
                    @php
                        $orderStatus = $invoice->order?->status ?? 'completed';
                    @endphp
                    <div class="od-meta-grid">
                        <div class="od-meta-item">
                            <div class="od-meta-label">Order Id</div>
                            <div class="od-meta-value" style="color:var(--blue);font-family:'Courier New',monospace;">#{{ $invoice->invoice_number }}</div>
                        </div>
                        <div class="od-meta-item">
                            <div class="od-meta-label">Payment Status</div>
                            <div class="od-meta-value">
                                <span style="color:var(--green-dk);font-weight:700;">Paid</span>
                            </div>
                        </div>
                        <div class="od-meta-item">
                            <div class="od-meta-label">Payment Method</div>
                            <div class="od-meta-value">{{ $invoice->payment_method_label }}</div>
                        </div>
                        <div class="od-meta-item">
                            <div class="od-meta-label">Order Status</div>
                            <div class="od-meta-value" style="color:var(--blue);font-weight:700;">
                                {{ ucfirst($orderStatus === 'completed' ? 'Delivered' : $orderStatus) }}
                            </div>
                        </div>
                        <div class="od-meta-item">
                            <div class="od-meta-label">Order Date</div>
                            <div class="od-meta-value">{{ $invoice->created_at->format('M d, Y') }}</div>
                        </div>
                        <div class="od-meta-item">
                            <div class="od-meta-label">Delivery Date</div>
                            <div class="od-meta-value" style="color:var(--muted);">—</div>
                        </div>
                    </div>

                    {{-- Product Table --}}
                    <div style="overflow-x:auto;">
                        <table class="od-table">
                            <thead>
                                <tr>
                                    <th style="width:42px;">SL</th>
                                    <th>Product</th>
                                    <th>Shop</th>
                                    <th class="center">Quantity</th>
                                    <th class="center">Size</th>
                                    <th class="center">Color</th>
                                    <th class="right">Price</th>
                                    <th class="right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $index => $item)
                                @php
                                    $itemPrice  = (float)($item['price'] ?? 0);
                                    $lineTotal  = (float)($item['line_total'] ?? 0);
                                    $bcDisplay  = !empty($item['barcode']) ? $item['barcode'] : ($item['sku'] ?? '');
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="prod-cell">
                                            @if(!empty($item['thumbnail']))
                                                <img class="prod-img"
                                                     src="{{ asset($item['thumbnail']) }}"
                                                     alt="{{ $item['name'] }}"
                                                     onerror="this.outerHTML='<div class=\'prod-img-ph\'>📦</div>'">
                                            @else
                                                <div class="prod-img-ph">📦</div>
                                            @endif
                                            <div>
                                                <div class="prod-name">{{ $item['name'] }}</div>
                                                @if($bcDisplay)
                                                    <div class="prod-sku">{{ $bcDisplay }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color:var(--muted);font-size:12px;">
                                        {{ $settings->website_name ?? config('app.name') }}
                                    </td>
                                    <td class="center">{{ $item['qty'] }}</td>
                                    <td class="center">{{ $item['size'] ?? '—' }}</td>
                                    <td class="center">{{ $item['color'] ?? '—' }}</td>
                                    <td class="right">{{ posShowFmt($itemPrice, $cur) }}</td>
                                    <td class="right">{{ posShowFmt($lineTotal, $cur) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="od-totals">
                        <div class="od-totals-inner">
                            <div class="totals-row">
                                <span class="tl">Sub Total</span>
                                <span class="tv">{{ posShowFmt($invoice->sub_total, $cur) }}</span>
                            </div>

                            @if($invoice->discount > 0)
                            <div class="totals-row discount">
                                <span class="tl">Coupon Discount</span>
                                <span class="tv">−{{ posShowFmt($invoice->discount, $cur) }}</span>
                            </div>
                            @else
                            <div class="totals-row">
                                <span class="tl">Coupon Discount</span>
                                <span class="tv">{{ posShowFmt(0, $cur) }}</span>
                            </div>
                            @endif

                            <div class="totals-row">
                                <span class="tl">Delivery Charge</span>
                                <span class="tv">{{ posShowFmt($invoice->delivery_charge ?? 0, $cur) }}</span>
                            </div>

                            @if($invoice->tax_breakdown && count($invoice->tax_breakdown))
                                @foreach($invoice->tax_breakdown as $tax)
                                <div class="totals-row">
                                    <span class="tl">{{ $tax['name'] }} ({{ $tax['rate'] }}%)</span>
                                    <span class="tv">{{ posShowFmt($tax['amount'], $cur) }}</span>
                                </div>
                                @endforeach
                            @else
                            <div class="totals-row">
                                <span class="tl">VAT &amp; Tax</span>
                                <span class="tv">{{ posShowFmt($invoice->tax_amount, $cur) }}</span>
                            </div>
                            @endif

                            <div class="totals-row grand">
                                <span class="tl">Grand Total</span>
                                <span class="tv">{{ posShowFmt($invoice->grand_total, $cur) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer Info --}}
            @php
                $customer    = $invoice->customer;
                $custUser    = $customer?->user;
                $custName    = $customer
                    ? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''))
                    : ($custUser?->name ?? '');
                $custPhone   = $custUser?->phone ?? '';
                $custEmail   = $custUser?->email ?? '';
                $custAddress = $customer?->address ?? '';
            @endphp
            <div class="od-card">
                <div class="od-card-head">
                    <div class="od-card-title"><i class="bi bi-person-circle" style="color:var(--accent);margin-right:6px;"></i>Customer Info</div>
                </div>
                <div class="od-card-body">
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $custName ?: 'Walk-in Customer' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $custPhone ?: '—' }}</span>
                    </div>
                    @if($custEmail)
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $custEmail }}</span>
                    </div>
                    @endif
                    @if($custAddress)
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value">{{ $custAddress }}</span>
                    </div>
                    @endif
                    @if($customer?->date_of_birth)
                    <div class="info-row">
                        <span class="info-label">DOB:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($invoice->note)
                    <div class="info-row">
                        <span class="info-label">Note:</span>
                        <span class="info-value" style="color:var(--warning);">{{ $invoice->note }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT: Sidebar --}}
        <div class="od-sidebar">
            <div class="sid-section">Order &amp; Shipping Info</div>

            {{-- Order Status --}}
            <div class="sid-card">
                <div class="sid-label">Change Order Status</div>
                <div class="status-select-wrap">
                    <select class="status-select" id="orderStatusSelect">
                        @php $currentStatus = $invoice->order?->status ?? 'completed'; @endphp
                        <option value="completed"  {{ $currentStatus === 'completed'  ? 'selected' : '' }}>Delivered</option>
                        <option value="draft"      {{ $currentStatus === 'draft'      ? 'selected' : '' }}>Draft</option>
                        <option value="cancelled"  {{ $currentStatus === 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <button class="btn-update-status" onclick="updateStatus()">
                    <i class="bi bi-check-circle"></i> Update Status
                </button>

                <div style="height:16px;"></div>
                <div class="sid-label">Payment Status</div>
                <div class="pay-toggle-row">
                    <span class="pay-toggle-label">Status</span>
                    <div class="pay-toggle" title="Paid"></div>
                </div>
                <span class="pay-status-label">
                    <i class="bi bi-check-circle-fill"></i> Paid
                </span>
            </div>

            {{-- Shipping Address --}}
            <div class="sid-card">
                <div class="sid-label">Shipping Address</div>
                <div class="addr-item">
                    <span class="addr-label">Name:</span>
                    <span class="addr-value">{{ $custName ?: '—' }}</span>
                </div>
                <div class="addr-item">
                    <span class="addr-label">Phone:</span>
                    <span class="addr-value">{{ $custPhone ?: '—' }}</span>
                </div>
                <div class="addr-item">
                    <span class="addr-label">Address Type:</span>
                    <span class="addr-na">—</span>
                </div>
                <div class="addr-item">
                    <span class="addr-label">Area:</span>
                    <span class="addr-na">N/A</span>
                </div>
                <div class="addr-item">
                    <span class="addr-label">Address Line:</span>
                    <span class="addr-value">{{ $custAddress ?: '—' }}</span>
                </div>
            </div>

            {{-- Invoice Summary --}}
            <div class="sid-card">
                <div class="sid-label">Invoice Summary</div>
                <div class="addr-item">
                    <span class="addr-label">Invoice #</span>
                    <span class="addr-value" style="color:var(--blue);font-family:'Courier New',monospace;font-weight:700;">#{{ $invoice->invoice_number }}</span>
                </div>
                <div class="addr-item">
                    <span class="addr-label">Date</span>
                    <span class="addr-value">{{ $invoice->created_at->format('d M Y') }}</span>
                </div>
                <div class="addr-item">
                    <span class="addr-label">Items</span>
                    <span class="addr-value">{{ count($invoice->items) }} product{{ count($invoice->items) !== 1 ? 's' : '' }}</span>
                </div>
                <div class="addr-item">
                    <span class="addr-label">Received</span>
                    <span class="addr-value" style="color:var(--green-dk);font-weight:700;">{{ posShowFmt($invoice->received_amount, $cur) }}</span>
                </div>
                @if($invoice->change_amount > 0)
                <div class="addr-item">
                    <span class="addr-label">Change</span>
                    <span class="addr-value">{{ posShowFmt($invoice->change_amount, $cur) }}</span>
                </div>
                @endif
                <div class="addr-item">
                    <span class="addr-label">Grand Total</span>
                    <span class="addr-value" style="font-weight:800;font-size:15px;">{{ posShowFmt($invoice->grand_total, $cur) }}</span>
                </div>
            </div>

            {{-- Back button --}}
            <a href="{{ route('admin.pointofsalepos.sales.index') }}" class="btn-hdr btn-back" style="justify-content:center;width:100%;text-decoration:none;">
                <i class="bi bi-arrow-left"></i> Back to Sales
            </a>
        </div>

    </div>
</div>

{{-- ══ BARCODE SCANNER MODAL ══ --}}
<div class="scanner-overlay" id="scannerOverlay" onclick="closeScannerBg(event)">
    <div class="scanner-modal">
        <div class="scanner-head">
            <h5><i class="bi bi-upc-scan" style="color:var(--accent);"></i>&nbsp; Enter Barcode Manually / Scan Barcode</h5>
            <button class="scanner-close" onclick="closeScanner()">&times;</button>
        </div>
        <div class="scanner-body">
            <div class="scan-hint">
                <span class="scan-hint-dot"></span>
                Type barcode and press Enter, or use a hardware scanner
            </div>
            <div class="scan-input-wrap">
                <i class="bi bi-upc scan-prefix"></i>
                <input type="text" id="scanBarcodeInput"
                       placeholder="Type barcode and press Enter"
                       autocomplete="off" autocorrect="off" spellcheck="false">
            </div>
            <div class="scan-results" id="scanResults"></div>
            <div class="scan-footer">
                <button class="btn-scan-close" onclick="closeScanner()">Close</button>
                <button class="btn-scan-confirm" onclick="confirmScan()">
                    <i class="bi bi-check-circle"></i> Confirm Submit
                </button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
'use strict';

const PRODUCT_LOOKUP_URL = '{{ route("admin.pointofsalepos.products") }}';
const UPDATE_STATUS_URL  = '{{ route("admin.pointofsalepos.sales.status", $invoice->id) }}';
const CSRF               = '{{ csrf_token() }}';
const CURRENCY           = '{{ $cur }}';

/* ── Toast ── */
function showToast(msg, type='info', ms=3000) {
    const icons = { success:'check-circle-fill', error:'x-circle-fill', warning:'exclamation-triangle-fill', info:'info-circle-fill' };
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = `pos-toast t-${type}`;
    t.innerHTML = `<i class="bi bi-${icons[type]||icons.info}"></i><span>${msg}</span>`;
    c.appendChild(t);
    setTimeout(() => {
        t.style.animation = 'tOut .3s ease forwards';
        t.addEventListener('animationend', () => t.remove(), { once: true });
    }, ms);
}

/* ── Update Status ── */
function updateStatus() {
    const status = document.getElementById('orderStatusSelect').value;
    fetch(UPDATE_STATUS_URL, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ status }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) showToast('Status updated successfully!', 'success');
        else showToast('Update failed.', 'error');
    })
    .catch(() => showToast('Network error.', 'error'));
}

/* ── Download Invoice ── */
document.getElementById('btnDownload').addEventListener('click', function(e) {
    e.preventDefault();
    const url = this.href;
    showToast('Opening invoice…', 'info', 2000);
    window.open(url, '_blank');
});

/* ── Scanner ── */
let scanLog = [];
let scanTimer = null;

function openScanner() {
    document.getElementById('scannerOverlay').classList.add('show');
    document.getElementById('scanResults').innerHTML = '';
    document.getElementById('scanBarcodeInput').value = '';
    scanLog = [];
    setTimeout(() => document.getElementById('scanBarcodeInput').focus(), 100);
}
function closeScanner() {
    document.getElementById('scannerOverlay').classList.remove('show');
}
function closeScannerBg(e) {
    if (e.target === document.getElementById('scannerOverlay')) closeScanner();
}
function confirmScan() {
    if (!scanLog.length) { showToast('No items scanned yet.', 'warning'); return; }
    const found = scanLog.filter(s => s.found);
    showToast(`${found.length} item${found.length!==1?'s':''} confirmed.`, 'success', 2500);
    closeScanner();
}

async function searchBarcode() {
    const input = document.getElementById('scanBarcodeInput');
    const code  = input.value.trim();
    if (!code) return;

    try {
        const res  = await fetch(PRODUCT_LOOKUP_URL + '?' + new URLSearchParams({ search: code, page: 1 }), {
            headers: { Accept: 'application/json' }
        });
        const data = await res.json();
        const products = data.data || [];

        const match = products.find(p =>
            (p.barcode && String(p.barcode).trim().toLowerCase() === code.toLowerCase()) ||
            (p.sku     && String(p.sku).trim().toLowerCase()     === code.toLowerCase())
        ) || (products.length ? products[0] : null);

        const resultsEl = document.getElementById('scanResults');
        const thumb = match?.thumbnail ? '/' + match.thumbnail : null;

        if (match) {
            const price = parseFloat(match.discount_price) > 0 ? match.discount_price : match.selling_price;
            const logItem = document.createElement('div');
            logItem.className = 'scan-log-item found';
            logItem.innerHTML = `
                <i class="bi bi-check-circle-fill" style="color:var(--green-dk);"></i>
                <div class="sli-info">
                    <div class="sli-name">${escHtml(match.name)}</div>
                    <div class="sli-bc">${escHtml(code)} | ${CURRENCY}${parseFloat(price).toFixed(2)} | Stock: ${match.stock_quantity}</div>
                </div>`;
            resultsEl.prepend(logItem);
            scanLog.unshift({ name: match.name, code, found: true });
            showToast(`Found: ${match.name}`, 'success', 2000);
        } else {
            const logItem = document.createElement('div');
            logItem.className = 'scan-log-item not-found';
            logItem.innerHTML = `
                <i class="bi bi-x-circle-fill" style="color:#be123c;"></i>
                <div class="sli-info">
                    <div class="sli-name" style="color:#be123c;">Not found</div>
                    <div class="sli-bc">${escHtml(code)}</div>
                </div>`;
            resultsEl.prepend(logItem);
            scanLog.unshift({ name: 'Not found', code, found: false });
            showToast(`No product: ${code}`, 'error', 2500);
        }
        input.value = '';
        input.focus();
    } catch (err) {
        showToast('Network error.', 'error');
    }
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

document.getElementById('scanBarcodeInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); searchBarcode(); }
});
document.getElementById('scanBarcodeInput').addEventListener('input', function() {
    clearTimeout(scanTimer);
    const val = this.value.trim();
    if (val.length >= 6 && !val.includes(' ')) {
        scanTimer = setTimeout(searchBarcode, 400);
    }
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeScanner(); });
</script>

@endsection
