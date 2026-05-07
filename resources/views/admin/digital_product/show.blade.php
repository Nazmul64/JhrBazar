@extends('admin.master')

@section('content')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
    .card { border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 25px; overflow: hidden; }
    .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; padding: 20px; }
    .card-title { font-weight: 700; color: #334155; margin-bottom: 0; }
    .label-muted { color: #94a3b8; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 5px; }
    .value-dark { color: #1e293b; font-weight: 700; font-size: 15px; }
    .badge-soft { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 12px; }
    .badge-soft-success { background: #dcfce7; color: #15803d; }
    .badge-soft-danger { background: #fee2e2; color: #b91c1c; }

    /* Slider Styling */
    .swiper { width: 100%; height: 100%; border-radius: 12px; }
    .swiper-slide { text-align: center; background: #fff; display: flex; justify-content: center; align-items: center; }
    .swiper-slide img { display: block; width: 100%; height: 400px; object-fit: contain; border-radius: 12px; background: #f8fafc; }
    
    .mySwiper2 { height: 400px; width: 100%; margin-bottom: 15px; border: 1px solid #f1f5f9; }
    .mySwiper { height: 80px; box-sizing: border-box; padding: 10px 0; }
    .mySwiper .swiper-slide { width: 25%; height: 100%; opacity: 0.4; cursor: pointer; transition: 0.3s; }
    .mySwiper .swiper-slide-thumb-active { opacity: 1; border: 2px solid #f43f5e; border-radius: 8px; }
    .mySwiper .swiper-slide img { height: 60px; object-fit: cover; border-radius: 6px; }

    .slider-container { padding: 10px; background: #fff; border-radius: 15px; }
    .license-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 10px; transition: 0.3s; }
    .license-box:hover { border-color: #f43f5e; box-shadow: 0 5px 15px rgba(244, 63, 94, 0.1); }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Product Details</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.digital_product.index') }}">Digital Products</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.digital_product.edit', $product->id) }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-pencil-square me-2"></i> Edit Product
            </a>
            <a href="{{ route('admin.digital_product.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card p-2">
                <div class="slider-container">
                    <!-- Main Slider -->
                    <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff" class="swiper mySwiper2">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="{{ asset($product->thumbnail) }}" />
                            </div>
                            @if($product->additional_thumbnails)
                                @foreach($product->additional_thumbnails as $thumb)
                                    <div class="swiper-slide">
                                        <img src="{{ asset($thumb) }}" />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>

                    <!-- Thumbnails Slider -->
                    <div thumbsSlider="" class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="{{ asset($product->thumbnail) }}" />
                            </div>
                            @if($product->additional_thumbnails)
                                @foreach($product->additional_thumbnails as $thumb)
                                    <div class="swiper-slide">
                                        <img src="{{ asset($thumb) }}" />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h6 class="card-title">Quick Status</h6></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="label-muted">Publish Status</span>
                        <span class="badge-soft {{ $product->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                            {{ $product->is_active ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="label-muted">Stock Quantity</span>
                        <span class="value-dark">{{ $product->stock_quantity }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h6 class="card-title">General Information</h6></div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="label-muted">Product Name</div>
                            <div class="value-dark">{{ $product->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="label-muted">Product SKU</div>
                            <div class="value-dark">{{ $product->sku }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="label-muted">Category</div>
                            <div class="value-dark">{{ $product->category->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="label-muted">Sub Category</div>
                            <div class="value-dark">{{ $product->subcategory->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="label-muted">Brand</div>
                            <div class="value-dark">{{ $product->brand->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <hr class="my-4 opacity-50">
                    <div class="mb-4">
                        <div class="label-muted">Short Description</div>
                        <p class="text-muted small">{{ $product->short_description ?? 'No short description provided.' }}</p>
                    </div>
                    <div>
                        <div class="label-muted">Long Description</div>
                        <div class="text-muted small">{!! $product->description ?? 'No detailed description available.' !!}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h6 class="card-title">Pricing & Financials</h6></div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="label-muted">Buying Price</div>
                            <div class="value-dark">${{ number_format($product->buying_price, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="label-muted">Selling Price</div>
                            <div class="value-dark text-success">${{ number_format($product->selling_price, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="label-muted">Discount</div>
                            <div class="value-dark text-danger">${{ number_format($product->discount_price, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($product->license_keys)
            <div class="card">
                <div class="card-header"><h6 class="card-title">License Keys</h6></div>
                <div class="card-body">
                    @foreach($product->license_keys as $key)
                    <div class="license-box d-flex justify-content-between align-items-center">
                        <code class="fw-bold text-primary" style="font-size: 14px;">{{ $key }}</code>
                        <button class="btn btn-sm btn-light border rounded-pill px-3" onclick="navigator.clipboard.writeText('{{ $key }}'); alert('Copied!')">
                            <i class="bi bi-clipboard text-primary"></i> Copy
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    var swiper = new Swiper(".mySwiper", {
        loop: true,
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
    });
    var swiper2 = new Swiper(".mySwiper2", {
        loop: true,
        spaceBetween: 10,
        effect: "fade", /* Animated Fade Effect */
        fadeEffect: {
            crossFade: true
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        thumbs: {
            swiper: swiper,
        },
    });
</script>
@endsection
