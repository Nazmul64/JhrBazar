@extends('admin.master')

@section('content')
<style>
    .cursor-pointer { cursor: pointer; }
    .image-upload-wrapper { 
        transition: all 0.3s ease; 
        border: 2px dashed #cbd5e1 !important;
        border-radius: 15px !important;
        background: #f8fafc;
        position: relative;
        overflow: hidden;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .image-upload-wrapper:hover { 
        border-color: #e11d48 !important; 
        background: #fff1f2 !important;
    }
    .card { border: none !important; border-radius: 15px !important; box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important; }
    .form-control, .form-select { border-radius: 10px !important; padding: 12px 15px; border: 1px solid #e2e8f0; }
    .form-control:focus, .form-select:focus { border-color: #e11d48; box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.1); }
    .btn { border-radius: 10px !important; transition: all 0.2s ease; }
    .btn-primary { background: #e11d48 !important; border: none !important; }
    .btn-primary:hover { background: #be123c !important; transform: translateY(-1px); }
    .table thead th { background: #f8fafc; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; color: #64748b; border: none; }
</style>

<div class="container-fluid py-4">
    {{-- Validation Errors Display --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <ul class="mb-0 small fw-bold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('seller.purchase.store') }}" method="POST" enctype="multipart/form-data" id="purchaseForm">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                {{-- General Info --}}
                <div class="card mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-plus me-2 text-primary"></i> General Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold">Purchase Title / Reference</label>
                                <input type="text" name="purchase_name" class="form-control" placeholder="e.g. New Stock for June" value="{{ old('purchase_name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Received Date <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Supplier <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="supplier_id" class="form-select" required>
                                        <option value="">Choose Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('seller.supplier.create') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-plus-lg"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold">Notes</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="Write any specific instruction or note...">{{ old('note') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products Table --}}
                <div class="card mb-4">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam me-2 text-primary"></i> Selected Products</h6>
                        <button type="button" class="btn btn-primary btn-sm px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#productModal">
                            <i class="bi bi-plus-circle-fill me-2"></i> Add Product
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="selectedProductsTable">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Product Details</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Unit Price</th>
                                        <th class="text-center">Subtotal</th>
                                        <th class="text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="selectedProductsList">
                                    <tr id="noProductsRow">
                                        <td colspan="5" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="bi bi-cart-x text-muted display-4 d-block mb-3"></i>
                                                <span class="text-muted fw-medium">No products selected yet. Please add products to your list.</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 py-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted fw-bold me-3">GRAND TOTAL:</span>
                                    <h3 class="fw-bold mb-0 text-primary">$<span id="grandTotal">0.00</span></h3>
                                    <input type="hidden" name="total_amount" id="totalAmountInput" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 fw-bold small text-muted">PAID AMOUNT</span>
                                    <input type="number" name="paid_amount" id="paidAmount" class="form-control fw-bold text-end" placeholder="0.00" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Purchase Slip --}}
                <div class="card mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-image me-2 text-primary"></i> Purchase Slip / Photo</h6>
                    </div>
                    <div class="card-body">
                        <div class="image-upload-wrapper">
                            <input type="file" name="purchase_slip" id="purchaseSlip" class="d-none" accept="image/*">
                            <label for="purchaseSlip" class="cursor-pointer mb-0 w-100 h-100 text-center p-4">
                                <div id="previewPlaceholder">
                                    <i class="bi bi-cloud-arrow-up-fill text-primary display-4"></i>
                                    <p class="mb-0 text-muted small fw-bold mt-2">Click to Upload Slip Image</p>
                                    <p class="text-muted tiny mt-1">(JPG, PNG or PDF)</p>
                                </div>
                                <img id="imagePreview" src="#" alt="Preview" class="img-fluid d-none rounded shadow-sm">
                            </label>
                        </div>
                        <div id="fileInfo" class="mt-2 text-center small text-success fw-bold d-none">
                            <i class="bi bi-check-circle-fill me-1"></i> File selected!
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-3">
                    <button type="submit" class="btn btn-primary py-3 fw-bold shadow-lg" id="submitBtn">
                        <i class="bi bi-check2-all me-2"></i> COMPLETE PURCHASE
                    </button>
                    <a href="{{ route('seller.purchase.index') }}" class="btn btn-light py-2 text-muted fw-bold">
                        Discard & Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Select Products From Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <div class="input-group mb-4 shadow-sm rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search"></i></span>
                    <input type="text" id="modalSearch" class="form-control border-0" placeholder="Search by name, sku, or barcode...">
                </div>
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover align-middle" id="modalTable">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Product</th>
                                <th>Buying Price</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset($product->thumbnail ?? 'assets/images/no-image.png') }}" class="rounded me-3 border" style="width: 45px; height: 45px; object-fit: cover;">
                                        <div>
                                            <span class="fw-bold text-dark d-block mb-0">{{ $product->name }}</span>
                                            <small class="text-muted">SKU: {{ $product->sku }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-bold text-dark">${{ number_format($product->buying_price, 2) }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-dark btn-sm px-3 rounded-pill add-to-list"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-price="{{ $product->buying_price }}">
                                        Add To List
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const selectedProducts = new Set();
    const tableBody = document.getElementById('selectedProductsList');
    const grandTotalSpan = document.getElementById('grandTotal');
    const totalAmountInput = document.getElementById('totalAmountInput');

    // Total Calculation
    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.item-total').forEach(span => {
            total += parseFloat(span.innerText) || 0;
        });
        grandTotalSpan.innerText = total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        totalAmountInput.value = total.toFixed(2);
    }

    // Update individual row total
    function updateRowTotal(row) {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const total = qty * price;
        row.querySelector('.item-total').innerText = total.toFixed(2);
        calculateGrandTotal();
    }

    // Add product to list
    document.querySelectorAll('.add-to-list').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price) || 0;

            if (selectedProducts.has(id)) {
                Swal.fire({ icon: 'warning', title: 'Already Added', text: 'This product is already in your selection.', timer: 1500, showConfirmButton: false });
                return;
            }

            // Hide the empty row
            const emptyRow = document.getElementById('noProductsRow');
            if (emptyRow) emptyRow.style.display = 'none';

            selectedProducts.add(id);

            const row = document.createElement('tr');
            row.id = `row-${id}`;
            row.className = 'fade-in';
            row.innerHTML = `
                <td class="ps-4">
                    <span class="fw-bold d-block text-dark small">${name}</span>
                    <input type="hidden" name="products[${id}][id]" value="${id}">
                </td>
                <td class="text-center" style="width: 120px;">
                    <input type="number" name="products[${id}][qty]" class="form-control form-control-sm text-center qty-input" value="1" min="1">
                </td>
                <td class="text-center" style="width: 150px;">
                    <input type="number" name="products[${id}][price]" class="form-control form-control-sm text-center price-input" value="${price.toFixed(2)}" step="0.01">
                </td>
                <td class="text-center fw-bold text-primary">
                    $<span class="item-total">${price.toFixed(2)}</span>
                </td>
                <td class="text-end pe-4">
                    <button type="button" class="btn btn-sm btn-link text-danger remove-item" data-id="${id}">
                        <i class="bi bi-trash-fill fs-5"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
            calculateGrandTotal();

            // Event listeners for new row
            row.querySelector('.qty-input').addEventListener('input', () => updateRowTotal(row));
            row.querySelector('.price-input').addEventListener('input', () => updateRowTotal(row));
            row.querySelector('.remove-item').addEventListener('click', function() {
                selectedProducts.delete(this.dataset.id);
                row.remove();
                if (selectedProducts.size === 0) {
                    const noRow = document.getElementById('noProductsRow');
                    if (noRow) noRow.style.display = 'table-row';
                }
                calculateGrandTotal();
            });

            // Feedback
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1000 });
            Toast.fire({ icon: 'success', title: 'Product Added!' });
        });
    });

    // Modal Search Logic
    document.getElementById('modalSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#modalTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
        });
    });

    // Image Preview Logic (SHOW PHOTO)
    const purchaseSlip = document.getElementById('purchaseSlip');
    const imagePreview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('previewPlaceholder');
    const fileInfo = document.getElementById('fileInfo');

    if (purchaseSlip) {
        purchaseSlip.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                    fileInfo.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Form Submission Check
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        if (selectedProducts.size === 0) {
            e.preventDefault();
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Please add at least one product to your purchase list!' });
            return false;
        }
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
        submitBtn.disabled = true;
    });
});
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .fade-in { animation: fadeIn 0.3s ease-out; }
    .tiny { font-size: 10px; }
</style>
@endsection
