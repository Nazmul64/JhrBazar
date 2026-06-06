@extends('admin.master')
@section('content')
<style>
    :root {
        --brand-primary: #1d4ed8;
        --brand-primary-light: rgba(29, 78, 216, 0.1);
        --text-dark: #111827;
        --text-muted: #6b7280;
        --border-color: #e5e7eb;
        --card-bg: #ffffff;
        --surface-bg: #f8fafc;
        --shadow-sm: 0 4px 18px rgba(15, 23, 42, 0.08);
        --radius-lg: 18px;
        --radius-md: 12px;
        --radius-sm: 8px;
        --transition: all 0.18s ease;
    }

    .page-header { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:1.5rem; }
    .page-title { font-size:1.45rem; font-weight:700; color:var(--text-dark); margin:0; }
    .page-subtitle { font-size:12.8px; color:var(--text-muted); margin:4px 0 0 0; }

    .btn-add {
        display:inline-flex; align-items:center; gap:8px;
        background:var(--brand-primary); color:#fff; border:none;
        border-radius:12px; padding:11px 20px;
        font-size:13.5px; font-weight:700; cursor:pointer;
        transition:var(--transition);
        box-shadow:0 12px 22px rgba(29,78,216,0.16);
    }
    .btn-add:hover { transform:translateY(-1px); background:#1e40af; }

    .alert-custom { border-radius:14px; border:none; font-size:13.5px; padding:14px 18px; display:flex; align-items:center; gap:10px; margin-bottom:1.4rem; }
    .alert-success-custom { background:#ecfdf5; color:#166534; border-left:4px solid #22c55e; }
    .alert-danger-custom { background:#fef2f2; color:#b91c1c; border-left:4px solid #ef4444; }

    .data-card { background:var(--card-bg); border-radius:var(--radius-lg); border:1px solid var(--border-color); overflow:hidden; box-shadow:var(--shadow-sm); }
    .card-head { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--border-color); background:var(--surface-bg); }
    .card-head-label { font-size:12.5px; font-weight:700; letter-spacing:.35px; color:var(--text-muted); text-transform:uppercase; }
    .badge-count { background:rgba(29,78,216,0.08); color:var(--brand-primary); border-radius:999px; padding:4px 12px; font-size:12px; font-weight:700; }

    .brand-table { width:100%; border-collapse:collapse; }
    .brand-table th, .brand-table td { padding:14px 18px; border-bottom:1px solid var(--border-color); }
    .brand-table th { font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.4px; }
    .brand-table td { font-size:13.4px; color:var(--text-dark); vertical-align:middle; }
    .brand-table tbody tr:hover { background:#f8fafc; }

    .sl-badge { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:50%; background:#f3f4f6; color:#374151; font-weight:700; }
    .brand-preview { display:inline-flex; align-items:center; gap:14px; }
    .brand-preview-thumb { width:72px; height:72px; border-radius:16px; overflow:hidden; border:1px solid var(--border-color); background:#fff; display:flex; align-items:center; justify-content:center; }
    .brand-preview-thumb img { width:100%; height:100%; object-fit:contain; }
    .brand-title { font-size:14.5px; font-weight:700; color:var(--text-dark); }
    .brand-caption { display:block; color:var(--text-muted); font-size:12.9px; margin-top:6px; }

    .status-label { font-size:11.8px; font-weight:700; padding:4px 10px; border-radius:999px; display:inline-flex; align-items:center; gap:6px; }
    .status-active { background:#eef2ff; color:#4338ca; }
    .status-inactive { background:#f8fafc; color:#475569; }

    .action-wrap { display:flex; align-items:center; justify-content:flex-end; gap:8px; }
    .btn-icon { width:38px; height:38px; border-radius:12px; border:1px solid #d1d5db; background:#fff; color:#374151; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; transition:var(--transition); }
    .btn-icon:hover { border-color:#1d4ed8; color:#1d4ed8; transform:translateY(-1px); }
    .btn-icon-delete:hover { background:#fef2f2; color:#b91c1c; border-color:#fca5a5; }

    .form-label-custom { display:block; font-size:13.2px; font-weight:700; margin-bottom:10px; color:var(--text-dark); }
    .form-control-custom { border-radius:12px; border:1px solid var(--border-color); padding:11px 14px; font-size:14px; color:var(--text-dark); width:100%; }
    .form-control-custom:focus { outline:none; border-color:#1d4ed8; box-shadow:0 0 0 3px rgba(59,130,246,0.18); }
    .form-check-input { width:44px; height:24px; cursor:pointer; }
    .btn-modal-submit { background:#1d4ed8; border:none; color:#fff; border-radius:12px; padding:10px 22px; font-weight:700; }
    .btn-modal-submit:hover { background:#1e40af; }

    .empty-state { padding:60px 20px; text-align:center; }
    .empty-title { font-size:15px; font-weight:700; color:var(--text-dark); margin-bottom:6px; }
    .empty-sub { color:var(--text-muted); font-size:13px; }
</style>

<div class="page-header">
    <div>
        <h4 class="page-title">Our Brand Slider</h4>
        <p class="page-subtitle">Upload and manage brand images for the homepage slider.</p>
    </div>
    <button type="button" class="btn-add" onclick="openCreateModal()">
        <i class="bi bi-plus-lg"></i> Upload Image
    </button>
</div>

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

<div class="data-card">
    <div class="card-head">
        <span class="card-head-label">Uploaded Brand Images</span>
        <span class="badge-count">{{ $brands->count() }} items</span>
    </div>

    <div class="table-responsive">
        <table class="brand-table">
            <thead>
                <tr>
                    <th style="width:54px;">#</th>
                    <th>Preview</th>
                    <th style="width:160px; text-align:center;">Status</th>
                    <th style="width:120px; text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $index => $brand)
                    <tr>
                        <td><span class="sl-badge">{{ $index + 1 }}</span></td>
                        <td>
                            <div class="brand-preview">
                                <div class="brand-preview-thumb">
                                    <img src="{{ $brand->image ? asset($brand->image) : asset('placeholder.jpg') }}" alt="Brand {{ $brand->id }}">
                                </div>
                                <div>
                                    <span class="brand-title">Brand #{{ $brand->id }}</span>
                                    <span class="brand-caption">{{ $brand->image }}</span>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <form action="{{ route('admin.ourbrands.toggle', $brand->id) }}" method="POST" class="d-inline">
                                @csrf
                                <div class="form-check form-switch d-flex align-items-center justify-content-center gap-2 m-0">
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
                                <a href="{{ route('admin.ourbrands.edit', $brand->id) }}" class="btn-icon" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn-icon btn-icon-delete" title="Delete" onclick="openDeleteModal({{ $brand->id }}, '{{ addslashes($brand->title ?: 'Brand '.$brand->id) }}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <div class="empty-title">No brand slider images yet.</div>
                                <p class="empty-sub">Use the upload button to add images to public/uploads/ourbrand.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Our Brand Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.ourbrands.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <label class="form-label-custom" for="image">Image <span style="color:var(--brand-primary);">*</span></label>
                    <input type="file" name="image" id="image" class="form-control form-control-custom" accept="image/*" required>

                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Upload Image</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:430px;">
        <div class="modal-content">
            <div class="modal-body text-center" style="padding:32px 24px !important;">
                <div class="delete-modal-icon" style="width:64px;height:64px;border-radius:50%;background:#fef2f2;color:#b91c1c;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <p style="font-size:15px;font-weight:700;color:var(--text-dark);margin-bottom:8px;">Delete Brand Image?</p>
                <p style="color:var(--text-muted);font-size:13.4px;margin-bottom:18px;">Are you sure you want to delete <span id="delete-brand-name"></span>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer" style="justify-content:center;">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modal-submit" style="background:#dc2626;">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openCreateModal() {
        var modal = new bootstrap.Modal(document.getElementById('createModal'));
        modal.show();
    }

    function openDeleteModal(id, name) {
        const url = "{{ route('admin.ourbrands.destroy', ['ourbrand' => '__ID__']) }}".replace('__ID__', id);
        document.getElementById('deleteForm').action = url;
        document.getElementById('delete-brand-name').textContent = '"' + name + '"';
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endsection
