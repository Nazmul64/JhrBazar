@extends('admin.master')

@section('content')

<style>
    /* =============================================
       General Settings – Custom Styles
    ============================================= */
    :root {
        --primary: #e91e8c;
        --primary-hover: #c4166f;
        --border-radius: 12px;
        --input-border: var(--border-color);
        --section-bg: var(--bg-card);
        --page-bg: var(--bg-body);
        --label-color: var(--text-main);
        --section-title: var(--text-main);
        --toggle-on: #e91e8c;
    }

    .gs-page-wrapper {
        padding: 30px;
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

    /* ---- Action & Reset Buttons ---- */
    .reset-buttons {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 24px;
        padding-bottom: 30px;
        border-top: 1px solid #f0f0f0;
        margin-top: 25px;
    }
    .reset-btn {
        padding: 10px 24px;
        border-radius: var(--border-radius);
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        outline: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        line-height: 1;
    }
    .reset-btn-neutral {
        background: transparent;
        color: #6c757d;
        border: 1.5px solid #d1d5db;
    }
    .reset-btn-neutral:hover {
        background: rgba(0, 0, 0, 0.05);
        border-color: #9ca3af;
        color: #333;
    }
    .reset-btn-warning {
        background: transparent;
        color: #d97706;
        border: 1.5px solid #fcd34d;
    }
    .reset-btn-warning:hover {
        background: #fffbeb;
        border-color: #f59e0b;
        color: #b45309;
    }
    .btn-save {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 10.5px 28px;
        border-radius: var(--border-radius);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        line-height: 1;
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
                                $cur = old('default_currency', $setting ? $setting->getRawOriginal('default_currency') : 'USD ($)');
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

                {{-- Admin Theme --}}
                <div style="margin-bottom:18px;">
                    <label class="gs-label">Admin Theme Style</label>
                    <select name="admin_theme" class="gs-select">
                        <option value="light" {{ ($setting->admin_theme ?? 'light') == 'light' ? 'selected' : '' }}>Light Mode (Classic)</option>
                        <option value="dark" {{ ($setting->admin_theme ?? 'light') == 'dark' ? 'selected' : '' }}>Dark Mode (Premium)</option>
                    </select>
                    <div class="img-sub-label">Choose the visual style of your administration panel.</div>
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
        <div class="gs-row" style="margin-top: 18px;">
            <div class="gs-col">
                <label class="gs-label">Trade License Number</label>
                <input type="text" name="trade_license_number" class="gs-input"
                       placeholder="Enter Trade License Number"
                       value="{{ old('trade_license_number', $setting->trade_license_number ?? '') }}">
            </div>
            <div class="gs-col">
                <label class="gs-label">DBID Number</label>
                <input type="text" name="dbid_number" class="gs-input"
                       placeholder="Enter DBID Number"
                       value="{{ old('dbid_number', $setting->dbid_number ?? '') }}">
            </div>
            <div class="gs-col">
                {{-- Spacer --}}
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
         SECTION 5 – Top Rated Shops Setting
    ============================================= --}}
    <div class="gs-section">
        <div class="gs-section-title">
            <div class="title-left">
                <span class="title-icon">🏪</span>
                <span>Top Rated Shops Section</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:13px;font-weight:600;color:#333;">Show/Hide Top Rated Shops on Home Page</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="top_rated_shops_status" id="toggle_top_rated_shops"
                           {{ old('top_rated_shops_status', $setting->top_rated_shops_status ?? 1) ? 'checked' : '' }}
                           onchange="ajaxToggle(this, '{{ $setting ? route('admin.generalsettings.toggle', $setting->id) : '#' }}', 'top_rated_shops_status')">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>{{-- /section 5 --}}


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
        <div class="gs-row" style="margin-bottom:22px;">
            <div class="gs-col">
                <label class="gs-label">Footer Copyright Text</label>
                <input type="text" name="footer_copyright_text" class="gs-input"
                       placeholder="e.g. Copyright © 2026 JhrBazar. All rights reserved."
                       value="{{ old('footer_copyright_text', $setting->footer_copyright_text ?? '') }}">
                <small class="text-muted" style="font-size:11px;">এটি ফুটারের সবার নিচে কপিরাইট সেকশনে দেখাবে। খালি রাখলে ডিফল্ট ভ্যালু দেখাবে।</small>
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

    {{-- =============================================
         SECTION 5 – Appearance & Typography
    ============================================= --}}
    <div class="gs-section">
        <div class="gs-section-title">
            <div class="title-left">
                <span class="title-icon">🎨</span>
                <span>Appearance & Typography</span>
            </div>
        </div>

        <div class="gs-row" style="margin-bottom:22px;">
            <div class="gs-col">
                <label class="gs-label">Primary Color (Buttons, Highlights)</label>
                <div style="display: flex; gap: 10px;">
                    <input type="color" value="{{ old('primary_color', $setting->primary_color ?? '#57b500') }}" oninput="this.nextElementSibling.value=this.value" style="height:40px;width:50px;border:none;border-radius:4px;cursor:pointer;">
                    <input type="text" name="primary_color" class="gs-input" value="{{ old('primary_color', $setting->primary_color ?? '#57b500') }}" oninput="this.previousElementSibling.value=this.value" style="flex:1;">
                </div>
            </div>
            <div class="gs-col">
                <label class="gs-label">Top Header Color</label>
                <div style="display: flex; gap: 10px;">
                    <input type="color" value="{{ old('top_header_color', $setting->top_header_color ?? '#57b500') }}" oninput="this.nextElementSibling.value=this.value" style="height:40px;width:50px;border:none;border-radius:4px;cursor:pointer;">
                    <input type="text" name="top_header_color" class="gs-input" value="{{ old('top_header_color', $setting->top_header_color ?? '#57b500') }}" oninput="this.previousElementSibling.value=this.value" style="flex:1;">
                </div>
            </div>
        </div>

        <div class="gs-row" style="margin-bottom:22px;">
            <div class="gs-col">
                <label class="gs-label">Main Header Color</label>
                <div style="display: flex; gap: 10px;">
                    <input type="color" value="{{ old('header_color', $setting->header_color ?? '#ffffff') }}" oninput="this.nextElementSibling.value=this.value" style="height:40px;width:50px;border:none;border-radius:4px;cursor:pointer;">
                    <input type="text" name="header_color" class="gs-input" value="{{ old('header_color', $setting->header_color ?? '#ffffff') }}" oninput="this.previousElementSibling.value=this.value" style="flex:1;">
                </div>
            </div>
            <div class="gs-col">
                <label class="gs-label">Footer Background Color</label>
                <div style="display: flex; gap: 10px;">
                    <input type="color" value="{{ old('footer_color', $setting->footer_color ?? '#ffffff') }}" oninput="this.nextElementSibling.value=this.value" style="height:40px;width:50px;border:none;border-radius:4px;cursor:pointer;">
                    <input type="text" name="footer_color" class="gs-input" value="{{ old('footer_color', $setting->footer_color ?? '#ffffff') }}" oninput="this.previousElementSibling.value=this.value" style="flex:1;">
                </div>
            </div>
        </div>
        <div class="gs-row" style="margin-bottom:22px;">
            <div class="gs-col">
                <label class="gs-label">Tax Header Background Color</label>
                <div style="display: flex; gap: 10px;">
                    <input type="color" value="{{ old('tax_header_color', $setting->tax_header_color ?? '#f8f9fa') }}" oninput="this.nextElementSibling.value=this.value" style="height:40px;width:50px;border:none;border-radius:4px;cursor:pointer;">
                    <input type="text" name="tax_header_color" class="gs-input" value="{{ old('tax_header_color', $setting->tax_header_color ?? '#f8f9fa') }}" oninput="this.previousElementSibling.value=this.value" style="flex:1;">
                </div>
            </div>
            <div class="gs-col">
                <label class="gs-label">Tax Header Text Color</label>
                <div style="display: flex; gap: 10px;">
                    <input type="color" value="{{ old('tax_header_text_color', $setting->tax_header_text_color ?? '#1a1a2e') }}" oninput="this.nextElementSibling.value=this.value" style="height:40px;width:50px;border:none;border-radius:4px;cursor:pointer;">
                    <input type="text" name="tax_header_text_color" class="gs-input" value="{{ old('tax_header_text_color', $setting->tax_header_text_color ?? '#1a1a2e') }}" oninput="this.previousElementSibling.value=this.value" style="flex:1;">
                </div>
            </div>
        </div>
        <div class="gs-row" style="margin-bottom:22px;">
            <div class="gs-col">
                <label class="gs-label">Important Background Color</label>
                <div style="display: flex; gap: 10px;">
                    <input type="color" value="{{ old('important_background_color', $setting->important_background_color ?? '#ffffff') }}" oninput="this.nextElementSibling.value=this.value" style="height:40px;width:50px;border:none;border-radius:4px;cursor:pointer;">
                    <input type="text" name="important_background_color" class="gs-input" value="{{ old('important_background_color', $setting->important_background_color ?? '#ffffff') }}" oninput="this.previousElementSibling.value=this.value" style="flex:1;">
                </div>
            </div>
            <div class="gs-col">
                <label class="gs-label">Important Color</label>
                <div style="display: flex; gap: 10px;">
                    <input type="color" value="{{ old('important_color', $setting->important_color ?? '#ff0000') }}" oninput="this.nextElementSibling.value=this.value" style="height:40px;width:50px;border:none;border-radius:4px;cursor:pointer;">
                    <input type="text" name="important_color" class="gs-input" value="{{ old('important_color', $setting->important_color ?? '#ff0000') }}" oninput="this.previousElementSibling.value=this.value" style="flex:1;">
                </div>
            </div>
        </div>
        <div class="gs-row" style="margin-bottom:22px;">
            <div class="gs-col">
                <label class="gs-label">Button Background Color</label>
                <div style="display: flex; gap: 10px;">
                    <input type="color" value="{{ old('button_color', $setting->button_color ?? '#57b500') }}" oninput="this.nextElementSibling.value=this.value" style="height:40px;width:50px;border:none;border-radius:4px;cursor:pointer;">
                    <input type="text" name="button_color" class="gs-input" value="{{ old('button_color', $setting->button_color ?? '#57b500') }}" oninput="this.previousElementSibling.value=this.value" style="flex:1;">
                </div>
            </div>
            <div class="gs-col">
                <label class="gs-label">Button Hover Color</label>
                <div style="display: flex; gap: 10px;">
                    <input type="color" value="{{ old('button_hover_color', $setting->button_hover_color ?? '#4a9a00') }}" oninput="this.nextElementSibling.value=this.value" style="height:40px;width:50px;border:none;border-radius:4px;cursor:pointer;">
                    <input type="text" name="button_hover_color" class="gs-input" value="{{ old('button_hover_color', $setting->button_hover_color ?? '#4a9a00') }}" oninput="this.previousElementSibling.value=this.value" style="flex:1;">
                </div>
            </div>
        </div>

        <div class="gs-row" style="margin-bottom:22px;">
            <div class="gs-col">
                <label class="gs-label">Footer Text Color</label>
                <div style="display: flex; gap: 10px;">
                    <input type="color" value="{{ old('footer_text_color', $setting->footer_text_color ?? '#ffffff') }}" oninput="this.nextElementSibling.value=this.value" style="height:40px;width:50px;border:none;border-radius:4px;cursor:pointer;">
                    <input type="text" name="footer_text_color" class="gs-input" value="{{ old('footer_text_color', $setting->footer_text_color ?? '#ffffff') }}" oninput="this.previousElementSibling.value=this.value" style="flex:1;">
                </div>
            </div>
            <div class="gs-col">
                {{-- Placeholder --}}
            </div>
        </div>

        <div class="gs-row">
            <div class="gs-col">
                <label class="gs-label">Font Family</label>
                <select name="font_family" class="gs-input">
                    @php
                        $currentFont = old('font_family', $setting->font_family ?? 'Arial, sans-serif');
                        $fonts = [
                            'Arial, sans-serif' => 'Arial (System)',
                            'Roboto, sans-serif' => 'Roboto',
                            'Open Sans, sans-serif' => 'Open Sans',
                            'Lato, sans-serif' => 'Lato',
                            'Montserrat, sans-serif' => 'Montserrat',
                            'Poppins, sans-serif' => 'Poppins',
                            'Inter, sans-serif' => 'Inter',
                            'Nunito, sans-serif' => 'Nunito',
                            'DM Sans, sans-serif' => 'DM Sans',
                            'Sora, sans-serif' => 'Sora',
                            'Ubuntu, sans-serif' => 'Ubuntu',
                            'Merriweather, serif' => 'Merriweather',
                            'Quicksand, sans-serif' => 'Quicksand',
                            'Titillium Web, sans-serif' => 'Titillium Web',
                            'Playfair Display, serif' => 'Playfair Display',
                            'Oswald, sans-serif' => 'Oswald',
                            'Raleway, sans-serif' => 'Raleway',
                            'Hind Siliguri, sans-serif' => 'Hind Siliguri (Bengali)',
                            'Noto Sans Bengali, sans-serif' => 'Noto Sans Bengali (Bengali)',
                            'SolaimanLipi, sans-serif' => 'SolaimanLipi (Bengali)',
                            'Hind, sans-serif' => 'Hind (Bengali)',
                            'Mina, sans-serif' => 'Mina (Bengali)',
                        ];
                    @endphp
                    @foreach($fonts as $value => $label)
                        <option value="{{ $value }}" {{ $currentFont == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="gs-col">
                <label class="gs-label">Base Font Size</label>
                <input type="text" name="font_size" class="gs-input" placeholder="e.g. 14px or 1rem" value="{{ old('font_size', $setting->font_size ?? '14px') }}">
            </div>
        </div>

        <div class="gs-row" style="margin-top: 22px;">
            <div class="gs-col">
                <label class="gs-label">Product Title Size (Desktop)</label>
                <input type="text" name="product_title_size_desktop" class="gs-input" placeholder="e.g. 14px" value="{{ old('product_title_size_desktop', $setting->product_title_size_desktop ?? '14px') }}">
            </div>
            <div class="gs-col">
                <label class="gs-label">Product Title Size (Mobile)</label>
                <input type="text" name="product_title_size_mobile" class="gs-input" placeholder="e.g. 12px" value="{{ old('product_title_size_mobile', $setting->product_title_size_mobile ?? '12px') }}">
            </div>
        </div>

        <div class="gs-row" style="margin-top: 22px;">
            <div class="gs-col">
                <label class="gs-label">Current Price Size</label>
                <input type="text" name="product_price_size" class="gs-input" placeholder="e.g. 15px" value="{{ old('product_price_size', $setting->product_price_size ?? '15px') }}">
            </div>
            <div class="gs-col">
                <label class="gs-label">Old Price Size</label>
                <input type="text" name="product_old_price_size" class="gs-input" placeholder="e.g. 12px" value="{{ old('product_old_price_size', $setting->product_old_price_size ?? '12px') }}">
            </div>
        </div>
    </div>

    {{-- =============================================
         SECTION 5.5 – Tracking & Analytics
    ============================================= --}}
    <div class="gs-section">
        <div class="gs-section-title">
            <div class="title-left">
                <span class="title-icon">📊</span>
                <span>Tracking & Analytics</span>
            </div>
        </div>

        <div class="gs-row">
            {{-- Google Analytics --}}
            <div class="gs-col" style="border: 1px solid var(--border); padding: 20px; border-radius: 12px; background: #fcfcfc;">
                <label class="gs-label fw-bold"><i class="bi bi-google text-primary"></i> Google Analytics (Universal/GA4)</label>
                <input type="text" name="google_analytics_id" class="gs-input mb-3"
                       placeholder="UA-XXXXX-Y or G-XXXXXXX"
                       value="{{ old('google_analytics_id', $setting->google_analytics_id ?? '') }}">

                <div style="display:flex;align-items:center;gap:10px;">
                    <label class="toggle-switch">
                        <input type="checkbox" name="enable_analytics"
                               {{ old('enable_analytics', $setting->enable_analytics ?? 0) ? 'checked' : '' }}
                               onchange="ajaxToggle(this, '{{ $setting ? route('admin.generalsettings.toggle', $setting->id) : '#' }}', 'enable_analytics')">
                        <span class="toggle-slider"></span>
                    </label>
                    <span style="font-size:13px;font-weight:600;color:#333;">Enable Analytics Tracking</span>
                </div>
            </div>

            {{-- Facebook Pixel --}}
            <div class="gs-col" style="border: 1px solid var(--border); padding: 20px; border-radius: 12px; background: #fcfcfc;">
                <label class="gs-label fw-bold"><i class="bi bi-facebook text-primary"></i> Facebook Pixel ID</label>
                <input type="text" name="facebook_pixel_id" class="gs-input mb-3"
                       placeholder="Enter Pixel ID"
                       value="{{ old('facebook_pixel_id', $setting->facebook_pixel_id ?? '') }}">

                <div style="display:flex;align-items:center;gap:10px;">
                    <label class="toggle-switch">
                        <input type="checkbox" name="enable_pixel"
                               {{ old('enable_pixel', $setting->enable_pixel ?? 0) ? 'checked' : '' }}
                               onchange="ajaxToggle(this, '{{ $setting ? route('admin.generalsettings.toggle', $setting->id) : '#' }}', 'enable_pixel')">
                        <span class="toggle-slider"></span>
                    </label>
                    <span style="font-size:13px;font-weight:600;color:#333;">Enable Facebook Pixel Tracking</span>
                </div>
            </div>
        </div>

        <div class="gs-row" style="margin-top: 20px;">
            {{-- Google Tag Manager --}}
            <div class="gs-col" style="border: 1px solid var(--border); padding: 20px; border-radius: 12px; background: #fcfcfc;">
                <label class="gs-label fw-bold"><i class="bi bi-code-slash text-success"></i> Google Tag Manager (GTM) ID</label>
                <input type="text" name="gtm_id" class="gs-input mb-3"
                       placeholder="GTM-XXXXXXX"
                       value="{{ old('gtm_id', $setting->gtm_id ?? '') }}">

                <div style="display:flex;align-items:center;gap:10px;">
                    <label class="toggle-switch">
                        <input type="checkbox" name="enable_gtm"
                               {{ old('enable_gtm', $setting->enable_gtm ?? 0) ? 'checked' : '' }}
                               onchange="ajaxToggle(this, '{{ $setting ? route('admin.generalsettings.toggle', $setting->id) : '#' }}', 'enable_gtm')">
                        <span class="toggle-slider"></span>
                    </label>
                    <span style="font-size:13px;font-weight:600;color:#333;">Enable GTM Tracking</span>
                </div>
            </div>
            
            {{-- Customer Visit Notification --}}
            <div class="gs-col" style="border: 1px solid var(--border); padding: 20px; border-radius: 12px; background: #fcfcfc;">
                <label class="gs-label fw-bold"><i class="bi bi-bell text-danger"></i> Returning Customer Visit Alert</label>
                <p style="font-size:12px; color:#666; margin-bottom: 18px;">Show real-time notification popups in the admin panel when registered customers visit the website.</p>

                <div style="display:flex;align-items:center;gap:10px;">
                    <label class="toggle-switch">
                        <input type="checkbox" name="customer_visit_notification_status"
                               {{ old('customer_visit_notification_status', $setting->customer_visit_notification_status ?? 1) ? 'checked' : '' }}
                               onchange="ajaxToggle(this, '{{ $setting ? route('admin.generalsettings.toggle', $setting->id) : '#' }}', 'customer_visit_notification_status')">
                        <span class="toggle-slider"></span>
                    </label>
                    <span style="font-size:13px;font-weight:600;color:#333;">Enable Real-time Visitor Alert</span>
                </div>
            </div>
        </div>
    </div>

    {{-- =============================================
         SECTION 5.6 – Security / IP Block Settings
    ============================================= --}}
    <div class="gs-section">
        <div class="gs-section-title">
            <div class="title-left">
                <span class="title-icon">🔒</span>
                <span>Security / IP Block Settings</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:13px;font-weight:600;color:#333;">Enable Automated IP & Fraud Block</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="ip_block_status" id="toggle_ip_block"
                           {{ old('ip_block_status', $setting->ip_block_status ?? 0) ? 'checked' : '' }}
                           onchange="ajaxToggle(this, '{{ $setting ? route('admin.generalsettings.toggle', $setting->id) : '#' }}', 'ip_block_status')">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
        <p style="font-size:12px; color:#666; margin: 0;">যখন এই সুইচটি অন (Enable) থাকবে, তখন সিস্টেম অটোমেটিক ফেক অর্ডারকারী বা সন্দেহভাজন আইপি ট্র্যাক করে ব্লক করবে। তবে কাস্টমারের প্রথম অর্ডার করার ক্ষেত্রে কোনো ব্লক কার্যকর হবে না। সুইচটি অফ থাকলে আইপি ব্লক সিস্টেম সম্পূর্ণ নিষ্ক্রিয় থাকবে।</p>
    </div>

    {{-- =============================================
         SECTION 6 – Layout & Product Grid
    ============================================= --}}
    <div class="gs-section">
        <div class="gs-section-title">
            <div class="title-left">
                <span class="title-icon">📐</span>
                <span>Layout & Product Grid Settings</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:13px;font-weight:600;color:#333;">Show/Hide Product Stats (Badges/Rating/Sold)</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="show_product_stats" id="toggle_product_stats"
                           {{ old('show_product_stats', $setting->show_product_stats ?? 1) ? 'checked' : '' }}
                           onchange="ajaxToggle(this, '{{ $setting ? route('admin.generalsettings.toggle', $setting->id) : '#' }}', 'show_product_stats')">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        <div class="gs-row" style="margin-bottom:22px;">
            <div class="gs-col">
                <label class="gs-label">Global Layout Style</label>
                <select name="layout_style" class="gs-input">
                    @php $currentLayout = old('layout_style', $setting->layout_style ?? 'container'); @endphp
                    <option value="container" {{ $currentLayout == 'container' ? 'selected' : '' }}>Container (Centered)</option>
                    <option value="fluid" {{ $currentLayout == 'fluid' ? 'selected' : '' }}>Full Width (Fluid)</option>
                </select>
            </div>
            <div class="gs-col">
                <label class="gs-label">Product Card Width</label>
                <input type="text" name="product_card_width" class="gs-input" placeholder="e.g. 100% or 200px" value="{{ old('product_card_width', $setting->product_card_width ?? '100%') }}">
            </div>
            <div class="gs-col">
                <label class="gs-label">Product Card Height</label>
                <input type="text" name="product_card_height" class="gs-input" placeholder="e.g. auto or 350px" value="{{ old('product_card_height', $setting->product_card_height ?? 'auto') }}">
            </div>
        </div>

        <div class="gs-row">
            <div class="gs-col">
                <label class="gs-label">Products Per Row (Mobile)</label>
                <select name="products_per_row_mobile" class="gs-input">
                    @php $currentMobileGrid = old('products_per_row_mobile', $setting->products_per_row_mobile ?? 2); @endphp
                    <option value="1" {{ $currentMobileGrid == 1 ? 'selected' : '' }}>1 Product</option>
                    <option value="2" {{ $currentMobileGrid == 2 ? 'selected' : '' }}>2 Products</option>
                    <option value="3" {{ $currentMobileGrid == 3 ? 'selected' : '' }}>3 Products</option>
                </select>
            </div>
            <div class="gs-col">
                <label class="gs-label">Products Per Row (Large Devices)</label>
                <select name="products_per_row_desktop" class="gs-input">
                    @php $currentDesktopGrid = old('products_per_row_desktop', $setting->products_per_row_desktop ?? 6); @endphp
                    <option value="2" {{ $currentDesktopGrid == 2 ? 'selected' : '' }}>2 Products</option>
                    <option value="3" {{ $currentDesktopGrid == 3 ? 'selected' : '' }}>3 Products</option>
                    <option value="4" {{ $currentDesktopGrid == 4 ? 'selected' : '' }}>4 Products</option>
                    <option value="5" {{ $currentDesktopGrid == 5 ? 'selected' : '' }}>5 Products</option>
                    <option value="6" {{ $currentDesktopGrid == 6 ? 'selected' : '' }}>6 Products</option>
                    <option value="8" {{ $currentDesktopGrid == 8 ? 'selected' : '' }}>8 Products</option>
                </select>
            </div>
        </div>
    </div>

    {{-- =============================================
         SECTION 7 – Offer Marquee
    ============================================= --}}
    <div class="gs-section">
        <div class="gs-section-title">
            <div class="title-left">
                <span class="title-icon">📢</span>
                <span>Offer Marquee</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:13px;font-weight:600;color:#333;">Show/Hide Marquee on Home Page</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="show_marquee" id="toggle_marquee"
                           {{ old('show_marquee', $setting->show_marquee ?? 0) ? 'checked' : '' }}
                           onchange="ajaxToggle(this, '{{ $setting ? route('admin.generalsettings.toggle', $setting->id) : '#' }}', 'show_marquee')">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        <div class="gs-row">
            <div class="gs-col" style="flex: 1 1 100%;">
                <label class="gs-label">Marquee Text (Scrolling Offer Text)</label>
                <textarea name="marquee_text" class="gs-input" rows="2" placeholder="e.g. 🔥 Flash Sale! Get 50% Off on all electronics today! 🔥">{{ old('marquee_text', $setting->marquee_text ?? '') }}</textarea>
            </div>
        </div>

        <div class="gs-row" style="margin-top: 18px;">
            <div class="gs-col" style="flex: 1 1 100%;">
                <label class="gs-label">Free Shipping Announcement Text (Top Bar)</label>
                <input type="text" name="free_shipping_text" class="gs-input" placeholder="e.g. ⚡ Free shipping on orders over 5,000 BDT" value="{{ old('free_shipping_text', $setting->free_shipping_text ?? '') }}">
            </div>
        </div>
    </div>

    {{-- =============================================
         SECTION 8 – Slider & Category Dimensions
    ============================================= --}}
    <div class="gs-section">
        <div class="gs-section-title">
            <div class="title-left">
                <span class="title-icon">🖼️</span>
                <span>Slider & Category Settings</span>
            </div>
        </div>

        <div class="gs-row">
            <div class="gs-col">
                <label class="gs-label">Main Slider Height (Desktop)</label>
                <input type="text" name="slider_height" class="gs-input" value="{{ old('slider_height', $setting->slider_height ?? '420px') }}" placeholder="e.g. 400px or auto">
            </div>
            <div class="gs-col">
                <label class="gs-label">Main Slider Height (Mobile)</label>
                <input type="text" name="slider_height_mobile" class="gs-input" value="{{ old('slider_height_mobile', $setting->slider_height_mobile ?? '200px') }}" placeholder="e.g. 200px">
            </div>
            <div class="gs-col">
                <label class="gs-label">Main Slider Auto-Slide Speed (Seconds)</label>
                <input type="number" name="slider_speed" class="gs-input" value="{{ old('slider_speed', $setting->slider_speed ?? 5) }}" placeholder="e.g. 5">
            </div>
        </div>

        <div class="gs-row" style="margin-top: 20px;">
            <div class="gs-col">
                <label class="gs-label">Product Image Height (Desktop)</label>
                <input type="text" name="product_img_height_desktop" class="gs-input" value="{{ old('product_img_height_desktop', $setting->product_img_height_desktop ?? '200px') }}" placeholder="e.g. 200px">
            </div>
            <div class="gs-col">
                <label class="gs-label">Product Image Height (Mobile)</label>
                <input type="text" name="product_img_height_mobile" class="gs-input" value="{{ old('product_img_height_mobile', $setting->product_img_height_mobile ?? '150px') }}" placeholder="e.g. 150px">
            </div>
        </div>

        <div class="gs-row" style="margin-top: 20px;">
            <div class="gs-col">
                <label class="gs-label">Category Image Width</label>
                <input type="text" name="category_img_width" class="gs-input" value="{{ old('category_img_width', $setting->category_img_width ?? '100%') }}" placeholder="e.g. 100% or 120px">
            </div>
            <div class="gs-col">
                <label class="gs-label">Category Image Height</label>
                <input type="text" name="category_img_height" class="gs-input" value="{{ old('category_img_height', $setting->category_img_height ?? '100px') }}" placeholder="e.g. 100px or 120px">
            </div>
            <div class="gs-col">
                <label class="gs-label">Category Slider Auto-Slide Speed (Seconds)</label>
                <input type="number" name="category_slide_speed" class="gs-input" value="{{ old('category_slide_speed', $setting->category_slide_speed ?? 4) }}" placeholder="e.g. 4">
            </div>
        </div>

        <div class="gs-row" style="margin-top: 20px;">
            <div class="gs-col">
                <label class="gs-label">Category Menu Behavior (Homepage Sidebar)</label>
                <select name="sidebar_behavior" class="gs-input">
                    @php $currentSidebarBehavior = old('sidebar_behavior', $setting->sidebar_behavior ?? 'fixed'); @endphp
                    <option value="fixed" {{ $currentSidebarBehavior == 'fixed' ? 'selected' : '' }}>Fixed (Always Open on Home)</option>
                    <option value="hover" {{ $currentSidebarBehavior == 'hover' ? 'selected' : '' }}>Dropdown (Hover/Click to Open)</option>
                </select>
            </div>
            <div class="gs-col">
                {{-- Spacer --}}
            </div>
            <div class="gs-col">
                {{-- Spacer --}}
            </div>
        </div>

        <div class="gs-row" style="margin-top: 20px;">
            <div class="gs-col">
                <label class="gs-label">Category Image Shape</label>
                <select name="category_shape" class="gs-input">
                    @php $currentShape = old('category_shape', $setting->category_shape ?? 'rounded'); @endphp
                    <option value="square" {{ $currentShape == 'square' ? 'selected' : '' }}>Square (No Radius)</option>
                    <option value="rounded" {{ $currentShape == 'rounded' ? 'selected' : '' }}>Rounded (Soft Corners)</option>
                    <option value="circle" {{ $currentShape == 'circle' ? 'selected' : '' }}>Circle (50% Radius)</option>
                </select>
            </div>
            <div class="gs-col">
                <label class="gs-label">Category Component Behavior</label>
                <select name="category_behavior" class="gs-input">
                    @php $currentBehavior = old('category_behavior', $setting->category_behavior ?? 'slider'); @endphp
                    <option value="slider" {{ $currentBehavior == 'slider' ? 'selected' : '' }}>Horizontal Slider (Grab & Scroll)</option>
                    <option value="grid" {{ $currentBehavior == 'grid' ? 'selected' : '' }}>Fixed Grid (Multi-line layout)</option>
                </select>
            </div>
            <div class="gs-col">
                <label class="gs-label">Home Page Loader Status</label>
                <select name="loader_status" class="gs-input">
                    @php $currentLoader = old('loader_status', $setting->loader_status ?? 1); @endphp
                    <option value="1" {{ $currentLoader == 1 ? 'selected' : '' }}>ON (Show Loading Indicator)</option>
                    <option value="0" {{ $currentLoader == 0 ? 'selected' : '' }}>OFF (Direct Load)</option>
                </select>
            </div>
        </div>
    {{-- =============================================
         SECTION 9 – Membership & Payment Section
    ============================================= --}}
    <div class="gs-section">
        <div class="gs-section-title">
            <div class="title-left">
                <span class="title-icon">🏅</span>
                <span>Membership & Payment Banner</span>
            </div>
        </div>

        <div class="gs-row">
            {{-- Membership Management --}}
            <div class="gs-col">
                <label class="gs-label">Membership & Trust Logos</label>
                <div style="padding: 24px; border: 2px dashed #e91e8c; border-radius: 12px; text-align: center; background: #fff5f9;">
                    <div style="font-size: 24px; color: #e91e8c; margin-bottom: 10px;"><i class="fas fa-certificate"></i></div>
                    <p class="small fw-bold mb-3">Manage your dynamic trust logos (e-CAB, BASIS, SSL etc.)</p>
                    <a href="{{ route('admin.membership_logos.index') }}" class="btn-save" style="float: none; display: inline-block;">Manage All Logos</a>
                </div>
                <div style="margin-top:15px; background: #f8f9fa; padding: 10px; border-radius: 8px;">
                    <label class="toggle-switch" style="vertical-align: middle;">
                        <input type="checkbox" name="show_membership_section" id="toggle_membership"
                               {{ old('show_membership_section', $setting->show_membership_section ?? 1) ? 'checked' : '' }}
                               onchange="ajaxToggle(this, '{{ $setting ? route('admin.generalsettings.toggle', $setting->id) : '#' }}', 'show_membership_section')">
                        <span class="toggle-slider"></span>
                    </label>
                    <span style="font-size:13px;font-weight:600;color:#333; margin-left:10px;">Show this section on Website Footer</span>
                </div>
            </div>

            {{-- Payment Banner --}}
            <div class="gs-col">
                <label class="gs-label">Main Payment Methods Logo</label>
                <div class="img-preview-box" style="min-height:120px; border: 2px solid #eee; background: #fafafa;">
                    @if(!empty($setting->payment_methods_logo))
                        <img src="{{ asset($setting->payment_methods_logo) }}" alt="Payment Banner" id="preview_payment" style="max-height: 100px;">
                    @else
                        <img src="" alt="" id="preview_payment" style="display:none; max-height: 100px;">
                        <div id="placeholder_payment" class="text-muted small">
                            <i class="fas fa-credit-card fa-2x mb-2"></i><br>
                            Upload One Combined Logo
                        </div>
                    @endif
                </div>
                <p class="img-sub-label">This single logo appears at the bottom of the checkout/footer.</p>
                <div class="gs-file-wrap">
                    <label for="payment_methods_logo">Upload Logo</label>
                    <span class="file-name" id="name_payment">No file selected</span>
                    <input type="file" id="payment_methods_logo" name="payment_methods_logo" accept="image/*"
                           onchange="previewImage(this,'preview_payment','name_payment','placeholder_payment')">
                </div>
            </div>
        </div>
    </div>
    </div>


    {{-- ===== Save Button ===== --}}
        <div class="reset-buttons">
            <button type="reset" class="reset-btn reset-btn-neutral">
                <i class="bi bi-arrow-counterclockwise"></i> Reset Form
            </button>

            <button type="button" class="reset-btn reset-btn-warning" onclick="submitResetForm()">
                <i class="bi bi-exclamation-triangle"></i> Reset to Defaults
            </button>

            <button type="submit" class="btn-save" style="margin-left: auto;">
                <i class="bi bi-check-circle-fill"></i> Save And Update
            </button>
        </div>
    </form>

    @if($setting)
    <form id="reset-to-defaults-form" action="{{ route('admin.generalsettings.reset', $setting->id) }}" method="POST" style="display:none;">
        @csrf
    </form>
    @endif

</div>{{-- /gs-page-wrapper --}}

<script>
    function submitResetForm() {
        if(confirm('Are you sure you want to reset all settings to defaults? This cannot be undone.')) {
            const form = document.getElementById('reset-to-defaults-form');
            if (form) {
                form.submit();
            } else {
                alert('No settings found to reset.');
            }
        }
    }

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
@push('styles')
    <style>
        .reset-buttons .reset-btn {
            all: unset;
            cursor: pointer;
            padding: 8px 16px;
            background: #f5f5f5;
            border: 1px solid #ccc;
            border-radius: 6px;
            color: #333;
            font-weight: 500;
            transition: background 0.3s, border-color 0.3s;
        }
        .reset-buttons .reset-btn:hover {
            background: #e0e0e0;
            border-color: #999;
        }
        .reset-buttons .reset-btn:active {
            background: #d4d4d4;
        }
    </style>
@endpush

@endsection
