@extends('admin.master')

@section('title', 'Create New Order')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0 fw-bold">Create New Order</h4>
                    <div>
                        <button type="button" class="btn btn-danger btn-sm px-4" id="clear-cart">
                            <i class="bi bi-cart-x me-1"></i> Cart Clear
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm px-4">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Products <span class="text-danger">*</span></label>
                        <select id="product-select" class="form-select select2" data-placeholder="— পণ্য নির্বাচন করুন —">
                            <option value=""></option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-name="{{ $product->name }}" 
                                    data-price="{{ $product->discount_price > 0 ? $product->discount_price : $product->selling_price }}"
                                    data-image="{{ $product->thumbnail ? asset($product->thumbnail) : 'https://placehold.co/50x50/f3f4f6/6b7280?text=No+Image' }}"
                                    data-sku="{{ $product->sku }}">
                                {{ $product->name }} ({{ $product->sku }}) - ৳{{ $product->discount_price > 0 ? $product->discount_price : $product->selling_price }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="cart-table">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th style="width: 80px;">Image</th>
                                    <th>Product Name</th>
                                    <th style="width: 150px;">Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Discount</th>
                                    <th>Sub Total</th>
                                    <th style="width: 50px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="empty-cart-row">
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        উপরে থেকে পণ্য নির্বাচন করুন
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Customer Info --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">কাস্টমার তথ্য</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" id="customer-name" class="form-control" placeholder="Customer Name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" id="customer-phone" class="form-control" placeholder="Phone Number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Full Address <span class="text-danger">*</span></label>
                        <textarea id="customer-address" class="form-control" rows="2" placeholder="Full Address"></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Delivery Area <span class="text-danger">*</span></label>
                        <input type="text" id="delivery-area" class="form-control" placeholder="Delivery Area">
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                    <i class="bi bi-wallet2 me-2 text-primary"></i>
                    <h6 class="mb-0 fw-bold">পেমেন্ট তথ্য</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">পেমেন্ট মেথড</label>
                        <select id="payment-method" class="form-select">
                            <option value="cod">Cash on Delivery (COD)</option>
                            <option value="bkash">Bkash</option>
                            <option value="nagad">Nagad</option>
                            <option value="rocket">Rocket</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Transaction ID (TrxID)</label>
                        <input type="text" id="trx-id" class="form-control" placeholder="যেমন: 8N3A2B1C4D5E (ঐচ্ছিক)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">পেমেন্ট স্ট্যাটাস</label>
                        <select id="payment-status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="partial">Partial</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">অর্ডারের তারিখ <span class="text-muted small fw-normal">(খালি রাখলে আজকের তারিখ)</span></label>
                        <input type="datetime-local" id="order-date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush rounded">
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 py-3">
                            <span class="text-muted">Sub Total</span>
                            <span class="fw-bold" id="summary-subtotal">0</span>
                        </li>
                        <li class="list-group-item border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Shipping Zone</span>
                                <select id="shipping-zone" class="form-select form-select-sm w-auto">
                                    <option value="0">— শিপিং জোন নির্বাচন করুন —</option>
                                    @foreach($shippingCharges as $charge)
                                    <option value="{{ $charge->charge }}">{{ $charge->area_name }} (৳{{ $charge->charge }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small ms-auto">অথবা ম্যানুয়ালি লিখুন:</span>
                                <input type="number" id="manual-shipping" class="form-control form-control-sm w-25 ms-2 text-end" value="0.00">
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 py-3">
                            <span class="text-muted">Item Discount</span>
                            <span class="fw-bold" id="summary-discount">0</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-4 bg-light">
                            <span class="h5 mb-0 fw-bold">Total</span>
                            <span class="h5 mb-0 fw-bold text-primary">৳<span id="summary-total">0</span></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <button type="button" id="submit-order" class="btn w-100 py-3 fw-bold text-white shadow-sm" style="background-color: #1abc9c;">
                Create Order
            </button>
        </div>
    </div>
</div>

<style>
    .select2-container--default .select2-selection--single {
        height: 45px;
        padding: 8px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px;
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 15px;
    }
    .card {
        border-radius: 12px;
    }
    #cart-table thead th {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .qty-input {
        width: 60px;
        text-align: center;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin: 0 5px;
    }
    .qty-btn {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2();

    let cart = [];

    // Add Product to Cart
    $('#product-select').on('change', function() {
        let productId = $(this).val();
        if (!productId) return;

        let selectedOption = $(this).find(':selected');
        let name = selectedOption.data('name');
        let price = parseFloat(selectedOption.data('price'));
        let image = selectedOption.data('image');
        let sku = selectedOption.data('sku');

        let existingItem = cart.find(item => item.id == productId);
        if (existingItem) {
            existingItem.qty += 1;
        } else {
            cart.push({
                id: productId,
                name: name,
                price: price,
                image: image,
                sku: sku,
                qty: 1,
                discount: 0
            });
        }

        $(this).val('').trigger('change');
        renderCart();
    });

    // Render Cart Table
    function renderCart() {
        let html = '';
        let subtotal = 0;

        if (cart.length === 0) {
            html = `<tr><td colspan="7" class="text-center py-5 text-muted">উপরে থেকে পণ্য নির্বাচন করুন</td></tr>`;
        } else {
            cart.forEach((item, index) => {
                let lineTotal = (item.price * item.qty) - (item.discount || 0);
                subtotal += lineTotal;

                html += `
                    <tr>
                        <td><img src="${item.image}" alt="" class="rounded" style="width: 50px; height: 50px; object-fit: cover;"></td>
                        <td>
                            <div class="fw-bold">${item.name}</div>
                            <small class="text-muted">SKU: ${item.sku}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary btn-sm qty-btn" onclick="updateQty(${index}, -1)">-</button>
                                <input type="number" class="qty-input" value="${item.qty}" min="1" onchange="updateQty(${index}, this.value, true)">
                                <button type="button" class="btn btn-outline-secondary btn-sm qty-btn" onclick="updateQty(${index}, 1)">+</button>
                            </div>
                        </td>
                        <td>৳${item.price.toFixed(2)}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm w-75" value="${item.discount || 0}" onchange="updateDiscount(${index}, this.value)">
                        </td>
                        <td class="fw-bold">৳${lineTotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-sm text-danger" onclick="removeItem(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        $('#cart-table tbody').html(html);
        updateSummary(subtotal);
    }

    window.updateQty = function(index, val, direct = false) {
        if (direct) {
            cart[index].qty = Math.max(1, parseInt(val) || 1);
        } else {
            cart[index].qty = Math.max(1, cart[index].qty + val);
        }
        renderCart();
    };

    window.updateDiscount = function(index, val) {
        cart[index].discount = parseFloat(val) || 0;
        renderCart();
    };

    window.removeItem = function(index) {
        cart.splice(index, 1);
        renderCart();
    };

    $('#clear-cart').on('click', function() {
        cart = [];
        renderCart();
    });

    // Summary Update
    function updateSummary(subtotal) {
        let shipping = parseFloat($('#shipping-zone').val()) || parseFloat($('#manual-shipping').val()) || 0;
        let totalDiscount = cart.reduce((acc, item) => acc + (parseFloat(item.discount) || 0), 0);
        let total = subtotal + shipping;

        $('#summary-subtotal').text(subtotal.toFixed(2));
        $('#summary-discount').text(totalDiscount.toFixed(2));
        $('#summary-total').text(total.toFixed(2));
    }

    $('#shipping-zone, #manual-shipping').on('change input', function() {
        if ($(this).attr('id') === 'shipping-zone' && $(this).val() != "0") {
            $('#manual-shipping').val(parseFloat($(this).val()).toFixed(2));
        }
        let subtotal = cart.reduce((acc, item) => acc + (item.price * item.qty - (item.discount || 0)), 0);
        updateSummary(subtotal);
    });

    // Submit Order
    $('#submit-order').on('click', function() {
        if (cart.length === 0) {
            Swal.fire('Error', 'কার্টে কোনো পণ্য নেই!', 'error');
            return;
        }

        let orderData = {
            customer_name: $('#customer-name').val(),
            customer_phone: $('#customer-phone').val(),
            customer_address: $('#customer-address').val(),
            delivery_area: $('#delivery-area').val(),
            payment_method: $('#payment-method').val(),
            trx_id: $('#trx-id').val(),
            payment_status: $('#payment-status').val(),
            order_date: $('#order-date').val(),
            shipping_charge: $('#manual-shipping').val(),
            items: cart,
            _token: '{{ csrf_token() }}'
        };

        if (!orderData.customer_name || !orderData.customer_phone || !orderData.customer_address) {
            Swal.fire('Error', 'কাস্টমার তথ্য সম্পূর্ণ করুন!', 'error');
            return;
        }

        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

        $.ajax({
            url: "{{ route('admin.orders.store') }}",
            method: "POST",
            data: orderData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 2000
                    }).then(() => {
                        window.location.href = "{{ route('admin.orders.index') }}";
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                    $('#submit-order').prop('disabled', false).text('Create Order');
                }
            },
            error: function(xhr) {
                let msg = 'Failed to create order.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                Swal.fire('Error', msg, 'error');
                $('#submit-order').prop('disabled', false).text('Create Order');
            }
        });
    });
});
</script>
@endpush

@endsection
