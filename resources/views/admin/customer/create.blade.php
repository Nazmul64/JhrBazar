{{-- resources/views/admin/customer/create.blade.php --}}
@extends('admin.master')
@section('content')
<style>
  .page-card { background:#fff; border-radius:12px; padding:32px 36px; box-shadow:0 1px 8px rgba(0,0,0,.06); }
  .page-card h4 { font-size:1.2rem; font-weight:700; color:#1a1a1a; margin-bottom:24px; }
  .sep { border:none; border-top:1.5px solid #f0f0f0; margin-bottom:28px; }

  .form-label { font-size:.8rem; font-weight:500; color:#444; margin-bottom:6px; display:block; }
  .form-label .req { color:#e8194b; }
  .form-control {
    width:100%; padding:11px 14px; border:1.5px solid #dde; border-radius:9px;
    font-size:.85rem; font-family:inherit; background:#fafafa;
    transition:border-color .2s; box-sizing:border-box;
  }
  .form-control:focus { border-color:#e8194b; outline:none; background:#fff;
                        box-shadow:0 0 0 3px rgba(232,25,75,.08); }
  .form-control.is-invalid { border-color:#e8194b !important; }
  .error-text { font-size:.75rem; color:#e8194b; margin-top:4px; }

  .row { display:flex; gap:20px; margin-bottom:18px; }
  .col { flex:1; }

  /* Image preview */
  .img-preview-wrap { text-align:center; }
  .img-preview-box {
    width:160px; height:160px; border-radius:12px; border:2px dashed #dde;
    background:#f8f8f8; display:flex; align-items:center; justify-content:center;
    overflow:hidden; margin:0 auto 10px;
  }
  .img-preview-box img { width:100%; height:100%; object-fit:cover; }
  .img-preview-box .placeholder-text { font-size:.8rem; color:#bbb; }
  .img-upload-label { font-size:.78rem; font-weight:500; color:#444; margin-bottom:6px; display:block; }

  /* Buttons */
  .btn-row { display:flex; justify-content:space-between; align-items:center; margin-top:10px; }
  .btn-cancel { padding:10px 26px; border-radius:9px; border:1.5px solid #dde;
                background:#fff; font-size:.88rem; font-weight:500; cursor:pointer;
                text-decoration:none; color:#555; }
  .btn-submit { padding:10px 30px; border-radius:9px; border:none;
                background:linear-gradient(135deg,#e8194b,#b8002e); color:#fff;
                font-size:.88rem; font-weight:600; cursor:pointer;
                box-shadow:0 4px 14px rgba(232,25,75,.3);
                transition:transform .15s; }
  .btn-submit:hover { transform:translateY(-1px); }
</style>

<div class="page-card">
  <h4>Add Customer</h4>
  <hr class="sep"/>

  <form action="{{ route('admin.customers.store') }}" method="POST"
        enctype="multipart/form-data">
    @csrf

    <div style="display:flex; gap:32px;">

      {{-- Left: form fields --}}
      <div style="flex:1;">

        {{-- First & Last name --}}
        <div class="row">
          <div class="col">
            <label class="form-label">First Name <span class="req">*</span></label>
            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                   placeholder="Enter Name" value="{{ old('first_name') }}"/>
            @error('first_name') <div class="error-text">{{ $message }}</div> @enderror
          </div>
          <div class="col">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                   placeholder="Enter Name" value="{{ old('last_name') }}"/>
            @error('last_name') <div class="error-text">{{ $message }}</div> @enderror
          </div>
        </div>

        {{-- Phone --}}
        <div style="margin-bottom:18px;">
          <label class="form-label">Phone Number <span class="req">*</span></label>
          <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                 placeholder="Enter phone number" value="{{ old('phone') }}"/>
          @error('phone') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        {{-- Email --}}
        <div style="margin-bottom:18px;">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 placeholder="Enter Email Address" value="{{ old('email') }}"/>
          @error('email') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        {{-- Password --}}
        <div class="row">
          <div class="col">
            <label class="form-label">Password <span class="req">*</span></label>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Enter Password"/>
            @error('password') <div class="error-text">{{ $message }}</div> @enderror
          </div>
          <div class="col">
            <label class="form-label">Confirm Password <span class="req">*</span></label>
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="Enter Confirm Password"/>
          </div>
        </div>

        {{-- Gender --}}
        <div style="margin-bottom:18px;">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-control">
            <option value="">-- Select --</option>
            <option value="male"   {{ old('gender')=='male'   ? 'selected':'' }}>Male</option>
            <option value="female" {{ old('gender')=='female' ? 'selected':'' }}>Female</option>
            <option value="other"  {{ old('gender')=='other'  ? 'selected':'' }}>Other</option>
          </select>
        </div>

      </div>

      {{-- Right: image + DOB --}}
      <div style="width:220px; flex-shrink:0;">

        {{-- Image preview --}}
        <div class="img-preview-wrap">
          <div class="img-preview-box" id="previewBox">
            <span class="placeholder-text">500 × 500</span>
          </div>
          <span class="img-upload-label">User profile (Ratio 1:1)</span>
          <input type="file" name="profile_image" id="profileImageInput"
                 accept="image/*" class="form-control"
                 onchange="previewImage(event)"/>
          @error('profile_image') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        {{-- Date of Birth --}}
        <div style="margin-top:20px;">
          <label class="form-label">Date of Birth</label>
          <input type="date" name="date_of_birth" class="form-control"
                 value="{{ old('date_of_birth') }}"/>
        </div>

      </div>
    </div>

    {{-- Buttons --}}
    <div class="btn-row">
      <a href="{{ route('admin.customers.index') }}" class="btn-cancel">Cancel</a>
      <button type="submit" class="btn-submit">Submit</button>
    </div>

  </form>
</div>

<script>
  function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
      const box = document.getElementById('previewBox');
      box.innerHTML = '<img src="' + e.target.result + '" />';
    };
    reader.readAsDataURL(file);
  }
</script>

@endsection
