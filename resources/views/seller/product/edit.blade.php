@extends('admin.master')
@section('content')
<style>
    :root {
        --brand:       #e8174a;
        --brand-hover: #c9113e;
        --dark:        #1a1d23;
        --muted:       #6b7280;
        --border:      #e5e7eb;
        --surface:     #f8f9fc;
        --r-lg: 14px; --r-md: 10px; --r-sm: 7px;
    }

    .form-card { background:#fff; border-radius:var(--r-lg); box-shadow:0 1px 4px rgba(0,0,0,.06); border:1px solid var(--border); padding:24px; margin-bottom: 24px; }
    .form-title { font-size:16px; font-weight:700; color:var(--dark); margin-bottom:20px; display:flex; align-items:center; gap:8px; border-bottom: 1px solid var(--border); padding-bottom: 12px; }
    .form-label { font-size:13px; font-weight:600; color:var(--dark); margin-bottom:6px; }
    .text-danger { color:#ef4444; }
    .form-control, .form-select { border-radius:var(--r-sm); border:1px solid var(--border); padding:10px 14px; font-size:14px; color:var(--dark); box-shadow:none; }
    .form-control:focus, .form-select:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(232,23,74,.1); }
    .btn-submit { background:var(--brand); color:#fff; border:none; border-radius:var(--r-md); padding:10px 24px; font-size:14px; font-weight:600; cursor:pointer; }
    .btn-submit:hover { background:var(--brand-hover); }
    .btn-cancel { background:transparent; color:var(--muted); border:1px solid var(--border); border-radius:var(--r-md); padding:10px 24px; font-size:14px; font-weight:600; text-decoration:none; }
    .btn-cancel:hover { background:var(--surface); color:var(--dark); }
    
    .img-upload-box {
        border: 2px dashed #d1d5db;
        border-radius: var(--r-md);
        padding: 40px;
        text-align: center;
        background: #fafafa;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
    }
    .img-upload-box:hover { border-color: var(--brand); background: #fff5f7; }
    .img-upload-box input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
</style>

<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('seller.product.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0 fw-bold" style="color:var(--dark);">Edit Product</h4>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('seller.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')
        
        {{-- Product Info --}}
        <div class="form-card">
            <div class="form-title">Product Info</div>
            <div class="row g-4">
                <div class="col-md-12">
                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Enter Product Name" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Short Description</label>
                    <textarea name="short_description" class="form-control" rows="3" placeholder="Enter short description">{{ old('short_description', $product->short_description) }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label d-flex align-items-center gap-2">
                        Description
                        <button type="button" class="btn btn-sm" style="background:#e8174a; color:#fff; font-size:12px; padding:2px 8px; border-radius:4px; border:none;">
                            <i class="bi bi-robot"></i> Generate Via AI
                        </button>
                    </label>
                    <textarea name="description" id="description" class="form-control" rows="6">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- General Information --}}
        <div class="form-card">
            <div class="form-title">General Information</div>
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label">Select Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name ?? 'Category '.$category->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Select Sub Category</label>
                    <select name="sub_category_id" class="form-select">
                        <option value="">Select Sub Category</option>
                        @foreach($subcategories as $sub)
                            <option value="{{ $sub->id }}" {{ old('sub_category_id', $product->sub_category_id) == $sub->id ? 'selected' : '' }}>{{ $sub->name ?? 'SubCategory '.$sub->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Select Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name ?? 'Brand '.$brand->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Select Color</label>
                    <select name="color" class="form-select">
                        <option value="">Select Color</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->name ?? '' }}" {{ old('color', $product->color) == ($color->name ?? '') ? 'selected' : '' }}>{{ $color->name ?? 'Color '.$color->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Select Unit</label>
                    <select name="unit" class="form-select">
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->name ?? '' }}" {{ old('unit', $product->unit) == ($unit->name ?? '') ? 'selected' : '' }}>{{ $unit->name ?? 'Unit '.$unit->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Select Size</label>
                    <select name="size" class="form-select">
                        <option value="">Select Size</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->name ?? '' }}" {{ old('size', $product->size) == ($size->name ?? '') ? 'selected' : '' }}>{{ $size->name ?? 'Size '.$size->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label d-flex justify-content-between align-items-center">
                        <span>Product SKU <i class="bi bi-info-circle text-muted ms-1" style="font-size:12px;"></i></span>
                        <a href="javascript:void(0)" onclick="generateSKU()" class="text-primary text-decoration-none" style="font-size:13px;font-weight:600;">Generate Code</a>
                    </label>
                    <input type="text" name="sku" id="sku_input" class="form-control" placeholder="Ex: 134543" value="{{ old('sku', $product->sku) }}">
                </div>
            </div>
        </div>

        {{-- Frontend Placement Options --}}
        <div class="form-card">
            <div class="form-title">Frontend Placement Options</div>
            <div class="row g-4">
                <div class="col-md-12">
                    <div style="display:flex; flex-wrap:wrap; gap:20px; max-height: 250px; overflow-y: auto; padding: 15px; border: 1.5px solid var(--border); border-radius: var(--r-sm); background: #fff;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival', $product->is_new_arrival) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--brand);">
                            <span style="font-size:14px; font-weight:600; color:var(--dark);">New Arrival</span>
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller', $product->is_best_seller) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--brand);">
                            <span style="font-size:14px; font-weight:600; color:var(--dark);">Best Seller</span>
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="is_hot_product" value="1" {{ old('is_hot_product', $product->is_hot_product) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--brand);">
                            <span style="font-size:14px; font-weight:600; color:var(--dark);">Hot Product</span>
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="is_flash_sale" value="1" {{ old('is_flash_sale', $product->is_flash_sale) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--brand);">
                            <span style="font-size:14px; font-weight:600; color:var(--dark);">Flash Sale</span>
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="is_just_for_you" value="1" {{ old('is_just_for_you', $product->is_just_for_you) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--brand);">
                            <span style="font-size:14px; font-weight:600; color:var(--dark);">Just For You</span>
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="is_popular" value="1" {{ old('is_popular', $product->is_popular) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--brand);">
                            <span style="font-size:14px; font-weight:600; color:var(--dark);">Popular Product</span>
                        </label>

                        @php
                            $savedSections = old('frontend_sections', $product->frontend_sections);
                            if (is_string($savedSections)) {
                                $savedSections = json_decode($savedSections, true);
                            }
                            $savedSections = is_array($savedSections) ? $savedSections : [];
                        @endphp

                        @foreach($categories as $cat)
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="frontend_sections[]" value="{{ $cat->name }}" 
                                    {{ in_array($cat->name, $savedSections) ? 'checked' : '' }} 
                                    style="width:16px;height:16px;accent-color:var(--brand);">
                                <span style="font-size:14px; font-weight:600; color:var(--dark);">{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <small class="text-muted d-block mt-2">Toggle where this product will be displayed on the storefront home page.</small>
                </div>
            </div>
        </div>

        {{-- Payment Options --}}
        <div class="form-card">
            <div class="form-title">Payment Options</div>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="d-flex align-items-center gap-3 p-3 border rounded-3 cursor-pointer">
                        <input type="checkbox" name="cash_on_delivery" value="1" {{ old('cash_on_delivery', $product->cash_on_delivery) ? 'checked' : '' }} style="width:20px;height:20px;accent-color:#22c55e;">
                        <div>
                            <div class="fw-bold" style="font-size:14px;">Cash on Delivery</div>
                            <small class="text-muted">Allow customers to pay on delivery</small>
                        </div>
                    </label>
                </div>
                <div class="col-md-6">
                    <label class="d-flex align-items-center gap-3 p-3 border rounded-3 cursor-pointer">
                        <input type="checkbox" name="online_payment" value="1" {{ old('online_payment', $product->online_payment) ? 'checked' : '' }} style="width:20px;height:20px;accent-color:#3b82f6;">
                        <div>
                            <div class="fw-bold" style="font-size:14px;">Online Payment</div>
                            <small class="text-muted">Allow customers to pay via Online</small>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Price Information --}}
        <div class="form-card">
            <div class="form-title">Price Information</div>
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label">Buying Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="buying_price" class="form-control" placeholder="Buying Price" value="{{ old('buying_price', $product->buying_price) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="selling_price" class="form-control" placeholder="Selling Price" value="{{ old('selling_price', $product->selling_price) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Discount Price</label>
                    <input type="number" step="0.01" name="discount_price" class="form-control" placeholder="0" value="{{ old('discount_price', $product->discount_price) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Current Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" placeholder="Current Stock Quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}">
                </div>
            </div>
        </div>

        {{-- Shipping Options --}}
        <div class="form-card">
            <div class="form-title">Shipping Options</div>
            <div class="row g-4">
                <div class="col-md-12">
                    <label class="d-flex align-items-center gap-3 p-3 border rounded-3 cursor-pointer">
                        <input type="checkbox" name="is_shipping_charge" value="1" {{ old('is_shipping_charge', $product->is_shipping_charge) ? 'checked' : '' }} style="width:20px;height:20px;accent-color:var(--brand);">
                        <div>
                            <div class="fw-bold" style="font-size:14px;">Enable Shipping & Delivery Info</div>
                            <small class="text-muted">If checked, shipping charge and delivery area info will be visible on the product page.</small>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Images --}}
        <div class="form-card">
            <div class="form-title">Images</div>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Thumbnail (Ratio 1:1) <small class="text-muted">(Leave empty to keep current)</small></label>
                    <div class="img-upload-box">
                        <input type="file" name="thumbnail" accept="image/*" onchange="previewImg(this, 'preview1')">
                        <div id="placeholder1" style="display: {{ $product->thumbnail ? 'none' : 'block' }};">
                            <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                            <p class="mt-2 mb-0 text-muted">Click or drag image here (500x500)</p>
                            <small class="text-muted d-block mt-2">Supported formats: jpg, jpeg, png, webp, svg, gif · Max 10MB</small>
                        </div>
                        <img id="preview1" src="{{ $product->thumbnail ? asset($product->thumbnail) : '#' }}" alt="Preview" style="max-width:100%; max-height:200px; display:{{ $product->thumbnail ? 'block' : 'none' }}; margin:0 auto; border-radius:8px;">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gallery Images (Ratio 1:1) <small class="text-muted">(Leave empty to keep current)</small></label>
                    <div class="img-upload-box">
                        <input type="file" name="gallery_images[]" accept="image/*" multiple onchange="previewMultipleImg(this, 'preview-gallery')">
                        <div id="placeholder-gallery" style="display: {{ !empty($product->gallery_images) ? 'none' : 'block' }};">
                            <i class="bi bi-images fs-2 text-muted"></i>
                            <p class="mt-2 mb-0 text-muted">Click or drag images here (500x500)</p>
                            <small class="text-muted d-block mt-2">You can select multiple files (Max 10MB each)</small>
                        </div>
                        <div id="preview-gallery" class="d-flex flex-wrap gap-2 justify-content-center mt-3" style="display: {{ !empty($product->gallery_images) ? 'flex' : 'none' }};">
                            @if(!empty($product->gallery_images))
                                @foreach($product->gallery_images as $img)
                                    <img src="{{ asset($img) }}" style="width:80px; height:80px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Video --}}
        <div class="form-card">
            <div class="form-title">
                <i class="bi bi-play-circle-fill me-2"></i> Upload or Add Product Video
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label">Select Video Type</label>
                    <select name="video_type" id="video_type" class="form-select" onchange="toggleVideoInput()">
                        <option value="upload" {{ old('video_type', $product->video_type) == 'upload' ? 'selected' : '' }}>Upload Video File</option>
                        <option value="youtube" {{ old('video_type', $product->video_type) == 'youtube' ? 'selected' : '' }}>YouTube Link</option>
                        <option value="vimeo" {{ old('video_type', $product->video_type) == 'vimeo' ? 'selected' : '' }}>Vimeo Link</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <div id="video_file_container">
                        <label class="form-label">Upload Product Video <small class="text-muted">(Leave empty to keep current)</small></label>
                        <input type="file" name="video_file" class="form-control" accept="video/mp4,video/x-m4v,video/*">
                        @if($product->video_type == 'upload' && $product->video)
                            <small class="text-success mt-1 d-block"><i class="bi bi-check-circle"></i> Video uploaded</small>
                        @endif
                        <small class="text-muted d-block mt-1">Supported formats: MP4, AVI, MOV, WMV</small>
                    </div>
                    <div id="video_link_container" style="display: none;">
                        <label class="form-label">Video Link</label>
                        <input type="url" name="video_link" class="form-control" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('video_link', $product->video_type != 'upload' ? $product->video : '') }}">
                        <small class="text-muted d-block mt-1">Paste the full YouTube/Vimeo URL here.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- SEO Information --}}
        @php
            $hasSeoData = $product->meta_title || $product->meta_description || $product->meta_keywords;
        @endphp
        <div class="form-card">
            <div class="form-title d-flex justify-content-between align-items-center">
                <div><i class="bi bi-bar-chart-fill me-2"></i> SEO Information</div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="seo_toggle" role="switch" onchange="toggleSEO()" {{ $hasSeoData ? 'checked' : '' }}>
                </div>
            </div>
            <div class="row g-4" id="seo_body" style="display: {{ $hasSeoData ? 'flex' : 'none' }};">
                <div class="col-md-12">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" placeholder="Meta Title" value="{{ old('meta_title', $product->meta_title) }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="3" placeholder="Meta Description">{{ old('meta_description', $product->meta_description) }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control" placeholder="Write keywords and Press enter to add new one" value="{{ old('meta_keywords', $product->meta_keywords) }}">
                    <small class="text-muted mt-1 d-block">Write keywords and Press enter to add new one</small>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end align-items-center gap-3">
            <a href="{{ route('seller.product.index') }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-submit">Update Product</button>
        </div>
    </form>
</div>

<script>
    function previewImg(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
                document.getElementById(previewId).style.display = 'block';
                document.getElementById(previewId.replace('preview', 'placeholder')).style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleSEO() {
        var body = document.getElementById('seo_body');
        var toggle = document.getElementById('seo_toggle');
        if (toggle.checked) {
            body.style.display = 'flex';
        } else {
            body.style.display = 'none';
        }
    }

    function toggleVideoInput() {
        var type = document.getElementById('video_type').value;
        if (type === 'upload') {
            document.getElementById('video_file_container').style.display = 'block';
            document.getElementById('video_link_container').style.display = 'none';
        } else {
            document.getElementById('video_file_container').style.display = 'none';
            document.getElementById('video_link_container').style.display = 'block';
        }
    }

    function previewMultipleImg(input, previewContainerId) {
        var container = document.getElementById(previewContainerId);
        var placeholder = document.getElementById('placeholder-gallery');
        
        if (input.files && input.files.length > 0) {
            container.innerHTML = ''; // clear previous
            placeholder.style.display = 'none';
            container.style.display = 'flex';
            
            for (let i = 0; i < input.files.length; i++) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '6px';
                    img.style.border = '1px solid #e5e7eb';
                    container.appendChild(img);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    }

    function generateSKU() {
        let sku = Math.floor(100000 + Math.random() * 900000);
        document.getElementById('sku_input').value = sku;
    }

    document.addEventListener("DOMContentLoaded", function() {
        toggleVideoInput();
        var checkSummernote = setInterval(function() {
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.summernote !== 'undefined') {
                clearInterval(checkSummernote);
                jQuery('#description').summernote({
                    height: 200,
                    placeholder: 'Enter product description here...',
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview']]
                    ]
                });
            }
        }, 100);
    });
</script>
@endsection
