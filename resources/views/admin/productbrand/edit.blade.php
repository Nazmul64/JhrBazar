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

    .breadcrumb-custom { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--text-muted); margin-bottom:1.6rem; }
    .breadcrumb-custom a { color:var(--brand-primary); text-decoration:none; font-weight:600; }
    .breadcrumb-custom a:hover { text-decoration:underline; }

    .form-card { background:#fff; border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); border:1px solid var(--border-color); max-width:560px; }
    .form-card-header { padding:18px 24px; border-bottom:1px solid var(--border-color); background:var(--surface-bg); border-radius:var(--radius-lg) var(--radius-lg) 0 0; display:flex; align-items:center; justify-content:space-between; }
    .form-card-header-left { display:flex; align-items:center; gap:10px; }
    .form-card-icon { width:34px; height:34px; background:var(--brand-primary-light); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; color:var(--brand-primary); font-size:15px; }
    .form-card-title { font-size:14.5px; font-weight:700; color:var(--text-dark); }
    .brand-id-badge { font-size:11.5px; font-weight:600; background:var(--brand-primary-light); color:var(--brand-primary); border-radius:20px; padding:3px 11px; }

    .form-card-body { padding:28px 24px; }
    .field-group { margin-bottom:20px; }
    .field-label { display:block; font-size:13px; font-weight:600; color:var(--text-dark); margin-bottom:7px; }
    .field-label .required { color:var(--brand-primary); margin-left:2px; }
    .field-hint { font-size:11.5px; color:var(--text-muted); margin-top:5px; }

    .field-input { width:100%; border:1.5px solid var(--border-color); border-radius:var(--radius-sm); padding:10px 14px; font-size:13.5px; color:var(--text-dark); background:#fff; transition:border-color .15s ease, box-shadow .15s ease; outline:none; }
    .field-input:focus { border-color:var(--brand-primary); box-shadow:0 0 0 3px rgba(232,23,74,.1); }
    .field-input::placeholder { color:#b0b7c3; }
    .field-input.is-invalid { border-color:var(--brand-primary) !important; }
    .field-error-msg { font-size:12px; color:var(--brand-primary); margin-top:5px; display:flex; align-items:center; gap:4px; }

    .status-row { display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:var(--surface-bg); border-radius:var(--radius-md); border:1.5px solid var(--border-color); }
    .status-row-left { display:flex; flex-direction:column; gap:3px; }
    .status-row-label { font-size:13px; font-weight:600; color:var(--text-dark); }
    .status-row-sub { font-size:12px; color:var(--text-muted); }

    .form-check-input { width:40px !important; height:22px !important; cursor:pointer; border-radius:11px !important; }
    .form-check-input:checked { background-color:var(--brand-primary) !important; border-color:var(--brand-primary) !important; }
    .form-check-input:focus { box-shadow:0 0 0 .18rem rgba(232,23,74,.2) !important; }
    .form-check-input:not(:checked) { background-color:#d1d5db !important; border-color:#d1d5db !important; }

    .form-card-footer { padding:16px 24px; border-top:1px solid var(--border-color); background:var(--surface-bg); border-radius:0 0 var(--radius-lg) var(--radius-lg); display:flex; align-items:center; justify-content:space-between; gap:10px; }
    .footer-right { display:flex; align-items:center; gap:10px; }

    .btn-cancel { background:transparent; border:1.5px solid var(--border-color); color:var(--text-muted); border-radius:var(--radius-sm); padding:9px 22px; font-size:13px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:6px; transition:var(--transition); }
    .btn-cancel:hover { background:#f3f4f6; color:var(--text-dark); border-color:#d1d5db; }
    .btn-submit { background:var(--brand-primary); border:none; color:#fff; border-radius:var(--radius-sm); padding:9px 26px; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px; cursor:pointer; transition:var(--transition); box-shadow:0 2px 10px rgba(232,23,74,.22); }
    .btn-submit:hover { background:var(--brand-primary-hover); transform:translateY(-1px); box-shadow:0 4px 16px rgba(232,23,74,.32); }
</style>

<div class="breadcrumb-custom">
    <a href="{{ route('admin.productbrands.index') }}">Brands</a>
    <span style="opacity:.4;"><i class="bi bi-chevron-right" style="font-size:10px;"></i></span>
    <span>Edit Brand</span>
</div>

<div class="page-header">
    <div>
        <h4 class="page-title">Edit Brand</h4>
        <p class="page-subtitle">Update brand name or status</p>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-left">
            <div class="form-card-icon"><i class="bi bi-pencil-square"></i></div>
            <span class="form-card-title">Edit: {{ $productbrand->name }}</span>
        </div>
        <span class="brand-id-badge">#{{ $productbrand->id }}</span>
    </div>

    <form action="{{ route('admin.productbrands.update', $productbrand->id) }}" method="POST" id="editForm">
        @csrf
        @method('PUT')

        <div class="form-card-body">
            <div class="field-group">
                <label class="field-label" for="name">
                    Brand Name <span class="required">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       class="field-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       placeholder="Enter brand name"
                       value="{{ old('name', $productbrand->name) }}"
                       autocomplete="off"
                       autofocus>
                @error('name')
                    <div class="field-error-msg">
                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
                <p class="field-hint">Brand names must be unique. Max 255 characters.</p>
            </div>

            <div class="field-group" style="margin-bottom:0;">
                <label class="field-label">Status</label>
                <div class="status-row">
                    <div class="status-row-left">
                        <span class="status-row-label">Brand Active</span>
                        <span class="status-row-sub">Toggle to enable or disable this brand</span>
                    </div>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input"
                               type="checkbox"
                               role="switch"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $productbrand->is_active) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card-footer">
            <div style="font-size:12px; color:var(--text-muted);">
                <span id="changeDot" style="display:none; width:7px; height:7px; background:#f59e0b; border-radius:50%; display:none; margin-right:4px;"></span>
                <span id="changeLabel"></span>
            </div>
            <div class="footer-right">
                <a href="{{ route('admin.productbrands.index') }}" class="btn-cancel">
                    <i class="bi bi-x-lg"></i> Cancel
                </a>
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-lg"></i> Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    var originalName = document.getElementById('name').value;
    var originalStatus = document.querySelector('input[name="is_active"]').checked;

    function checkChanges() {
        var currentName = document.getElementById('name').value;
        var currentStatus = document.querySelector('input[name="is_active"]').checked;
        var hasChanges = (currentName !== originalName) || (currentStatus !== originalStatus);
        var dot = document.getElementById('changeDot');
        var label = document.getElementById('changeLabel');
        if (hasChanges) {
            dot.style.display = 'inline-block';
            label.textContent = 'Unsaved changes';
            label.style.color = '#f59e0b';
        } else {
            dot.style.display = 'none';
            label.textContent = '';
        }
    }

    document.getElementById('name').addEventListener('input', checkChanges);
    document.querySelector('input[name="is_active"]').addEventListener('change', checkChanges);

    var formSubmitted = false;
    document.getElementById('editForm').addEventListener('submit', function() { formSubmitted = true; });
    window.addEventListener('beforeunload', function(e) {
        var hasChanges = (document.getElementById('name').value !== originalName) ||
                         (document.querySelector('input[name="is_active"]').checked !== originalStatus);
        if (hasChanges && !formSubmitted) { e.preventDefault(); e.returnValue = ''; }
    });
</script>
@endsection
