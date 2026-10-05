@extends('admin.master')
@section('title', 'Edit Seller Product')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Edit Seller Product</h3>
            <p class="text-muted mb-0">Modify product details or update approval status.</p>
        </div>
        <a href="{{ route('admin.seller-products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Products
        </a>
    </div>
</div>

<div class="page-content">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.seller-products.update', $product->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Product Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Selling Price (৳)</label>
                                <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ old('selling_price', $product->selling_price) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Discount Price (৳)</label>
                                <input type="number" step="0.01" name="discount_price" class="form-control" value="{{ old('discount_price', $product->discount_price) }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Stock Quantity</label>
                                <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">SKU</label>
                                <input type="text" class="form-control" value="{{ $product->sku }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border mb-3">
                            <h6 class="fw-bold mb-3">Seller Details</h6>
                            <p class="mb-1"><strong>Name:</strong> {{ $product->seller->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $product->seller->email ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Shop:</strong> {{ $product->seller->shop_name ?? 'Seller Shop' }}</p>
                        </div>

                        <div class="p-3 bg-light rounded border mb-3">
                            <h6 class="fw-bold mb-3">Approval & Status</h6>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Admin Status</label>
                                <select name="admin_status" class="form-select">
                                    <option value="pending" {{ $product->admin_status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                                    <option value="approved" {{ $product->admin_status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $product->admin_status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveSwitch" {{ $product->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActiveSwitch">Live on Store (Active)</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <a href="{{ route('admin.seller-products.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
