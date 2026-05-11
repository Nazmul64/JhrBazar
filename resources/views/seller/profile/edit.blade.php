@extends('admin.master')
@section('content')

<div class="container-fluid py-4">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-store me-2 text-danger"></i> Edit Seller Profile & Shop
        </h4>
        <a href="{{ route('seller.profile.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Profile
        </a>
    </div>

    {{-- ── Validation Errors ────────────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-4 text-danger"></i>
                <div>
                    <strong class="d-block mb-1">Please fix the following errors:</strong>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('seller.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="row g-4">
            
            {{-- ── LEFT COLUMN: User & Shop Info ────────────────────────────────── --}}
            <div class="col-lg-8">
                
                {{-- User Info Section --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-4 border-bottom pb-2">
                            <i class="fas fa-user-circle me-2 text-danger"></i> Personal Information
                        </h6>
                        
                        @php
                            $nameParts = explode(' ', $user->name, 2);
                            $firstName = $nameParts[0] ?? '';
                            $lastName  = $nameParts[1] ?? '';
                        @endphp

                        <div class="row g-3">
                            {{-- Profile Image --}}
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold small text-uppercase">Profile Photo</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle overflow-hidden border shadow-sm flex-shrink-0 bg-light"
                                         style="width:80px;height:80px;">
                                        <img id="profilePreview"
                                             src="{{ $user->profile_image_url }}"
                                             alt="Profile"
                                             class="w-100 h-100" style="object-fit:cover;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" name="profile_image" class="form-control" accept="image/*"
                                               onchange="previewImage(this,'profilePreview')">
                                        <small class="text-muted">Recommended: Square image (JPG, PNG)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $firstName) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $lastName) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    @foreach(['Male','Female','Other'] as $g)
                                        <option value="{{ $g }}" {{ old('gender', $user->gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shop Info Section --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-4 border-bottom pb-2">
                            <i class="fas fa-shop me-2 text-danger"></i> Shop Settings
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Shop Name</label>
                                <input type="text" name="shop_name" class="form-control" value="{{ old('shop_name', $shop->name ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Order ID Prefix</label>
                                <input type="text" name="order_prefix" class="form-control" value="{{ old('order_prefix', $shop->order_prefix ?? 'RC') }}" maxlength="10">
                            </div>

                            {{-- Division & District --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Division</label>
                                <select name="division" id="division" class="form-select" onchange="onDivisionChange(this.value)">
                                    <option value="">Select Division</option>
                                    @foreach(array_keys($bdDivisions) as $div)
                                        <option value="{{ $div }}" {{ old('division', $shop->division ?? '') === $div ? 'selected' : '' }}>{{ $div }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">District</label>
                                <select name="district" id="district" class="form-select">
                                    <option value="">Select District</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Shop Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $shop->address ?? '') }}" placeholder="Detailed address...">
                            </div>

                            {{-- Delivery & Min Order --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Min. Order Amount (৳)</label>
                                <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $shop->min_order_amount ?? 0) }}" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Est. Delivery (Days)</label>
                                <input type="number" name="estimated_delivery" class="form-control" value="{{ old('estimated_delivery', $shop->estimated_delivery ?? 3) }}" min="1">
                            </div>

                            {{-- Times --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Opening Time</label>
                                <input type="time" name="opening_time" class="form-control" value="{{ old('opening_time', $shop->opening_time ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Closing Time</label>
                                <input type="time" name="closing_time" class="form-control" value="{{ old('closing_time', $shop->closing_time ?? '') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Shop Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $shop->description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Map Section --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">
                            <i class="fas fa-map-marked-alt me-2 text-danger"></i> Pin Your Location
                        </h6>
                        <input type="hidden" name="latitude" id="latInput" value="{{ old('latitude', $shop->latitude ?? '') }}">
                        <input type="hidden" name="longitude" id="lngInput" value="{{ old('longitude', $shop->longitude ?? '') }}">
                        <div id="shopMap" style="height: 350px; border-radius: 12px; border: 1px solid #eee;"></div>
                        <div id="coordsDisplay" class="mt-2 text-muted small {{ ($shop && $shop->latitude) ? '' : 'd-none' }}">
                            <i class="fas fa-location-dot me-1 text-danger"></i>
                            Location pinned at: <span id="latDisplay">{{ $shop->latitude ?? '' }}</span>, <span id="lngDisplay">{{ $shop->longitude ?? '' }}</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT COLUMN: Branding Assets ──────────────────────────────── --}}
            <div class="col-lg-4">
                
                {{-- Shop Logo --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h6 class="fw-bold mb-4 text-start border-bottom pb-2">Shop Logo</h6>
                        <div class="mb-4 mx-auto" style="width: 150px; height: 150px;">
                            <img id="logoPreview" 
                                 src="{{ ($shop && $shop->logo) ? asset($shop->logo) : 'https://via.placeholder.com/150?text=No+Logo' }}" 
                                 class="w-100 h-100 border rounded-3 shadow-sm" style="object-fit: cover;">
                        </div>
                        <input type="file" name="logo" class="form-control" accept="image/*" onchange="previewImage(this,'logoPreview')">
                        <p class="text-muted small mt-2 mb-0">Transparent PNG or JPG (500x500px)</p>
                    </div>
                </div>

                {{-- Shop Banner - THIS IS WHAT THE USER WAS ASKING FOR --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h6 class="fw-bold mb-4 text-start border-bottom pb-2">Shop Banner</h6>
                        <div class="mb-4 w-100 bg-light rounded-3 overflow-hidden border shadow-sm" style="height: 120px;">
                            @if($shop && $shop->banner)
                                <img id="bannerPreview" src="{{ asset($shop->banner) }}" class="w-100 h-100" style="object-fit: cover;">
                            @else
                                <img id="bannerPreview" src="https://via.placeholder.com/800x200?text=No+Banner" class="w-100 h-100" style="object-fit: cover;">
                            @endif
                        </div>
                        <input type="file" name="banner" class="form-control" accept="image/*" onchange="previewImage(this,'bannerPreview')">
                        <p class="text-muted small mt-2 mb-0">Wide landscape (e.g. 1920x480px)</p>
                        <div class="alert alert-info border-0 small mt-3 text-start">
                            <i class="fas fa-lightbulb me-1"></i> A professional banner increases shop trust by 40%!
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm bg-dark text-white">
                    <div class="card-body text-center py-4">
                        <p class="mb-3">Ready to save your changes?</p>
                        <button type="submit" class="btn btn-danger w-100 fw-bold py-2 shadow">
                            <i class="fas fa-save me-2"></i> SAVE ALL CHANGES
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

{{-- Leaflet Assets --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const BD_DIVISIONS = @json($bdDivisions);
const SAVED_DIVISION = @json(old('division', $shop->division ?? ''));
const SAVED_DISTRICT = @json(old('district', $shop->district ?? ''));

function populateDistricts(division, restoreDistrict) {
    const sel = document.getElementById('district');
    sel.innerHTML = '<option value="">Select District</option>';
    if (!division || !BD_DIVISIONS[division]) return;
    BD_DIVISIONS[division].forEach(function(dist) {
        const opt = document.createElement('option');
        opt.value = dist;
        opt.textContent = dist;
        if (dist === restoreDistrict) opt.selected = true;
        sel.appendChild(opt);
    });
}

function onDivisionChange(division) {
    populateDistricts(division, '');
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview || !input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) { preview.src = e.target.result; };
    reader.readAsDataURL(input.files[0]);
}

document.addEventListener('DOMContentLoaded', function() {
    if (SAVED_DIVISION) {
        populateDistricts(SAVED_DIVISION, SAVED_DISTRICT);
    }

    const latInput = document.getElementById('latInput');
    const lngInput = document.getElementById('lngInput');
    const defLat = parseFloat(latInput.value) || 23.8103;
    const defLng = parseFloat(lngInput.value) || 90.4125;
    const map = L.map('shopMap').setView([defLat, defLng], latInput.value ? 14 : 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    let marker = null;
    function placeMarker(lat, lng) {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        document.getElementById('latDisplay').textContent = lat.toFixed(5);
        document.getElementById('lngDisplay').textContent = lng.toFixed(5);
        document.getElementById('coordsDisplay').classList.remove('d-none');
        if (marker) marker.setLatLng([lat, lng]);
        else marker = L.marker([lat, lng], {draggable: true}).addTo(map);
    }

    if (latInput.value) placeMarker(defLat, defLng);
    map.on('click', function(e) { placeMarker(e.latlng.lat, e.latlng.lng); });
});
</script>

@endsection
