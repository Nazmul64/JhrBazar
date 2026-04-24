@extends('admin.master')

@section('content')

@php
    $cur = $settings->default_currency ?? '৳';
@endphp

<style>
/* ════════════════════════════════════════════════════════════
   POS Draft Orders — Index
   (Same design system as salesistory.blade.php)
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

.draft-page {
    padding: 24px;
    background: var(--bg);
    min-height: 100vh;
    font-family: 'Segoe UI', system-ui, sans-serif;
}

/* ── Page Header ── */
.draft-page-header {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 22px; flex-wrap: wrap; gap: 12px;
}
.draft-page-title { font-size: 20px; font-weight: 800; color: var(--text); margin: 0; }

.btn-back-pos {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; background: var(--white);
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; color: var(--muted);
    text-decoration: none; transition: all .15s;
}
.btn-back-pos:hover { background: #f1f5f9; color: var(--text); text-decoration: none; }

/* ── Summary Cards ── */
.summary-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
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
.sum-icon.orange { background: #ffedd5; color: #ea580c; }
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
.filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px; }
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
    background: #fef9c3; color: #a16207;
    border-radius: 20px; padding: 3px 10px;
    font-size: 12px; font-weight: 600;
}

.draft-table { width: 100%; border-collapse: collapse; }
.draft-table thead tr { background: #f8fafc; }
.draft-table thead th {
    padding: 11px 16px; text-align: left;
    font-size: 11.5px; font-weight: 700; color: var(--muted);
    white-space: nowrap; text-transform: uppercase; letter-spacing: .4px;
    border-bottom: 2px solid var(--border);
}
.draft-table tbody tr { border-bottom: 1px solid #f0f2f5; transition: background .12s; }
.draft-table tbody tr:last-child { border-bottom: none; }
.draft-table tbody tr:hover { background: #fffdf0; }
.draft-table tbody td { padding: 13px 16px; font-size: 13px; color: var(--text); vertical-align: middle; }

/* Customer cell */
.cust-cell { display: flex; flex-direction: column; gap: 2px; }
.cust-name  { font-weight: 600; color: var(--text); font-size: 13px; }
.cust-phone { font-size: 11.5px; color: var(--muted); }

/* Items badge */
.items-cell {
    background: #fef9c3; color: #a16207;
    padding: 3px 10px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 4px;
}

/* Amount */
.amt-bold { font-weight: 800; font-size: 14px; color: var(--text); }

/* Date cell */
.date-main { font-size: 13px; color: var(--text); font-weight: 500; }
.date-ago  { font-size: 11px; color: #aab; margin-top: 2px; }

/* Action buttons */
.action-cell { display: flex; align-items: center; gap: 8px; }
.btn-edit {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px; background: #dbeafe; color: #2563eb;
    border-radius: 6px; font-size: 12px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer;
    transition: background .15s; white-space: nowrap; font-family: inherit;
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

/* ── Delete Confirm Modal ── */
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

/* Scrollbar */
.table-card::-webkit-scrollbar { width: 4px; }
.table-card::-webkit-scrollbar-thumb { background: #dde2e8; border-radius: 4px; }

@media (max-width: 900px) { .summary-row { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 560px) {
    .summary-row { grid-template-columns: 1fr; }
    .filter-group { min-width: 100%; }
    .table-card { overflow-x: auto; }
}
</style>

<div class="draft-page">

    {{-- ── Page Header ── --}}
    <div class="draft-page-header">
        <h2 class="draft-page-title">
            <i class="bi bi-pencil-square" style="color:var(--warning);margin-right:6px;"></i>
            POS Draft Orders
        </h2>
        <a href="{{ route('admin.pointofsalepos.index') }}" class="btn-back-pos">
            <i class="bi bi-display"></i> Back to POS
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
    <div class="summary-row">
        <div class="summary-card">
            <div class="sum-icon yellow">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="sum-info">
                <div class="sum-value">{{ number_format($totalDrafts ?? 0) }}</div>
                <div class="sum-label">Total Drafts</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="sum-icon orange">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="sum-info">
                <div class="sum-value">{{ $cur }}{{ number_format($totalDraftValue ?? 0, 2) }}</div>
                <div class="sum-label">Total Draft Value</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="sum-icon blue">
                <i class="bi bi-calendar-day"></i>
            </div>
            <div class="sum-info">
                <div class="sum-value">{{ number_format($todayDrafts ?? 0) }}</div>
                <div class="sum-label">Today's Drafts</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="sum-icon green">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="sum-info">
                <div class="sum-value">{{ number_format($totalItems ?? 0) }}</div>
                <div class="sum-label">Total Items (Draft)</div>
            </div>
        </div>
    </div>

    {{-- ── Filter ── --}}
    <form method="GET" action="{{ route('admin.pointofsalepos.draft.index') }}">
        <div class="filter-card">
            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" name="search" class="filter-input"
                       placeholder="Customer name or phone..."
                       value="{{ request('search') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">From Date</label>
                <input type="date" name="date_from" class="filter-input"
                       value="{{ request('date_from') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">To Date</label>
                <input type="date" name="date_to" class="filter-input"
                       value="{{ request('date_to') }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('admin.pointofsalepos.draft.index') }}" class="btn-reset">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>

    {{-- ── Table ── --}}
    <div class="table-card">
        <div class="table-card-top">
            <div style="display:flex;align-items:center;gap:10px;">
                <h3 class="table-card-title">Draft Items</h3>
                <span class="count-badge">{{ $drafts->total() }} records</span>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="draft-table">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Created Date</th>
                        <th>Customer</th>
                        <th>Total Products</th>
                        <th>Sub Total</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drafts as $index => $draft)
                        @php
                            $customer  = $draft->customer;
                            $custUser  = $customer?->user;
                            $custName  = $customer
                                ? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''))
                                : ($custUser?->name ?? '');
                            if (!$custName) $custName = $custUser?->name ?? '';
                            $custPhone = $custUser?->phone ?? '';
                            $itemCount = count($draft->items ?? []);
                        @endphp
                        <tr>
                            {{-- SL --}}
                            <td style="color:var(--muted);font-size:12px;font-weight:600;">
                                {{ $drafts->firstItem() + $index }}
                            </td>

                            {{-- Created Date --}}
                            <td>
                                <div class="date-main">{{ $draft->created_at?->format('d M Y, h:i A') }}</div>
                                <div class="date-ago">{{ $draft->created_at?->diffForHumans() }}</div>
                            </td>

                            {{-- Customer --}}
                            <td>
                                <div class="cust-cell">
                                    <span class="cust-name">{{ $custName ?: 'N/A' }}</span>
                                    @if($custPhone)
                                        <span class="cust-phone">{{ $custPhone }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Total Products --}}
                            <td>
                                <span class="items-cell">
                                    <i class="bi bi-box-seam" style="font-size:11px;"></i>
                                    {{ $itemCount }} Item{{ $itemCount != 1 ? 's' : '' }}
                                </span>
                            </td>

                            {{-- Sub Total --}}
                            <td style="font-weight:600;">
                                {{ $cur }}{{ number_format($draft->sub_total ?? 0, 2) }}
                            </td>

                            {{-- Discount --}}
                            <td>
                                @if(($draft->discount ?? 0) > 0)
                                    <span style="color:var(--green-dk);font-weight:600;">
                                        −{{ $cur }}{{ number_format($draft->discount, 2) }}
                                    </span>
                                @else
                                    <span style="color:var(--muted);">{{ $cur }}0</span>
                                @endif
                            </td>

                            {{-- Grand Total --}}
                            <td>
                                <span class="amt-bold">
                                    {{ $cur }}{{ number_format($draft->grand_total ?? 0, 2) }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="action-cell">
                                    {{-- ✅ Edit: load draft into POS via AJAX getDraft route --}}
                                    <a href="{{ route('admin.pointofsalepos.index') }}?draft_id={{ $draft->id }}"
                                       class="btn-edit" title="Edit in POS">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    {{-- ✅ Delete: opens confirm modal --}}
                                    <button class="btn-delete"
                                            onclick="confirmDelete({{ $draft->id }}, {{ $itemCount }}, '{{ route('admin.pointofsalepos.draft.destroy', $draft->id) }}')"
                                            title="Delete Draft">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <span class="empty-icon bi bi-pencil-square"></span>
                                    <h4>No Draft Orders Found</h4>
                                    <p>No saved draft orders match your filter criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($drafts->hasPages())
            <div class="pagi-area">
                <div class="pagi-info">
                    Showing <strong>{{ $drafts->firstItem() }}</strong>
                    to <strong>{{ $drafts->lastItem() }}</strong>
                    of <strong>{{ $drafts->total() }}</strong> results
                </div>
                <div>
                    {{ $drafts->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

{{-- ══ DELETE CONFIRM MODAL ══ --}}
<div class="confirm-overlay" id="deleteOverlay">
    <div class="confirm-modal">
        <div class="confirm-head">
            <div class="confirm-icon">
                <i class="bi bi-trash"></i>
            </div>
            <h5>Delete Draft Order?</h5>
        </div>
        <div class="confirm-body">
            <p>
                Are you sure you want to delete this draft order with
                <strong id="deleteItemCount">0</strong> item(s)?
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
            {{-- ✅ FIXED: action set dynamically via JS using correct named route --}}
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

/* ── Delete Modal ── */
function confirmDelete(draftId, itemCount, deleteUrl) {
    document.getElementById('deleteItemCount').textContent = itemCount;
    // ✅ FIXED: URL আসে Blade route() থেকে, JS-এ hardcode না করে
    document.getElementById('deleteForm').action = deleteUrl;
    document.getElementById('deleteOverlay').classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteOverlay').classList.remove('show');
}

/* Close on overlay background click */
document.getElementById('deleteOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

/* Close on Escape key */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});

/* ── Toast ── */
function showToast(msg, type, ms) {
    type = type || 'success';
    ms   = ms   || 3200;
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
