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
    .ph { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
    .ph-title { font-size:1.4rem; font-weight:700; color:var(--dark); margin:0; }
    .btn-add {
        display:inline-flex; align-items:center; gap:7px;
        background:var(--brand); color:#fff; border:none;
        border-radius:var(--r-md); padding:10px 22px;
        font-size:13.5px; font-weight:600; cursor:pointer;
        box-shadow:0 2px 10px rgba(232,23,74,.3);
        text-decoration:none; transition:var(--ease);
    }
    .btn-add:hover { background:var(--brand-hover); color:#fff; transform:translateY(-1px); }

    /* ── Alert ── */
    .alert-ok {
        background:#f0fdf4; color:#15803d;
        border-left:3.5px solid #22c55e;
        border-radius:var(--r-md); padding:12px 16px;
        font-size:13.5px; margin-bottom:1.2rem;
        display:flex; align-items:center; gap:9px;
    }

    /* ── Table Card ── */
    .data-card {
        background:#fff; border-radius:var(--r-lg);
        box-shadow:var(--shadow); border:1px solid var(--border);
        overflow:hidden;
    }
    .fs-table { width:100%; border-collapse:collapse; }
    .fs-table th {
        padding:13px 18px;
        font-size:12px; font-weight:700;
        text-transform:uppercase; letter-spacing:.4px;
        color:var(--muted); background:var(--surface);
        border-bottom:1px solid var(--border);
        white-space:nowrap;
    }
    .fs-table td {
        padding:14px 18px; font-size:13px; color:var(--dark);
        border-bottom:1px solid var(--border); vertical-align:middle;
    }
    .fs-table tbody tr:last-child td { border-bottom:none; }
    .fs-table tbody tr:hover { background:#fafbff; }

    .date-cell { font-size:12.5px; font-family:monospace; color:var(--dark); white-space:nowrap; }

    /* ── Action buttons ── */
    .action-wrap { display:flex; align-items:center; gap:7px; justify-content:flex-end; }
    .btn-act {
        width:32px; height:32px;
        display:inline-flex; align-items:center; justify-content:center;
        border-radius:var(--r-sm); border:1.5px solid;
        background:transparent; cursor:pointer;
        font-size:14px; text-decoration:none;
        transition:var(--ease);
    }
    .btn-act-edit  { border-color:rgba(20,184,166,.3); color:#14b8a6; }
    .btn-act-edit:hover  { background:#14b8a6; border-color:#14b8a6; color:#fff; }
    .btn-act-del   { border-color:rgba(239,68,68,.3); color:#ef4444; }
    .btn-act-del:hover   { background:#ef4444; border-color:#ef4444; color:#fff; }

    /* ── Status toggle ── */
    .form-check-input {
        width:42px !important; height:22px !important;
        cursor:pointer; border-radius:11px !important;
    }
    .form-check-input:checked {
        background-color:var(--brand) !important;
        border-color:var(--brand) !important;
    }
    .form-check-input:not(:checked) {
        background-color:#d1d5db !important;
        border-color:#d1d5db !important;
    }

    /* ── Empty state ── */
    .empty-row td { text-align:center; padding:50px 20px; color:var(--muted); font-size:13px; }
</style>

<div class="container-fluid px-4 py-4">
    <div class="ph">
        <h4 class="ph-title">Promo Codes</h4>
        <a href="{{ route('seller.promocode.create') }}" class="btn-add">
            <i class="bi bi-plus-circle"></i> Add Promo Code
        </a>
    </div>

    @if(session('success'))
        <div class="alert-ok">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="data-card">
        <div class="table-responsive">
            <table class="fs-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Min Amount</th>
                        <th>Started At</th>
                        <th>Expired At</th>
                        <th>Status</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                    <tr>
                        <td style="font-weight:600;">{{ $voucher->voucher_code }}</td>
                        <td style="font-weight:700; color:var(--brand);">{{ $voucher->formatted_discount }}</td>
                        <td>${{ number_format($voucher->minimum_order_amount, 2) }}</td>
                        <td class="date-cell">{{ $voucher->started_at }}</td>
                        <td class="date-cell">{{ $voucher->expired_at }}</td>
                        <td>
                            <form action="{{ route('seller.promocode.toggle', $voucher->id) }}" method="POST">
                                @csrf
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           {{ $voucher->status ? 'checked' : '' }}
                                           onchange="this.closest('form').submit()">
                                </div>
                            </form>
                        </td>
                        <td>
                            <div class="action-wrap">
                                <a href="{{ route('seller.promocode.edit', $voucher->id) }}" class="btn-act btn-act-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('seller.promocode.destroy', $voucher->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this promo code?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-act btn-act-del" title="Delete">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="7">
                            <i class="bi bi-tags" style="font-size:36px;color:#d1d5db;display:block;margin-bottom:8px;"></i>
                            No promo codes found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
