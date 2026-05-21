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

    .alert-ok { background:#f0fdf4; color:#15803d; border-left:3.5px solid #22c55e; border-radius:var(--r-md); padding:12px 16px; font-size:13.5px; margin-bottom:1.2rem; display:flex; align-items:center; gap:9px; }

    /* Product Grid Styling */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .product-card {
        background: #fff;
        border-radius: var(--r-md);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .product-img-wrap {
        width: 100%;
        height: 200px;
        background: var(--surface);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-bottom: 1px solid var(--border);
    }

    .product-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-body {
        padding: 16px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-desc {
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: auto;
        margin-bottom: 12px;
    }

    .price-main {
        font-size: 16px;
        font-weight: 800;
        color: var(--dark);
    }

    .price-discount {
        font-size: 13px;
        color: var(--brand);
        background: rgba(232, 23, 74, 0.1);
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
    }

    .product-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px dashed var(--border);
    }

    /* ── Status toggle ── */
    .form-check-input { width:38px !important; height:20px !important; cursor:pointer; border-radius:10px !important; }
    .form-check-input:checked { background-color:var(--brand) !important; border-color:var(--brand) !important; }

    .action-icons {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: var(--surface);
        color: var(--muted);
        text-decoration: none;
        transition: var(--ease);
    }

    .btn-icon:hover { background: var(--border); color: var(--dark); }
    .btn-icon-del { color: #ef4444; }
    .btn-icon-del:hover { background: rgba(239, 68, 68, 0.1); }
    .btn-icon-edit { color: #14b8a6; }
    .btn-icon-edit:hover { background: rgba(20, 184, 166, 0.1); }
    .btn-icon-view { color: #3b82f6; }
    .btn-icon-view:hover { background: rgba(59, 130, 246, 0.1); }
    .btn-icon-barcode { color: #8b5cf6; }
    .btn-icon-barcode:hover { background: rgba(139, 92, 246, 0.1); }

    .empty-state { text-align:center; padding:50px 20px; color:var(--muted); font-size:15px; grid-column: 1 / -1; background:#fff; border-radius:var(--r-md); border:1px dashed var(--border); }
</style>

<div class="container-fluid px-4 py-4">
    <div class="ph">
        <h4 class="ph-title">Product List</h4>
        <a href="{{ route('seller.product.create') }}" class="btn-add">
            <i class="bi bi-plus-circle"></i> Add Product
        </a>
    </div>

    @if(session('success'))
        <div class="alert-ok">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="product-grid">
        @forelse($products as $product)
            <div class="product-card">
                <div class="product-img-wrap">
                    @if($product->thumbnail)
                        <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}">
                    @else
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    @endif
                </div>
                <div class="product-body">
                    <div class="product-title" title="{{ $product->name }}">{{ $product->name }}</div>
                    <div class="product-desc">{{ $product->short_description ?? 'No description provided.' }}</div>
                    
                    <div class="product-price-row">
                        <span class="price-main">৳{{ number_format($product->selling_price, 0) }}</span>
                        @if($product->discount_price > 0)
                            <span class="price-discount">৳{{ number_format($product->discount_price, 0) }}</span>
                        @endif
                    </div>

                    <div class="product-actions">
                        <form action="{{ route('seller.product.toggle', $product->id) }}" method="POST">
                            @csrf
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       {{ $product->is_active ? 'checked' : '' }}
                                       onchange="this.closest('form').submit()">
                            </div>
                        </form>

                        <div class="action-icons">
                            <a href="{{ route('seller.product.show', $product->id) }}" class="btn-icon btn-icon-view" title="View Details"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('seller.product.barcode', $product->id) }}" class="btn-icon btn-icon-barcode" title="Generate Barcode"><i class="bi bi-upc-scan"></i></a>
                            <a href="{{ route('seller.product.edit', $product->id) }}" class="btn-icon btn-icon-edit" title="Edit Product"><i class="bi bi-pencil-square"></i></a>
                            
                            <form action="{{ route('seller.product.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-del border-0" title="Delete Product">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="bi bi-box-seam" style="font-size:48px;color:#d1d5db;display:block;margin-bottom:12px;"></i>
                No products found. Start adding products to your store!
            </div>
        @endforelse
    </div>
</div>
@endsection
