@extends('admin.master')
@section('content')

<div class="container-fluid py-4">

    {{-- ── Alerts ──────────────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-4 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-store me-2 text-danger"></i> Seller Profile
        </h4>
        <a href="{{ route('seller.profile.edit') }}" class="btn btn-danger btn-sm px-4 shadow-sm">
            <i class="fas fa-edit me-1"></i> Edit Shop & Profile
        </a>
    </div>

    <div class="row g-4">

        {{-- ── LEFT COLUMN ─────────────────────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Banner Card --}}
            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <div class="position-relative" style="height: 240px; background: #f8f9fa;">
                    @if($shop && $shop->banner)
                        <img src="{{ asset($shop->banner) }}"
                             alt="Shop Banner"
                             class="w-100 h-100"
                             style="object-fit: cover;">
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                            <i class="fas fa-image fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No Shop Banner Uploaded</p>
                            <small>Upload one in "Edit Profile" to attract more customers!</small>
                        </div>
                    @endif
                </div>

                {{-- Shop Logo + Info Overlay --}}
                <div class="card-body position-relative pt-0">
                    <div class="d-flex align-items-end gap-4" style="margin-top: -45px;">
                        {{-- Logo --}}
                        <div class="flex-shrink-0" style="z-index: 10;">
                            <div class="rounded-circle border border-4 border-white shadow-sm overflow-hidden bg-white"
                                 style="width: 100px; height: 100px;">
                                @if($shop && $shop->logo)
                                    <img src="{{ asset($shop->logo) }}"
                                         alt="Shop Logo"
                                         class="w-100 h-100"
                                         style="object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                                        <i class="fas fa-store fa-2x"></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Name & Rating --}}
                        <div class="pb-2 flex-grow-1">
                            <h5 class="fw-bold mb-1 text-dark">{{ $shop->name ?? 'Your Shop Name' }}</h5>
                            <div class="d-flex align-items-center gap-2">
                                <div class="text-warning small">
                                    @php
                                        $rating = $shop->avg_rating ?? 0;
                                        $fullStars = floor($rating);
                                        $halfStar = ($rating - $fullStars) >= 0.5;
                                    @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $fullStars)
                                            <i class="fas fa-star"></i>
                                        @elseif($i == $fullStars + 1 && $halfStar)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-muted small">({{ number_format($rating, 1) }})</span>
                                <span class="badge bg-soft-success text-success border border-success-subtle ms-2">Active Seller</span>
                            </div>
                        </div>
                        
                        <div class="pb-2">
                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-external-link-alt me-1"></i> Visit Shop
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Shop Details Table --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">
                            <i class="fas fa-info-circle me-2 text-danger"></i> Shop Information
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <tbody>
                                <tr>
                                    <td class="text-muted ps-4" style="width: 220px;">Shop Name</td>
                                    <td class="fw-semibold">{{ $shop->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-4">Business Address</td>
                                    <td>{{ $shop->address ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-4">Location</td>
                                    <td>
                                        <span class="text-dark">{{ $shop->division ?? '—' }}</span>
                                        @if($shop->district)
                                            <i class="fas fa-chevron-right mx-2 text-muted" style="font-size: 10px;"></i>
                                            <span class="text-dark">{{ $shop->district }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-4">Business Hours</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="far fa-clock me-1 text-danger"></i>
                                            {{ $shop->opening_time ? \Carbon\Carbon::parse($shop->opening_time)->format('h:i A') : '—' }} 
                                            to 
                                            {{ $shop->closing_time ? \Carbon\Carbon::parse($shop->closing_time)->format('h:i A') : '—' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-4">Min. Order Amount</td>
                                    <td class="fw-bold text-danger">৳ {{ number_format($shop->min_order_amount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-4">Estimated Delivery</td>
                                    <td>{{ $shop->estimated_delivery ?? '—' }} {{ Str::plural('Day', $shop->estimated_delivery) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-4">Order ID Prefix</td>
                                    <td><span class="badge bg-secondary px-3">{{ $shop->order_prefix ?? 'RC' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-4 pb-4">Shop Description</td>
                                    <td class="pb-4 text-muted small">{{ $shop->description ?? 'No description provided.' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>{{-- /col-lg-8 --}}

        {{-- ── RIGHT COLUMN ─────────────────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Performance Stats --}}
            <div class="card border-0 shadow-sm mb-4 bg-danger text-white">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 opacity-75">Quick Overview</h6>
                    <div class="row g-3">
                        <div class="col-6 border-end border-white border-opacity-25">
                            <div class="small opacity-75">Total Sales</div>
                            <div class="fs-4 fw-bold">0</div>
                        </div>
                        <div class="col-6">
                            <div class="small opacity-75">Active Products</div>
                            <div class="fs-4 fw-bold">{{ $totalProducts }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Personal Profile Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    <div class="position-relative d-inline-block mb-3">
                        <div class="rounded-circle border border-4 border-light shadow-sm overflow-hidden bg-white"
                             style="width: 110px; height: 110px;">
                            <img src="{{ $user->profile_image_url }}"
                                 alt="{{ $user->name }}"
                                 class="w-100 h-100"
                                 style="object-fit: cover;">
                        </div>
                        <div class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle"
                             style="width: 18px; height: 18px;" title="Online"></div>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small mb-3">{{ $user->email }}</p>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-soft-primary text-primary px-3 text-capitalize">Seller</span>
                        <span class="badge bg-soft-info text-info px-3">Joined {{ $user->created_at ? $user->created_at->format('M Y') : '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Change Password Card --}}
            <div class="card border-0 shadow-sm" id="change-password">
                <div class="card-body">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-shield-alt me-2 text-danger"></i> Security Settings
                    </h6>
                    <form action="{{ route('seller.profile.change-password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Current Password</label>
                            <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">New Password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="New password" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" placeholder="Confirm" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 fw-bold">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>

        </div>{{-- /col-lg-4 --}}

    </div>
</div>

<style>
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
</style>

@endsection
