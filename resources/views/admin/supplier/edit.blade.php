@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="sup-form-wrap">

        <div class="sup-form-header">
            <h5 class="fw-bold mb-0" style="font-size:20px; color:#1a1a2e;">Edit Supplier</h5>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mx-4 mt-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

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

        <form action="{{ route('admin.supplier.update', $supplier->id) }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="sup-form-body">

                {{-- Profile Photo --}}
                <div class="text-center mb-4">
                    <p class="sup-label mb-2">
                        Profile Photo
                        <span style="color:#3b82f6; font-weight:600;">Ratio 1:1 (500 × 500 px)</span>
                    </p>
                    <div id="imgBox"
                         onclick="document.getElementById('profile_image').click()"
                         style="width:230px; height:230px; background:#e9ecef;
                                border:1.5px dashed #ced4da; border-radius:10px;
                                overflow:hidden; cursor:pointer; margin:0 auto;
                                position:relative; transition:border-color .2s;">
                        @if($supplier->profile_image)
                            <img id="imgPreview" src="{{ asset($supplier->profile_image) }}"
                                 alt="Profile" style="width:100%; height:100%; object-fit:cover;">
                            <span id="imgPlaceholder" style="display:none;"></span>
                        @else
                            <div style="width:100%; height:100%; display:flex;
                                        align-items:center; justify-content:center;">
                                <span id="imgPlaceholder"
                                      style="font-size:22px; color:#adb5bd; font-weight:300;">
                                    500 × 500
                                </span>
                            </div>
                            <img id="imgPreview" src="" alt=""
                                 style="display:none; width:100%; height:100%;
                                        object-fit:cover; position:absolute; top:0; left:0;">
                        @endif
                    </div>
                    <p class="text-muted mt-2 mb-0" style="font-size:12px;">
                        Supported formats: jpg, jpeg, png
                    </p>
                    <input type="file" name="profile_image" id="profile_image"
                           class="d-none @error('profile_image') is-invalid @enderror"
                           accept="image/jpg,image/jpeg,image/png"
                           onchange="previewImg(this)">
                    @error('profile_image')
                        <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4" style="border-color:#f0f0f0;">

                {{-- Full Name — user থেকে --}}
                <div class="mb-3">
                    <label class="sup-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                           class="form-control sup-input @error('name') is-invalid @enderror"
                           placeholder="Enter Name"
                           value="{{ old('name', $supplier->user->name) }}">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Phone & Email — user থেকে --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="sup-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone"
                               class="form-control sup-input @error('phone') is-invalid @enderror"
                               placeholder="Enter phone number"
                               value="{{ old('phone', $supplier->user->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="sup-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control sup-input @error('email') is-invalid @enderror"
                               placeholder="Enter Email Address"
                               value="{{ old('email', $supplier->user->email) }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Address — supplier থেকে --}}
                <div class="mb-4">
                    <label class="sup-label">Address</label>
                    <input type="text" name="address"
                           class="form-control sup-input @error('address') is-invalid @enderror"
                           placeholder="Enter Address"
                           value="{{ old('address', $supplier->address) }}">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>

            {{-- Footer Buttons --}}
            <div class="sup-form-footer">
                <a href="{{ route('admin.supplier.index') }}" class="btn-sup-cancel">Cancel</a>
                <button type="submit" class="btn-sup-submit">Update</button>
            </div>

        </form>
    </div>

</div>

<style>
.sup-form-wrap {
    max-width: 780px; margin: 0 auto; background: #fff;
    border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden;
}
.sup-form-header { padding: 20px 28px; border-bottom: 1px solid #f0f0f0; }
.sup-form-body { padding: 28px 28px 8px; }
.sup-form-footer {
    padding: 18px 28px; border-top: 1px solid #f0f0f0;
    display: flex; justify-content: space-between; align-items: center;
}
.sup-label { display:block; font-size:13px; font-weight:500; color:#333; margin-bottom:6px; }
.sup-input {
    font-size:14px; border:1px solid #e2e8f0; border-radius:8px;
    padding:9px 14px; color:#333; background:#fff;
    transition: border-color .2s, box-shadow .2s;
}
.sup-input:focus { border-color:#e91e63; box-shadow:0 0 0 3px rgba(233,30,99,.1); outline:none; }
.btn-sup-cancel {
    background:#fff; color:#555; border:1.5px solid #e0e0e0; border-radius:8px;
    padding:9px 28px; font-size:14px; font-weight:500; text-decoration:none;
    transition:all .2s; display:inline-flex; align-items:center;
}
.btn-sup-cancel:hover { background:#f5f5f5; color:#333; }
.btn-sup-submit {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color:#fff; border:none; border-radius:8px; padding:10px 36px;
    font-size:15px; font-weight:500; cursor:pointer; transition:all .2s;
    box-shadow:0 3px 10px rgba(233,30,99,.35);
}
.btn-sup-submit:hover {
    background: linear-gradient(135deg, #c2185b, #ad1457); transform:translateY(-1px);
}
#imgBox:hover { border-color: #e91e63 !important; }
</style>

<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview     = document.getElementById('imgPreview');
            const placeholder = document.getElementById('imgPlaceholder');
            preview.src           = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
