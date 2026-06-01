@extends('admin.master')

@section('title', 'Refund Management')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Refund Management</h4>
                    <div class="page-title-right">
                        <a href="{{ route('admin.refunds.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> New Refund
                        </a>
                        <a href="{{ route('admin.refunds.export') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-download me-1"></i> Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" class="row align-items-end g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Search</label>
                                <input type="text" name="search" class="form-control"
                                       placeholder="Order ID / Product Name"
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Reason</label>
                                <select name="reason" class="form-select">
                                    <option value="">All Reasons</option>
                                    @foreach($reasons as $key => $reason)
                                    <option value="{{ $key }}" {{ request('reason') === $key ? 'selected' : '' }}>
                                        {{ $reason }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Refunds Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        @if($refunds->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">No refunds found</p>
                        </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Refund ID</th>
                                        <th>Order ID</th>
                                        <th>Product</th>
                                        <th>Courier</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Amount</th>
                                        <th>Reason</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($refunds as $refund)
                                    <tr>
                                        <td class="ps-4">
                                            <input type="checkbox" class="form-check-input refund-checkbox"
                                                   value="{{ $refund->id }}">
                                        </td>
                                        <td>
                                            <strong>#{{ $refund->id }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $refund->order_id) }}"
                                               class="text-primary text-decoration-none fw-bold">
                                                #{{ $refund->order->invoice->invoice_number ?? $refund->order_id }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $orderItem = null;
                                                    if ($refund->order && is_array($refund->order->items)) {
                                                        foreach ($refund->order->items as $item) {
                                                            if (($item['id'] ?? null) == $refund->product_id || ($item['name'] ?? $item['title'] ?? '') === $refund->product_name) {
                                                                $orderItem = $item;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    $thumbnail = $orderItem['thumbnail'] ?? null;
                                                    if (!$thumbnail && $refund->product) {
                                                        $thumbnail = $refund->product->thumbnail;
                                                    }
                                                @endphp
                                                @if($thumbnail)
                                                    <img src="{{ asset($thumbnail) }}" alt="{{ $refund->product_name }}" class="rounded border me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded border me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-image text-muted" style="font-size: 1.1rem;"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $refund->product_name }}</div>
                                                    <div class="text-muted small">Price: {{ settings()->default_currency ?? '৳' }}{{ number_format($refund->product_price, 0) }}</div>
                                                    <small class="text-muted">{{ $refund->product->sku ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                {{ $refund->courier->name ?? $refund->order->courier_name ?? 'N/A' }}
                                            </span>
                                            @if($refund->order && $refund->order->courier_status)
                                                <small class="d-block text-muted mt-1 text-capitalize">{{ strtolower($refund->order->courier_status) }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $refund->quantity }}</td>
                                        <td class="text-end fw-bold text-success">
                                            {{ settings()->default_currency ?? '৳' }}{{ number_format($refund->total_amount, 0) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $refund->getCancelReasonDisplay() }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted fw-semibold">
                                                {{ $refund->refund_date?->format('d M Y') ?? $refund->created_at?->format('d M Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            {!! $refund->getStatusBadge() !!}
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <!-- View -->
                                                <a href="{{ route('admin.refunds.show', $refund->id) }}"
                                                   class="btn btn-sm btn-info text-white shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                   style="width: 28px; height: 28px; border-radius: 6px;" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <!-- Edit -->
                                                @if($refund->isPending())
                                                <a href="{{ route('admin.refunds.edit', $refund->id) }}"
                                                   class="btn btn-sm btn-warning text-white shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                   style="width: 28px; height: 28px; border-radius: 6px;" title="Edit Refund">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @endif
                                                <!-- Delete -->
                                                <button type="button" class="btn btn-sm btn-danger shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                        style="width: 28px; height: 28px; border-radius: 6px;"
                                                        onclick="deleteRefund({{ $refund->id }})" title="Delete Refund">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                
                                                <!-- Status Controls (Dropdown if Pending) -->
                                                @if($refund->isPending())
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                            style="width: 28px; height: 28px; border-radius: 6px;"
                                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                               onclick="updateStatus({{ $refund->id }}, 'approved')">
                                                                <i class="bi bi-check-circle text-success me-1"></i> Approve
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                               onclick="updateStatus({{ $refund->id }}, 'processing')">
                                                                <i class="bi bi-arrow-repeat text-warning me-1"></i> Processing
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                               onclick="showRejectModal({{ $refund->id }})">
                                                                <i class="bi bi-x-circle text-danger me-1"></i> Reject
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row mt-3">
                            <div class="col-12">
                                {{ $refunds->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Reason for Rejection</label>
                    <textarea id="rejectReason" class="form-control" rows="3"
                              placeholder="Enter reason..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitReject()">Reject</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentRefundId = null;

// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.refund-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

function updateStatus(refundId, status) {
    fetch(`{{ url('admin/refunds') }}/${refundId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: data.message,
                timer: 2000
            }).then(() => location.reload());
        }
    });
}

function showRejectModal(refundId) {
    currentRefundId = refundId;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function submitReject() {
    const reason = document.getElementById('rejectReason').value;
    if(!reason) {
        Swal.fire('Error', 'Please enter a reason', 'error');
        return;
    }

    fetch(`{{ url('admin/refunds') }}/${currentRefundId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Rejected',
                text: data.message,
                timer: 2000
            }).then(() => location.reload());
        }
    });
}

function deleteRefund(refundId) {
    Swal.fire({
        title: 'Delete Refund?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if(result.isConfirmed) {
            fetch(`{{ url('admin/refunds') }}/${refundId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                }
            });
        }
    });
}
</script>
@endsection
