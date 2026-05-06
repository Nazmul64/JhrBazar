@extends('admin.master')
@section('content')
<style>
    :root {
        --brand: #e8174a;
        --brand-light: rgba(232,23,74,.08);
        --brand-hover: #c9113e;
        --dark: #1a1d23;
        --muted: #6b7280;
        --border: #e5e7eb;
        --surface: #f8f9fc;
        --shadow: 0 1px 4px rgba(0,0,0,.06), 0 2px 12px rgba(0,0,0,.04);
        --r-lg: 14px; --r-md: 10px; --r-sm: 7px;
        --ease: all .18s ease;
    }
    .ph {display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;}
    .ph-title {font-size:1.4rem;font-weight:700;color:var(--dark);margin:0;}
    .data-card {background:#fff;border-radius:var(--r-lg);box-shadow:var(--shadow);border:1px solid var(--border);padding:24px;}
    .form-label {font-size:13px;font-weight:600;color:var(--dark);margin-bottom:6px;}
    .form-control, .form-select {border:1px solid var(--border);border-radius:var(--r-md);padding:10px 14px;font-size:14px;transition:var(--ease);}
    .form-control:focus, .form-select:focus {border-color:var(--brand);box-shadow:0 0 0 3px var(--brand-light);}
    .btn-submit {background:var(--brand);color:#fff;border:none;border-radius:var(--r-md);padding:12px 24px;font-size:14px;font-weight:600;cursor:pointer;transition:var(--ease);box-shadow:0 2px 8px rgba(232,23,74,.2);}
    .btn-submit:hover {background:var(--brand-hover);}
    .btn-cancel {background:var(--surface);color:var(--dark);border:1px solid var(--border);border-radius:var(--r-md);padding:12px 24px;font-size:14px;font-weight:600;cursor:pointer;transition:var(--ease);text-decoration:none;}
    .btn-cancel:hover {background:#f1f5f9;}
    .preview-img {width:100%;max-width:300px;height:auto;border-radius:var(--r-md);border:1px solid var(--border);margin-top:12px;}
</style>

<div class="ph">
    <h4 class="ph-title">Edit Banner</h4>
    <a href="{{ route('seller.banners.index') }}" class="btn-cancel"><i class="bi bi-arrow-left"></i> Back to Banners</a>
</div>

<div class="data-card">
    <form action="{{ route('seller.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Banner Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}" required placeholder="Enter banner title">
                @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">Target Link (Optional)</label>
                <input type="url" name="link" class="form-control" value="{{ old('link', $banner->link) }}" placeholder="https://example.com/promo">
                @error('link') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Start Date (Optional)</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($banner->start_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">End Date (Optional)</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', optional($banner->end_date)->format('Y-m-d')) }}">
                @error('end_date') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Banner Image (Leave empty to keep current image)</label>
                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewFile(this)">
                @if($banner->image)
                    <img id="preview" class="preview-img" src="{{ asset($banner->image) }}" style="display:block;">
                @else
                    <img id="preview" class="preview-img" style="display:none;">
                @endif
                @error('image') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-12 mb-4">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="statusSwitch" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} style="cursor:pointer; width: 2.5em; height: 1.2em;">
                    <label class="form-check-label ms-2 mt-1" for="statusSwitch" style="font-weight:600;color:var(--dark);cursor:pointer;">Active Status</label>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit"><i class="bi bi-save me-1"></i> Update Banner</button>
        </div>
    </form>
</div>

<script>
function previewFile(input) {
    const file = input.files[0];
    const preview = document.getElementById('preview');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
