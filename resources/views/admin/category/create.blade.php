@extends('admin.master')

@section('content')

{{-- Page Header --}}
<div class="mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-grid me-2"></i> Create New Category
    </h4>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        {{-- Section Title --}}
        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-person-fill me-2 text-muted"></i>
            <span class="fw-semibold">Category Information</span>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Name --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Name <span class="text-danger">*</span>
                </label>
                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Enter Name"
                       value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Thumbnail Preview --}}
            <div class="mb-3 text-center">
                <div id="preview-wrapper"
                     class="mx-auto rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden"
                     style="width:160px;height:160px;">
                    <span class="text-muted small" id="preview-placeholder">500 × 500</span>
                    <img id="preview-img"
                         src=""
                         alt="Preview"
                         class="d-none w-100 h-100"
                         style="object-fit:cover;">
                </div>
            </div>

            {{-- Thumbnail Upload --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Thumbnail (Ratio 1:1) <span class="text-danger">*</span>
                </label>
                <input type="file"
                       name="thumbnail"
                       id="thumbnail"
                       class="form-control @error('thumbnail') is-invalid @enderror"
                       accept=".jpg,.jpeg,.png,.webp"
                       onchange="previewImage(this)">
                @error('thumbnail')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-5">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description"
                          class="form-control @error('description') is-invalid @enderror"
                          rows="4"
                          placeholder="Enter description">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.categories.index') }}"
                   class="btn btn-secondary px-4">Back</a>
                <button type="submit"
                        class="btn btn-danger px-5">Submit</button>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function previewImage(input) {
        const img         = document.getElementById('preview-img');
        const placeholder = document.getElementById('preview-placeholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                img.src = e.target.result;
                img.classList.remove('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
