@extends('admin.master')
@section('title', 'Gallery Import')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Bulk Gallery Imports</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <i class="ri-gallery-upload-line fs-4 text-primary me-2"></i>
                            <h5 class="card-title mb-0">Upload Gallery Images</h5>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success border-0 shadow-sm mb-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('seller.import-export.gallery-import.submit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Select Multiple Images</label>
                                <div class="p-5 border border-dashed text-center" style="border-radius: 10px; background-color: #f8fafc; border-color: #cbd5e1 !important;">
                                    <i class="ri-image-add-line fs-1 text-muted mb-3 d-block"></i>
                                    <input type="file" name="images[]" class="form-control mb-2" multiple required>
                                    <p class="text-muted small mb-0">Drag & drop or click to upload. Support: JPG, PNG, WEBP.</p>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="reset" class="btn btn-secondary px-4 me-2 border-0" style="background-color: #64748b;">Reset</button>
                                <button type="submit" class="btn btn-danger px-4 border-0" style="background-color: #f43f5e;">
                                    <i class="ri-upload-cloud-line me-1"></i> Upload Gallery
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
