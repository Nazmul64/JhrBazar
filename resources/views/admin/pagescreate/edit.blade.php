@extends('admin.master')

@section('content')

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Page Edit</h4>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-primary rounded-pill px-4">
            Manage
        </a>
    </div>

    <!-- Form Card -->
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

            <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Name & Title Row -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $page->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="title" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $page->title) }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="8">{{ old('description', $page->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status Toggle -->
                <div class="mb-4">
                    <label class="form-label fw-semibold d-block">Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status"
                               name="status" value="1"
                               {{ old('status', $page->status) == 1 ? 'checked' : '' }}
                               style="width:50px; height:26px; cursor:pointer;">
                    </div>
                </div>

                <!-- Submit -->
                <div>
                    <button type="submit" class="btn btn-success px-4">Update</button>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary px-4 ms-2">Cancel</a>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
     Initialize rich text editor if needed
     $('#description').summernote({ height: 200 });
     tinymce.init({ selector: '#description', height: 200 });
</script>


@endsection
