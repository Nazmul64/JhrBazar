@extends('admin.master')

@section('content')
<style>
    .card { border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .table thead th { background: #f8fafc; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; color: #64748b; padding: 15px; border: none; }
    .table tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .product-img { width: 45px; height: 45px; border-radius: 10px; object-fit: cover; }
    .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .status-active { background: #dcfce7; color: #15803d; }
    .status-inactive { background: #fee2e2; color: #b91c1c; }
    .btn-action { width: 32px; height: 32px; padding: 0; line-height: 32px; border-radius: 8px; font-size: 14px; transition: all 0.2s; }
    .btn-edit { background: #f1f5f9; color: #475569; }
    .btn-edit:hover { background: #e2e8f0; color: #0f172a; }
    .btn-delete { background: #fff1f2; color: #e11d48; }
    .btn-delete:hover { background: #ffe4e6; color: #be123c; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Digital Products</h4>
            <p class="text-muted small mb-0">Manage and track all digital products in your store</p>
        </div>
        <a href="{{ route('admin.digital_product.create') }}" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Add New Product
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset($product->thumbnail) }}" class="product-img me-3 border">
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $product->name }}</span>
                                        <small class="text-muted">SKU: {{ $product->sku }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark fw-medium">{{ $product->category->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">${{ number_format($product->selling_price, 2) }}</div>
                                @if($product->discount_price > 0)
                                    <small class="text-danger text-decoration-line-through">${{ number_format($product->discount_price, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $product->stock_quantity }}</span>
                            </td>
                            <td>
                                <form action="{{ route('admin.digital_product.toggle', $product->id) }}" method="POST">
                                    @csrf
                                    <div class="form-check form-switch">
                                        <input class="form-check-input cursor-pointer" type="checkbox" 
                                               {{ $product->is_active ? 'checked' : '' }} 
                                               onchange="this.form.submit()">
                                        <span class="status-badge {{ $product->is_active ? 'status-active' : 'status-inactive' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </form>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.digital_product.barcode', $product->id) }}" class="btn btn-action btn-edit" title="Barcode">
                                        <i class="bi bi-upc-scan"></i>
                                    </a>

                                    <a href="{{ route('admin.digital_product.show', $product->id) }}" class="btn btn-action btn-edit" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.digital_product.edit', $product->id) }}" class="btn btn-action btn-edit" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.digital_product.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-delete" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                No digital products found. <a href="{{ route('admin.digital_product.create') }}">Create one now</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
