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
    
    .barcode-preview-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .barcode-item { border: 1px dashed #d1d5db; padding: 20px; text-align: center; border-radius: var(--r-md); background: #fff; }
    .barcode-label { font-size: 14px; font-weight: 700; color: var(--dark); margin-bottom: 5px; }
    .barcode-sub { font-size: 12px; color: var(--muted); margin-bottom: 10px; }
    .barcode-price { font-size: 14px; font-weight: 700; color: var(--brand); margin-bottom: 10px; }
    
    .barcode-img { width: 100%; max-width: 200px; margin: 0 auto; }
    .barcode-code { font-size: 11px; font-weight: 600; color: var(--dark); margin-top: 5px; }

    .control-panel { background: var(--surface); padding: 20px; border-radius: var(--r-md); border: 1px solid var(--border); }
    .btn-print { background: #10b981; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; }
    .btn-print:hover { background: #059669; }
    
    @media print {
        #main, .ph, .control-panel, .btn-print, .action-icons, .sidebar, .header { display: none !important; }
        .barcode-card { border: none; box-shadow: none; padding: 0; }
        .barcode-preview-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    }
</style>

<div class="container-fluid px-4 py-4">
    <div class="ph d-flex align-items-center justify-content-between mb-4">
        <h4 class="ph-title">Generate Barcode</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('seller.product.index') }}" class="btn btn-sm btn-outline-secondary px-3"><i class="bi bi-arrow-left me-1"></i> Back to List</a>
        </div>
    </div>

    <div class="barcode-card">
        <div class="row g-4">
            {{-- Left: Product Info --}}
            <div class="col-lg-5">
                <div class="d-flex gap-3 mb-4">
                    <img src="{{ asset($product->thumbnail) }}" alt="" style="width:120px; height:120px; object-fit:cover; border-radius:10px; border:1px solid var(--border);">
                    <div>
                        <h5 class="fw-bold mb-1">{{ $product->name }}</h5>
                        <p class="text-muted small mb-2">{{ Str::limit($product->short_description, 100) }}</p>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark">${{ number_format($product->selling_price, 2) }}</span>
                            @if($product->discount_price > 0)
                                <span class="badge bg-danger">-${{ number_format($product->discount_price, 2) }}</span>
                            @endif
                        </div>
                        <div class="mt-2 small text-muted">Code: <span class="fw-bold text-dark">{{ $product->sku }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Right: Barcode Grid --}}
            <div class="col-lg-7">
                <div class="control-panel mb-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <input type="number" id="barcode_qty" value="{{ $print_quantity }}" class="form-control" style="width: 80px;" onchange="updateBarcodeGrid()">
                        <div class="btn-group">
                            <button class="btn btn-outline-danger" title="Regenerate"><i class="bi bi-arrow-clockwise"></i></button>
                            <button class="btn btn-outline-success" title="Print" onclick="window.print()"><i class="bi bi-printer"></i></button>
                        </div>
                    </div>
                    <div class="action-icons">
                        <button class="btn btn-outline-danger"><i class="bi bi-list-columns"></i></button>
                    </div>
                </div>

                <div id="barcode_grid" class="barcode-preview-grid">
                    @for($i = 0; $i < $print_quantity; $i++)
                        <div class="barcode-item">
                            <div class="barcode-label">JHR Bazar</div>
                            <div class="barcode-sub">{{ Str::limit($product->name, 20) }}</div>
                            <div class="barcode-price">${{ number_format($product->selling_price, 2) }}</div>
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
        const barcodeElements = document.querySelectorAll('.barcode-img-gen');
        barcodeElements.forEach(el => {
            const value = el.getAttribute('data-value');
            JsBarcode(el, value, {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 40,
                displayValue: false
            });
        });
    }

    function updateBarcodeGrid() {
        const qty = document.getElementById('barcode_qty').value;
        const grid = document.getElementById('barcode_grid');
        const sku = "{{ $product->sku }}";
        const name = "{{ Str::limit($product->name, 20) }}";
        const price = "{{ number_format($product->selling_price, 2) }}";
        
        let html = '';
        for (let i = 0; i < qty; i++) {
            html += `
                <div class="barcode-item">
                    <div class="barcode-label">JHR Bazar</div>
                    <div class="barcode-sub">${name}</div>
                    <div class="barcode-price">$${price}</div>
                    <img class="barcode-img-gen" data-value="${sku}">
                    <div class="barcode-code">Code: ${sku}</div>
                </div>
            `;
        }
        grid.innerHTML = html;
        generateBarcodes();
    }

    document.addEventListener("DOMContentLoaded", function() {
        generateBarcodes();
    });
</script>
@endsection
