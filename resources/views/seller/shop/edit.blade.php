@extends('admin.master')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 400px; width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; }
    .form-section-title { font-size: 14px; font-weight: 700; color: #1a1f36; display: flex; align-items: center; gap: 8px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9; }
</style>

<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-shop me-2"></i>Edit Shop</h4>
        <a href="{{ route('seller.shop.index') }}" class="btn btn-outline-secondary btn-sm">Back to Details</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('seller.shop.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        {{-- User Information --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="form-section-title"><i class="bi bi-person"></i> User Information</div>
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', explode(' ', $user->name)[0]) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ $user->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center">
                        <div class="mb-3">
                            <label class="form-label small fw-bold d-block">User Profile (Ratio 1:1)</label>
                            <img src="{{ $user->profile_image ? asset($user->profile_image) : asset('assets/admin/images/default-avatar.png') }}" 
                                 id="profilePreview" class="rounded border shadow-sm mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <input type="file" name="profile_image" class="form-control form-control-sm" onchange="previewImg(this, 'profilePreview')">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shop Information --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="form-section-title"><i class="bi bi-shop-window"></i> Shop Information</div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Shop Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $shop->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Address <span class="text-danger">*</span></label>
                        <input type="text" name="address" id="addressInput" class="form-control" value="{{ old('address', $shop->address) }}" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Minimum Order Amount</label>
                        <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $shop->min_order_amount) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Opening Time <span class="text-danger">*</span></label>
                        <input type="time" name="opening_time" class="form-control" value="{{ old('opening_time', $shop->opening_time) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Closing Time <span class="text-danger">*</span></label>
                        <input type="time" name="closing_time" class="form-control" value="{{ old('closing_time', $shop->closing_time) }}" required>
                    </div>

                    <div class="col-md-6">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold">Shop Logo (Ratio 1:1)</label>
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <img src="{{ $shop->logo ? asset($shop->logo) : asset('assets/admin/images/default-logo.png') }}" 
                                         id="logoPreview" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                                    <input type="file" name="logo" class="form-control form-control-sm" onchange="previewImg(this, 'logoPreview')">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Order ID Prefix <span class="text-danger">*</span></label>
                                <input type="text" name="order_prefix" class="form-control" value="{{ old('order_prefix', $shop->order_prefix) }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold">Shop Banner Ratio 4:1 (2000 × 500 px)</label>
                                <img src="{{ $shop->banner ? asset($shop->banner) : asset('assets/admin/images/default-banner.png') }}" 
                                     id="bannerPreview" class="rounded border mb-2 w-100" style="height: 100px; object-fit: cover;">
                                <input type="file" name="banner" class="form-control form-control-sm" onchange="previewImg(this, 'bannerPreview')">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Estimated Delivery <span class="text-danger">*</span></label>
                                <input type="text" name="estimated_delivery" class="form-control" value="{{ old('estimated_delivery', $shop->estimated_delivery) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $shop->description) }}</textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label small fw-bold mb-3">Location Pin (Drag marker to set location)</label>
                    <div id="map"></div>
                    <input type="hidden" name="latitude" id="lat" value="{{ $shop->latitude }}">
                    <input type="hidden" name="longitude" id="lng" value="{{ $shop->longitude }}">
                </div>

                <div class="mt-5">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="background: #e7567c; border:none;">Update Shop</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function previewImg(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Leaflet Map Integration
    const initialLat = {{ $shop->latitude ?: 23.8103 }};
    const initialLng = {{ $shop->longitude ?: 90.4125 }};

    const map = L.map('map').setView([initialLat, initialLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        document.getElementById('lat').value = pos.lat;
        document.getElementById('lng').value = pos.lng;
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('lat').value = e.latlng.lat;
        document.getElementById('lng').value = e.latlng.lng;
    });
</script>
@endsection
