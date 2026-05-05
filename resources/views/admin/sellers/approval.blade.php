@extends('admin.master')
@section('content')

<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-person-check-fill me-2 text-danger"></i> Seller Approvals
        </h4>
    </div>

    {{-- ── PENDING SELLERS ─────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-warning me-2"></i> Pending Approvals</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Owner Info</th>
                            <th>Shop Info</th>
                            <th>Bank Account</th>
                            <th>Documents</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingSellers as $seller)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 bg-light rounded-circle text-center d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                                        <i class="bi bi-person text-muted"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $seller->name }} {{ $seller->last_name }}</div>
                                        <div class="small text-muted">{{ $seller->email }}</div>
                                        <div class="small text-muted">{{ $seller->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($seller->shop)
                                    <div class="fw-bold text-primary">{{ $seller->shop->name }}</div>
                                    <div class="small text-muted">{{ $seller->shop->business_type }}</div>
                                    <div class="small">{{ $seller->shop->city }}, {{ $seller->shop->postal_code }}</div>
                                @else
                                    <span class="text-muted small">No Shop Info</span>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-bold">{{ $seller->bank_name }}</div>
                                <div class="small">{{ $seller->bank_account_number }}</div>
                                <div class="small text-muted">{{ $seller->bank_account_holder }}</div>
                            </td>
                            <td>
                                @if($seller->national_id_card)
                                    <a href="{{ asset($seller->national_id_card) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark-image me-1"></i> View NID
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted">No NID</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <form action="{{ route('admin.sellers.approve', $seller->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm px-3 fw-bold">
                                            <i class="bi bi-check-lg me-1"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.sellers.reject', $seller->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No pending seller registrations found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── ACTIVE SELLERS ──────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-check-circle-fill text-success me-2"></i> Approved Sellers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Owner</th>
                            <th>Shop Name</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeSellers as $seller)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $seller->name }} {{ $seller->last_name }}</div>
                                <div class="small text-muted">{{ $seller->email }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ $seller->shop->name ?? 'N/A' }}</div>
                            </td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>{{ $seller->created_at->format('d M, Y') }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.sellers.reject', $seller->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                                        Suspend
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No approved sellers yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection
