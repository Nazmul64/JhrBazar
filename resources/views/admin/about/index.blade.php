@extends('admin.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">About Company</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.about.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $about->title ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="summernote" class="form-control" rows="15">{{ old('content', $about->content ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Company Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            @if(isset($about->image))
                                <div class="mt-2">
                                    <img src="{{ asset($about->image) }}" alt="About Company" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                        </div>
                        
                        <hr>
                        <h5 class="mb-3 mt-4">SEO Settings</h5>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $about->meta_title ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $about->meta_description ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Meta Keywords</label>
                            <textarea name="meta_keywords" class="form-control" rows="2">{{ old('meta_keywords', $about->meta_keywords ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-5 rounded-pill">Update About Company</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            height: 400,
            placeholder: 'Enter about company details...'
        });
    });
</script>
@endpush
@endsection
