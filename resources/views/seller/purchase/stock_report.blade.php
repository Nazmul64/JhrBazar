@extends('admin.master')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Stock Report</h5>
            <div>
                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search products..." style="width: 250px; border-radius: 20px;">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="stockTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 80px;">SN</th>
                            <th>Product Name</th>
                            <th class="text-center" style="background: #f8fafc;">Stock Details</th>
                            <th class="text-center">Purchased (In)</th>
                            <th class="text-center">Sold (Out)</th>
                            <th class="text-center pe-4">Available Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                        <tr>
                            <td class="ps-4">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($product->thumbnail)
                                            <img src="{{ asset($product->thumbnail) }}" alt="" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 8px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="fw-semibold text-primary d-block">{{ $product->name }}</span>
                                        <small class="text-muted">SKU: {{ $product->sku }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center" style="background: #f8fafc;">
                                <span class="badge bg-light text-dark border">{{ $product->stock_quantity }} Items</span>
                            </td>
                            <td class="text-center fw-bold">{{ $product->purchased_in ?? 0 }}</td>
                            <td class="text-center fw-bold">{{ $product->sold_out ?? 0 }}</td>
                            <td class="text-center pe-4">
                                <span class="badge {{ $product->stock_quantity > 10 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2" style="border-radius: 10px;">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-box-seam text-muted display-4 d-block mb-3"></i>
                                <span class="text-muted">No products found in your stock.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#stockTable tbody tr');
    
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>
@endsection
