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

    .ai-btn { background:#fff; border:1px solid var(--brand); color:var(--brand); border-radius:20px; padding:2px 12px; font-size:12px; font-weight:600; display:flex; align-items:center; gap:5px; }
    .ai-btn:hover { background:var(--brand); color:#fff; }
</style>

<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold" style="color:var(--dark);">Add New Digital Product</h4>
        <a href="{{ route('seller.digital_product.index') }}" class="btn-cancel">Back to List</a>
    </div>

    <form action="{{ route('seller.digital_product.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            {{-- Product Info --}}
            <div class="col-12">
                <div class="form-card">
                    <div class="form-title">Product Info</div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter Product Name" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Short Description <span class="text-danger">*</span></label>
                            <textarea name="short_description" class="form-control" rows="3" placeholder="Enter short description" required></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label mb-0">Description <span class="text-danger">*</span></label>
                                <button type="button" class="ai-btn"><i class="bi bi-chat-dots-fill"></i> Generate Via AI</button>
                            </div>
                            <textarea name="description" id="editor" class="form-control" rows="6"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- General Information --}}
            <div class="col-12">
                <div class="form-card">
                    <div class="form-title">General Information</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Select Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Select Sub Categories</label>
                            <select name="sub_category_id" class="form-select">
                                <option value="">Select Sub Category</option>
                                @foreach($subcategories as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Select Brand</label>
                            <select name="brand_id" class="form-select">
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="form-label">Product SKU <span class="text-danger">*</span></label>
                                <a href="javascript:void(0)" onclick="generateSKU()" class="text-primary small fw-bold" style="text-decoration:none;">Generate Code</a>
                            </div>
                            <input type="text" name="sku" id="sku_input" class="form-control" placeholder="Ex: 134543" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Digital Product Attachments --}}
            <div class="col-12">
                <div class="form-card">
                    <div class="form-title"><i class="bi bi-file-earmark-arrow-down-fill me-2"></i> Digital Product Attachments (Downloadable)</div>
                    
                    <div class="mb-4">
                        <label class="form-label">Digital Additional Documents</label>
                        <div class="img-upload-box" style="padding: 20px;">
                            <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                            <p class="mb-0 text-muted small">Upload digital file (ZIP, PDF, etc.)</p>
                            <input type="file" name="digital_file">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label class="form-label">License Keys</label>
                            <div id="license_keys_container">
                                <div class="d-flex gap-2 mb-2 align-items-center">
                                    <input type="text" name="license_keys[]" class="form-control" placeholder="License Key">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap; padding: 7px 15px;" onclick="generateToField(this)">Generate key</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap; padding: 7px 15px;" onclick="copyField(this)">Copy key</button>
                                    <button type="button" class="btn btn-outline-danger btn-sm px-3" style="padding: 7px 15px;" onclick="removeField(this)">X</button>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 mt-3">
                                <button type="button" class="btn btn-sm px-3" style="background: #10b981; color:#fff;" onclick="addLicenseField()">+ Add License</button>
                                <button type="button" class="btn btn-sm px-3" style="background: #e8174a; color:#fff;" onclick="bulkGenerate(3)">Bulk Generate (3)</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Price Information --}}
            <div class="col-12">
                <div class="form-card">
                    <div class="form-title">Price Information</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Buying Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="buying_price" class="form-control" placeholder="Buying Price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="selling_price" class="form-control" placeholder="10" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Discount Price</label>
                            <input type="number" step="0.01" name="discount_price" class="form-control" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Current Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control" value="1">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Shipping Options --}}
            <div class="col-12">
                <div class="form-card">
                    <div class="form-title">Shipping Options</div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="d-flex align-items-center gap-3 p-3 border rounded-3 cursor-pointer">
                                <input type="checkbox" name="is_shipping_charge" value="1" {{ old('is_shipping_charge', 1) ? 'checked' : '' }} style="width:20px;height:20px;accent-color:var(--brand);">
                                <div>
                                    <div class="fw-bold" style="font-size:14px;">Enable Shipping & Delivery Info</div>
                                    <small class="text-muted">If checked, shipping charge and delivery area info will be visible on the product page.</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="col-12">
                <div class="form-card">
                    <div class="form-title">Images</div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Thumbnail Image</label>
                            <div class="img-upload-box">
                                <i class="bi bi-image fs-2 text-muted"></i>
                                <p class="mb-0 text-muted small">Drop main image here</p>
                                <input type="file" name="thumbnail" onchange="previewImage(this, 'thumb_preview')" required>
                                <img id="thumb_preview" class="preview-img d-none">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Additional Images</label>
                            <div class="img-upload-box">
                                <i class="bi bi-images fs-2 text-muted"></i>
                                <p class="mb-0 text-muted small">Drop more images</p>
                                <input type="file" name="additional_thumbnails[]" multiple onchange="previewMultiple(this, 'multi_preview')">
                                <div id="multi_preview" class="d-flex flex-wrap gap-2 mt-2 justify-content-center"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Video --}}
            <div class="col-12">
                <div class="form-card">
                    <div class="form-title" data-bs-toggle="collapse" data-bs-target="#video_section" style="cursor:pointer;">
                        <i class="bi bi-play-btn-fill me-2"></i> Upload or Add Product Video
                    </div>
                    <div id="video_section" class="collapse">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Select Video Type</label>
                                <select name="video_type" id="video_type" class="form-select" onchange="toggleVideoInput()">
                                    <option value="upload">Upload Video File</option>
                                    <option value="youtube">YouTube Video Link</option>
                                    <option value="vimeo">Vimeo Video Link</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3" id="video_file_wrap">
                                <label class="form-label">Upload Product Video</label>
                                <input type="file" name="video_file" class="form-control">
                            </div>
                            <div class="col-md-8 mb-3 d-none" id="video_link_wrap">
                                <label class="form-label">Video Link</label>
                                <input type="text" name="video_link" class="form-control" placeholder="https://youtube.com/...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO Information --}}
            <div class="col-12">
                <div class="form-card">
                    <div class="form-title" data-bs-toggle="collapse" data-bs-target="#seo_section" style="cursor:pointer;">
                        <i class="bi bi-search me-2"></i> SEO Information
                    </div>
                    <div id="seo_section" class="collapse">
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" placeholder="Meta Title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3" placeholder="Meta description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control" placeholder="Write keywords and Press enter to add new one">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="reset" class="btn-cancel">Reset</button>
            <button type="submit" class="btn-submit">Submit</button>
        </div>
    </form>
</div>

{{-- Summernote & JS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        $('#editor').summernote({
            placeholder: 'Write product description here...',
            tabsize: 2,
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });

    function toggleVideoInput() {
        const type = document.getElementById('video_type').value;
        const fileWrap = document.getElementById('video_file_wrap');
        const linkWrap = document.getElementById('video_link_wrap');
        
        if(type === 'upload') {
            fileWrap.classList.remove('d-none');
            linkWrap.classList.add('d-none');
        } else {
            fileWrap.classList.add('d-none');
            linkWrap.classList.remove('d-none');
        }
    }

    function generateSKU() {
        const sku = 'DP-' + Math.random().toString(36).substr(2, 8).toUpperCase();
        document.getElementById('sku_input').value = sku;
    }

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewMultiple(input, containerId) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-img';
                    img.style.width = '60px';
                    img.style.height = '60px';
                    container.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // --- Screenshot-matched License Key Logic ---
    function addLicenseField() {
        const container = document.getElementById('license_keys_container');
        const div = document.createElement('div');
        div.className = 'd-flex gap-2 mb-2 align-items-center';
        div.innerHTML = `
            <input type="text" name="license_keys[]" class="form-control" placeholder="License Key">
            <button type="button" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap; padding: 7px 15px;" onclick="generateToField(this)">Generate key</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap; padding: 7px 15px;" onclick="copyField(this)">Copy key</button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" style="padding: 7px 15px;" onclick="removeField(this)">X</button>
        `;
        container.appendChild(div);
    }

    function generateToField(btn) {
        const part = () => Math.random().toString(36).substr(2, 4).toUpperCase();
        const key = `${part()}-${part()}-${part()}-${part()}`;
        btn.parentElement.querySelector('input').value = key;
    }

    function copyField(btn) {
        const input = btn.parentElement.querySelector('input');
        if(!input.value) return;
        input.select();
        document.execCommand('copy');
    }

    function removeField(btn) {
        const container = document.getElementById('license_keys_container');
        if(container.children.length > 1) {
            btn.parentElement.remove();
        } else {
            btn.parentElement.querySelector('input').value = '';
        }
    }

    function bulkGenerate(num) {
        for(let i=0; i<num; i++) {
            addLicenseField();
            const last = document.getElementById('license_keys_container').lastElementChild;
            generateToField(last.querySelector('button'));
        }
    }
</script>
@endsection
