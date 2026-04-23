{{-- resources/views/admin/product/create.blade.php --}}
@extends('admin.master')
@section('content')
<style>
    :root {
        --brand: #e8174a;
        --brand-light: rgba(232,23,74,0.08);
        --brand-hover: #c9113e;
        --dark: #1a1d23;
        --muted: #6b7280;
        --border: #e5e7eb;
        --surface: #f8f9fc;
        --r-lg: 14px;
        --r-md: 10px;
        --r-sm: 7px;
        --ease: all .18s ease;
    }

    .page-title { font-size:1.35rem; font-weight:700; color:var(--dark); margin:0 0 1.5rem; }

    /* ── Section cards ── */
    .s-card { background:#fff; border:1px solid var(--border); border-radius:var(--r-lg); margin-bottom:1.4rem; overflow:hidden; }
    .s-title { font-size:14px; font-weight:700; color:var(--dark); padding:16px 24px; border-bottom:1px solid var(--border); margin:0; background:var(--surface); }
    .s-body  { padding:24px; }

    /* ── Fields ── */
    .f-label { display:block; font-size:13px; font-weight:600; color:var(--dark); margin-bottom:7px; }
    .req { color:var(--brand); margin-left:2px; }
    .f-input {
        width:100%; border:1.5px solid var(--border); border-radius:var(--r-sm);
        padding:10px 14px; font-size:13.5px; color:var(--dark); background:#fff;
        outline:none; transition:border-color .15s; appearance:none; -webkit-appearance:none;
    }
    .f-input:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(232,23,74,.1); }
    .f-input::placeholder { color:#b0b7c3; }
    .f-input.is-invalid { border-color:var(--brand) !important; }
    .f-err { font-size:12px; color:var(--brand); margin-top:5px; display:flex; align-items:center; gap:4px; }

    /* select arrow */
    select.f-input {
        background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position:right 12px center; background-repeat:no-repeat; background-size:18px; padding-right:40px;
    }

    .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:18px; }
    .mb-4   { margin-bottom:18px; }

    /* ── Thumbnail upload box ── */
    .thumb-box {
        width:180px; height:180px;
        border:2px dashed var(--brand); border-radius:var(--r-md);
        display:flex; flex-direction:column; align-items:center; justify-content:center;
        cursor:pointer; transition:var(--ease); position:relative; overflow:hidden;
        background:var(--surface);
    }
    .thumb-box:hover { background:var(--brand-light); }
    .thumb-box .prev-img { width:100%; height:100%; object-fit:contain; position:absolute; top:0; left:0; display:none; }
    .thumb-ph { display:flex; flex-direction:column; align-items:center; gap:6px; pointer-events:none; }
    .thumb-ph span { font-size:11px; color:var(--muted); }
    .img-hint { font-size:11.5px; color:var(--muted); margin-top:6px; }

    /* ── Gallery upload area ── */
    .gal-area {
        border:2px dashed var(--border); border-radius:var(--r-md);
        padding:24px; background:var(--surface); cursor:pointer; transition:var(--ease);
    }
    .gal-area:hover { border-color:var(--brand); background:var(--brand-light); }
    .gal-trigger { display:flex; flex-direction:column; align-items:center; gap:8px; pointer-events:none; }
    .gal-trigger i { font-size:32px; color:#d1d5db; }
    .gal-trigger span { font-size:13px; color:var(--muted); }
    .gal-trigger strong { color:var(--brand); }
    .gal-preview-grid { display:flex; flex-wrap:wrap; gap:12px; margin-top:14px; }
    .gal-item { position:relative; width:100px; height:100px; }
    .gal-item img {
        width:100%; height:100%; object-fit:contain;
        border-radius:var(--r-sm); border:1px solid var(--border);
        background:#fff; padding:4px;
    }
    .gal-rm {
        position:absolute; top:-6px; right:-6px;
        width:22px; height:22px; background:#ef4444; color:#fff;
        border:none; border-radius:50%; font-size:13px; line-height:1;
        cursor:pointer; display:flex; align-items:center; justify-content:center;
        box-shadow:0 1px 4px rgba(0,0,0,.2);
    }
    .gal-rm:hover { background:#dc2626; }

    /* ── Quill ── */
    #quill-editor { min-height:140px; font-size:13.5px; }
    .ql-toolbar  { border-radius:var(--r-sm) var(--r-sm) 0 0 !important; border-color:var(--border) !important; }
    .ql-container{ border-radius:0 0 var(--r-sm) var(--r-sm) !important; border-color:var(--border) !important; font-size:13.5px !important; }

    /* ── Buttons ── */
    .btn-ai { display:inline-flex; align-items:center; gap:6px; background:var(--brand); color:#fff; border:none; border-radius:20px; padding:5px 14px; font-size:12px; font-weight:600; cursor:pointer; }
    .sku-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:7px; }
    .btn-gen-sku { background:none; border:none; color:var(--brand); font-size:13px; font-weight:600; cursor:pointer; padding:0; }
    .btn-gen-sku:hover { text-decoration:underline; }

    /* Barcode note */
    .barcode-note {
        background:var(--brand-light); border-radius:var(--r-sm);
        padding:10px 14px; font-size:12.5px; color:var(--brand);
        display:flex; align-items:center; gap:8px; margin-top:8px;
    }

    /* ── Sub-cat loading ── */
    .sub-loading { font-size:12px; color:var(--muted); padding:4px 0; display:none; }

    /* ── Tags ── */
    .tags-wrap {
        border:1.5px solid var(--border); border-radius:var(--r-sm);
        padding:6px 10px; display:flex; flex-wrap:wrap; gap:6px;
        min-height:44px; cursor:text;
    }
    .tags-wrap:focus-within { border-color:var(--brand); box-shadow:0 0 0 3px rgba(232,23,74,.1); }
    .tag-item {
        background:var(--brand-light); color:var(--brand); border-radius:20px;
        padding:3px 10px; font-size:12px; font-weight:600;
        display:inline-flex; align-items:center; gap:5px;
    }
    .tag-item button { background:none; border:none; cursor:pointer; color:var(--brand); padding:0; font-size:14px; line-height:1; }
    .tag-input-real { border:none; outline:none; font-size:13px; min-width:120px; flex:1; background:transparent; }

    /* ── Video type ── */
    .video-sel { border:1.5px solid var(--border); border-radius:var(--r-sm); padding:9px 14px; font-size:13px; outline:none; background:#fff; appearance:none; -webkit-appearance:none; }

    /* ── Footer ── */
    .form-footer { display:flex; justify-content:flex-end; gap:12px; padding-top:8px; }
    .btn-reset {
        background:transparent; border:1.5px solid var(--border); color:var(--muted);
        border-radius:var(--r-sm); padding:10px 28px; font-size:13px; font-weight:500; cursor:pointer;
    }
    .btn-reset:hover { background:var(--surface); }
    .btn-submit {
        background:var(--brand); border:none; color:#fff;
        border-radius:var(--r-sm); padding:10px 32px;
        font-size:13px; font-weight:600; cursor:pointer;
        box-shadow:0 2px 10px rgba(232,23,74,.25); transition:var(--ease);
    }
    .btn-submit:hover { background:var(--brand-hover); transform:translateY(-1px); }

    /* ── Error alert ── */
    .err-alert {
        background:#fff1f3; color:#be123c;
        border-left:3.5px solid var(--brand); border-radius:var(--r-md);
        padding:12px 16px; font-size:13.5px; margin-bottom:1.2rem;
        display:flex; align-items:center; gap:9px;
    }
</style>

{{-- AJAX base URL --}}
<script>var SUBCATEGORY_URL = '{{ url("admin/products/subcategories") }}';</script>

<h4 class="page-title">Add New Product</h4>

@if($errors->any())
    <div class="err-alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form action="{{ route('products.store') }}" method="POST"
      enctype="multipart/form-data" id="productForm">
    @csrf

    {{-- ══════════════ PRODUCT INFO ══════════════ --}}
    <div class="s-card">
        <p class="s-title">Product Info</p>
        <div class="s-body">

            <div class="mb-4">
                <label class="f-label">Product Name <span class="req">*</span></label>
                <input type="text" name="name"
                       class="f-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       placeholder="Enter product name" value="{{ old('name') }}">
                @error('name')<div class="f-err"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="f-label">Short Description <span class="req">*</span></label>
                <textarea name="short_description"
                          class="f-input {{ $errors->has('short_description') ? 'is-invalid' : '' }}"
                          rows="3" placeholder="Short product description">{{ old('short_description') }}</textarea>
                @error('short_description')<div class="f-err"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                    <label class="f-label" style="margin:0;">Description</label>
                    <button type="button" class="btn-ai"><i class="bi bi-stars"></i> Generate Via AI</button>
                </div>
                <div id="quill-editor">{{ old('description') }}</div>
                <input type="hidden" name="description" id="desc-input">
            </div>

        </div>
    </div>

    {{-- ══════════════ GENERAL INFO ══════════════ --}}
    <div class="s-card">
        <p class="s-title">General Information</p>
        <div class="s-body">

            {{-- Category / Sub / Brand --}}
            <div class="grid-3 mb-4">
                <div>
                    <label class="f-label">Category <span class="req">*</span></label>
                    <select name="category_id" id="categorySelect"
                            class="f-input {{ $errors->has('category_id') ? 'is-invalid' : '' }}">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="f-err"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="f-label">Sub Category</label>
                    <select name="sub_category_id" id="subCategorySelect" class="f-input">
                        <option value="">— Select Category First —</option>
                    </select>
                    <span class="sub-loading" id="subLoading">
                        <i class="bi bi-arrow-repeat"></i> Loading…
                    </span>
                </div>
                <div>
                    <label class="f-label">Brand</label>
                    <select name="brand_id" class="f-input">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Color / Unit / Size --}}
            <div class="grid-3 mb-4">
                <div>
                    <label class="f-label">Color</label>
                    <select name="color" class="f-input">
                        <option value="">Select Color</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->name }}" {{ old('color') == $color->name ? 'selected' : '' }}>
                                {{ $color->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="f-label">Unit</label>
                    <select name="unit" class="f-input">
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->name }}" {{ old('unit') == $unit->name ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="f-label">Size</label>
                    <select name="size" class="f-input">
                        <option value="">Select Size</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->name }}" {{ old('size') == $size->name ? 'selected' : '' }}>
                                {{ $size->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- SKU --}}
            <div style="max-width:340px;">
                <div class="sku-row">
                    <label class="f-label" style="margin:0;">Product SKU <span class="req">*</span></label>
                    <button type="button" class="btn-gen-sku" onclick="generateSku()">Generate Code</button>
                </div>
                <input type="text" name="sku" id="skuInput"
                       class="f-input {{ $errors->has('sku') ? 'is-invalid' : '' }}"
                       placeholder="e.g. SKU-AB1234" value="{{ old('sku') }}">
                @error('sku')<div class="f-err"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>@enderror
                <div class="barcode-note">
                    <i class="bi bi-upc-scan"></i>
                    Barcode will be auto-generated from SKU when product is saved.
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════ PRICE ══════════════ --}}
    <div class="s-card">
        <p class="s-title">Price Information</p>
        <div class="s-body">
            <div class="grid-3 mb-4">
                <div>
                    <label class="f-label">Buying Price <span class="req">*</span></label>
                    <input type="number" name="buying_price" step="0.01" min="0"
                           class="f-input {{ $errors->has('buying_price') ? 'is-invalid' : '' }}"
                           placeholder="0.00" value="{{ old('buying_price') }}">
                    @error('buying_price')<div class="f-err"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="f-label">Selling Price <span class="req">*</span></label>
                    <input type="number" name="selling_price" step="0.01" min="0"
                           class="f-input {{ $errors->has('selling_price') ? 'is-invalid' : '' }}"
                           placeholder="0.00" value="{{ old('selling_price') }}">
                    @error('selling_price')<div class="f-err"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="f-label">Discount Price</label>
                    <input type="number" name="discount_price" step="0.01" min="0"
                           class="f-input" placeholder="0" value="{{ old('discount_price', 0) }}">
                </div>
            </div>
            <div style="max-width:340px;">
                <label class="f-label">Stock Quantity</label>
                <input type="number" name="stock_quantity" min="0" class="f-input"
                       placeholder="0" value="{{ old('stock_quantity', 0) }}">
            </div>
        </div>
    </div>

    {{-- ══════════════ IMAGES — সব ছবি uploads/product এ সেভ হবে ══════════════ --}}
    <div class="s-card">
        <p class="s-title">Images</p>
        <div class="s-body">

            {{-- Thumbnail --}}
            <div class="mb-4">
                <label class="f-label">
                    Thumbnail
                    <span style="color:#3b82f6;font-weight:500;">(500×500 px recommended)</span>
                    <span class="req">*</span>
                </label>
                {{-- Click করলে file picker open হয়, preview দেখায় --}}
                <div class="thumb-box" id="thumbBox"
                     onclick="document.getElementById('thumbInput').click()">
                    <img class="prev-img" id="thumbPreview" src="" alt="preview">
                    <div class="thumb-ph" id="thumbPh">
                        <i class="bi bi-cloud-arrow-up" style="font-size:38px;color:var(--brand);opacity:.6;"></i>
                        <span style="font-size:12px;font-weight:600;color:var(--brand);">Click to Upload</span>
                        <span>500 × 500 px</span>
                    </div>
                </div>
                {{-- File input — name="thumbnail", সার্ভারে uploads/product এ যাবে --}}
                <input type="file" id="thumbInput" name="thumbnail"
                       accept="image/jpg,image/jpeg,image/png,image/webp" style="display:none;"
                       onchange="previewThumb(this)">
                <p class="img-hint">Supported: jpg, jpeg, png, webp · Max 2MB · Saved to: <strong>uploads/product/</strong></p>
                @error('thumbnail')<div class="f-err"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>@enderror
            </div>

            {{-- Gallery --}}
            <div>
                <label class="f-label">
                    Gallery Images
                    <span style="color:#3b82f6;font-weight:500;">(Select multiple)</span>
                </label>
                {{-- Click করলে multiple file picker open হয় --}}
                <div class="gal-area" onclick="document.getElementById('galInput').click()">
                    <div class="gal-trigger">
                        <i class="bi bi-images"></i>
                        <span>Click to select images · <strong>Hold Ctrl/Cmd for multiple</strong></span>
                        <span style="font-size:12px;">jpg, jpeg, png, webp · Max 2MB each · Saved to: <strong>uploads/product/</strong></span>
                    </div>
                </div>
                {{-- File input — name="gallery_images[]", সার্ভারে uploads/product এ যাবে --}}
                <input type="file" id="galInput" name="gallery_images[]"
                       accept="image/jpg,image/jpeg,image/png,image/webp" multiple style="display:none;"
                       onchange="previewGallery(this)">
                <div class="gal-preview-grid" id="galGrid"></div>
                @error('gallery_images.*')<div class="f-err"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>@enderror
            </div>

        </div>
    </div>

    {{-- ══════════════ VIDEO — uploads/product এ সেভ হবে ══════════════ --}}
    <div class="s-card">
        <p class="s-title">Product Video</p>
        <div class="s-body">
            <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
                <div>
                    <label class="f-label">Video Type</label>
                    <select name="video_type" id="videoType" class="f-input video-sel"
                            style="width:auto;" onchange="handleVideoType()">
                        <option value="file">Upload Video File</option>
                        <option value="url">External URL</option>
                        <option value="youtube">YouTube Link</option>
                    </select>
                </div>
                <div id="videoFileWrap">
                    <label class="f-label">Upload Video</label>
                    {{-- name="video_file", সার্ভারে uploads/product এ যাবে --}}
                    <input type="file" name="video_file"
                           accept="video/mp4,video/avi,video/mov,video/wmv"
                           class="f-input" style="padding:6px;">
                    <p class="img-hint">Supported: MP4, AVI, MOV, WMV · Max 50MB · Saved to: <strong>uploads/product/</strong></p>
                </div>
                <div id="videoUrlWrap" style="display:none;flex:1;min-width:220px;">
                    <label class="f-label">Video URL</label>
                    <input type="text" name="video_url" class="f-input" placeholder="https://…">
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════ SEO ══════════════ --}}
    <div class="s-card">
        <p class="s-title">SEO Information</p>
        <div class="s-body">
            <div class="mb-4">
                <label class="f-label">Meta Title</label>
                <input type="text" name="meta_title" class="f-input"
                       placeholder="Meta Title" value="{{ old('meta_title') }}">
            </div>
            <div class="mb-4">
                <label class="f-label">Meta Description</label>
                <textarea name="meta_description" class="f-input" rows="3"
                          placeholder="Meta Description">{{ old('meta_description') }}</textarea>
            </div>
            <div>
                <label class="f-label">Meta Keywords</label>
                <div class="tags-wrap" id="tagsWrap"
                     onclick="document.getElementById('tagInput').focus()">
                    <input type="text" id="tagInput" class="tag-input-real"
                           placeholder="Write keyword and press Enter">
                </div>
                <input type="hidden" name="meta_keywords" id="metaKw" value="{{ old('meta_keywords') }}">
                <p class="img-hint">Press Enter to add each keyword</p>
            </div>
        </div>
    </div>

    {{-- SUBMIT --}}
    <div class="form-footer">
        <button type="button" class="btn-reset" onclick="resetForm()">Reset</button>
        <button type="submit" class="btn-submit">
            <i class="bi bi-check-lg me-1"></i> Submit
        </button>
    </div>
</form>

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
// ══════════════════════════════════════════════════
//  Quill editor
// ══════════════════════════════════════════════════
var quill = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'Enter product description…',
    modules: {
        toolbar: [
            [{ header:[false,1,2,3] },{ font:[] }],
            ['bold','italic','underline','strike','blockquote'],
            [{ list:'ordered' },{ list:'bullet' },{ align:[] }],
            [{ color:[] },{ background:[] }],
            ['link','image','video'],['clean']
        ]
    }
});
document.getElementById('productForm').addEventListener('submit', function() {
    document.getElementById('desc-input').value = quill.root.innerHTML;
});

// ══════════════════════════════════════════════════
//  Sub-Category AJAX
// ══════════════════════════════════════════════════
function loadSubCats(catId, selId) {
    var sel  = document.getElementById('subCategorySelect');
    var load = document.getElementById('subLoading');
    sel.innerHTML = '<option value="">Select Sub Category</option>';
    if (!catId) {
        sel.innerHTML = '<option value="">— Select Category First —</option>';
        return;
    }
    sel.disabled = true;
    load.style.display = 'inline';
    fetch(SUBCATEGORY_URL + '/' + catId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
    .then(function(data) {
        sel.innerHTML = '<option value="">Select Sub Category</option>';
        if (!data || !data.length) {
            sel.innerHTML += '<option value="" disabled>No sub category found</option>';
        } else {
            data.forEach(function(s) {
                var o = document.createElement('option');
                o.value = s.id;
                o.textContent = s.name;
                if (selId && String(s.id) === String(selId)) o.selected = true;
                sel.appendChild(o);
            });
        }
    })
    .catch(function() { sel.innerHTML = '<option value="">⚠ Error — try again</option>'; })
    .finally(function() { sel.disabled = false; load.style.display = 'none'; });
}

document.getElementById('categorySelect').addEventListener('change', function() {
    loadSubCats(this.value, null);
});

document.addEventListener('DOMContentLoaded', function() {
    var oc = '{{ old("category_id") }}';
    var os = '{{ old("sub_category_id") }}';
    if (oc) loadSubCats(oc, os || null);
});

// ══════════════════════════════════════════════════
//  Thumbnail preview
// ══════════════════════════════════════════════════
function previewThumb(input) {
    if (!input.files[0]) return;
    var r = new FileReader();
    r.onload = function(e) {
        var prev = document.getElementById('thumbPreview');
        var ph   = document.getElementById('thumbPh');
        prev.src = e.target.result;
        prev.style.display = 'block';
        ph.style.display = 'none';
    };
    r.readAsDataURL(input.files[0]);
}

// ══════════════════════════════════════════════════
//  Gallery preview (client-side, DataTransfer trick)
// ══════════════════════════════════════════════════
var galStore = new DataTransfer();

function previewGallery(input) {
    var grid = document.getElementById('galGrid');
    Array.from(input.files).forEach(function(file) {
        // duplicate check by name + size
        for (var i = 0; i < galStore.files.length; i++) {
            if (galStore.files[i].name === file.name && galStore.files[i].size === file.size) return;
        }
        galStore.items.add(file);
        var r = new FileReader();
        r.onload = function(e) {
            var item = document.createElement('div');
            item.className = 'gal-item';
            item.dataset.fn = file.name;
            item.dataset.fs = file.size;

            var img = document.createElement('img');
            img.src = e.target.result;
            img.alt = file.name;

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'gal-rm';
            btn.innerHTML = '×';
            btn.onclick = function() { removeGal(file.name, file.size); };

            item.appendChild(img);
            item.appendChild(btn);
            grid.appendChild(item);
        };
        r.readAsDataURL(file);
    });
    input.files = galStore.files;
}

function removeGal(fn, fs) {
    var ns = new DataTransfer();
    for (var i = 0; i < galStore.files.length; i++) {
        var f = galStore.files[i];
        if (!(f.name === fn && f.size == fs)) ns.items.add(f);
    }
    galStore = ns;
    document.getElementById('galInput').files = galStore.files;

    var el = document.querySelector('.gal-item[data-fn="' + fn + '"]');
    if (el) el.remove();
}

// ══════════════════════════════════════════════════
//  SKU Generator
// ══════════════════════════════════════════════════
function generateSku() {
    var r = Math.random().toString(36).substring(2, 8).toUpperCase();
    var t = Date.now().toString().slice(-4);
    document.getElementById('skuInput').value = 'SKU-' + r + '-' + t;
}

// ══════════════════════════════════════════════════
//  Video type toggle
// ══════════════════════════════════════════════════
function handleVideoType() {
    var t = document.getElementById('videoType').value;
    document.getElementById('videoFileWrap').style.display = (t === 'file') ? 'block' : 'none';
    document.getElementById('videoUrlWrap').style.display  = (t !== 'file') ? 'block' : 'none';
}

// ══════════════════════════════════════════════════
//  Meta keywords tags
// ══════════════════════════════════════════════════
var tags = [];
var oldKw = document.getElementById('metaKw').value;
if (oldKw) {
    tags = oldKw.split(',').map(function(t) { return t.trim(); }).filter(Boolean);
    renderTags();
}

document.getElementById('tagInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        var v = this.value.trim().replace(/,$/, '');
        if (v && !tags.includes(v)) {
            tags.push(v);
            renderTags();
            document.getElementById('metaKw').value = tags.join(',');
        }
        this.value = '';
    }
});

function renderTags() {
    var w = document.getElementById('tagsWrap');
    w.querySelectorAll('.tag-item').forEach(function(el) { el.remove(); });
    var inp = document.getElementById('tagInput');
    tags.forEach(function(tag, i) {
        var s = document.createElement('span');
        s.className = 'tag-item';
        s.innerHTML = tag + ' <button type="button" onclick="removeTag(' + i + ')">×</button>';
        w.insertBefore(s, inp);
    });
}

function removeTag(i) {
    tags.splice(i, 1);
    renderTags();
    document.getElementById('metaKw').value = tags.join(',');
}

// ══════════════════════════════════════════════════
//  Reset form
// ══════════════════════════════════════════════════
function resetForm() {
    document.getElementById('productForm').reset();
    quill.setContents([]);
    document.getElementById('thumbPreview').style.display = 'none';
    document.getElementById('thumbPreview').src = '';
    document.getElementById('thumbPh').style.display = 'flex';
    document.getElementById('galGrid').innerHTML = '';
    galStore = new DataTransfer();
    tags = [];
    renderTags();
    document.getElementById('metaKw').value = '';
    document.getElementById('subCategorySelect').innerHTML = '<option value="">— Select Category First —</option>';
}
</script>
@endsection
