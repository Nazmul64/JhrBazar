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
        <h4 class="fw-bold mb-0">
            <i class="fas fa-user-circle me-2 text-danger"></i> Profile Details
        </h4>
        <a href="{{ route('admin.profile.edit') }}" class="btn btn-danger btn-sm px-4">
            <i class="fas fa-edit me-1"></i> Edit Profile
        </a>
    </div>

    <div class="row g-4">

        {{-- ── LEFT COLUMN ─────────────────────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Banner Card --}}
            <div class="card border-0 shadow-sm overflow-hidden mb-4">

                {{-- Banner Image --}}
                <div class="position-relative" style="height: 220px; background: #f0f0f0;">
                    @if($shop && $shop->banner)
                        <img src="{{ asset($shop->banner) }}"
                             alt="Shop Banner"
                             class="w-100 h-100"
                             style="object-fit: cover;">
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 bg-light text-muted">
                            <i class="fas fa-image fa-3x mb-2"></i>
                            <small>No banner uploaded</small>
                        </div>
                    @endif
                </div>

                {{-- Shop Logo + Name --}}
                <div class="card-body d-flex align-items-center gap-4 pt-2">

                    {{-- Logo (overlaps banner) --}}
                    <div class="flex-shrink-0" style="margin-top: -50px; z-index: 10;">
                        <div class="rounded-circle border border-3 border-white shadow overflow-hidden bg-white"
                             style="width: 90px; height: 90px;">
                            @if($shop && $shop->logo)
                                <img src="{{ asset($shop->logo) }}"
                                     alt="Shop Logo"
                                     class="w-100 h-100"
                                     style="object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                                    <i class="fas fa-store fa-lg"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Name & Rating --}}
                    <div class="pt-1">
                        <h5 class="fw-bold mb-1">{{ $shop->name ?? 'No Shop Yet' }}</h5>
                        <div class="text-warning mb-2">
                            @php
                                $rating     = $shop->avg_rating ?? 0;
                                $fullStars  = floor($rating);
                                $halfStar   = ($rating - $fullStars) >= 0.5;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $fullStars)
                                    <i class="fas fa-star"></i>
                                @elseif($i == $fullStars + 1 && $halfStar)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                            <span class="text-muted small ms-1">
                                {{ number_format($rating, 1) }} ({{ $totalReviews }} {{ Str::plural('Review', $totalReviews) }})
                            </span>
                        </div>
                        @if($shop)
                            <a href="#" class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i> View Live
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- User Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-user me-2 text-danger"></i> User Information
                    </h6>
                    <table class="table table-borderless mb-0 align-middle">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 180px;">Name</td>
                                <td class="fw-semibold">{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Phone</td>
                                <td>{{ $user->phone ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Gender</td>
                                <td>{{ $user->gender ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Role</td>
                                <td>
                                    <span class="badge bg-primary text-capitalize">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Shop Information --}}
            @if($shop)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">
                            <i class="fas fa-store me-2 text-danger"></i> Shop Information
                        </h6>
                        <table class="table table-borderless mb-0 align-middle">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 200px;">Shop Name</td>
                                    <td class="fw-semibold">{{ $shop->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Address</td>
                                    <td>{{ $shop->address ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Estimated Delivery</td>
                                    <td>{{ $shop->estimated_delivery }} {{ Str::plural('day', $shop->estimated_delivery) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Opening Time</td>
                                    <td>{{ $shop->opening_time ? \Carbon\Carbon::parse($shop->opening_time)->format('h:i A') : '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Closing Time</td>
                                    <td>{{ $shop->closing_time ? \Carbon\Carbon::parse($shop->closing_time)->format('h:i A') : '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Min. Order Amount</td>
                                    <td>{{ number_format($shop->minimum_order_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Order ID Prefix</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $shop->order_id_prefix }}</span>
                                    </td>
                                </tr>
                                @if($shop->latitude && $shop->longitude)
                                <tr>
                                    <td class="text-muted">Location</td>
                                    <td>
                                        <a href="https://www.google.com/maps?q={{ $shop->latitude }},{{ $shop->longitude }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-map-marker-alt me-1 text-danger"></i> View on Map
                                        </a>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Description</td>
                                    <td>{{ $shop->description ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-store-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">No shop created yet.</p>
                        <a href="{{ route('admin.profile.create') }}" class="btn btn-danger btn-sm px-4">
                            <i class="fas fa-plus me-1"></i> Create Shop
                        </a>
                    </div>
                </div>
            @endif

        </div>{{-- /col-lg-8 --}}

        {{-- ── RIGHT COLUMN ─────────────────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Stats Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-chart-bar me-2 text-danger"></i> Statistics
                    </h6>

                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="text-muted">
                            <i class="fas fa-box me-2 text-primary"></i> Total Products
                        </span>
                        <span class="fw-bold fs-5">{{ $totalProducts }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="text-muted">
                            <i class="fas fa-shopping-bag me-2 text-success"></i> Total Orders
                        </span>
                        <span class="fw-bold fs-5">{{ $totalOrders }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <span class="text-muted">
                            <i class="fas fa-star me-2 text-warning"></i> Reviews
                        </span>
                        <span class="fw-bold fs-5">{{ $totalReviews }}</span>
                    </div>
                </div>
            </div>

            {{-- Profile Photo Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-id-card me-2 text-danger"></i> Profile Photo
                    </h6>
                    <div class="mx-auto rounded-circle overflow-hidden border border-2 border-light shadow-sm mb-2"
                         style="width: 100px; height: 100px;">
                        <img src="{{ $user->profile_image_url }}"
                             alt="{{ $user->name }}"
                             class="w-100 h-100"
                             style="object-fit: cover;">
                    </div>
                    <p class="fw-semibold mb-1">{{ $user->name }}</p>
                    <span class="badge bg-primary text-capitalize">
                        {{ str_replace('_', ' ', $user->role) }}
                    </span>
                </div>
            </div>

        </div>{{-- /col-lg-4 --}}

    </div>{{-- /row --}}
</div>

@endsection
