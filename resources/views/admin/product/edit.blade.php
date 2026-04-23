{{-- resources/views/admin/product/edit.blade.php --}}
@extends('admin.master')
@section('content')
<style>
    :root {
        --brand:       #e8174a;
        --brand-light: rgba(232,23,74,.08);
        --brand-hover: #c9113e;
        --dark:        #1a1d23;
        --muted:       #6b7280;
        --border:      #e5e7eb;
        --surface:     #f8f9fc;
        --shadow:      0 1px 4px rgba(0,0,0,.06),0 2px 12px rgba(0,0,0,.04);
        --r-lg: 14px; --r-md: 10px; --r-sm: 7px;
        --ease: all .18s ease;
    }

    /* ── Breadcrumb & Title ── */
    .breadcrumb-nav { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--muted); margin-bottom:.8rem; }
    .breadcrumb-nav a { color:var(--brand); text-decoration:none; font-weight:600; }
    .breadcrumb-nav a:hover { text-decoration:underline; }
    .breadcrumb-sep { font-size:10px; color:#d1d5db; }
    .page-title { font-size:1.35rem; font-weight:700; color:var(--dark); margin:0 0 1.5rem; }

    /* ── Section Card ── */
    .section-card { background:#fff; border:1px solid var(--border); border-radius:var(--r-lg); margin-bottom:1.4rem; overflow:hidden; box-shadow:var(--shadow); }
    .section-head { font-size:13.5px; font-weight:700; color:var(--dark); padding:14px 22px; border-bottom:1px solid var(--border); margin:0; background:var(--surface); display:flex; align-items:center; gap:8px; }
    .section-head i { color:var(--brand); font-size:15px; }
    .section-body { padding:22px 24px; }

    /* ── Fields ── */
    .field-label { display:block; font-size:13px; font-weight:600; color:var(--dark); margin-bottom:6px; }
    .req { color:var(--brand); margin-left:2px; }
    .field-hint { font-size:11px; color:var(--muted); font-weight:400; margin-left:6px; }
    .field-input {
        width:100%; border:1.5px solid var(--border); border-radius:var(--r-sm);
        padding:10px 14px; font-size:13.5px; color:var(--dark); background:#fff;
        outline:none; appearance:none; -webkit-appearance:none; transition:border-color .15s;
    }
    .field-input:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(232,23,74,.1); }
    .field-input::placeholder { color:#b0b7c3; }
    .field-input.is-invalid { border-color:var(--brand) !important; }
    select.field-input {
        background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position:right 12px center; background-repeat:no-repeat; background-size:18px; padding-right:38px;
    }
    .field-error { font-size:12px; color:#ef4444; margin-top:5px; display:flex; align-items:center; gap:4px; }
    .mb-field { margin-bottom:18px; }

    /* ── Grid ── */
    .g3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:18px; }
    .g2 { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
    @media(max-width:768px){ .g3,.g2 { grid-template-columns:1fr; } }

    /* ── Alert ── */
    .alert-err { background:#fff1f3; color:#be123c; border-left:3.5px solid var(--brand); border-radius:var(--r-md); padding:12px 16px; font-size:13.5px; margin-bottom:1.2rem; display:flex; align-items:center; gap:9px; }

    /* ── AI btn ── */
    .btn-ai { display:inline-flex; align-items:center; gap:6px; background:var(--brand); color:#fff; border:none; border-radius:20px; padding:5px 14px; font-size:12px; font-weight:600; cursor:pointer; transition:var(--ease); }
    .btn-ai:hover { background:var(--brand-hover); }

    /* ── SKU row ── */
    .sku-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; }
    .btn-gen-sku { background:none; border:none; color:var(--brand); font-size:13px; font-weight:600; cursor:pointer; padding:0; }
    .btn-gen-sku:hover { text-decoration:underline; }

    /* ── Barcode row ── */
    .barcode-row { display:flex; align-items:center; gap:10px; background:var(--brand-light); border-radius:var(--r-sm); padding:8px 14px; margin-top:8px; flex-wrap:wrap; }
    .bc-icon { color:var(--brand); font-size:18px; flex-shrink:0; }
    .bc-label { font-size:12px; color:var(--brand); font-weight:700; white-space:nowrap; }
    .bc-input { border:1.5px solid rgba(232,23,74,.3); border-radius:6px; padding:5px 12px; font-size:13px; color:var(--brand); font-weight:700; background:#fff; outline:none; flex:1; min-width:120px; transition:border-color .15s; }
    .bc-input:focus { border-color:var(--brand); box-shadow:0 0 0 2px rgba(232,23,74,.15); }
    .bc-view-link { color:var(--brand); font-weight:700; font-size:12px; text-decoration:none; white-space:nowrap; margin-left:auto; }
    .bc-view-link:hover { text-decoration:underline; }

    /* ── Sub-cat loading ── */
    .sub-loading { font-size:12px; color:var(--muted); display:none; padding:3px 0; }

    /* ── Thumbnail upload ── */
    .thumb-upload-wrap { display:flex; align-items:flex-start; gap:20px; flex-wrap:wrap; }
    .current-thumb-box { display:flex; flex-direction:column; align-items:center; gap:4px; }
    .current-thumb { width:90px; height:90px; object-fit:contain; border-radius:var(--r-md); border:1px solid var(--border); background:var(--surface); padding:5px; }
    .current-label { font-size:11px; color:var(--muted); text-align:center; }
    .thumb-arrow { color:#d1d5db; font-size:18px; align-self:center; flex-shrink:0; }
    .img-upload-box {
        width:180px; height:180px; border:2px dashed rgba(232,23,74,.4);
        border-radius:var(--r-md); display:flex; flex-direction:column;
        align-items:center; justify-content:center; cursor:pointer;
        transition:var(--ease); position:relative; overflow:hidden; background:var(--surface);
        flex-shrink:0;
    }
    .img-upload-box:hover { background:var(--brand-light); border-color:var(--brand); }
    .img-upload-box .preview-img { width:100%; height:100%; object-fit:contain; position:absolute; top:0; left:0; display:none; }
    .upload-ph { display:flex; flex-direction:column; align-items:center; gap:6px; pointer-events:none; }
    .upload-ph span { font-size:11px; font-weight:600; color:var(--brand); }
    .upload-ph small { font-size:11px; color:var(--muted); }
    .img-hint { font-size:11.5px; color:var(--muted); margin-top:6px; }

    /* ── Gallery ── */
    .gallery-grid { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:14px; }
    .gal-item { position:relative; width:100px; height:100px; }
    .gal-item img { width:100%; height:100%; object-fit:contain; border-radius:var(--r-sm); border:1px solid var(--border); background:#fff; padding:4px; }
    .gal-item .rm-btn { position:absolute; top:-6px; right:-6px; width:22px; height:22px; background:#ef4444; color:#fff; border:none; border-radius:50%; font-size:13px; line-height:1; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 1px 4px rgba(0,0,0,.2); transition:background .15s; }
    .gal-item .rm-btn:hover { background:#dc2626; }
    .gal-item.marked-rm { opacity:.3; filter:grayscale(1); }
    .gal-item.marked-rm .rm-btn { background:#9ca3af !important; cursor:not-allowed; }
    .gal-upload-area { border:2px dashed var(--border); border-radius:var(--r-md); padding:22px; background:var(--surface); cursor:pointer; transition:var(--ease); text-align:center; }
    .gal-upload-area:hover { border-color:var(--brand); background:var(--brand-light); }
    .gal-upload-area i { font-size:28px; color:#d1d5db; display:block; margin-bottom:6px; }
    .gal-upload-area span { font-size:13px; color:var(--muted); }
    .gal-upload-area strong { color:var(--brand); }

    /* ── Quill ── */
    #quill-editor { min-height:140px; font-size:13.5px; }
    .ql-toolbar { border-radius:var(--r-sm) var(--r-sm) 0 0 !important; border-color:var(--border) !important; }
    .ql-container { border-radius:0 0 var(--r-sm) var(--r-sm) !important; border-color:var(--border) !important; font-size:13.5px !important; }

    /* ── Tags ── */
    .tags-wrap { border:1.5px solid var(--border); border-radius:var(--r-sm); padding:6px 10px; display:flex; flex-wrap:wrap; gap:6px; min-height:44px; cursor:text; transition:border-color .15s; }
    .tags-wrap:focus-within { border-color:var(--brand); box-shadow:0 0 0 3px rgba(232,23,74,.1); }
    .tag { background:var(--brand-light); color:var(--brand); border-radius:20px; padding:3px 10px; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:5px; }
    .tag button { background:none; border:none; cursor:pointer; color:var(--brand); padding:0; font-size:14px; line-height:1; }
    .tag-text-input { border:none; outline:none; font-size:13px; min-width:120px; flex:1; background:transparent; }

    /* ── Video ── */
    .video-type-select { border:1.5px solid var(--border); border-radius:var(--r-sm); padding:9px 14px; font-size:13px; outline:none; background:#fff; appearance:none; -webkit-appearance:none; background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position:right 10px center; background-repeat:no-repeat; background-size:18px; padding-right:36px; min-width:180px; }

    /* ── Footer ── */
    .form-footer { display:flex; justify-content:flex-end; gap:12px; padding-top:8px; }
    .btn-cancel { background:transparent; border:1.5px solid var(--border); color:var(--muted); border-radius:var(--r-sm); padding:10px 28px; font-size:13px; font-weight:500; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; transition:var(--ease); }
    .btn-cancel:hover { background:var(--surface); color:var(--dark); }
    .btn-submit { background:var(--brand); border:none; color:#fff; border-radius:var(--r-sm); padding:10px 34px; font-size:13px; font-weight:600; cursor:pointer; box-shadow:0 2px 10px rgba(232,23,74,.25); transition:var(--ease); display:inline-flex; align-items:center; gap:7px; }
    .btn-submit:hover { background:var(--brand-hover); transform:translateY(-1px); }

    /* ── Modal delete ── */
    .modal-content { border-radius:var(--r-lg) !important; border:none !important; box-shadow:0 20px 60px rgba(0,0,0,.15) !important; }
</style>

{{-- ── JS Vars ── --}}
<script>
    var SUBCATEGORY_URL     = '{{ url("admin/products/subcategories") }}';
    var CURRENT_SUB_ID      = '{{ old("sub_category_id", $product->sub_category_id ?? "") }}';
    var CURRENT_CATEGORY_ID = '{{ old("category_id", $product->category_id ?? "") }}';
</script>

{{-- Breadcrumb --}}
<div class="breadcrumb-nav">
    <a href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Products</a>
    <i class="bi bi-chevron-right breadcrumb-sep"></i>
    <span>Edit Product</span>
</div>
<h4 class="page-title">Edit Product</h4>

@if($errors->any())
    <div class="alert-err">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form action="{{ route('products.update', $product->id) }}" method="POST"
      enctype="multipart/form-data" id="productForm">
    @csrf
    @method('PUT')

    {{-- ════ PRODUCT INFO ════ --}}
    <div class="section-card">
        <p class="section-head"><i class="bi bi-info-circle"></i> Product Info</p>
        <div class="section-body">

            <div class="mb-field">
                <label class="field-label">Product Name <span class="req">*</span></label>
                <input type="text" name="name"
                       class="field-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       placeholder="Enter product name"
                       value="{{ old('name', $product->name) }}">
                @error('name')
                    <div class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="mb-field">
                <label class="field-label">Short Description <span class="req">*</span></label>
                <textarea name="short_description" rows="3"
                          class="field-input {{ $errors->has('short_description') ? 'is-invalid' : '' }}"
                          placeholder="Brief product description…">{{ old('short_description', $product->short_description) }}</textarea>
                @error('short_description')
                    <div class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div>
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                    <label class="field-label" style="margin:0;">Description</label>
                    <button type="button" class="btn-ai">
                        <i class="bi bi-stars"></i> Generate via AI
                    </button>
                </div>
                <div id="quill-editor">{!! old('description', $product->description) !!}</div>
                <input type="hidden" name="description" id="description-input">
            </div>

        </div>
    </div>

    {{-- ════ GENERAL INFORMATION ════ --}}
    <div class="section-card">
        <p class="section-head"><i class="bi bi-grid"></i> General Information</p>
        <div class="section-body">

            {{-- Row 1: Category / SubCategory / Brand --}}
            <div class="g3 mb-field">
                <div>
                    <label class="field-label">Category <span class="req">*</span></label>
                    <select name="category_id" id="categorySelect"
                            class="field-input {{ $errors->has('category_id') ? 'is-invalid' : '' }}">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="field-label">Sub Category</label>
                    <select name="sub_category_id" id="subCategorySelect" class="field-input">
                        <option value="">Select Sub Category</option>
                        @foreach($subCategories as $sub)
                            <option value="{{ $sub->id }}"
                                {{ old('sub_category_id', $product->sub_category_id) == $sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="sub-loading" id="subLoading">
                        <i class="bi bi-arrow-repeat"></i> Loading…
                    </div>
                </div>

                <div>
                    <label class="field-label">Brand</label>
                    <select name="brand_id" class="field-input">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}"
                                {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Row 2: Color / Unit / Size --}}
            <div class="g3 mb-field">
                <div>
                    <label class="field-label">Color</label>
                    <select name="color" class="field-input">
                        <option value="">Select Color</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->name }}"
                                {{ old('color', $product->color) == $color->name ? 'selected' : '' }}>
                                {{ $color->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Unit</label>
                    <select name="unit" class="field-input">
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->name }}"
                                {{ old('unit', $product->unit) == $unit->name ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Size</label>
                    <select name="size" class="field-input">
                        <option value="">Select Size</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->name }}"
                                {{ old('size', $product->size) == $size->name ? 'selected' : '' }}>
                                {{ $size->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Row 3: SKU + Barcode --}}
            <div class="g2">
                <div>
                    <div class="sku-row">
                        <label class="field-label" style="margin:0;">
                            Product SKU <span class="req">*</span>
                        </label>
                        <button type="button" class="btn-gen-sku" onclick="generateSku()">
                            <i class="bi bi-arrow-repeat"></i> Generate
                        </button>
                    </div>
                    <input type="text" name="sku" id="skuInput"
                           class="field-input {{ $errors->has('sku') ? 'is-invalid' : '' }}"
                           placeholder="e.g. SKU-ABCD-1234"
                           value="{{ old('sku', $product->sku) }}">
                    @error('sku')
                        <div class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="field-label">
                        Barcode
                        <span class="field-hint">(SKU পরিবর্তন হলে auto-update · manually edit করা যাবে)</span>
                    </label>
                    <div class="barcode-row">
                        <i class="bi bi-upc-scan bc-icon"></i>
                        <span class="bc-label">Code:</span>
                        <input type="text" name="barcode" id="barcodeInput" class="bc-input"
                               value="{{ old('barcode', $product->barcode) }}"
                               placeholder="Auto-generated from SKU">
                        @if($product->barcode)
                            <a href="{{ route('products.barcode', $product->id) }}"
                               target="_blank" class="bc-view-link">
                                View Barcode →
                            </a>
                        @endif
                    </div>
                    @error('barcode')
                        <div class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

        </div>
    </div>

    {{-- ════ PRICE ════ --}}
    <div class="section-card">
        <p class="section-head"><i class="bi bi-currency-dollar"></i> Price Information</p>
        <div class="section-body">

            <div class="g3 mb-field">
                <div>
                    <label class="field-label">Buying Price <span class="req">*</span></label>
                    <input type="number" name="buying_price" step="0.01" min="0"
                           class="field-input {{ $errors->has('buying_price') ? 'is-invalid' : '' }}"
                           placeholder="0.00"
                           value="{{ old('buying_price', $product->buying_price) }}">
                    @error('buying_price')
                        <div class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="field-label">Selling Price <span class="req">*</span></label>
                    <input type="number" name="selling_price" step="0.01" min="0"
                           class="field-input {{ $errors->has('selling_price') ? 'is-invalid' : '' }}"
                           placeholder="0.00"
                           value="{{ old('selling_price', $product->selling_price) }}">
                    @error('selling_price')
                        <div class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="field-label">Discount Price</label>
                    <input type="number" name="discount_price" step="0.01" min="0"
                           class="field-input"
                           placeholder="0.00"
                           value="{{ old('discount_price', $product->discount_price) }}">
                </div>
            </div>

            <div style="max-width:320px;">
                <label class="field-label">Stock Quantity</label>
                <input type="number" name="stock_quantity" min="0"
                       class="field-input"
                       placeholder="0"
                       value="{{ old('stock_quantity', $product->stock_quantity) }}">
            </div>

        </div>
    </div>

    {{-- ════ IMAGES ════ --}}
    <div class="section-card">
        <p class="section-head"><i class="bi bi-images"></i> Images</p>
        <div class="section-body">

            {{-- ── Thumbnail ── --}}
            <div class="mb-field" style="border-bottom:1px solid var(--border);padding-bottom:22px;">
                <label class="field-label" style="margin-bottom:12px;">
                    Thumbnail
                    <span class="field-hint">(Recommended: 500×500 px)</span>
                </label>

                <div class="thumb-upload-wrap">

                    {{-- Current thumbnail --}}
                    @if($product->thumbnail)
                        <div class="current-thumb-box">
                            {{-- ★ FIX: path is already "uploads/product/filename.ext" → use asset() directly --}}
                            <img src="{{ asset($product->thumbnail) }}"
                                 class="current-thumb" id="currentThumbnailImg"
                                 alt="Current thumbnail"
                                 onerror="this.src='https://via.placeholder.com/90?text=No+Image'">
                            <span class="current-label">Current</span>
                        </div>
                        <div class="thumb-arrow"><i class="bi bi-arrow-right"></i></div>
                    @endif

                    {{-- New upload box --}}
                    <div>
                        <div class="img-upload-box" id="thumbnailBox"
                             onclick="document.getElementById('thumbnailInput').click()">
                            <img class="preview-img" id="thumbnailPreview" src="" alt="Preview">
                            <div class="upload-ph" id="thumbnailPlaceholder">
                                <i class="bi bi-cloud-arrow-up" style="font-size:36px;color:var(--brand);opacity:.6;"></i>
                                <span>{{ $product->thumbnail ? 'Replace Image' : 'Upload Image' }}</span>
                                <small>(optional)</small>
                            </div>
                        </div>
                        <input type="file" id="thumbnailInput" name="thumbnail"
                               accept="image/jpg,image/jpeg,image/png,image/webp"
                               style="display:none;" onchange="previewThumbnail(this)">
                        <p class="img-hint">jpg, jpeg, png, webp · Max 2 MB</p>
                        @error('thumbnail')
                            <div class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ── Gallery ── --}}
            <div>
                <label class="field-label" style="margin-bottom:12px;">
                    Gallery Images
                    <span class="field-hint">(Multiple allowed)</span>
                </label>

                {{-- Existing gallery --}}
                @if($product->gallery_images && count($product->gallery_images))
                    <p style="font-size:12px;color:var(--muted);margin-bottom:10px;">
                        Current images — click <strong style="color:#ef4444;">×</strong> to mark for removal on save:
                    </p>
                    <div class="gallery-grid" id="existingGallery">
                        @foreach($product->gallery_images as $img)
                            <div class="gal-item" id="existing_{{ md5($img) }}">
                                {{-- ★ FIX: path is already "uploads/product/filename.ext" → use asset() directly --}}
                                <img src="{{ asset($img) }}"
                                     alt="Gallery image"
                                     onerror="this.src='https://via.placeholder.com/100?text=Error'">
                                <button type="button" class="rm-btn"
                                        onclick="markRemoveExisting('{{ $img }}','{{ md5($img) }}')">×</button>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Hidden inputs for images to remove --}}
                <div id="removeInputsContainer"></div>

                {{-- Upload more --}}
                <div class="gal-upload-area" onclick="document.getElementById('galleryInput').click()">
                    <i class="bi bi-plus-circle"></i>
                    <span>Click to add more gallery images &nbsp;·&nbsp;
                        <strong>Hold Ctrl / Cmd for multiple</strong>
                    </span>
                    <br><small style="font-size:12px;color:#b0b7c3;">jpg, jpeg, png, webp · Max 2 MB each</small>
                </div>
                <input type="file" id="galleryInput" name="gallery_images[]"
                       accept="image/jpg,image/jpeg,image/png,image/webp"
                       multiple style="display:none;"
                       onchange="previewNewGallery(this)">
                <div class="gallery-grid" id="newGallery" style="margin-top:14px;"></div>
                @error('gallery_images.*')
                    <div class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

        </div>
    </div>

    {{-- ════ VIDEO ════ --}}
    <div class="section-card">
        <p class="section-head"><i class="bi bi-play-btn"></i> Product Video</p>
        <div class="section-body">
            <div style="display:flex;align-items:flex-start;gap:18px;flex-wrap:wrap;">
                <div>
                    <label class="field-label">Video Type</label>
                    <select name="video_type" id="videoType"
                            class="video-type-select" onchange="handleVideoType()">
                        <option value="file"    {{ old('video_type', $product->video_type) === 'file'    ? 'selected' : '' }}>Upload Video File</option>
                        <option value="url"     {{ old('video_type', $product->video_type) === 'url'     ? 'selected' : '' }}>External URL</option>
                        <option value="youtube" {{ old('video_type', $product->video_type) === 'youtube' ? 'selected' : '' }}>YouTube Link</option>
                    </select>
                </div>

                <div id="videoFileWrap" style="flex:1;min-width:220px;">
                    <label class="field-label">Upload Video</label>
                    <input type="file" name="video_file"
                           accept="video/mp4,video/avi,video/mov,video/wmv"
                           class="field-input" style="padding:6px;">
                    @if($product->video && $product->video_type === 'file')
                        <p class="img-hint" style="color:var(--brand);">
                            <i class="bi bi-play-circle"></i> Current: {{ basename($product->video) }}
                        </p>
                    @endif
                    <p class="img-hint">MP4, AVI, MOV, WMV · Max 50 MB</p>
                </div>

                <div id="videoUrlWrap" style="display:none;flex:1;min-width:220px;">
                    <label class="field-label">Video URL</label>
                    <input type="text" name="video_url" class="field-input"
                           placeholder="https://…"
                           value="{{ in_array(old('video_type', $product->video_type), ['url','youtube'])
                                    ? old('video', $product->video)
                                    : '' }}">
                </div>
            </div>
        </div>
    </div>

    {{-- ════ SEO ════ --}}
    <div class="section-card">
        <p class="section-head"><i class="bi bi-search"></i> SEO Information</p>
        <div class="section-body">

            <div class="mb-field">
                <label class="field-label">Meta Title</label>
                <input type="text" name="meta_title" class="field-input"
                       placeholder="Page title for search engines"
                       value="{{ old('meta_title', $product->meta_title) }}">
            </div>

            <div class="mb-field">
                <label class="field-label">Meta Description</label>
                <textarea name="meta_description" class="field-input" rows="3"
                          placeholder="Brief description for search engines…">{{ old('meta_description', $product->meta_description) }}</textarea>
            </div>

            <div>
                <label class="field-label">Meta Keywords</label>
                <div class="tags-wrap" id="tagsWrap"
                     onclick="document.getElementById('tagsInput').focus()">
                    <input type="text" id="tagsInput" class="tag-text-input"
                           placeholder="Type keyword and press Enter">
                </div>
                <input type="hidden" name="meta_keywords" id="metaKeywordsInput"
                       value="{{ old('meta_keywords', $product->meta_keywords) }}">
                <p class="img-hint">Press Enter after each keyword</p>
            </div>

        </div>
    </div>

    {{-- ════ FOOTER ════ --}}
    <div class="form-footer">
        <a href="{{ route('products.index') }}" class="btn-cancel">
            <i class="bi bi-x-lg"></i> Cancel
        </a>
        <button type="submit" class="btn-submit">
            <i class="bi bi-check-lg"></i> Save Changes
        </button>
    </div>

</form>

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
/* ══════════════════════════════════════
   Quill Rich Text Editor
══════════════════════════════════════ */
var quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ header:[false,1,2,3] }, { font:[] }],
            ['bold','italic','underline','strike','blockquote'],
            [{ list:'ordered' }, { list:'bullet' }, { align:[] }],
            [{ color:[] }, { background:[] }],
            ['link','image','video'],
            ['clean']
        ]
    }
});
document.getElementById('productForm').addEventListener('submit', function () {
    document.getElementById('description-input').value = quill.root.innerHTML;
});

/* ══════════════════════════════════════
   SKU → Barcode Auto-Sync
══════════════════════════════════════ */
document.getElementById('skuInput').addEventListener('input', function () {
    document.getElementById('barcodeInput').value = this.value;
});

/* ══════════════════════════════════════
   Generate SKU
══════════════════════════════════════ */
function generateSku() {
    var rand   = Math.random().toString(36).substring(2, 8).toUpperCase();
    var ts     = Date.now().toString().slice(-4);
    var newSku = 'SKU-' + rand + '-' + ts;
    document.getElementById('skuInput').value    = newSku;
    document.getElementById('barcodeInput').value = newSku;
}

/* ══════════════════════════════════════
   Sub-Category AJAX Loader
══════════════════════════════════════ */
function loadSubCategories(categoryId, selectedId) {
    var select  = document.getElementById('subCategorySelect');
    var loading = document.getElementById('subLoading');

    select.innerHTML = '<option value="">Select Sub Category</option>';

    if (!categoryId) {
        select.innerHTML = '<option value="">— Select Category First —</option>';
        return;
    }

    select.disabled    = true;
    loading.style.display = 'inline';

    fetch(SUBCATEGORY_URL + '/' + categoryId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(function (data) {
        select.innerHTML = '<option value="">Select Sub Category</option>';
        if (!data || data.length === 0) {
            select.innerHTML += '<option value="" disabled>No sub-categories found</option>';
        } else {
            data.forEach(function (sub) {
                var opt      = document.createElement('option');
                opt.value    = sub.id;
                opt.textContent = sub.name;
                var matchId  = selectedId || CURRENT_SUB_ID;
                if (matchId && String(sub.id) === String(matchId)) opt.selected = true;
                select.appendChild(opt);
            });
        }
    })
    .catch(function (err) {
        console.error('SubCategory load error:', err);
        select.innerHTML = '<option value="">⚠ Error loading — try again</option>';
    })
    .finally(function () {
        select.disabled      = false;
        loading.style.display = 'none';
    });
}

document.getElementById('categorySelect').addEventListener('change', function () {
    CURRENT_SUB_ID = '';
    loadSubCategories(this.value, null);
});

/* ══════════════════════════════════════
   Thumbnail Preview
══════════════════════════════════════ */
function previewThumbnail(input) {
    var file = input.files[0];
    if (!file) return;

    var reader = new FileReader();
    reader.onload = function (e) {
        var preview     = document.getElementById('thumbnailPreview');
        var placeholder = document.getElementById('thumbnailPlaceholder');
        var currentImg  = document.getElementById('currentThumbnailImg');

        if (currentImg) currentImg.style.opacity = '0.4';
        preview.src            = e.target.result;
        preview.style.display  = 'block';
        placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

/* ══════════════════════════════════════
   New Gallery Preview (with dedup)
══════════════════════════════════════ */
var galleryFileStore = new DataTransfer();

function previewNewGallery(input) {
    var grid  = document.getElementById('newGallery');
    var files = Array.from(input.files);

    files.forEach(function (file) {
        // Deduplicate by name + size
        var exists = false;
        for (var i = 0; i < galleryFileStore.files.length; i++) {
            var f = galleryFileStore.files[i];
            if (f.name === file.name && f.size === file.size) { exists = true; break; }
        }
        if (exists) return;
        galleryFileStore.items.add(file);

        var reader = new FileReader();
        reader.onload = function (e) {
            var item          = document.createElement('div');
            item.className    = 'gal-item';
            item.dataset.name = file.name;
            item.dataset.size = file.size;

            var img   = document.createElement('img');
            img.src   = e.target.result;
            img.alt   = file.name;

            var btn     = document.createElement('button');
            btn.type    = 'button';
            btn.className = 'rm-btn';
            btn.innerHTML = '×';
            btn.onclick   = function () { removeNewGallery(file.name, file.size); };

            item.appendChild(img);
            item.appendChild(btn);
            grid.appendChild(item);
        };
        reader.readAsDataURL(file);
    });

    input.files = galleryFileStore.files;
}

function removeNewGallery(filename, filesize) {
    var newStore = new DataTransfer();
    for (var i = 0; i < galleryFileStore.files.length; i++) {
        var f = galleryFileStore.files[i];
        if (!(f.name === filename && f.size == filesize)) newStore.items.add(f);
    }
    galleryFileStore = newStore;
    document.getElementById('galleryInput').files = galleryFileStore.files;

    var item = document.querySelector('#newGallery .gal-item[data-name="' + filename + '"]');
    if (item) item.remove();
}

/* ══════════════════════════════════════
   Existing Gallery → Mark for Removal
══════════════════════════════════════ */
function markRemoveExisting(path, hash) {
    var container = document.getElementById('removeInputsContainer');
    if (container.querySelector('input[value="' + path + '"]')) return; // already marked

    var inp   = document.createElement('input');
    inp.type  = 'hidden';
    inp.name  = 'remove_images[]';
    inp.value = path;
    container.appendChild(inp);

    var item = document.getElementById('existing_' + hash);
    if (item) {
        item.classList.add('marked-rm');
        var btn = item.querySelector('.rm-btn');
        if (btn) { btn.disabled = true; btn.title = 'Will be removed on save'; }
    }
}

/* ══════════════════════════════════════
   Video Type Toggle
══════════════════════════════════════ */
function handleVideoType() {
    var type = document.getElementById('videoType').value;
    document.getElementById('videoFileWrap').style.display = (type === 'file') ? 'block' : 'none';
    document.getElementById('videoUrlWrap').style.display  = (type !== 'file') ? 'block' : 'none';
}
handleVideoType(); // run on page load

/* ══════════════════════════════════════
   Meta Keywords Tags Input
══════════════════════════════════════ */
var tags = [];
var savedKeywords = document.getElementById('metaKeywordsInput').value;
if (savedKeywords) {
    tags = savedKeywords.split(',').map(function (t) { return t.trim(); }).filter(Boolean);
    renderTags();
}

document.getElementById('tagsInput').addEventListener('keydown', function (e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        var val = this.value.trim().replace(/,$/, '');
        if (val && !tags.includes(val)) {
            tags.push(val);
            renderTags();
            document.getElementById('metaKeywordsInput').value = tags.join(',');
        }
        this.value = '';
    }
});

function renderTags() {
    var wrap    = document.getElementById('tagsWrap');
    var inputEl = document.getElementById('tagsInput');
    wrap.querySelectorAll('.tag').forEach(function (el) { el.remove(); });
    tags.forEach(function (tag, i) {
        var span         = document.createElement('span');
        span.className   = 'tag';
        span.innerHTML   = tag + ' <button type="button" onclick="removeTag(' + i + ')">×</button>';
        wrap.insertBefore(span, inputEl);
    });
}

function removeTag(i) {
    tags.splice(i, 1);
    renderTags();
    document.getElementById('metaKeywordsInput').value = tags.join(',');
}
</script>
@endsection
