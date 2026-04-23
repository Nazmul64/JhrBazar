{{-- resources/views/admin/flashsale/create.blade.php --}}
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
    .row-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; }
    .row-grid-full { margin-top:20px; }

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
    .fg input:focus,
    .fg textarea:focus {
        border-color:var(--brand);
        box-shadow:var(--shadow-focus);
    }
    .fg textarea { resize:vertical; min-height:110px; line-height:1.6; }
    .fg .err { font-size:12px; color:#ef4444; margin-top:3px; display:flex; align-items:center; gap:4px; }

    /* ── Thumbnail Upload Zone ── */
    .upload-zone {
        border:2px dashed var(--border); border-radius:var(--r-md);
        padding:28px 20px; text-align:center; cursor:pointer;
        transition:var(--ease); position:relative; background:var(--surface);
    }
    .upload-zone:hover, .upload-zone.drag-over {
        border-color:var(--brand); background:var(--brand-light);
    }
    .upload-zone input[type="file"] {
        position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;
    }
    .upload-icon {
        width:52px; height:52px; border-radius:var(--r-md);
        background:#fff; border:1.5px solid var(--border);
        display:flex; align-items:center; justify-content:center;
        margin:0 auto 12px; font-size:22px; color:var(--muted);
        transition:var(--ease);
    }
    .upload-zone:hover .upload-icon { border-color:var(--brand); color:var(--brand); background:var(--brand-light); }
    .upload-text { font-size:13px; color:var(--muted); line-height:1.5; }
    .upload-text strong { color:var(--brand); }
    .upload-meta { font-size:11.5px; color:#9ca3af; margin-top:5px; }

    /* preview */
    #thumb-preview-wrap {
        display:none; margin-top:14px; position:relative; text-align:left;
    }
    #thumb-preview {
        width:100%; max-height:180px; object-fit:cover;
        border-radius:var(--r-md); border:1.5px solid var(--border); display:block;
    }
    #thumb-remove {
        position:absolute; top:8px; right:8px;
        width:26px; height:26px; border-radius:50%;
        background:rgba(0,0,0,.55); border:none; color:#fff;
        font-size:13px; cursor:pointer; display:flex;
        align-items:center; justify-content:center; transition:var(--ease);
    }
    #thumb-remove:hover { background:rgba(239,68,68,.9); }

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

    /* ── Discount badge preview ── */
    .discount-preview {
        display:inline-flex; align-items:center; gap:5px;
        background:var(--brand-light); color:var(--brand);
        border-radius:20px; padding:4px 12px;
        font-size:12px; font-weight:700; margin-top:6px;
        transition:var(--ease);
    }

    /* ── Footer Buttons ── */
    .form-card-footer {
        padding:18px 24px; border-top:1px solid var(--border);
        background:var(--surface); display:flex; align-items:center;
        justify-content:flex-end; gap:10px;
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

    @media (max-width: 700px) {
        .row-grid, .row-grid-3 { grid-template-columns:1fr; }
    }
</style>

{{-- PAGE HEADER --}}
<div class="ph">
    <div class="ph-left">
        <a href="{{ route('admin.flashsale.index') }}" class="ph-back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="ph-title">Create Flash Sale</h4>
            <p class="ph-sub">Set up a new limited-time flash sale event</p>
        </div>
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

<form action="{{ route('admin.flashsale.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- ── Section 1: Basic Info ── --}}
    <div class="form-card" style="margin-bottom:20px;">
        <div class="form-card-header">
            <div class="form-card-header-icon"><i class="bi bi-info-circle"></i></div>
            <div>
                <p class="form-card-header-title">Basic Information</p>
                <p class="form-card-header-sub">Name, description and thumbnail of the flash sale</p>
            </div>
        </div>
        <div class="form-card-body">

            {{-- Name --}}
            <div class="fg" style="margin-bottom:20px;">
                <label for="name">Sale Name <span class="req">*</span></label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
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
                          placeholder="Describe the flash sale — what products are included, how customers can benefit, etc.">{{ old('description') }}</textarea>
                @error('description')
                    <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Thumbnail --}}
            <div class="fg">
                <label>Thumbnail Image <span class="req">*</span></label>
                <div class="upload-zone" id="uploadZone">
                    <input type="file" name="thumbnail" id="thumbInput"
                           accept="image/jpg,image/jpeg,image/png,image/webp">
                    <div class="upload-icon" id="uploadIcon">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>
                    <div class="upload-text">
                        <strong>Click to upload</strong> or drag & drop
                    </div>
                    <div class="upload-meta">JPG, JPEG, PNG, WEBP &nbsp;·&nbsp; Max 2 MB</div>
                </div>
                <div id="thumb-preview-wrap">
                    <img id="thumb-preview" src="" alt="Thumbnail preview">
                    <button type="button" id="thumb-remove" title="Remove image">
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
                <p class="form-card-header-sub">Define when the sale runs and its minimum discount</p>
            </div>
        </div>
        <div class="form-card-body">

            {{-- Minimum Discount --}}
            <div class="fg" style="margin-bottom:24px;">
                <label for="minimum_discount">Minimum Discount (%) <span class="req">*</span></label>
                <input type="number" id="minimum_discount" name="minimum_discount"
                       value="{{ old('minimum_discount', 0) }}"
                       min="0" max="100" step="0.01"
                       placeholder="e.g. 10">
                <div class="discount-preview" id="discountBadge">
                    <i class="bi bi-tag-fill"></i>
                    <span id="discountBadgeText">0% minimum discount</span>
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
                           value="{{ old('start_date') }}">
                    @error('start_date')
                        <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                {{-- Start Time --}}
                <div class="fg">
                    <label for="start_time">Start Time <span class="req">*</span></label>
                    <input type="time" id="start_time" name="start_time"
                           value="{{ old('start_time') }}">
                    @error('start_time')
                        <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                {{-- End Date --}}
                <div class="fg">
                    <label for="end_date">End Date <span class="req">*</span></label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ old('end_date') }}">
                    @error('end_date')
                        <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                {{-- End Time --}}
                <div class="fg">
                    <label for="end_time">End Time <span class="req">*</span></label>
                    <input type="time" id="end_time" name="end_time"
                           value="{{ old('end_time') }}">
                    @error('end_time')
                        <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

        </div>

        <div class="form-card-footer">
            <a href="{{ route('admin.flashsale.index') }}" class="btn-cancel">
                <i class="bi bi-x-lg"></i> Cancel
            </a>
            <button type="submit" class="btn-submit">
                <i class="bi bi-lightning-charge-fill"></i> Create Flash Sale
            </button>
        </div>
    </div>

</form>

<script>
    // ── Thumbnail preview ──
    const thumbInput   = document.getElementById('thumbInput');
    const previewWrap  = document.getElementById('thumb-preview-wrap');
    const previewImg   = document.getElementById('thumb-preview');
    const removeBtn    = document.getElementById('thumb-remove');
    const uploadZone   = document.getElementById('uploadZone');

    thumbInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                previewWrap.style.display = 'block';
                uploadZone.style.display = 'none';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    removeBtn.addEventListener('click', function () {
        thumbInput.value = '';
        previewImg.src   = '';
        previewWrap.style.display = 'none';
        uploadZone.style.display  = 'block';
    });

    // Drag & drop visual
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

    // ── Discount badge live update ──
    const discInput      = document.getElementById('minimum_discount');
    const discBadgeText  = document.getElementById('discountBadgeText');

    function updateDiscount() {
        const v = parseFloat(discInput.value) || 0;
        discBadgeText.textContent = v + '% minimum discount';
    }
    discInput.addEventListener('input', updateDiscount);
    updateDiscount(); // init
</script>
@endsection
