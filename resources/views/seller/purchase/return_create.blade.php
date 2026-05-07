@extends('admin.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Create Purchase Return</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Search by Invoice</label>
                        <div class="input-group">
                            <select id="purchaseSelect" class="form-select border-0 bg-light" style="border-radius: 10px 0 0 10px;">
                                <option value="">Select Invoice</option>
                                @foreach($purchases as $purchase)
                                    <option value="{{ $purchase->id }}">{{ $purchase->invoice_no }} ({{ $purchase->supplier->name }})</option>
                                @endforeach
                            </select>
                            <button type="button" id="loadPurchase" class="btn btn-primary px-4" style="border-radius: 0 10px 10px 0;">
                                <i class="bi bi-search me-1"></i> Search
                            </button>
                        </div>
                    </div>

                    <form action="{{ route('seller.purchase.return-store') }}" method="POST" id="returnForm" class="d-none">
                        @csrf
                        <input type="hidden" name="purchase_id" id="purchaseIdInput">
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Return Date</label>
                                <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Note</label>
                                <input type="text" name="note" class="form-control" placeholder="Optional notes" style="border-radius: 8px;">
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Purchased Qty</th>
                                        <th class="text-center" style="width: 150px;">Return Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="returnItemsBody">
                                    {{-- Items will be loaded here via JS --}}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Return Total</td>
                                        <td class="text-end fw-bold text-danger fs-5">$<span id="returnGrandTotal">0.00</span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-danger px-5 py-2 fw-bold" style="border-radius: 10px;">
                                <i class="bi bi-arrow-return-left me-2"></i> Submit Return
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loadPurchase').addEventListener('click', function() {
    const id = document.getElementById('purchaseSelect').value;
    if (!id) return;

    fetch(`/seller/purchase/details/${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('purchaseIdInput').value = data.id;
            const body = document.getElementById('returnItemsBody');
            body.innerHTML = '';
            
            data.items.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <span class="fw-semibold">${item.product.name}</span>
                        <input type="hidden" name="products[${index}][id]" value="${item.product_id}">
                        <input type="hidden" name="products[${index}][price]" value="${item.unit_price}">
                    </td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-center">
                        <input type="number" name="products[${index}][qty]" class="form-control form-control-sm text-center return-qty" 
                               value="0" min="0" max="${item.quantity}" style="border-radius: 5px;">
                    </td>
                    <td class="text-end">${parseFloat(item.unit_price).toFixed(2)}</td>
                    <td class="text-end fw-bold return-item-total">0.00</td>
                `;
                body.appendChild(row);

                row.querySelector('.return-qty').addEventListener('input', function() {
                    const qty = parseFloat(this.value) || 0;
                    const price = parseFloat(item.unit_price);
                    row.querySelector('.return-item-total').innerText = (qty * price).toFixed(2);
                    calculateGrandTotal();
                });
            });

            document.getElementById('returnForm').classList.remove('d-none');
        });
});

function calculateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.return-item-total').forEach(el => {
        total += parseFloat(el.innerText);
    });
    document.getElementById('returnGrandTotal').innerText = total.toFixed(2);
}
</script>
@endsection
