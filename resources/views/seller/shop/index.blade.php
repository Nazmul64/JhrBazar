@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">
    <h4 class="mb-4 fw-bold">Profile Details</h4>

    <div class="row">
        <div class="col-lg-8">
            {{-- Shop Banner & Info Card --}}
            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <div class="position-relative" style="height: 250px;">
                    <img src="{{ $shop->banner ? asset($shop->banner) : asset('assets/admin/images/default-banner.png') }}" 
                         class="w-100 h-100" style="object-fit: cover;" alt="Banner">
                    <a href="{{ route('seller.shop.edit') }}" class="btn btn-light position-absolute top-0 end-0 m-3 shadow-sm btn-sm px-3">
                        <i class="bi bi-pencil-square text-danger"></i> <span class="text-dark fw-bold">Edit</span>
                    </a>
                </div>
                <div class="card-body position-relative pt-0">
                    <div class="d-flex align-items-end mb-3" style="margin-top: -50px;">
                        <img src="{{ $shop->logo ? asset($shop->logo) : asset('assets/admin/images/default-logo.png') }}" 
                             class="rounded-circle border border-4 border-white shadow" 
                             style="width: 120px; height: 120px; object-fit: cover; background: #fff;" alt="Logo">
                        <div class="ms-3 mb-2">
                            <h4 class="mb-1 fw-bold">{{ $shop->name }}</h4>
                            <div class="text-warning small mb-2">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <span class="text-muted ms-1">(0 Reviews)</span>
                            </div>
                            <a href="#" class="btn btn-outline-danger btn-sm px-4 rounded-pill">View Live</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- User Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 fw-bold">User Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Name:</div>
                        <div class="col-sm-9 fw-bold text-uppercase">{{ $user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Phone:</div>
                        <div class="col-sm-9 fw-bold text-primary">{{ $user->phone }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 text-muted">Email:</div>
                        <div class="col-sm-9 fw-bold text-danger">{{ $user->email }}</div>
                    </div>
                </div>
            </div>

            {{-- Shop Information --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 fw-bold">Shop Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Name:</div>
                        <div class="col-sm-9 fw-bold">{{ $shop->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Estimated Delivery:</div>
                        <div class="col-sm-9 fw-bold text-primary">{{ $shop->estimated_delivery ?? 'N/A' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 text-muted">Shop Description:</div>
                        <div class="col-sm-9 text-muted small">{{ $shop->description ?? 'No description available.' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold">Product Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 py-2 border-bottom">
                        <span class="text-muted small fw-bold">Total Products:</span>
                        <span class="fw-bold fs-5">{{ $totalProducts }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4 py-2 border-bottom">
                        <span class="text-muted small fw-bold">Total Orders:</span>
                        <span class="fw-bold fs-5">{{ $totalOrders }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4 py-2 border-bottom">
                        <span class="text-muted small fw-bold">Reviews:</span>
                        <span class="fw-bold fs-5">{{ $totalReviews }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
