@extends('admin.master')
@section('content')

<div class="container-fluid py-4">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-store me-2 text-danger"></i> Edit Shop
        </h4>
        <a href="{{ route('admin.profile.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    {{-- ── Validation Errors ────────────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle me-2"></i> Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ✅ METHOD: POST only — route admin/profile/update supports POST, not PUT --}}
    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- ══════════════════════════════════════════════════════════════════
             USER INFORMATION
        ══════════════════════════════════════════════════════════════════ --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-4">
                    <i class="fas fa-user me-2 text-danger"></i> User Information
                </h6>

                @php
                    $nameParts = explode(' ', $user->name, 2);
                    $firstName = $nameParts[0] ?? '';
                    $lastName  = $nameParts[1] ?? '';
                @endphp

                <div class="row g-3">

                    {{-- Profile Image --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Profile Photo <small class="text-muted fw-normal">(Ratio 1:1)</small>
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle overflow-hidden border shadow-sm flex-shrink-0"
                                 style="width:80px;height:80px;">
                                <img id="profilePreview"
                                     src="{{ $user->profile_image_url }}"
                                     alt="{{ $user->name }}"
                                     class="w-100 h-100" style="object-fit:cover;">
                            </div>
                            <div class="flex-grow-1">
                                <input type="file"
                                       name="profile_image"
                                       id="profileImageInput"
                                       class="form-control @error('profile_image') is-invalid @enderror"
                                       accept="image/jpeg,image/png,image/webp,image/gif"
                                       onchange="previewImage(this,'profilePreview')">
                                <small class="text-muted">Leave empty to keep current photo</small>
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- First Name --}}
                    <div class="col-md-6">
                        <label for="first_name" class="form-label fw-semibold">
                            First Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="first_name" id="first_name"
                               class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name', $firstName) }}"
                               placeholder="Enter first name" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Last Name --}}
                    <div class="col-md-6">
                        <label for="last_name" class="form-label fw-semibold">Last Name</label>
                        <input type="text"
                               name="last_name" id="last_name"
                               class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name', $lastName) }}"
                               placeholder="Enter last name">
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">
                            Phone Number <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="phone" id="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="e.g. +880 1700-000000" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Gender --}}
                    <div class="col-md-6">
                        <label for="gender" class="form-label fw-semibold">Gender</label>
                        <select name="gender" id="gender"
                                class="form-select @error('gender') is-invalid @enderror">
                            <option value="">— Select Gender —</option>
                            @foreach(['Male','Female','Other'] as $g)
                                <option value="{{ $g }}"
                                    {{ old('gender', $user->gender) === $g ? 'selected' : '' }}>
                                    {{ $g }}
                                </option>
                            @endforeach
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-12">
                        <label for="email" class="form-label fw-semibold">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email"
                               name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}"
                               placeholder="example@email.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>{{-- /row --}}
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             SHOP INFORMATION
        ══════════════════════════════════════════════════════════════════ --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-4">
                    <i class="fas fa-store me-2 text-danger"></i> Shop Information
                </h6>

                <div class="row g-3">

                    {{-- Shop Name --}}
                    <div class="col-md-4">
                        <label for="shop_name" class="form-label fw-semibold">
                            Shop Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="shop_name" id="shop_name"
                               class="form-control @error('shop_name') is-invalid @enderror"
                               value="{{ old('shop_name', $shop->name ?? '') }}"
                               placeholder="Enter shop name" required>
                        @error('shop_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="col-md-8">
                        <label for="address" class="form-label fw-semibold">Address</label>
                        <input type="text"
                               name="address" id="address"
                               class="form-control @error('address') is-invalid @enderror"
                               value="{{ old('address', $shop->address ?? '') }}"
                               placeholder="Street / area / locality">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ────────────────────────────────────────────────────────
                         DIVISION  (searchable)
                    ──────────────────────────────────────────────────────── --}}
                    <div class="col-md-4">
                        <label for="division" class="form-label fw-semibold">Division</label>

                        {{-- inline search --}}
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted fa-xs"></i>
                            </span>
                            <input type="text"
                                   id="divisionSearch"
                                   class="form-control border-start-0 ps-0"
                                   placeholder="Search division…"
                                   autocomplete="off"
                                   oninput="filterSelect('divisionSearch','division')">
                        </div>

                        <select name="division" id="division"
                                class="form-select @error('division') is-invalid @enderror"
                                onchange="onDivisionChange(this.value)">
                            <option value="">— Select Division —</option>
                            @foreach(array_keys($bdDivisions) as $div)
                                <option value="{{ $div }}"
                                    {{ old('division', $shop->division ?? '') === $div ? 'selected' : '' }}>
                                    {{ $div }}
                                </option>
                            @endforeach
                        </select>
                        @error('division')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ────────────────────────────────────────────────────────
                         DISTRICT  (searchable, filtered by division)
                    ──────────────────────────────────────────────────────── --}}
                    <div class="col-md-4">
                        <label for="district" class="form-label fw-semibold">District</label>

                        {{-- inline search --}}
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted fa-xs"></i>
                            </span>
                            <input type="text"
                                   id="districtSearch"
                                   class="form-control border-start-0 ps-0"
                                   placeholder="Search district…"
                                   autocomplete="off"
                                   oninput="filterSelect('districtSearch','district')">
                        </div>

                        <select name="district" id="district"
                                class="form-select @error('district') is-invalid @enderror">
                            <option value="">— Select District —</option>
                            {{-- Populated by JS on load / division change --}}
                        </select>
                        @error('district')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Minimum Order Amount --}}
                    <div class="col-md-4">
                        <label for="minimum_order_amount" class="form-label fw-semibold">
                            Minimum Order Amount
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number"
                                   name="minimum_order_amount" id="minimum_order_amount"
                                   class="form-control @error('minimum_order_amount') is-invalid @enderror"
                                   value="{{ old('minimum_order_amount', $shop->minimum_order_amount ?? 0) }}"
                                   min="0" step="0.01" placeholder="0.00">
                            @error('minimum_order_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Shop Logo --}}
                    <div class="col-md-6">
                        <label for="logo" class="form-label fw-semibold">
                            Shop Logo <small class="text-muted fw-normal">(Ratio 1:1 — 500×500 px)</small>
                        </label>
                        <div class="mb-2">
                            <img id="logoPreview"
                                 src="{{ $shop && $shop->logo ? asset($shop->logo) : '' }}"
                                 alt="Logo"
                                 class="rounded border shadow-sm"
                                 style="width:100px;height:100px;object-fit:cover;{{ !($shop && $shop->logo) ? 'display:none;' : '' }}">
                        </div>
                        <input type="file"
                               name="logo" id="logo"
                               class="form-control @error('logo') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/webp"
                               onchange="previewImage(this,'logoPreview')">
                        <small class="text-muted">Leave empty to keep current logo</small>
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Shop Banner --}}
                    <div class="col-md-6">
                        <label for="banner" class="form-label fw-semibold">
                            Shop Banner <small class="text-muted fw-normal">(Ratio 4:1 — 2000×500 px)</small>
                        </label>
                        <div class="mb-2">
                            <img id="bannerPreview"
                                 src="{{ $shop && $shop->banner ? asset($shop->banner) : '' }}"
                                 alt="Banner"
                                 class="rounded border w-100"
                                 style="height:100px;object-fit:cover;{{ !($shop && $shop->banner) ? 'display:none;' : '' }}">
                        </div>
                        <input type="file"
                               name="banner" id="banner"
                               class="form-control @error('banner') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/webp"
                               onchange="previewImage(this,'bannerPreview')">
                        <small class="text-muted">Leave empty to keep current banner</small>
                        @error('banner')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Opening Time --}}
                    <div class="col-md-4">
                        <label for="opening_time" class="form-label fw-semibold">
                            Opening Time <span class="text-danger">*</span>
                        </label>
                        <input type="time"
                               name="opening_time" id="opening_time"
                               class="form-control @error('opening_time') is-invalid @enderror"
                               value="{{ old('opening_time', $shop->opening_time ?? '') }}" required>
                        @error('opening_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Closing Time --}}
                    <div class="col-md-4">
                        <label for="closing_time" class="form-label fw-semibold">
                            Closing Time <span class="text-danger">*</span>
                        </label>
                        <input type="time"
                               name="closing_time" id="closing_time"
                               class="form-control @error('closing_time') is-invalid @enderror"
                               value="{{ old('closing_time', $shop->closing_time ?? '') }}" required>
                        @error('closing_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Estimated Delivery --}}
                    <div class="col-md-4">
                        <label for="estimated_delivery" class="form-label fw-semibold">
                            Estimated Delivery (days) <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               name="estimated_delivery" id="estimated_delivery"
                               class="form-control @error('estimated_delivery') is-invalid @enderror"
                               value="{{ old('estimated_delivery', $shop->estimated_delivery ?? 3) }}"
                               min="1" required>
                        @error('estimated_delivery')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Order ID Prefix --}}
                    <div class="col-md-4">
                        <label for="order_id_prefix" class="form-label fw-semibold">
                            Order ID Prefix <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="order_id_prefix" id="order_id_prefix"
                               class="form-control @error('order_id_prefix') is-invalid @enderror"
                               value="{{ old('order_id_prefix', $shop->order_id_prefix ?? 'RC') }}"
                               placeholder="RC" maxlength="10" required>
                        <small class="text-muted">e.g. RC-000001</small>
                        @error('order_id_prefix')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="3"
                                  placeholder="Brief description about your shop…">{{ old('description', $shop->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>{{-- /row --}}
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             SHOP LOCATION (MAP)
        ══════════════════════════════════════════════════════════════════ --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-1">
                    <i class="fas fa-map-marker-alt me-2 text-danger"></i> Shop Location
                </h6>
                <p class="text-muted small mb-3">
                    Click on the map to set / update your shop location. Drag the pin to fine-tune.
                </p>

                <input type="hidden" name="latitude"  id="latInput"
                       value="{{ old('latitude',  $shop->latitude  ?? '') }}">
                <input type="hidden" name="longitude" id="lngInput"
                       value="{{ old('longitude', $shop->longitude ?? '') }}">

                <div id="shopMap"
                     style="height:420px;border-radius:8px;border:1px solid #dee2e6;z-index:1;">
                </div>

                <div id="coordsDisplay"
                     class="mt-2 text-muted small {{ ($shop && $shop->latitude) ? '' : 'd-none' }}">
                    <i class="fas fa-crosshairs me-1 text-danger"></i>
                    Pinned at:
                    <strong id="latDisplay">{{ $shop->latitude ?? '' }}</strong>,
                    <strong id="lngDisplay">{{ $shop->longitude ?? '' }}</strong>
                </div>
            </div>
        </div>

        {{-- ── Form Actions ─────────────────────────────────────────────────── --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('admin.profile.index') }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-danger px-5 fw-semibold">
                <i class="fas fa-save me-1"></i> Update Shop
            </button>
        </div>

    </form>
</div>

{{-- ── Leaflet ──────────────────────────────────────────────────────────────── --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

{{-- Pass PHP division data safely to JS --}}
<script>
/* ══════════════════════════════════════════════════════════════════════════
   Bangladesh — 8 Divisions + 64 Districts (A–Z within each division)
══════════════════════════════════════════════════════════════════════════ */
const BD_DIVISIONS = @json($bdDivisions);

/* Values saved in DB / old() — used to restore selects after page load */
const SAVED_DIVISION = @json(old('division', $shop->division ?? ''));
const SAVED_DISTRICT = @json(old('district', $shop->district ?? ''));

/* ── Populate district <select> from a given division key ─────────────── */
function populateDistricts(division, restoreDistrict) {
    const sel = document.getElementById('district');
    sel.innerHTML = '<option value="">— Select District —</option>';
    if (!division || !BD_DIVISIONS[division]) return;

    BD_DIVISIONS[division].forEach(function (dist) {
        const opt       = document.createElement('option');
        opt.value       = dist;
        opt.textContent = dist;
        if (dist === restoreDistrict) opt.selected = true;
        sel.appendChild(opt);
    });
}

/* Called when user picks a division manually */
function onDivisionChange(division) {
    populateDistricts(division, '');
    document.getElementById('districtSearch').value = '';
}

/* ── Live text filter on any <select> ────────────────────────────────── */
function filterSelect(searchId, selectId) {
    const query   = document.getElementById(searchId).value.toLowerCase().trim();
    const options = document.getElementById(selectId).querySelectorAll('option');
    options.forEach(function (opt) {
        if (!opt.value) { opt.style.display = ''; return; }  // keep placeholder
        opt.style.display = opt.textContent.toLowerCase().includes(query) ? '' : 'none';
    });
}

/* ── Image preview ────────────────────────────────────────────────────── */
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview || !input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src          = e.target.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}

/* ── DOM Ready ────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {

    /* Restore saved division + district (edit mode / validation fail) */
    if (SAVED_DIVISION) {
        document.getElementById('division').value = SAVED_DIVISION;
        populateDistricts(SAVED_DIVISION, SAVED_DISTRICT);
    }

    /* ── Leaflet Map ──────────────────────────────────────────────────── */
    const latInput      = document.getElementById('latInput');
    const lngInput      = document.getElementById('lngInput');
    const coordsDisplay = document.getElementById('coordsDisplay');
    const latDisplay    = document.getElementById('latDisplay');
    const lngDisplay    = document.getElementById('lngDisplay');

    const hasLocation = latInput.value !== '' && lngInput.value !== '';
    const defLat      = parseFloat(latInput.value)  || 23.8103;   // Dhaka default
    const defLng      = parseFloat(lngInput.value) || 90.4125;

    const map = L.map('shopMap').setView([defLat, defLng], hasLocation ? 13 : 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution : '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom     : 19,
    }).addTo(map);

    const markerIcon = L.icon({
        iconUrl    : 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        shadowUrl  : 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        iconSize   : [25, 41],
        iconAnchor : [12, 41],
        popupAnchor: [1, -34],
        shadowSize : [41, 41],
    });

    let marker = null;

    function placeMarker(lat, lng) {
        latInput.value         = lat.toFixed(7);
        lngInput.value         = lng.toFixed(7);
        latDisplay.textContent = lat.toFixed(5);
        lngDisplay.textContent = lng.toFixed(5);
        coordsDisplay.classList.remove('d-none');

        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { icon: markerIcon, draggable: true }).addTo(map);
            marker.on('dragend', function () {
                const p = marker.getLatLng();
                placeMarker(p.lat, p.lng);
            });
        }
    }

    if (hasLocation) placeMarker(defLat, defLng);

    map.on('click', function (e) {
        placeMarker(e.latlng.lat, e.latlng.lng);
    });
});
</script>

@endsection
