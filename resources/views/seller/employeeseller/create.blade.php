@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="sup-form-wrap">

        <div class="sup-form-header">
            <h5 class="fw-bold mb-0" style="font-size:20px; color:#1a1a2e;">
                <i class="bi bi-person-plus-fill me-2 text-danger"></i>Add Employee
            </h5>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mx-4 mt-4">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li style="font-size:13px;">{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('seller.employeeseller.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="sup-form-body">

                {{-- ══ User Information Section ══ --}}
                <div class="section-title mb-3">
                    <i class="bi bi-person-fill me-1"></i> User Information
                </div>

                {{-- Row 1: First Name + Last Name + Photo --}}
                <div class="row g-3 mb-3 align-items-start">
                    <div class="col-md-4">
                        <label class="sup-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name"
                               class="form-control sup-input @error('first_name') is-invalid @enderror"
                               placeholder="Enter Name"
                               value="{{ old('first_name') }}">
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="sup-label">Last Name</label>
                        <input type="text" name="last_name"
                               class="form-control sup-input @error('last_name') is-invalid @enderror"
                               placeholder="Enter Name"
                               value="{{ old('last_name') }}">
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        {{-- Profile Photo --}}
                        <label class="sup-label">
                            User profile <span style="color:#3b82f6; font-weight:600;">(Ratio 1:1)</span>
                        </label>
                        <div id="imgBox"
                             onclick="document.getElementById('profile_image').click()"
                             style="width:100px; height:100px; background:#e9ecef;
                                    border:1.5px dashed #ced4da; border-radius:10px;
                                    display:flex; align-items:center; justify-content:center;
                                    overflow:hidden; cursor:pointer;
                                    transition:border-color .2s;">
                            <span id="imgPlaceholder" style="font-size:11px; color:#adb5bd; text-align:center; padding:5px;">
                                500 × 500
                            </span>
                            <img id="imgPreview" src="" alt=""
                                 style="display:none; width:100%; height:100%; object-fit:cover;">
                        </div>
                        <input type="file" name="profile_image" id="profile_image"
                               class="form-control mt-2 @error('profile_image') is-invalid @enderror"
                               accept="image/jpg,image/jpeg,image/png"
                               onchange="previewImg(this)">
                        @error('profile_image')
                            <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Row 2: Phone + Gender --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="sup-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone"
                               class="form-control sup-input @error('phone') is-invalid @enderror"
                               placeholder="Enter phone number"
                               value="{{ old('phone') }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="sup-label">Gender</label>
                        <select name="gender" class="form-select sup-input @error('gender') is-invalid @enderror">
                            <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other"  {{ old('gender') == 'other'  ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Row 3: Email + Role --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="sup-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control sup-input @error('email') is-invalid @enderror"
                               placeholder="Enter Email Address"
                               value="{{ old('email') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="sup-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select sup-input @error('role') is-invalid @enderror">
                            <option value="employee"  {{ old('role') == 'employee'  ? 'selected' : '' }}>Employee</option>
                            <option value="manager"   {{ old('role') == 'manager'   ? 'selected' : '' }}>Manager</option>
                            <option value="cashier"   {{ old('role') == 'cashier'   ? 'selected' : '' }}>Cashier</option>
                            <option value="accountant"{{ old('role') == 'accountant'? 'selected' : '' }}>Accountant</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Row 4: Password + Confirm Password --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="sup-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password"
                               class="form-control sup-input @error('password') is-invalid @enderror"
                               placeholder="Enter Password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="sup-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation"
                               class="form-control sup-input"
                               placeholder="Enter Confirm Password">
                    </div>
                </div>

                {{-- Address --}}
                <div class="mb-4">
                    <label class="sup-label">Address</label>
                    <input type="text" name="address"
                           class="form-control sup-input @error('address') is-invalid @enderror"
                           placeholder="Enter Address"
                           value="{{ old('address') }}">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>

            {{-- Footer Buttons --}}
            <div class="sup-form-footer">
                <a href="{{ route('seller.employeeseller.index') }}" class="btn-sup-cancel">Cancel</a>
                <button type="submit" class="btn-sup-submit">Submit</button>
            </div>

        </form>
    </div>

</div>

<style>
.sup-form-wrap {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    overflow: hidden;
}
.sup-form-header {
    padding: 20px 28px;
    border-bottom: 1px solid #f0f0f0;
}
.sup-form-body { padding: 28px 28px 8px; }
.sup-form-footer {
    padding: 18px 28px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.section-title {
    font-size: 13px;
    font-weight: 600;
    color: #555;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 8px;
}
.sup-label {
    display: block; font-size: 13px; font-weight: 500;
    color: #333; margin-bottom: 6px;
}
.sup-input {
    font-size: 14px; border: 1px solid #e2e8f0; border-radius: 8px;
    padding: 9px 14px; color: #333; background: #fff;
    transition: border-color .2s, box-shadow .2s;
}
.sup-input:focus {
    border-color: #e91e63;
    box-shadow: 0 0 0 3px rgba(233,30,99,.1);
    outline: none;
}
.btn-sup-cancel {
    background: #fff; color: #555; border: 1.5px solid #e0e0e0;
    border-radius: 8px; padding: 9px 28px; font-size: 14px;
    font-weight: 500; text-decoration: none; transition: all .2s;
    display: inline-flex; align-items: center;
}
.btn-sup-cancel:hover { background: #f5f5f5; color: #333; }
.btn-sup-submit {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff; border: none; border-radius: 8px;
    padding: 10px 36px; font-size: 15px; font-weight: 500;
    cursor: pointer; transition: all .2s;
    box-shadow: 0 3px 10px rgba(233,30,99,.35);
}
.btn-sup-submit:hover {
    background: linear-gradient(135deg, #c2185b, #ad1457);
    transform: translateY(-1px);
}
#imgBox:hover { border-color: #e91e63 !important; }
</style>

<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imgPreview').src = e.target.result;
            document.getElementById('imgPreview').style.display = 'block';
            document.getElementById('imgPlaceholder').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
