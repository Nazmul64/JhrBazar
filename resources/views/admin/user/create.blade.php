@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    <h5 class="fw-bold mb-4" style="color:#1a1a2e; font-size:20px;">Add New User</h5>

    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">

                <div class="row g-4">
                    {{-- Basic Info --}}
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter Full Name" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Enter Phone Number" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter Email Address" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter Password" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                            </div>

                            {{-- Dynamic Type & Role --}}
                            <div class="col-md-6">
                                <label class="form-label">User Type <span class="text-danger">*</span></label>
                                <select name="user_type" id="user_type_select" class="form-select" required onchange="filterRoles()">
                                    <option value="">-- Select Type --</option>
                                    @foreach($userTypes as $type)
                                        <option value="{{ $type }}" {{ old('user_type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Assign Role <span class="text-danger">*</span></label>
                                <select name="role_id" id="role_id_select" class="form-select @error('role_id') is-invalid @enderror" required>
                                    <option value="">-- Select Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" 
                                                data-type="{{ $role->user_type }}"
                                                {{ old('role_id') == $role->id ? 'selected' : '' }}
                                                style="display: none;">
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Image Upload --}}
                    <div class="col-lg-4">
                        <div class="text-center">
                            <div id="previewContainer" class="mb-3" style="width: 200px; height: 200px; border: 2px dashed #ddd; border-radius: 12px; margin: 0 auto; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f8f9fa; cursor: pointer;" onclick="document.getElementById('profile_image').click()">
                                <img id="imagePreview" src="" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                <div id="previewPlaceholder" class="text-muted">
                                    <i class="fas fa-camera fa-2x mb-2"></i>
                                    <div style="font-size: 12px;">Upload Profile</div>
                                </div>
                            </div>
                            <input type="file" name="profile_image" id="profile_image" class="d-none" accept="image/*" onchange="previewImage(this)">
                            @error('profile_image') <div class="text-danger mt-2" style="font-size: 12px;">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5" style="background: #4f46e5; border: none;">Create User</button>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    function filterRoles() {
        const type = document.getElementById('user_type_select').value;
        const roleSelect = document.getElementById('role_id_select');
        const options = roleSelect.querySelectorAll('option');

        let firstVisible = false;
        options.forEach(opt => {
            if (opt.value === "") {
                opt.style.display = "block";
                return;
            }
            if (opt.dataset.type === type) {
                opt.style.display = "block";
                if(!firstVisible) {
                    // roleSelect.value = opt.value; // Don't auto-select to avoid confusion
                    firstVisible = true;
                }
            } else {
                opt.style.display = "none";
            }
        });
        
        // Reset role select if current value is hidden
        if (roleSelect.selectedOptions[0] && roleSelect.selectedOptions[0].style.display === 'none') {
            roleSelect.value = "";
        }
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('imagePreview');
                const placeholder = document.getElementById('previewPlaceholder');
                img.src = e.target.result;
                img.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Initial filter if old input exists
    document.addEventListener('DOMContentLoaded', filterRoles);
</script>

<style>
    .form-label { font-weight: 600; color: #4b5563; font-size: 14px; }
    .form-control, .form-select { border-radius: 8px; padding: 10px 15px; border-color: #e5e7eb; }
    .form-control:focus, .form-select:focus { box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); border-color: #4f46e5; }
</style>
@endsection
