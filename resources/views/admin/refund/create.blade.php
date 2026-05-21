@extends('admin.master')

@section('title', 'Create Refund')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Create Refund Request</h4>
                    <a href="{{ route('admin.refunds.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Refund Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="refundForm" class="needs-validation" novalidate>
                            @csrf

                            <!-- Order Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Select Order <span class="text-danger">*</span></label>
                                <select id="orderId" name="order_id" class="form-select" required>
                                    <option value="">-- Choose Order --</option>
                                    @if($order)
                                    <option value="{{ $order->id }}" selected>
                                        #{{ $order->order_no }} - {{ $order->customer->user->name ?? 'N/A' }}
                                    </option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">Please select an order</div>
                            </div>

                            <!-- Product Section -->
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-header bg-white border-bottom">
                                    <h6 class="mb-0">Product Details</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Product Selection with Autocomplete -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Product <span class="text-danger">*</span></label>
                                        <select id="productId" name="product_id" class="form-select select2" required>
                                            <option value="">-- Search Product --</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a product</div>
                                    </div>

                                    <!-- Product Name (Manual Entry) -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" id="productName" name="product_name" class="form-control"
                                               placeholder="Enter product name" required>
                                        <small class="text-muted d-block mt-1">
                                            <i class="bi bi-info-circle"></i> This will be auto-filled when you select a product
                                        </small>
                                    </div>

                                    <!-- Product Price -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Product Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ settings()->default_currency ?? '৳' }}</span>
                                                <input type="number" id="productPrice" name="product_price"
                                                       class="form-control" step="0.01" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                                            <input type="number" id="quantity" name="quantity" class="form-control"
                                                   min="1" value="1" required>
                                        </div>
                                    </div>

                                    <!-- Total Amount (Calculated) -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Total Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ settings()->default_currency ?? '৳' }}</span>
                                            <input type="text" id="totalAmount" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Refund Reason Section -->
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-header bg-white border-bottom">
                                    <h6 class="mb-0">Refund Reason</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Cancel Reason <span class="text-danger">*</span></label>
                                        <select id="cancelReason" name="cancel_reason" class="form-select" required>
                                            <option value="">-- Select Reason --</option>
                                            @foreach($reasons as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description</label>
                                        <textarea id="reasonDescription" name="cancel_reason_description" class="form-control"
                                                  rows="3" placeholder="Add detailed description (optional)"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Courier & Shipping Section -->
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-header bg-white border-bottom">
                                    <h6 class="mb-0">Courier Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Courier <span class="text-muted">(Optional)</span></label>
                                        <select id="courierId" name="courier_id" class="form-select">
                                            <option value="">-- Select Courier --</option>
                                            @foreach($couriers as $courier)
                                            <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted d-block mt-1">
                                            <i class="bi bi-info-circle"></i> Select which courier this refund is associated with
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-check-circle me-1"></i> Create Refund
                                </button>
                                <a href="{{ route('admin.refunds.index') }}" class="btn btn-secondary px-4">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select2 CSS/JS for Product Autocomplete -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Initialize Select2 for product search
$('#productId').select2({
    placeholder: '-- Search Product --',
    ajax: {
        url: '{{ route("admin.refunds.api.products") }}',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                q: params.term
            };
        },
        processResults: function(data) {
            return {
                results: data.results
            };
        }
    },
    minimumInputLength: 2
});

// When product is selected, auto-fill product name and price
$('#productId').on('change', function() {
    const data = $(this).select2('data');
    if(data.length > 0) {
        const selected = data[0];
        document.getElementById('productName').value = selected.name || '';
        document.getElementById('productPrice').value = selected.price || 0;
        calculateTotal();
    }
});

// Calculate total amount
function calculateTotal() {
    const price = parseFloat(document.getElementById('productPrice').value) || 0;
    const qty = parseInt(document.getElementById('quantity').value) || 1;
    const total = price * qty;
    document.getElementById('totalAmount').value = total.toFixed(2);
}

// Recalculate on price/quantity change
document.getElementById('productPrice').addEventListener('change', calculateTotal);
document.getElementById('quantity').addEventListener('change', calculateTotal);

// Form submission
document.getElementById('refundForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        order_id: document.getElementById('orderId').value,
        product_id: document.getElementById('productId').value,
        product_name: document.getElementById('productName').value,
        product_price: parseFloat(document.getElementById('productPrice').value),
        quantity: parseInt(document.getElementById('quantity').value),
        courier_id: document.getElementById('courierId').value || null,
        cancel_reason: document.getElementById('cancelReason').value,
        cancel_reason_description: document.getElementById('reasonDescription').value,
    };

    try {
        const response = await fetch('{{ route("admin.refunds.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if(result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: result.message,
                confirmButtonText: 'View Refund'
            }).then(() => {
                window.location.href = `{{ url('admin/refunds') }}/${result.refund_id}`;
            });
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Something went wrong: ' + error.message, 'error');
    }
});

// Order search functionality (simple approach)
const orderSelect = document.getElementById('orderId');
orderSelect.addEventListener('focus', async function() {
    if(this.options.length <= 1) {
        try {
            const response = await fetch('{{ route("admin.orders.index") }}?api=1');
            const orders = await response.json();
            // This is simplified - in real implementation, you'd fetch orders via API
        } catch (error) {
            console.error('Error loading orders:', error);
        }
    }
});
</script>

<style>
.select2-container--default .select2-selection--single {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    height: 38px;
    display: flex;
    align-items: center;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    padding: 0.375rem 0.75rem;
}
</style>
@endsection
