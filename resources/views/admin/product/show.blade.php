{{-- resources/views/admin/product/show.blade.php --}}
@extends('admin.master')
@section('content')
<style>
    :root {
        --brand: #e8174a; --brand-light: rgba(232,23,74,.08); --brand-hover: #c9113e;
        --dark: #1a1d23; --muted: #6b7280; --border: #e5e7eb; --surface: #f8f9fc;
        --shadow: 0 1px 4px rgba(0,0,0,.06), 0 2px 12px rgba(0,0,0,.04);
        --r-lg: 14px; --r-md: 10px; --r-sm: 7px; --ease: all .18s ease;
    }
    .ph { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
    .ph-title { font-size: 1.4rem; font-weight: 700; color: var(--dark); margin: 0; }
    .btn-back { display: inline-flex; align-items: center; gap: 6px; color: var(--muted); background: #fff; border: 1.5px solid var(--border); border-radius: var(--r-md); padding: 8px 16px; font-size: 13.5px; font-weight: 600; cursor: pointer; text-decoration: none; transition: var(--ease); }
    .btn-back:hover { background: var(--surface); color: var(--dark); }
    .btn-edit { display: inline-flex; align-items: center; gap: 7px; background: var(--brand); color: #fff; border: none; border-radius: var(--r-md); padding: 9px 20px; font-size: 13.5px; font-weight: 600; cursor: pointer; box-shadow: 0 2px 10px rgba(232,23,74,.25); text-decoration: none; transition: var(--ease); }
    .btn-edit:hover { background: var(--brand-hover); color: #fff; transform: translateY(-1px); }

    .show-card { background: #fff; border-radius: var(--r-lg); border: 1px solid var(--border); box-shadow: var(--shadow); padding: 24px; margin-bottom: 1.5rem; }
    
    .product-grid-layout { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
    @media (max-width: 991px) { .product-grid-layout { grid-template-columns: 1fr; } }
    
    .img-showcase { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-md); padding: 20px; text-align: center; }
    .main-img { max-width: 100%; max-height: 350px; object-fit: contain; }
    .gallery-list { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; justify-content: center; }
    .gallery-list img { width: 60px; height: 60px; object-fit: contain; border: 1px solid var(--border); border-radius: 6px; background: #fff; padding: 4px; cursor: pointer; }
    
    .detail-section { margin-bottom: 20px; }
    .detail-section h4 { font-size: 1.3rem; font-weight: 700; color: var(--dark); margin-bottom: 8px; }
    .detail-section .badge-cat { background: var(--surface); color: var(--brand); border: 1px solid rgba(232,23,74,.2); font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 20px; display: inline-block; margin-bottom: 10px; }
    .detail-section .price-block { display: flex; align-items: baseline; gap: 10px; margin: 15px 0; }
    .detail-section .price-sell { font-size: 1.6rem; font-weight: 700; color: var(--brand); }
    .detail-section .price-buy { font-size: 1.1rem; color: var(--muted); text-decoration: line-through; }
    
    .info-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .info-table th, .info-table td { padding: 12px 16px; border-bottom: 1px solid var(--border); font-size: 14px; }
    .info-table th { width: 200px; color: var(--muted); font-weight: 600; background: #fafafa; }
    .info-table td { color: var(--dark); }
    
    .desc-block { padding-top: 20px; border-top: 1px solid var(--border); margin-top: 20px; }
    .desc-block h5 { font-size: 1.1rem; font-weight: 700; margin-bottom: 10px; }
    .desc-block .content { color: #4b5563; line-height: 1.6; font-size: 14.5px; }

</style>

<div class="ph">
    <div style="display:flex;align-items:center;gap:12px;">
        <a href="{{ route('products.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Back</a>
        <h4 class="ph-title">Product Details</h4>
    </div>
    <a href="{{ route('products.edit', $product->id) }}" class="btn-edit">
        <i class="bi bi-pencil-square"></i> Edit Product
    </a>
</div>

<div class="show-card">
    <div class="product-grid-layout">
        <!-- Images -->
        <div>
            <div class="img-showcase">
                @if($product->thumbnail)
                    <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}" class="main-img" id="mainImageShow">
                @else
                    <div style="height:350px;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:50px;" id="mainImageShow">
                        <i class="bi bi-image"></i>
                    </div>
                @endif
            </div>
            
            @if($product->gallery_images && count($product->gallery_images))
            <div class="gallery-list">
                @if($product->thumbnail)
                    <img src="{{ asset($product->thumbnail) }}" alt="Thumbnail" onclick="changeMainImage('{{ asset($product->thumbnail) }}')">
                @endif
                @foreach($product->gallery_images as $gi)
                    <img src="{{ asset($gi) }}" alt="Gallery Image" onclick="changeMainImage('{{ asset($gi) }}')">
                @endforeach
            </div>
            @endif
        </div>
        
        <!-- Details -->
        <div>
            <div class="detail-section">
                @if($product->category)
                    <span class="badge-cat">{{ $product->category->name }}</span>
                @endif
                <h4>{{ $product->name }}</h4>
                <p style="color:var(--muted); font-size:14px; margin-bottom:5px;">SKU: {{ $product->sku }}</p>
                @if($product->barcode)
                    <p style="color:var(--muted); font-size:14px; margin-bottom:5px;">Barcode: {{ $product->barcode }}</p>
                @endif
                
                <div class="price-block">
                    <span class="price-sell">৳{{ number_format($product->selling_price, 0) }}</span>
                    @if($product->discount_price > 0)
                        <span class="price-buy">৳{{ number_format($product->buying_price, 0) }}</span>
                    @endif
                </div>
            </div>
            
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Stock Quantity</th>
                        <td>{{ $product->stock_quantity >= 999999 ? 'Unlimited' : $product->stock_quantity }}</td>
                    </tr>
                    @if($product->brand)
                    <tr>
                        <th>Brand</th>
                        <td>{{ $product->brand->name }}</td>
                    </tr>
                    @endif
                    @if($product->color)
                    <tr>
                        <th>Color(s)</th>
                        <td>{{ $product->color }}</td>
                    </tr>
                    @endif
                    @if($product->size)
                    <tr>
                        <th>Size(s)</th>
                        <td>{{ $product->size }}</td>
                    </tr>
                    @endif
                    @if($product->unit)
                    <tr>
                        <th>Unit</th>
                        <td>{{ $product->unit }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Features</th>
                        <td>
                            @if($product->is_new_arrival) <span class="badge bg-primary" style="margin-right:5px;">New Arrival</span> @endif
                            @if($product->is_best_seller) <span class="badge bg-success" style="margin-right:5px;">Best Seller</span> @endif
                            @if($product->is_hot_product) <span class="badge bg-danger" style="margin-right:5px;">Hot Product</span> @endif
                            @if($product->is_flash_sale) <span class="badge bg-warning text-dark" style="margin-right:5px;">Flash Sale</span> @endif
                            @if($product->is_just_for_you) <span class="badge bg-info text-dark" style="margin-right:5px;">Just For You</span> @endif
                            @if($product->is_popular) <span class="badge bg-secondary" style="margin-right:5px;">Popular</span> @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            
            @if($product->short_description)
            <div class="desc-block">
                <h5>Short Description</h5>
                <div class="content">{{ $product->short_description }}</div>
            </div>
            @endif
            
            @if($product->description)
            <div class="desc-block">
                <h5>Full Description</h5>
                <div class="content">{!! $product->description !!}</div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    let images = [];
    @if($product->thumbnail)
        images.push("{{ asset($product->thumbnail) }}");
    @endif
    @if($product->gallery_images && count($product->gallery_images))
        @foreach($product->gallery_images as $gi)
            images.push("{{ asset($gi) }}");
        @endforeach
    @endif

    let currentIndex = 0;
    let autoSlideInterval;

    function startAutoSlide() {
        if (images.length > 1) {
            autoSlideInterval = setInterval(() => {
                currentIndex = (currentIndex + 1) % images.length;
                let mainImg = document.getElementById('mainImageShow');
                if(mainImg) mainImg.src = images[currentIndex];
            }, 3000); // Slides every 3 seconds
        }
    }

    function changeMainImage(src) {
        let mainImg = document.getElementById('mainImageShow');
        if (mainImg) {
            mainImg.src = src;
            
            // Update currentIndex to the clicked image
            let index = images.indexOf(src);
            if (index !== -1) {
                currentIndex = index;
            }
            
            // Reset the auto-slide timer when manually clicked
            clearInterval(autoSlideInterval);
            startAutoSlide();
        }
    }

    // Initialize auto slider on page load
    document.addEventListener('DOMContentLoaded', startAutoSlide);
</script>
@endsection
