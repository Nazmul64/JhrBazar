<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>

    @php
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

    $customer = $invoice->customer;
    $customerUser = $customer?->user;
    $custName = $customer
        ? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''))
        : ($customerUser?->name ?? 'Walk-in Customer');
    $custEmail = $customerUser?->email ?? '';
    $custPhone = $customerUser?->phone ?? ($invoice->phone ?? ($invoice->order?->phone ?? ''));
    $custAddress = $customer?->address ?? ($customerUser?->address ?? ($invoice->order?->note ?? ''));

    // Extract address if note contains details
    if (empty($custAddress) || str_contains($custAddress, 'Online Order')) {
        $note = $invoice->order?->note ?? '';
        if (preg_match('/Address:\s*([^\n]+)/i', $note, $m)) {
            $custAddress = trim($m[1]);
        }
        if (empty($custPhone) && preg_match('/Phone:\s*([^\n]+)/i', $note, $m)) {
            $custPhone = trim($m[1]);
        }
        if (empty($custName) || $custName === 'Walk-in Customer') {
            if (preg_match('/Name:\s*([^\n]+)/i', $note, $m)) {
                $custName = trim($m[1]);
            }
        }
    }

    $orderStatus = $invoice->order?->status ?? ($invoice->payment_status ?? 'Completed');
    $paymentMethod = $invoice->payment_method ?? 'Cash On Delivery';
    $deliveryCharge = (float)($invoice->delivery_charge ?? ($invoice->order?->delivery_charge ?? 0));
    $deliveryArea = $deliveryCharge > 100 ? 'Outside Dhaka Home Delivery(24-96Hrs)' : ($deliveryCharge > 0 ? 'Inside Dhaka Home Delivery(24-48Hrs)' : 'Standard Delivery');

    $items = $invoice->items ?? [];
    $totalQty = collect($items)->sum(fn($i) => (int)($i['qty'] ?? 1));
    $trackUrl = url('/order-success?invoice=' . $invoice->invoice_number);
    $qrApi = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($trackUrl);
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #0f172a;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
            --bg-page: #f1f5f9;
            --font: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            --font-bn: 'Hind Siliguri', 'Plus Jakarta Sans', sans-serif;
            --font-mono: 'Courier Prime', 'Courier New', monospace;
        }

        body {
            font-family: var(--font);
            background: var(--bg-page);
            color: var(--text);
            font-size: 13px;
            line-height: 1.45;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ════════ TOP ACTION / SIZE SWITCHER BAR ════════ */
        .top-toolbar {
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }

        .tb-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .tb-title {
            font-size: 14px;
            font-weight: 700;
            color: #f8fafc;
        }

        .tb-meta {
            font-size: 12px;
            color: #94a3b8;
        }

        .size-switcher {
            display: flex;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 3px;
            gap: 4px;
        }

        .size-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: inherit;
        }

        .size-btn:hover {
            color: #ffffff;
            background: rgba(255,255,255,0.08);
        }

        .size-btn.active {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(37,99,235,0.4);
        }

        .tb-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-act {
            height: 36px;
            padding: 0 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-act-back {
            background: #334155;
            color: #e2e8f0;
        }
        .btn-act-back:hover { background: #475569; color: #fff; }

        .btn-act-print {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            box-shadow: 0 2px 10px rgba(16,185,129,0.3);
        }
        .btn-act-print:hover { opacity: 0.92; }

        /* ════════ LAYOUT VIEWS ════════ */
        .layout-container {
            margin: 24px auto 40px;
            display: flex;
            justify-content: center;
        }

        /* ── 1. BASEUS SHEET INVOICE (A4 & A5) ── */
        .baseus-invoice-card {
            background: #ffffff;
            border: 1px solid var(--border);
            box-shadow: 0 6px 24px rgba(0,0,0,0.06);
            border-radius: 12px;
            padding: 32px 36px;
            width: 860px;
            max-width: 100%;
            box-sizing: border-box;
        }

        .mode-a5 .baseus-invoice-card {
            width: 650px;
            padding: 22px 26px;
            font-size: 12px;
        }

        .inv-head-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
            gap: 16px;
        }

        .inv-brand-box {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .inv-brand-logo {
            max-height: 48px;
            max-width: 180px;
            object-fit: contain;
            margin-bottom: 2px;
        }

        .inv-brand-fallback {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .inv-brand-sub {
            font-size: 11px;
            color: var(--muted);
            font-weight: 500;
        }

        .inv-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            margin-top: 6px;
            width: fit-content;
        }
        .inv-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
        }
        .inv-status-dot.danger { background: #ef4444; }
        .inv-status-dot.warning { background: #f59e0b; }

        .inv-head-right {
            display: flex;
            align-items: center;
            gap: 18px;
            text-align: right;
        }

        .inv-qr-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .inv-qr-img {
            width: 72px;
            height: 72px;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 2px;
            background: #fff;
        }

        .inv-qr-lbl {
            font-size: 10px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .inv-title-box {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .inv-label-tag {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .inv-num-big {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            line-height: 1.1;
            margin-bottom: 3px;
        }

        .inv-date-text {
            font-size: 12px;
            color: var(--muted);
            font-weight: 500;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid var(--border);
        }

        .info-col-title {
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            display: block;
        }

        .info-col-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .info-col-text {
            font-size: 12px;
            color: #475569;
            line-height: 1.5;
            word-break: break-word;
        }

        .delivery-method-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 6px;
        }

        .info-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            padding: 3px 0;
        }
        .info-meta-lbl { color: var(--muted); font-weight: 500; }
        .info-meta-val { color: #0f172a; font-weight: 600; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
        }

        .items-table thead tr {
            border-bottom: 1px solid #cbd5e1;
        }

        .items-table thead th {
            padding: 10px 8px;
            font-size: 11px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            text-align: left;
        }

        .items-table thead th.center { text-align: center; }
        .items-table thead th.right { text-align: right; }

        .items-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .items-table tbody td {
            padding: 12px 8px;
            vertical-align: middle;
            font-size: 12.5px;
            color: #1e293b;
        }

        .items-table tbody td.center { text-align: center; }
        .items-table tbody td.right { text-align: right; font-weight: 600; }

        .prod-info-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .item-thumb {
            width: 44px;
            height: 44px;
            border-radius: 6px;
            object-fit: cover;
            background: #f8fafc;
            border: 1px solid var(--border);
            flex-shrink: 0;
        }

        .item-thumb-ph {
            width: 44px;
            height: 44px;
            border-radius: 6px;
            background: #f8fafc;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #94a3b8;
            flex-shrink: 0;
        }

        .prod-title {
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
        }

        .prod-sku {
            font-size: 11px;
            color: #64748b;
            font-family: var(--font-mono);
            margin-top: 2px;
        }

        .qty-badge {
            display: inline-block;
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 3px 10px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 12px;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
            padding: 10px 0 16px;
        }

        .totals-box {
            width: 320px;
            max-width: 100%;
        }

        .tot-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            font-size: 13px;
        }

        .tot-lbl { color: #64748b; font-weight: 500; }
        .tot-val { color: #0f172a; font-weight: 600; }

        .tot-card-banner {
            background: #0f172a;
            color: #ffffff;
            border-radius: 8px;
            padding: 14px 18px;
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tot-card-banner .lbl {
            font-size: 12px;
            font-weight: 700;
            color: #cbd5e1;
        }
        .tot-card-banner .lbl small {
            display: block;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 400;
        }

        .tot-card-banner .amt {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.3px;
        }

        .terms-wrap {
            margin-top: 14px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
            font-family: var(--font-bn);
        }

        .terms-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 12px;
        }

        .terms-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
        }

        .terms-card-title {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .terms-card-text {
            font-size: 11px;
            color: #475569;
            line-height: 1.45;
        }

        .terms-alert {
            background: #fff1f2;
            border-left: 3px solid #e11d48;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 11px;
            color: #be123c;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding-top: 14px;
            border-top: 1px solid var(--border);
            font-size: 11.5px;
        }

        .foot-title {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: block;
        }

        .foot-text {
            color: #334155;
            line-height: 1.5;
        }


        /* ── 2. 4" × 6" THERMAL SHIPPING LABEL (XP-420B) ── */
        .thermal-label-card {
            display: none;
            background: #ffffff;
            border: 1px solid #000;
            width: 100mm;
            min-height: 150mm;
            padding: 6mm;
            box-sizing: border-box;
            color: #000;
            font-family: var(--font);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        .lbl-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 4mm;
            margin-bottom: 4mm;
        }

        .lbl-store {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .lbl-tracking-barcode {
            text-align: center;
            padding: 4mm 0;
            border-bottom: 2px dashed #000;
            margin-bottom: 4mm;
        }

        .barcode-strip {
            display: inline-block;
            letter-spacing: 2px;
            font-family: var(--font-mono);
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
        }

        .lbl-section-box {
            border: 1px solid #000;
            border-radius: 4px;
            padding: 3mm;
            margin-bottom: 3mm;
        }

        .lbl-sec-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #333;
            margin-bottom: 2px;
        }

        .lbl-recipient-name {
            font-size: 14px;
            font-weight: 800;
        }

        .lbl-recipient-phone {
            font-size: 13px;
            font-weight: 800;
            margin-top: 2px;
        }

        .lbl-items-mini-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin: 3mm 0;
        }

        .lbl-items-mini-table th {
            border-bottom: 1px solid #000;
            padding: 2px 0;
            text-align: left;
            font-size: 9.5px;
        }

        .lbl-items-mini-table td {
            padding: 2.5px 0;
            vertical-align: top;
        }

        .lbl-totals-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 800;
            padding-top: 2mm;
            border-top: 1px solid #000;
        }


        /* ── 3. POS THERMAL ROLL (80mm & 58mm) ── */
        .pos-thermal-card {
            display: none;
            background: #ffffff;
            color: #000;
            font-family: var(--font-mono);
            font-size: 12px;
            line-height: 1.35;
            padding: 12px 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px dashed #ccc;
            box-sizing: border-box;
        }

        .mode-80mm .pos-thermal-card {
            width: 80mm;
            max-width: 100%;
            font-size: 11.5px;
        }

        .mode-58mm .pos-thermal-card {
            width: 58mm;
            max-width: 100%;
            font-size: 10px;
            padding: 8px 6px;
        }

        .receipt-center { text-align: center; }
        .receipt-right  { text-align: right; }
        .receipt-bold   { font-weight: bold; }

        .receipt-store-title {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .receipt-divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .receipt-double-divider {
            border-top: 1px double #000;
            margin: 6px 0;
        }

        .receipt-meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .mode-58mm .receipt-meta-row { font-size: 9.5px; }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: 11px;
        }
        .mode-58mm .receipt-table { font-size: 9.5px; }

        .receipt-table th {
            border-bottom: 1px dashed #000;
            padding: 3px 0;
            text-align: left;
        }
        .receipt-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .receipt-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
        }
        .receipt-total-row.grand {
            font-size: 13px;
            font-weight: bold;
            padding: 4px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            margin: 4px 0;
        }


        /* ════════ ACTIVE VISIBILITY RULES ════════ */
        /* A4 & A5 Mode: Show Baseus Card */
        body.mode-a4 .baseus-invoice-card,
        body.mode-a5 .baseus-invoice-card {
            display: block;
        }
        body.mode-a4 .thermal-label-card,
        body.mode-a5 .thermal-label-card,
        body.mode-a4 .pos-thermal-card,
        body.mode-a5 .pos-thermal-card {
            display: none !important;
        }

        /* 4x6 Label Mode: Show Thermal Label Card */
        body.mode-4x6 .thermal-label-card {
            display: block;
        }
        body.mode-4x6 .baseus-invoice-card,
        body.mode-4x6 .pos-thermal-card {
            display: none !important;
        }

        /* 80mm & 58mm POS Mode: Show POS Thermal Card */
        body.mode-80mm .pos-thermal-card,
        body.mode-58mm .pos-thermal-card {
            display: block;
        }
        body.mode-80mm .baseus-invoice-card,
        body.mode-58mm .baseus-invoice-card,
        body.mode-80mm .thermal-label-card,
        body.mode-58mm .thermal-label-card {
            display: none !important;
        }


        /* ════════ PRINT RULES ════════ */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .top-toolbar {
                display: none !important;
            }

            .layout-container {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .baseus-invoice-card,
            .thermal-label-card,
            .pos-thermal-card {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>

    <style id="dynamicPrintPageStyle">
        @page { size: A4 portrait; margin: 8mm; }
    </style>
</head>
<body class="mode-a4">

{{-- ══ TOP TOOLBAR (WITH 5 PRINT PRESET BUTTONS) ══ --}}
<div class="top-toolbar">
    <div class="tb-left">
        <a href="javascript:history.back()" class="btn-act btn-act-back">
            ← Back
        </a>
        <div>
            <div class="tb-title">Invoice #{{ $invoice->invoice_number }}</div>
            <div class="tb-meta">{{ $invoice->created_at->format('d M Y, h:i A') }}</div>
        </div>
    </div>

    {{-- 5 SIZE PRESET SELECTORS --}}
    <div class="size-switcher">
        <button type="button" class="size-btn active" onclick="switchSize('a4', this)">
            <span>📄</span> A4 Sheet
        </button>
        <button type="button" class="size-btn" onclick="switchSize('a5', this)">
            <span>📑</span> A5 Compact
        </button>
        <button type="button" class="size-btn" onclick="switchSize('4x6', this)" title="Thermal Label (XP-420B, 100x150mm)">
            <span>🏷️</span> 4"×6" Label (XP-420B)
        </button>
        <button type="button" class="size-btn" onclick="switchSize('80mm', this)">
            <span>🧾</span> 80mm POS Roll
        </button>
        <button type="button" class="size-btn" onclick="switchSize('58mm', this)">
            <span>🎫</span> 58mm Mini
        </button>
    </div>

    <div class="tb-actions">
        <button type="button" class="btn-act btn-act-print" onclick="window.print()">
            🖨 Print Receipt
        </button>
    </div>
</div>

<div class="layout-container">

    {{-- ══════════════════════════════════════════════════════════════
         VIEW 1 · BASEUS FULL E-COMMERCE INVOICE (FOR A4 & A5)
         ══════════════════════════════════════════════════════════════ --}}
    <div class="baseus-invoice-card">
        {{-- HEADER --}}
        <div class="inv-head-row">
            <div class="inv-brand-box">
                @if($settings && $settings->logo)
                    <img class="inv-brand-logo" src="{{ asset($settings->logo) }}" alt="{{ $settings->website_name ?? 'Store' }}">
                @elseif($settings && $settings->app_logo)
                    <img class="inv-brand-logo" src="{{ asset($settings->app_logo) }}" alt="{{ $settings->website_name ?? 'Store' }}">
                @else
                    <div class="inv-brand-fallback">{{ $settings->website_name ?? 'JHR BAZAR' }}</div>
                @endif
                <div class="inv-brand-sub">Official Flagship Store in Bangladesh · Powered by {{ $settings->website_name ?? 'JhrBazar' }}</div>

                <div class="inv-status-pill">
                    <span class="inv-status-dot {{ in_array(strtolower($orderStatus), ['cancelled', 'failed']) ? 'danger' : (in_array(strtolower($orderStatus), ['pending', 'processing']) ? 'warning' : '') }}"></span>
                    <span>{{ ucfirst($orderStatus) }} ({{ ucfirst($paymentMethod) }})</span>
                </div>
            </div>

            <div class="inv-head-right">
                <div class="inv-qr-box">
                    <img class="inv-qr-img" src="{{ $qrApi }}" alt="Track Order QR">
                    <span class="inv-qr-lbl">Track Order</span>
                </div>

                <div class="inv-title-box">
                    <span class="inv-label-tag">INVOICE</span>
                    <div class="inv-num-big">#{{ $invoice->invoice_number }}</div>
                    <div class="inv-date-text">Date: {{ $invoice->created_at->format('F d, Y') }}</div>
                </div>
            </div>
        </div>

        {{-- 3-COLUMN INFO BOX --}}
        <div class="info-grid">
            {{-- Billed To --}}
            <div>
                <span class="info-col-title">BILLED TO</span>
                <div class="info-col-name">{{ $custName ?: 'Valued Customer' }}</div>
                <div class="info-col-text">{{ $custAddress ?: 'Dhaka, Bangladesh' }}</div>
                <div class="info-col-text">Bangladesh</div>
                <div class="info-col-text" style="font-weight:700; margin-top:2px;">{{ $custPhone ?: '—' }}</div>
            </div>

            {{-- Shipped To --}}
            <div>
                <span class="info-col-title">SHIPPED TO</span>
                <div class="info-col-name">{{ $custName ?: 'Valued Customer' }}</div>
                <div class="info-col-text">{{ $custAddress ?: 'Dhaka, Bangladesh' }}</div>
                <div class="info-col-text">Bangladesh</div>
                <div class="delivery-method-pill">
                    ➔ {{ $deliveryArea }}
                </div>
            </div>

            {{-- Order Info --}}
            <div>
                <span class="info-col-title">ORDER INFO</span>
                <div class="info-meta-row">
                    <span class="info-meta-lbl">Order ID</span>
                    <span class="info-meta-val">#{{ $invoice->invoice_number }}</span>
                </div>
                <div class="info-meta-row">
                    <span class="info-meta-lbl">Date</span>
                    <span class="info-meta-val">{{ $invoice->created_at->format('F d, Y') }}</span>
                </div>
                <div class="info-meta-row">
                    <span class="info-meta-lbl">Email</span>
                    <span class="info-meta-val">{{ $custEmail ?: '—' }}</span>
                </div>
                <div class="info-meta-row">
                    <span class="info-meta-lbl">Payment</span>
                    <span class="info-meta-val">{{ ucfirst($paymentMethod) }}</span>
                </div>
            </div>
        </div>

        {{-- ORDER ITEMS TABLE --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 55%;">PRODUCT</th>
                    <th class="center" style="width: 15%;">QTY</th>
                    <th class="right" style="width: 15%;">UNIT PRICE</th>
                    <th class="right" style="width: 15%;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $pName = $item['name'] ?? ($item['title'] ?? 'Product');
                        $pSku = $item['sku'] ?? ($item['barcode'] ?? '');
                        $pQty = (int)($item['qty'] ?? 1);
                        $pPrice = (float)($item['price'] ?? ($item['selling_price'] ?? 0));
                        $pLineTotal = (float)($item['line_total'] ?? ($pPrice * $pQty));
                        $thumb = $item['thumbnail'] ?? null;
                    @endphp
                    <tr>
                        <td>
                            <div class="prod-info-wrap">
                                @if($thumb)
                                    <img class="item-thumb" src="{{ str_starts_with($thumb, 'http') ? $thumb : asset($thumb) }}" alt="{{ $pName }}" onerror="this.style.display='none'">
                                @else
                                    <div class="item-thumb-ph">📦</div>
                                @endif
                                <div>
                                    <div class="prod-title">{{ $pName }}</div>
                                    @if($pSku)
                                        <div class="prod-sku">SKU: {{ $pSku }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="center">
                            <span class="qty-badge">{{ $pQty }}</span>
                        </td>
                        <td class="right">
                            {{ fmtAmt($pPrice) }}{{ $cur }}
                        </td>
                        <td class="right" style="font-weight: 700; color: #0f172a;">
                            {{ fmtAmt($pLineTotal) }}{{ $cur }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="center" style="padding: 24px; color: var(--muted);">
                            No items found in this order.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- TOTALS SECTION --}}
        <div class="totals-section">
            <div class="totals-box">
                <div class="tot-row">
                    <span class="tot-lbl">Subtotal</span>
                    <span class="tot-val">{{ fmtAmt($invoice->sub_total) }}{{ $cur }}</span>
                </div>
                @if($deliveryCharge > 0)
                <div class="tot-row">
                    <span class="tot-lbl">Shipping ({{ $deliveryArea }})</span>
                    <span class="tot-val">{{ fmtAmt($deliveryCharge) }}{{ $cur }}</span>
                </div>
                @endif
                @if(($invoice->discount ?? 0) > 0)
                <div class="tot-row">
                    <span class="tot-lbl">Discount</span>
                    <span class="tot-val" style="color:#16a34a;">-{{ fmtAmt($invoice->discount) }}{{ $cur }}</span>
                </div>
                @endif
                @if(($invoice->tax_amount ?? 0) > 0)
                <div class="tot-row">
                    <span class="tot-lbl">Tax</span>
                    <span class="tot-val">{{ fmtAmt($invoice->tax_amount) }}{{ $cur }}</span>
                </div>
                @endif

                <div class="tot-card-banner">
                    <div class="lbl">
                        Total Amount
                        <small>incl. all taxes & fees</small>
                    </div>
                    <div class="amt">{{ $cur }} {{ fmtAmt($invoice->grand_total) }}</div>
                </div>
            </div>
        </div>

        {{-- TERMS & WARRANTY (EXACT BENGALI COPY) --}}
        <div class="terms-wrap">
            <div class="terms-grid">
                <div class="terms-card">
                    <div class="terms-card-title">🎥 আনবক্সিং ভিডিও বাধ্যতামূলক</div>
                    <div class="terms-card-text">
                        পার্সেল খোলার আগে অবশ্যই ভিডিও ধারণ করুন। ইনভয়েসে থাকা QR স্ক্যান করে ৩ দিনের মধ্যে আমাদের ফেসবুক গ্রুপে আপলোড করুন।
                    </div>
                </div>
                <div class="terms-card">
                    <div class="terms-card-title">🛡️ ওয়ারেন্টি শর্তাবলী</div>
                    <div class="terms-card-text">
                        ভিডিও ছাড়া কোনো ড্যামেজ / মিসিং / ওয়ারেন্টি অভিযোগ গ্রহণযোগ্য নয়। Replace/Exchange এর জন্য বক্স ও প্রোডাক্ট অবশ্যই অক্ষত থাকতে হবে, ক্ষতিগ্রস্ত হলে প্রযোজ্য নয়।
                    </div>
                </div>
            </div>

            <div class="terms-alert">
                বিঃদ্রঃ পুড়ে গেলে, ভুল ব্যবহারে, ফিজিক্যাল বা পানিজনিত ক্ষতিতে ওয়ারেন্টি প্রযোজ্য নয়।
            </div>
        </div>

        {{-- STORE ADDRESS & CONTACT FOOTER --}}
        <div class="footer-grid">
            <div>
                <span class="foot-title">Our Shop Address</span>
                <div class="foot-text">
                    <strong>Main Branch:</strong> {{ $settings->address ?? 'Shop 504 lift 04, Police Plaza Concord, Gulshan, Hatirjheel, Dhaka' }}<br>
                    @if($settings?->website_url)
                        <span>{{ $settings->website_url }}</span>
                    @endif
                </div>
            </div>

            <div>
                <span class="foot-title">Our Contact Info</span>
                <div class="foot-text">
                    <strong>Phone:</strong> {{ $settings->mobile_number ?? '+8801826476476' }}<br>
                    <strong>Email:</strong> {{ $settings->email_address ?? 'support@jhrbazar.com' }}<br>
                    @if($settings?->hotline_number)
                        <strong>Hotline:</strong> {{ $settings->hotline_number }}
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════
         VIEW 2 · 4" × 6" THERMAL SHIPPING LABEL (XPRINTER XP-420B)
         ══════════════════════════════════════════════════════════════ --}}
    <div class="thermal-label-card">
        <div class="lbl-header">
            <div>
                <div class="lbl-store">{{ $settings->website_name ?? 'JHR BAZAR' }}</div>
                <div style="font-size:10px; color:#444;">{{ $settings->mobile_number ?? '' }}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:11px; font-weight:bold;">#{{ $invoice->invoice_number }}</div>
                <div style="font-size:9.5px;">{{ $invoice->created_at->format('d/m/Y') }}</div>
            </div>
        </div>

        {{-- Tracking Barcode & QR Code --}}
        <div style="display:flex; align-items:center; justify-content:space-between; border-bottom:1.5px solid #000; padding-bottom:3mm; margin-bottom:3mm;">
            <div style="flex:1;">
                <svg viewBox="0 0 160 40" style="height:35px; width:100%;">
                    @for($i=0; $i<36; $i++)
                        @php $w = ($i % 3 == 0) ? 3 : (($i % 2 == 0) ? 2 : 1); $x = $i * 4.2; @endphp
                        <rect x="{{ $x }}" y="0" width="{{ $w }}" height="32" fill="#000"/>
                    @endfor
                </svg>
                <div class="barcode-strip" style="font-size:11px; text-align:center;">*{{ $invoice->invoice_number }}*</div>
            </div>
            <div style="margin-left:8px; text-align:center;">
                <img src="{{ $qrApi }}" style="width:48px; height:48px; border:1px solid #000;" alt="QR">
            </div>
        </div>

        {{-- Recipient Info Box --}}
        <div class="lbl-section-box">
            <div class="lbl-sec-title">SHIP TO / CUSTOMER</div>
            <div class="lbl-recipient-name">{{ $custName }}</div>
            <div style="font-size:11px; line-height:1.35; margin-top:2px;">{{ $custAddress }}</div>
            <div class="lbl-recipient-phone">📞 {{ $custPhone }}</div>
            <div style="font-size:10.5px; font-weight:bold; margin-top:3px; background:#eee; padding:2px 4px; display:inline-block;">
                {{ $deliveryArea }}
            </div>
        </div>

        {{-- Itemized summary --}}
        <div class="lbl-section-box" style="margin-bottom:2mm;">
            <div class="lbl-sec-title">ITEMS (TOTAL QTY: {{ $totalQty }})</div>
            <table class="lbl-items-mini-table">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th style="text-align:center; width:35px;">QTY</th>
                        <th style="text-align:right; width:55px;">PRICE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item['name'] ?? ($item['title'] ?? 'Product') }}</td>
                            <td style="text-align:center;">{{ $item['qty'] ?? 1 }}</td>
                            <td style="text-align:right;">{{ fmtAmt($item['line_total'] ?? ($item['price'] ?? 0)) }}{{ $cur }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="lbl-totals-row">
                <span>COLLECT CASH (COD):</span>
                <span>{{ $cur }} {{ fmtAmt($invoice->grand_total) }}</span>
            </div>
        </div>

        <div style="font-size:8.5px; text-align:center; color:#333; margin-top:2mm; font-family:var(--font-bn);">
            * পার্সেল খোলার সময় বাধ্যতামূলক আনবক্সিং ভিডিও করুন। ধন্যবাদ!
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════
         VIEW 3 · 80mm & 58mm POS THERMAL RECEIPT
         ══════════════════════════════════════════════════════════════ --}}
    <div class="pos-thermal-card">
        <div class="receipt-center">
            <div class="receipt-store-title">{{ $settings->website_name ?? 'JHR BAZAR' }}</div>
            @if($settings?->address)
                <div>{{ $settings->address }}</div>
            @endif
            @if($settings?->mobile_number)
                <div>Tel: {{ $settings->mobile_number }}</div>
            @endif
        </div>

        <div class="receipt-divider"></div>

        <div class="receipt-meta-row">
            <span>Invoice #: <strong>{{ $invoice->invoice_number }}</strong></span>
            <span>{{ $invoice->created_at->format('d/m/y H:i') }}</span>
        </div>
        <div class="receipt-meta-row">
            <span>Cust: {{ $custName }}</span>
            <span>{{ $custPhone }}</span>
        </div>

        <div class="receipt-divider"></div>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th style="width:55%;">ITEM</th>
                    <th style="width:15%; text-align:center;">QTY</th>
                    <th style="width:30%; text-align:right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    @php
                        $pName = $item['name'] ?? ($item['title'] ?? 'Product');
                        $pQty = (int)($item['qty'] ?? 1);
                        $pPrice = (float)($item['price'] ?? 0);
                        $pLineTotal = (float)($item['line_total'] ?? ($pPrice * $pQty));
                    @endphp
                    <tr>
                        <td>
                            <div>{{ $pName }}</div>
                            <div style="font-size:9.5px; color:#555;">@ {{ fmtAmt($pPrice) }}</div>
                        </td>
                        <td style="text-align:center;">{{ $pQty }}</td>
                        <td style="text-align:right;">{{ fmtAmt($pLineTotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="receipt-divider"></div>

        <div class="receipt-total-row">
            <span>Sub Total:</span>
            <span>{{ $cur }}{{ fmtAmt($invoice->sub_total) }}</span>
        </div>
        @if($deliveryCharge > 0)
        <div class="receipt-total-row">
            <span>Delivery Charge:</span>
            <span>{{ $cur }}{{ fmtAmt($deliveryCharge) }}</span>
        </div>
        @endif
        @if(($invoice->discount ?? 0) > 0)
        <div class="receipt-total-row">
            <span>Discount:</span>
            <span>-{{ $cur }}{{ fmtAmt($invoice->discount) }}</span>
        </div>
        @endif

        <div class="receipt-total-row grand">
            <span>GRAND TOTAL:</span>
            <span>{{ $cur }}{{ fmtAmt($invoice->grand_total) }}</span>
        </div>

        <div class="receipt-total-row">
            <span>Paid ({{ ucfirst($paymentMethod) }}):</span>
            <span>{{ $cur }}{{ fmtAmt($invoice->received_amount ?? $invoice->grand_total) }}</span>
        </div>
        @if(($invoice->change_amount ?? 0) > 0)
        <div class="receipt-total-row">
            <span>Change:</span>
            <span>{{ $cur }}{{ fmtAmt($invoice->change_amount) }}</span>
        </div>
        @endif

        <div class="receipt-divider"></div>

        {{-- Barcode for POS scanner --}}
        <div class="receipt-center" style="margin: 6px 0;">
            <svg viewBox="0 0 160 36" style="height:30px; max-width:80%;">
                @for($i=0; $i<36; $i++)
                    @php $w = ($i % 3 == 0) ? 3 : (($i % 2 == 0) ? 2 : 1); $x = $i * 4.2; @endphp
                    <rect x="{{ $x }}" y="0" width="{{ $w }}" height="30" fill="#000"/>
                @endfor
            </svg>
            <div style="font-size:10.5px; font-weight:bold; letter-spacing:1px; margin-top:2px;">{{ $invoice->invoice_number }}</div>
        </div>

        <div class="receipt-center" style="font-size:10px; margin-top:4px;">
            *** THANK YOU FOR SHOPPING! ***<br>
            Please visit again
        </div>
    </div>

</div>

<script>
const PAGE_SIZES = {
    'a4':   { class: 'mode-a4',   page: 'A4 portrait',   margin: '8mm' },
    'a5':   { class: 'mode-a5',   page: 'A5 portrait',   margin: '6mm' },
    '4x6':  { class: 'mode-4x6',  page: '100mm 150mm',   margin: '3mm' },
    '80mm': { class: 'mode-80mm', page: '80mm auto',     margin: '2mm' },
    '58mm': { class: 'mode-58mm', page: '58mm auto',     margin: '1.5mm' }
};

function switchSize(sizeKey, btn) {
    const config = PAGE_SIZES[sizeKey] || PAGE_SIZES['a4'];
    document.body.className = config.class;

    document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const styleEl = document.getElementById('dynamicPrintPageStyle');
    styleEl.innerHTML = `@page { size: ${config.page}; margin: ${config.margin}; }`;

    localStorage.setItem('preferred_invoice_size', sizeKey);
}

// Restore user's preferred print size
document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('preferred_invoice_size');
    const urlParams = new URLSearchParams(window.location.search);
    const urlSize = urlParams.get('size');
    const targetSize = urlSize || saved || 'a4';

    const targetBtn = Array.from(document.querySelectorAll('.size-btn')).find(b => b.getAttribute('onclick')?.includes(`'${targetSize}'`));
    if (targetBtn) {
        switchSize(targetSize, targetBtn);
    }

    if (urlParams.get('autoprint') === '1') {
        setTimeout(() => window.print(), 600);
    }
});
</script>

</body>
</html>
