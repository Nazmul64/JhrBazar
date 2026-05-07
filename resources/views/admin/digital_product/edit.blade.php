@extends('admin.master')

@section('content')
<style>
    .card { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: 0.3s; }
    .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; padding: 20px; }
    .card-title { font-weight: 700; color: #334155; margin-bottom: 0; }
    .form-label { font-weight: 600; color: #475569; margin-bottom: 8px; }
    .form-control, .form-select { border-radius: 10px; padding: 12px 15px; border: 1.5px solid #e2e8f0; transition: 0.3s; }
    .form-control:focus, .form-select:focus { border-color: #f43f5e; box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.1); }
    .btn-submit { background: linear-gradient(135deg, #f43f5e 0%, #fb7185 100%); border: none; padding: 12px 35px; border-radius: 12px; font-weight: 700; color: #fff; transition: 0.3s; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(244, 63, 94, 0.3); }
    
    .img-preview-container { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px; }
    .preview-box { position: relative; width: 100px; height: 100px; border-radius: 12px; overflow: hidden; border: 2px solid #e2e8f0; }
    .preview-box img { width: 100%; height: 100%; object-fit: cover; }
    
    .accordion-button:not(.collapsed) { background-color: #fff1f2; color: #f43f5e; box-shadow: none; }
    .accordion-button:focus { box-shadow: none; border-color: #f43f5e; }
    .accordion-item { border: 1.5px solid #e2e8f0; border-radius: 12px !important; overflow: hidden; margin-bottom: 15px; }

    .license-item { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 10px; position: relative; }
    .btn-remove-key { position: absolute; top: -10px; right: -10px; background: #f43f5e; color: #fff; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Digital Product</h4>
            <p class="text-muted small mb-0">Modify product information and license settings</p>
        </div>
        <a href="{{ route('admin.digital_product.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.digital_product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Info -->
                <div class="card mb-4">
                    <div class="card-header"><h5 class="card-title">General Information</h5></div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">Product Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required placeholder="Enter product name">
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Product SKU</label>
                                <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" placeholder="Auto-generated if empty">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" name="stock_quantity" class="form-control" value="{{ $product->stock_quantity }}" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" class="form-control" rows="3" placeholder="Brief summary...">{{ $product->short_description }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editor" class="form-control">{{ $product->description }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="card mb-4">
                    <div class="card-header"><h5 class="card-title">Product Media</h5></div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Main Thumbnail *</label>
                                <input type="file" name="thumbnail" class="form-control" onchange="previewImage(this, 'main_preview')">
                                <div id="main_preview" class="img-preview-container">
                                    <div class="preview-box">
                                        <img src="{{ asset($product->thumbnail) }}" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Additional Thumbnails</label>
                                <input type="file" name="additional_thumbnails[]" class="form-control" multiple onchange="previewImages(this, 'gallery_preview')">
                                <div id="gallery_preview" class="img-preview-container">
                                    @if($product->additional_thumbnails)
                                        @foreach($product->additional_thumbnails as $img)
                                            <div class="preview-box"><img src="{{ asset($img) }}" alt=""></div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Sections -->
                <div class="accordion mb-4" id="extraSections">
                    <!-- SEO -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSEO">
                                <i class="bi bi-search me-2"></i> SEO Information
                            </button>
                        </h2>
                        <div id="collapseSEO" class="accordion-collapse collapse" data-bs-parent="#extraSections">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <label class="form-label">Meta Title</label>
                                    <input type="text" name="meta_title" class="form-control" value="{{ $product->meta_title }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Meta Description</label>
                                    <textarea name="meta_description" class="form-control" rows="3">{{ $product->meta_description }}</textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Meta Keywords</label>
                                    <input type="text" name="meta_keywords" class="form-control" value="{{ $product->meta_keywords }}" placeholder="keyword1, keyword2...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Video -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVideo">
                                <i class="bi bi-play-circle me-2"></i> Product Video
                            </button>
                        </h2>
                        <div id="collapseVideo" class="accordion-collapse collapse" data-bs-parent="#extraSections">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <label class="form-label">Video Provider</label>
                                    <select name="video_type" class="form-select">
                                        <option value="youtube" {{ $product->video_type == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                        <option value="vimeo" {{ $product->video_type == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                                        <option value="upload" {{ $product->video_type == 'upload' ? 'selected' : '' }}>Local Upload</option>
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Video URL / File</label>
                                    <input type="text" name="video_link" class="form-control" value="{{ $product->video_type != 'upload' ? $product->video : '' }}" placeholder="Paste link here...">
                                    <input type="file" name="video_file" class="form-control mt-2" style="display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Category & Pricing -->
                <div class="card mb-4">
                    <div class="card-header"><h5 class="card-title">Category & Pricing</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Buying Price *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="buying_price" class="form-control" value="{{ $product->buying_price }}" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Selling Price *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ $product->selling_price }}" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Discount Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="discount_price" class="form-control" value="{{ $product->discount_price }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- License Keys -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">License Keys</h5>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addLicenseKey()">+ Add</button>
                    </div>
                    <div class="card-body">
                        <div id="license_container">
                            @if($product->license_keys)
                                @foreach($product->license_keys as $key)
                                    <div class="license-item">
                                        <input type="text" name="license_keys[]" class="form-control" value="{{ $key }}">
                                        <button type="button" class="btn-remove-key" onclick="this.parentElement.remove()">&times;</button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-submit w-100 mb-3">
                            <i class="bi bi-check-circle me-2"></i> Update Product
                        </button>
                        <a href="{{ route('admin.digital_product.index') }}" class="btn btn-light w-100 rounded-pill">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(input, target) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(target).innerHTML = `<div class="preview-box"><img src="${e.target.result}"></div>`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewImages(input, target) {
        const container = document.getElementById(target);
        container.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const box = document.createElement('div');
                    box.className = 'preview-box';
                    box.innerHTML = `<img src="${e.target.result}">`;
                    container.appendChild(box);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    function addLicenseKey() {
        const container = document.getElementById('license_container');
        const div = document.createElement('div');
        div.className = 'license-item';
        div.innerHTML = `
            <input type="text" name="license_keys[]" class="form-control" placeholder="Enter key...">
            <button type="button" class="btn-remove-key" onclick="this.parentElement.remove()">&times;</button>
        `;
        container.appendChild(div);
    }
</script>
@endsection
