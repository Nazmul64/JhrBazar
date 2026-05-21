@extends('admin.master')
@section('content')
<style>
    :root {
        --brand:       #e8174a;
        --brand-hover: #c9113e;
        --dark:        #1a1d23;
        --muted:       #6b7280;
        --border:      #e5e7eb;
        --surface:     #f8f9fc;
        --r-lg: 14px; --r-md: 10px; --r-sm: 7px;
    }

    .barcode-card { background:#fff; border-radius:var(--r-lg); box-shadow:0 1px 4px rgba(0,0,0,.06); border:1px solid var(--border); padding:30px; margin-bottom: 24px; }
    .barcode-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; }
    .barcode-item { border: 1px dashed #d1d5db; padding: 15px; text-align: center; border-radius: var(--r-md); background: #fff; width: 100%; max-width: 250px; margin: 0 auto; }
    .barcode-label { font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 2px; }
    .barcode-sub { font-size: 11px; color: var(--muted); margin-bottom: 5px; }
    .barcode-price { font-size: 13px; font-weight: 700; color: var(--brand); margin-bottom: 5px; }
    .barcode-img-gen { width: 100%; height: auto; display: block; margin: 0 auto; }
    .barcode-code { font-size: 10px; font-weight: 600; color: var(--dark); margin-top: 5px; }
    .control-panel { background: var(--surface); padding: 20px; border-radius: var(--r-md); border: 1px solid var(--border); }
    
    @media print {
        header, footer, .sidebar, .ph, .control-panel, .btn, .breadcrumb, .navbar, .col-lg-5, .item-selector { display: none !important; }
        #main-wrapper, #main, .page-wrapper, .container-fluid, .barcode-card { margin: 0 !important; padding: 0 !important; border: none !important; box-shadow: none !important; width: 100% !important; }
        .barcode-preview-grid { display: grid !important; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)) !important; gap: 10px !important; }
        .barcode-item { break-inside: avoid; border: 1px solid #ddd !important; margin-bottom: 10px !important; }
        .barcode-item:not(.selected-for-print) { display: none !important; }
        @page { margin: 5mm; }
    }
    .item-selector { position: absolute; top: 10px; right: 10px; z-index: 10; }
    .barcode-item { position: relative; cursor: pointer; transition: all 0.2s; }
    .barcode-item.selected-for-print { border-color: var(--brand); background: #fffafb; }
</style>

<div class="container-fluid px-4 py-4">
    <div class="ph d-flex align-items-center justify-content-between mb-4">
        <h4 class="ph-title">Generate Barcode (Digital Product)</h4>
        <a href="{{ route('seller.digital_product.index') }}" class="btn btn-sm btn-outline-secondary px-3"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>

    <div class="barcode-card">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="d-flex gap-3 mb-4">
                    <img src="{{ asset($product->thumbnail) }}" alt="" style="width:120px; height:120px; object-fit:cover; border-radius:10px; border:1px solid var(--border);">
                    <div>
                        <h5 class="fw-bold mb-1">{{ $product->name }}</h5>
                        <p class="text-muted small mb-2">{{ Str::limit($product->short_description, 100) }}</p>
                        <div class="fw-bold text-dark">৳{{ number_format($product->selling_price, 0) }}</div>
                        <div class="mt-2 small text-muted">SKU: <span class="fw-bold text-dark">{{ $product->sku }}</span></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="control-panel mb-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <input type="number" id="barcode_qty" value="{{ $print_quantity }}" class="form-control" style="width: 80px;" onchange="updateBarcodeGrid()">
                        <div class="form-check m-0">
                            <input class="form-check-input" type="checkbox" id="select_all" checked onchange="toggleAll(this)">
                            <label class="form-check-label small fw-bold" for="select_all">Select All</label>
                        </div>
                    </div>
                    <button class="btn btn-outline-success" title="Print" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                </div>

                <div id="barcode_grid" class="barcode-preview-grid">
                    @for($i = 0; $i < $print_quantity; $i++)
                        <div class="barcode-item selected-for-print" onclick="toggleItem(this)">
                            <div class="item-selector">
                                <input type="checkbox" class="form-check-input print-chk" checked>
                            </div>
                            <div class="barcode-label">JHR Bazar (Digital)</div>
                            <div class="barcode-sub">{{ Str::limit($product->name, 20) }}</div>
                            <div class="barcode-price">৳{{ number_format($product->selling_price, 0) }}</div>
                            <img class="barcode-img-gen" data-value="{{ $product->sku }}">
                            <div class="barcode-code">Code: {{ $product->sku }}</div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    function generateBarcodes() {
        document.querySelectorAll('.barcode-img-gen').forEach(el => {
            JsBarcode(el, el.getAttribute('data-value'), { format: "CODE128", width: 2, height: 40, displayValue: false });
        });
    }

    function toggleItem(el) {
        el.classList.toggle('selected-for-print');
        const chk = el.querySelector('.print-chk');
        chk.checked = el.classList.contains('selected-for-print');
    }

    function toggleAll(chk) {
        document.querySelectorAll('.barcode-item').forEach(item => {
            if(chk.checked) {
                item.classList.add('selected-for-print');
                item.querySelector('.print-chk').checked = true;
            } else {
                item.classList.remove('selected-for-print');
                item.querySelector('.print-chk').checked = false;
            }
        });
    }

    function updateBarcodeGrid() {
        const qty = document.getElementById('barcode_qty').value;
        const grid = document.getElementById('barcode_grid');
        const isAllChecked = document.getElementById('select_all').checked;
        let html = '';
        for (let i = 0; i < qty; i++) {
            html += `
                <div class="barcode-item ${isAllChecked ? 'selected-for-print' : ''}" onclick="toggleItem(this)">
                    <div class="item-selector">
                        <input type="checkbox" class="form-check-input print-chk" ${isAllChecked ? 'checked' : ''}>
                    </div>
                    <div class="barcode-label">JHR Bazar (Digital)</div>
                    <div class="barcode-sub">{{ Str::limit($product->name, 20) }}</div>
                    <div class="barcode-price">৳{{ number_format($product->selling_price, 0) }}</div>
                    <img class="barcode-img-gen" data-value="{{ $product->sku }}">
                    <div class="barcode-code">Code: {{ $product->sku }}</div>
                </div>`;
        }
        grid.innerHTML = html;
        generateBarcodes();
    }
    document.addEventListener("DOMContentLoaded", generateBarcodes);
</script>
@endsection
