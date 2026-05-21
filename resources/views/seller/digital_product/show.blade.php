@extends('admin.master')
@section('content')
{{-- Swiper CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

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
    .detail-card { background:#fff; border-radius:var(--r-lg); box-shadow:0 1px 4px rgba(0,0,0,.06); border:1px solid var(--border); padding:30px; margin-bottom: 24px; }
    .swiper { width: 100%; border-radius: var(--r-md); border: 1px solid var(--border); background: var(--surface); margin-bottom: 15px; }
    .swiper-slide img { width: 100%; height: 400px; object-fit: contain; }
    .swiper-thumbs { height: 80px; padding: 10px 0; }
    .swiper-thumbs .swiper-slide { width: 25%; height: 100%; opacity: 0.4; cursor: pointer; border: 1px solid var(--border); border-radius: var(--r-sm); overflow: hidden; }
    .swiper-thumbs .swiper-slide-thumb-active { opacity: 1; border-color: var(--brand); }
    .swiper-thumbs .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }
    
    .badge-digital { background: #3b82f6; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; margin-bottom: 10px; display: inline-block; }
    .prod-name { font-size: 24px; font-weight: 700; color: var(--dark); margin-bottom: 10px; }
    .price-wrap { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
    .curr-price { font-size: 28px; font-weight: 800; color: var(--brand); }
    
    .attachment-box { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 20px; margin-top: 20px; }
    .license-list { list-style: none; padding: 0; }
    .license-item { background: #fff; border: 1px solid var(--border); padding: 8px 15px; border-radius: 6px; margin-bottom: 8px; font-family: monospace; display: flex; justify-content: space-between; align-items: center; }
</style>

<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">Digital Product Details</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('seller.digital_product.index') }}" class="btn btn-sm btn-outline-secondary px-3">Back</a>
            <a href="{{ route('seller.digital_product.edit', $product->id) }}" class="btn btn-sm btn-primary px-3" style="background:var(--brand); border:none;">Edit</a>
        </div>
    </div>

    <div class="detail-card">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="swiper mainSwiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide"><img src="{{ asset($product->thumbnail) }}" /></div>
                        @if($product->additional_thumbnails)
                            @foreach($product->additional_thumbnails as $img)
                                <div class="swiper-slide"><img src="{{ asset($img) }}" /></div>
                            @endforeach
                        @endif
                    </div>
                    <div class="swiper-button-next" style="color: var(--brand);"></div>
                    <div class="swiper-button-prev" style="color: var(--brand);"></div>
                </div>
                <div thumbsSlider="" class="swiper swiper-thumbs">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide"><img src="{{ asset($product->thumbnail) }}" /></div>
                        @if($product->additional_thumbnails)
                            @foreach($product->additional_thumbnails as $img)
                                <div class="swiper-slide"><img src="{{ asset($img) }}" /></div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <span class="badge-digital">Digital Product</span>
                <h1 class="prod-name">{{ $product->name }}</h1>
                <div class="text-muted small mb-3">Category: {{ $product->category->name ?? 'N/A' }} | SKU: {{ $product->sku }}</div>

                <div class="price-wrap">
                    <span class="curr-price">৳{{ number_format($product->selling_price, 0) }}</span>
                    @if($product->discount_price > 0)
                        <span class="text-muted text-decoration-line-through">৳{{ number_format($product->selling_price + $product->discount_price, 0) }}</span>
                    @endif
                </div>

                <div class="attachment-box">
                    <h6 class="fw-bold mb-3"><i class="bi bi-download me-2"></i> Downloadable Content</h6>
                    @if($product->digital_file)
                        <a href="{{ asset($product->digital_file) }}" class="btn btn-sm btn-primary" download><i class="bi bi-cloud-download me-1"></i> Download File</a>
                    @else
                        <span class="text-muted">No file attached</span>
                    @endif

                    @if($product->license_keys && count($product->license_keys) > 0)
                        <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-key me-2"></i> License Keys</h6>
                        <ul class="license-list">
                            @foreach($product->license_keys as $key)
                                <li class="license-item">
                                    <span>{{ $key }}</span>
                                    <button class="btn btn-sm btn-light" onclick="copyText('{{ $key }}')"><i class="bi bi-clipboard"></i></button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-5 pt-5 border-top">
            <h5 class="fw-bold mb-3">Description</h5>
            <div class="text-muted">{!! $product->description !!}</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".swiper-thumbs", { spaceBetween: 10, slidesPerView: 4, freeMode: true, watchSlidesProgress: true });
    var swiper2 = new Swiper(".mainSwiper", { spaceBetween: 10, effect: "fade", loop: true, autoplay: { delay: 3000 }, navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" }, thumbs: { swiper: swiper } });
    function copyText(text) { navigator.clipboard.writeText(text); alert('Copied: ' + text); }
</script>
@endsection
