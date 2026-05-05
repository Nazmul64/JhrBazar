@extends('admin.master')
@section('content')

<div class="container-fluid py-4">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0 text-primary">
            <i class="fas fa-user-edit me-2"></i> Update Staff Profile
        </h4>
        <a href="{{ route('admin.profile.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Profile
        </a>
    </div>

    {{-- ── Validation Errors ────────────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle me-2"></i> Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- USER INFORMATION --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-4">
                    <i class="fas fa-id-badge me-2 text-primary"></i> Personal Information
                </h6>

                @php
                    $nameParts = explode(' ', $user->name, 2);
                    $firstName = $nameParts[0] ?? '';
                    $lastName  = $nameParts[1] ?? '';
                @endphp

                <div class="row g-3">
                    {{-- Profile Image --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Profile Photo</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle overflow-hidden border shadow-sm flex-shrink-0"
                                 style="width:100px;height:100px;">
                                <img id="profilePreview"
                                     src="{{ $user->profile_image_url }}"
                                     alt="{{ $user->name }}"
                                     class="w-100 h-100" style="object-fit:cover;">
                            </div>
                            <div class="flex-grow-1">
                                <input type="file"
                                       name="profile_image"
                                       id="profileImageInput"
                                       class="form-control @error('profile_image') is-invalid @enderror"
                                       accept="image/*"
                                       onchange="previewImage(this,'profilePreview')">
                                <small class="text-muted">Max size 2MB (JPG, PNG, WebP)</small>
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- First Name --}}
                    <div class="col-md-6">
                        <label for="first_name" class="form-label fw-semibold">First Name</label>
                        <input type="text" name="first_name" id="first_name"
                               class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name', $firstName) }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Last Name --}}
                    <div class="col-md-6">
                        <label for="last_name" class="form-label fw-semibold">Last Name</label>
                        <input type="text" name="last_name" id="last_name"
                               class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name', $lastName) }}">
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" id="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Gender --}}
                    <div class="col-md-6">
                        <label for="gender" class="form-label fw-semibold">Gender</label>
                        <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">Select Gender</option>
                            @foreach(['Male','Female','Other'] as $g)
                                <option value="{{ $g }}" {{ old('gender', $user->gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Email --}}
                    <div class="col-12">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.profile.index') }}" class="btn btn-light px-4 border">Cancel</a>
            <button type="submit" class="btn btn-primary px-5 fw-bold">Save Changes</button>
        </div>
    </form>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) { preview.src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection
