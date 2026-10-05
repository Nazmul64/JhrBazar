@extends('admin.master')
@section('title', 'Seller Products Management')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Seller Products Management</h3>
            <p class="text-muted mb-0">Review, approve, reject, or edit products uploaded by sellers.</p>
        </div>
    </div>
</div>

<div class="page-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        {{-- Tabs --}}
        <div class="card-header bg-white pb-0 border-bottom">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('admin.seller-products.index') }}">
                            All Products <span class="badge bg-secondary ms-1">{{ $counts['all'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') === 'pending' ? 'active bg-warning text-dark' : '' }}" href="{{ route('admin.seller-products.index', ['status' => 'pending']) }}">
                            <i class="bi bi-hourglass-split me-1"></i> Pending Review
                            @if($counts['pending'] > 0)
                                <span class="badge bg-danger ms-1">{{ $counts['pending'] }}</span>
                            @else
                                <span class="badge bg-secondary ms-1">0</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') === 'approved' ? 'active' : '' }}" href="{{ route('admin.seller-products.index', ['status' => 'approved']) }}">
                            <i class="bi bi-check-circle me-1"></i> Approved <span class="badge bg-secondary ms-1">{{ $counts['approved'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') === 'rejected' ? 'active bg-danger' : '' }}" href="{{ route('admin.seller-products.index', ['status' => 'rejected']) }}">
                            <i class="bi bi-x-circle me-1"></i> Rejected <span class="badge bg-secondary ms-1">{{ $counts['rejected'] }}</span>
                        </a>
                    </li>
                </ul>

                {{-- Search Box --}}
                <form action="{{ route('admin.seller-products.index') }}" method="GET" class="d-flex gap-2" style="max-width: 320px;">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search product, seller, SKU..." value="{{ request('search') }}">
                    <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    @if(request('search'))
                        <a href="{{ route('admin.seller-products.index', request('status') ? ['status' => request('status')] : []) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Product</th>
                            <th>Seller</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Live</th>
                            <th class="text-end pe-4" style="min-width: 170px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($product->thumbnail)
                                            <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}" class="rounded" style="width: 44px; height: 44px; object-fit: cover;">
                                        @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 13.5px;">{{ Str::limit($product->name, 38) }}</div>
                                            <small class="text-muted">SKU: {{ $product->sku ?: '—' }} | Code: {{ $product->barcode ?: '—' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $product->seller->name ?? 'Unknown Seller' }}</div>
                                    <small class="text-muted">{{ $product->seller->email ?? '' }}</small>
                                </td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td>
                                    <div class="fw-bold text-primary">৳{{ number_format($product->selling_price, 2) }}</div>
                                    @if($product->discount_price > 0)
                                        <small class="text-danger text-decoration-line-through">৳{{ number_format($product->discount_price, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $product->stock_quantity > 5 ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} border">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td>
                                    @if(($product->admin_status ?? 'pending') === 'pending')
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split me-1"></i> Pending Review</span>
                                    @elseif($product->admin_status === 'approved')
                                        <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i> Approved</span>
                                    @else
                                        <span class="badge bg-danger px-2 py-1" data-bs-toggle="tooltip" title="{{ $product->rejection_reason ?? 'Rejected' }}">
                                            <i class="bi bi-x-circle me-1"></i> Rejected
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success" style="font-size: 11px;">Active</span>
                                    @else
                                        <span class="badge bg-secondary" style="font-size: 11px;">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        {{-- Quick Approve button if not approved --}}
                                        @if($product->admin_status !== 'approved')
                                            <form action="{{ route('admin.seller-products.status', $product->id) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="admin_status" value="approved">
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve & Publish">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Reject button opens modal --}}
                                        @if($product->admin_status !== 'rejected')
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $product->id }}" title="Reject Product">
                                                <i class="bi bi-x-lg"></i> Reject
                                            </button>
                                        @endif

                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.seller-products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Product">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('admin.seller-products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this seller product permanently?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Reject Modal --}}
                                    <div class="modal fade text-start" id="rejectModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.seller-products.status', $product->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="admin_status" value="rejected">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i> Reject Seller Product</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-2"><strong>Product:</strong> {{ $product->name }}</p>
                                                        <p class="mb-3 text-muted"><strong>Seller:</strong> {{ $product->seller->name ?? 'N/A' }}</p>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                                                            <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Please specify why this product is being rejected (e.g., incorrect pricing, prohibited item, unclear photos)..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-box-seam" style="font-size: 40px; display:block; margin-bottom:10px;"></i>
                                    No seller products found in this category.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($products->hasPages())
                <div class="p-3 border-top">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
