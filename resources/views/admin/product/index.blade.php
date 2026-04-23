{{-- resources/views/admin/product/index.blade.php --}}
@extends('admin.master')
@section('content')
<style>
    :root{
        --brand:#e8174a;--brand-light:rgba(232,23,74,.08);--brand-hover:#c9113e;
        --dark:#1a1d23;--muted:#6b7280;--border:#e5e7eb;--surface:#f8f9fc;
        --shadow:0 1px 4px rgba(0,0,0,.06),0 2px 12px rgba(0,0,0,.04);
        --r-lg:14px;--r-md:10px;--r-sm:7px;--ease:all .18s ease;
    }
    .ph{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem}
    .ph-title{font-size:1.4rem;font-weight:700;color:var(--dark);margin:0}
    .btn-view-toggle{display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:var(--r-sm);border:1.5px solid var(--border);background:#fff;cursor:pointer;color:var(--muted);transition:var(--ease);font-size:16px}
    .btn-view-toggle.active,.btn-view-toggle:hover{background:var(--brand);border-color:var(--brand);color:#fff}
    .btn-add{display:inline-flex;align-items:center;gap:7px;background:var(--brand);color:#fff;border:none;border-radius:var(--r-md);padding:9px 20px;font-size:13.5px;font-weight:600;cursor:pointer;box-shadow:0 2px 10px rgba(232,23,74,.25);text-decoration:none;transition:var(--ease)}
    .btn-add:hover{background:var(--brand-hover);color:#fff;transform:translateY(-1px)}
    .search-card{background:#fff;border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 20px;margin-bottom:1.2rem;box-shadow:var(--shadow)}
    .search-row{display:flex;align-items:center;gap:8px;justify-content:flex-end;flex-wrap:wrap}
    .search-input{border:1.5px solid var(--border);border-radius:var(--r-sm);padding:9px 14px;font-size:13px;outline:none;min-width:240px;transition:border-color .15s;color:var(--dark)}
    .search-input:focus{border-color:var(--brand)}
    .btn-search{background:var(--brand);color:#fff;border:none;border-radius:var(--r-sm);padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px}
    .alert-ok{background:#f0fdf4;color:#15803d;border-left:3.5px solid #22c55e;border-radius:var(--r-md);padding:12px 16px;font-size:13.5px;margin-bottom:1.2rem;display:flex;align-items:center;gap:9px}

    /* ── Grid ── */
    .product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px}
    .p-card{background:#fff;border-radius:var(--r-lg);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;transition:var(--ease);display:flex;flex-direction:column}
    .p-card:hover{box-shadow:0 8px 30px rgba(0,0,0,.1);transform:translateY(-2px)}
    .p-img-wrap{position:relative;background:#fafafa;flex-shrink:0}
    .p-img{width:100%;height:200px;object-fit:contain;padding:16px;display:block;transition:opacity .2s}
    .p-img-placeholder{width:100%;height:200px;background:var(--surface);display:flex;align-items:center;justify-content:center;color:#d1d5db;font-size:40px}
    .gal-strip{display:flex;gap:4px;padding:6px 8px 8px;border-top:1px solid var(--border);background:#fafafa;overflow-x:auto}
    .gal-strip img{width:36px;height:36px;object-fit:contain;border-radius:4px;border:1.5px solid var(--border);background:#fff;padding:2px;cursor:pointer;transition:border-color .15s;flex-shrink:0}
    .gal-strip img:hover{border-color:var(--brand)}
    .gal-strip img.active{border-color:var(--brand);box-shadow:0 0 0 2px rgba(232,23,74,.2)}
    .p-actions{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-top:1px solid var(--border)}
    .action-group{display:flex;align-items:center;gap:6px}
    .btn-act{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;border:1.5px solid;background:transparent;cursor:pointer;font-size:13px;text-decoration:none;transition:var(--ease)}
    .btn-act-view{border-color:rgba(232,23,74,.3);color:var(--brand)}
    .btn-act-view:hover{background:var(--brand);border-color:var(--brand);color:#fff}
    .btn-act-barcode{border-color:rgba(107,114,128,.3);color:#6b7280}
    .btn-act-barcode:hover{background:#6b7280;border-color:#6b7280;color:#fff}
    .btn-act-edit{border-color:rgba(59,130,246,.3);color:#3b82f6}
    .btn-act-edit:hover{background:#3b82f6;border-color:#3b82f6;color:#fff}
    .btn-act-del{border-color:rgba(239,68,68,.3);color:#ef4444}
    .btn-act-del:hover{background:#ef4444;border-color:#ef4444;color:#fff}
    .form-check-input{width:38px!important;height:20px!important;cursor:pointer;border-radius:10px!important}
    .form-check-input:checked{background-color:var(--brand)!important;border-color:var(--brand)!important}
    .form-check-input:not(:checked){background-color:#d1d5db!important;border-color:#d1d5db!important}
    .p-body{padding:14px 16px;flex:1}
    .p-name{font-size:13.5px;font-weight:700;color:var(--dark);margin:0 0 5px;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden}
    .p-desc{font-size:12px;color:var(--muted);margin:0 0 8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
    .p-price{font-size:14px;font-weight:700;color:var(--dark)}
    .p-rating{font-size:12px;color:#f59e0b;display:flex;align-items:center;gap:4px}

    /* ── List ── */
    .data-card{background:#fff;border-radius:var(--r-lg);box-shadow:var(--shadow);border:1px solid var(--border);overflow:hidden}
    .p-table{width:100%;border-collapse:collapse}
    .p-table th{padding:10px 16px;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);background:var(--surface);border-bottom:1px solid var(--border)}
    .p-table td{padding:12px 16px;font-size:13px;color:var(--dark);border-bottom:1px solid var(--border);vertical-align:middle}
    .p-table tbody tr:hover{background:#fafbff}
    .thumb-sm{width:48px;height:48px;object-fit:contain;border-radius:8px;background:var(--surface);padding:4px;border:1px solid var(--border)}
    .thumb-ph-sm{width:48px;height:48px;background:var(--surface);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#d1d5db;border:1px solid var(--border)}
    .gal-mini{display:flex;gap:4px}
    .gal-mini img{width:28px;height:28px;object-fit:contain;border-radius:4px;border:1px solid var(--border);background:#fff;padding:2px}

    /* ── Delete modal ── */
    .modal-content{border-radius:var(--r-lg)!important;border:none!important;box-shadow:0 20px 60px rgba(0,0,0,.15)!important}
    .del-icon{width:60px;height:60px;background:#fff1f3;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:26px;color:#ef4444}
    .btn-del-confirm{background:#ef4444;border:none;color:#fff;border-radius:var(--r-sm);padding:8px 24px;font-size:13px;font-weight:600;cursor:pointer}
    .btn-modal-cancel{background:transparent;border:1.5px solid var(--border);color:var(--muted);border-radius:var(--r-sm);padding:8px 20px;font-size:13px;cursor:pointer}
    .empty-state{padding:60px 20px;text-align:center}
    .empty-state i{font-size:48px;color:#d1d5db;display:block;margin-bottom:12px}
    .empty-state p{font-size:14px;font-weight:600;color:#6b7280;margin:0 0 4px}
    .empty-state span{font-size:12.5px;color:#9ca3af}
</style>

{{-- PAGE HEADER --}}
<div class="ph">
    <h4 class="ph-title">Product List</h4>
    <div style="display:flex;align-items:center;gap:8px;">
        <button class="btn-view-toggle active" id="gridBtn" onclick="setView('grid')" title="Grid View">
            <i class="bi bi-grid"></i>
        </button>
        <button class="btn-view-toggle" id="listBtn" onclick="setView('list')" title="List View">
            <i class="bi bi-list-ul"></i>
        </button>
        <a href="{{ route('products.create') }}" class="btn-add">
            <i class="bi bi-plus-lg"></i> Add Product
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-ok alert-dismissible" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- SEARCH --}}
<div class="search-card">
    <form method="GET" action="{{ route('products.index') }}" class="search-row">
        <input type="text" name="search" class="search-input"
               placeholder="Search by product name…" value="{{ request('search') }}">
        <button type="submit" class="btn-search">
            <i class="bi bi-search"></i> Search
        </button>
    </form>
</div>

{{-- ════ GRID VIEW ════ --}}
<div id="gridView">
    @if($products->count())
        <div class="product-grid">
            @foreach($products as $product)
            <div class="p-card">

                <div class="p-img-wrap">
                    {{-- Thumbnail — stored as "uploads/product/filename.ext" --}}
                    @if($product->thumbnail)
                        <img src="{{ asset($product->thumbnail) }}"
                             alt="{{ $product->name }}"
                             class="p-img"
                             id="mainImg_{{ $product->id }}"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="p-img-placeholder" style="display:none;"><i class="bi bi-image"></i></div>
                    @else
                        <div class="p-img-placeholder"><i class="bi bi-image"></i></div>
                    @endif

                    {{-- Gallery strip — paths stored as "uploads/product/filename.ext" --}}
                    @if($product->gallery_images && count($product->gallery_images))
                        <div class="gal-strip">
                            @if($product->thumbnail)
                                <img src="{{ asset($product->thumbnail) }}"
                                     class="active"
                                     onclick="switchImg({{ $product->id }},'{{ asset($product->thumbnail) }}',this)"
                                     title="Main">
                            @endif
                            @foreach($product->gallery_images as $gi)
                                <img src="{{ asset($gi) }}"
                                     onclick="switchImg({{ $product->id }},'{{ asset($gi) }}',this)"
                                     title="Gallery">
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="p-actions">
                    <form action="{{ route('products.toggle', $product->id) }}" method="POST">
                        @csrf
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   {{ $product->is_active ? 'checked' : '' }}
                                   onchange="this.closest('form').submit()">
                        </div>
                    </form>
                    <div class="action-group">
                        <a href="#" class="btn-act btn-act-view" title="View"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('products.barcode', $product->id) }}"
                           class="btn-act btn-act-barcode" title="Barcode">
                            <i class="bi bi-upc-scan"></i>
                        </a>
                        <a href="{{ route('products.edit', $product->id) }}"
                           class="btn-act btn-act-edit" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <button type="button" class="btn-act btn-act-del" title="Delete"
                                onclick="openDel({{ $product->id }},'{{ addslashes($product->name) }}')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>

                <div class="p-body">
                    <p class="p-name">{{ $product->name }}</p>
                    <p class="p-desc">{{ $product->short_description }}</p>
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span class="p-price">${{ number_format($product->selling_price, 0) }}</span>
                        <span class="p-rating">
                            <i class="bi bi-star-fill"></i>
                            ({{ number_format($product->rating ?? 0, 1) }})
                        </span>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    @else
        <div class="data-card">
            <div class="empty-state">
                <i class="bi bi-box-seam"></i>
                <p>No Products Found</p>
                <span>Click "Add Product" to add your first product.</span>
            </div>
        </div>
    @endif
</div>

{{-- ════ LIST VIEW ════ --}}
<div id="listView" style="display:none;">
    <div class="data-card">
        <div class="table-responsive">
            <table class="p-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Thumbnail</th>
                        <th>Gallery</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $i => $product)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            {{-- Thumbnail — "uploads/product/filename.ext" --}}
                            @if($product->thumbnail)
                                <img src="{{ asset($product->thumbnail) }}"
                                     class="thumb-sm" alt="{{ $product->name }}"
                                     onerror="this.style.display='none';">
                            @else
                                <div class="thumb-ph-sm"><i class="bi bi-image"></i></div>
                            @endif
                        </td>
                        <td>
                            {{-- Gallery — "uploads/product/filename.ext" --}}
                            @if($product->gallery_images && count($product->gallery_images))
                                <div class="gal-mini">
                                    @foreach(array_slice($product->gallery_images, 0, 3) as $gi)
                                        <img src="{{ asset($gi) }}" title="Gallery"
                                             onerror="this.style.display='none';">
                                    @endforeach
                                    @if(count($product->gallery_images) > 3)
                                        <span style="font-size:11px;color:var(--muted);align-self:center;">
                                            +{{ count($product->gallery_images) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span style="font-size:12px;color:#d1d5db;">—</span>
                            @endif
                        </td>
                        <td style="font-weight:600;">{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--muted);font-family:monospace;">{{ $product->sku }}</td>
                        <td>${{ number_format($product->selling_price, 0) }}</td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td style="text-align:center;">
                            <form action="{{ route('products.toggle', $product->id) }}" method="POST">
                                @csrf
                                <div class="form-check form-switch d-flex justify-content-center m-0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           {{ $product->is_active ? 'checked' : '' }}
                                           onchange="this.closest('form').submit()">
                                </div>
                            </form>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                <a href="{{ route('products.barcode', $product->id) }}"
                                   class="btn-act btn-act-barcode" title="Barcode">
                                    <i class="bi bi-upc-scan"></i>
                                </a>
                                <a href="{{ route('products.edit', $product->id) }}"
                                   class="btn-act btn-act-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn-act btn-act-del"
                                        onclick="openDel({{ $product->id }},'{{ addslashes($product->name) }}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:40px;color:var(--muted);">
                            No products found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- DELETE MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-body" style="padding:32px 24px;text-align:center;">
                <div class="del-icon"><i class="bi bi-trash3-fill"></i></div>
                <p style="font-size:15px;font-weight:700;color:#1a1d23;margin:0 0 8px;">Delete Product?</p>
                <p style="font-size:13.5px;color:var(--muted);margin:0;">
                    Are you sure you want to delete <strong id="del-name"></strong>?
                    <br>This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer gap-2" style="justify-content:center;border-top:1px solid var(--border);padding:14px 22px;">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">No, Cancel</button>
                <form id="delForm" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del-confirm">
                        <i class="bi bi-trash3 me-1"></i> Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function setView(v) {
    var g = v === 'grid';
    document.getElementById('gridView').style.display = g ? 'block' : 'none';
    document.getElementById('listView').style.display = g ? 'none'  : 'block';
    document.getElementById('gridBtn').classList.toggle('active', g);
    document.getElementById('listBtn').classList.toggle('active', !g);
    localStorage.setItem('productView', v);
}
document.addEventListener('DOMContentLoaded', function () {
    setView(localStorage.getItem('productView') || 'grid');
});

function switchImg(pid, src, el) {
    var main = document.getElementById('mainImg_' + pid);
    if (main) { main.src = src; main.style.display = 'block'; }
    var strip = el.closest('.gal-strip');
    if (strip) strip.querySelectorAll('img').forEach(function(i){ i.classList.remove('active'); });
    el.classList.add('active');
}

function openDel(id, name) {
    var base = "{{ route('products.destroy', '__ID__') }}";
    document.getElementById('delForm').action = base.replace('__ID__', id);
    document.getElementById('del-name').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
