{{-- resources/views/admin/product/barcode.blade.php --}}
@extends('admin.master')
@section('content')
<style>
    :root {
        --brand:       #e8174a;
        --brand-light: rgba(232,23,74,.08);
        --brand-hover: #c9113e;
        --dark:        #1a1d23;
        --muted:       #6b7280;
        --border:      #e5e7eb;
        --surface:     #f8f9fc;
        --shadow:      0 1px 4px rgba(0,0,0,.06), 0 4px 20px rgba(0,0,0,.07);
        --r-lg: 16px; --r-md: 10px; --r-sm: 8px;
        --ease: all .18s ease;
    }

    /* ── Breadcrumb ── */
    .bc-nav { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--muted); margin-bottom:1rem; }
    .bc-nav a { color:var(--brand); text-decoration:none; font-weight:600; }
    .bc-nav a:hover { text-decoration:underline; }
    .bc-sep { font-size:9px; color:#d1d5db; }

    /* ══════════════════════════════════════
       MAIN CARD — two-column layout
    ══════════════════════════════════════ */
    .main-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        box-shadow: var(--shadow);
        display: flex;
        overflow: hidden;
        min-height: 460px;
    }

    /* ── LEFT PANEL ── */
    .panel-left {
        flex: 0 0 52%;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--border);
    }
    .product-image-area {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 30px 20px;
        background: #fff;
        overflow: hidden;
    }
    .product-image-area img {
        max-width: 100%;
        max-height: 280px;
        object-fit: contain;
        display: block;
    }
    .product-image-ph {
        width: 180px; height: 180px;
        background: var(--surface);
        border-radius: var(--r-md);
        display: flex; align-items: center; justify-content: center;
        color: #d1d5db; font-size: 52px;
        border: 1px solid var(--border);
    }
    .product-info-area {
        padding: 16px 26px 24px;
        border-top: 1px solid var(--border);
    }
    .product-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 10px;
        line-height: 1.4;
    }
    .product-meta-row {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    .p-price {
        font-size: 18px;
        font-weight: 800;
        color: var(--dark);
    }
    .p-rating {
        display: flex; align-items: center; gap: 5px;
        font-size: 13px; color: var(--muted);
    }
    .p-rating i { color: #f59e0b; font-size: 14px; }
    .p-code {
        display: flex; align-items: center; gap: 5px;
        font-size: 13px; color: var(--muted);
        margin-left: auto;
    }
    .p-code strong {
        color: var(--dark);
        font-size: 14px;
        font-family: monospace;
        font-weight: 700;
    }
    .product-desc {
        font-size: 12.5px;
        color: var(--muted);
        line-height: 1.6;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ── RIGHT PANEL ── */
    .panel-right {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 18px 20px 24px;
        background: var(--surface);
    }

    /* Controls bar */
    .ctrl-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
    }
    .ctrl-qty-input {
        flex: 1;
        border: 1.5px solid var(--border);
        border-radius: var(--r-sm);
        padding: 9px 14px;
        font-size: 14px;
        font-weight: 700;
        color: var(--dark);
        background: #fff;
        outline: none;
        transition: border-color .15s;
    }
    .ctrl-qty-input:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(232,23,74,.1);
    }
    .ctrl-btn {
        width: 40px; height: 40px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: var(--r-sm);
        border: 1.5px solid;
        background: #fff;
        cursor: pointer;
        font-size: 16px;
        transition: var(--ease);
        flex-shrink: 0;
        text-decoration: none;
    }
    /* Barcode btn — red */
    .ctrl-btn-barcode {
        border-color: rgba(232,23,74,.4);
        color: var(--brand);
    }
    .ctrl-btn-barcode:hover {
        background: var(--brand);
        border-color: var(--brand);
        color: #fff;
    }
    /* Refresh btn — gray */
    .ctrl-btn-refresh {
        border-color: rgba(107,114,128,.3);
        color: var(--muted);
    }
    .ctrl-btn-refresh:hover {
        background: var(--muted);
        border-color: var(--muted);
        color: #fff;
    }
    /* Print btn — green */
    .ctrl-btn-print {
        border-color: rgba(22,163,74,.4);
        color: #16a34a;
    }
    .ctrl-btn-print:hover {
        background: #16a34a;
        border-color: #16a34a;
        color: #fff;
    }

    /* Barcode grid */
    .bc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        align-content: start;
    }
    .bc-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--r-sm);
        padding: 14px 12px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: box-shadow .15s;
    }
    .bc-card:hover {
        box-shadow: 0 3px 12px rgba(0,0,0,.08);
    }
    .bc-shop-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 2px;
    }
    .bc-prod-name {
        font-size: 11px;
        color: var(--muted);
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        width: 100%;
    }
    .bc-prod-price {
        font-size: 13px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 8px;
    }
    .bc-code-text {
        font-size: 11px;
        color: var(--muted);
        margin-top: 6px;
        font-family: monospace;
    }
    .bc-svg { display:block; margin:0 auto; }

    /* ── Print styles ── */
    @media print {
        body * { visibility: hidden; }
        #printArea, #printArea * { visibility: visible; }
        #printArea { position: fixed; top: 0; left: 0; width: 100%; padding: 12px; }
        .print-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .print-card { border: 1px solid #ccc; border-radius: 6px; padding: 12px; text-align: center; page-break-inside: avoid; break-inside: avoid; }
        .print-card .bc-shop-name  { font-size: 13px; font-weight: 700; margin-bottom: 2px; }
        .print-card .bc-prod-name  { font-size: 10px; color: #555; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .print-card .bc-prod-price { font-size: 12px; font-weight: 700; margin-bottom: 6px; }
        .print-card .bc-svg        { display: block; margin: 0 auto; }
        .print-card .bc-code-text  { font-size: 10px; color: #555; margin-top: 4px; font-family: monospace; }
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .main-card { flex-direction: column; }
        .panel-left { flex: none; border-right: none; border-bottom: 1px solid var(--border); }
        .bc-grid { grid-template-columns: 1fr 1fr; }
    }
</style>

{{-- Breadcrumb --}}
<div class="bc-nav">
    <a href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Products</a>
    <i class="bi bi-chevron-right bc-sep"></i>
    <span>Barcode</span>
</div>

{{-- ════ MAIN CARD ════ --}}
<div class="main-card">

    {{-- ════════════════════════════════
         LEFT PANEL — Product Image + Info
    ════════════════════════════════ --}}
    <div class="panel-left">

        {{-- Large product image --}}
        <div class="product-image-area">
            @if($product->thumbnail)
                {{-- path: "uploads/product/filename.ext" → asset() directly --}}
                <img src="{{ asset($product->thumbnail) }}"
                     alt="{{ $product->name }}"
                     id="mainProductImg"
                     onerror="this.style.display='none';
                              document.getElementById('imgPh').style.display='flex';">
                <div class="product-image-ph" id="imgPh" style="display:none;">
                    <i class="bi bi-image"></i>
                </div>
            @else
                <div class="product-image-ph"><i class="bi bi-image"></i></div>
            @endif
        </div>

        {{-- Product name / price / rating / code / description --}}
        <div class="product-info-area">
            <p class="product-name">{{ $product->name }}</p>

            <div class="product-meta-row">
                <span class="p-price">${{ number_format($product->selling_price, 0) }}</span>

                <span class="p-rating">
                    <i class="bi bi-star-fill"></i>
                    ({{ number_format($product->rating ?? 0, 1) }})
                </span>

                <span class="p-code">
                    Code: <strong>{{ $product->barcode ?? $product->sku }}</strong>
                </span>
            </div>

            @if($product->short_description)
                <p class="product-desc">{{ $product->short_description }}</p>
            @endif
        </div>

    </div>

    {{-- ════════════════════════════════
         RIGHT PANEL — Controls + Barcodes
    ════════════════════════════════ --}}
    <div class="panel-right">

        {{-- Control bar --}}
        <div class="ctrl-bar">
            {{-- Qty input --}}
            <input type="number"
                   id="qtyInput"
                   class="ctrl-qty-input"
                   value="4"
                   min="1"
                   max="100"
                   onchange="renderBarcodes()">

            {{-- Barcode view btn (red) --}}
            <button type="button"
                    class="ctrl-btn ctrl-btn-barcode"
                    title="Regenerate barcodes"
                    onclick="renderBarcodes()">
                <i class="bi bi-upc"></i>
            </button>

            {{-- Refresh btn (gray) --}}
            <button type="button"
                    class="ctrl-btn ctrl-btn-refresh"
                    title="Refresh"
                    onclick="renderBarcodes()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>

            {{-- Print btn (green) --}}
            <button type="button"
                    class="ctrl-btn ctrl-btn-print"
                    title="Print barcodes"
                    onclick="printBarcodes()">
                <i class="bi bi-printer"></i>
            </button>
        </div>

        {{-- Barcode preview grid --}}
        <div class="bc-grid" id="barcodeGrid"></div>

    </div>

</div>

{{-- Hidden print area --}}
<div id="printArea" style="display:none;"></div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
/* ══════════════════════════════════════
   Product config (from Blade)
══════════════════════════════════════ */
var CFG = {
    name:  @json($product->name),
    price: '${{ number_format($product->selling_price, 2) }}',
    code:  @json($product->barcode ?? $product->sku),
    shop:  'Jhr Bazar'
};

/* ══════════════════════════════════════
   HTML escape utility (XSS safe)
══════════════════════════════════════ */
function esc(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(String(str)));
    return d.innerHTML;
}

/* ══════════════════════════════════════
   Build one barcode card element
   forPrint=false → .bc-card  (preview)
   forPrint=true  → .print-card (print)
══════════════════════════════════════ */
function makeCard(forPrint) {
    var short  = CFG.name.length > 22
                 ? CFG.name.substring(0, 22) + '...'
                 : CFG.name;
    var cls    = forPrint ? 'print-card' : 'bc-card';
    var svgId  = 'bc_' + Math.random().toString(36).substring(2, 10);

    var card       = document.createElement('div');
    card.className = cls;
    card.innerHTML = [
        '<div class="bc-shop-name">'  + esc(CFG.shop)  + '</div>',
        '<div class="bc-prod-name">'  + esc(short)      + '</div>',
        '<div class="bc-prod-price">' + esc(CFG.price)  + '</div>',
        '<svg id="' + svgId + '" class="bc-svg"></svg>',
        '<div class="bc-code-text">Code: ' + esc(CFG.code) + '</div>',
    ].join('');

    /* JsBarcode needs the element in the DOM first → defer with setTimeout */
    setTimeout(function () {
        try {
            JsBarcode('#' + svgId, CFG.code, {
                format:       'CODE128',
                width:        forPrint ? 1.4 : 1.6,
                height:       forPrint ? 40  : 48,
                displayValue: false,
                margin:       0,
                lineColor:    '#111',
            });
        } catch (e) {
            var el = document.getElementById(svgId);
            if (el) {
                el.outerHTML =
                    '<p style="color:#ef4444;font-size:11px;margin:6px 0;">' +
                    '⚠ Invalid barcode value</p>';
            }
        }
    }, 0);

    return card;
}

/* ══════════════════════════════════════
   Render preview grid
══════════════════════════════════════ */
function renderBarcodes() {
    var qty  = Math.max(1, Math.min(100,
               parseInt(document.getElementById('qtyInput').value) || 4));
    var grid = document.getElementById('barcodeGrid');
    grid.innerHTML = '';
    for (var i = 0; i < qty; i++) {
        grid.appendChild(makeCard(false));
    }
}

/* ══════════════════════════════════════
   Print
══════════════════════════════════════ */
function printBarcodes() {
    var qty  = Math.max(1,
               parseInt(document.getElementById('qtyInput').value) || 4);
    var pDiv = document.getElementById('printArea');
    pDiv.innerHTML = '';

    var pg       = document.createElement('div');
    pg.className = 'print-grid';
    for (var i = 0; i < qty; i++) {
        pg.appendChild(makeCard(true));
    }
    pDiv.appendChild(pg);
    pDiv.style.display = 'block';

    /* Give JsBarcode time to render before printing */
    setTimeout(function () {
        window.print();
        pDiv.style.display = 'none';
    }, 450);
}

/* ── Render on page load ── */
document.addEventListener('DOMContentLoaded', renderBarcodes);
</script>
@endsection
