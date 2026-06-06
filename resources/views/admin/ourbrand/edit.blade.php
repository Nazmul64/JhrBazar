@extends('admin.master')
@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
    .page-title { font-size:1.45rem; font-weight:700; color:#111827; margin:0; }
    .page-subtitle { color:#6b7280; margin:4px 0 0 0; font-size:13px; }
    .form-card { background:#fff; border:1px solid #e5e7eb; border-radius:18px; box-shadow:0 12px 28px rgba(15,23,42,0.06); padding:28px; }
    .form-label { display:block; margin-bottom:10px; font-weight:700; color:#111827; }
    .form-control { width:100%; padding:12px 14px; border:1px solid #d1d5db; border-radius:12px; font-size:14px; }
    .form-control:focus { outline:none; border-color:#1d4ed8; box-shadow:0 0 0 3px rgba(59,130,246,0.18); }
    .btn-save { background:#1d4ed8; border:none; color:#fff; padding:11px 22px; border-radius:12px; font-weight:700; }
    .btn-save:hover { background:#1e40af; }
    .btn-cancel { background:#f3f4f6; border:none; color:#374151; padding:11px 22px; border-radius:12px; font-weight:700; }
    .preview-thumb { width:180px; height:140px; overflow:hidden; border-radius:16px; border:1px solid #e5e7eb; background:#fff; display:inline-flex; align-items:center; justify-content:center; margin-bottom:16px; }
    .preview-thumb img { width:100%; height:100%; object-fit:contain; }
</style>

<div class="page-header">
    <div>
        <h4 class="page-title">Edit Our Brand Slider</h4>
        <p class="page-subtitle">Update the image and sort order for the homepage brand slider.</p>
    </div>
    <a href="{{ route('admin.ourbrands.index') }}" class="btn-cancel">Back to List</a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card">
    <form action="{{ route('admin.ourbrands.update', $ourbrand->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label class="form-label" for="image">Image</label>
        <div class="preview-thumb">
            <img src="{{ $ourbrand->image ? asset($ourbrand->image) : asset('placeholder.jpg') }}" alt="Current brand image">
        </div>
        <input type="file" name="image" id="image" class="form-control" accept="image/*">
        <small class="text-muted">Upload a new image only if you want to replace the existing one.</small>

        <div class="form-check form-switch mt-4">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $ourbrand->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>

        <div class="mt-5 d-flex gap-2">
            <button type="submit" class="btn-save">Save Changes</button>
            <a href="{{ route('admin.ourbrands.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection
