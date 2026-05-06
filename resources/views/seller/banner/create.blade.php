@extends('admin.master')
@section('content')
<style>
    :root {
        --brand:       #e8174a;
        --brand-hover: #c9113e;
        --dark:        #1a1d23;
        --muted:       #6b7280;
        --border:      #e5e7eb;
        --surface:     #f8f9fc;
        --r-lg: 14px; --r-md: 10px; --r-sm: 7px;
    }

    .form-card { background:#fff; border-radius:var(--r-lg); box-shadow:0 1px 4px rgba(0,0,0,.06); border:1px solid var(--border); padding:24px; }
    .form-title { font-size:16px; font-weight:700; color:var(--dark); margin-bottom:20px; display:flex; align-items:center; gap:8px; }
    .form-label { font-size:13px; font-weight:600; color:var(--dark); margin-bottom:6px; }
    .text-danger { color:#ef4444; }
    .form-control, .form-select { border-radius:var(--r-sm); border:1px solid var(--border); padding:10px 14px; font-size:14px; color:var(--dark); box-shadow:none; }
    .form-control:focus, .form-select:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(232,23,74,.1); }
    .btn-submit { background:var(--brand); color:#fff; border:none; border-radius:var(--r-md); padding:10px 24px; font-size:14px; font-weight:600; cursor:pointer; }
    .btn-submit:hover { background:var(--brand-hover); }
    .btn-cancel { background:transparent; color:var(--muted); border:1px solid var(--border); border-radius:var(--r-md); padding:10px 24px; font-size:14px; font-weight:600; text-decoration:none; }
    .btn-cancel:hover { background:var(--surface); color:var(--dark); }
</style>

<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('seller.banner.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0 fw-bold" style="color:var(--dark);">Add New Banner</h4>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card" style="max-width:800px;">
        <div class="form-title">
            <i class="bi bi-plus-circle"></i> Add New Banner
        </div>

        <form action="{{ route('seller.banner.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-md-12">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Banner Title" value="{{ old('title') }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Banner Image <span class="text-danger">*</span> <small class="text-muted">(Ratio: 3:1)</small></label>
                    <input type="file" name="image" class="form-control" accept="image/*" required onchange="previewImg(this)">
                    <img id="preview" src="#" alt="Preview" style="max-width: 100%; margin-top: 10px; display: none; border-radius: 8px;">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top:1px solid var(--border);">
                <a href="{{ route('seller.banner.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImg(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('preview').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
