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
                                <select id="orderId" name="order_id" class="form-select select2" required style="width: 100%;">
                                    <option value="">-- Search and Select Order (Invoice #, Name, Phone) --</option>
                                    @if($order)
                                    <option value="{{ $order->id }}" selected>
                                        Invoice #{{ $order->invoice->invoice_number ?? 'N/A' }} - {{ $order->customer->user->name ?? 'N/A' }} ({{ $order->customer->user->phone ?? 'N/A' }}) - ৳{{ $order->grand_total }}
                                    </option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">Please select an order</div>
                            </div>

                            <!-- Dynamic Order & Courier Status Card -->
                            <div id="orderInfoCard" class="card border-0 mb-4" style="display: none; background: rgba(255, 255, 255, 0.45); backdrop-filter: blur(15px); border-radius: 12px; border: 1px solid rgba(220, 224, 230, 0.5); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <i class="bi bi-info-circle-fill fs-4 text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 1.05rem;">Order & Courier Details</h6>
                                            <p class="text-muted mb-0 small">Real-time status for Invoice <span class="fw-bold text-primary" id="infoInvoiceNo"></span></p>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6 col-lg-3">
                                            <div class="p-3 border rounded bg-white bg-opacity-60 h-100">
                                                <small class="text-muted d-block uppercase-tracking fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">CUSTOMER</small>
                                                <span class="fw-semibold text-dark mt-1 d-block" id="infoCustomerName" style="font-size: 0.95rem;"></span>
                                                <span class="text-muted small" id="infoCustomerPhone"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="p-3 border rounded bg-white bg-opacity-60 h-100">
                                                <small class="text-muted d-block uppercase-tracking fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">ORDER STATUS</small>
                                                <span class="badge px-2.5 py-1.5 mt-2 d-inline-block" id="infoOrderStatus" style="font-size: 0.85rem;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="p-3 border rounded bg-white bg-opacity-60 h-100">
                                                <small class="text-muted d-block uppercase-tracking fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">COURIER NAME & STATUS</small>
                                                <span class="fw-semibold text-dark mt-1 d-block" id="infoCourierName" style="font-size: 0.95rem;"></span>
                                                <span class="badge bg-light text-dark px-2 py-1 mt-1 d-inline-block" id="infoCourierStatus" style="font-size: 0.8rem;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="p-3 border rounded bg-white bg-opacity-60 h-100 d-flex flex-column justify-content-between">
                                                <div>
                                                    <small class="text-muted d-block uppercase-tracking fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">REJECTION DETAILS</small>
                                                    <div id="rejectionBadgeContainer" class="mt-2"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Section -->
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-header bg-white border-bottom">
                                    <h6 class="mb-0 fw-bold text-dark">Product Details</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Product Selection -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Product <span class="text-danger">*</span></label>
                                        <select id="productId" name="product_id" class="form-select select2" required style="width: 100%;">
                                            <option value="">-- Select Product --</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a product</div>
                                    </div>

                                    <!-- Product Name (Manual Entry) -->
                                    <div class="mb-3" style="display: none;">
                                        <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" id="productName" name="product_name" class="form-control"
                                               placeholder="Enter product name" required>
                                    </div>

                                    <!-- Product Price -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Product Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ $gs->default_currency ?? '৳' }}</span>
                                                <input type="number" id="productPrice" name="product_price"
                                                       class="form-control" step="0.01" min="0" required readonly style="background-color: #f8f9fa;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                                            <input type="number" id="quantity" name="quantity" class="form-control"
                                                   min="1" value="1" required>
                                            <small class="text-muted d-block mt-1" id="quantityHelp">
                                                Select a product to view the max refundable quantity.
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Total Amount (Calculated) -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Total Refund Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ $gs->default_currency ?? '৳' }}</span>
                                            <input type="text" id="totalAmount" class="form-control" disabled style="background-color: #f8f9fa; font-weight: bold; color: #28a745;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Refund Reason Section -->
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-header bg-white border-bottom">
                                    <h6 class="mb-0 fw-bold text-dark">Refund Reason</h6>
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
                                    <h6 class="mb-0 fw-bold text-dark">Courier Information</h6>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for Order autocomplete
    $('#orderId').select2({
        placeholder: '-- Search and Select Order (Invoice #, Name, Phone) --',
        ajax: {
            url: '{{ route("admin.refunds.api.orders") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term || ''
                };
            },
            processResults: function(data) {
                return {
                    results: data.results
                };
            },
            cache: true
        },
        minimumInputLength: 0
    });

    // Initialize Select2 for Product select
    $('#productId').select2({
        placeholder: '-- Select Product --'
    });

    // When order is selected, load items and courier info
    $('#orderId').on('change', function() {
        const orderId = $(this).val();
        if (!orderId) {
            $('#orderInfoCard').fadeOut();
            $('#productId').html('<option value="">-- Select Product --</option>').trigger('change');
            return;
        }

        // Fetch details
        fetch(`{{ url('admin/refunds/api/orders') }}/${orderId}/details`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const order = data.order;

                    // Fill Card
                    $('#infoInvoiceNo').text('#' + order.invoice_number);
                    $('#infoCustomerName').text(order.customer_name);
                    $('#infoCustomerPhone').text(order.customer_phone);
                    
                    // Order status badge styling
                    let orderBadgeClass = 'bg-secondary';
                    if (order.status.toLowerCase() === 'completed' || order.status.toLowerCase() === 'delivered') {
                        orderBadgeClass = 'bg-success';
                    } else if (order.status.toLowerCase() === 'cancelled') {
                        orderBadgeClass = 'bg-danger';
                    } else if (order.status.toLowerCase() === 'pending' || order.status.toLowerCase() === 'draft') {
                        orderBadgeClass = 'bg-warning text-dark';
                    } else {
                        orderBadgeClass = 'bg-info';
                    }
                    $('#infoOrderStatus').text(order.status).attr('class', 'badge px-2.5 py-1.5 mt-2 ' + orderBadgeClass);

                    // Courier Status
                    $('#infoCourierName').text(order.courier_name || 'N/A');
                    $('#infoCourierStatus').text(order.courier_status || 'N/A');

                    // Rejection Info Banner
                    let rejectionBadges = '';
                    if (order.is_admin_cancelled) {
                        rejectionBadges += '<span class="badge bg-danger-subtle text-danger px-2.5 py-1.5 d-inline-block small"><i class="bi bi-x-circle-fill me-1"></i> Rejected by Admin</span>';
                    }
                    if (order.is_courier_rejected) {
                        rejectionBadges += '<span class="badge bg-warning-subtle text-warning px-2.5 py-1.5 mt-1 d-inline-block small"><i class="bi bi-truck me-1"></i> Courier Failed/Rejected</span>';
                    }
                    if (!order.is_admin_cancelled && !order.is_courier_rejected) {
                        rejectionBadges += '<span class="badge bg-success-subtle text-success px-2.5 py-1.5 d-inline-block small"><i class="bi bi-check-circle-fill me-1"></i> No Rejections</span>';
                    }
                    $('#rejectionBadgeContainer').html(rejectionBadges);

                    $('#orderInfoCard').fadeIn();

                    // Populate Products Dropdown
                    let productOptions = '<option value="">-- Choose Product --</option>';
                    order.items.forEach(item => {
                        const variantDetails = [];
                        if (item.size) variantDetails.push('Size: ' + item.size);
                        if (item.color) variantDetails.push('Color: ' + item.color);
                        const variantText = variantDetails.length > 0 ? ' (' + variantDetails.join(', ') + ')' : '';

                        const productName = item.title || item.name || 'Unknown Product';
                        productOptions += `<option value="${item.id}" data-name="${productName}" data-price="${item.price}" data-qty="${item.qty}">
                            ${productName}${variantText} - ৳${item.price} [Ordered Qty: ${item.qty}]
                        </option>`;
                    });
                    $('#productId').html(productOptions).trigger('change');
                } else {
                    Swal.fire('Error', 'Failed to retrieve order details', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Failed to load order details: ' + err.message, 'error');
            });
    });

    // When product is selected, auto-fill price, max qty, and compute total
    $('#productId').on('change', function() {
        const option = $(this).find(':selected');
        if (!option.val()) {
            $('#productName').val('');
            $('#productPrice').val('');
            $('#quantity').val(1).removeAttr('max');
            $('#quantityHelp').text('Select a product to view the max refundable quantity.');
            calculateTotal();
            return;
        }

        const name = option.data('name');
        const price = parseFloat(option.data('price')) || 0;
        const qty = parseInt(option.data('qty')) || 1;

        $('#productName').val(name);
        $('#productPrice').val(price.toFixed(2));
        $('#quantity').attr('max', qty).val(1);
        $('#quantityHelp').html(`<i class="bi bi-info-circle"></i> Price: ৳${price.toFixed(2)} each. Max <strong>${qty}</strong> item(s) purchased.`);
        
        calculateTotal();
    });

    // Recalculate on price or quantity change
    $('#quantity').on('change keyup input', function() {
        const max = parseInt($(this).attr('max')) || 1;
        const val = parseInt($(this).val()) || 1;
        
        // Prevent exceeding purchased quantity
        if (val > max) {
            $(this).val(max);
            Swal.fire({
                icon: 'warning',
                title: 'Quantity Limit',
                text: `You cannot refund more than the purchased quantity of ${max}.`,
                timer: 2000
            });
        }
        calculateTotal();
    });

    // Compute refund total
    function calculateTotal() {
        const price = parseFloat($('#productPrice').val()) || 0;
        const qty = parseInt($('#quantity').val()) || 0;
        const total = price * qty;
        $('#totalAmount').val(total.toFixed(2));
    }

    // Handle Form Submission via AJAX
    $('#refundForm').on('submit', async function(e) {
        e.preventDefault();

        // Perform custom validations
        const orderId = $('#orderId').val();
        const productId = $('#productId').val();
        const cancelReason = $('#cancelReason').val();
        const quantity = parseInt($('#quantity').val()) || 0;

        if (!orderId || !productId || !cancelReason || quantity < 1) {
            Swal.fire('Validation Error', 'Please fill in all required fields.', 'error');
            return;
        }

        const data = {
            order_id: orderId,
            product_id: productId,
            product_name: $('#productName').val(),
            product_price: parseFloat($('#productPrice').val()),
            quantity: quantity,
            courier_id: $('#courierId').val() || null,
            cancel_reason: cancelReason,
            cancel_reason_description: $('#reasonDescription').val(),
        };

        try {
            const response = await fetch('{{ route("admin.refunds.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Refund Created!',
                    text: result.message,
                    confirmButtonText: 'View Details'
                }).then(() => {
                    window.location.href = `{{ url('admin/refunds') }}/${result.refund_id}`;
                });
            } else {
                let errorMsg = result.message || 'Failed to create refund.';
                if (result.errors) {
                    const errorList = Object.values(result.errors).flat().join('<br>');
                    errorMsg = `<strong>${errorMsg}</strong><br><br>${errorList}`;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorMsg
                });
            }
        } catch (error) {
            Swal.fire('Error', 'An unexpected error occurred: ' + error.message, 'error');
        }
    });

    // If pre-selected order exists (loaded via request query parameter)
    @if($order)
        $('#orderId').trigger('change');
    @endif
});
</script>
@endpush

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
