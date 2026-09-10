@extends('admin.master')

@section('title', 'Stock Management & Inventory Ledger')

@section('content')
<style>
    .stat-card-custom {
        border-radius: 16px;
        padding: 20px;
        transition: all 0.25s ease;
        border: 1px solid rgba(0,0,0,0.06);
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: block;
    }
    .stat-card-custom:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    .stat-card-custom.active-card {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 2px rgba(99,102,241,0.25);
    }
    .stat-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .inv-table-wrap {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .badge-status-pill {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .table-prod-img {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid rgba(0,0,0,0.08);
    }
    .history-timeline-item {
        position: relative;
        padding-left: 28px;
        padding-bottom: 20px;
        border-left: 2px solid #e2e8f0;
    }
    .history-timeline-item:last-child {
        border-left: 2px solid transparent;
        padding-bottom: 0;
    }
    .history-timeline-dot {
        position: absolute;
        left: -7px;
        top: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ── Header ── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-box-seam-fill text-primary"></i> Stock Management & Ledger
            </h4>
            <p class="text-muted small mb-0">Track real-time product quantities, inventory ledger logs, and low stock warnings.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('products.create') }}" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> Add New Product
            </a>
        </div>
    </div>

    {{-- ── Top Stat Cards ── --}}
    <div class="row g-3 mb-4">
        {{-- Total Products --}}
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route('admin.inventory.index') }}" class="card stat-card-custom bg-white {{ !request('status') || request('status') === 'all' ? 'active-card' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">Total Products</div>
                        <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalProducts) }}</h3>
                    </div>
                    <div class="stat-icon-wrap bg-primary-subtle text-primary">
                        <i class="bi bi-boxes"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- In Stock Items --}}
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route('admin.inventory.index', ['status' => 'in_stock']) }}" class="card stat-card-custom bg-white {{ request('status') === 'in_stock' ? 'active-card' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">In Stock</div>
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($inStockCount) }}</h3>
                    </div>
                    <div class="stat-icon-wrap bg-success-subtle text-success">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Low Stock Items --}}
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route('admin.inventory.index', ['status' => 'low_stock']) }}" class="card stat-card-custom bg-white {{ request('status') === 'low_stock' ? 'active-card' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">Low Stock (≤ Alert)</div>
                        <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($lowStockCount) }}</h3>
                    </div>
                    <div class="stat-icon-wrap bg-warning-subtle text-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Out of Stock Items --}}
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route('admin.inventory.index', ['status' => 'out_of_stock']) }}" class="card stat-card-custom bg-white {{ request('status') === 'out_of_stock' ? 'active-card' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">Out Of Stock</div>
                        <h3 class="fw-bold text-danger mb-0 mt-1">{{ number_format($outOfStockCount) }}</h3>
                    </div>
                    <div class="stat-icon-wrap bg-danger-subtle text-danger">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Unlimited Stock Items --}}
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route('admin.inventory.index', ['status' => 'unlimited']) }}" class="card stat-card-custom bg-white {{ request('status') === 'unlimited' ? 'active-card' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">Unlimited Stock</div>
                        <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($unlimitedCount) }}</h3>
                    </div>
                    <div class="stat-icon-wrap bg-info-subtle text-info">
                        <i class="bi bi-infinity"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ── Filters & Search Toolbar ── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.inventory.index') }}" class="row g-2 align-items-center">
                {{-- Preserve status filter if set --}}
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                {{-- Search --}}
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Search by name, SKU, or barcode..." value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Category Filter --}}
                <div class="col-6 col-md-3">
                    <select name="category_id" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="" {{ !request('status') ? 'selected' : '' }}>All Stock Statuses</option>
                        <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out Of Stock</option>
                        <option value="unlimited" {{ request('status') === 'unlimited' ? 'selected' : '' }}>Unlimited</option>
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-3">Filter</button>
                    @if(request()->hasAny(['search', 'category_id', 'status']))
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-light rounded-3" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── Inventory Table ── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table-premium">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Product Details</th>
                        <th>Category</th>
                        <th class="text-center">Total In</th>
                        <th class="text-center">Total Sold</th>
                        <th class="text-center">Available Stock</th>
                        <th class="text-center">Alert Threshold</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr id="product-row-{{ $product->id }}">
                            {{-- Product Name & SKU --}}
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $product->thumbnail ? asset($product->thumbnail) : 'https://placehold.co/46x46?text=No+Img' }}"
                                         alt="{{ $product->name }}" class="table-prod-img shadow-sm"
                                         onerror="this.src='https://placehold.co/46x46?text=No+Img'">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                                        <div class="small text-muted d-flex align-items-center gap-2 mt-0.5">
                                            <span>SKU: <code class="text-primary">{{ $product->sku }}</code></span>
                                            @if($product->barcode)
                                                <span>| Barcode: <code>{{ $product->barcode }}</code></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Category --}}
                            <td>
                                <span class="badge bg-light text-dark border">{{ $product->category->name ?? '—' }}</span>
                            </td>

                            {{-- Total In --}}
                            <td class="text-center font-monospace fw-semibold text-secondary">
                                <span class="prod-total-in-{{ $product->id }}">
                                    {{ $product->is_unlimited ? '—' : number_format($product->total_in) }}
                                </span>
                            </td>

                            {{-- Total Sold --}}
                            <td class="text-center font-monospace fw-semibold text-primary">
                                <span class="prod-total-sold-{{ $product->id }}">
                                    {{ number_format($product->total_sold) }}
                                </span>
                            </td>

                            {{-- Available Stock --}}
                            <td class="text-center font-monospace">
                                @if($product->is_unlimited)
                                    <span class="badge bg-info-subtle text-info fw-bold py-1 px-2">
                                        <i class="bi bi-infinity"></i> Unlimited
                                    </span>
                                @else
                                    <span class="fs-6 fw-bold prod-stock-qty-{{ $product->id }} {{ $product->stock_quantity <= 0 ? 'text-danger' : ($product->isLowStock() ? 'text-warning' : 'text-success') }}">
                                        {{ number_format($product->stock_quantity) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Threshold --}}
                            <td class="text-center text-muted small">
                                {{ $product->is_unlimited ? '—' : '≤ ' . ($product->low_stock_threshold ?? 3) }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="text-center prod-status-badge-{{ $product->id }}">
                                @if($product->is_unlimited)
                                    <span class="badge-status-pill bg-info-subtle text-info">
                                        <i class="bi bi-infinity"></i> Unlimited
                                    </span>
                                @elseif($product->stock_quantity <= 0)
                                    <span class="badge-status-pill bg-danger-subtle text-danger">
                                        <i class="bi bi-x-circle-fill"></i> Out of Stock
                                    </span>
                                @elseif($product->isLowStock())
                                    <span class="badge-status-pill bg-warning-subtle text-warning-emphasis">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Low Stock
                                    </span>
                                @else
                                    <span class="badge-status-pill bg-success-subtle text-success">
                                        <i class="bi bi-check-circle-fill"></i> In Stock
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    @if(!$product->is_unlimited)
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1 me-1"
                                                onclick="openAdjustModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock_quantity }})"
                                                title="Quick Stock In / Adjust">
                                            <i class="bi bi-plus-slash-minus me-1"></i> Adjust
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1"
                                            onclick="openHistoryModal({{ $product->id }})"
                                            title="View Stock Ledger History">
                                        <i class="bi bi-clock-history me-1"></i> History
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                No products found matching your inventory filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{--  MODAL: Quick Stock In / Manual Adjustment                     --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="adjustStockModalLabel">
                    <i class="bi bi-boxes text-primary me-1"></i> Update Stock Quantity
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adjustStockForm" onsubmit="submitStockAdjustment(event)">
                <div class="modal-body pt-3">
                    <input type="hidden" id="adjust_product_id" name="product_id">
                    
                    {{-- Target product info banner --}}
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="fw-semibold text-dark" id="adjust_product_name">—</div>
                        <div class="small text-muted">Current Stock: <strong class="text-primary fs-6" id="adjust_current_stock">0</strong> units</div>
                    </div>

                    {{-- Action Type Tabs --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Adjustment Action</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="action" id="action_stock_in" value="stock_in" checked onchange="toggleActionInputs('stock_in')">
                            <label class="btn btn-outline-primary" for="action_stock_in">
                                <i class="bi bi-plus-circle me-1"></i> Stock In (+ Add)
                            </label>

                            <input type="radio" class="btn-check" name="action" id="action_manual_adjust" value="manual_adjustment" onchange="toggleActionInputs('manual_adjustment')">
                            <label class="btn btn-outline-secondary" for="action_manual_adjust">
                                <i class="bi bi-pencil-square me-1"></i> Set Exact Stock
                            </label>
                        </div>
                    </div>

                    {{-- Stock In Quantity input --}}
                    <div class="mb-3" id="stock_in_group">
                        <label class="form-label small fw-semibold">Quantity to Add <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-plus-lg"></i></span>
                            <input type="number" id="adjust_quantity" name="quantity" min="1" class="form-control" placeholder="e.g. 50">
                        </div>
                        <small class="text-muted" style="font-size: 11px;">This quantity will be added to existing stock and total stock-in count.</small>
                    </div>

                    {{-- Set Exact Stock input --}}
                    <div class="mb-3 d-none" id="manual_adjust_group">
                        <label class="form-label small fw-semibold">New Exact Stock Value <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-check2"></i></span>
                            <input type="number" id="adjust_new_stock" name="new_stock" min="0" class="form-control" placeholder="e.g. 100">
                        </div>
                        <small class="text-muted" style="font-size: 11px;">Directly sets the new stock count. A ledger difference log will be generated.</small>
                    </div>

                    {{-- Remarks / Notes --}}
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Note / Remarks (Optional)</label>
                        <input type="text" id="adjust_note" name="note" class="form-control" placeholder="e.g. Purchased new batch from supplier, audit correction">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSubmitAdjustment" class="btn btn-primary rounded-3 px-4 fw-semibold">
                        <span id="btnAdjustSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                        Save Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{--  MODAL: Stock Movement Ledger History                          --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <img id="hist_prod_img" src="" alt="" class="table-prod-img shadow-sm" onerror="this.src='https://placehold.co/46x46?text=No+Img'">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="hist_prod_name">Product History</h5>
                        <div class="small text-muted mt-0.5">
                            SKU: <code class="text-primary" id="hist_prod_sku">—</code> | 
                            Current Stock: <strong class="text-success" id="hist_prod_stock">—</strong> | 
                            Total In: <strong id="hist_prod_in">—</strong> | 
                            Total Sold: <strong id="hist_prod_sold">—</strong>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="hist_loading_spinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted small mt-2">Loading inventory ledger...</p>
                </div>

                <div id="hist_content" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr style="font-size: 12px;">
                                    <th>Date & Time</th>
                                    <th>Type</th>
                                    <th class="text-center">Qty Change</th>
                                    <th class="text-center">Before → After</th>
                                    <th>Ref / Order</th>
                                    <th>Remarks</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody id="hist_table_body">
                                {{-- Filled via JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="hist_empty_state" class="text-center py-5 text-muted d-none">
                    <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                    No ledger entries found for this product yet.
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ── Open Adjust Modal ─────────────────────────────────────────
    function openAdjustModal(productId, productName, currentStock) {
        document.getElementById('adjust_product_id').value = productId;
        document.getElementById('adjust_product_name').innerText = productName;
        document.getElementById('adjust_current_stock').innerText = currentStock;
        document.getElementById('adjust_quantity').value = '';
        document.getElementById('adjust_new_stock').value = currentStock;
        document.getElementById('adjust_note').value = '';

        // Reset tabs
        document.getElementById('action_stock_in').checked = true;
        toggleActionInputs('stock_in');

        const modal = new bootstrap.Modal(document.getElementById('adjustStockModal'));
        modal.show();
    }

    // ── Toggle Action Inputs ──────────────────────────────────────
    function toggleActionInputs(action) {
        const stockInGroup = document.getElementById('stock_in_group');
        const manualGroup  = document.getElementById('manual_adjust_group');
        const qtyInput     = document.getElementById('adjust_quantity');
        const newStockInput= document.getElementById('adjust_new_stock');

        if (action === 'stock_in') {
            stockInGroup.classList.remove('d-none');
            manualGroup.classList.add('d-none');
            qtyInput.setAttribute('required', 'required');
            newStockInput.removeAttribute('required');
        } else {
            stockInGroup.classList.add('d-none');
            manualGroup.classList.remove('d-none');
            qtyInput.removeAttribute('required');
            newStockInput.setAttribute('required', 'required');
        }
    }

    // ── Submit Stock Adjustment via AJAX ──────────────────────────
    function submitStockAdjustment(e) {
        e.preventDefault();
        const form = document.getElementById('adjustStockForm');
        const btn  = document.getElementById('btnSubmitAdjustment');
        const spin = document.getElementById('btnAdjustSpinner');

        const productId = document.getElementById('adjust_product_id').value;
        const action    = document.querySelector('input[name="action"]:checked').value;
        const quantity  = document.getElementById('adjust_quantity').value;
        const newStock  = document.getElementById('adjust_new_stock').value;
        const note      = document.getElementById('adjust_note').value;

        btn.disabled = true;
        spin.classList.remove('d-none');

        fetch("{{ route('admin.inventory.adjust') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                product_id: productId,
                action: action,
                quantity: quantity,
                new_stock: newStock,
                note: note
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            spin.classList.add('d-none');

            if (data.success) {
                // Update table row dynamically
                const qtyElem = document.querySelector(`.prod-stock-qty-${productId}`);
                const inElem  = document.querySelector(`.prod-total-in-${productId}`);
                const statElem= document.querySelector(`.prod-status-badge-${productId}`);

                if (qtyElem) {
                    qtyElem.innerText = data.stock_quantity;
                    qtyElem.className = `fs-6 fw-bold prod-stock-qty-${productId} ` + (data.stock_quantity <= 0 ? 'text-danger' : (data.status === 'low_stock' ? 'text-warning' : 'text-success'));
                }
                if (inElem) inElem.innerText = data.total_in;

                if (statElem) {
                    let badgeHtml = '';
                    if (data.status === 'out_of_stock') {
                        badgeHtml = '<span class="badge-status-pill bg-danger-subtle text-danger"><i class="bi bi-x-circle-fill"></i> Out of Stock</span>';
                    } else if (data.status === 'low_stock') {
                        badgeHtml = '<span class="badge-status-pill bg-warning-subtle text-warning-emphasis"><i class="bi bi-exclamation-triangle-fill"></i> Low Stock</span>';
                    } else {
                        badgeHtml = '<span class="badge-status-pill bg-success-subtle text-success"><i class="bi bi-check-circle-fill"></i> In Stock</span>';
                    }
                    statElem.innerHTML = badgeHtml;
                }

                bootstrap.Modal.getInstance(document.getElementById('adjustStockModal')).hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Stock Updated',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Adjustment Failed',
                    text: data.message || 'Error updating stock.'
                });
            }
        })
        .catch(err => {
            btn.disabled = false;
            spin.classList.add('d-none');
            Swal.fire({ icon: 'error', title: 'Network Error', text: err.message });
        });
    }

    // ── Open History View Modal ───────────────────────────────────
    function openHistoryModal(productId) {
        const modal = new bootstrap.Modal(document.getElementById('historyModal'));
        modal.show();

        const spinner    = document.getElementById('hist_loading_spinner');
        const content    = document.getElementById('hist_content');
        const emptyState = document.getElementById('hist_empty_state');
        const tbody      = document.getElementById('hist_table_body');

        spinner.classList.remove('d-none');
        content.classList.add('d-none');
        emptyState.classList.add('d-none');
        tbody.innerHTML = '';

        fetch(`{{ url('admin/stock-management/history') }}/${productId}`)
            .then(res => res.json())
            .then(data => {
                spinner.classList.add('d-none');

                if (data.success && data.product) {
                    const prod = data.product;
                    document.getElementById('hist_prod_img').src = prod.thumbnail || 'https://placehold.co/46x46?text=No+Img';
                    document.getElementById('hist_prod_name').innerText = prod.name;
                    document.getElementById('hist_prod_sku').innerText = prod.sku;
                    document.getElementById('hist_prod_stock').innerText = prod.is_unlimited ? 'Unlimited' : prod.stock_quantity + ' units';
                    document.getElementById('hist_prod_in').innerText = prod.total_in;
                    document.getElementById('hist_prod_sold').innerText = prod.total_sold;

                    if (data.ledgers && data.ledgers.length > 0) {
                        content.classList.remove('d-none');

                        let rows = '';
                        data.ledgers.forEach(item => {
                            const isPositive = item.quantity > 0;
                            const qtyClass   = isPositive ? 'text-success fw-bold' : 'text-danger fw-bold';
                            const qtySign    = isPositive ? '+' : '';

                            rows += `
                                <tr>
                                    <td class="small text-muted">${item.date}</td>
                                    <td>
                                        <span class="badge ${item.type_badge} py-1 px-2" style="font-size: 11px;">
                                            ${item.type_label}
                                        </span>
                                    </td>
                                    <td class="text-center font-monospace ${qtyClass}">
                                        ${qtySign}${item.quantity}
                                    </td>
                                    <td class="text-center font-monospace small text-muted">
                                        ${item.previous_stock} → <strong>${item.current_stock}</strong>
                                    </td>
                                    <td>
                                        ${item.invoice_number ? `<span class="badge bg-light text-primary border">${item.invoice_number}</span>` : '<span class="text-muted">—</span>'}
                                    </td>
                                    <td class="small text-secondary">${item.note || '—'}</td>
                                    <td class="small text-muted">${item.admin_name}</td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = rows;
                    } else {
                        emptyState.classList.remove('d-none');
                    }
                } else {
                    emptyState.classList.remove('d-none');
                }
            })
            .catch(err => {
                spinner.classList.add('d-none');
                emptyState.classList.remove('d-none');
            });
    }
</script>
@endsection
