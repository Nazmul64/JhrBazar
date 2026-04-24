@extends('admin.master')

@section('content')

@php
    $settings = \App\Models\GenaralSetting::first();
    $cur = $settings->default_currency ?? '৳';
@endphp

<style>
/* ════════════════════════════════════════════════════════════
   Shipping Charge — Index
   ════════════════════════════════════════════════════════════ */
:root {
    --accent:    #e7567c;
    --accent-dk: #c93f65;
    --blue:      #4361ee;
    --green:     #22c55e;
    --green-dk:  #16a34a;
    --warning:   #f59e0b;
    --text:      #1a1f36;
    --muted:     #6b7a99;
    --border:    #e4e9f2;
    --bg:        #f0f2f5;
    --white:     #ffffff;
    --radius:    8px;
    --radius-sm: 5px;
    --shadow:    0 1px 4px rgba(0,0,0,.07);
}

*, *::before, *::after { box-sizing: border-box; }

.sc-page {
    padding: 24px;
    background: var(--bg);
    min-height: 100vh;
    font-family: 'Segoe UI', system-ui, sans-serif;
}

/* ── Page Header ── */
.sc-page-header {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 22px; flex-wrap: wrap; gap: 12px;
}
.sc-page-title { font-size: 20px; font-weight: 800; color: var(--text); margin: 0; }

.btn-add-new {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #e7567c, #c93f65);
    color: #fff; border: none; border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 700; cursor: pointer;
    text-decoration: none; transition: opacity .15s; white-space: nowrap;
}
.btn-add-new:hover { opacity: .88; color: #fff; text-decoration: none; }

/* ── Summary Cards ── */
.summary-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}
.summary-card {
    background: var(--white); border-radius: var(--radius);
    padding: 18px 20px; box-shadow: var(--shadow);
    display: flex; align-items: center; gap: 14px;
}
.sum-icon {
    width: 46px; height: 46px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.sum-icon.blue   { background: #dbeafe; color: #2563eb; }
.sum-icon.green  { background: #dcfce7; color: #16a34a; }
.sum-icon.yellow { background: #fef9c3; color: #ca8a04; }
.sum-info { flex: 1; min-width: 0; }
.sum-value { font-size: 20px; font-weight: 800; color: var(--text); line-height: 1.2; }
.sum-label { font-size: 12px; color: var(--muted); margin-top: 2px; }

/* ── Filter Card ── */
.filter-card {
    background: var(--white); border-radius: var(--radius);
    padding: 16px 20px; box-shadow: var(--shadow);
    margin-bottom: 20px;
    display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
}
.filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 200px; }
.filter-label { font-size: 11.5px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .3px; }
.filter-input {
    height: 38px; border: 1.5px solid var(--border);
    border-radius: var(--radius-sm); padding: 0 12px;
    font-size: 13px; color: var(--text); background: var(--white);
    outline: none; transition: border-color .15s; width: 100%;
    font-family: inherit;
}
.filter-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(231,86,124,.08); }
.filter-actions { display: flex; gap: 8px; align-items: flex-end; }
.btn-filter {
    height: 38px; padding: 0 20px; background: var(--accent);
    color: var(--white); border: none; border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px;
    font-family: inherit; transition: background .15s; white-space: nowrap;
}
.btn-filter:hover { background: var(--accent-dk); }
.btn-reset {
    height: 38px; padding: 0 16px;
    background: #f1f5f9; color: var(--muted);
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; cursor: pointer;
    text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    font-family: inherit; transition: background .15s; white-space: nowrap;
}
.btn-reset:hover { background: #e2e8f0; color: var(--text); text-decoration: none; }

/* ── Table Card ── */
.table-card {
    background: var(--white); border-radius: var(--radius);
    box-shadow: var(--shadow); overflow: hidden;
}
.table-card-top {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid var(--border);
    flex-wrap: wrap; gap: 10px;
}
.table-card-title { font-size: 15px; font-weight: 700; color: var(--text); margin: 0; }
.count-badge {
    background: #f3f4f6; color: var(--muted);
    border-radius: 20px; padding: 3px 10px;
    font-size: 12px; font-weight: 600;
}

.sc-table { width: 100%; border-collapse: collapse; }
.sc-table thead tr { background: #f8fafc; }
.sc-table thead th {
    padding: 11px 16px; text-align: left;
    font-size: 11.5px; font-weight: 700; color: var(--muted);
    white-space: nowrap; text-transform: uppercase; letter-spacing: .4px;
    border-bottom: 2px solid var(--border);
}
.sc-table tbody tr { border-bottom: 1px solid #f0f2f5; transition: background .12s; }
.sc-table tbody tr:last-child { border-bottom: none; }
.sc-table tbody tr:hover { background: #fafbff; }
.sc-table tbody td { padding: 13px 16px; font-size: 13px; color: var(--text); vertical-align: middle; }

/* Area name */
.area-name { font-weight: 700; color: var(--text); font-size: 14px; }

/* Charge badge */
.charge-badge {
    display: inline-flex; align-items: center; gap: 4px;
    background: #dbeafe; color: #1d4ed8;
    padding: 4px 12px; border-radius: 20px;
    font-size: 13px; font-weight: 700;
}

/* Status toggle */
.status-toggle-form { display: inline; }
.status-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    border: none; font-family: inherit; transition: all .15s;
}
.status-btn.active  { background: #dcfce7; color: #15803d; }
.status-btn.active:hover  { background: #bbf7d0; }
.status-btn.inactive { background: #f3f4f6; color: #6b7280; }
.status-btn.inactive:hover { background: #e5e7eb; }
.status-dot {
    width: 7px; height: 7px; border-radius: 50%;
    display: inline-block; flex-shrink: 0;
}
.status-dot.active   { background: #15803d; }
.status-dot.inactive { background: #6b7280; }

/* Action buttons */
.action-cell { display: flex; align-items: center; gap: 8px; }
.btn-edit {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px; background: #dbeafe; color: #2563eb;
    border-radius: 6px; font-size: 12px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer;
    transition: background .15s; white-space: nowrap;
}
.btn-edit:hover { background: #bfdbfe; color: #1d4ed8; text-decoration: none; }
.btn-delete {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 12px; background: #fee2e2; color: #dc2626;
    border-radius: 6px; font-size: 12px; font-weight: 600;
    border: none; cursor: pointer; transition: background .15s;
    white-space: nowrap; font-family: inherit;
}
.btn-delete:hover { background: #fecaca; }

/* Empty state */
.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { font-size: 52px; color: #d1d5db; display: block; margin-bottom: 14px; }
.empty-state h4 { font-size: 16px; color: #374151; font-weight: 700; margin-bottom: 6px; }
.empty-state p  { font-size: 13.5px; color: var(--muted); margin: 0; }

/* Pagination */
.pagi-area {
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 14px 20px; border-top: 1px solid var(--border);
    flex-wrap: wrap; gap: 10px;
}
.pagi-info { font-size: 13px; color: var(--muted); }

/* Alerts */
.alert-ok {
    background: #ecfdf5; border: 1px solid #6ee7b7;
    color: #065f46; padding: 12px 16px; border-radius: 8px;
    margin-bottom: 16px; font-size: 14px; font-weight: 500;
}
.alert-err {
    background: #fff1f2; border: 1px solid #fecdd3;
    color: #be123c; padding: 12px 16px; border-radius: 8px;
    margin-bottom: 16px; font-size: 14px; font-weight: 500;
}

/* Delete Confirm Modal */
.confirm-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,.5);
    z-index: 50000; display: flex; align-items: center;
    justify-content: center; opacity: 0; pointer-events: none;
    transition: opacity .2s; padding: 16px;
}
.confirm-overlay.show { opacity: 1; pointer-events: all; }
.confirm-modal {
    background: var(--white); border-radius: 14px;
    width: 420px; max-width: 100%;
    box-shadow: 0 24px 64px rgba(0,0,0,.22);
    transform: scale(.96) translateY(8px); transition: transform .2s;
    overflow: hidden;
}
.confirm-overlay.show .confirm-modal { transform: scale(1) translateY(0); }
.confirm-head {
    padding: 20px 22px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 12px;
}
.confirm-icon {
    width: 44px; height: 44px; border-radius: 50%;
    background: #fee2e2; display: flex; align-items: center;
    justify-content: center; font-size: 20px; color: #dc2626; flex-shrink: 0;
}
.confirm-head h5 { font-size: 16px; font-weight: 700; color: var(--text); margin: 0; }
.confirm-body { padding: 20px 22px; }
.confirm-body p { font-size: 14px; color: var(--muted); margin: 0; line-height: 1.6; }
.confirm-body strong { color: var(--text); }
.confirm-foot {
    padding: 14px 22px; border-top: 1px solid var(--border);
    display: flex; justify-content: flex-end; gap: 10px;
}
.btn-cancel-modal {
    height: 40px; padding: 0 20px;
    background: #f1f5f9; border: 1px solid var(--border);
    border-radius: var(--radius-sm); font-size: 13px;
    cursor: pointer; color: var(--muted); font-family: inherit;
    transition: background .15s;
}
.btn-cancel-modal:hover { background: #e2e8f0; }
.btn-confirm-delete {
    height: 40px; padding: 0 20px;
    background: #dc2626; color: #fff; border: none;
    border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;
    cursor: pointer; font-family: inherit;
    display: inline-flex; align-items: center; gap: 6px;
    transition: background .15s;
}
.btn-confirm-delete:hover { background: #b91c1c; }

/* Toast */
.toast-container {
    position: fixed; bottom: 24px; right: 24px; z-index: 99999;
    display: flex; flex-direction: column; gap: 8px;
}
.pos-toast {
    background: #1e293b; color: #fff; border-radius: var(--radius);
    padding: 12px 18px; font-size: 13px; font-weight: 500;
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
    display: flex; align-items: center; gap: 10px; min-width: 260px;
    animation: tIn .3s ease;
}
.pos-toast.t-success { background: #15803d; }
.pos-toast.t-error   { background: #be123c; }
@keyframes tIn  { from{opacity:0;transform:translateX(40px);}to{opacity:1;transform:translateX(0);} }
@keyframes tOut { from{opacity:1;}to{opacity:0;transform:translateX(40px);} }

.sc-table::-webkit-scrollbar { width: 4px; }
.sc-table::-webkit-scrollbar-thumb { background: #dde2e8; border-radius: 4px; }

@media (max-width: 900px) { .summary-row { grid-template-columns: 1fr 1fr; } }
@media (max-width: 560px) {
    .summary-row { grid-template-columns: 1fr; }
    .table-card { overflow-x: auto; }
}
</style>

<div class="sc-page">

    {{-- ── Page Header ── --}}
    <div class="sc-page-header">
        <h2 class="sc-page-title">
            <i class="bi bi-truck" style="color:var(--accent);margin-right:6px;"></i>
            Shipping Charge
        </h2>
        <a href="{{ route('admin.shippingcharge.create') }}" class="btn-add-new">
            <i class="bi bi-plus-circle"></i> Add New
        </a>
    </div>

    @if(session('success'))
        <div class="alert-ok">
            <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-err">
            <i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Summary Cards ── --}}
    @php
        $totalCharges  = \App\Models\ShippingCharge::count();
        $activeCharges = \App\Models\ShippingCharge::where('status', 1)->count();
        $avgCharge     = \App\Models\ShippingCharge::avg('charge') ?? 0;
    @endphp
    <div class="summary-row">
        <div class="summary-card">
            <div class="sum-icon blue"><i class="bi bi-truck"></i></div>
            <div class="sum-info">
                <div class="sum-value">{{ number_format($totalCharges) }}</div>
                <div class="sum-label">Total Areas</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="sum-icon green"><i class="bi bi-check-circle"></i></div>
            <div class="sum-info">
                <div class="sum-value">{{ number_format($activeCharges) }}</div>
                <div class="sum-label">Active Areas</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="sum-icon yellow"><i class="bi bi-cash"></i></div>
            <div class="sum-info">
                <div class="sum-value">{{ $cur }}{{ number_format($avgCharge, 2) }}</div>
                <div class="sum-label">Average Charge</div>
            </div>
        </div>
    </div>

    {{-- ── Filter ── --}}
    <form method="GET" action="{{ route('admin.shippingcharge.index') }}">
        <div class="filter-card">
            <div class="filter-group">
                <label class="filter-label">Search Area</label>
                <input type="text" name="search" class="filter-input"
                       placeholder="Search by area name..."
                       value="{{ request('search') }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="{{ route('admin.shippingcharge.index') }}" class="btn-reset">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>

    {{-- ── Table ── --}}
    <div class="table-card">
        <div class="table-card-top">
            <div style="display:flex;align-items:center;gap:10px;">
                <h3 class="table-card-title">Shipping Areas</h3>
                <span class="count-badge">{{ $shippingCharges->total() }} records</span>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="sc-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Area Name</th>
                        <th>Charge</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shippingCharges as $index => $sc)
                        <tr>
                            <td style="color:var(--muted);font-size:12px;font-weight:600;">
                                {{ $shippingCharges->firstItem() + $index }}
                            </td>

                            <td>
                                <span class="area-name">
                                    <i class="bi bi-geo-alt-fill" style="color:var(--accent);margin-right:4px;"></i>
                                    {{ $sc->area_name }}
                                </span>
                            </td>

                            <td>
                                <span class="charge-badge">
                                    <i class="bi bi-currency-dollar" style="font-size:12px;"></i>
                                    {{ $cur }}{{ number_format($sc->charge, 2) }}
                                </span>
                            </td>

                            <td>
                                <form class="status-toggle-form"
                                      action="{{ route('admin.shippingcharge.toggle-status', $sc->id) }}"
                                      method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="status-btn {{ $sc->status ? 'active' : 'inactive' }}">
                                        <span class="status-dot {{ $sc->status ? 'active' : 'inactive' }}"></span>
                                        {{ $sc->status ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>

                            <td style="color:var(--muted);font-size:12px;">
                                {{ $sc->created_at?->format('d M Y') }}<br>
                                <span style="font-size:11px;">{{ $sc->created_at?->diffForHumans() }}</span>
                            </td>

                            <td>
                                <div class="action-cell">
                                    <a href="{{ route('admin.shippingcharge.edit', $sc->id) }}"
                                       class="btn-edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button class="btn-delete"
                                            onclick="confirmDelete({{ $sc->id }}, '{{ addslashes($sc->area_name) }}', '{{ route('admin.shippingcharge.destroy', $sc->id) }}')"
                                            title="Delete">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <span class="empty-icon bi bi-truck"></span>
                                    <h4>No Shipping Areas Found</h4>
                                    <p>No shipping charge records match your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($shippingCharges->hasPages())
            <div class="pagi-area">
                <div class="pagi-info">
                    Showing <strong>{{ $shippingCharges->firstItem() }}</strong>
                    to <strong>{{ $shippingCharges->lastItem() }}</strong>
                    of <strong>{{ $shippingCharges->total() }}</strong> results
                </div>
                <div>{{ $shippingCharges->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>

</div>

{{-- ══ DELETE CONFIRM MODAL ══ --}}
<div class="confirm-overlay" id="deleteOverlay">
    <div class="confirm-modal">
        <div class="confirm-head">
            <div class="confirm-icon"><i class="bi bi-trash"></i></div>
            <h5>Delete Shipping Area?</h5>
        </div>
        <div class="confirm-body">
            <p>
                Are you sure you want to delete
                <strong id="deleteAreaName">this area</strong>?
                <br><br>
                <span style="color:#dc2626;font-weight:600;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    This action cannot be undone.
                </span>
            </p>
        </div>
        <div class="confirm-foot">
            <button class="btn-cancel-modal" onclick="closeDeleteModal()">
                <i class="bi bi-x"></i> Cancel
            </button>
            <form id="deleteForm" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">
                    <i class="bi bi-trash"></i> Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
'use strict';

function confirmDelete(id, areaName, deleteUrl) {
    document.getElementById('deleteAreaName').textContent = areaName;
    document.getElementById('deleteForm').action = deleteUrl;
    document.getElementById('deleteOverlay').classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteOverlay').classList.remove('show');
}

document.getElementById('deleteOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});

function showToast(msg, type, ms) {
    type = type || 'success'; ms = ms || 3200;
    var icons = { success: 'check-circle-fill', error: 'x-circle-fill' };
    var c = document.getElementById('toastContainer');
    var t = document.createElement('div');
    t.className = 'pos-toast t-' + type;
    t.innerHTML = '<i class="bi bi-' + (icons[type] || icons.success) + '"></i><span>' + msg + '</span>';
    c.appendChild(t);
    setTimeout(function() {
        t.style.animation = 'tOut .3s ease forwards';
        t.addEventListener('animationend', function() { t.remove(); }, { once: true });
    }, ms);
}

@if(session('success'))
    showToast('{{ addslashes(session("success")) }}', 'success');
@endif
@if(session('error'))
    showToast('{{ addslashes(session("error")) }}', 'error');
@endif
</script>

@endsection
