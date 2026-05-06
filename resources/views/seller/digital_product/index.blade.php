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
        --shadow:      0 1px 4px rgba(0,0,0,.06), 0 2px 12px rgba(0,0,0,.04);
        --r-lg: 14px; --r-md: 10px; --r-sm: 7px;
        --ease: all .18s ease;
    }

    .ph { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
    .ph-title { font-size:1.4rem; font-weight:700; color:var(--dark); margin:0; }
    .btn-add {
        display:inline-flex; align-items:center; gap:7px;
        background:var(--brand); color:#fff; border:none;
        border-radius:var(--r-md); padding:10px 22px;
        font-size:13.5px; font-weight:600; cursor:pointer;
        box-shadow:0 2px 10px rgba(232,23,74,.3);
        text-decoration:none; transition:var(--ease);
    }
    .btn-add:hover { background:var(--brand-hover); color:#fff; transform:translateY(-1px); }

    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .product-card { background: #fff; border-radius: var(--r-md); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; display: flex; flex-direction: column; }
    .product-img-wrap { width: 100%; height: 200px; background: var(--surface); display: flex; align-items: center; justify-content: center; overflow: hidden; border-bottom: 1px solid var(--border); }
    .product-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .product-body { padding: 16px; flex-grow: 1; display: flex; flex-direction: column; }
    .product-title { font-size: 14px; font-weight: 700; color: var(--dark); margin-bottom: 8px; }
    .digital-badge { background: #3b82f6; color: #fff; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; margin-bottom: 5px; display: inline-block; width: fit-content; }
    
    .product-actions { display: flex; align-items: center; justify-content: space-between; padding-top: 12px; border-top: 1px dashed var(--border); }
    .form-check-input { width:38px !important; height:20px !important; cursor:pointer; border-radius:10px !important; }
    .form-check-input:checked { background-color:var(--brand) !important; border-color:var(--brand) !important; }

    .action-icons { display: flex; gap: 6px; }
    .btn-icon { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: var(--surface); color: var(--muted); text-decoration: none; transition: var(--ease); }
    .btn-icon:hover { background: var(--border); color: var(--dark); }
</style>

<div class="container-fluid px-4 py-4">
    <div class="ph">
        <h4 class="ph-title">Digital Product List</h4>
        <a href="{{ route('seller.digital_product.create') }}" class="btn-add">
            <i class="bi bi-plus-circle"></i> Add Digital Product
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="product-grid">
        @forelse($products as $product)
            <div class="product-card">
                <div class="product-img-wrap">
                    @if($product->thumbnail)
                        <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}">
                    @else
                        <i class="bi bi-file-earmark-arrow-down text-muted" style="font-size: 3rem;"></i>
                    @endif
                </div>
                <div class="product-body">
                    <span class="digital-badge">Digital</span>
                    <div class="product-title">{{ $product->name }}</div>
                    <div class="text-muted small mb-2">SKU: {{ $product->sku }}</div>
                    
                    <div class="product-price-row mb-3">
                        <span class="fw-bold text-dark">${{ number_format($product->selling_price, 2) }}</span>
                    </div>

                    <div class="product-actions">
                        <form action="{{ route('seller.digital_product.toggle', $product->id) }}" method="POST">
                            @csrf
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       {{ $product->is_active ? 'checked' : '' }}
                                       onchange="this.closest('form').submit()">
                            </div>
                        </form>

                        <div class="action-icons">
                            <a href="{{ route('seller.digital_product.show', $product->id) }}" class="btn-icon" title="View"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('seller.digital_product.barcode', $product->id) }}" class="btn-icon" title="Barcode"><i class="bi bi-upc-scan"></i></a>
                            <a href="{{ route('seller.digital_product.edit', $product->id) }}" class="btn-icon" title="Edit"><i class="bi bi-pencil-square"></i></a>
                            
                            <form action="{{ route('seller.digital_product.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon border-0 text-danger"><i class="bi bi-trash3"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">No digital products found.</div>
        @endforelse
    </div>
</div>
@endsection
