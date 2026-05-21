@extends('admin.master')
@section('title', 'Withdrawals')
@section('content')

<style>
    .balance-card {
        background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);
        color: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
    }
    .balance-card h3 { font-size: 32px; font-weight: 800; margin-bottom: 5px; color: white; }
    .balance-card p { opacity: 0.8; font-size: 14px; margin: 0; }
    .limit-info { background: #f8fafc; border-radius: 12px; padding: 15px; margin-top: 20px; }
    .limit-info div { display: flex; justify-content: space-between; font-size: 13px; color: #64748b; margin-bottom: 5px; }
    .limit-info strong { color: #1e293b; }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Withdrawals</h4>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                            <i class="ri-add-line me-1"></i> Add Withdraw
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="balance-card">
                    <p>Available Balance</p>
                    <h3>৳{{ number_format($balance, 2) }}</h3>
                    <div class="limit-info">
                        <div>
                            <span>Min Withdraw:</span>
                            <strong>৳{{ number_format($withdrawConfig['min'] ?? 0, 2) }}</strong>
                        </div>
                        <div>
                            <span>Max Withdraw:</span>
                            <strong>৳{{ number_format($withdrawConfig['max'] ?? 10000, 2) }}</strong>
                        </div>
                        <div>
                            <span>Commission:</span>
                            <strong>{{ number_format($withdrawConfig['commission_percent'] ?? 0, 2) }}%</strong>
                        </div>
                        <div>
                            <span>Withdraw Fee:</span>
                            <strong>৳{{ number_format($withdrawConfig['charge'] ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Withdraw History</h4>
                        <table class="table table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Amount</th>
                                    <th>Bank Info</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdraws as $key => $item)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td><h6 class="mb-0">৳{{ number_format($item->amount, 2) }}</h6></td>
                                    <td>
                                        <strong>{{ $item->bank->name }}</strong><br>
                                        <small>{{ $item->account_number }}</small>
                                    </td>
                                    <td>{{ $item->created_at->format('d M, Y') }}</td>
                                    <td>
                                        @if($item->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($item->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($item->status == 'cancelled')
                                            <span class="badge bg-secondary">Cancelled</span>
                                        @elseif($item->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif

                                        @if($item->admin_note)
                                            <div class="mt-1">
                                                <small class="text-danger"><strong>Note:</strong> {{ $item->admin_note }}</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status == 'pending')
                                            <form action="{{ route('seller.withdraws.cancel', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-danger btn-sm">Cancel Withdraw</button>
                                            </form>
                                        @else
                                            <span class="text-muted small">No Action</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No withdrawal requests found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $withdraws->links() }}
                        </div>

                        @if(!empty($withdrawConfig['rules']))
                            <div class="alert alert-info mt-3">
                                <h5 class="fs-6 mb-2">Withdraw Rules</h5>
                                <ul class="mb-0">
                                    @foreach($withdrawConfig['rules'] as $rule)
                                        <li>{{ $rule }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Withdraw Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('seller.withdraws.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Withdraw Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control"
                               placeholder="Min: {{ $withdrawConfig['min'] ?? 0 }} | Max: {{ $withdrawConfig['max'] ?? 1000000 }}"
                               min="{{ $withdrawConfig['min'] ?? 0 }}"
                               max="{{ $withdrawConfig['max'] ?? 1000000 }}"
                               required>
                        <div class="form-text small">Admin Limit: ৳{{ number_format($withdrawConfig['min'] ?? 0, 2) }} - ৳{{ number_format($withdrawConfig['max'] ?? 1000000, 2) }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Bank <span class="text-danger">*</span></label>
                        <select name="bank_id" class="form-select" required>
                            <option value="">Choose Bank...</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" class="form-control" placeholder="Name on account" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" class="form-control" placeholder="Account number / Wallet ID" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                        <input type="text" name="contact_number" class="form-control" placeholder="Your phone number" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Any message (Optional)</label>
                        <textarea name="seller_note" class="form-control" rows="2" placeholder="Message to admin..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
