{{-- resources/views/admin/banner/edit.blade.php --}}
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

    .page-heading {
        display:flex; align-items:center; gap:10px;
        font-size:1.25rem; font-weight:700; color:var(--dark);
        margin-bottom:1.5rem;
    }
    .page-heading i { font-size:1.2rem; }

    .alert-err {
        background:#fff1f2; color:#be123c;
        border-left:3.5px solid #ef4444;
        border-radius:var(--r-md); padding:12px 16px;
        font-size:13px; margin-bottom:1.2rem;
    }
    .alert-err ul { margin:6px 0 0; padding-left:18px; }
    .alert-err li { margin-bottom:2px; }

    .form-card {
        background:#fff; border-radius:var(--r-lg);
        box-shadow:var(--shadow); border:1px solid var(--border);
        padding:28px 28px 32px;
        max-width:780px;
    }

    .fg { display:flex; flex-direction:column; gap:6px; margin-bottom:22px; }
    .fg label { font-size:13px; font-weight:600; color:var(--dark); }
    .fg input[type="text"] {
        width:100%; padding:10px 14px;
        border:1.5px solid var(--border); border-radius:var(--r-sm);
        font-size:13.5px; color:var(--dark); background:#fff;
        outline:none; transition:var(--ease); font-family:inherit;
    }
    .fg input[type="text"]:focus {
        border-color:var(--brand); box-shadow:var(--shadow-focus);
    }
    .fg .err { font-size:12px; color:#ef4444; margin-top:3px; display:flex; align-items:center; gap:4px; }

    .banner-preview-box {
        width:100%; aspect-ratio:4/1;
        border-radius:var(--r-md); overflow:hidden;
        border:1.5px solid var(--border);
        background:var(--surface);
        display:flex; align-items:center; justify-content:center;
        margin-bottom:10px; position:relative; min-height:120px;
    }
    #currentBannerImg {
        width:100%; height:100%; object-fit:cover;
        position:absolute; inset:0;
    }
    #newPreviewImg {
        width:100%; height:100%; object-fit:cover;
        display:none; position:absolute; inset:0; z-index:2;
    }
    .placeholder-text {
        font-size:2.8rem; font-weight:700;
        color:#c9cdd4; letter-spacing:-1px; user-select:none;
        display:none;
    }

    .file-row { display:flex; flex-direction:column; gap:6px; }
    .file-label { font-size:13px; color:var(--dark); }

    .file-input-wrap {
        display:flex; align-items:center;
        border:1.5px solid var(--border); border-radius:var(--r-sm);
        overflow:hidden; background:#fff;
    }
    .file-input-wrap label.choose-btn {
        padding:9px 16px; background:#f3f4f6;
        font-size:13px; font-weight:600; color:var(--dark);
        border-right:1.5px solid var(--border); cursor:pointer;
        white-space:nowrap; user-select:none; transition:var(--ease);
        margin:0;
    }
    .file-input-wrap label.choose-btn:hover { background:#e5e7eb; }
    .file-input-wrap input[type="file"] { display:none; }
    .file-name-display {
        padding:9px 14px; font-size:13px; color:var(--muted);
        flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    }

    .own-shop-row {
        display:flex; align-items:center; gap:10px;
        margin-bottom:28px;
    }
    .own-shop-row label {
        font-size:13.5px; font-weight:700; color:var(--dark);
        cursor:pointer; user-select:none; margin:0;
    }
    .own-shop-row input[type="checkbox"] {
        width:16px; height:16px; accent-color:var(--brand); cursor:pointer;
    }

    .btn-wrap { display:flex; justify-content:flex-end; }
    .btn-submit {
        display:inline-flex; align-items:center; gap:8px;
        background:var(--brand); color:#fff; border:none;
        border-radius:var(--r-md); padding:11px 40px;
        font-size:14px; font-weight:600; cursor:pointer;
        box-shadow:0 2px 10px rgba(232,23,74,.3);
        transition:var(--ease);
    }
    .btn-submit:hover { background:var(--brand-hover); transform:translateY(-1px); }
    .btn-submit:active { transform:translateY(0); }
</style>

<div class="page-heading">
    <i class="bi bi-image"></i> Edit Banner
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

<form action="{{ route('admin.banner.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-card">

        {{-- Title --}}
        <div class="fg">
            <label for="title">Title</label>
            <input type="text" id="title" name="title"
                   value="{{ old('title', $banner->title) }}"
                   placeholder="Enter Short Title"
                   autocomplete="off">
            @error('title')
                <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        {{-- Banner preview: shows current image; new image overlays on select ── --}}
        <div class="banner-preview-box">
            @if($banner->image)
                <img id="currentBannerImg"
                     src="{{ asset($banner->image) }}"
                     alt="{{ $banner->title }}"
                     onerror="this.style.display='none'">
            @else
                <span class="placeholder-text" style="display:block;">2000 × 500</span>
            @endif
            <img id="newPreviewImg" src="" alt="New Preview">
        </div>

        {{-- Image file input --}}
        <div class="file-row" style="margin-bottom:22px;">
            <div class="file-label">
                Banner Ratio 4:1 (2000 × 500 px)
            </div>
            <div class="file-input-wrap">
                <label class="choose-btn" for="imageInput">Choose File</label>
                <input type="file" id="imageInput" name="image"
                       accept="image/jpg,image/jpeg,image/png,image/webp">
                <span class="file-name-display" id="fileNameDisplay">No file chosen</span>
            </div>
            @error('image')
                <div class="err"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        {{-- Own shop checkbox --}}
        <div class="own-shop-row">
            <label for="for_own_shop">This Banner For Own Shop</label>
            <input type="checkbox" id="for_own_shop" name="for_own_shop"
                   value="1"
                   {{ old('for_own_shop', $banner->for_own_shop) ? 'checked' : '' }}>
        </div>

        {{-- Submit --}}
        <div class="btn-wrap">
            <button type="submit" class="btn-submit">Submit</button>
        </div>

    </div>
</form>

<script>
    const imageInput       = document.getElementById('imageInput');
    const newPreviewImg    = document.getElementById('newPreviewImg');
    const currentBannerImg = document.getElementById('currentBannerImg');
    const fileNameDisplay  = document.getElementById('fileNameDisplay');

    imageInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const file   = this.files[0];
            fileNameDisplay.textContent = file.name;
            const reader = new FileReader();
            reader.onload = e => {
                newPreviewImg.src           = e.target.result;
                newPreviewImg.style.display = 'block';
                if (currentBannerImg) currentBannerImg.style.opacity = '0';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
