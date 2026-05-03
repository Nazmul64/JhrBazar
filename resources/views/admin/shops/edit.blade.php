@extends('admin.master')
@section('content')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
* { box-sizing: border-box; }

.shop-form-wrap {
    background: #f4f6f9;
    min-height: 100vh;
    padding: 28px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #333;
}

/* ── Page title ── */
.form-page-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
    font-weight: 700;
    color: #222;
    margin-bottom: 24px;
}
.form-page-title i { color: #333; font-size: 18px; }

/* ── Error alert ── */
.form-error-box {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 6px;
    padding: 12px 16px;
    margin-bottom: 20px;
    font-size: 14px;
    color: #856404;
}
.form-error-box ul { margin: 6px 0 0; padding-left: 18px; }

/* ── Section card ── */
.form-section {
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e8e8e8;
    margin-bottom: 20px;
    overflow: hidden;
}
.form-section-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 20px;
    font-size: 15px;
    font-weight: 700;
    color: #222;
    border-bottom: 1px solid #f0f0f0;
}
.form-section-head i { color: #555; font-size: 15px; }
.form-section-body { padding: 20px; }

/* ── Grid helpers ── */
.row-2  { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.row-3  { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
.row-img { display: grid; grid-template-columns: 1fr 3fr; gap: 24px; align-items: start; }

/* ── Field ── */
.field-wrap { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
.field-wrap:last-child { margin-bottom: 0; }
.field-label {
    font-size: 13px;
    font-weight: 600;
    color: #444;
}
.field-label .req { color: #e83e8c; margin-left: 2px; }
.field-input, .field-select, .field-textarea {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 9px 12px;
    font-size: 14px;
    color: #333;
    background: #fff;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    appearance: none;
    -webkit-appearance: none;
}
.field-input:focus, .field-select:focus, .field-textarea:focus {
    border-color: #e83e8c;
    box-shadow: 0 0 0 3px rgba(232,62,140,.08);
}
.field-input.is-invalid, .field-select.is-invalid, .field-textarea.is-invalid {
    border-color: #dc3545;
}
.field-input::placeholder, .field-textarea::placeholder { color: #bbb; }
.field-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23999'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 32px;
    cursor: pointer;
}
.field-select option { color: #333; }
.field-textarea { resize: vertical; min-height: 90px; }
.field-error { font-size: 12px; color: #dc3545; margin-top: 2px; }

/* ── Image preview boxes ── */
.img-preview-box {
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
    background: #f8f8f8;
    display: flex;
    align-items: center;
    justify-content: center;
}
.img-preview-box img {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
}
.img-preview-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%; height: 100%;
    color: #ccc;
}
.img-preview-placeholder span { font-size: 14px; font-weight: 700; color: #ccc; }

.profile-img-box { height: 160px; width: 100%; margin-bottom: 10px; }
.logo-img-box    { height: 180px; }
.banner-img-box  { height: 180px; }

/* ══════════════════════════
   MAP SEARCH BOX
══════════════════════════ */
.map-search-wrap {
    position: relative;
    margin-bottom: 10px;
}
.map-search-input-row {
    display: flex;
    gap: 8px;
}
.map-search-input {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 9px 12px 9px 36px;
    font-size: 14px;
    color: #333;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='%23aaa' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") no-repeat 10px center;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.map-search-input:focus {
    border-color: #e83e8c;
    box-shadow: 0 0 0 3px rgba(232,62,140,.08);
}
.map-search-input::placeholder { color: #bbb; }
.btn-map-search {
    display: inline-flex; align-items: center; gap: 6px;
    background: #e83e8c; color: #fff;
    border: none; border-radius: 5px;
    padding: 9px 18px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background .2s;
    white-space: nowrap;
}
.btn-map-search:hover { background: #d6317e; }
.btn-map-search i { font-size: 13px; }

/* Autocomplete dropdown */
.map-search-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 60px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    box-shadow: 0 6px 24px rgba(0,0,0,.10);
    z-index: 9999;
    max-height: 220px;
    overflow-y: auto;
    display: none;
}
.map-search-dropdown.open { display: block; }
.map-search-result-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f5f5f5;
    transition: background .15s;
    font-size: 13px;
    color: #333;
    line-height: 1.4;
}
.map-search-result-item:last-child { border-bottom: none; }
.map-search-result-item:hover { background: #fdf0f6; }
.map-search-result-item i {
    color: #e83e8c;
    font-size: 13px;
    margin-top: 2px;
    flex-shrink: 0;
}
.map-search-no-result {
    padding: 14px;
    font-size: 13px;
    color: #aaa;
    text-align: center;
}
.map-search-loading {
    padding: 14px;
    font-size: 13px;
    color: #999;
    text-align: center;
}

/* ── Map ── */
#shopMap {
    width: 100%;
    height: 340px;
    border-radius: 6px;
    border: 1px solid #ddd;
    overflow: hidden;
}
.coord-info {
    margin-top: 8px;
    font-size: 13px;
    color: #888;
}
.coord-info span { color: #e83e8c; font-weight: 600; }

/* ── Submit bar ── */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 16px 20px;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e8e8e8;
}
.btn-cancel {
    display: inline-flex; align-items: center; gap: 6px;
    background: #fff; border: 1px solid #ddd; color: #555;
    padding: 9px 22px; border-radius: 6px;
    font-size: 14px; font-weight: 600; text-decoration: none; transition: all .2s;
}
.btn-cancel:hover { border-color: #999; color: #333; }
.btn-submit {
    display: inline-flex; align-items: center; gap: 6px;
    background: #e83e8c; border: none; color: #fff;
    padding: 9px 28px; border-radius: 6px;
    font-size: 14px; font-weight: 700; cursor: pointer; transition: all .2s;
}
.btn-submit:hover { background: #d6317e; }

@media (max-width: 768px) {
    .row-2, .row-3, .row-img { grid-template-columns: 1fr; }
    .map-search-dropdown { right: 0; }
}
</style>

@php
    $user      = $shop->user;
    $nameParts = explode(' ', $user->name ?? '', 2);
    $firstName = $nameParts[0] ?? '';
    $lastName  = $nameParts[1] ?? '';
@endphp

<div class="shop-form-wrap">

    {{-- Title --}}
    <div class="form-page-title">
        <i class="fas fa-store"></i> Edit Shop
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="form-error-box">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div style="background:#d4edda; border:1px solid #c3e6cb; color:#155724; border-radius:6px; padding:11px 16px; margin-bottom:20px; font-size:14px; display:flex; align-items:center; gap:8px">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.shops.update', $shop) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- ══ USER INFORMATION ══ --}}
        <div class="form-section">
            <div class="form-section-head">
                <i class="fas fa-user"></i> User Information
            </div>
            <div class="form-section-body">
                <div class="row-img">

                    {{-- Left: user fields --}}
                    <div>
                        <div class="row-2" style="margin-bottom:16px">
                            <div class="field-wrap" style="margin-bottom:0">
                                <label class="field-label">First Name <span class="req">*</span></label>
                                <input type="text" name="first_name"
                                    class="field-input @error('first_name') is-invalid @enderror"
                                    placeholder="Enter Name"
                                    value="{{ old('first_name', $firstName) }}">
                                @error('first_name')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="field-wrap" style="margin-bottom:0">
                                <label class="field-label">Last Name</label>
                                <input type="text" name="last_name"
                                    class="field-input @error('last_name') is-invalid @enderror"
                                    placeholder="Enter Name"
                                    value="{{ old('last_name', $lastName) }}">
                                @error('last_name')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="field-wrap" style="margin-bottom:16px">
                            <label class="field-label">Phone Number <span class="req">*</span></label>
                            <input type="text" name="phone"
                                class="field-input @error('phone') is-invalid @enderror"
                                placeholder="Enter phone number"
                                value="{{ old('phone', $user->phone) }}">
                            @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field-wrap" style="margin-bottom:16px">
                            <label class="field-label">Gender</label>
                            <select name="gender" class="field-select @error('gender') is-invalid @enderror">
                                @foreach(['Male', 'Female', 'Other'] as $g)
                                    <option value="{{ $g }}" {{ old('gender', $user->gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                            @error('gender')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field-wrap" style="margin-bottom:0">
                            <label class="field-label">Email <span class="req">*</span></label>
                            <input type="email" name="email"
                                class="field-input @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}">
                            @error('email')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Right: profile image --}}
                    <div>
                        <div class="img-preview-box profile-img-box" id="profilePreviewBox">
                            @if($user->profile_image_url)
                                <img src="{{ $user->profile_image_url }}" alt="Profile">
                            @else
                                <div class="img-preview-placeholder">
                                    <span>500 × 500</span>
                                </div>
                            @endif
                        </div>
                        <div class="field-wrap" style="margin-bottom:0">
                            <label class="field-label">User profile (Ratio 1:1)</label>
                            <input type="file" name="profile_image" id="profileInput"
                                class="field-input @error('profile_image') is-invalid @enderror"
                                accept="image/*"
                                style="padding:6px 10px">
                            @error('profile_image')<div class="field-error">{{ $message }}</div>@enderror
                            <small style="color:#999; font-size:12px">Leave empty to keep current photo</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ══ SHOP INFORMATION ══ --}}
        <div class="form-section">
            <div class="form-section-head">
                <i class="fas fa-store"></i> Shop Information
            </div>
            <div class="form-section-body">

                {{-- Name & Address --}}
                <div class="row-2" style="margin-bottom:20px">
                    <div class="field-wrap" style="margin-bottom:0">
                        <label class="field-label">Shop Name <span class="req">*</span></label>
                        <input type="text" name="shop_name"
                            class="field-input @error('shop_name') is-invalid @enderror"
                            placeholder="Enter Shop Name"
                            value="{{ old('shop_name', $shop->name) }}">
                        @error('shop_name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field-wrap" style="margin-bottom:0">
                        <label class="field-label">Address</label>
                        <input type="text" name="address"
                            class="field-input"
                            placeholder="Enter Address"
                            value="{{ old('address', $shop->address) }}">
                    </div>
                </div>

                {{-- Logo + Banner current previews --}}
                <div class="row-2" style="margin-bottom:12px">
                    <div>
                        <div class="img-preview-box logo-img-box" id="logoPreviewBox">
                            @if($shop->logo_url)
                                <img src="{{ $shop->logo_url }}" alt="Logo">
                            @else
                                <div class="img-preview-placeholder"><span>500 × 500</span></div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="img-preview-box banner-img-box" id="bannerPreviewBox">
                            @if($shop->banner_url)
                                <img src="{{ $shop->banner_url }}" alt="Banner">
                            @else
                                <div class="img-preview-placeholder"><span style="font-size:22px">2000 × 500</span></div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Logo + Banner file inputs --}}
                <div class="row-2" style="margin-bottom:20px">
                    <div class="field-wrap" style="margin-bottom:0">
                        <label class="field-label">Shop logo (Ratio 1:1)</label>
                        <input type="file" name="shop_logo" id="logoInput"
                            class="field-input @error('shop_logo') is-invalid @enderror"
                            accept="image/*"
                            style="padding:6px 10px">
                        @error('shop_logo')<div class="field-error">{{ $message }}</div>@enderror
                        <small style="color:#999; font-size:12px">Leave empty to keep current logo</small>
                    </div>
                    <div class="field-wrap" style="margin-bottom:0">
                        <label class="field-label">Shop banner Ratio 4:1 (2000 × 500 px)</label>
                        <input type="file" name="shop_banner" id="bannerInput"
                            class="field-input @error('shop_banner') is-invalid @enderror"
                            accept="image/*"
                            style="padding:6px 10px">
                        @error('shop_banner')<div class="field-error">{{ $message }}</div>@enderror
                        <small style="color:#999; font-size:12px">Leave empty to keep current banner</small>
                    </div>
                </div>

                {{-- Description --}}
                <div class="field-wrap" style="margin-bottom:20px">
                    <label class="field-label">Description</label>
                    <textarea name="description" class="field-textarea"
                        placeholder="Enter Description">{{ old('description', $shop->description) }}</textarea>
                </div>

                {{-- ── Map Search ── --}}
                <div class="map-search-wrap" id="mapSearchWrap">
                    <label class="field-label" style="margin-bottom:6px; display:block">
                        <i class="fas fa-search-location" style="color:#e83e8c; margin-right:4px"></i>
                        Search Location
                    </label>
                    <div class="map-search-input-row">
                        <input type="text" id="mapSearchInput"
                            class="map-search-input"
                            placeholder="Search for a place, city, address…"
                            autocomplete="off">
                        <button type="button" class="btn-map-search" id="mapSearchBtn">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                    <div class="map-search-dropdown" id="mapSearchDropdown"></div>
                </div>

                {{-- Map --}}
                <div id="shopMap"></div>
                <input type="hidden" name="latitude"  id="latitude"
                    value="{{ old('latitude', $shop->latitude) }}">
                <input type="hidden" name="longitude" id="longitude"
                    value="{{ old('longitude', $shop->longitude) }}">
                <div class="coord-info" id="coordInfo">
                    @if($shop->latitude && $shop->longitude)
                        Pinned: <span>{{ $shop->latitude }}, {{ $shop->longitude }}</span>
                    @else
                        No location pinned — click on the map or use the search box above
                    @endif
                </div>

            </div>
        </div>

        {{-- Submit --}}
        <div class="form-actions">
            <a href="{{ route('admin.shops.index') }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-submit">Update</button>
        </div>

    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── Image preview helpers
function bindPreview(inputId, boxId) {
    var input = document.getElementById(inputId);
    var box   = document.getElementById(boxId);
    if (!input || !box) return;
    input.addEventListener('change', function () {
        if (!this.files[0]) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            box.innerHTML = '<img src="' + e.target.result + '" alt="preview">';
        };
        reader.readAsDataURL(this.files[0]);
    });
}

bindPreview('profileInput', 'profilePreviewBox');
bindPreview('logoInput',    'logoPreviewBox');
bindPreview('bannerInput',  'bannerPreviewBox');

// ── Leaflet map
var savedLat = {{ $shop->latitude  ?? 23.8103 }};
var savedLng = {{ $shop->longitude ?? 90.4125 }};

var map    = L.map('shopMap').setView([savedLat, savedLng], 13);
var marker = null;

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'Leaflet | &copy; OpenStreetMap'
}).addTo(map);

// Place existing pin if coordinates exist
@if($shop->latitude && $shop->longitude)
    marker = L.marker([{{ $shop->latitude }}, {{ $shop->longitude }}]).addTo(map);
@endif

// Pin on click
map.on('click', function (e) {
    setPin(e.latlng.lat, e.latlng.lng);
});

function setPin(lat, lng) {
    var latF = parseFloat(lat).toFixed(6);
    var lngF = parseFloat(lng).toFixed(6);
    document.getElementById('latitude').value  = latF;
    document.getElementById('longitude').value = lngF;
    document.getElementById('coordInfo').innerHTML =
        'Pinned: <span>' + latF + ', ' + lngF + '</span>';
    var latlng = L.latLng(lat, lng);
    if (marker) {
        marker.setLatLng(latlng);
    } else {
        marker = L.marker(latlng).addTo(map);
    }
}

// ══════════════════════════
//  MAP SEARCH  (Nominatim)
// ══════════════════════════
var searchInput    = document.getElementById('mapSearchInput');
var searchBtn      = document.getElementById('mapSearchBtn');
var searchDropdown = document.getElementById('mapSearchDropdown');
var searchTimer    = null;

function closeDropdown() {
    searchDropdown.classList.remove('open');
    searchDropdown.innerHTML = '';
}

function showLoading() {
    searchDropdown.innerHTML = '<div class="map-search-loading"><i class="fas fa-spinner fa-spin"></i> Searching…</div>';
    searchDropdown.classList.add('open');
}

function doSearch(q) {
    q = q.trim();
    if (!q) { closeDropdown(); return; }
    showLoading();

    var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=7&q=' + encodeURIComponent(q);

    fetch(url, { headers: { 'Accept-Language': 'en' } })
        .then(function (r) { return r.json(); })
        .then(function (results) {
            if (!results.length) {
                searchDropdown.innerHTML = '<div class="map-search-no-result">No results found for "<strong>' + q + '</strong>"</div>';
                searchDropdown.classList.add('open');
                return;
            }
            var html = '';
            results.forEach(function (item) {
                html += '<div class="map-search-result-item" data-lat="' + item.lat + '" data-lng="' + item.lon + '">' +
                    '<i class="fas fa-map-marker-alt"></i>' +
                    '<span>' + item.display_name + '</span>' +
                    '</div>';
            });
            searchDropdown.innerHTML = html;
            searchDropdown.classList.add('open');

            // Click a result
            searchDropdown.querySelectorAll('.map-search-result-item').forEach(function (el) {
                el.addEventListener('click', function () {
                    var lat = parseFloat(this.dataset.lat);
                    var lng = parseFloat(this.dataset.lng);
                    map.setView([lat, lng], 15);
                    setPin(lat, lng);
                    searchInput.value = this.querySelector('span').textContent;
                    closeDropdown();
                });
            });
        })
        .catch(function () {
            searchDropdown.innerHTML = '<div class="map-search-no-result">Search failed. Please try again.</div>';
        });
}

// Trigger on button click
searchBtn.addEventListener('click', function () {
    doSearch(searchInput.value);
});

// Trigger on Enter
searchInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        doSearch(this.value);
    }
});

// Live autocomplete (debounced 400ms)
searchInput.addEventListener('input', function () {
    clearTimeout(searchTimer);
    var q = this.value.trim();
    if (q.length < 3) { closeDropdown(); return; }
    searchTimer = setTimeout(function () { doSearch(q); }, 400);
});

// Close dropdown on outside click
document.addEventListener('click', function (e) {
    if (!document.getElementById('mapSearchWrap').contains(e.target)) {
        closeDropdown();
    }
});
</script>

@endsection
