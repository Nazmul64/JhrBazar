<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Receipt #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace, -apple-system, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background-color: #f1f5f9;
            padding: 20px 0;
        }
        .toolbar {
            max-width: 320px;
            margin: 0 auto 15px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .btn {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        .btn-secondary {
            background: #64748b;
        }
        .btn-secondary:hover {
            background: #475569;
        }
        .receipt-container {
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 12px 10px;
            border: 1px dashed #ccc;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        .store-name {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .double-divider {
            border-top: 1px double #000;
            margin: 6px 0;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: 11px;
        }
        table th {
            border-bottom: 1px dashed #000;
            padding: 4px 0;
            text-align: left;
        }
        table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
        }
        .grand-total {
            font-size: 14px;
            font-weight: 900;
            padding: 4px 0;
        }
        .barcode-wrap {
            margin-top: 10px;
            text-align: center;
        }
        .barcode-wrap svg {
            max-width: 100%;
            height: 40px;
        }

        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .toolbar {
                display: none !important;
            }
            .receipt-container {
                width: 100% !important;
                border: none !important;
                box-shadow: none !important;
                padding: 4px 2px !important;
                margin: 0 !important;
            }
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button class="btn btn-secondary" onclick="window.close() || history.back()">← Back</button>
    <button class="btn" onclick="window.print()">🖨 Print Receipt</button>
</div>

<div class="receipt-container" id="receiptContent">

    {{-- Store Header --}}
    <div class="text-center">
        <div class="store-name">{{ $settings->website_name ?? config('app.name', 'JhrBazar') }}</div>
        @if($settings?->address)
            <div style="font-size: 10px;">{{ $settings->address }}</div>
        @endif
        @if($settings?->mobile_number || $settings?->hotline_number)
            <div style="font-size: 10px;">Tel: {{ $settings->mobile_number ?? $settings->hotline_number }}</div>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Invoice Details --}}
    <div class="meta-row">
        <span>Invoice #: <strong>{{ $invoice->invoice_number }}</strong></span>
        <span>{{ $invoice->created_at ? $invoice->created_at->format('d/m/y h:i A') : '' }}</span>
    </div>
    <div class="meta-row">
        <span>Cust: {{ $invoice->customer?->user?->name ?? ($invoice->customer?->first_name ? $invoice->customer->first_name . ' ' . $invoice->customer->last_name : 'Walk-in Customer') }}</span>
        @if($invoice->customer?->user?->phone)
            <span>{{ $invoice->customer->user->phone }}</span>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Items List --}}
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">ITEM</th>
                <th class="text-center" style="width: 18%;">QTY</th>
                <th class="text-right" style="width: 32%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                @php
                    $price = (float)($item['price'] ?? 0);
                    $qty = (int)($item['qty'] ?? 1);
                    $lineTotal = (float)($item['line_total'] ?? ($price * $qty));
                    $name = $item['name'] ?? $item['title'] ?? 'Product';
                @endphp
                <tr>
                    <td>
                        <div class="fw-bold">{{ $name }}</div>
                        <div style="font-size: 10px; color: #444;">@ ৳{{ number_format($price, 2) }}</div>
                    </td>
                    <td class="text-center">{{ $qty }}</td>
                    <td class="text-right fw-bold">৳{{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    {{-- Calculation & Totals --}}
    <div class="total-row">
        <span>Sub Total:</span>
        <span>৳{{ number_format($invoice->sub_total, 2) }}</span>
    </div>

    @if($invoice->discount > 0)
        <div class="total-row">
            <span>Discount:</span>
            <span>-৳{{ number_format($invoice->discount, 2) }}</span>
        </div>
    @endif

    @if($invoice->tax_amount > 0)
        <div class="total-row">
            <span>VAT / Tax:</span>
            <span>+৳{{ number_format($invoice->tax_amount, 2) }}</span>
        </div>
    @endif

    <div class="double-divider"></div>

    <div class="total-row grand-total">
        <span>GRAND TOTAL:</span>
        <span>৳{{ number_format($invoice->grand_total, 2) }}</span>
    </div>

    <div class="divider"></div>

    <div class="total-row">
        <span>Paid ({{ ucfirst($invoice->payment_method ?? 'Cash') }}):</span>
        <span>৳{{ number_format($invoice->received_amount ?? $invoice->grand_total, 2) }}</span>
    </div>

    @if(($invoice->change_amount ?? 0) > 0)
        <div class="total-row">
            <span>Change Returned:</span>
            <span>৳{{ number_format($invoice->change_amount, 2) }}</span>
        </div>
    @endif

    {{-- Barcode / QR Code --}}
    <div class="barcode-wrap">
        <svg viewBox="0 0 100 25" preserveAspectRatio="none" style="width: 180px; height: 35px;">
            <rect x="0" y="0" width="2" height="25" fill="#000"/>
            <rect x="4" y="0" width="1" height="25" fill="#000"/>
            <rect x="7" y="0" width="3" height="25" fill="#000"/>
            <rect x="12" y="0" width="2" height="25" fill="#000"/>
            <rect x="16" y="0" width="1" height="25" fill="#000"/>
            <rect x="19" y="0" width="2" height="25" fill="#000"/>
            <rect x="23" y="0" width="4" height="25" fill="#000"/>
            <rect x="29" y="0" width="1" height="25" fill="#000"/>
            <rect x="32" y="0" width="3" height="25" fill="#000"/>
            <rect x="37" y="0" width="2" height="25" fill="#000"/>
            <rect x="41" y="0" width="1" height="25" fill="#000"/>
            <rect x="44" y="0" width="4" height="25" fill="#000"/>
            <rect x="50" y="0" width="2" height="25" fill="#000"/>
            <rect x="54" y="0" width="1" height="25" fill="#000"/>
            <rect x="57" y="0" width="3" height="25" fill="#000"/>
            <rect x="62" y="0" width="2" height="25" fill="#000"/>
            <rect x="66" y="0" width="1" height="25" fill="#000"/>
            <rect x="69" y="0" width="4" height="25" fill="#000"/>
            <rect x="75" y="0" width="2" height="25" fill="#000"/>
            <rect x="79" y="0" width="1" height="25" fill="#000"/>
            <rect x="82" y="0" width="3" height="25" fill="#000"/>
            <rect x="87" y="0" width="2" height="25" fill="#000"/>
            <rect x="91" y="0" width="1" height="25" fill="#000"/>
            <rect x="94" y="0" width="4" height="25" fill="#000"/>
        </svg>
        <div style="font-size: 10px; font-family: monospace; letter-spacing: 1px; margin-top: 2px;">{{ $invoice->invoice_number }}</div>
    </div>

    {{-- Footer Thanks --}}
    <div class="text-center" style="margin-top: 10px; font-size: 10px;">
        <div>*** THANK YOU FOR SHOPPING! ***</div>
        <div>Please visit again</div>
    </div>

</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        // Auto print prompt if opened with ?autoprint=1 or direct
        const params = new URLSearchParams(window.location.search);
        if (params.get('autoprint') === '1') {
            setTimeout(() => {
                window.print();
            }, 300);
        }
    });
</script>

</body>
</html>
