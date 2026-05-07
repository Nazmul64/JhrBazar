@extends('admin.master')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Purchase Returns</h5>
            <a href="{{ route('seller.purchase.return-create') }}" class="btn btn-danger btn-sm px-3" style="border-radius: 10px;">
                <i class="bi bi-arrow-return-left me-1"></i> Add Purchase Return
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Return ID</th>
                            <th>Original Invoice</th>
                            <th>Return Date</th>
                            <th class="text-center">Total Amount</th>
                            <th>Note</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $return)
                        <tr>
                            <td class="ps-4 fw-bold text-danger">#RET-{{ $return->id }}</td>
                            <td>{{ $return->purchase->invoice_no }}</td>
                            <td>{{ date('d M, Y', strtotime($return->return_date)) }}</td>
                            <td class="text-center fw-bold">{{ number_format($return->total_amount, 2) }}</td>
                            <td>{{ $return->note ?? '—' }}</td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light-danger view-details" data-id="{{ $return->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No purchase returns found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.btn-light-danger { background: #fff1f2; color: #f43f5e; border: none; }
.btn-light-danger:hover { background: #f43f5e; color: #fff; }
</style>
@endsection
