@extends('admin.master')
@section('content')

<div class="container-fluid py-4">

    {{-- ── Alerts ──────────────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0 text-primary">
            <i class="fas fa-user-badge me-2"></i> Staff Profile
        </h4>
        <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary btn-sm px-4">
            <i class="fas fa-user-edit me-1"></i> Update Profile
        </a>
    </div>

    <div class="row g-4">

        {{-- ── LEFT COLUMN ─────────────────────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- User Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-id-card me-2 text-primary"></i> Personal Details
                    </h6>
                    <table class="table table-borderless mb-0 align-middle">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 180px;">Full Name</td>
                                <td class="fw-semibold">{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Phone Number</td>
                                <td>{{ $user->phone ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Official Email</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Gender</td>
                                <td>{{ $user->gender ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Job Designation</td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary border border-primary px-3 text-capitalize">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Joined Since</td>
                                <td>{{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Performance or Tasks (Placeholder) --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body py-5 text-center">
                    <i class="fas fa-tasks fa-3x text-muted mb-3 opacity-25"></i>
                    <p class="text-muted mb-0">System performance metrics and assigned tasks will appear here.</p>
                </div>
            </div>

        </div>{{-- /col-lg-8 --}}

        {{-- ── RIGHT COLUMN ─────────────────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Profile Photo Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    <div class="mx-auto rounded-circle overflow-hidden border border-4 border-light shadow-sm mb-3"
                         style="width: 120px; height: 120px;">
                        <img src="{{ $user->profile_image_url }}"
                             alt="{{ $user->name }}"
                             class="w-100 h-100"
                             style="object-fit: cover;">
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small mb-0 text-capitalize">{{ $user->role }}</p>
                </div>
            </div>

            {{-- Change Password Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-key me-2 text-primary"></i> Change Security Password
                    </h6>
                    <form action="{{ route('admin.profile.change-password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Current Password</label>
                            <input type="password" name="current_password" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">New Password</label>
                            <input type="password" name="new_password" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Confirm Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control form-control-sm" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                            Update My Password
                        </button>
                    </form>
                </div>
            </div>

        </div>{{-- /col-lg-4 --}}

    </div>{{-- /row --}}
</div>

<style>
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
</style>

@endsection
