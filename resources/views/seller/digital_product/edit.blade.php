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
    .form-title { font-size:16px; font-weight:700; color:var(--dark); margin-bottom:20px; border-bottom: 1px solid var(--border); padding-bottom: 12px; }
    .form-label { font-size:13px; font-weight:600; color:var(--dark); margin-bottom:6px; }
    .form-control, .form-select { border-radius:var(--r-sm); border:1px solid var(--border); padding:10px 14px; font-size:14px; }
    .btn-submit { background:var(--brand); color:#fff; border:none; border-radius:var(--r-md); padding:10px 24px; font-weight:600; }
    .img-upload-box { border: 2px dashed #d1d5db; border-radius: var(--r-md); padding: 20px; text-align: center; background: #fafafa; position: relative; cursor: pointer; }
    .img-upload-box input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .preview-img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-top: 10px; border: 1px solid var(--border); }
</style>

<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">Edit Digital Product</h4>
        <a href="{{ route('seller.digital_product.index') }}" class="btn btn-sm btn-outline-secondary px-3">Back</a>
    </div>

    <form action="{{ route('seller.digital_product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-12">
                <div class="form-card">
                    <div class="form-title">Product Info</div>
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2">{{ $product->short_description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="editor" class="form-control">{{ $product->description }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="form-card">
                    <div class="form-title">General Info</div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Thumbnail Image</label>
                            <div class="img-upload-box">
                                <i class="bi bi-image fs-2 text-muted"></i>
                                <p class="mb-0 text-muted small">Update main image</p>
                                <input type="file" name="thumbnail" onchange="previewImage(this, 'thumb_preview')">
                                <img id="thumb_preview" src="{{ asset($product->thumbnail) }}" class="preview-img">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Additional Images</label>
                            <div class="img-upload-box">
                                <i class="bi bi-images fs-2 text-muted"></i>
                                <p class="mb-0 text-muted small">Update additional images</p>
                                <input type="file" name="additional_thumbnails[]" multiple onchange="previewMultiple(this, 'multi_preview')">
                                <div id="multi_preview" class="d-flex flex-wrap gap-2 mt-2 justify-content-center">
                                    @if($product->additional_thumbnails)
                                        @foreach($product->additional_thumbnails as $img)
                                            <img src="{{ asset($img) }}" class="preview-img" style="width:60px; height:60px;">
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="form-card">
                    <div class="form-title">Digital Attachments</div>
                    <div class="mb-4">
                        <label class="form-label">Update Digital File (Leave empty to keep current)</label>
                        <div class="img-upload-box">
                            <i class="bi bi-cloud-arrow-up fs-3 text-muted"></i>
                            <p class="mb-0 small text-muted">{{ $product->digital_file ? basename($product->digital_file) : 'Upload new file' }}</p>
                            <input type="file" name="digital_file">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Shipping Options</label>
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_shipping_charge" id="is_shipping_charge" value="1" {{ old('is_shipping_charge', $product->is_shipping_charge) ? 'checked' : '' }} style="width: 35px; height: 18px; cursor: pointer;">
                                <label class="form-check-label fw-bold ms-2" for="is_shipping_charge" style="cursor: pointer;">Enable Shipping & Delivery Info</label>
                                <p class="text-muted small mb-0 mt-1">If checked, shipping charge and delivery area info will be visible on the product page.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">License Keys</label>
                        <div id="license_keys_container">
                            @if($product->license_keys && count($product->license_keys) > 0)
                                @foreach($product->license_keys as $key)
                                    <div class="d-flex gap-2 mb-2 align-items-center">
                                        <input type="text" name="license_keys[]" class="form-control" value="{{ $key }}">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap; padding: 7px 15px;" onclick="generateToField(this)">Generate key</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap; padding: 7px 15px;" onclick="copyField(this)">Copy key</button>
                                        <button type="button" class="btn btn-outline-danger btn-sm px-3" style="padding: 7px 15px;" onclick="removeField(this)">X</button>
                                    </div>
                                @endforeach
                            @else
                                <div class="d-flex gap-2 mb-2 align-items-center">
                                    <input type="text" name="license_keys[]" class="form-control" placeholder="License Key">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap; padding: 7px 15px;" onclick="generateToField(this)">Generate key</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap; padding: 7px 15px;" onclick="copyField(this)">Copy key</button>
                                    <button type="button" class="btn btn-outline-danger btn-sm px-3" style="padding: 7px 15px;" onclick="removeField(this)">X</button>
                                </div>
                            @endif
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-sm px-3" style="background: #10b981; color:#fff;" onclick="addLicenseField()">+ Add License</button>
                            <button type="button" class="btn btn-sm px-3" style="background: #e8174a; color:#fff;" onclick="bulkGenerate(3)">Bulk Generate (3)</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 text-end">
                <button type="submit" class="btn-submit">Update Product</button>
            </div>
        </div>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() { $('#editor').summernote({ height: 200 }); });

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
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
