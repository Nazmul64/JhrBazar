<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>

    @php
    /**
     * Smart money formatter — removes trailing zeros:
     *   515.00 → 515
     *   515.50 → 515.5
     *   515.55 → 515.55
     */
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
    $cur = $settings->default_currency ?? '৳';
    function fmtMoney($num, $cur) {
        return $cur . fmtAmt($num);
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

        .btn-back {
            height: 36px; padding: 0 16px;
            background: #334155; color: #cbd5e1; border: none;
            border-radius: 6px; font-size: 13px; cursor: pointer; font-family: inherit;
        }
        .btn-back:hover { background: #475569; }

        .btn-scanner {
            height: 36px; padding: 0 16px;
            background: #0f766e; color: #fff; border: none;
            border-radius: 6px; font-size: 13px; font-weight: 700;
            cursor: pointer; font-family: inherit;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-scanner:hover { background: #0d6b64; }

        /* ════════ INVOICE WRAPPER ════════ */
        .invoice-wrap {
            max-width: 860px; margin: 30px auto;
            background: var(--white); border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10); overflow: hidden;
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
        .inv-table tbody tr:last-child { border-bottom: none; }
        .inv-table tbody tr.scanned-row { background: #f0fdf4; animation: rowFlash .5s ease; }
        @keyframes rowFlash { 0%{background:#bbf7d0;} 100%{background:#f0fdf4;} }

        .inv-table tbody td { padding: 14px 16px; font-size: 13px; vertical-align: middle; }
        .inv-table tbody td.center { text-align: center; }
        .inv-table tbody td.right  { text-align: right; font-weight: 600; }

        .item-cell { display: flex; align-items: center; gap: 12px; }
        .item-img { width: 46px; height: 46px; object-fit: cover; border-radius: 6px; flex-shrink: 0; background: #f1f5f9; }
        .item-img-ph {
            width: 46px; height: 46px; border-radius: 6px;
            background: #f1f5f9; display: flex; align-items: center;
            justify-content: center; color: #c8d0db; font-size: 20px; flex-shrink: 0;
        }
        .item-name { font-size: 13px; font-weight: 600; color: var(--text); }
        .item-desc { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        .bc-badge {
            display: inline-flex; align-items: center; gap: 4px;
            background: #f0f4ff; border: 1px solid #d8e0f8;
            color: var(--blue); border-radius: 3px;
            padding: 2px 6px; font-family: 'Courier New', monospace;
            font-size: 11px; font-weight: 700; letter-spacing: .4px; margin-top: 3px;
        }

        /* ════════ TOTALS ════════ */
        .inv-totals { display: flex; justify-content: flex-end; padding: 16px 36px 0; }
        .totals-table { min-width: 300px; }
        .totals-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 7px 0; font-size: 13px; border-bottom: 1px solid #f1f5f9;
        }
        .totals-row:last-child { border-bottom: none; }
        .totals-row .tl { color: var(--muted); }
        .totals-row .tv { color: var(--text); font-weight: 500; }
        .totals-row.discount .tv { color: var(--green); }
        .totals-row.final {
            border-top: 1px solid var(--border); margin-top: 2px; padding-top: 10px;
        }
        .totals-row.final .tl,
        .totals-row.final .tv { color: var(--text); font-size: 14px; font-weight: 700; }

        /* ════════ NOTE ════════ */
        .inv-note {
            margin: 12px 36px 0; padding: 12px 16px;
            background: #fffbeb; border-left: 3px solid #fcd34d;
            border-radius: 4px; font-size: 13px; color: #92400e;
        }
        .inv-note strong { display: block; margin-bottom: 3px; }

        /* ════════ FOOTER ════════ */
        .inv-footer {
            padding: 24px 36px 28px; margin-top: 16px;
            display: flex; justify-content: space-between;
            align-items: flex-end; border-top: 1px solid var(--border);
        }
        .inv-footer-thanks { font-size: 13px; color: var(--muted); }
        .inv-footer-sign { text-align: right; }
        .inv-footer-sign-line { width: 160px; border-top: 1px solid #94a3b8; margin-bottom: 6px; }
        .inv-footer-sign-label { font-size: 13px; color: var(--muted); }

        /* ════════ SCANNER MODAL ════════ */
        .scanner-overlay {
            position: fixed; inset: 0; background: rgba(15,23,42,.6);
            z-index: 50000; display: flex; align-items: center;
            justify-content: center; opacity: 0; pointer-events: none;
            transition: opacity .2s; padding: 16px;
        }
        .scanner-overlay.show { opacity: 1; pointer-events: all; }
        .scanner-modal {
            background: var(--white); border-radius: 14px;
            width: 460px; max-width: 100%;
            box-shadow: 0 24px 64px rgba(0,0,0,.25);
            transform: scale(.95) translateY(10px); transition: transform .2s; overflow: hidden;
        }
        .scanner-overlay.show .scanner-modal { transform: scale(1) translateY(0); }
        .scanner-head {
            background: #0f766e; padding: 16px 20px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .scanner-head h5 {
            font-size: 15px; font-weight: 700; color: #fff; margin: 0;
            display: flex; align-items: center; gap: 8px;
        }
        .scanner-close {
            background: none; border: none; color: rgba(255,255,255,.7);
            font-size: 22px; cursor: pointer; line-height: 1;
        }
        .scanner-close:hover { color: #fff; }
        .scanner-body { padding: 24px 20px; }
        .scanner-input-wrap { position: relative; margin-bottom: 14px; }
        .scanner-input-prefix {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%); color: #0f766e; pointer-events: none;
        }
        #barcodeInput {
            width: 100%; height: 52px; border: 2px solid #0f766e;
            border-radius: var(--radius-sm); padding: 0 14px 0 42px;
            font-size: 16px; font-weight: 600; font-family: 'Courier New', monospace;
            outline: none; color: var(--text); letter-spacing: .5px;
        }
        #barcodeInput:focus { box-shadow: 0 0 0 3px rgba(15,118,110,.18); }
        #barcodeInput::placeholder {
            font-family: 'Segoe UI', sans-serif; font-weight: 400;
            letter-spacing: 0; font-size: 13px;
        }
        .scanner-hint {
            font-size: 12px; color: var(--muted); margin-bottom: 14px;
            display: flex; align-items: center; gap: 5px;
        }
        .scanner-hint::before {
            content: ''; display: inline-block; width: 8px; height: 8px;
            border-radius: 50%; background: #22c55e; animation: blink 1s infinite;
        }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:.3;} }

        .btn-scan-search {
            width: 100%; height: 46px; background: #0f766e; color: #fff;
            border: none; border-radius: var(--radius-sm); font-size: 14px;
            font-weight: 700; cursor: pointer; font-family: inherit;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-scan-search:hover { background: #0d6b64; }
        .btn-scan-search:disabled { opacity: .6; cursor: not-allowed; }

        .scan-result {
            display: none; margin-top: 16px;
            border: 1.5px solid var(--border); border-radius: var(--radius); overflow: hidden;
        }
        .scan-result.show { display: block; }
        .scan-result.found     { border-color: #bbf7d0; }
        .scan-result.not-found { border-color: #fecdd3; }
        .scan-result-head {
            padding: 10px 14px; font-size: 12px; font-weight: 700;
            display: flex; align-items: center; gap: 6px;
        }
        .scan-result.found .scan-result-head     { background: #f0fdf4; color: #15803d; }
        .scan-result.not-found .scan-result-head { background: #fff1f2; color: #be123c; }
        .scan-result-body { padding: 14px; }
        .scan-prod-row { display: flex; gap: 12px; align-items: flex-start; }
        .scan-prod-img {
            width: 60px; height: 60px; object-fit: cover;
            border-radius: 6px; flex-shrink: 0; background: #f1f5f9;
        }
        .scan-prod-img-ph {
            width: 60px; height: 60px; border-radius: 6px;
            background: #f1f5f9; display: flex; align-items: center;
            justify-content: center; color: #c8d0db; font-size: 24px; flex-shrink: 0;
        }
        .scan-prod-info { flex: 1; min-width: 0; }
        .scan-prod-name  { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
        .scan-prod-meta  { font-size: 12px; color: var(--muted); margin-bottom: 6px; }
        .scan-prod-price { font-size: 16px; font-weight: 800; color: var(--blue); }
        .scan-prod-old   { font-size: 12px; color: #bbc; text-decoration: line-through; margin-left: 6px; }
        .scan-prod-stock { font-size: 12px; color: var(--muted); margin-top: 3px; }
        .scan-not-found-msg { font-size: 13px; color: #be123c; text-align: center; padding: 8px 0; }

        .scanner-log-title {
            font-size: 12px; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: .5px;
            margin-top: 18px; margin-bottom: 8px;
        }
        .scanner-log { max-height: 120px; overflow-y: auto; }
        .scanner-log-item {
            display: flex; align-items: center; justify-content: space-between;
            font-size: 12px; padding: 5px 0; border-bottom: 1px solid #f1f5f9;
        }
        .scanner-log-item:last-child { border-bottom: none; }
        .sli-name { color: var(--text); font-weight: 600; flex: 1; }
        .sli-bc   { font-family: 'Courier New', monospace; color: var(--blue); font-size: 11px; }
        .sli-ok   { color: #16a34a; font-size: 13px; }
        .sli-err  { color: #dc2626; font-size: 13px; }

        /* ════════ TOAST ════════ */
        .toast-container {
            position: fixed; bottom: 24px; right: 24px; z-index: 99999;
            display: flex; flex-direction: column; gap: 8px; pointer-events: none;
        }
        .inv-toast {
            background: #1e293b; color: #fff; border-radius: var(--radius);
            padding: 12px 18px; font-size: 13px; font-weight: 500;
            box-shadow: 0 8px 24px rgba(0,0,0,.2); display: flex;
            align-items: center; gap: 10px; min-width: 260px; max-width: 340px;
            animation: tIn .3s ease; pointer-events: all;
        }
        .inv-toast.t-success { background: #15803d; }
        .inv-toast.t-error   { background: #be123c; }
        .inv-toast.t-warning { background: #b45309; }
        @keyframes tIn  { from{opacity:0;transform:translateX(40px);} to{opacity:1;transform:translateX(0);} }
        @keyframes tOut { from{opacity:1;} to{opacity:0;transform:translateX(40px);} }

        /* ════════ PRINT ════════ */
        @media print {
            body { background: #fff !important; }
            .print-bar, .scanner-overlay, .toast-container { display: none !important; }
            .invoice-wrap { margin: 0; border-radius: 0; box-shadow: none; }
            @page { margin: 14mm; }
        }

        @media (max-width: 600px) {
            .inv-header { flex-direction: column; }
            .inv-right  { text-align: left; }
            .inv-info-bar { grid-template-columns: 1fr 1fr; }
            .inv-totals, .inv-note, .inv-footer,
            .inv-bill, .inv-header, .inv-info-bar { padding-left: 16px; padding-right: 16px; }
            .inv-footer { flex-direction: column; align-items: flex-start; gap: 24px; }
        }
    </style>
</head>
<body>

{{-- ══ PRINT TOOLBAR ══ --}}
<div class="print-bar">
    <div class="print-bar-left">
        Invoice <strong>#{{ $invoice->invoice_number }}</strong>
        &nbsp;·&nbsp; {{ $invoice->created_at->format('d M Y, h:i A') }}
    </div>
    <div class="print-bar-btns">
        <button class="btn-scanner" onclick="openScanner()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M2 4h2v16H2V4zm3 0h1v16H5V4zm2 0h2v16H7V4zm3 0h1v16h-1V4zm2 0h2v16h-2V4zm3 0h1v16h-1V4zm2 0h2v16h-2V4z"/>
            </svg>
            Barcode Scanner
        </button>
        <button class="btn-back" onclick="history.back()">← Back to POS</button>
        <button class="btn-print" onclick="window.print()">🖨 Print Invoice</button>
    </div>
</div>

{{-- ══ INVOICE ══ --}}
<div class="invoice-wrap">

    {{-- HEADER --}}
    <div class="inv-header">
        <div class="inv-brand">
            @if($settings && $settings->logo)
                <img class="inv-logo" src="{{ asset($settings->logo) }}"
                     alt="{{ $settings->website_name }}" onerror="this.style.display='none'">
            @elseif($settings && $settings->app_logo)
                <img class="inv-logo" src="{{ asset($settings->app_logo) }}"
                     alt="{{ $settings->website_name }}" onerror="this.style.display='none'">
            @else
                <div class="inv-logo-placeholder">
                    {{ strtoupper(substr($settings->website_name ?? 'S', 0, 1)) }}
                </div>
            @endif

            <div class="inv-brand-info">
                <div class="inv-brand-name">{{ $settings->website_name ?? config('app.name') }}</div>
                @if($settings?->website_url)
                    <div class="inv-brand-meta">{{ $settings->website_url }}</div>
                @endif
                @if($settings?->email_address)
                    <div class="inv-brand-meta">{{ $settings->email_address }}</div>
                @endif
                @if($settings?->mobile_number)
                    <div class="inv-brand-meta">{{ $settings->mobile_number }}</div>
                @endif
                @if($settings?->hotline_number)
                    <div class="inv-brand-meta">Hotline: {{ $settings->hotline_number }}</div>
                @endif
            </div>
        </div>

        <div class="inv-right">
            <div class="inv-right-label">Business Address</div>
            <div class="inv-right-addr">{{ $settings->address ?? '—' }}</div>

            <div class="inv-total-badge">
                <div class="tbl">Invoice of ({{ $cur }})</div>
                {{-- NO trailing .00 --}}
                <div class="tba">{{ $cur }}{{ fmtAmt($invoice->grand_total) }}</div>
            </div>

            <div class="qr-placeholder">
                @if(!empty($invoice->qr_code))
                    <img src="{{ $invoice->qr_code }}" alt="QR Code">
                @else
                    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" width="60" height="60">
                        <rect width="64" height="64" fill="white"/>
                        <rect x="4" y="4" width="18" height="18" fill="none" stroke="#222" stroke-width="2"/>
                        <rect x="8" y="8" width="10" height="10" fill="#222"/>
                        <rect x="42" y="4" width="18" height="18" fill="none" stroke="#222" stroke-width="2"/>
                        <rect x="46" y="8" width="10" height="10" fill="#222"/>
                        <rect x="4" y="42" width="18" height="18" fill="none" stroke="#222" stroke-width="2"/>
                        <rect x="8" y="46" width="10" height="10" fill="#222"/>
                        <rect x="26" y="4" width="4" height="4" fill="#222"/>
                        <rect x="32" y="4" width="4" height="4" fill="#222"/>
                        <rect x="38" y="4" width="4" height="4" fill="#222"/>
                        <rect x="26" y="10" width="4" height="4" fill="#222"/>
                        <rect x="38" y="10" width="4" height="4" fill="#222"/>
                        <rect x="26" y="16" width="4" height="4" fill="#222"/>
                        <rect x="32" y="16" width="4" height="4" fill="#222"/>
                        <rect x="4"  y="26" width="4" height="4" fill="#222"/>
                        <rect x="10" y="26" width="4" height="4" fill="#222"/>
                        <rect x="16" y="26" width="4" height="4" fill="#222"/>
                        <rect x="26" y="26" width="4" height="4" fill="#222"/>
                        <rect x="38" y="26" width="4" height="4" fill="#222"/>
                        <rect x="44" y="26" width="4" height="4" fill="#222"/>
                        <rect x="56" y="26" width="4" height="4" fill="#222"/>
                        <rect x="4"  y="32" width="4" height="4" fill="#222"/>
                        <rect x="16" y="32" width="4" height="4" fill="#222"/>
                        <rect x="26" y="32" width="4" height="4" fill="#222"/>
                        <rect x="32" y="32" width="4" height="4" fill="#222"/>
                        <rect x="44" y="32" width="4" height="4" fill="#222"/>
                        <rect x="56" y="32" width="4" height="4" fill="#222"/>
                        <rect x="4"  y="38" width="4" height="4" fill="#222"/>
                        <rect x="10" y="38" width="4" height="4" fill="#222"/>
                        <rect x="26" y="38" width="4" height="4" fill="#222"/>
                        <rect x="38" y="38" width="4" height="4" fill="#222"/>
                        <rect x="50" y="38" width="4" height="4" fill="#222"/>
                        <rect x="26" y="44" width="4" height="4" fill="#222"/>
                        <rect x="32" y="44" width="4" height="4" fill="#222"/>
                        <rect x="44" y="44" width="4" height="4" fill="#222"/>
                        <rect x="56" y="44" width="4" height="4" fill="#222"/>
                        <rect x="32" y="50" width="4" height="4" fill="#222"/>
                        <rect x="38" y="50" width="4" height="4" fill="#222"/>
                        <rect x="50" y="50" width="4" height="4" fill="#222"/>
                        <rect x="26" y="56" width="4" height="4" fill="#222"/>
                        <rect x="44" y="56" width="4" height="4" fill="#222"/>
                        <rect x="56" y="56" width="4" height="4" fill="#222"/>
                    </svg>
                @endif
            </div>
        </div>
    </div>

    {{-- BILL TO --}}
    <div class="inv-bill">
        @php
            $customer     = $invoice->customer;
            $customerUser = $customer?->user;
            $custName     = $customer
                ? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''))
                : ($customerUser?->name ?? '');
            $custEmail    = $customerUser?->email ?? '';
            $custPhone    = $customerUser?->phone ?? '';
            $custAddress  = $customer?->address   ?? '';
        @endphp
        <div class="bill-row">
            <span class="bl">Bill To:</span>
            <span class="bv">{{ $custName ?: 'Walk-in Customer' }}</span>
        </div>
        @if($custAddress)
        <div class="bill-row">
            <span class="bl">Address:</span>
            <span class="bv">{{ $custAddress }}</span>
        </div>
        @endif
        @if($custEmail)
        <div class="bill-row">
            <span class="bl">Email:</span>
            <span class="bv">{{ $custEmail }}</span>
        </div>
        @endif
        @if($custPhone)
        <div class="bill-row">
            <span class="bl">Phone:</span>
            <span class="bv">{{ $custPhone }}</span>
        </div>
        @endif
        @if($customer?->date_of_birth)
        <div class="bill-row">
            <span class="bl">DOB:</span>
            <span class="bv">{{ \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') }}</span>
        </div>
        @endif
    </div>

    {{-- INFO BAR --}}
    <div class="inv-info-bar">
        <div>
            <div class="info-label">Payment Method</div>
            <div class="info-value">{{ $invoice->payment_method_label }}</div>
        </div>
        <div>
            <div class="info-label">Invoice Number</div>
            <div class="info-value">#{{ $invoice->invoice_number }}</div>
        </div>
        <div>
            <div class="info-label">Invoice Date</div>
            <div class="info-value">{{ $invoice->created_at->format('d F, Y') }}</div>
        </div>
        <div>
            <div class="info-label">Order Date</div>
            <div class="info-value">{{ $invoice->created_at->format('d F, Y') }}</div>
        </div>
    </div>

    {{-- ITEMS TABLE --}}
    <div class="inv-table-wrap">
        <table class="inv-table" id="invoiceTable">
            <thead>
                <tr>
                    <th style="width:46px;">#</th>
                    <th>Item Name</th>
                    <th class="center">Rate</th>
                    <th class="center">Qty</th>
                    <th class="center">Size</th>
                    <th class="center">Color</th>
                    <th class="right">Price</th>
                </tr>
            </thead>
            <tbody id="invoiceTableBody">
                @foreach($invoice->items as $index => $item)
                @php
                    $barcodeDisplay = !empty($item['barcode']) ? $item['barcode'] : ($item['sku'] ?? '');
                    $itemPrice      = (float)($item['price']         ?? 0);
                    $itemSellPrice  = (float)($item['selling_price'] ?? 0);
                    $itemLineTotal  = (float)($item['line_total']    ?? 0);
                    $hasDiscount    = !empty($item['discount_price'])
                                      && (float)$item['discount_price'] > 0
                                      && $itemSellPrice != $itemPrice;
                @endphp
                <tr data-barcode="{{ $barcodeDisplay }}" data-sku="{{ $item['sku'] ?? '' }}">
                    <td>{{ $index + 1 }}.</td>
                    <td>
                        <div class="item-cell">
                            @if(!empty($item['thumbnail']))
                                <img class="item-img"
                                     src="{{ asset($item['thumbnail']) }}"
                                     alt="{{ $item['name'] }}"
                                     onerror="this.outerHTML='<div class=\'item-img-ph\'>📦</div>'">
                            @else
                                <div class="item-img-ph">📦</div>
                            @endif
                            <div>
                                <div class="item-name">{{ $item['name'] }}</div>
                                @if($barcodeDisplay)
                                    <div class="bc-badge">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M2 4h2v16H2V4zm3 0h1v16H5V4zm2 0h2v16H7V4zm3 0h1v16h-1V4zm2 0h2v16h-2V4zm3 0h1v16h-1V4zm2 0h2v16h-2V4z"/>
                                        </svg>
                                        {{ $barcodeDisplay }}
                                    </div>
                                @elseif(!empty($item['short_description']))
                                    <div class="item-desc">{{ $item['short_description'] }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="center">
                        {{ fmtMoney($itemPrice, $cur) }}
                        @if($hasDiscount)
                            <br><small style="color:#aab;text-decoration:line-through;font-size:11px;">
                                {{ fmtMoney($itemSellPrice, $cur) }}
                            </small>
                        @endif
                    </td>
                    <td class="center">{{ $item['qty'] }}</td>
                    <td class="center">{{ $item['size']  ?? '--' }}</td>
                    <td class="center">{{ $item['color'] ?? '--' }}</td>
                    <td class="right">{{ fmtMoney($itemLineTotal, $cur) }}</td>
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

            @if($invoice->discount > 0)
            <div class="totals-row discount">
                <span class="tl">Discount</span>
                <span class="tv">− {{ fmtMoney($invoice->discount, $cur) }}</span>
            </div>
            @endif

            @if($invoice->coupon_code)
            <div class="totals-row">
                <span class="tl">Coupon ({{ $invoice->coupon_code }})</span>
                <span class="tv" style="color:#16a34a;">Applied</span>
            </div>
            @endif

            <div class="totals-row">
                <span class="tl">Delivery Charge</span>
                <span class="tv">{{ fmtMoney($invoice->delivery_charge ?? 0, $cur) }}</span>
            </div>

            @if($invoice->tax_breakdown && count($invoice->tax_breakdown))
                @foreach($invoice->tax_breakdown as $tax)
                <div class="totals-row">
                    <span class="tl">{{ $tax['name'] }} ({{ $tax['rate'] }}%)</span>
                    <span class="tv">{{ fmtMoney($tax['amount'], $cur) }}</span>
                </div>
                @endforeach
            @elseif($invoice->tax_amount > 0)
            <div class="totals-row">
                <span class="tl">Tax</span>
                <span class="tv">{{ fmtMoney($invoice->tax_amount, $cur) }}</span>
            </div>
            @endif

            <div class="totals-row final">
                <span class="tl">Total Amount</span>
                <span class="tv">{{ fmtMoney($invoice->grand_total, $cur) }}</span>
            </div>
        </div>
    </div>

    @if($invoice->note)
    <div class="inv-note">
        <strong>Order Note:</strong>
        {{ $invoice->note }}
    </div>
    @endif

    <div class="inv-footer">
        <div class="inv-footer-thanks">
            {{ $settings?->footer_text ?: 'Thanks for the business.' }}
        </div>
        <div class="inv-footer-sign">
            <div class="inv-footer-sign-line"></div>
            <div class="inv-footer-sign-label">Signature</div>
        </div>
    </div>

</div>

{{-- ══ BARCODE SCANNER MODAL ══ --}}
<div class="scanner-overlay" id="scannerOverlay" onclick="closeScannerOnBg(event)">
    <div class="scanner-modal">
        <div class="scanner-head">
            <h5>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                    <path d="M2 4h2v16H2V4zm3 0h1v16H5V4zm2 0h2v16H7V4zm3 0h1v16h-1V4zm2 0h2v16h-2V4zm3 0h1v16h-1V4zm2 0h2v16h-2V4z"/>
                </svg>
                Barcode / SKU Scanner
            </h5>
            <button class="scanner-close" onclick="closeScanner()">&times;</button>
        </div>
        <div class="scanner-body">
            <div class="scanner-hint">Scanner ready — scan or type barcode / SKU below</div>
            <div class="scanner-input-wrap">
                <span class="scanner-input-prefix">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#0f766e">
                        <path d="M2 4h2v16H2V4zm3 0h1v16H5V4zm2 0h2v16H7V4zm3 0h1v16h-1V4zm2 0h2v16h-2V4zm3 0h1v16h-1V4zm2 0h2v16h-2V4z"/>
                    </svg>
                </span>
                <input type="text" id="barcodeInput"
                       placeholder="Scan barcode or type SKU…"
                       autocomplete="off" autocorrect="off" spellcheck="false" autofocus>
            </div>
            <button class="btn-scan-search" id="btnScanSearch" onclick="searchBarcode()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="6"/><path d="M21 21l-4.35-4.35"/>
                </svg>
                Search Product
            </button>
            <div class="scan-result" id="scanResult">
                <div class="scan-result-head" id="scanResultHead"></div>
                <div class="scan-result-body" id="scanResultBody"></div>
            </div>
            <div id="scanLogWrap" style="display:none;">
                <div class="scanner-log-title">Scan History</div>
                <div class="scanner-log" id="scanLog"></div>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
'use strict';

const PRODUCT_LOOKUP_URL = '{{ route("admin.pointofsalepos.products") }}';
const CURRENCY = '{{ $cur }}';

/* Smart JS formatter — mirrors PHP fmtAmt */
function fmtAmt(n) {
    const num = parseFloat(n) || 0;
    if (num === Math.floor(num)) return num.toLocaleString();
    const r1 = Math.round(num * 10) / 10;
    if (r1 === Math.round(num * 100) / 100) return r1.toFixed(1);
    return num.toFixed(2);
}
function fmt(n) { return CURRENCY + fmtAmt(n); }

let scanLog   = [];
let scanTimer = null;

/* ══ TOAST ══ */
function showToast(msg, type = 'info', ms = 3000) {
    const icons = { success: '✓', error: '✕', warning: '⚠', info: 'ℹ' };
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = `inv-toast t-${type}`;
    t.innerHTML = `<span>${icons[type]||icons.info}</span><span>${msg}</span>`;
    c.appendChild(t);
    setTimeout(() => {
        t.style.animation = 'tOut .3s ease forwards';
        t.addEventListener('animationend', () => t.remove(), { once: true });
    }, ms);
}

/* ══ SCANNER MODAL ══ */
function openScanner() {
    document.getElementById('scannerOverlay').classList.add('show');
    setTimeout(() => document.getElementById('barcodeInput').focus(), 100);
}
function closeScanner() {
    document.getElementById('scannerOverlay').classList.remove('show');
    document.getElementById('barcodeInput').value = '';
    document.getElementById('scanResult').className = 'scan-result';
}
function closeScannerOnBg(e) {
    if (e.target === document.getElementById('scannerOverlay')) closeScanner();
}

/* ══ BARCODE SEARCH — real DB ══ */
async function searchBarcode() {
    const input = document.getElementById('barcodeInput');
    const code  = input.value.trim();

    if (!code) { showToast('Please enter or scan a barcode / SKU.', 'warning'); input.focus(); return; }

    const btn = document.getElementById('btnScanSearch');
    btn.disabled    = true;
    btn.textContent = 'Searching…';

    try {
        const res = await fetch(PRODUCT_LOOKUP_URL + '?' + new URLSearchParams({ search: code, page: 1 }), {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Server error ' + res.status);

        const data     = await res.json();
        const products = data.data || [];

        /* Exact barcode or SKU match → fallback first result */
        const match = products.find(p =>
            (p.barcode && String(p.barcode).trim().toLowerCase() === code.toLowerCase()) ||
            (p.sku     && String(p.sku).trim().toLowerCase()     === code.toLowerCase())
        ) || (products.length ? products[0] : null);

        if (match) { showScanResult(match, code); highlightTableRow(code, match); }
        else        { showScanNotFound(code); }

    } catch (err) {
        showToast('Network error. Please try again.', 'error');
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round">
            <circle cx="11" cy="11" r="6"/><path d="M21 21l-4.35-4.35"/></svg> Search Product`;
    }
}

function showScanResult(product, scannedCode) {
    const price    = parseFloat(product.discount_price) > 0 ? product.discount_price : product.selling_price;
    const oldPrice = parseFloat(product.discount_price) > 0 ? product.selling_price  : null;
    const thumb    = product.thumbnail ? '/' + product.thumbnail : null;
    const stock    = parseInt(product.stock_quantity) || 0;
    const sold     = parseInt(product.sold) || 0;
    const bcVal    = product.barcode || product.sku || scannedCode;

    const resultEl = document.getElementById('scanResult');
    resultEl.className = 'scan-result show found';
    document.getElementById('scanResultHead').innerHTML =
        `✓ Product found — <span style="font-family:'Courier New',monospace;font-weight:700;">${escHtml(scannedCode)}</span>`;
    document.getElementById('scanResultBody').innerHTML = `
        <div class="scan-prod-row">
            ${thumb
                ? `<img class="scan-prod-img" src="${thumb}" alt="${escHtml(product.name)}"
                       onerror="this.outerHTML='<div class=\\'scan-prod-img-ph\\'>📦</div>'">`
                : `<div class="scan-prod-img-ph">📦</div>`}
            <div class="scan-prod-info">
                <div class="scan-prod-name">${escHtml(product.name)}</div>
                <div class="scan-prod-meta">
                    SKU: <strong style="font-family:'Courier New',monospace;">${escHtml(bcVal)}</strong>
                    ${product.size  ? ` | Size: ${escHtml(product.size)}`  : ''}
                    ${product.color ? ` | Color: ${escHtml(product.color)}` : ''}
                </div>
                <div>
                    <span class="scan-prod-price">${fmt(price)}</span>
                    ${oldPrice ? `<span class="scan-prod-old">${fmt(oldPrice)}</span>` : ''}
                </div>
                <div class="scan-prod-stock">${stock} in stock · ${sold} sold</div>
            </div>
        </div>`;
    addScanLog(product.name, scannedCode, true);
    showToast(`Found: ${product.name}`, 'success', 2500);
}

function showScanNotFound(code) {
    const resultEl = document.getElementById('scanResult');
    resultEl.className = 'scan-result show not-found';
    document.getElementById('scanResultHead').innerHTML =
        `✕ Not found — <span style="font-family:'Courier New',monospace;font-weight:700;">${escHtml(code)}</span>`;
    document.getElementById('scanResultBody').innerHTML = `
        <div class="scan-not-found-msg">
            No product matched barcode/SKU <strong>${escHtml(code)}</strong>.<br>
            Please check the code and try again.
        </div>`;
    addScanLog('Not found', code, false);
    showToast(`No product for: ${code}`, 'error', 3000);
}

function highlightTableRow(code, product) {
    const rows = document.querySelectorAll('#invoiceTableBody tr');
    let hit = false;
    rows.forEach(row => {
        const rBc  = (row.dataset.barcode || '').toLowerCase();
        const rSku = (row.dataset.sku     || '').toLowerCase();
        if (rBc === code.toLowerCase() || rSku === code.toLowerCase()) {
            row.classList.remove('scanned-row');
            void row.offsetWidth;
            row.classList.add('scanned-row');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            hit = true;
        }
    });
    if (!hit && product) showToast('Product found but not in this invoice.', 'warning', 3000);
}

function addScanLog(name, code, found) {
    scanLog.unshift({ name, code, found });
    document.getElementById('scanLogWrap').style.display = 'block';
    document.getElementById('scanLog').innerHTML = scanLog.slice(0, 10).map(e => `
        <div class="scanner-log-item">
            <span class="sli-name">${escHtml(e.name)}</span>
            <span class="sli-bc">${escHtml(e.code)}</span>
            <span class="${e.found ? 'sli-ok' : 'sli-err'}">${e.found ? '✓' : '✕'}</span>
        </div>`).join('');
}

function escHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
        .replace(/'/g,'&#39;');
}

/* Hardware scanner fires Enter */
document.getElementById('barcodeInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); searchBarcode(); }
});

/* Auto-trigger on fast paste (≥6 chars, no spaces, 400ms idle) */
document.getElementById('barcodeInput').addEventListener('input', function() {
    clearTimeout(scanTimer);
    const val = this.value.trim();
    if (val.length >= 6 && !val.includes(' ')) {
        scanTimer = setTimeout(searchBarcode, 400);
    }
});

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeScanner(); });
</script>

</body>
</html>
