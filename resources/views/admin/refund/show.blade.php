@extends('admin.master')

@section('title', 'Refund Details - #' . $refund->id)

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Refund Details</h4>
                    <div class="page-title-right">
                        <a href="{{ route('admin.refunds.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                        <a href="{{ route('admin.refunds.index') }}" class="btn btn-primary ms-2 bg-gradient shadow-sm">
                            <i class="bi bi-list-ul me-1"></i> Refund List
                        </a>
                        @if($refund->isPending())
                        <a href="{{ route('admin.refunds.edit', $refund->id) }}" class="btn btn-warning ms-2 shadow-sm">
                            <i class="bi bi-pencil me-1"></i> Edit Refund
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Refund Information -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Refund #{{ $refund->id }}</h5>
                        {!! $refund->getStatusBadge() !!}
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="text-muted mb-1"><small>Order ID</small></p>
                                <p class="fw-bold">
                                    <a href="{{ route('admin.orders.show', $refund->order->id) }}"
                                       class="text-decoration-none">
                                        #{{ $refund->order->invoice->invoice_number ?? $refund->order->id }}
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1"><small>Created Date</small></p>
                                <p class="fw-bold">{{ $refund->refund_date?->format('d M Y H:i') ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted mb-1"><small>Seller</small></p>
                                <p class="fw-bold">
                                    {{ $refund->seller->name ?? 'N/A' }}
                                    @if($refund->seller)
                                    <span class="badge bg-light text-dark">{{ $refund->seller->email }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1"><small>Courier</small></p>
                                <p class="fw-bold">{{ $refund->courier->name ?? 'Not Selected' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Product Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
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
                                <img src="{{ asset($thumbnail) }}"
                                     alt="{{ $refund->product_name }}" class="img-fluid rounded border shadow-sm" style="max-height: 150px; object-fit: cover;">
                                @else
                                <div class="bg-light p-5 text-center rounded">
                                    <i class="bi bi-image" style="font-size: 2rem; color: #ccc;"></i>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted"><small>Product Name</small></td>
                                        <td class="fw-bold">{{ $refund->product_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><small>SKU</small></td>
                                        <td class="fw-bold">{{ $refund->product->sku ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><small>Unit Price</small></td>
                                        <td class="fw-bold">{{ settings()->default_currency ?? '৳' }}{{ number_format($refund->product_price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><small>Quantity</small></td>
                                        <td class="fw-bold">{{ $refund->quantity }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="text-muted"><small>Total Amount</small></td>
                                        <td class="fw-bold h5 mb-0">{{ settings()->default_currency ?? '৳' }}{{ number_format($refund->total_amount, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Refund Reason -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Cancellation Reason</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <span class="badge bg-warning text-dark">{{ $refund->getCancelReasonDisplay() }}</span>
                        </p>
                        @if($refund->cancel_reason_description)
                        <p class="text-muted mb-0">
                            <strong>Details:</strong><br>
                            {{ $refund->cancel_reason_description }}
                        </p>
                        @endif
                    </div>
                </div>

                <!-- Admin Notes -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Admin Notes</h5>
                    </div>
                    <div class="card-body">
                        @if($refund->admin_note)
                        <p class="mb-0">{{ $refund->admin_note }}</p>
                        @else
                        <p class="text-muted mb-0"><em>No notes yet</em></p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- Status Update -->
                @if($refund->isPending())
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Update Status</h5>
                    </div>
                    <div class="card-body">
                        <form id="statusForm" class="space-y-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">New Status</label>
                                <select id="newStatus" class="form-select" required>
                                    <option value="">-- Select Status --</option>
                                    <option value="approved">✓ Approve</option>
                                    <option value="processing">⟳ Processing</option>
                                    <option value="completed">✓ Completed</option>
                                    <option value="rejected">✕ Reject</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Admin Note</label>
                                <textarea id="adminNote" class="form-control" rows="3"
                                          placeholder="Add notes..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-1"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Key Information -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Quick Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="text-muted mb-1"><small>REFUND ID</small></p>
                            <p class="fw-bold">#{{ str_pad($refund->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1"><small>CURRENT STATUS</small></p>
                            <p>{!! $refund->getStatusBadge() !!}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1"><small>REFUND AMOUNT</small></p>
                            <p class="fw-bold h5 text-success mb-0">
                                {{ settings()->default_currency ?? '৳' }}{{ number_format($refund->total_amount, 2) }}
                            </p>
                        </div>
                        <hr>
                        <div>
                            <p class="text-muted mb-1"><small>CREATED</small></p>
                            <p class="small mb-0">{{ $refund->created_at?->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-muted mb-1"><small>LAST UPDATED</small></p>
                            <p class="small mb-0">{{ $refund->updated_at?->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Status Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <p class="text-muted mb-1"><small>Created</small></p>
                                    <p class="fw-bold mb-0">{{ $refund->created_at?->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            @if($refund->refund_date)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <p class="text-muted mb-1"><small>Refund Processed</small></p>
                                    <p class="fw-bold mb-0">{{ $refund->refund_date->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -15px;
    top: 3px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    padding-left: 10px;
}
</style>

<script>
document.getElementById('statusForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const status = document.getElementById('newStatus').value;
    const note = document.getElementById('adminNote').value;

    if(!status) {
        Swal.fire('Error', 'Please select a status', 'error');
        return;
    }

    try {
        const response = await fetch(`{{ route('admin.refunds.update-status', $refund->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: status,
                admin_note: note
            })
        });

        const result = await response.json();

        if(result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: result.message,
                timer: 2000
            }).then(() => location.reload());
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Something went wrong: ' + error.message, 'error');
    }
});
</script>
@endsection
