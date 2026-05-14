@extends('admin.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Privacy Policy</h4>
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

            <form action="{{ route('admin.privacy.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $policy->title ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="summernote" class="form-control" rows="15">{{ old('content', $policy->content ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5 class="mb-3">SEO Settings</h5>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $policy->meta_title ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="4">{{ old('meta_description', $policy->meta_description ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Meta Keywords</label>
                            <textarea name="meta_keywords" class="form-control" rows="3">{{ old('meta_keywords', $policy->meta_keywords ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-5 rounded-pill">Update Privacy Policy</button>
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
            placeholder: 'Enter privacy policy details...'
        });
    });
</script>
@endpush
@endsection
