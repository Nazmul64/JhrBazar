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
        
        /* Swiper Custom Styles */
        .swiper { width: 100%; border-radius: var(--r-md); border: 1px solid var(--border); overflow: hidden; background: var(--surface); margin-bottom: 15px; }
        .swiper-slide img { width: 100%; height: 400px; object-fit: contain; background: var(--surface); }
        
        .swiper-thumbs { height: 80px; box-sizing: border-box; padding: 10px 0; }
        .swiper-thumbs .swiper-slide { width: 25%; height: 100%; opacity: 0.4; cursor: pointer; border: 1px solid var(--border); border-radius: var(--r-sm); overflow: hidden; }
        .swiper-thumbs .swiper-slide-thumb-active { opacity: 1; border-color: var(--brand); }
        .swiper-thumbs .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }

        .badge-seller { background: var(--brand); color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; margin-bottom: 10px; display: inline-block; }
        .prod-name { font-size: 24px; font-weight: 700; color: var(--dark); margin-bottom: 10px; line-height: 1.3; }
        .prod-meta { color: var(--muted); font-size: 13px; margin-bottom: 20px; }
        
        .price-wrap { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
        .curr-price { font-size: 28px; font-weight: 800; color: var(--brand); }
        .old-price { font-size: 18px; color: var(--muted); text-decoration: line-through; }
        .save-perc { background: #fff1f2; color: var(--brand); font-weight: 700; font-size: 12px; padding: 2px 8px; border-radius: 4px; }

        .variant-label { font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 8px; display: block; }
        .variant-opt { display: inline-block; padding: 6px 15px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; color: var(--muted); margin-bottom: 20px; background: #fff; }
        
        .stock-info { font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 25px; }
        
        .btn-view-live { background: #fff; border: 1px solid var(--brand); color: var(--brand); padding: 8px 20px; border-radius: 6px; font-weight: 600; font-size: 14px; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-view-live:hover { background: var(--brand); color: #fff; }

        .desc-title { font-size: 16px; font-weight: 700; color: var(--dark); margin-bottom: 15px; border-bottom: 2px solid var(--brand); display: inline-block; padding-bottom: 5px; }
        .desc-content { font-size: 14px; color: #4b5563; line-height: 1.6; }
    </style>

    <div class="container-fluid px-4 py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 fw-bold" style="color:var(--dark);">Product Details</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('seller.product.index') }}" class="btn btn-sm btn-outline-secondary px-3"><i class="bi bi-arrow-left me-1"></i> Back to List</a>
                <a href="{{ route('seller.product.edit', $product->id) }}" class="btn btn-sm btn-primary px-3" style="background:var(--brand); border:none;"><i class="bi bi-pencil-square me-1"></i> Edit Product</a>
            </div>
        </div>

        <div class="detail-card">
            <div class="row g-5">
                {{-- Left: Images Slider --}}
                <div class="col-lg-6">
                    <!-- Main Slider -->
                    <div class="swiper mainSwiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="{{ asset($product->thumbnail) }}" />
                            </div>
                            @if($product->gallery_images)
                                @foreach($product->gallery_images as $img)
                                    <div class="swiper-slide">
                                        <img src="{{ asset($img) }}" />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="swiper-button-next" style="color: var(--brand);"></div>
                        <div class="swiper-button-prev" style="color: var(--brand);"></div>
                    </div>

                    <!-- Thumbnails -->
                    <div thumbsSlider="" class="swiper swiper-thumbs">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="{{ asset($product->thumbnail) }}" />
                            </div>
                            @if($product->gallery_images)
                                @foreach($product->gallery_images as $img)
                                    <div class="swiper-slide">
                                        <img src="{{ asset($img) }}" />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right: Info --}}
                <div class="col-lg-6">
                    <span class="badge-seller">Seller ID: #{{ Auth::id() }}</span>
                    <h1 class="prod-name">{{ $product->name }}</h1>
                    <div class="prod-meta">
                        <span class="me-3"><i class="bi bi-grid-fill me-1"></i> {{ $product->category->name ?? 'Uncategorized' }}</span>
                        <span><i class="bi bi-upc-scan me-1"></i> SKU: {{ $product->sku }}</span>
                    </div>

                    <div class="price-wrap">
                        <span class="curr-price">${{ number_format($product->selling_price, 2) }}</span>
                        @if($product->discount_price > 0)
                            <span class="old-price">${{ number_format($product->discount_price + $product->selling_price, 2) }}</span>
                            <span class="save-perc">Save {{ round(($product->discount_price / ($product->discount_price + $product->selling_price)) * 100) }}%</span>
                        @endif
                    </div>

                    @if($product->size)
                    <div class="mb-4">
                        <span class="variant-label">Size</span>
                        <span class="variant-opt">{{ $product->size }}</span>
                    </div>
                    @endif

                    @if($product->color)
                    <div class="mb-4">
                        <span class="variant-label">Color</span>
                        <span class="variant-opt">{{ $product->color }}</span>
                    </div>
                    @endif

                    <div class="stock-info">
                        <span class="text-muted">Quantity:</span> {{ $product->stock_quantity }}
                    </div>

                    <div class="mb-4">
                        <a href="#" class="btn-view-live"><i class="bi bi-globe"></i> View Live</a>
                    </div>
                </div>
            </div>

            <div class="mt-5 border-top pt-5">
                <h3 class="desc-title">Description</h3>
                <div class="desc-content">
                    {!! $product->description !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Swiper JS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".swiper-thumbs", {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });
        var swiper2 = new Swiper(".mainSwiper", {
            spaceBetween: 10,
            effect: "fade",
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
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
