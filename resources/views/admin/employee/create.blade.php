@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Title --}}
    <h5 class="fw-bold mb-4" style="color:#1a1a2e; font-size:20px;">Create New</h5>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li style="font-size:13px;">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">

                {{-- Section Heading --}}
                <div class="d-flex align-items-center gap-2 mb-4">
                    <i class="fas fa-user" style="color:#aaa; font-size:15px;"></i>
                    <span style="font-size:14px; font-weight:600; color:#333;">User Information</span>
                </div>

                <div class="row g-0">

                    {{-- ── Left / Center Form Fields ── --}}
                    <div class="col-lg-8 pe-lg-4">
                        <div class="row g-3">

                            {{-- First Name & Last Name --}}
                            <div class="col-md-6">
                                <label class="emp-label">
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="first_name"
                                       class="form-control emp-input @error('first_name') is-invalid @enderror"
                                       placeholder="Enter Name"
                                       value="{{ old('first_name') }}">
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="emp-label">Last Name</label>
                                <input type="text"
                                       name="last_name"
                                       class="form-control emp-input @error('last_name') is-invalid @enderror"
                                       placeholder="Enter Name"
                                       value="{{ old('last_name') }}">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="col-12">
                                <label class="emp-label">
                                    Phone Number <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="phone"
                                       class="form-control emp-input @error('phone') is-invalid @enderror"
                                       placeholder="Enter phone number"
                                       value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Gender --}}
                            <div class="col-12">
                                <label class="emp-label">Gender</label>
                                <select name="gender"
                                        class="form-select emp-input @error('gender') is-invalid @enderror">
                                    <option value="Male"   {{ old('gender','Male')=='Male'   ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender')=='Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other"  {{ old('gender')=='Other'  ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-12">
                                <label class="emp-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       name="email"
                                       class="form-control emp-input @error('email') is-invalid @enderror"
                                       placeholder="Enter Email Address"
                                       value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password & Confirm --}}
                            <div class="col-md-6">
                                <label class="emp-label">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                       name="password"
                                       class="form-control emp-input @error('password') is-invalid @enderror"
                                       placeholder="Enter Password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="emp-label">
                                    Confirm Password <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                       name="password_confirmation"
                                       class="form-control emp-input"
                                       placeholder="Enter Confirm Password">
                            </div>

                        </div>
                    </div>

                    {{-- ── Right Column: Image + Role ── --}}
                    <div class="col-lg-4 mt-4 mt-lg-0">

                        {{-- Image Preview Box --}}
                        <div class="mb-3">
                            <div id="imgPreviewBox"
                                 onclick="document.getElementById('profile_image').click()"
                                 style="width:160px; height:160px;
                                        background:#e9ecef;
                                        border:1.5px dashed #ced4da;
                                        border-radius:10px;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        overflow:hidden;
                                        cursor:pointer;
                                        transition: border-color .2s;">
                                <span id="imgPlaceholder" style="font-size:13px; color:#adb5bd;">500 × 500</span>
                                <img id="imgPreview" src="" alt=""
                                     style="display:none; width:100%; height:100%; object-fit:cover;">
                            </div>
                        </div>

                        {{-- Profile Upload --}}
                        <div class="mb-3">
                            <label class="emp-label">
                                User profile
                                <span style="font-size:11px; color:#888;">(Ratio 1:1)</span>
                            </label>
                            <input type="file"
                                   name="profile_image"
                                   id="profile_image"
                                   class="form-control emp-input @error('profile_image') is-invalid @enderror"
                                   accept="image/*"
                                   onchange="previewImg(this)">
                            @error('profile_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div class="mb-3">
                            <label class="emp-label">
                                Role <span class="text-danger">*</span>
                            </label>
                            <select name="role_id"
                                    id="role_id_select"
                                    class="form-select emp-input @error('role_id') is-invalid @enderror"
                                    onchange="syncRoleName(this)">
                                <option value="">Select Role</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->id }}"
                                            data-name="{{ $r->name }}"
                                            {{ old('role_id') == $r->id ? 'selected' : '' }}>
                                        {{ ucfirst($r->name) }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- hidden: stores the role name string --}}
                            <input type="hidden" name="role" id="role_name_hidden"
                                   value="{{ old('role', 'admin') }}">
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mt-3">
            <button type="submit" class="btn-submit-emp">Submit</button>
        </div>

    </form>
</div>

<style>
.emp-label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #333;
    margin-bottom: 6px;
}
.emp-input {
    font-size: 14px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 9px 14px;
    color: #333;
    background: #fff;
    transition: border-color .2s, box-shadow .2s;
}
.emp-input:focus {
    border-color: #e91e63;
    box-shadow: 0 0 0 3px rgba(233,30,99,.1);
    outline: none;
}
.btn-submit-emp {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 32px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 3px 10px rgba(233,30,99,.35);
}
.btn-submit-emp:hover {
    background: linear-gradient(135deg, #c2185b, #ad1457);
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(233,30,99,.4);
}

/* Preview box hover */
#imgPreviewBox:hover { border-color: #e91e63 !important; }
</style>

<script>
function previewImg(input) {
    const box         = document.getElementById('imgPreviewBox');
    const placeholder = document.getElementById('imgPlaceholder');
    const preview     = document.getElementById('imgPreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src          = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function syncRoleName(select) {
    const opt  = select.options[select.selectedIndex];
    const name = opt.dataset.name || 'admin';
    document.getElementById('role_name_hidden').value = name;
}

// On load: if old role_id is selected, sync hidden input
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('role_id_select');
    if (sel.value) syncRoleName(sel);
});
</script>
@endsection
