@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">POS Drafts</h4>
        <a href="{{ route('seller.pos.index') }}" class="btn btn-primary" style="background: #e7567c; border:none;">Go to POS</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">SL</th>
                            <th>Created Date</th>
                            <th>Customer</th>
                            <th>Total Products</th>
                            <th>Sub Total</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drafts as $index => $draft)
                        <tr>
                            <td class="ps-4">{{ $index + 1 }}</td>
                            <td>
                                <div>{{ $draft->created_at->format('d M Y, h:i A') }}</div>
                                <small class="text-muted">{{ $draft->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($draft->customer)
                                    <div>{{ $draft->customer->first_name }} {{ $draft->customer->last_name }}</div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-danger rounded-pill">{{ count($draft->items) }} Items</span>
                            </td>
                            <td>৳{{ number_format($draft->sub_total, 2) }}</td>
                            <td>৳{{ number_format($draft->discount, 2) }}</td>
                            <td><span class="fw-bold text-primary">৳{{ number_format($draft->grand_total, 2) }}</span></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('seller.pos.index') }}?draft_id={{ $draft->id }}" class="btn btn-sm btn-outline-info me-1"><i class="bi bi-pencil-square"></i></a>
                                <form action="javascript:void(0)" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No drafts found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($drafts->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $drafts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
