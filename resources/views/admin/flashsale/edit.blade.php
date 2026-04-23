{{-- resources/views/admin/flashsale/edit.blade.php --}}
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
        --shadow-focus:0 0 0 3px rgba(232,23,74,.15);
        --r-lg: 14px; --r-md: 10px; --r-sm: 7px;
        --ease: all .18s ease;
    }

    /* ── Page Header ── */
    .ph { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
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
    .ph-sub { font-size:13px; color:var(--muted); margin:3px 0 0; }

    /* ── Edit badge ── */
    .edit-badge {
        display:inline-flex; align-items:center; gap:6px;
        background:#fff7ed; color:#c2410c;
        border:1.5px solid #fed7aa; border-radius:20px;
        padding:5px 14px; font-size:12px; font-weight:600;
    }

    /* ── Form Card ── */
    .form-card {
        background:#fff; border-radius:var(--r-lg);
        box-shadow:var(--shadow); border:1px solid var(--border);
        overflow:hidden;
    }
    .form-card-header {
        padding:18px 24px; border-bottom:1px solid var(--border);
        background:var(--surface); display:flex; align-items:center; gap:10px;
    }
    .form-card-header-icon {
        width:36px; height:36px; border-radius:var(--r-sm);
        background:var(--brand-light); color:var(--brand);
        display:flex; align-items:center; justify-content:center; font-size:17px;
    }
    .form-card-header-title { font-size:14px; font-weight:700; color:var(--dark); margin:0; }
    .form-card-header-sub { font-size:12px; color:var(--muted); margin:2px 0 0; }
    .form-card-body { padding:26px 24px; }

    /* ── Form Groups ── */
    .row-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
    .fg { display:flex; flex-direction:column; gap:6px; }
    .fg label {
        font-size:12.5px; font-weight:600; color:var(--dark);
        display:flex; align-items:center; gap:5px;
    }
    .fg label .req { color:var(--brand); }
    .fg .hint { font-size:11.5px; color:var(--muted); margin-top:2px; }

    .fg input[type="text"],
    .fg input[type="number"],
    .fg input[type="date"],
    .fg input[type="time"],
    .fg textarea {
        width:100%; padding:10px 14px;
        border:1.5px solid var(--border); border-radius:var(--r-sm);
        font-size:13.5px; color:var(--dark); background:#fff;
        outline:none; transition:var(--ease);
        font-family:inherit;
    }
    .fg input:focus, .fg textarea:focus {
        border-color:var(--brand); box-shadow:var(--shadow-focus);
    }
    .fg textarea { resize:vertical; min-height:110px; line-height:1.6; }
    .fg .err { font-size:12px; color:#ef4444; margin-top:3px; display:flex; align-items:center; gap:4px; }

    /* ── Thumbnail area ── */
    /* Current thumb */
    .current-thumb-box {
        border:1.5px solid var(--border); border-radius:var(--r-md);
        padding:14px; background:var(--surface);
        display:flex; align-items:center; gap:16px;
        margin-bottom:14px;
    }
    .current-thumb-box img {
        width:90px; height:62px; object-fit:cover;
        border-radius:var(--r-sm); border:1px solid var(--border);
        flex-shrink:0;
    }
    .current-thumb-box .ct-info { flex:1; }
    .current-thumb-box .ct-label {
        font-size:12px; font-weight:700; color:var(--muted);
        text-transform:uppercase; letter-spacing:.4px; margin-bottom:4px;
    }
    .current-thumb-box .ct-name {
        font-size:12.5px; color:var(--dark);
        word-break:break-all; line-height:1.4;
    }
    .ct-change-btn {
        display:inline-flex; align-items:center; gap:5px;
        background:transparent; border:1.5px solid var(--border);
        border-radius:var(--r-sm); padding:6px 14px;
        font-size:12px; font-weight:600; color:var(--muted);
        cursor:pointer; transition:var(--ease); white-space:nowrap;
    }
    .ct-change-btn:hover { border-color:var(--brand); color:var(--brand); background:var(--brand-light); }

    /* New upload zone */
    .upload-zone {
        border:2px dashed var(--border); border-radius:var(--r-md);
        padding:24px 20px; text-align:center; cursor:pointer;
        transition:var(--ease); position:relative; background:var(--surface);
        display:none;
    }
    .upload-zone.visible { display:block; }
    .upload-zone:hover, .upload-zone.drag-over {
        border-color:var(--brand); background:var(--brand-light);
    }
    .upload-zone input[type="file"] {
        position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;
    }
    .upload-zone .upload-icon {
        width:46px; height:46px; border-radius:var(--r-sm);
        background:#fff; border:1.5px solid var(--border);
        display:flex; align-items:center; justify-content:center;
        margin:0 auto 10px; font-size:20px; color:var(--muted);
        transition:var(--ease);
    }
    .upload-zone:hover .upload-icon { border-color:var(--brand); color:var(--brand); background:var(--brand-light); }
    .upload-text { font-size:13px; color:var(--muted); }
    .upload-text strong { color:var(--brand); }
    .upload-meta { font-size:11.5px; color:#9ca3af; margin-top:4px; }

    /* new preview */
    #new-preview-wrap { display:none; margin-top:12px; position:relative; }
    #new-preview {
        width:100%; max-height:180px; object-fit:cover;
        border-radius:var(--r-md); border:1.5px solid var(--border);
    }
    #new-remove {
        position:absolute; top:8px; right:8px;
        width:26px; height:26px; border-radius:50%;
        background:rgba(0,0,0,.55); border:none; color:#fff;
        font-size:13px; cursor:pointer; display:flex;
        align-items:center; justify-content:center; transition:var(--ease);
    }
    #new-remove:hover { background:rgba(239,68,68,.9); }

    /* ── Divider ── */
    .section-divider {
        display:flex; align-items:center; gap:12px;
        margin:26px 0 20px;
    }
    .section-divider span {
        font-size:12px; font-weight:700; color:var(--muted);
        text-transform:uppercase; letter-spacing:.5px; white-space:nowrap;
    }
    .section-divider::before, .section-divider::after {
        content:''; flex:1; height:1px; background:var(--border);
    }

    /* ── Discount badge ── */
    .discount-preview {
        display:inline-flex; align-items:center; gap:5px;
        background:var(--brand-light); color:var(--brand);
        border-radius:20px; padding:4px 12px;
        font-size:12px; font-weight:700; margin-top:6px;
        transition:var(--ease);
    }

    /* ── Status toggle ── */
    .status-row {
        display:flex; align-items:center; justify-content:space-between;
        padding:14px 18px; border:1.5px solid var(--border);
        border-radius:var(--r-md); margin-top:20px; background:var(--surface);
    }
    .status-row-label { font-size:13.5px; font-weight:600; color:var(--dark); }
    .status-row-sub { font-size:12px; color:var(--muted); margin-top:2px; }
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

    /* ── Footer Buttons ── */
    .form-card-footer {
        padding:18px 24px; border-top:1px solid var(--border);
        background:var(--surface); display:flex; align-items:center;
        justify-content:space-between; gap:10px; flex-wrap:wrap;
    }
    .btn-cancel {
        display:inline-flex; align-items:center; gap:7px;
        background:transparent; border:1.5px solid var(--border);
        border-radius:var(--r-md); padding:10px 22px;
        font-size:13.5px; font-weight:600; color:var(--muted);
        text-decoration:none; cursor:pointer; transition:var(--ease);
    }
    .btn-cancel:hover { border-color:#9ca3af; color:var(--dark); background:#f3f4f6; }
    .btn-submit {
        display:inline-flex; align-items:center; gap:7px;
        background:var(--brand); color:#fff; border:none;
        border-radius:var(--r-md); padding:10px 26px;
        font-size:13.5px; font-weight:600; cursor:pointer;
        box-shadow:0 2px 10px rgba(232,23,74,.3);
        transition:var(--ease);
    }
    .btn-submit:hover { background:var(--brand-hover); transform:translateY(-1px); }
    .btn-submit:active { transform:translateY(0); }

    /* ── Alert errors ── */
    .alert-err {
        background:#fff1f2; color:#be123c;
        border-left:3.5px solid #ef4444;
        border-radius:var(--r-md); padding:12px 16px;
        font-size:13px; margin-bottom:1.2rem;
    }
    .alert-err ul { margin:6px 0 0; padding-left:18px; }
    .alert-err li { margin-bottom:2px; }

    /* ── Meta info bar ── */
    .meta-bar {
        display:flex; align-items:center; gap:16px; flex-wrap:wrap;
        padding:12px 16px; background:var(--surface);
        border-radius:var(--r-md); border:1px solid var(--border);
        margin-bottom:20px; font-size:12.5px; color:var(--muted);
    }
    .meta-bar span { display:flex; align-items:center; gap:5px; }
    .meta-bar strong { color:var(--dark); }

    @media (max-width: 700px) {
        .row-grid { grid-template-columns:1fr; }
        .form-card-footer { justify-content:flex-end; }
        .current-thumb-box { flex-wrap:wrap; }
    }
</style>

{{-- PAGE HEADER --}}
<div class="ph">
    <div class="ph-left">
        <a href="{{ route('admin.flashsale.index') }}" class="ph-back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="ph-title">Edit Flash Sale</h4>
            <p class="ph-sub">Update the details of "{{ $flashsale->name }}"</p>
        </div>
    </div>
    <div class="edit-badge">
        <i class="bi bi-pencil-square"></i> Editing ID #{{ $flashsale->id }}
    </div>
</div>

@if($errors->any())
<div class="alert-err">
    <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Please fix the following errors:</strong>
    <ul>
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.flashsale.update', $flashsale->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- ── Section 1: Basic Info ── --}}
    <div class="form-card" style="margin-bottom:20px;">
        <div class="form-card-header">
            <div class="form-card-header-icon"><i class="bi bi-info-circle"></i></div>
            <div>
                <p class="form-card-header-title">Basic Information</p>
                <p class="form-card-header-sub">Update the name, description and thumbnail</p>
            </div>
        </div>
        <div class="form-card-body">

            {{-- Name --}}
            <div class="fg" style="margin-bottom:20px;">
                <label for="name">Sale Name <span class="req">*</span></label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $flashsale->name) }}"
                       placeholder="e.g. Summer Mega Flash Sale"
                       autocomplete="off">
                @error('name')
                    <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="fg" style="margin-bottom:20px;">
                <label for="description">Description <span class="req">*</span></label>
                <textarea id="description" name="description"
                          placeholder="Describe the flash sale…">{{ old('description', $flashsale->description) }}</textarea>
                @error('description')
                    <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Thumbnail --}}
            <div class="fg">
                <label>Thumbnail Image <span style="font-weight:400; color:var(--muted);">(optional — leave blank to keep current)</span></label>

                {{-- Current thumbnail --}}
                @if($flashsale->thumbnail)
                <div class="current-thumb-box" id="currentThumbBox">
                    <img src="{{ asset($flashsale->thumbnail) }}" alt="{{ $flashsale->name }}"
                         onerror="this.closest('.current-thumb-box').style.display='none'">
                    <div class="ct-info">
                        <div class="ct-label">Current Thumbnail</div>
                        <div class="ct-name">{{ basename($flashsale->thumbnail) }}</div>
                    </div>
                    <button type="button" class="ct-change-btn" id="ctChangeBtn">
                        <i class="bi bi-arrow-repeat"></i> Replace
                    </button>
                </div>
                @endif

                {{-- Upload zone --}}
                <div class="upload-zone {{ !$flashsale->thumbnail ? 'visible' : '' }}" id="uploadZone">
                    <input type="file" name="thumbnail" id="thumbInput"
                           accept="image/jpg,image/jpeg,image/png,image/webp">
                    <div class="upload-icon">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>
                    <div class="upload-text">
                        <strong>Click to upload</strong> or drag & drop new image
                    </div>
                    <div class="upload-meta">JPG, JPEG, PNG, WEBP &nbsp;·&nbsp; Max 2 MB</div>
                </div>

                {{-- New image preview --}}
                <div id="new-preview-wrap">
                    <img id="new-preview" src="" alt="New thumbnail preview">
                    <button type="button" id="new-remove" title="Remove new image">
                        <i class="bi bi-x"></i>
                    </button>
                </div>

                @error('thumbnail')
                    <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

        </div>
    </div>

    {{-- ── Section 2: Schedule & Discount ── --}}
    <div class="form-card" style="margin-bottom:20px;">
        <div class="form-card-header">
            <div class="form-card-header-icon"><i class="bi bi-calendar-event"></i></div>
            <div>
                <p class="form-card-header-title">Schedule & Discount</p>
                <p class="form-card-header-sub">Update the sale period and minimum discount</p>
            </div>
        </div>
        <div class="form-card-body">

            {{-- Minimum Discount --}}
            <div class="fg" style="margin-bottom:24px;">
                <label for="minimum_discount">Minimum Discount (%) <span class="req">*</span></label>
                <input type="number" id="minimum_discount" name="minimum_discount"
                       value="{{ old('minimum_discount', $flashsale->minimum_discount) }}"
                       min="0" max="100" step="0.01"
                       placeholder="e.g. 10">
                <div class="discount-preview">
                    <i class="bi bi-tag-fill"></i>
                    <span id="discountBadgeText">{{ $flashsale->minimum_discount }}% minimum discount</span>
                </div>
                <span class="hint">Products must offer at least this discount to appear in the flash sale.</span>
                @error('minimum_discount')
                    <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="section-divider"><span>Sale Period</span></div>

            <div class="row-grid">
                {{-- Start Date --}}
                <div class="fg">
                    <label for="start_date">Start Date <span class="req">*</span></label>
                    <input type="date" id="start_date" name="start_date"
                           value="{{ old('start_date', $flashsale->start_date) }}">
                    @error('start_date')
                        <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                {{-- Start Time --}}
                <div class="fg">
                    <label for="start_time">Start Time <span class="req">*</span></label>
                    <input type="time" id="start_time" name="start_time"
                           value="{{ old('start_time', \Carbon\Carbon::parse($flashsale->start_time)->format('H:i')) }}">
                    @error('start_time')
                        <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                {{-- End Date --}}
                <div class="fg">
                    <label for="end_date">End Date <span class="req">*</span></label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ old('end_date', $flashsale->end_date) }}">
                    @error('end_date')
                        <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                {{-- End Time --}}
                <div class="fg">
                    <label for="end_time">End Time <span class="req">*</span></label>
                    <input type="time" id="end_time" name="end_time"
                           value="{{ old('end_time', \Carbon\Carbon::parse($flashsale->end_time)->format('H:i')) }}">
                    @error('end_time')
                        <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Status toggle --}}
            <div class="status-row">
                <div>
                    <div class="status-row-label">Flash Sale Status</div>
                    <div class="status-row-sub">Enable or disable this flash sale</div>
                </div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch"
                           id="is_active" name="is_active" value="1"
                           {{ $flashsale->is_active ? 'checked' : '' }}>
                </div>
            </div>

        </div>

        <div class="form-card-footer">
            <a href="{{ route('admin.flashsale.index') }}" class="btn-cancel">
                <i class="bi bi-x-lg"></i> Cancel
            </a>
            <button type="submit" class="btn-submit">
                <i class="bi bi-check2-circle"></i> Save Changes
            </button>
        </div>
    </div>

</form>

<script>
    // ── Replace thumbnail ──
    const ctChangeBtn    = document.getElementById('ctChangeBtn');
    const currentThumbBox= document.getElementById('currentThumbBox');
    const uploadZone     = document.getElementById('uploadZone');
    const thumbInput     = document.getElementById('thumbInput');
    const newPreviewWrap = document.getElementById('new-preview-wrap');
    const newPreviewImg  = document.getElementById('new-preview');
    const newRemoveBtn   = document.getElementById('new-remove');

    if (ctChangeBtn) {
        ctChangeBtn.addEventListener('click', function () {
            currentThumbBox.style.display = 'none';
            uploadZone.classList.add('visible');
        });
    }

    thumbInput && thumbInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                newPreviewImg.src = e.target.result;
                newPreviewWrap.style.display = 'block';
                uploadZone.classList.remove('visible');
                uploadZone.style.display = 'none';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    newRemoveBtn && newRemoveBtn.addEventListener('click', function () {
        thumbInput.value = '';
        newPreviewImg.src = '';
        newPreviewWrap.style.display = 'none';

        // Show current thumb if exists, else show upload zone
        if (currentThumbBox) {
            currentThumbBox.style.display = 'flex';
            uploadZone.style.display = '';
            uploadZone.classList.remove('visible');
        } else {
            uploadZone.classList.add('visible');
        }
    });

    // Drag & drop
    if (uploadZone) {
        uploadZone.addEventListener('dragover', e => {
            e.preventDefault(); uploadZone.classList.add('drag-over');
        });
        uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('drag-over'));
        uploadZone.addEventListener('drop', e => {
            e.preventDefault(); uploadZone.classList.remove('drag-over');
            if (e.dataTransfer.files.length) {
                thumbInput.files = e.dataTransfer.files;
                thumbInput.dispatchEvent(new Event('change'));
            }
        });
    }

    // ── Discount badge live update ──
    const discInput     = document.getElementById('minimum_discount');
    const discBadgeText = document.getElementById('discountBadgeText');

    function updateDiscount() {
        const v = parseFloat(discInput.value) || 0;
        discBadgeText.textContent = v + '% minimum discount';
    }
    discInput && discInput.addEventListener('input', updateDiscount);
</script>
@endsection
