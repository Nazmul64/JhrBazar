{{-- resources/views/admin/flashsale/show.blade.php --}}
@extends('admin.master')
@section('content')
<style>
    :root {
        --brand:       #e8174a;
        --brand-light: rgba(232,23,74,.08);
        --brand-hover: #c9113e;
        --dark:        #1a1d23;
        --muted:       #6b7280;
        --border:      #e5e7eb;
        --surface:     #f8f9fc;
        --shadow:      0 1px 4px rgba(0,0,0,.06), 0 2px 12px rgba(0,0,0,.04);
        --r-lg: 14px; --r-md: 10px; --r-sm: 7px;
        --ease: all .18s ease;
    }

    /* ── Page Header ── */
    .ph { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:10px; }
    .ph-left { display:flex; align-items:center; gap:12px; }
    .ph-back {
        width:36px; height:36px;
        display:inline-flex; align-items:center; justify-content:center;
        border-radius:var(--r-sm); border:1.5px solid var(--border);
        background:#fff; color:var(--muted); font-size:16px;
        text-decoration:none; transition:var(--ease);
    }
    .ph-back:hover { border-color:var(--brand); color:var(--brand); background:var(--brand-light); }
    .ph-title { font-size:1.4rem; font-weight:700; color:var(--dark); margin:0; }
    .ph-sub   { font-size:13px; color:var(--muted); margin:3px 0 0; }
    .ph-actions { display:flex; align-items:center; gap:8px; }

    .btn-edit {
        display:inline-flex; align-items:center; gap:7px;
        background:transparent; border:1.5px solid rgba(20,184,166,.35);
        color:#0d9488; border-radius:var(--r-md); padding:9px 20px;
        font-size:13px; font-weight:600; text-decoration:none; transition:var(--ease);
    }
    .btn-edit:hover { background:#0d9488; border-color:#0d9488; color:#fff; }

    .btn-del-page {
        display:inline-flex; align-items:center; gap:7px;
        background:transparent; border:1.5px solid rgba(239,68,68,.35);
        color:#ef4444; border-radius:var(--r-md); padding:9px 20px;
        font-size:13px; font-weight:600; text-decoration:none;
        cursor:pointer; transition:var(--ease);
    }
    .btn-del-page:hover { background:#ef4444; border-color:#ef4444; color:#fff; }

    /* ── Alert ── */
    .alert-ok {
        background:#f0fdf4; color:#15803d;
        border-left:3.5px solid #22c55e;
        border-radius:var(--r-md); padding:12px 16px;
        font-size:13.5px; margin-bottom:1.2rem;
        display:flex; align-items:center; gap:9px;
    }

    /* ── Info Card ── */
    .info-card {
        background:#fff; border-radius:var(--r-lg);
        box-shadow:var(--shadow); border:1px solid var(--border);
        overflow:hidden; margin-bottom:20px;
    }
    .info-card-header {
        padding:16px 24px; border-bottom:1px solid var(--border);
        background:var(--surface); display:flex; align-items:center;
        justify-content:space-between; gap:10px;
    }
    .info-card-header-left { display:flex; align-items:center; gap:10px; }
    .info-card-icon {
        width:36px; height:36px; border-radius:var(--r-sm);
        background:var(--brand-light); color:var(--brand);
        display:flex; align-items:center; justify-content:center; font-size:17px;
    }
    .info-card-title { font-size:14px; font-weight:700; color:var(--dark); margin:0; }
    .info-card-sub   { font-size:12px; color:var(--muted); margin:2px 0 0; }
    .info-card-body  { padding:24px; }

    /* ── Detail Grid ── */
    .detail-grid {
        display:grid; grid-template-columns:repeat(3,1fr); gap:0;
        border:1px solid var(--border); border-radius:var(--r-md); overflow:hidden;
    }
    .detail-cell {
        padding:16px 20px; border-right:1px solid var(--border);
        border-bottom:1px solid var(--border);
    }
    .detail-cell:nth-child(3n) { border-right:none; }
    .detail-cell:nth-last-child(-n+3) { border-bottom:none; }
    .detail-cell .dc-label {
        font-size:11.5px; font-weight:700; color:var(--muted);
        text-transform:uppercase; letter-spacing:.45px; margin-bottom:6px;
    }
    .detail-cell .dc-value {
        font-size:13.5px; font-weight:600; color:var(--dark); line-height:1.4;
    }
    .detail-cell .dc-value.mono { font-family:monospace; font-size:13px; }

    /* ── Status pill ── */
    .status-pill {
        display:inline-flex; align-items:center; gap:6px;
        padding:5px 13px; border-radius:20px; font-size:12px; font-weight:700;
    }
    .status-pill.active   { background:#f0fdf4; color:#16a34a; border:1.5px solid #bbf7d0; }
    .status-pill.inactive { background:#f9fafb; color:var(--muted); border:1.5px solid var(--border); }
    .status-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
    .status-dot.active   { background:#22c55e; }
    .status-dot.inactive { background:#d1d5db; }

    /* ── Discount badge ── */
    .disc-badge {
        display:inline-flex; align-items:center; gap:5px;
        background:var(--brand-light); color:var(--brand);
        border-radius:20px; padding:4px 13px;
        font-size:13px; font-weight:700;
    }

    /* ── Thumbnail in detail ── */
    .detail-thumb {
        width:90px; height:62px; object-fit:cover;
        border-radius:var(--r-sm); border:1px solid var(--border);
    }
    .detail-thumb-ph {
        width:90px; height:62px; background:var(--surface);
        border-radius:var(--r-sm); border:1px solid var(--border);
        display:flex; align-items:center; justify-content:center;
        color:#d1d5db; font-size:22px;
    }

    /* ── Description ── */
    .desc-box {
        margin-top:18px; padding:14px 18px;
        background:var(--surface); border-radius:var(--r-md);
        border:1px solid var(--border); font-size:13.5px;
        color:var(--dark); line-height:1.7;
    }
    .desc-box-label {
        font-size:11.5px; font-weight:700; color:var(--muted);
        text-transform:uppercase; letter-spacing:.45px; margin-bottom:8px;
    }

    /* ── Products Table Card ── */
    .prod-card {
        background:#fff; border-radius:var(--r-lg);
        box-shadow:var(--shadow); border:1px solid var(--border);
        overflow:hidden;
    }
    .prod-card-header {
        padding:16px 24px; border-bottom:1px solid var(--border);
        background:var(--surface); display:flex; align-items:center;
        justify-content:space-between; gap:10px;
    }
    .prod-card-header-left { display:flex; align-items:center; gap:10px; }

    .btn-add-prod {
        display:inline-flex; align-items:center; gap:7px;
        background:var(--brand); color:#fff; border:none;
        border-radius:var(--r-md); padding:9px 20px;
        font-size:13px; font-weight:600; cursor:pointer;
        box-shadow:0 2px 10px rgba(232,23,74,.3);
        text-decoration:none; transition:var(--ease);
    }
    .btn-add-prod:hover { background:var(--brand-hover); color:#fff; transform:translateY(-1px); }

    /* count badge */
    .count-badge {
        display:inline-flex; align-items:center; justify-content:center;
        background:var(--brand); color:#fff; border-radius:20px;
        padding:2px 10px; font-size:11px; font-weight:700; min-width:24px;
    }

    /* ── Products Table ── */
    .prod-table { width:100%; border-collapse:collapse; }
    .prod-table th {
        padding:12px 18px; font-size:12px; font-weight:700;
        text-transform:uppercase; letter-spacing:.4px;
        color:var(--muted); background:var(--surface);
        border-bottom:1px solid var(--border); white-space:nowrap;
    }
    .prod-table td {
        padding:13px 18px; font-size:13px; color:var(--dark);
        border-bottom:1px solid var(--border); vertical-align:middle;
    }
    .prod-table tbody tr:last-child td { border-bottom:none; }
    .prod-table tbody tr:hover { background:#fafbff; }

    /* ── Product thumb ── */
    .p-thumb {
        width:56px; height:40px; object-fit:cover;
        border-radius:var(--r-sm); border:1px solid var(--border); display:block;
    }
    .p-thumb-ph {
        width:56px; height:40px; background:var(--surface);
        border-radius:var(--r-sm); border:1px solid var(--border);
        display:flex; align-items:center; justify-content:center;
        color:#d1d5db; font-size:16px;
    }

    /* ── Category badge ── */
    .cat-badge {
        display:inline-flex; align-items:center; gap:4px;
        background:#eff6ff; color:#2563eb;
        border-radius:20px; padding:3px 10px;
        font-size:11.5px; font-weight:600;
    }

    /* ── Price ── */
    .price-orig { font-size:12px; color:var(--muted); text-decoration:line-through; }
    .price-sale  { font-size:13.5px; font-weight:700; color:var(--brand); }

    /* ── Discount tag ── */
    .disc-tag {
        display:inline-flex; align-items:center;
        background:#fff1f2; color:var(--brand);
        border-radius:var(--r-sm); padding:2px 8px;
        font-size:11.5px; font-weight:700; margin-left:4px;
    }

    /* ── Qty ── */
    .qty-badge {
        display:inline-flex; align-items:center; justify-content:center;
        background:var(--surface); border:1.5px solid var(--border);
        border-radius:var(--r-sm); padding:3px 12px;
        font-size:13px; font-weight:600; color:var(--dark);
    }

    /* ── Remove btn ── */
    .btn-rm {
        width:30px; height:30px;
        display:inline-flex; align-items:center; justify-content:center;
        border-radius:var(--r-sm); border:1.5px solid rgba(239,68,68,.3);
        background:transparent; color:#ef4444; cursor:pointer;
        font-size:13px; transition:var(--ease);
    }
    .btn-rm:hover { background:#ef4444; border-color:#ef4444; color:#fff; }

    /* ── Empty state ── */
    .empty-row td { text-align:center; padding:50px 20px; color:var(--muted); font-size:13px; }

    /* ── Delete modal ── */
    .modal-content { border-radius:var(--r-lg) !important; border:none !important; box-shadow:0 20px 60px rgba(0,0,0,.15) !important; }
    .del-icon {
        width:60px; height:60px; background:#fff1f3; border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        margin:0 auto 14px; font-size:26px; color:#ef4444;
    }
    .btn-del-confirm { background:#ef4444; border:none; color:#fff; border-radius:var(--r-sm); padding:8px 24px; font-size:13px; font-weight:600; cursor:pointer; }
    .btn-modal-cancel { background:transparent; border:1.5px solid var(--border); color:var(--muted); border-radius:var(--r-sm); padding:8px 20px; font-size:13px; cursor:pointer; }

    /* ── Toggle form inline ── */
    .toggle-form { display:inline; }

    @media (max-width:768px) {
        .detail-grid { grid-template-columns:1fr 1fr; }
        .detail-cell:nth-child(2n) { border-right:none; }
        .detail-cell:nth-child(3n) { border-right:1px solid var(--border); }
    }
    @media (max-width:500px) {
        .detail-grid { grid-template-columns:1fr; }
        .detail-cell { border-right:none !important; }
    }
</style>

{{-- PAGE HEADER --}}
<div class="ph">
    <div class="ph-left">
        <a href="{{ route('admin.flashsale.index') }}" class="ph-back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="ph-title">Flash Deal Details</h4>
            <p class="ph-sub">{{ $flashsale->name }}</p>
        </div>
    </div>
    <div class="ph-actions">
        <a href="{{ route('admin.flashsale.edit', $flashsale->id) }}" class="btn-edit">
            <i class="bi bi-pencil-square"></i> Edit
        </a>
        <button type="button" class="btn-del-page"
                onclick="openDel({{ $flashsale->id }}, '{{ addslashes($flashsale->name) }}')">
            <i class="bi bi-trash3"></i> Delete
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert-ok">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- ── Flash Sale Info Card ── --}}
<div class="info-card">
    <div class="info-card-header">
        <div class="info-card-header-left">
            <div class="info-card-icon"><i class="bi bi-lightning-charge"></i></div>
            <div>
                <p class="info-card-title">Flash Sale Information</p>
                <p class="info-card-sub">All details about this flash sale event</p>
            </div>
        </div>
        {{-- Inline status toggle --}}
        <form action="{{ route('admin.flashsale.toggle', $flashsale->id) }}"
              method="POST" class="toggle-form">
            @csrf
            <div class="form-check form-switch m-0" style="display:flex;align-items:center;gap:8px;">
                <input class="form-check-input" type="checkbox" role="switch"
                       title="{{ $flashsale->is_active ? 'Active — click to deactivate' : 'Inactive — click to activate' }}"
                       {{ $flashsale->is_active ? 'checked' : '' }}
                       onchange="this.closest('form').submit()">
                <span style="font-size:12.5px;font-weight:600;color:var(--muted);">
                    {{ $flashsale->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </form>
    </div>

    <div class="info-card-body">

        {{-- Detail grid --}}
        <div class="detail-grid">

            {{-- Deal Name --}}
            <div class="detail-cell">
                <div class="dc-label">Deal Name</div>
                <div class="dc-value">{{ $flashsale->name }}</div>
            </div>

            {{-- Start Date --}}
            <div class="detail-cell">
                <div class="dc-label">Start Date</div>
                <div class="dc-value mono">{{ $flashsale->start_date_time_display ?? $flashsale->start_date . ' ' . $flashsale->start_time }}</div>
            </div>

            {{-- End Date --}}
            <div class="detail-cell">
                <div class="dc-label">End Date</div>
                <div class="dc-value mono">{{ $flashsale->end_date_time_display ?? $flashsale->end_date . ' ' . $flashsale->end_time }}</div>
            </div>

            {{-- Minimum Discount --}}
            <div class="detail-cell">
                <div class="dc-label">Minimum Discount</div>
                <div class="dc-value">
                    <span class="disc-badge">
                        <i class="bi bi-tag-fill"></i>
                        {{ $flashsale->minimum_discount }}%
                    </span>
                </div>
            </div>

            {{-- Publish Status --}}
            <div class="detail-cell">
                <div class="dc-label">Publish Status</div>
                <div class="dc-value">
                    @if($flashsale->is_active)
                        <span class="status-pill active">
                            <span class="status-dot active"></span> Active
                        </span>
                    @else
                        <span class="status-pill inactive">
                            <span class="status-dot inactive"></span> Inactive
                        </span>
                    @endif
                </div>
            </div>

            {{-- Thumbnail --}}
            <div class="detail-cell">
                <div class="dc-label">Thumbnail</div>
                <div class="dc-value">
                    @if($flashsale->thumbnail)
                        <img src="{{ asset($flashsale->thumbnail) }}"
                             class="detail-thumb" alt="{{ $flashsale->name }}"
                             onerror="this.style.display='none'">
                    @else
                        <div class="detail-thumb-ph"><i class="bi bi-image"></i></div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Description --}}
        @if($flashsale->description)
        <div class="desc-box">
            <div class="desc-box-label">Description</div>
            {{ $flashsale->description }}
        </div>
        @endif

    </div>
</div>

{{-- ── Added Products Card ── --}}
<div class="prod-card">
    <div class="prod-card-header">
        <div class="prod-card-header-left">
            <div class="info-card-icon"><i class="bi bi-box-seam"></i></div>
            <div>
                <p class="info-card-title">
                    Added Products
                    <span class="count-badge ms-2">{{ $flashsale->products->count() }}</span>
                </p>
                <p class="info-card-sub">Products currently in this flash sale</p>
            </div>
        </div>
        {{-- Add Product button — update route as needed --}}
        <a href="#" class="btn-add-prod">
            <i class="bi bi-plus-circle-fill"></i> Add Product
        </a>
    </div>

    <div class="table-responsive">
        <table class="prod-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Thumbnail</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Shop</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($flashsale->products as $i => $product)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($product->thumbnail ?? $product->image ?? null)
                            <img src="{{ asset($product->thumbnail ?? $product->image) }}"
                                 class="p-thumb" alt="{{ $product->name }}"
                                 onerror="this.style.display='none'">
                        @else
                            <div class="p-thumb-ph"><i class="bi bi-image"></i></div>
                        @endif
                    </td>
                    <td style="font-weight:600; min-width:160px;">{{ $product->name }}</td>
                    <td>
                        @if($product->category)
                            <span class="cat-badge">
                                <i class="bi bi-tag"></i>
                                {{ $product->category->name }}
                            </span>
                        @else
                            <span style="color:var(--muted); font-size:12px;">—</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:13px; color:var(--dark);">
                            {{ $product->shop->name ?? '—' }}
                        </span>
                    </td>
                    <td style="min-width:120px;">
                        @if(isset($product->discount_price) && $product->discount_price < $product->price)
                            <div class="price-orig">৳{{ number_format($product->price, 2) }}</div>
                            <div>
                                <span class="price-sale">৳{{ number_format($product->discount_price, 2) }}</span>
                                @php
                                    $disc = round((($product->price - $product->discount_price) / $product->price) * 100);
                                @endphp
                                <span class="disc-tag">-{{ $disc }}%</span>
                            </div>
                        @else
                            <span class="price-sale">৳{{ number_format($product->price ?? 0, 2) }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="qty-badge">{{ $product->quantity ?? $product->stock ?? 0 }}</span>
                    </td>
                    <td style="text-align:right;">
                        {{-- Remove product from flash sale — update route as needed --}}
                        <button type="button" class="btn-rm" title="Remove from sale"
                                onclick="openRemProd({{ $product->id }}, '{{ addslashes($product->name) }}')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="8">
                        <i class="bi bi-box-seam" style="font-size:36px;color:#d1d5db;display:block;margin-bottom:8px;"></i>
                        No Data Found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Delete Flash Sale Modal ── --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-body" style="padding:32px 24px; text-align:center;">
                <div class="del-icon"><i class="bi bi-trash3-fill"></i></div>
                <p style="font-size:15px; font-weight:700; color:#1a1d23; margin:0 0 8px;">Delete Flash Sale?</p>
                <p style="font-size:13.5px; color:var(--muted); margin:0;">
                    Are you sure you want to delete <strong id="del-name"></strong>?
                    <br>This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer gap-2" style="justify-content:center; border-top:1px solid var(--border); padding:14px 22px;">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">No, Cancel</button>
                <form id="delForm" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del-confirm">
                        <i class="bi bi-trash3 me-1"></i> Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ── Remove Product Modal ── --}}
<div class="modal fade" id="removeProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-body" style="padding:32px 24px; text-align:center;">
                <div class="del-icon" style="background:#fff7ed;">
                    <i class="bi bi-box-arrow-left" style="color:#f97316;"></i>
                </div>
                <p style="font-size:15px; font-weight:700; color:#1a1d23; margin:0 0 8px;">Remove Product?</p>
                <p style="font-size:13.5px; color:var(--muted); margin:0;">
                    Remove <strong id="rem-prod-name"></strong> from this flash sale?
                </p>
            </div>
            <div class="modal-footer gap-2" style="justify-content:center; border-top:1px solid var(--border); padding:14px 22px;">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                <form id="remProdForm" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del-confirm" style="background:#f97316;">
                        <i class="bi bi-box-arrow-left me-1"></i> Yes, Remove
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Delete flash sale
    function openDel(id, name) {
        var base = "{{ route('admin.flashsale.destroy', '__ID__') }}";
        document.getElementById('delForm').action = base.replace('__ID__', id);
        document.getElementById('del-name').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Remove product from flash sale
    // Update the route below to match your actual remove-product route
    function openRemProd(productId, name) {
        var base = "/admin/flashsale/{{ $flashsale->id }}/product/__PID__/remove";
        document.getElementById('remProdForm').action = base.replace('__PID__', productId);
        document.getElementById('rem-prod-name').textContent = name;
        new bootstrap.Modal(document.getElementById('removeProductModal')).show();
    }
</script>
@endsection
