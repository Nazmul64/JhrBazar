@extends('admin.master')

@section('content')

@php
    $cur = $settings->default_currency ?? '৳';
@endphp

<style>
/* ════════════════════════════════════════════════════════════
   POS Sales History — Index
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

*, *::before, *::after { box-sizing: border-box; }

.sales-page { padding: 24px; background: var(--bg); min-height: 100vh; font-family: 'Segoe UI', system-ui, sans-serif; }

/* ── Page Header ── */
.sales-page-header {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 22px; flex-wrap: wrap; gap: 12px;
}
.sales-page-title { font-size: 20px; font-weight: 800; color: var(--text); margin: 0; }

.btn-back-pos {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; background: var(--white);
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; color: var(--muted);
    text-decoration: none; transition: all .15s;
}
.btn-back-pos:hover { background: #f1f5f9; color: var(--text); text-decoration: none; }

/* ── Summary Cards ── */
.summary-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}
.summary-card {
    background: var(--white);
    border-radius: var(--radius);
    padding: 18px 20px;
    box-shadow: var(--shadow);
    display: flex; align-items: center; gap: 14px;
}
.sum-icon {
    width: 46px; height: 46px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.sum-icon.blue    { background: #dbeafe; color: #2563eb; }
.sum-icon.green   { background: #dcfce7; color: #16a34a; }
.sum-icon.yellow  { background: #fef9c3; color: #ca8a04; }
.sum-icon.purple  { background: #ede9fe; color: #7c3aed; }
.sum-info { flex: 1; min-width: 0; }
.sum-value { font-size: 20px; font-weight: 800; color: var(--text); line-height: 1.2; }
.sum-label { font-size: 12px; color: var(--muted); margin-top: 2px; }

/* ── Filter Card ── */
.filter-card {
    background: var(--white); border-radius: var(--radius);
    padding: 16px 20px; box-shadow: var(--shadow);
    margin-bottom: 20px;
    display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
}
.filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px; }
.filter-label { font-size: 11.5px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .3px; }
.filter-input {
    height: 38px; border: 1.5px solid var(--border);
    border-radius: var(--radius-sm); padding: 0 12px;
    font-size: 13px; color: var(--text); background: var(--white);
    outline: none; transition: border-color .15s; width: 100%;
    font-family: inherit;
}
.filter-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(231,86,124,.08); }
select.filter-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7a99' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center;
    padding-right: 32px; cursor: pointer;
}
.filter-actions { display: flex; gap: 8px; align-items: flex-end; }
.btn-filter {
    height: 38px; padding: 0 20px; background: var(--accent);
    color: var(--white); border: none; border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px;
    font-family: inherit; transition: background .15s; white-space: nowrap;
}
.btn-filter:hover { background: var(--accent-dk); }
.btn-reset {
    height: 38px; padding: 0 16px;
    background: #f1f5f9; color: var(--muted);
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; cursor: pointer;
    text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    font-family: inherit; transition: background .15s; white-space: nowrap;
}
.btn-reset:hover { background: #e2e8f0; color: var(--text); text-decoration: none; }

/* ── Table Card ── */
.table-card {
    background: var(--white); border-radius: var(--radius);
    box-shadow: var(--shadow); overflow: hidden;
}
.table-card-top {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid var(--border);
    flex-wrap: wrap; gap: 10px;
}
.table-card-title { font-size: 15px; font-weight: 700; color: var(--text); margin: 0; }
.count-badge {
    background: #f3f4f6; color: var(--muted);
    border-radius: 20px; padding: 3px 10px;
    font-size: 12px; font-weight: 600;
}

.sales-table { width: 100%; border-collapse: collapse; }
.sales-table thead tr { background: #f8fafc; }
.sales-table thead th {
    padding: 11px 16px; text-align: left;
    font-size: 11.5px; font-weight: 700; color: var(--muted);
    white-space: nowrap; text-transform: uppercase; letter-spacing: .4px;
    border-bottom: 2px solid var(--border);
}
.sales-table tbody tr { border-bottom: 1px solid #f0f2f5; transition: background .12s; }
.sales-table tbody tr:last-child { border-bottom: none; }
.sales-table tbody tr:hover { background: #fafbff; }
.sales-table tbody td { padding: 13px 16px; font-size: 13px; color: var(--text); vertical-align: middle; }

/* Invoice # */
.inv-num {
    font-family: 'Courier New', monospace;
    font-weight: 700; color: var(--blue); font-size: 13px;
}

/* Customer cell */
.cust-cell { display: flex; flex-direction: column; gap: 2px; }
.cust-name { font-weight: 600; color: var(--text); font-size: 13px; }
.cust-phone { font-size: 11.5px; color: var(--muted); }

/* Items cell */
.items-cell {
    background: #f3f4f6; color: #374151;
    padding: 3px 8px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
    display: inline-block;
}

/* Amount */
.amt-cell { font-weight: 700; color: var(--text); }

/* Payment badge */
.pay-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 9px; border-radius: 5px;
    font-size: 11.5px; font-weight: 600;
    background: #f3f4f6; color: #374151;
}

/* Status badge */
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 12px; font-weight: 600; white-space: nowrap;
}
.status-badge::before {
    content: ''; width: 6px; height: 6px;
    border-radius: 50%; flex-shrink: 0;
}
.badge-completed  { background: #dcfce7; color: #15803d; }
.badge-completed::before  { background: #15803d; }
.badge-pending    { background: #fef9c3; color: #a16207; }
.badge-pending::before    { background: #a16207; }
.badge-processing { background: #dbeafe; color: #1d4ed8; }
.badge-processing::before { background: #1d4ed8; }
.badge-cancelled  { background: #fee2e2; color: #dc2626; }
.badge-cancelled::before  { background: #dc2626; }
.badge-draft      { background: #f3f4f6; color: #6b7280; }
.badge-draft::before      { background: #6b7280; }

/* Date cell */
.date-main { font-size: 13px; color: var(--text); font-weight: 500; }
.date-time  { font-size: 11px; color: var(--muted); margin-top: 1px; }

/* Action btns */
.action-cell { display: flex; align-items: center; gap: 8px; }
.btn-view {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; background: #dbeafe; color: #2563eb;
    border-radius: 6px; font-size: 12px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer;
    transition: background .15s; white-space: nowrap;
}
.btn-view:hover { background: #bfdbfe; color: #1d4ed8; text-decoration: none; }
.btn-print {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; background: #f3f4f6; color: #374151;
    border-radius: 6px; font-size: 12px; font-weight: 600;
    text-decoration: none; border: 1px solid #e5e7eb; cursor: pointer;
    transition: background .15s; white-space: nowrap;
}
.btn-print:hover { background: #e5e7eb; text-decoration: none; color: #374151; }

/* Empty state */
.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { font-size: 52px; color: #d1d5db; display: block; margin-bottom: 14px; }
.empty-state h4 { font-size: 16px; color: #374151; font-weight: 700; margin-bottom: 6px; }
.empty-state p  { font-size: 13.5px; color: var(--muted); margin: 0; }

/* Pagination area */
.pagi-area {
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 14px 20px; border-top: 1px solid var(--border);
    flex-wrap: wrap; gap: 10px;
}
.pagi-info { font-size: 13px; color: var(--muted); }

/* Alert */
.alert-ok {
    background: #ecfdf5; border: 1px solid #6ee7b7;
    color: #065f46; padding: 12px 16px; border-radius: 8px;
    margin-bottom: 16px; font-size: 14px; font-weight: 500;
}

/* Scrollbar */
.table-card::-webkit-scrollbar { width: 4px; }
.table-card::-webkit-scrollbar-thumb { background: #dde2e8; border-radius: 4px; }

@media (max-width: 900px) { .summary-row { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 560px) {
    .summary-row { grid-template-columns: 1fr; }
    .filter-group { min-width: 100%; }
    .table-card { overflow-x: auto; }
}
</style>

<div class="sales-page">

    {{-- ── Page Header ── --}}
    <div class="sales-page-header">
        <h2 class="sales-page-title">
            <i class="bi bi-receipt" style="color:var(--accent);margin-right:6px;"></i>
            POS Sales History
        </h2>
        <a href="{{ route('admin.pointofsalepos.index') }}" class="btn-back-pos">
            <i class="bi bi-display"></i> Back to POS
        </a>
    </div>

    @if(session('success'))
        <div class="alert-ok">{{ session('success') }}</div>
    @endif

    {{-- ── Summary Cards ── --}}
    <div class="summary-row">
        <div class="summary-card">
            <div class="sum-icon blue">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="sum-info">
                <div class="sum-value">{{ number_format($totalOrders ?? 0) }}</div>
                <div class="sum-label">Total Orders</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="sum-icon green">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="sum-info">
                <div class="sum-value">{{ $cur }}{{ number_format($totalSales ?? 0, 2) }}</div>
                <div class="sum-label">Total Revenue</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="sum-icon yellow">
                <i class="bi bi-calendar-day"></i>
            </div>
            <div class="sum-info">
                <div class="sum-value">{{ number_format($todayOrders ?? 0) }}</div>
                <div class="sum-label">Today's Orders</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="sum-icon purple">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="sum-info">
                <div class="sum-value">{{ $cur }}{{ number_format($todaySales ?? 0, 2) }}</div>
                <div class="sum-label">Today's Revenue</div>
            </div>
        </div>
    </div>

    {{-- ── Filter ── --}}
    <form method="GET" action="{{ route('admin.pointofsalepos.sales.index') }}">
        <div class="filter-card">

            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" name="search" class="filter-input"
                       placeholder="Invoice # or customer..."
                       value="{{ request('search') }}">
            </div>

            <div class="filter-group">
                <label class="filter-label">Payment Method</label>
                <select name="payment_method" class="filter-input">
                    <option value="">All Methods</option>
                    <option value="cash"   {{ request('payment_method') == 'cash'   ? 'selected' : '' }}>Cash</option>
                    <option value="card"   {{ request('payment_method') == 'card'   ? 'selected' : '' }}>Card</option>
                    <option value="mobile" {{ request('payment_method') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">From Date</label>
                <input type="date" name="date_from" class="filter-input"
                       value="{{ request('date_from') }}">
            </div>

            <div class="filter-group">
                <label class="filter-label">To Date</label>
                <input type="date" name="date_to" class="filter-input"
                       value="{{ request('date_to') }}">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('admin.pointofsalepos.sales.index') }}" class="btn-reset">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>

    {{-- ── Table ── --}}
    <div class="table-card">
        <div class="table-card-top">
            <div style="display:flex;align-items:center;gap:10px;">
                <h3 class="table-card-title">Sales List</h3>
                <span class="count-badge">{{ $invoices->total() }} records</span>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Sub Total</th>
                        <th>Discount</th>
                        <th>Grand Total</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $index => $invoice)
                        @php
                            $custUser = $invoice->customer?->user;
                            $custName = $invoice->customer
                                ? trim(($invoice->customer->first_name ?? '') . ' ' . ($invoice->customer->last_name ?? ''))
                                : ($custUser?->name ?? '');
                            if (!$custName) $custName = $custUser?->name ?? '';
                            $custPhone = $custUser?->phone ?? '';
                        @endphp
                        <tr>
                            {{-- SL --}}
                            <td style="color:var(--muted);font-size:12px;">
                                {{ $invoices->firstItem() + $index }}
                            </td>

                            {{-- Invoice # --}}
                            <td>
                                <span class="inv-num">#{{ $invoice->invoice_number }}</span>
                            </td>

                            {{-- Customer --}}
                            <td>
                                <div class="cust-cell">
                                    <span class="cust-name">{{ $custName ?: 'Walk-in Customer' }}</span>
                                    @if($custPhone)
                                        <span class="cust-phone">{{ $custPhone }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Items Count --}}
                            <td>
                                <span class="items-cell">
                                    {{ count($invoice->items ?? []) }} item{{ count($invoice->items ?? []) != 1 ? 's' : '' }}
                                </span>
                            </td>

                            {{-- Sub Total --}}
                            <td class="amt-cell">{{ $cur }}{{ number_format($invoice->sub_total ?? 0, 2) }}</td>

                            {{-- Discount --}}
                            <td>
                                @if(($invoice->discount ?? 0) > 0)
                                    <span style="color:var(--green-dk);font-weight:600;">
                                        −{{ $cur }}{{ number_format($invoice->discount, 2) }}
                                    </span>
                                @else
                                    <span style="color:var(--muted);">—</span>
                                @endif
                            </td>

                            {{-- Grand Total --}}
                            <td>
                                <span style="font-weight:800;font-size:14px;color:var(--text);">
                                    {{ $cur }}{{ number_format($invoice->grand_total ?? 0, 2) }}
                                </span>
                            </td>

                            {{-- Payment Method --}}
                            <td>
                                <span class="pay-badge">
                                    @if($invoice->payment_method === 'cash')
                                        <i class="bi bi-cash-stack" style="color:#16a34a;"></i>
                                    @elseif($invoice->payment_method === 'card')
                                        <i class="bi bi-credit-card" style="color:#2563eb;"></i>
                                    @else
                                        <i class="bi bi-phone" style="color:#7c3aed;"></i>
                                    @endif
                                    {{ ucfirst($invoice->payment_method ?? 'N/A') }}
                                </span>
                            </td>

                            {{-- Date --}}
                            <td>
                                <div class="date-main">{{ $invoice->created_at?->format('M d, Y') }}</div>
                                <div class="date-time">{{ $invoice->created_at?->format('h:i A') }}</div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="action-cell">
                                    <a href="{{ route('admin.pointofsalepos.sales.show', $invoice->id) }}"
                                       class="btn-view" title="View Details">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.pointofsalepos.invoice', $invoice->id) }}"
                                       class="btn-print" title="Print Invoice" target="_blank">
                                        <i class="bi bi-printer"></i> Print
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <span class="empty-icon bi bi-receipt"></span>
                                    <h4>No Sales Found</h4>
                                    <p>No POS sales records match your filter criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($invoices->hasPages())
            <div class="pagi-area">
                <div class="pagi-info">
                    Showing <strong>{{ $invoices->firstItem() }}</strong>
                    to <strong>{{ $invoices->lastItem() }}</strong>
                    of <strong>{{ $invoices->total() }}</strong> results
                </div>
                <div>
                    {{ $invoices->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

@endsection
