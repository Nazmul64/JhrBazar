@extends('admin.master')
@section('content')
<style>
    :root {
        --brand-primary: #e8174a;
        --brand-primary-light: rgba(232, 23, 74, 0.08);
        --brand-primary-hover: #c9113e;
        --text-dark: #1a1d23;
        --text-muted: #6b7280;
        --border-color: #f0f0f5;
        --card-bg: #ffffff;
        --surface-bg: #f8f9fc;
        --shadow-sm: 0 1px 4px rgba(0,0,0,0.06), 0 2px 12px rgba(0,0,0,0.04);
        --radius-lg: 14px;
        --radius-md: 10px;
        --radius-sm: 7px;
        --transition: all 0.18s ease;
    }

    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.6rem; }
    .page-title { font-size:1.35rem; font-weight:700; color:var(--text-dark); letter-spacing:-0.3px; margin:0; }
    .page-subtitle { font-size:12.5px; color:var(--text-muted); margin:2px 0 0 0; }

    .btn-add {
        display:inline-flex; align-items:center; gap:7px;
        background:var(--brand-primary); color:#fff; border:none;
        border-radius:var(--radius-md); padding:9px 20px;
        font-size:13.5px; font-weight:600; cursor:pointer;
        transition:var(--transition);
        box-shadow:0 2px 10px rgba(232,23,74,0.25);
    }
    .btn-add:hover { background:var(--brand-primary-hover); transform:translateY(-1px); box-shadow:0 4px 16px rgba(232,23,74,0.35); }

    .alert-custom { border-radius:var(--radius-md); border:none; font-size:13.5px; padding:12px 16px; display:flex; align-items:center; gap:9px; margin-bottom:1.2rem; }
    .alert-success-custom { background:#f0fdf4; color:#15803d; border-left:3.5px solid #22c55e; }
    .alert-danger-custom { background:#fff1f3; color:#be123c; border-left:3.5px solid var(--brand-primary); }
    .alert-custom .btn-close { margin-left:auto; opacity:.5; font-size:11px; }

    .data-card { background:var(--card-bg); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); border:1px solid var(--border-color); overflow:hidden; }
    .card-head { display:flex; align-items:center; justify-content:space-between; padding:14px 22px; border-bottom:1px solid var(--border-color); background:var(--surface-bg); }
    .card-head-label { font-size:12.5px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.6px; }
    .badge-count { background:var(--brand-primary-light); color:var(--brand-primary); border-radius:20px; padding:2px 10px; font-size:12px; font-weight:700; }

    .brand-table { width:100%; border-collapse:collapse; }
    .brand-table thead tr { background:var(--surface-bg); }
    .brand-table th { padding:10px 18px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.55px; color:var(--text-muted); border-bottom:1px solid var(--border-color); white-space:nowrap; }
    .brand-table td { padding:12px 18px; font-size:13.5px; color:var(--text-dark); border-bottom:1px solid var(--border-color); vertical-align:middle; }
    .brand-table tbody tr:last-child td { border-bottom:none; }
    .brand-table tbody tr { transition:background .12s ease; }
    .brand-table tbody tr:hover { background:#fafbff; }

    .sl-badge { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; background:var(--surface-bg); border-radius:50%; font-size:12px; font-weight:600; color:var(--text-muted); border:1px solid var(--border-color); }
    .brand-name { font-weight:600; color:var(--text-dark); font-size:13.5px; }
    .brand-name-icon { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:var(--brand-primary-light); border-radius:var(--radius-sm); margin-right:10px; color:var(--brand-primary); font-size:14px; flex-shrink:0; }

    .form-check-input { width:40px !important; height:22px !important; cursor:pointer; border-radius:11px !important; }
    .form-check-input:checked { background-color:var(--brand-primary) !important; border-color:var(--brand-primary) !important; }
    .form-check-input:focus { box-shadow:0 0 0 .18rem rgba(232,23,74,.2) !important; }
    .form-check-input:not(:checked) { background-color:#d1d5db !important; border-color:#d1d5db !important; }
    .status-label { font-size:11.5px; font-weight:600; padding:3px 9px; border-radius:20px; margin-left:8px; }
    .status-active { background:#f0fdf4; color:#15803d; }
    .status-inactive { background:#f3f4f6; color:#6b7280; }

    .action-wrap { display:flex; align-items:center; justify-content:flex-end; gap:6px; }
    .btn-icon { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:var(--radius-sm); border:1.5px solid; background:transparent; cursor:pointer; transition:var(--transition); font-size:13px; }
    .btn-icon-edit { border-color:rgba(232,23,74,.3); color:var(--brand-primary); }
    .btn-icon-edit:hover { background:var(--brand-primary); border-color:var(--brand-primary); color:#fff; transform:translateY(-1px); box-shadow:0 3px 10px rgba(232,23,74,.25); }
    .btn-icon-delete { border-color:rgba(107,114,128,.3); color:#6b7280; }
    .btn-icon-delete:hover { background:#ef4444; border-color:#ef4444; color:#fff; transform:translateY(-1px); box-shadow:0 3px 10px rgba(239,68,68,.25); }

    .empty-state { padding:56px 20px; text-align:center; }
    .empty-icon-wrap { width:72px; height:72px; background:var(--surface-bg); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:16px; border:1px dashed var(--border-color); }
    .empty-icon-wrap i { font-size:30px; color:#d1d5db; }
    .empty-title { font-size:14px; font-weight:600; color:var(--text-muted); margin:0 0 6px; }
    .empty-sub { font-size:12.5px; color:#9ca3af; margin:0; }

    .modal-content { border-radius:var(--radius-lg) !important; border:none !important; box-shadow:0 20px 60px rgba(0,0,0,0.15) !important; }
    .modal-header { border-radius:var(--radius-lg) var(--radius-lg) 0 0 !important; padding:18px 22px !important; border-bottom:1px solid var(--border-color) !important; background:var(--surface-bg); }
    .modal-title-text { font-size:15px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:9px; }
    .modal-title-icon { width:30px; height:30px; background:var(--brand-primary-light); border-radius:var(--radius-sm); display:inline-flex; align-items:center; justify-content:center; color:var(--brand-primary); font-size:14px; }
    .modal-body { padding:24px 22px !important; }
    .modal-footer { padding:14px 22px !important; border-top:1px solid var(--border-color) !important; background:var(--surface-bg); border-radius:0 0 var(--radius-lg) var(--radius-lg) !important; }

    .form-label-custom { font-size:13px; font-weight:600; color:var(--text-dark); margin-bottom:7px; display:block; }
    .form-control-custom { border-radius:var(--radius-sm) !important; border:1.5px solid var(--border-color) !important; font-size:13.5px !important; padding:9px 13px !important; color:var(--text-dark) !important; background:#fff !important; transition:border-color .15s ease, box-shadow .15s ease !important; }
    .form-control-custom:focus { border-color:var(--brand-primary) !important; box-shadow:0 0 0 3px rgba(232,23,74,.1) !important; outline:none !important; }
    .form-control-custom::placeholder { color:#b0b7c3 !important; }

    .btn-modal-close { background:transparent; border:1.5px solid var(--border-color); color:var(--text-muted); border-radius:var(--radius-sm); padding:8px 20px; font-size:13px; font-weight:500; cursor:pointer; transition:var(--transition); }
    .btn-modal-close:hover { background:var(--surface-bg); color:var(--text-dark); }
    .btn-modal-submit { background:var(--brand-primary); border:none; color:#fff; border-radius:var(--radius-sm); padding:8px 24px; font-size:13px; font-weight:600; cursor:pointer; transition:var(--transition); box-shadow:0 2px 8px rgba(232,23,74,.22); }
    .btn-modal-submit:hover { background:var(--brand-primary-hover); box-shadow:0 3px 14px rgba(232,23,74,.32); transform:translateY(-1px); }

    .delete-modal-icon { width:60px; height:60px; background:#fff1f3; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; font-size:26px; color:#ef4444; }
    .delete-modal-name { font-weight:700; color:var(--text-dark); }
    .btn-delete-confirm { background:#ef4444; border:none; color:#fff; border-radius:var(--radius-sm); padding:8px 24px; font-size:13px; font-weight:600; cursor:pointer; transition:var(--transition); }
    .btn-delete-confirm:hover { background:#dc2626; transform:translateY(-1px); }
</style>

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h4 class="page-title">Product Brands</h4>
        <p class="page-subtitle">Manage all product brands from here</p>
    </div>
    <button type="button" class="btn-add" onclick="openCreateModal()">
        <i class="bi bi-plus-lg"></i> Add Brand
    </button>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="alert-custom alert-success-custom alert-dismissible" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert-custom alert-danger-custom alert-dismissible" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ $errors->first() }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Data Card --}}
<div class="data-card">
    <div class="card-head">
        <span class="card-head-label">Brand List</span>
        <span class="badge-count">{{ $brands->count() }} total</span>
    </div>

    <div class="table-responsive">
        <table class="brand-table">
            <thead>
                <tr>
                    <th style="width:64px;">#</th>
                    <th>Brand Name</th>
                    <th style="width:160px; text-align:center;">Status</th>
                    <th style="width:120px; text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $i => $brand)
                <tr>
                    <td><span class="sl-badge">{{ $i + 1 }}</span></td>

                    <td>
                        <div style="display:flex; align-items:center;">
                            <span class="brand-name-icon"><i class="bi bi-tag-fill"></i></span>
                            <span class="brand-name">{{ $brand->name }}</span>
                        </div>
                    </td>

                    <td style="text-align:center;">
                        <form action="{{ route('admin.productbrands.toggle', $brand->id) }}" method="POST" class="d-inline">
                            @csrf
                            <div class="form-check form-switch d-flex align-items-center justify-content-center m-0 gap-2">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       {{ $brand->is_active ? 'checked' : '' }}
                                       onchange="this.closest('form').submit()">
                                <span class="status-label {{ $brand->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </form>
                    </td>

                    <td>
                        <div class="action-wrap">
                            <a href="{{ route('admin.productbrands.edit', $brand->id) }}"
                               class="btn-icon btn-icon-edit" title="Edit Brand">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <button type="button"
                                    class="btn-icon btn-icon-delete"
                                    title="Delete Brand"
                                    onclick="openDeleteModal({{ $brand->id }}, '{{ addslashes($brand->name) }}')">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <div class="empty-icon-wrap"><i class="bi bi-tags"></i></div>
                            <p class="empty-title">No Brands Found</p>
                            <p class="empty-sub">Click "Add Brand" to create your first brand.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- CREATE MODAL --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title-text">
                    <span class="modal-title-icon"><i class="bi bi-plus-lg"></i></span>
                    Create New Brand
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.productbrands.store') }}" method="POST" id="createForm">
                @csrf
                <input type="hidden" name="_modal" value="create">
                <div class="modal-body">
                    <label class="form-label-custom" for="create-name">
                        Brand Name <span style="color:var(--brand-primary)">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="create-name"
                           class="form-control form-control-custom"
                           placeholder="e.g. Samsung, Apple, Nike…"
                           value="{{ old('name') }}"
                           autocomplete="off">
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="bi bi-check-lg me-1"></i> Create Brand
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DELETE CONFIRM MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-body" style="padding:32px 24px !important; text-align:center;">
                <div class="delete-modal-icon"><i class="bi bi-trash3-fill"></i></div>
                <p style="font-size:15px; font-weight:700; color:#1a1d23; margin:0 0 8px;">Delete Brand?</p>
                <p style="font-size:13.5px; color:var(--text-muted); margin:0;">
                    Are you sure you want to delete
                    <span class="delete-modal-name" id="delete-brand-name"></span>?
                    <br>This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer gap-2" style="justify-content:center; border-top:1px solid var(--border-color);">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">No, Cancel</button>

                {{-- ✅ FIX: form এর বাইরে action dynamically set হবে JS দিয়ে --}}
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
        var modal = new bootstrap.Modal(document.getElementById('createModal'));
        modal.show();
        setTimeout(() => document.getElementById('create-name').focus(), 350);
    }

    // ✅ FIX: Laravel route() helper দিয়ে সঠিক URL তৈরি করা হচ্ছে
    function openDeleteModal(id, name) {
        // Laravel route helper ব্যবহার করে সঠিক URL বানাচ্ছি
        const url = "{{ route('admin.productbrands.destroy', ['productbrand' => '__ID__']) }}".replace('__ID__', id);
        document.getElementById('deleteForm').action = url;
        document.getElementById('delete-brand-name').textContent = '"' + name + '"';
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    @if($errors->any() && old('_modal') === 'create')
        document.addEventListener('DOMContentLoaded', function () { openCreateModal(); });
    @endif
</script>
@endsection
