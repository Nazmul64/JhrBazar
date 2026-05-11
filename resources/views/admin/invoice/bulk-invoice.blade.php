<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Invoices</title>

    @php
    /**
     * Smart money formatter — removes trailing zeros:
     */
    if (!function_exists('fmtAmt')) {
        function fmtAmt($num) {
            $n = (float) $num;
            if ($n == floor($n)) {
                return number_format($n, 0);
            }
            $one = round($n, 1);
            if ($one == round($n, 2)) {
                return number_format($n, 1);
            }
            return number_format($n, 2);
        }
    }
    $cur = $settings->default_currency ?? '৳';
    if (!function_exists('fmtMoney')) {
        function fmtMoney($num, $cur) {
            return $cur . fmtAmt($num);
        }
    }
    @endphp

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent:    #e7567c;
            --accent-dk: #c93f65;
            --blue:      #3a4edc;
            --green:     #16a34a;
            --text:      #1e293b;
            --muted:     #64748b;
            --border:    #e2e8f0;
            --bg:        #f5f6fa;
            --white:     #ffffff;
            --radius:    8px;
            --radius-sm: 5px;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
        }

        /* ════════ PRINT TOOLBAR ════════ */
        .print-bar {
            background: #1e293b; padding: 12px 32px;
            display: flex; align-items: center;
            justify-content: space-between; gap: 12px;
            position: sticky; top: 0; z-index: 100;
        }
        .print-bar-left { color: #94a3b8; font-size: 13px; }
        .print-bar-left strong { color: #fff; }
        .print-bar-btns { display: flex; gap: 10px; align-items: center; }

        .btn-print {
            height: 36px; padding: 0 18px;
            background: var(--accent); color: #fff; border: none;
            border-radius: 6px; font-size: 13px; font-weight: 700;
            cursor: pointer; font-family: inherit;
        }
        .btn-print:hover { background: var(--accent-dk); }

        /* ════════ INVOICE WRAPPER ════════ */
        .invoice-wrap {
            max-width: 860px; margin: 30px auto;
            background: var(--white); border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10); overflow: hidden;
            page-break-after: always;
        }

        /* ════════ HEADER ════════ */
        .inv-header {
            padding: 28px 36px 22px;
            display: flex; justify-content: space-between;
            align-items: flex-start; border-bottom: 1px solid var(--border); gap: 24px;
        }
        .inv-brand { display: flex; align-items: flex-start; gap: 14px; }
        .inv-logo { width: 64px; height: 64px; object-fit: contain; border-radius: 10px; flex-shrink: 0; }
        .inv-logo-placeholder {
            width: 64px; height: 64px; border-radius: 10px; flex-shrink: 0;
            background: #fff3f6; display: flex; align-items: center; justify-content: center;
            font-size: 28px; font-weight: 800; color: var(--accent); border: 2px solid #fde0ea;
        }
        .inv-brand-info { display: flex; flex-direction: column; gap: 3px; padding-top: 4px; }
        .inv-brand-name { font-size: 20px; font-weight: 800; color: var(--text); line-height: 1.2; }
        .inv-brand-meta { font-size: 12px; color: var(--muted); margin-top: 1px; }

        .inv-right { text-align: right; flex-shrink: 0; }
        .inv-right-label { font-size: 12px; font-weight: 600; color: var(--muted); }
        .inv-right-addr  { font-size: 12px; color: var(--muted); margin-top: 2px; line-height: 1.6; }
        .inv-total-badge { text-align: right; margin-top: 10px; }
        .inv-total-badge .tbl { font-size: 13px; color: var(--muted); margin-bottom: 2px; }
        .inv-total-badge .tba { font-size: 28px; font-weight: 800; color: var(--text); }

        .qr-placeholder {
            width: 64px; height: 64px; border: 1px solid var(--border);
            border-radius: 6px; margin-top: 10px; margin-left: auto;
            display: flex; align-items: center; justify-content: center; overflow: hidden;
        }
        .qr-placeholder img { width: 100%; height: 100%; object-fit: cover; }

        /* ════════ BILL TO ════════ */
        .inv-bill { padding: 16px 36px; border-bottom: 1px solid var(--border); }
        .bill-row { display: flex; align-items: baseline; gap: 8px; font-size: 13px; padding: 3px 0; }
        .bill-row .bl { font-weight: 500; color: var(--muted); min-width: 65px; }
        .bill-row .bv { color: var(--text); }

        /* ════════ INFO BAR ════════ */
        .inv-info-bar {
            display: grid; grid-template-columns: repeat(4, 1fr);
            padding: 14px 36px; border-bottom: 1px solid var(--border); gap: 16px;
        }
        .info-label { font-size: 12px; font-weight: 700; color: var(--text); margin-bottom: 3px; }
        .info-value { font-size: 13px; color: var(--muted); }

        /* ════════ ITEMS TABLE ════════ */
        .inv-table-wrap { padding: 0; overflow-x: auto; }
        .inv-table { width: 100%; border-collapse: collapse; }
        .inv-table thead tr { background: var(--blue); color: #fff; }
        .inv-table thead th {
            padding: 12px 16px; font-size: 13px; font-weight: 600;
            text-align: left; white-space: nowrap;
        }
        .inv-table thead th.center { text-align: center; }
        .inv-table thead th.right  { text-align: right; }

        .inv-table tbody tr { border-bottom: 1px solid #f1f5f9; }
        .inv-table tbody td { padding: 14px 16px; font-size: 13px; vertical-align: middle; }
        .inv-table tbody td.center { text-align: center; }
        .inv-table tbody td.right  { text-align: right; font-weight: 600; }

        .item-cell { display: flex; align-items: center; gap: 12px; }
        .item-img { width: 46px; height: 46px; object-fit: cover; border-radius: 6px; flex-shrink: 0; background: #f1f5f9; }
        .item-name { font-size: 13px; font-weight: 600; color: var(--text); }

        /* ════════ TOTALS ════════ */
        .inv-totals { display: flex; justify-content: flex-end; padding: 16px 36px 0; }
        .totals-table { min-width: 300px; }
        .totals-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 7px 0; font-size: 13px; border-bottom: 1px solid #f1f5f9;
        }
        .totals-row.final {
            border-top: 1px solid var(--border); margin-top: 2px; padding-top: 10px;
        }
        .totals-row.final .tl,
        .totals-row.final .tv { color: var(--text); font-size: 14px; font-weight: 700; }

        /* ════════ FOOTER ════════ */
        .inv-footer {
            padding: 24px 36px 28px; margin-top: 16px;
            display: flex; justify-content: space-between;
            align-items: flex-end; border-top: 1px solid var(--border);
        }
        .inv-footer-thanks { font-size: 13px; color: var(--muted); }

        @media print {
            body { background: #fff !important; }
            .print-bar { display: none !important; }
            .invoice-wrap { margin: 0; border-radius: 0; box-shadow: none; border-bottom: 1px solid #eee; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>

<div class="print-bar">
    <div class="print-bar-left">
        Bulk Invoices (<strong>{{ $invoices->count() }} Orders</strong>)
    </div>
    <div class="print-bar-btns">
        <button class="btn-print" onclick="window.print()">🖨 Print All Invoices</button>
    </div>
</div>

@foreach($invoices as $invoice)
<div class="invoice-wrap">
    {{-- HEADER --}}
    <div class="inv-header">
        <div class="inv-brand">
            @if($settings && $settings->logo)
                <img class="inv-logo" src="{{ asset($settings->logo) }}" alt="Logo">
            @else
                <div class="inv-logo-placeholder">{{ strtoupper(substr($settings->website_name ?? 'S', 0, 1)) }}</div>
            @endif

            <div class="inv-brand-info">
                <div class="inv-brand-name">{{ $settings->website_name ?? config('app.name') }}</div>
                <div class="inv-brand-meta">{{ $settings->website_url }}</div>
                <div class="inv-brand-meta">{{ $settings->email_address }}</div>
            </div>
        </div>

        <div class="inv-right">
            <div class="inv-right-label">Business Address</div>
            <div class="inv-right-addr">{{ $settings->address ?? '—' }}</div>

            <div class="inv-total-badge">
                <div class="tbl">Invoice of ({{ $cur }})</div>
                <div class="tba">{{ $cur }}{{ fmtAmt($invoice->grand_total) }}</div>
            </div>
        </div>
    </div>

    {{-- BILL TO --}}
    <div class="inv-bill">
        @php
            $customer = $invoice->customer;
            $custName = $customer ? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) : ($customer->user->name ?? 'Walk-in');
        @endphp
        <div class="bill-row">
            <span class="bl">Bill To:</span>
            <span class="bv">{{ $custName }}</span>
        </div>
        <div class="bill-row">
            <span class="bl">Phone:</span>
            <span class="bv">{{ $invoice->customer?->user?->phone ?? '—' }}</span>
        </div>
        <div class="bill-row">
            <span class="bl">Address:</span>
            <span class="bv">{{ $invoice->customer?->address ?? '—' }}</span>
        </div>
    </div>

    {{-- INFO BAR --}}
    <div class="inv-info-bar">
        <div>
            <div class="info-label">Invoice #</div>
            <div class="info-value">#{{ $invoice->invoice_number }}</div>
        </div>
        <div>
            <div class="info-label">Date</div>
            <div class="info-value">{{ $invoice->created_at->format('d M, Y') }}</div>
        </div>
        <div>
            <div class="info-label">Payment</div>
            <div class="info-value">{{ $invoice->payment_method_label }}</div>
        </div>
        <div>
            <div class="info-label">Status</div>
            <div class="info-value" style="text-transform: capitalize;">{{ $invoice->order->status ?? '—' }}</div>
        </div>
    </div>

    {{-- ITEMS --}}
    <div class="inv-table-wrap">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="center">Rate</th>
                    <th class="center">Qty</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item['name'] ?? 'Product' }}</div>
                    </td>
                    <td class="center">{{ fmtMoney($item['price'] ?? 0, $cur) }}</td>
                    <td class="center">{{ $item['qty'] }}</td>
                    <td class="right">{{ fmtMoney($item['line_total'] ?? 0, $cur) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TOTALS --}}
    <div class="inv-totals">
        <div class="totals-table">
            <div class="totals-row">
                <span class="tl">Sub Total</span>
                <span class="tv">{{ fmtMoney($invoice->sub_total, $cur) }}</span>
            </div>
            @if($invoice->delivery_charge > 0)
            <div class="totals-row">
                <span class="tl">Delivery</span>
                <span class="tv">{{ fmtMoney($invoice->delivery_charge, $cur) }}</span>
            </div>
            @endif
            <div class="totals-row final">
                <span class="tl">Grand Total</span>
                <span class="tv">{{ fmtMoney($invoice->grand_total, $cur) }}</span>
            </div>
        </div>
    </div>

    <div class="inv-footer">
        <div class="inv-footer-thanks">Thanks for the business.</div>
    </div>
</div>
@endforeach

</body>
</html>
