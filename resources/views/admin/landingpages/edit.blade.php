@extends('admin.master')

@section('content')

@php
    $settings = \App\Models\GenaralSetting::first();
@endphp

<style>
:root{--accent:#e7567c;--accent-dk:#c93f65;--blue:#4361ee;--green:#22c55e;--text:#1a1f36;--muted:#6b7a99;--border:#e4e9f2;--bg:#f0f2f5;--white:#ffffff;--radius:8px;--radius-sm:5px;--shadow:0 1px 4px rgba(0,0,0,.07);}
*,*::before,*::after{box-sizing:border-box;}
.lp-page{padding:24px;background:var(--bg);min-height:100vh;font-family:'Segoe UI',system-ui,sans-serif;}

.lp-page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px;}
.lp-page-title{font-size:20px;font-weight:800;color:var(--text);margin:0;}
.btn-back{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:var(--white);border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-weight:600;color:var(--muted);text-decoration:none;transition:all .15s;}
.btn-back:hover{background:#f1f5f9;color:var(--text);text-decoration:none;}

.lp-form-grid{display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;}
.form-card{background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;margin-bottom:20px;}
.form-card-head{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;}
.form-card-head h4{font-size:14px;font-weight:700;color:var(--text);margin:0;}
.form-card-body{padding:22px 20px;}

.form-group{margin-bottom:18px;}
.form-group:last-child{margin-bottom:0;}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text);margin-bottom:7px;}
.form-label span{color:var(--accent);}
.form-control{width:100%;height:44px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 14px;font-size:13px;color:var(--text);background:#f9fafb;outline:none;font-family:inherit;transition:border-color .15s,box-shadow .15s;}
.form-control:focus{border-color:var(--accent);background:var(--white);box-shadow:0 0 0 3px rgba(231,86,124,.1);}
.form-control.is-invalid{border-color:#dc2626;}
.invalid-feedback{font-size:12px;color:#dc2626;margin-top:5px;}
select.form-control{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7a99' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px;cursor:pointer;}

.file-input{width:100%;height:44px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0;font-size:13px;color:var(--text);background:#f9fafb;outline:none;cursor:pointer;font-family:inherit;}
.file-input:focus{border-color:var(--accent);}
.img-preview-wrap{margin-top:10px;}
.img-preview{max-width:120px;max-height:120px;object-fit:cover;border-radius:8px;border:2px solid var(--border);}
.img-current-label{font-size:11px;color:var(--muted);margin-top:4px;}

.toggle-wrap{display:flex;align-items:center;gap:12px;}
.toggle-switch{position:relative;width:50px;height:26px;cursor:pointer;display:inline-block;}
.toggle-switch input{opacity:0;width:0;height:0;}
.toggle-slider{position:absolute;inset:0;background:#d1d5db;border-radius:26px;transition:background .2s;}
.toggle-slider::after{content:'';position:absolute;width:20px;height:20px;border-radius:50%;background:#fff;left:3px;top:3px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2);}
.toggle-switch input:checked+.toggle-slider{background:#22c55e;}
.toggle-switch input:checked+.toggle-slider::after{transform:translateX(24px);}
.toggle-label{font-size:13px;color:var(--muted);font-weight:500;}

.review-list{display:flex;flex-direction:column;gap:14px;}
.review-item{background:#f8fafc;border:1.5px solid var(--border);border-radius:var(--radius);padding:14px;position:relative;}
.review-item-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
.review-item-num{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.3px;}
.btn-remove-review{background:#fee2e2;color:#dc2626;border:none;border-radius:4px;padding:3px 10px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:4px;}
.btn-remove-review:hover{background:#fecaca;}
.btn-add-review{height:40px;padding:0 18px;background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0;border-radius:var(--radius-sm);font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:7px;transition:all .15s;margin-top:10px;}
.btn-add-review:hover{background:#dcfce7;}

.form-footer{padding:16px 20px;border-top:1px solid var(--border);display:flex;gap:12px;}
.btn-submit{height:44px;padding:0 28px;background:linear-gradient(135deg,#e7567c,#c93f65);color:#fff;border:none;border-radius:var(--radius-sm);font-size:14px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px;font-family:inherit;transition:opacity .15s;}
.btn-submit:hover{opacity:.9;}
.btn-cancel{height:44px;padding:0 22px;background:#f1f5f9;color:var(--muted);border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:7px;font-family:inherit;}
.btn-cancel:hover{background:#e2e8f0;color:var(--text);text-decoration:none;}

@media(max-width:1024px){.lp-form-grid{grid-template-columns:1fr;}}
</style>

<div class="lp-page">

    <div class="lp-page-header">
        <h2 class="lp-page-title">
            <i class="bi bi-pencil" style="color:var(--accent);margin-right:6px;"></i>
            Edit Landing Page
        </h2>
        <a href="{{ route('admin.landingpages.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.landingpages.update', $landingpage->id) }}"
          method="POST" enctype="multipart/form-data" id="lpForm">
        @csrf
        @method('PUT')

        <div class="lp-form-grid">

            {{-- ── LEFT ── --}}
            <div>

                <div class="form-card">
                    <div class="form-card-head">
                        <i class="bi bi-info-circle" style="color:var(--accent);"></i>
                        <h4>Basic Information</h4>
                    </div>
                    <div class="form-card-body">

                        {{-- Title --}}
                        <div class="form-group">
                            <label class="form-label">Landing Page Title <span>*</span></label>
                            <input type="text" name="title"
                                   class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                                   value="{{ old('title', $landingpage->title) }}"
                                   placeholder="Enter title">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Product + Media --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div class="form-group">
                                <label class="form-label">Products</label>
                                <select name="product_id" class="form-control">
                                    <option value="">Choose...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ old('product_id', $landingpage->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Media Type <span>*</span></label>
                                <select name="media_type" class="form-control">
                                    <option value="image" {{ old('media_type', $landingpage->media_type) === 'image' ? 'selected' : '' }}>Image</option>
                                    <option value="video" {{ old('media_type', $landingpage->media_type) === 'video' ? 'selected' : '' }}>Video</option>
                                </select>
                            </div>
                        </div>

                        {{-- Main Image --}}
                        <div class="form-group">
                            <label class="form-label">Image <span style="color:var(--muted);font-weight:400;">(leave empty to keep current)</span></label>
                            <input type="file" name="image" class="file-input" accept="image/*"
                                   onchange="previewImg(this,'mainPreview','mainPreviewWrap')">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="img-preview-wrap" id="mainPreviewWrap">
                                @if($landingpage->image && file_exists(public_path($landingpage->image)))
                                    <img id="mainPreview" src="{{ asset($landingpage->image) }}" alt="Current" class="img-preview">
                                    <div class="img-current-label">Current image</div>
                                @else
                                    <img id="mainPreview" src="#" alt="" class="img-preview" style="display:none;">
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Short Description --}}
                <div class="form-card">
                    <div class="form-card-head">
                        <i class="bi bi-text-paragraph" style="color:var(--accent);"></i>
                        <h4>Short Description</h4>
                    </div>
                    <div class="form-card-body">
                        <textarea name="short_description" id="shortDesc" class="form-control"
                                  style="height:120px;resize:vertical;padding:12px;">{{ old('short_description', $landingpage->short_description) }}</textarea>
                    </div>
                </div>

                {{-- Description --}}
                <div class="form-card">
                    <div class="form-card-head">
                        <i class="bi bi-file-text" style="color:var(--accent);"></i>
                        <h4>Description</h4>
                    </div>
                    <div class="form-card-body">
                        <textarea name="description" id="descEditor" class="form-control"
                                  style="height:160px;resize:vertical;padding:12px;">{{ old('description', $landingpage->description) }}</textarea>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT ── --}}
            <div>

                {{-- Reviews --}}
                <div class="form-card">
                    <div class="form-card-head">
                        <i class="bi bi-star" style="color:var(--warning);"></i>
                        <h4>Reviews</h4>
                    </div>
                    <div class="form-card-body">

                        <div class="review-list" id="reviewList">
                            @php $existingReviews = $landingpage->reviews ?? []; @endphp
                            @forelse($existingReviews as $ri => $rev)
                                <div class="review-item" id="review_{{ $ri }}">
                                    <div class="review-item-head">
                                        <span class="review-item-num">Review #{{ $ri + 1 }}</span>
                                        <button type="button" class="btn-remove-review" onclick="removeReview({{ $ri }})">
                                            <i class="bi bi-x"></i> Remove
                                        </button>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Review Text <span>*</span></label>
                                        <input type="text" name="reviews[{{ $ri }}][review]"
                                               class="form-control"
                                               value="{{ old("reviews.{$ri}.review", $rev['review'] ?? '') }}"
                                               placeholder="Customer review text...">
                                    </div>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label class="form-label">Review Image</label>
                                        <input type="file" name="reviews[{{ $ri }}][image]"
                                               class="file-input" accept="image/*"
                                               onchange="previewImg(this,'rPreview_{{ $ri }}','rPreviewWrap_{{ $ri }}')">
                                        <div class="img-preview-wrap" id="rPreviewWrap_{{ $ri }}">
                                            @if(!empty($rev['image']) && file_exists(public_path($rev['image'])))
                                                <img id="rPreview_{{ $ri }}" src="{{ asset($rev['image']) }}" alt="" class="img-preview">
                                                <div class="img-current-label">Current image</div>
                                            @else
                                                <img id="rPreview_{{ $ri }}" src="#" alt="" class="img-preview" style="display:none;">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                {{-- Default empty review row --}}
                                <div class="review-item" id="review_0">
                                    <div class="review-item-head">
                                        <span class="review-item-num">Review #1</span>
                                        <button type="button" class="btn-remove-review" onclick="removeReview(0)">
                                            <i class="bi bi-x"></i> Remove
                                        </button>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Review Text <span>*</span></label>
                                        <input type="text" name="reviews[0][review]"
                                               class="form-control" placeholder="Customer review text...">
                                    </div>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label class="form-label">Review Image</label>
                                        <input type="file" name="reviews[0][image]"
                                               class="file-input" accept="image/*"
                                               onchange="previewImg(this,'rPreview_0','rPreviewWrap_0')">
                                        <div class="img-preview-wrap" id="rPreviewWrap_0">
                                            <img id="rPreview_0" src="#" alt="" class="img-preview" style="display:none;">
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <button type="button" class="btn-add-review" onclick="addReview()">
                            <i class="bi bi-plus-circle"></i> Add Review
                        </button>

                    </div>
                </div>

                {{-- Status --}}
                <div class="form-card">
                    <div class="form-card-head">
                        <i class="bi bi-toggle-on" style="color:var(--green);"></i>
                        <h4>Status</h4>
                    </div>
                    <div class="form-card-body">
                        <div class="toggle-wrap">
                            <label class="toggle-switch">
                                <input type="checkbox" name="status" value="1"
                                       {{ old('status', $landingpage->status) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label">Active</span>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-check-circle"></i> Update
                        </button>
                        <a href="{{ route('admin.landingpages.index') }}" class="btn-cancel">
                            <i class="bi bi-x"></i> Cancel
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
'use strict';

var reviewCount = {{ count($existingReviews ?? []) ?: 1 }};

function previewImg(input, previewId, wrapId) {
    var prev = document.getElementById(previewId);
    if (input.files && input.files[0] && prev) {
        var reader = new FileReader();
        reader.onload = function(e) {
            prev.src = e.target.result;
            prev.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function addReview() {
    var idx  = reviewCount;
    var list = document.getElementById('reviewList');
    var div  = document.createElement('div');
    div.className = 'review-item';
    div.id = 'review_' + idx;
    div.innerHTML = `
        <div class="review-item-head">
            <span class="review-item-num">Review #${idx + 1}</span>
            <button type="button" class="btn-remove-review" onclick="removeReview(${idx})">
                <i class="bi bi-x"></i> Remove
            </button>
        </div>
        <div class="form-group">
            <label class="form-label">Review Text <span style="color:var(--accent)">*</span></label>
            <input type="text" name="reviews[${idx}][review]"
                   class="form-control" placeholder="Customer review text...">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Review Image</label>
            <input type="file" name="reviews[${idx}][image]"
                   class="file-input" accept="image/*"
                   onchange="previewImg(this,'rPreview_${idx}','rPreviewWrap_${idx}')">
            <div class="img-preview-wrap" id="rPreviewWrap_${idx}">
                <img id="rPreview_${idx}" src="#" alt="" class="img-preview" style="display:none;">
            </div>
        </div>`;
    list.appendChild(div);
    reviewCount++;
    renumberReviews();
}

function removeReview(idx) {
    var el = document.getElementById('review_' + idx);
    if (el) el.remove();
    renumberReviews();
}

function renumberReviews() {
    var items = document.querySelectorAll('#reviewList .review-item');
    items.forEach(function(item, i) {
        var numEl = item.querySelector('.review-item-num');
        if (numEl) numEl.textContent = 'Review #' + (i + 1);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn && $.fn.summernote) {
        $('#shortDesc, #descEditor').summernote({ height: 150 });
    }
});
</script>

@endsection
