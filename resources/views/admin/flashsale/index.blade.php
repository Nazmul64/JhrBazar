{{-- resources/views/admin/flashsale/index.blade.php --}}
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
    /* Edit — teal */
    .btn-act-edit  { border-color:rgba(20,184,166,.3); color:#14b8a6; }
    .btn-act-edit:hover  { background:#14b8a6; border-color:#14b8a6; color:#fff; }
    /* Delete — red */
    .btn-act-del   { border-color:rgba(239,68,68,.3); color:#ef4444; }
    .btn-act-del:hover   { background:#ef4444; border-color:#ef4444; color:#fff; }

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
</style>

{{-- PAGE HEADER --}}
<div class="ph">
    <h4 class="ph-title">Flash Sales</h4>
    <a href="{{ route('admin.flashsale.create') }}" class="btn-add">
        <i class="bi bi-plus-circle"></i> Add Flash Sale
    </a>
</div>

@if(session('success'))
    <div class="alert-ok">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
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
                        <form action="{{ route('admin.flashsale.toggle', $fs->id) }}" method="POST">
                            @csrf
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       {{ $fs->is_active ? 'checked' : '' }}
                                       onchange="this.closest('form').submit()">
                            </div>
                        </form>
                    </td>
                    <td>
                        <div class="desc-cell">{{ $fs->description }}</div>
                    </td>
                    <td>
                        <div class="action-wrap">
                            <a href="{{ route('admin.flashsale.show', $fs->id) }}"
                               class="btn-act btn-act-view" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.flashsale.edit', $fs->id) }}"
                               class="btn-act btn-act-edit" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button type="button" class="btn-act btn-act-del" title="Delete"
                                    onclick="openDel({{ $fs->id }}, '{{ addslashes($fs->name) }}')">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="8">
                        <i class="bi bi-lightning-charge" style="font-size:36px;color:#d1d5db;display:block;margin-bottom:8px;"></i>
                        No flash sales found. Click "Add Flash Sale" to create one.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- DELETE MODAL --}}
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

<script>
function openDel(id, name) {
    var base = "{{ route('admin.flashsale.destroy', '__ID__') }}";
    document.getElementById('delForm').action = base.replace('__ID__', id);
    document.getElementById('del-name').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
