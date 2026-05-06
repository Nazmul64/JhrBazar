{{-- resources/views/seller/flashsales/index.blade.php --}}
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

    /* ── Thumbnail ── */
    .fs-thumb {
        width:72px; height:48px; object-fit:cover;
        border-radius:var(--r-sm); border:1px solid var(--border);
        display:block;
    }
    .fs-thumb-ph {
        width:72px; height:48px; background:var(--surface);
        border-radius:var(--r-sm); display:flex;
        align-items:center; justify-content:center;
        color:#d1d5db; font-size:20px;
        border:1px solid var(--border);
    }

    /* ── Date cell ── */
    .date-cell { font-size:12.5px; font-family:monospace; color:var(--dark); white-space:nowrap; }

    /* ── Description cell ── */
    .desc-cell {
        font-size:12.5px; color:var(--muted);
        max-width:260px; line-height:1.5;
        display:-webkit-box; -webkit-line-clamp:3;
        -webkit-box-orient:vertical; overflow:hidden;
    }

    /* ── Status badge ── */
    .status-active {
        display:inline-flex; align-items:center; gap:5px;
        background:#dcfce7; color:#16a34a; border:1.5px solid #bbf7d0;
        border-radius:20px; padding:3px 10px; font-size:11.5px; font-weight:700;
    }
    .status-inactive {
        display:inline-flex; align-items:center; gap:5px;
        background:#f3f4f6; color:var(--muted); border:1.5px solid var(--border);
        border-radius:20px; padding:3px 10px; font-size:11.5px; font-weight:700;
    }

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
    /* View — pink */
    .btn-act-view  { border-color:rgba(232,23,74,.3); color:var(--brand); }
    .btn-act-view:hover  { background:var(--brand); border-color:var(--brand); color:#fff; }

    /* ── Empty state ── */
    .empty-row td { text-align:center; padding:50px 20px; color:var(--muted); font-size:13px; }
</style>

{{-- PAGE HEADER --}}
<div class="ph">
    <h4 class="ph-title">Flash Sales</h4>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- TABLE CARD --}}
<div class="data-card">
    <div class="table-responsive">
        <table class="fs-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Thumbnail</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($flashsales as $i => $fs)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($fs->thumbnail)
                            <img src="{{ asset($fs->thumbnail) }}"
                                 class="fs-thumb" alt="{{ $fs->name }}"
                                 onerror="this.style.display='none'">
                        @else
                            <div class="fs-thumb-ph"><i class="bi bi-image"></i></div>
                        @endif
                    </td>
                    <td style="font-weight:600; min-width:140px;">{{ $fs->name }}</td>
                    <td class="date-cell">{{ $fs->start_date_time_display }}</td>
                    <td class="date-cell">{{ $fs->end_date_time_display }}</td>
                    <td>
                        @if($fs->is_active)
                            <span class="status-active">
                                <span style="width:6px;height:6px;background:#16a34a;border-radius:50%;"></span>
                                Active
                            </span>
                        @else
                            <span class="status-inactive">
                                <span style="width:6px;height:6px;background:#9ca3af;border-radius:50%;"></span>
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="desc-cell">{{ $fs->description }}</div>
                    </td>
                    <td>
                        <div class="action-wrap">
                            <a href="{{ route('seller.flashsales.show', $fs->id) }}"
                               class="btn-act btn-act-view" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="8">
                        <i class="bi bi-lightning-charge" style="font-size:36px;color:#d1d5db;display:block;margin-bottom:8px;"></i>
                        No flash sales found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
