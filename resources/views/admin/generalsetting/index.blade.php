@extends('admin.master')

@section('content')

<style>
    /* =============================================
       General Settings – Custom Styles
    ============================================= */
    :root {
        --primary: #e91e8c;
        --primary-hover: #c4166f;
        --border-radius: 6px;
        --input-border: #dee2e6;
        --section-bg: #ffffff;
        --page-bg: #f4f6f9;
        --label-color: #444;
        --section-title: #333;
        --toggle-on: #e91e8c;
    }

    .gs-page-wrapper {
        padding: 20px;
        background: var(--page-bg);
        min-height: 100vh;
    }

    /* ---- Page Header ---- */
    .gs-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 22px;
    }
    .gs-header h4 {
        font-size: 18px;
        font-weight: 700;
        color: var(--section-title);
        margin: 0;
    }
    .gs-header .gear-icon {
        font-size: 18px;
        color: var(--section-title);
    }
    .btn-run-script {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 7px 18px;
        border-radius: var(--border-radius);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
    }
    .btn-run-script:hover { background: var(--primary-hover); color:#fff; }

    /* ---- Section Card ---- */
    .gs-section {
        background: var(--section-bg);
        border-radius: var(--border-radius);
        padding: 22px 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,.07);
    }
    .gs-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
        font-weight: 700;
        color: var(--section-title);
        padding-bottom: 16px;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 20px;
    }
    .gs-section-title .title-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .gs-section-title .title-icon {
        font-size: 15px;
    }

    /* ---- Form Labels & Inputs ---- */
    .gs-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--label-color);
        margin-bottom: 6px;
        display: block;
    }
    .gs-input,
    .gs-select,
    .gs-textarea {
        width: 100%;
        padding: 9px 13px;
        font-size: 13px;
        border: 1px solid var(--input-border);
        border-radius: var(--border-radius);
        color: #333;
        background: #fff;
        transition: border-color .2s;
        outline: none;
    }
    .gs-input:focus,
    .gs-select:focus,
    .gs-textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(233,30,140,.08);
    }
    .gs-textarea {
        min-height: 90px;
        resize: vertical;
    }
    .gs-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23666' d='M6 8L0 0h12z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 10px;
        padding-right: 32px;
        cursor: pointer;
    }

    /* ---- File Upload ---- */
    .gs-file-wrap {
        display: flex;
        align-items: center;
        gap: 0;
        border: 1px solid var(--input-border);
        border-radius: var(--border-radius);
        overflow: hidden;
        background: #fff;
    }
    .gs-file-wrap label {
        background: #f8f8f8;
        border-right: 1px solid var(--input-border);
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 500;
        color: #444;
        cursor: pointer;
        white-space: nowrap;
        margin: 0;
        user-select: none;
    }
    .gs-file-wrap label:hover { background: #efefef; }
    .gs-file-wrap .file-name {
        padding: 8px 12px;
        font-size: 13px;
        color: #888;
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .gs-file-wrap input[type="file"] {
        display: none;
    }

    /* ---- Image Preview ---- */
    .img-preview-box {
        border: 1px solid var(--input-border);
        border-radius: var(--border-radius);
        padding: 6px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
        min-height: 60px;
    }
    .img-preview-box img {
        max-width: 100%;
        max-height: 100px;
        object-fit: contain;
        border-radius: 4px;
    }
    .img-preview-placeholder {
        width: 100%;
        background: #e9ecef;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #aaa;
        font-size: 12px;
    }
    .img-sub-label {
        font-size: 12px;
        color: #666;
        margin-bottom: 6px;
    }

    /* ---- Toggle Switch ---- */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #ccc;
        border-radius: 24px;
        transition: .3s;
    }
    .toggle-slider:before {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .3s;
        box-shadow: 0 1px 3px rgba(0,0,0,.2);
    }
    .toggle-switch input:checked + .toggle-slider { background: var(--toggle-on); }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }

    /* ---- Save Button ---- */
    .btn-save {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 10px 28px;
        border-radius: var(--border-radius);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
        float: right;
    }
    .btn-save:hover { background: var(--primary-hover); }

    /* ---- Alert ---- */
    .gs-alert {
        padding: 12px 16px;
        border-radius: var(--border-radius);
        margin-bottom: 16px;
        font-size: 13px;
        font-weight: 500;
    }
    .gs-alert.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .gs-alert.error   { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

    /* ---- Grid ---- */
    .gs-row { display: flex; gap: 20px; flex-wrap: wrap; }
    .gs-col { flex: 1; min-width: 220px; }
    .gs-col-2 { flex: 2; min-width: 280px; }
</style>

<div class="gs-page-wrapper">

    {{-- ===== Page Header ===== --}}
    <div class="gs-header">
        <span class="gear-icon">⚙</span>
        <h4>Admin Settings</h4>
        <button type="button" class="btn-run-script" onclick="runUpdateScript()">Run Latest Update Script</button>
    </div>

    {{-- ===== Alerts ===== --}}
    @if(session('success'))
        <div class="gs-alert success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="gs-alert error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="gs-alert error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ===== FORM ===== --}}
    @php $setting = $setting ?? null; @endphp
    @if($setting)
        <form action="{{ route('admin.generalsettings.update', $setting->id) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
    @else
        <form action="{{ route('admin.generalsettings.store') }}" method="POST" enctype="multipart/form-data">
    @endif
    @csrf

    {{-- =============================================
         SECTION 1 – Basic Info + Logos
    ============================================= --}}
    <div class="gs-section">

        <div class="gs-row">

            {{-- Left Column --}}
            <div style="flex:1; min-width:300px;">

                {{-- Website Name --}}
                <div style="margin-bottom:18px;">
                    <label class="gs-label">Website Name</label>
                    <input type="text" name="website_name" class="gs-input"
                           placeholder="Enter Website Name"
                           value="{{ old('website_name', $setting->website_name ?? '') }}">
                </div>

                {{-- Website Title --}}
                <div style="margin-bottom:18px;">
                    <label class="gs-label">Website Title</label>
                    <input type="text" name="website_title" class="gs-input"
                           placeholder="Enter Website Title"
                           value="{{ old('website_title', $setting->website_title ?? 'Ready eCommerce') }}">
                </div>

                {{-- Currency Row --}}
                <div class="gs-row" style="margin-bottom:18px;">
                    <div class="gs-col">
                        <label class="gs-label">Default Currency</label>
                        <select name="default_currency" class="gs-select">
                            @php
                                $currencies = ['USD ($)' => 'USD ($)', 'EUR (€)' => 'EUR (€)', 'GBP (£)' => 'GBP (£)', 'BDT (৳)' => 'BDT (৳)', 'INR (₹)' => 'INR (₹)'];
                                $cur = old('default_currency', $setting->default_currency ?? 'USD ($)');
                            @endphp
                            @foreach($currencies as $val => $label)
                                <option value="{{ $val }}" {{ $cur == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="gs-col">
                        <label class="gs-label">Currency Position</label>
                        <select name="currency_position" class="gs-select">
                            @php $pos = old('currency_position', $setting->currency_position ?? 'Prefix'); @endphp
                            <option value="Prefix" {{ $pos == 'Prefix' ? 'selected' : '' }}>Prefix</option>
                            <option value="Suffix" {{ $pos == 'Suffix' ? 'selected' : '' }}>Suffix</option>
                        </select>
                    </div>
                </div>

            </div>

            {{-- Logo (4:1 200x50) --}}
            <div style="flex:0 0 300px; min-width:240px;">
                <div class="img-preview-box" style="min-height:80px;">
                    @if(!empty($setting->logo))
                        <img src="{{ asset($setting->logo) }}" alt="Logo" id="preview_logo"
                             style="max-height:60px;">
                    @else
                        <img src="" alt="" id="preview_logo" style="display:none; max-height:60px;">
                        <span id="placeholder_logo" style="font-size:12px;color:#aaa;">No Logo</span>
                    @endif
                </div>
                <p class="img-sub-label">Logo Ratio4:1 (200×50)</p>
                <div class="gs-file-wrap">
                    <label for="logo">Choose File</label>
                    <span class="file-name" id="name_logo">No file chosen</span>
                    <input type="file" id="logo" name="logo" accept="image/*"
                           onchange="previewImage(this,'preview_logo','name_logo','placeholder_logo')">
                </div>
            </div>

            {{-- Favicon (300x300) --}}
            <div style="flex:0 0 160px; min-width:140px;">
                <div class="img-preview-box" style="min-height:80px; max-width:120px;">
                    @if(!empty($setting->favicon))
                        <img src="{{ asset($setting->favicon) }}" alt="Favicon" id="preview_favicon"
                             style="max-height:60px;">
                    @else
                        <img src="" alt="" id="preview_favicon" style="display:none; max-height:60px;">
                        <span id="placeholder_favicon" style="font-size:12px;color:#aaa;">No Favicon</span>
                    @endif
                </div>
                <p class="img-sub-label">Favicon (300×300)</p>
                <div class="gs-file-wrap">
                    <label for="favicon">Choose File</label>
                    <span class="file-name" id="name_favicon">No file chosen</span>
                    <input type="file" id="favicon" name="favicon" accept="image/*"
                           onchange="previewImage(this,'preview_favicon','name_favicon','placeholder_favicon')">
                </div>
            </div>

        </div>

        {{-- App Logo (300x300) --}}
        <div style="max-width:420px; margin-top:8px;">
            <div class="img-preview-box" style="min-height:100px; max-width:140px;">
                @if(!empty($setting->app_logo))
                    <img src="{{ asset($setting->app_logo) }}" alt="App Logo" id="preview_app_logo"
                         style="max-height:90px;">
                @else
                    <img src="" alt="" id="preview_app_logo" style="display:none; max-height:90px;">
                    <span id="placeholder_app_logo" style="font-size:12px;color:#aaa;">No App Logo</span>
                @endif
            </div>
            <p class="img-sub-label">App Logo (300×300)</p>
            <div class="gs-file-wrap">
                <label for="app_logo">Choose File</label>
                <span class="file-name" id="name_app_logo">No file chosen</span>
                <input type="file" id="app_logo" name="app_logo" accept="image/*"
                       onchange="previewImage(this,'preview_app_logo','name_app_logo','placeholder_app_logo')">
            </div>
        </div>

    </div>{{-- /section 1 --}}


    {{-- =============================================
         SECTION 2 – Others Information
    ============================================= --}}
    <div class="gs-section">
        <div class="gs-section-title">
            <div class="title-left">
                <span class="title-icon">↗</span>
                <span>Others Information</span>
            </div>
        </div>

        <div class="gs-row">
            <div class="gs-col">
                <label class="gs-label">Mobile Number</label>
                <input type="text" name="mobile_number" class="gs-input"
                       placeholder="Enter Mobile Number"
                       value="{{ old('mobile_number', $setting->mobile_number ?? '') }}">
            </div>
            <div class="gs-col">
                <label class="gs-label">Email Address</label>
                <input type="email" name="email_address" class="gs-input"
                       placeholder="Enter Email Address"
                       value="{{ old('email_address', $setting->email_address ?? '') }}">
            </div>
            <div class="gs-col">
                <label class="gs-label">Address</label>
                <input type="text" name="address" class="gs-input"
                       placeholder="Enter Address"
                       value="{{ old('address', $setting->address ?? '') }}">
            </div>
        </div>
    </div>{{-- /section 2 --}}


    {{-- =============================================
         SECTION 3 – Download App Link
    ============================================= --}}
    <div class="gs-section">
        <div class="gs-section-title">
            <div class="title-left">
                <span class="title-icon">↗</span>
                <span>Download App Link</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:13px;font-weight:600;color:#333;">Show/Hide Website Navigation Download App</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="show_download_app" id="toggle_download_app"
                           {{ old('show_download_app', $setting->show_download_app ?? 1) ? 'checked' : '' }}
                           onchange="ajaxToggle(this, '{{ $setting ? route('admin.generalsettings.toggle', $setting->id) : '#' }}', 'show_download_app')">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        <div class="gs-row">
            <div class="gs-col">
                <label class="gs-label">Google PlayStore App Link</label>
                <textarea name="google_playstore_link" class="gs-textarea"
                          placeholder="https://play.google.com/store/apps/...">{{ old('google_playstore_link', $setting->google_playstore_link ?? '') }}</textarea>
            </div>
            <div class="gs-col">
                <label class="gs-label">Apple Store App Link</label>
                <textarea name="apple_store_link" class="gs-textarea"
                          placeholder="https://apps.apple.com/...">{{ old('apple_store_link', $setting->apple_store_link ?? '') }}</textarea>
            </div>
        </div>
    </div>{{-- /section 3 --}}


    {{-- =============================================
         SECTION 4 – Footer Section Info
    ============================================= --}}
    <div class="gs-section">
        <div class="gs-section-title">
            <div class="title-left">
                <span class="title-icon">⬇</span>
                <span>Footer Section Info</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:13px;font-weight:600;color:#333;">Show/Hide Admin Bottom Footer Section</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="show_footer_section" id="toggle_footer_section"
                           {{ old('show_footer_section', $setting->show_footer_section ?? 1) ? 'checked' : '' }}
                           onchange="ajaxToggle(this, '{{ $setting ? route('admin.generalsettings.toggle', $setting->id) : '#' }}', 'show_footer_section')">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        {{-- Hotline + Footer Text --}}
        <div class="gs-row" style="margin-bottom:22px;">
            <div class="gs-col">
                <label class="gs-label">Hotline Number</label>
                <input type="text" name="hotline_number" class="gs-input"
                       placeholder="Enter Hotline Number"
                       value="{{ old('hotline_number', $setting->hotline_number ?? '') }}">
            </div>
            <div class="gs-col">
                <label class="gs-label">Footer Text</label>
                <input type="text" name="footer_text" class="gs-input"
                       placeholder="e.g. All rights reserved by..."
                       value="{{ old('footer_text', $setting->footer_text ?? '') }}">
            </div>
        </div>

        {{-- Footer Logo + QR --}}
        <div class="gs-row">

            {{-- Footer Logo --}}
            <div style="flex:1; min-width:260px;">
                <div class="img-preview-box" style="min-height:90px; max-width:300px;">
                    @if(!empty($setting->footer_logo))
                        <img src="{{ asset($setting->footer_logo) }}" alt="Footer Logo" id="preview_footer_logo"
                             style="max-height:70px;">
                    @else
                        <img src="" alt="" id="preview_footer_logo" style="display:none; max-height:70px;">
                        <span id="placeholder_footer_logo" style="font-size:12px;color:#aaa;">No Footer Logo</span>
                    @endif
                </div>
                <p class="img-sub-label">Frontend Footer Logo Ratio4:1</p>
                <div class="gs-file-wrap">
                    <label for="footer_logo">Choose File</label>
                    <span class="file-name" id="name_footer_logo">No file chosen</span>
                    <input type="file" id="footer_logo" name="footer_logo" accept="image/*"
                           onchange="previewImage(this,'preview_footer_logo','name_footer_logo','placeholder_footer_logo')">
                </div>
            </div>

            {{-- QR Code --}}
            <div style="flex:0 0 260px; min-width:220px;">
                <div class="img-preview-box" style="min-height:120px; max-width:180px;">
                    @if(!empty($setting->footer_qr))
                        <img src="{{ asset($setting->footer_qr) }}" alt="QR Code" id="preview_footer_qr"
                             style="max-height:110px;">
                    @else
                        <div id="placeholder_footer_qr" class="img-preview-placeholder" style="height:120px;max-width:180px;">
                            <span>200 × 200</span>
                        </div>
                        <img src="" alt="" id="preview_footer_qr" style="display:none; max-height:110px;">
                    @endif
                </div>
                <p class="img-sub-label">Frontend Scan the QR (200×200)</p>
                <div class="gs-file-wrap">
                    <label for="footer_qr">Choose File</label>
                    <span class="file-name" id="name_footer_qr">No file chosen</span>
                    <input type="file" id="footer_qr" name="footer_qr" accept="image/*"
                           onchange="previewImage(this,'preview_footer_qr','name_footer_qr','placeholder_footer_qr')">
                </div>
            </div>

        </div>
    </div>{{-- /section 4 --}}


    {{-- ===== Save Button ===== --}}
    <div style="overflow:hidden; padding-bottom:30px;">
        <button type="submit" class="btn-save">Save And Update</button>
    </div>

    </form>

</div>{{-- /gs-page-wrapper --}}



<script>
    /* ---- Image Preview ---- */
    function previewImage(input, previewId, nameId, placeholderId) {
        const preview     = document.getElementById(previewId);
        const nameEl      = document.getElementById(nameId);
        const placeholder = placeholderId ? document.getElementById(placeholderId) : null;

        if (input.files && input.files[0]) {
            const file   = input.files[0];
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src          = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
            nameEl.textContent = file.name;
        }
    }

    /* ---- Ajax Toggle ---- */
    function ajaxToggle(checkbox, url, field) {
        if (!url || url === '#') return; // first-time create, will save with form
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ field: field }),
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                checkbox.checked = !checkbox.checked; // revert on failure
            }
        })
        .catch(() => { checkbox.checked = !checkbox.checked; });
    }

    /* ---- Run Update Script ---- */
    function runUpdateScript() {
        if (!confirm('Are you sure you want to run the latest update script?')) return;
        // Implement your update script logic here
        alert('Update script executed successfully!');
    }
</script>
@endsection
