@extends('admin.master')
@section('content')
<style>
    :root {
        --brand-primary: #e8174a;
        --brand-primary-light: rgba(232, 23, 74, 0.08);
        --brand-primary-hover: #c9113e;
        --text-dark: #1a1d23;
        --text-muted: #6b7280;
        --border-color: #e5e7eb;
        --card-bg: #ffffff;
        --surface-bg: #f8f9fc;
        --shadow-sm: 0 1px 4px rgba(0,0,0,0.06), 0 2px 12px rgba(0,0,0,0.04);
        --radius-lg: 14px;
        --radius-md: 10px;
        --radius-sm: 7px;
        --transition: all 0.18s ease;
    }

    body { background: #f3f4f8; }

    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.4rem; padding:0 2px; }
    .page-title  { font-size:1.25rem; font-weight:700; color:var(--text-dark); margin:0; }

    .btn-add {
        display:inline-flex; align-items:center; gap:7px;
        background:var(--brand-primary); color:#fff; border:none;
        border-radius:var(--radius-md); padding:10px 22px;
        font-size:14px; font-weight:600; cursor:pointer;
        transition:var(--transition);
        box-shadow:0 2px 10px rgba(232,23,74,0.25);
    }
    .btn-add:hover { background:var(--brand-primary-hover); transform:translateY(-1px); }

    .alert-custom { border-radius:var(--radius-md); border:none; font-size:13.5px; padding:12px 16px; display:flex; align-items:center; gap:9px; margin-bottom:1.2rem; }
    .alert-success-custom { background:#f0fdf4; color:#15803d; border-left:3.5px solid #22c55e; }
    .alert-danger-custom  { background:#fff1f3; color:#be123c; border-left:3.5px solid var(--brand-primary); }
    .alert-custom .btn-close { margin-left:auto; opacity:.5; font-size:11px; }

    .data-card { background:var(--card-bg); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); border:1px solid var(--border-color); overflow:hidden; }

    .size-table { width:100%; border-collapse:collapse; }
    .size-table thead tr { background:var(--surface-bg); }
    .size-table th { padding:12px 24px; font-size:13px; font-weight:600; color:var(--text-muted); border-bottom:1px solid var(--border-color); white-space:nowrap; }
    .size-table td { padding:14px 24px; font-size:14px; color:var(--text-dark); border-bottom:1px solid var(--border-color); vertical-align:middle; }
    .size-table tbody tr:last-child td { border-bottom:none; }
    .size-table tbody tr:hover { background:#fafbff; }

    .form-check-input { width:42px !important; height:24px !important; cursor:pointer; border-radius:12px !important; }
    .form-check-input:checked     { background-color:var(--brand-primary) !important; border-color:var(--brand-primary) !important; }
    .form-check-input:focus       { box-shadow:0 0 0 .18rem rgba(232,23,74,.2) !important; }
    .form-check-input:not(:checked) { background-color:#d1d5db !important; border-color:#d1d5db !important; }

    .action-wrap { display:flex; align-items:center; justify-content:flex-end; gap:6px; }
    .btn-icon { display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:var(--radius-sm); border:1.5px solid; background:transparent; cursor:pointer; font-size:14px; transition:var(--transition); }
    .btn-icon-edit   { border-color:rgba(232,23,74,.3); color:var(--brand-primary); }
    .btn-icon-edit:hover   { background:var(--brand-primary); border-color:var(--brand-primary); color:#fff; transform:translateY(-1px); box-shadow:0 3px 10px rgba(232,23,74,.25); }
    .btn-icon-delete { border-color:rgba(107,114,128,.3); color:#6b7280; }
    .btn-icon-delete:hover { background:#ef4444; border-color:#ef4444; color:#fff; transform:translateY(-1px); box-shadow:0 3px 10px rgba(239,68,68,.25); }

    .empty-state { padding:60px 20px; text-align:center; }
    .empty-title { font-size:14px; font-weight:600; color:var(--text-muted); margin:0 0 6px; }
    .empty-sub   { font-size:12.5px; color:#9ca3af; margin:0; }

    /* Modal */
    .modal-content { border-radius:var(--radius-lg) !important; border:none !important; box-shadow:0 20px 60px rgba(0,0,0,0.15) !important; }
    .modal-header  { padding:18px 24px !important; border-bottom:1px solid var(--border-color) !important; background:#fff; border-radius:var(--radius-lg) var(--radius-lg) 0 0 !important; }
    .modal-title   { font-size:15px; font-weight:700; color:var(--text-dark); }
    .modal-body    { padding:24px !important; }
    .modal-footer  { padding:14px 24px !important; border-top:1px solid var(--border-color) !important; background:var(--surface-bg); border-radius:0 0 var(--radius-lg) var(--radius-lg) !important; }

    .form-label-custom { font-size:13px; font-weight:600; color:var(--text-dark); margin-bottom:7px; display:block; }
    .form-label-custom span { color:var(--brand-primary); }
    .form-control-custom {
        border-radius:var(--radius-sm) !important; border:1.5px solid var(--border-color) !important;
        font-size:14px !important; padding:10px 14px !important;
        color:var(--text-dark) !important; background:#fff !important;
        transition:border-color .15s, box-shadow .15s !important; width:100%;
    }
    .form-control-custom:focus       { border-color:var(--brand-primary) !important; box-shadow:0 0 0 3px rgba(232,23,74,.1) !important; outline:none !important; }
    .form-control-custom::placeholder { color:#b0b7c3 !important; }

    .btn-close-modal   { background:transparent; border:1.5px solid var(--border-color); color:var(--text-muted); border-radius:var(--radius-sm); padding:9px 22px; font-size:13px; font-weight:500; cursor:pointer; transition:var(--transition); }
    .btn-close-modal:hover { background:var(--surface-bg); color:var(--text-dark); }
    .btn-submit        { background:var(--brand-primary); border:none; color:#fff; border-radius:var(--radius-sm); padding:9px 26px; font-size:13px; font-weight:600; cursor:pointer; transition:var(--transition); box-shadow:0 2px 8px rgba(232,23,74,.22); }
    .btn-submit:hover  { background:var(--brand-primary-hover); transform:translateY(-1px); }

    /* Delete modal */
    .delete-modal-icon { width:64px; height:64px; background:#fff1f3; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:28px; color:#ef4444; }
    .btn-delete-confirm       { background:#ef4444; border:none; color:#fff; border-radius:var(--radius-sm); padding:9px 26px; font-size:13px; font-weight:600; cursor:pointer; transition:var(--transition); }
    .btn-delete-confirm:hover { background:#dc2626; transform:translateY(-1px); }
</style>

{{-- Header --}}
<div class="page-header">
    <h4 class="page-title">Size List</h4>
    <button type="button" class="btn-add" onclick="openCreateModal()">
        <i class="bi bi-plus-circle-fill"></i> Add Size
    </button>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="alert-custom alert-success-custom alert-dismissible" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert-custom alert-danger-custom alert-dismissible" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ $errors->first() }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Table --}}
<div class="data-card">
    <div class="table-responsive">
        <table class="size-table">
            <thead>
                <tr>
                    <th style="width:80px;">SL</th>
                    <th>Name</th>
                    <th style="width:180px;">Status</th>
                    <th style="width:120px; text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sizes as $i => $size)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $size->name }}</td>
                    <td>
                        <form action="{{ route('admin.sizes.toggle', $size->id) }}" method="POST" class="d-inline">
                            @csrf
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       {{ $size->is_active ? 'checked' : '' }}
                                       onchange="this.closest('form').submit()">
                            </div>
                        </form>
                    </td>
                    <td>
                        <div class="action-wrap">
                            <button type="button" class="btn-icon btn-icon-edit"
                                    onclick="openEditModal({{ $size->id }}, '{{ addslashes($size->name) }}')"
                                    title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" class="btn-icon btn-icon-delete"
                                    onclick="openDeleteModal({{ $size->id }}, '{{ addslashes($size->name) }}')"
                                    title="Delete">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <p class="empty-title">No Sizes Found</p>
                            <p class="empty-sub">Click "Add Size" to create your first size.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===================== CREATE MODAL ===================== --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Size</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.sizes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_modal" value="create">
                <div class="modal-body">
                    <label class="form-label-custom">Name <span>*</span></label>
                    <input type="text" name="name" id="create-name"
                           class="form-control-custom"
                           placeholder="Name"
                           value="{{ old('name') }}"
                           autocomplete="off">
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== EDIT MODAL ===================== --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Size</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <label class="form-label-custom">Name <span>*</span></label>
                    <input type="text" name="name" id="edit-name"
                           class="form-control-custom"
                           placeholder="Name"
                           autocomplete="off">
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn-submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== DELETE MODAL ===================== --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-body" style="padding:36px 24px !important; text-align:center;">
                <div class="delete-modal-icon"><i class="bi bi-trash3-fill"></i></div>
                <p style="font-size:15px; font-weight:700; color:#1a1d23; margin:0 0 8px;">Delete Size?</p>
                <p style="font-size:13.5px; color:var(--text-muted); margin:0;">
                    Are you sure you want to delete
                    <strong id="delete-size-name"></strong>?
                    <br>This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer gap-2" style="justify-content:center;">
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal">No, Cancel</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete-confirm">
                        <i class="bi bi-trash3 me-1"></i> Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('create-name').value = '';
        new bootstrap.Modal(document.getElementById('createModal')).show();
        setTimeout(() => document.getElementById('create-name').focus(), 350);
    }

    function openEditModal(id, name) {
        const url = "{{ route('admin.sizes.update', ['size' => '__ID__']) }}".replace('__ID__', id);
        document.getElementById('editForm').action = url;
        document.getElementById('edit-name').value = name;
        new bootstrap.Modal(document.getElementById('editModal')).show();
        setTimeout(() => document.getElementById('edit-name').focus(), 350);
    }

    function openDeleteModal(id, name) {
        const url = "{{ route('admin.sizes.destroy', ['size' => '__ID__']) }}".replace('__ID__', id);
        document.getElementById('deleteForm').action = url;
        document.getElementById('delete-size-name').textContent = '"' + name + '"';
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    @if($errors->any() && old('_modal') === 'create')
        document.addEventListener('DOMContentLoaded', function () { openCreateModal(); });
    @endif
</script>
@endsection
