@extends('admin.master')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Purchase Invoices</h5>
            <a href="{{ route('seller.purchase.create') }}" class="btn btn-primary btn-sm px-3" style="border-radius: 10px;">
                <i class="bi bi-plus-lg me-1"></i> Add New Purchase
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Invoice No</th>
                            <th>Purchase Name</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-primary">{{ $purchase->invoice_no }}</span>
                            </td>
                            <td>{{ $purchase->purchase_name ?? '—' }}</td>
                            <td>
                                <span class="fw-semibold">{{ $purchase->supplier->name }}</span>
                            </td>
                            <td>{{ date('d M, Y', strtotime($purchase->purchase_date)) }}</td>
                            <td class="text-center">
                                <span class="fw-bold">{{ number_format($purchase->total_amount, 2) }}</span>
                                <div class="small text-muted">Paid: {{ number_format($purchase->paid_amount, 2) }}</div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClass = match($purchase->payment_status) {
                                        'paid' => 'bg-success-subtle text-success',
                                        'partial' => 'bg-warning-subtle text-warning',
                                        default => 'bg-danger-subtle text-danger'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} px-3 py-2 text-uppercase" style="border-radius: 10px; font-size: 0.7rem;">
                                    {{ $purchase->payment_status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light-primary view-details" data-id="{{ $purchase->id }}" data-bs-toggle="modal" data-bs-target="#detailsModal">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if($purchase->purchase_slip)
                                    <a href="{{ asset($purchase->purchase_slip) }}" target="_blank" class="btn btn-sm btn-light-info">
                                        <i class="bi bi-file-earmark-image"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No purchase history found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Purchase Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <div id="detailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.view-details').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const content = document.getElementById('detailsContent');
        content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
        
        fetch(`/seller/purchase/details/${id}`)
            .then(res => res.json())
            .then(data => {
                let itemsHtml = '';
                data.items.forEach(item => {
                    itemsHtml += `
                        <tr>
                            <td>${item.product.name}</td>
                            <td class="text-center">${item.quantity}</td>
                            <td class="text-center">${parseFloat(item.unit_price).toFixed(2)}</td>
                            <td class="text-end">${parseFloat(item.sub_total).toFixed(2)}</td>
                        </tr>
                    `;
                });

                content.innerHTML = `
                    <div class="row mb-4">
                        <div class="col-6">
                            <small class="text-muted text-uppercase d-block mb-1">Invoice</small>
                            <h6 class="fw-bold">${data.invoice_no}</h6>
                            <small class="text-muted text-uppercase d-block mb-1 mt-3">Date</small>
                            <h6 class="fw-bold">${data.purchase_date}</h6>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted text-uppercase d-block mb-1">Supplier</small>
                            <h6 class="fw-bold">${data.supplier.name}</h6>
                        </div>
                    </div>
                    <table class="table table-sm">
                        <thead>
                            <tr class="text-muted small">
                                <th>Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>${itemsHtml}</tbody>
                        <tfoot class="border-top-0">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Grand Total</td>
                                <td class="text-end fw-bold text-primary">${parseFloat(data.total_amount).toFixed(2)}</td>
                            </tr>
                        </tfoot>
                    </table>
                `;
            });
    });
});
</script>

<style>
.btn-light-primary { background: #eef2ff; color: #4f46e5; border: none; }
.btn-light-primary:hover { background: #4f46e5; color: #fff; }
.btn-light-info { background: #ecfdf5; color: #059669; border: none; }
.btn-light-info:hover { background: #059669; color: #fff; }
</style>
@endsection
