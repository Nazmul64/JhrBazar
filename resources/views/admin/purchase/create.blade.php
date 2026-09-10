@extends('admin.master')

@section('title', 'New Local Purchase Entry')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ── Breadcrumb & Title ── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-cart-plus-fill text-primary"></i> Record Local Purchase
            </h4>
            <p class="text-muted small mb-0">Add purchased stock from local suppliers/vendors. Main product stock will automatically update.</p>
        </div>
        <div>
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Back to Purchases
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please check the form errors:</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.purchases.store') }}" method="POST" enctype="multipart/form-data" id="purchaseForm">
        @csrf

        <div class="row g-4">
            {{-- ── Left / Main Form Column ── --}}
            <div class="col-12 col-xl-8">
                
                {{-- Purchase Header Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-info-circle text-primary me-1"></i> Purchase Information
                        </h6>
                        <div class="row g-3">
                            {{-- Supplier --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Supplier / Vendor <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="supplier_id" id="supplier_id" class="form-select" required>
                                        <option value="">Select Supplier...</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                                {{ $sup->name }} {{ $sup->phone ? '('.$sup->phone.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('admin.supplier.create') }}" target="_blank" class="btn btn-outline-secondary" title="Add New Supplier">
                                        <i class="bi bi-plus-lg"></i>
                                    </a>
                                </div>
                            </div>

                            {{-- Purchase Date --}}
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-semibold">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                            </div>

                            {{-- Invoice Number --}}
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-semibold">Invoice / Reference No</label>
                                <input type="text" name="invoice_no" class="form-control font-monospace" value="{{ old('invoice_no', $nextInvoiceNo) }}" placeholder="Auto generated">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Product Line Items Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-boxes text-primary me-1"></i> Purchased Products
                            </h6>
                            <button type="button" class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold" onclick="addProductRow()">
                                <i class="bi bi-plus-circle me-1"></i> Add Item
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle" id="purchaseItemsTable">
                                <thead class="table-light">
                                    <tr style="font-size: 13px;">
                                        <th style="min-width: 250px;">Product Name / SKU</th>
                                        <th style="width: 140px;">Unit Price (৳)</th>
                                        <th style="width: 120px;">Qty</th>
                                        <th style="width: 140px;" class="text-end">Subtotal (৳)</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="purchaseItemsBody">
                                    {{-- First initial row --}}
                                    <tr class="item-row" data-row-id="0">
                                        <td>
                                            <select name="items[0][product_id]" class="form-select product-select" required onchange="onProductSelect(this, 0)">
                                                <option value="">Select Product...</option>
                                                @foreach($products as $prod)
                                                    <option value="{{ $prod->id }}"
                                                            data-buying-price="{{ $prod->buying_price }}"
                                                            data-stock="{{ $prod->stock_quantity }}"
                                                            data-sku="{{ $prod->sku }}">
                                                        {{ $prod->name }} (SKU: {{ $prod->sku }} | Stock: {{ $prod->is_unlimited ? '∞' : $prod->stock_quantity }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="items[0][unit_price]" class="form-control item-price" placeholder="0.00" required oninput="calculateRowSubtotal(0)">
                                        </td>
                                        <td>
                                            <input type="number" min="1" name="items[0][quantity]" class="form-control item-qty" value="1" required oninput="calculateRowSubtotal(0)">
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold font-monospace item-subtotal-text">৳0.00</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeProductRow(this)" title="Remove Row">
                                                <i class="bi bi-x-circle-fill fs-5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Right Column: Payment & Summary ── --}}
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-wallet2 text-primary me-1"></i> Payment Summary
                        </h6>

                        {{-- Total Amount --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted fw-semibold">Grand Total:</span>
                            <span class="fs-4 fw-bold text-dark font-monospace" id="display_grand_total">৳0.00</span>
                        </div>

                        {{-- Payment Method --}}
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="bKash" {{ old('payment_method') === 'bKash' ? 'selected' : '' }}>bKash</option>
                                <option value="Nagad" {{ old('payment_method') === 'Nagad' ? 'selected' : '' }}>Nagad</option>
                                <option value="Cheque" {{ old('payment_method') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                        </div>

                        {{-- Paid Amount --}}
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Paid Amount (৳)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">৳</span>
                                <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount_input" class="form-control text-success fw-bold font-monospace" placeholder="0.00" value="{{ old('paid_amount', 0) }}" oninput="calculateDue()">
                            </div>
                        </div>

                        {{-- Due Amount --}}
                        <div class="p-3 bg-light rounded-3 mb-3 d-flex justify-content-between align-items-center">
                            <span class="small fw-semibold text-muted">Remaining Due:</span>
                            <span class="fw-bold font-monospace text-danger fs-5" id="display_due_amount">৳0.00</span>
                        </div>

                        {{-- Purchase Slip Upload --}}
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Attachment / Purchase Slip (Optional)</label>
                            <input type="file" name="purchase_slip" class="form-control" accept="image/*,.pdf">
                            <small class="text-muted" style="font-size: 11px;">Upload invoice photo or receipt.</small>
                        </div>

                        {{-- Note --}}
                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Notes / Remarks</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="e.g. Purchased from wholesale market, invoice #...">{{ old('note') }}</textarea>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold fs-6 shadow-sm">
                            <i class="bi bi-check-circle-fill me-1"></i> Save Purchase & Update Stock
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

{{-- Dynamic Row Template & Script --}}
<script>
    let rowIndex = 1;

    const productsData = @json($products);

    function addProductRow() {
        const tbody = document.getElementById('purchaseItemsBody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.dataset.rowId = rowIndex;

        let optionsHtml = '<option value="">Select Product...</option>';
        productsData.forEach(p => {
            optionsHtml += `<option value="${p.id}" data-buying-price="${p.buying_price}" data-stock="${p.stock_quantity}" data-sku="${p.sku}">${p.name} (SKU: ${p.sku} | Stock: ${p.is_unlimited ? '∞' : p.stock_quantity})</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select product-select" required onchange="onProductSelect(this, ${rowIndex})">
                    ${optionsHtml}
                </select>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" class="form-control item-price" placeholder="0.00" required oninput="calculateRowSubtotal(${rowIndex})">
            </td>
            <td>
                <input type="number" min="1" name="items[${rowIndex}][quantity]" class="form-control item-qty" value="1" required oninput="calculateRowSubtotal(${rowIndex})">
            </td>
            <td class="text-end">
                <span class="fw-bold font-monospace item-subtotal-text">৳0.00</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeProductRow(this)" title="Remove Row">
                    <i class="bi bi-x-circle-fill fs-5"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        rowIndex++;
    }

    function removeProductRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length <= 1) {
            Swal.fire({ icon: 'warning', title: 'Action not allowed', text: 'At least one product item is required for a purchase.' });
            return;
        }
        btn.closest('tr').remove();
        calculateGrandTotal();
    }

    function onProductSelect(selectElem, rowId) {
        const selectedOpt = selectElem.options[selectElem.selectedIndex];
        const buyingPrice = selectedOpt.getAttribute('data-buying-price') || 0;

        const row = selectElem.closest('tr');
        const priceInput = row.querySelector('.item-price');
        if (priceInput && (!priceInput.value || priceInput.value == 0)) {
            priceInput.value = parseFloat(buyingPrice).toFixed(2);
        }

        calculateRowSubtotal(rowId);
    }

    function calculateRowSubtotal(rowId) {
        const row = document.querySelector(`.item-row[data-row-id="${rowId}"]`);
        if (!row) return;

        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const qty   = parseInt(row.querySelector('.item-qty').value) || 0;
        const subtotal = price * qty;

        row.querySelector('.item-subtotal-text').innerText = '৳' + subtotal.toFixed(2);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const qty   = parseInt(row.querySelector('.item-qty').value) || 0;
            grandTotal += (price * qty);
        });

        document.getElementById('display_grand_total').innerText = '৳' + grandTotal.toFixed(2);

        calculateDue();
    }

    function calculateDue() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const qty   = parseInt(row.querySelector('.item-qty').value) || 0;
            grandTotal += (price * qty);
        });

        const paid = parseFloat(document.getElementById('paid_amount_input').value) || 0;
        const due  = Math.max(0, grandTotal - paid);

        document.getElementById('display_due_amount').innerText = '৳' + due.toFixed(2);
    }
</script>
@endsection
