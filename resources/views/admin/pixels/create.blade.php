@extends('admin.master')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0">Pixels Create</h4>
    <a href="{{ route('admin.pixels.index') }}" class="btn btn-primary rounded-pill px-4">Manage</a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('admin.pixels.store') }}" method="POST">
            @csrf

            {{-- Pixels ID --}}
            <div class="mb-4">
                <label for="pixels_id" class="form-label fw-semibold">
                    Pixels ID <span class="text-danger">*</span>
                </label>
                <input
                    type="text"
                    id="pixels_id"
                    name="pixels_id"
                    class="form-control @error('pixels_id') is-invalid @enderror"
                    value="{{ old('pixels_id') }}"
                    autocomplete="off"
                >
                @error('pixels_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status --}}
            <div class="mb-4">
                <label class="form-label fw-semibold d-block">Status</label>
                <div class="form-check form-switch">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="status"
                        name="status"
                        role="switch"
                        style="width:3rem;height:1.6rem;cursor:pointer;"
                        {{ old('status', '1') ? 'checked' : '' }}
                    >
                </div>
            </div>

            {{-- Submit --}}
            <div class="mb-0">
                <button type="submit" class="btn btn-success px-4">Submit</button>
            </div>
        </form>
    </div>
</div>

@endsection
