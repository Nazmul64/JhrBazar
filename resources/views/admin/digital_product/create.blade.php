@extends('admin.master')

@section('content')
<style>
    .card { border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 25px; }
    .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-weight: 700; color: #334155; margin-bottom: 0; font-size: 15px; }
    .form-label { font-weight: 600; color: #475569; font-size: 13px; margin-bottom: 8px; }
    .form-control, .form-select { border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 15px; font-size: 14px; }
    .btn-submit { background: #f43f5e; color: #fff; font-weight: 600; padding: 12px 30px; border-radius: 8px; border: none; }
    .dropzone-box { border: 2px dashed #e2e8f0; border-radius: 12px; padding: 25px; text-align: center; cursor: pointer; background: #f8fafc; position: relative; }
    .license-row { background: #f8fafc; padding: 12px; border-radius: 10px; margin-bottom: 10px; border: 1px solid #f1f5f9; position: relative; }
    .preview-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
    .preview-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
    .toggle-icon { transition: transform 0.3s; }
    .card-header.collapsed .toggle-icon { transform: rotate(-90deg); }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Add New Digital Product</h4>
        <a href="{{ route('admin.digital_product.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.digital_product.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                {{-- Product Info --}}
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Product Info</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter Product Name" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" class="form-control" rows="2" placeholder="Enter short description"></textarea>
                        </div>
                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Description</label>
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" style="font-size: 11px;">
                                    <i class="bi bi-cpu me-1"></i> Generate Via AI
                                </button>
                            </div>
                            <textarea name="description" class="form-control" id="editor" rows="6"></textarea>
                        </div>
                    </div>
                </div>

                {{-- General Information --}}
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">General Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category_id" id="categoryId" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sub Category</label>
                                <select name="sub_category_id" id="subCategoryId" class="form-select">
                                    <option value="">Select Sub Category</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" class="form-select">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Product SKU <span class="text-danger">*</span></label>
                                    <a href="javascript:void(0)" id="generateSku" class="text-danger fw-bold small text-decoration-none">Generate Code</a>
                                </div>
                                <input type="text" name="sku" id="sku" class="form-control" placeholder="Ex: 134543" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Attachments & Licenses --}}
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Digital Product Attachments (Downloadable)</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">Digital Additional Documents</label>
                            <div class="dropzone-box" onclick="document.getElementById('digital_file').click()">
                                <input type="file" name="digital_file" id="digital_file" class="d-none">
                                <i class="bi bi-file-earmark-zip display-6 text-muted"></i>
                                <p class="text-muted small mt-2 mb-0" id="fileNameDisplay">Upload Zip/RAR or PDF file (Max 50MB)</p>
                            </div>
                        </div>
                        
                        <label class="form-label">License Keys</label>
                        <div id="licenseContainer">
                            <div class="license-row">
                                <div class="input-group">
                                    <input type="text" name="license_keys[]" class="form-control license-input" placeholder="License Key">
                                    <button type="button" class="btn btn-outline-secondary gen-key-btn" title="Generate Key"><i class="bi bi-magic"></i></button>
                                    <button type="button" class="btn btn-outline-primary copy-key-btn" title="Copy Key"><i class="bi bi-clipboard"></i></button>
                                    <button type="button" class="btn btn-outline-danger remove-license"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-success btn-sm px-3" id="addLicense">
                                <i class="bi bi-plus-lg me-1"></i> Add License
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="bulkGenerate">
                                Bulk Generate (5)
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Price Information --}}
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Price Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label">Buying Price <span class="text-danger">*</span></label>
                                <input type="number" name="buying_price" class="form-control" placeholder="0.00" step="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" name="selling_price" class="form-control" placeholder="0.00" step="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Discount Price</label>
                                <input type="number" name="discount_price" class="form-control" placeholder="0.00" value="0" step="0.01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" name="stock_quantity" class="form-control" value="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Images --}}
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Images</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">Thumbnail (1:1) <span class="text-danger">*</span></label>
                            <div class="dropzone-box" onclick="document.getElementById('thumbnail').click()">
                                <input type="file" name="thumbnail" id="thumbnail" class="d-none" accept="image/*" required>
                                <div id="thumbPlaceholder">
                                    <i class="bi bi-image display-6 text-muted"></i>
                                    <p class="text-muted small mt-2 mb-0">Click to Upload</p>
                                </div>
                                <img id="thumbImg" class="img-fluid d-none rounded shadow-sm">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Additional Thumbnails</label>
                            <div class="dropzone-box" onclick="document.getElementById('additional_thumbnails').click()">
                                <input type="file" name="additional_thumbnails[]" id="additional_thumbnails" class="d-none" multiple accept="image/*">
                                <i class="bi bi-images display-6 text-muted"></i>
                                <p class="text-muted small mt-2 mb-0">Click to Add More</p>
                            </div>
                            <div id="additionalPreview" class="preview-container"></div>
                        </div>
                    </div>
                </div>

                {{-- Video --}}
                <div class="card">
                    <div class="card-header accordion-header collapsed" onclick="toggleSection('videoBody', this)">
                        <h6 class="card-title">Product Video</h6>
                        <i class="bi bi-chevron-down toggle-icon"></i>
                    </div>
                    <div class="card-body d-none" id="videoBody">
                        <div class="mb-3">
                            <label class="form-label">Video Type</label>
                            <select name="video_type" id="videoType" class="form-select">
                                <option value="upload">Upload Video</option>
                                <option value="youtube">YouTube</option>
                                <option value="vimeo">Vimeo</option>
                            </select>
                        </div>
                        <div id="videoUploadBox">
                            <input type="file" name="video_file" class="form-control" accept="video/*">
                        </div>
                        <div id="videoLinkBox" class="d-none">
                            <input type="url" name="video_link" class="form-control" placeholder="Video URL">
                        </div>
                    </div>
                </div>

                {{-- SEO --}}
                <div class="card">
                    <div class="card-header accordion-header collapsed" onclick="toggleSection('seoBody', this)">
                        <h6 class="card-title">SEO Information</h6>
                        <i class="bi bi-chevron-down toggle-icon"></i>
                    </div>
                    <div class="card-body d-none" id="seoBody">
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" placeholder="Meta Title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3" placeholder="Meta Description"></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control" placeholder="Keyword 1, Keyword 2">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit w-100 fw-bold shadow">Save Product</button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleSection(id, header) {
    const body = document.getElementById(id);
    body.classList.toggle('d-none');
    header.classList.toggle('collapsed');
}

document.addEventListener('DOMContentLoaded', function() {
    // Subcategory Loading
    const categoryId = document.getElementById('categoryId');
    const subCategoryId = document.getElementById('subCategoryId');
    
    categoryId.addEventListener('change', function() {
        const id = this.value;
        subCategoryId.innerHTML = '<option value="">Select Sub Category</option>';
        if (id) {
            fetch(`/admin/admin-digital-products/subcategories/${id}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(sub => {
                        subCategoryId.innerHTML += `<option value="${sub.id}">${sub.name}</option>`;
                    });
                });
        }
    });

    // SKU Generation
    document.getElementById('generateSku').addEventListener('click', function() {
        const sku = 'DP-' + Math.random().toString(36).substr(2, 8).toUpperCase();
        document.getElementById('sku').value = sku;
    });

    // License Management
    function generateKey() {
        return 'KEY-' + Math.random().toString(36).substr(2, 5).toUpperCase() + '-' + Math.random().toString(36).substr(2, 5).toUpperCase();
    }

    document.getElementById('addLicense').addEventListener('click', function() {
        const container = document.getElementById('licenseContainer');
        const row = document.createElement('div');
        row.className = 'license-row';
        row.innerHTML = `
            <div class="input-group">
                <input type="text" name="license_keys[]" class="form-control license-input" placeholder="License Key">
                <button type="button" class="btn btn-outline-secondary gen-key-btn"><i class="bi bi-magic"></i></button>
                <button type="button" class="btn btn-outline-primary copy-key-btn"><i class="bi bi-clipboard"></i></button>
                <button type="button" class="btn btn-outline-danger remove-license"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
    });

    document.getElementById('bulkGenerate').addEventListener('click', function() {
        const container = document.getElementById('licenseContainer');
        for(let i=0; i<5; i++) {
            const row = document.createElement('div');
            row.className = 'license-row';
            row.innerHTML = `
                <div class="input-group">
                    <input type="text" name="license_keys[]" class="form-control license-input" value="${generateKey()}">
                    <button type="button" class="btn btn-outline-secondary gen-key-btn"><i class="bi bi-magic"></i></button>
                    <button type="button" class="btn btn-outline-primary copy-key-btn"><i class="bi bi-clipboard"></i></button>
                    <button type="button" class="btn btn-outline-danger remove-license"><i class="bi bi-trash"></i></button>
                </div>
            `;
            container.appendChild(row);
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-license')) {
            e.target.closest('.license-row').remove();
        }
        if (e.target.closest('.gen-key-btn')) {
            const input = e.target.closest('.license-row').querySelector('.license-input');
            input.value = generateKey();
        }
        if (e.target.closest('.copy-key-btn')) {
            const input = e.target.closest('.license-row').querySelector('.license-input');
            if(input.value) {
                navigator.clipboard.writeText(input.value);
                alert('Copied to clipboard!');
            }
        }
    });

    // Image Previews
    document.getElementById('thumbnail').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('thumbImg').src = e.target.result;
                document.getElementById('thumbImg').classList.remove('d-none');
                document.getElementById('thumbPlaceholder').classList.add('d-none');
            }
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('additional_thumbnails').addEventListener('change', function() {
        const container = document.getElementById('additionalPreview');
        container.innerHTML = '';
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-img shadow-sm';
                container.appendChild(img);
            }
            reader.readAsDataURL(file);
        });
    });

    // Video Toggle
    document.getElementById('videoType').addEventListener('change', function() {
        const val = this.value;
        if (val === 'upload') {
            document.getElementById('videoUploadBox').classList.remove('d-none');
            document.getElementById('videoLinkBox').classList.add('d-none');
        } else {
            document.getElementById('videoUploadBox').classList.add('d-none');
            document.getElementById('videoLinkBox').classList.remove('d-none');
        }
    });
});
</script>
@endsection
