@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-0 fw-bold">Edit Customer</h4>
                <a href="{{ route('seller.customers.index') }}" class="btn btn-outline-secondary">Back to List</a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('seller.customers.update', $customer->id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">First Name</label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $customer->first_name) }}" required>
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Last Name</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $customer->last_name) }}">
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $customer->user->phone ?? '') }}" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer->user->email ?? '') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12">
                                <hr>
                                <p class="text-muted small">Leave password fields blank if you don't want to change it.</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">New Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('password')"><i class="bi bi-eye"></i></button>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('password_confirmation')"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-5" style="background: #e7567c; border:none;">Update Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePass(id) {
    const el = document.getElementById(id);
    const btn = el.nextElementSibling;
    const icon = btn.querySelector('i');
    if(el.type === 'password') {
        el.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        el.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endsection
