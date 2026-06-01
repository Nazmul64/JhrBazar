@extends('admin.master')

@section('title', 'Create Landing Page')

@section('content')

@php
    $settings = \App\Models\GenaralSetting::first();
@endphp

<style>
:root {
    --cr-accent: #1e3a8a; --cr-accent-lt: #2563eb;
    --cr-text: #1a1f36; --cr-muted: #64748b;
    --cr-border: #e2e8f0; --cr-bg: #f1f5f9;
    --cr-white: #ffffff; --cr-radius: 10px;
    --cr-shadow: 0 1px 6px rgba(0,0,0,0.08);
    --cr-label-color: #0f172a;
}
*, *::before, *::after { box-sizing: border-box; }

.cr-wrap { padding: 28px 28px 60px; background: var(--cr-bg); min-height: 100vh; font-family: 'DM Sans','Segoe UI',system-ui,sans-serif; }

/* ── Header ── */
.cr-header { margin-bottom: 8px; }
.cr-header h1 { font-size: 20px; font-weight: 800; color: var(--cr-text); margin: 0 0 4px; }
.cr-breadcrumb { font-size: 12px; color: var(--cr-muted); }
.cr-breadcrumb a { color: var(--cr-muted); text-decoration: none; }
.cr-breadcrumb a:hover { color: var(--cr-accent); }
.cr-breadcrumb span { margin: 0 5px; }

/* ── Two-column grid ── */
.cr-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start; margin-top: 20px; }
@media(max-width: 1100px) { .cr-grid { grid-template-columns: 1fr; } }

/* ── Card ── */
.cr-card { background: var(--cr-white); border-radius: var(--cr-radius); box-shadow: var(--cr-shadow); border: 1px solid var(--cr-border); margin-bottom: 18px; overflow: hidden; }
.cr-card-head { padding: 14px 20px; border-bottom: 1px solid var(--cr-border); display: flex; align-items: center; gap: 8px; }
.cr-card-head h4 { font-size: 14px; font-weight: 700; color: var(--cr-text); margin: 0; }
.cr-card-head .head-icon { width: 30px; height: 30px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: var(--cr-accent-lt); font-size: 15px; flex-shrink: 0; }
.cr-card-body { padding: 20px 20px; }

/* ── Form controls ── */
.cr-group { margin-bottom: 16px; }
.cr-group:last-child { margin-bottom: 0; }
.cr-label { display: block; font-size: 12.5px; font-weight: 700; color: var(--cr-label-color); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.4px; }
.cr-label .opt { font-weight: 500; color: var(--cr-muted); text-transform: none; letter-spacing: 0; }
.cr-input {
    width: 100%; height: 42px; border: 1.5px solid var(--cr-border); border-radius: 8px;
    padding: 0 14px; font-size: 13.5px; color: var(--cr-text); background: #f8fafc;
    outline: none; font-family: inherit; transition: border-color .15s, box-shadow .15s;
}
.cr-input:focus { border-color: var(--cr-accent-lt); background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.cr-input.is-invalid { border-color: #dc2626; }
.cr-invalid { font-size: 12px; color: #dc2626; margin-top: 4px; }

/* Slug wrapper */
.cr-slug-wrap { display: flex; align-items: center; border: 1.5px solid var(--cr-border); border-radius: 8px; background: #f8fafc; overflow: hidden; transition: border-color .15s; }
.cr-slug-wrap:focus-within { border-color: var(--cr-accent-lt); background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.cr-slug-prefix { padding: 0 10px; font-size: 13px; color: var(--cr-muted); background: #f1f5f9; border-right: 1.5px solid var(--cr-border); height: 42px; display: flex; align-items: center; font-weight: 600; white-space: nowrap; }
.cr-slug-input { flex: 1; height: 42px; border: none; outline: none; padding: 0 12px; font-size: 13.5px; color: var(--cr-text); background: transparent; font-family: inherit; }

select.cr-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7a99' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-color: #f8fafc; padding-right: 36px; cursor: pointer; }

/* Multi-select list */
.cr-multiselect { width: 100%; border: 1.5px solid var(--cr-border); border-radius: 8px; background: #f8fafc; font-size: 13px; color: var(--cr-text); font-family: inherit; padding: 4px 0; min-height: 130px; outline: none; transition: border-color .15s; }
.cr-multiselect:focus { border-color: var(--cr-accent-lt); }
.cr-multiselect option { padding: 6px 12px; }
.cr-multiselect option:checked { background: #dbeafe; color: #1e40af; }
.cr-hint { font-size: 11.5px; color: var(--cr-muted); margin-top: 5px; }

/* File input */
.cr-file-wrap { display: flex; align-items: center; gap: 0; border: 1.5px solid var(--cr-border); border-radius: 8px; overflow: hidden; background: #f8fafc; }
.cr-file-btn { padding: 0 14px; height: 42px; background: #e2e8f0; border: none; border-right: 1.5px solid var(--cr-border); font-size: 13px; font-weight: 600; color: var(--cr-text); cursor: pointer; white-space: nowrap; display: flex; align-items: center; gap: 6px; }
.cr-file-name { flex: 1; padding: 0 12px; font-size: 13px; color: var(--cr-muted); height: 42px; display: flex; align-items: center; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
.cr-file-input-hidden { position: absolute; opacity: 0; width: 0; height: 0; }
.cr-img-preview { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1.5px solid var(--cr-border); margin-top: 10px; display: none; }

/* Template picker */
.cr-templates { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: 4px; }
.cr-tpl-card { border: 2px solid var(--cr-border); border-radius: 10px; overflow: hidden; cursor: pointer; transition: border-color .15s, box-shadow .15s; position: relative; }
.cr-tpl-card:hover { border-color: var(--cr-accent-lt); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.cr-tpl-card input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
.cr-tpl-card.selected { border-color: var(--cr-accent-lt); box-shadow: 0 0 0 3px rgba(37,99,235,.15); }
.cr-tpl-card.selected::after { content: '✓'; position: absolute; top: 8px; right: 8px; width: 22px; height: 22px; background: var(--cr-accent-lt); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; }
.cr-tpl-thumb { height: 100px; background: linear-gradient(135deg,#1e293b,#0f172a); display: flex; align-items: center; justify-content: center; font-size: 30px; }
.cr-tpl-thumb.light { background: linear-gradient(135deg,#f8fafc,#e2e8f0); }
.cr-tpl-thumb.builder { background: linear-gradient(135deg,#1e40af,#2563eb); }
.cr-tpl-name { padding: 8px 10px 10px; font-size: 12px; font-weight: 600; color: var(--cr-text); text-align: center; }

/* Color pickers */
.cr-color-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.cr-color-wrap { display: flex; align-items: center; gap: 10px; }
.cr-color-input { width: 42px; height: 42px; border: 1.5px solid var(--cr-border); border-radius: 8px; padding: 3px; cursor: pointer; background: #f8fafc; }
.cr-color-hex { flex: 1; height: 42px; border: 1.5px solid var(--cr-border); border-radius: 8px; padding: 0 12px; font-size: 13px; color: var(--cr-text); background: #f8fafc; outline: none; font-family: inherit; }

/* Status select */
.cr-status-select { width: 100%; height: 42px; border: 1.5px solid #86efac; border-radius: 8px; padding: 0 14px; font-size: 13.5px; color: #15803d; background: #f0fdf4; font-weight: 600; font-family: inherit; outline: none; cursor: pointer; }

/* Buttons */
.cr-btn-row { display: flex; gap: 10px; margin-top: 4px; }
.cr-btn-primary { height: 44px; padding: 0 24px; background: var(--cr-accent); color: #fff; border: none; border-radius: 8px; font-size: 13.5px; font-weight: 700; cursor: pointer; font-family: inherit; transition: opacity .15s; display: inline-flex; align-items: center; gap: 8px; }
.cr-btn-primary:hover { opacity: .88; }
.cr-btn-secondary { height: 44px; padding: 0 20px; background: var(--cr-white); border: 1.5px solid var(--cr-border); border-radius: 8px; font-size: 13.5px; font-weight: 600; color: var(--cr-muted); cursor: pointer; font-family: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; transition: all .15s; }
.cr-btn-secondary:hover { background: #f1f5f9; color: var(--cr-text); text-decoration: none; }
</style>

<div class="cr-wrap">

    {{-- Header --}}
    <div class="cr-header">
        <h1>Create Landing Page</h1>
        <p class="cr-breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span>›</span>
            <a href="{{ route('admin.landingpages.index') }}">Landing Pages</a>
            <span>›</span> New
        </p>
    </div>

    <form action="{{ route('admin.landingpages.store') }}" method="POST" enctype="multipart/form-data" id="lpCreateForm">
        @csrf

        <div class="cr-grid">

            {{-- ════════════════════════════════ LEFT ════════════════════════════════ --}}
            <div>

                {{-- Basic Information --}}
                <div class="cr-card">
                    <div class="cr-card-head">
                        <div class="head-icon"><i class="bi bi-info-circle-fill"></i></div>
                        <h4>Basic Information</h4>
                    </div>
                    <div class="cr-card-body">

                        {{-- Title --}}
                        <div class="cr-group">
                            <label class="cr-label">SUPER Page Title <span class="opt">(Optional)</span></label>
                            <input type="text" name="title"
                                   class="cr-input {{ $errors->has('title') ? 'is-invalid' : '' }}"
                                   placeholder="Enter landing page title"
                                   value="{{ old('title') }}">
                            @error('title')<div class="cr-invalid">{{ $message }}</div>@enderror
                        </div>

                        {{-- URL Slug --}}
                        <div class="cr-group">
                            <label class="cr-label">URL Slug <span class="opt">(Optional)</span></label>
                            <div class="cr-slug-wrap">
                                <span class="cr-slug-prefix">/N/</span>
                                <input type="text" name="slug" class="cr-slug-input"
                                       placeholder="unique-page-slug"
                                       value="{{ old('slug') }}">
                            </div>
                        </div>

                        {{-- Primary Product --}}
                        <div class="cr-group">
                            <label class="cr-label">Select Primary Product</label>
                            <select name="product_id" class="cr-input">
                                <option value="">-- Choose Main Product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Additional Products --}}
                        <div class="cr-group">
                            <label class="cr-label">Additional Products / Combo Options <span class="opt">(Optional)</span></label>
                            <select name="additional_product_ids[]" class="cr-multiselect" multiple size="6">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ in_array($product->id, old('additional_product_ids', [])) ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="cr-hint">These will appear as options in the order form.</p>
                        </div>

                    </div>
                </div>

                {{-- Choose Visual Style --}}
                <div class="cr-card">
                    <div class="cr-card-head">
                        <div class="head-icon"><i class="bi bi-palette-fill"></i></div>
                        <h4>Choose Visual Style (Blade Template) <span style="color:#ef4444;font-size:12px">*</span></h4>
                    </div>
                    <div class="cr-card-body">
                        <div class="cr-templates">

                            {{-- Template 1 --}}
                            <label class="cr-tpl-card" id="tpl1Label">
                                <input type="radio" name="template" value="template1" {{ old('template','template3') === 'template1' ? 'checked' : '' }}>
                                <div class="cr-tpl-thumb">🌙</div>
                                <div class="cr-tpl-name">Template 1 (Modern Dark)</div>
                            </label>

                            {{-- Template 2 --}}
                            <label class="cr-tpl-card" id="tpl2Label">
                                <input type="radio" name="template" value="template2" {{ old('template') === 'template2' ? 'checked' : '' }}>
                                <div class="cr-tpl-thumb light">☀️</div>
                                <div class="cr-tpl-name">Template 2 (Clean Light)</div>
                            </label>

                            {{-- Template 3 (Default / Dynamic Builder) --}}
                            <label class="cr-tpl-card {{ old('template','template3') === 'template3' ? 'selected' : '' }}" id="tpl3Label">
                                <input type="radio" name="template" value="template3" {{ old('template','template3') === 'template3' ? 'checked' : '' }}>
                                <div class="cr-tpl-thumb builder">🚀</div>
                                <div class="cr-tpl-name">Template 3 (Dynamic Builder)</div>
                            </label>

                        </div>
                    </div>
                </div>

            </div>
            {{-- ════════════════════════════════ RIGHT ════════════════════════════════ --}}
            <div>

                {{-- Global Media --}}
                <div class="cr-card">
                    <div class="cr-card-head">
                        <div class="head-icon"><i class="bi bi-images"></i></div>
                        <h4>Global Media <span style="font-size:11px;color:var(--cr-muted);font-weight:500;">ⓘ</span></h4>
                    </div>
                    <div class="cr-card-body">

                        {{-- Feature Image --}}
                        <div class="cr-group">
                            <label class="cr-label">Main Feature Image <span class="opt">(Optional)</span></label>
                            <div class="cr-file-wrap" onclick="document.getElementById('featureImageInput').click()">
                                <span class="cr-file-btn">Choose file</span>
                                <span class="cr-file-name" id="featureImageName">No file chosen</span>
                            </div>
                            <input type="file" name="image" id="featureImageInput" class="cr-file-input-hidden" accept="image/*"
                                   onchange="handleFile(this,'featureImageName','featureImgPreview')">
                            <img id="featureImgPreview" class="cr-img-preview" alt="Preview">
                            <p class="cr-hint">Shows at the top of the page.</p>
                        </div>

                        {{-- Video URL --}}
                        <div class="cr-group">
                            <label class="cr-label">Main Video URL <span class="opt">(Optional)</span></label>
                            <input type="text" name="video_url" class="cr-input"
                                   placeholder="https://youtube.com/..."
                                   value="{{ old('video_url') }}">
                        </div>

                        {{-- Checkout Review Image --}}
                        <div class="cr-group">
                            <label class="cr-label">Checkout Review Image <span class="opt">(Optional)</span></label>
                            <div class="cr-file-wrap" onclick="document.getElementById('reviewImgInput').click()">
                                <span class="cr-file-btn">Choose file</span>
                                <span class="cr-file-name" id="reviewImgName">No file chosen</span>
                            </div>
                            <input type="file" name="checkout_review_image" id="reviewImgInput" class="cr-file-input-hidden" accept="image/*"
                                   onchange="handleFile(this,'reviewImgName','reviewImgPreview')">
                            <img id="reviewImgPreview" class="cr-img-preview" alt="Preview">
                            <p class="cr-hint">This single review image will stay fixed right above the Order Form.</p>
                        </div>

                    </div>
                </div>

                {{-- Theme Colors --}}
                <div class="cr-card">
                    <div class="cr-card-head">
                        <div class="head-icon"><i class="bi bi-palette2"></i></div>
                        <h4>Theme Colors 🎨</h4>
                    </div>
                    <div class="cr-card-body">
                        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:16px;">
                            These colors will automatically apply to all blocks you add in the Page Builder later!
                        </div>

                        <div class="cr-color-row">
                            {{-- Background Color --}}
                            <div class="cr-group">
                                <label class="cr-label">Background Color</label>
                                <div class="cr-color-wrap">
                                    <input type="color" name="bg_color" id="bgColorPicker" class="cr-color-input"
                                           value="{{ old('bg_color','#ffffff') }}"
                                           onchange="document.getElementById('bgColorHex').value = this.value">
                                    <input type="text" id="bgColorHex" class="cr-color-hex"
                                           value="{{ old('bg_color','#ffffff') }}"
                                           onchange="document.getElementById('bgColorPicker').value = this.value">
                                </div>
                            </div>
                            {{-- Button Color --}}
                            <div class="cr-group">
                                <label class="cr-label">Primary/Button Color</label>
                                <div class="cr-color-wrap">
                                    <input type="color" name="button_color" id="btnColorPicker" class="cr-color-input"
                                           value="{{ old('button_color','#1e3a8a') }}"
                                           onchange="document.getElementById('btnColorHex').value = this.value">
                                    <input type="text" id="btnColorHex" class="cr-color-hex"
                                           value="{{ old('button_color','#1e3a8a') }}"
                                           onchange="document.getElementById('btnColorPicker').value = this.value">
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="cr-group" style="margin-top:8px;">
                            <label class="cr-label">Status</label>
                            <select name="status" class="cr-status-select">
                                <option value="1" {{ old('status','1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="cr-btn-row">
                    <input type="hidden" name="action" value="builder">
                    <button type="submit" class="cr-btn-primary">
                        <i class="bi bi-arrow-right-circle-fill"></i>
                        Create & Open Builder
                    </button>
                    <a href="{{ route('admin.landingpages.index') }}" class="cr-btn-secondary">
                        Cancel
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
// Template card selection highlight
document.querySelectorAll('.cr-tpl-card input[type="radio"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.cr-tpl-card').forEach(c => c.classList.remove('selected'));
        this.closest('.cr-tpl-card').classList.add('selected');
    });
});

// File input handler
function handleFile(input, nameId, previewId) {
    if (input.files && input.files[0]) {
        document.getElementById(nameId).textContent = input.files[0].name;
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.getElementById(previewId);
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto-generate slug from title
document.querySelector('[name="title"]').addEventListener('input', function() {
    var slugInput = document.querySelector('[name="slug"]');
    if (!slugInput.value || slugInput.dataset.manual !== '1') {
        var slug = this.value
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/--+/g, '-')
            .trim();
        slugInput.value = slug;
    }
});
document.querySelector('[name="slug"]').addEventListener('input', function() {
    this.dataset.manual = '1';
});
</script>

@endsection
