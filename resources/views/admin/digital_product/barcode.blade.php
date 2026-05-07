@extends('admin.master')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Product Barcode</h4>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-printer me-2"></i> Print Barcode
            </button>
            <a href="{{ route('admin.digital_product.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-5">
            <div class="row row-cols-2 row-cols-md-4 g-4 text-center print-area">
                @for($i = 0; $i < $print_quantity; $i++)
                <div class="col">
                    <div class="border p-3 rounded bg-white shadow-sm">
                        <div class="fw-bold small mb-2">{{ $product->name }}</div>
                        <div class="barcode-img mb-2">
                            {!! app('DNS1D')->getBarcodeHTML($product->sku, 'C128', 1.5, 50) !!}
                        </div>


                        <div class="fw-bold small">{{ $product->sku }}</div>
                        <div class="text-danger fw-bold">${{ number_format($product->selling_price, 2) }}</div>
                    </div>
                </div>
                @endfor
            </div>
            
            <form action="" method="GET" class="mt-5 no-print">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Quantity to Print</label>
                        <input type="number" name="quantity" class="form-control" value="{{ $print_quantity }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: #white; }
    .card { box-shadow: none; border: none; }
}
</style>
@endsection
