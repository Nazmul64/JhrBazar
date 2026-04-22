@extends('admin.master')

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0">Supplier Details</h4>
    <a href="{{ route('admin.supplier.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row g-4">

    {{-- ════════════ LEFT COLUMN ════════════ --}}
    <div class="col-lg-8">

        {{-- Profile Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $supplier->user->name }}</h4>
                        <div class="d-flex flex-wrap gap-3 text-muted small mb-1">
                            <span><i class="bi bi-envelope me-1"></i>{{ $supplier->user->email }}</span>
                            <span><i class="bi bi-telephone me-1"></i>{{ $supplier->user->phone }}</span>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>{{ $supplier->address ?? 'N/A' }}
                        </div>
                    </div>
                    <div>
                        @if($supplier->profile_image)
                            <img src="{{ asset($supplier->profile_image) }}"
                                 alt="Profile"
                                 class="rounded-circle"
                                 style="width:72px;height:72px;object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width:72px;height:72px;font-size:28px;">
                                {{ strtoupper(substr($supplier->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>

                <hr class="my-3">

                {{-- Stats Row --}}
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 text-center">
                            <div class="fs-5 fw-bold text-dark">
                                ${{ number_format($purchases->sum('total_amount'), 2) }}
                            </div>
                            <div class="text-muted small mt-1 d-flex align-items-center justify-content-between">
                                Balance Amount
                                <i class="bi bi-bar-chart-fill text-success ms-1"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 text-center">
                            <div class="fs-5 fw-bold text-dark">
                                ${{ number_format($purchases->sum('paid_amount'), 2) }}
                            </div>
                            <div class="text-muted small mt-1 d-flex align-items-center justify-content-between">
                                Paid Amount
                                <i class="bi bi-credit-card-fill text-warning ms-1"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 text-center">
                            <div class="fs-5 fw-bold text-dark">
                                ${{ number_format($purchases->sum('due_amount'), 2) }}
                            </div>
                            <div class="text-muted small mt-1 d-flex align-items-center justify-content-between">
                                Due Amount
                                <i class="bi bi-exclamation-circle-fill text-danger ms-1"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 text-center">
                            <div class="fs-5 fw-bold text-dark">
                                {{ $supplier->purchases()->count() }}
                            </div>
                            <div class="text-muted small mt-1 d-flex align-items-center justify-content-between">
                                Total Purchases
                                <i class="bi bi-bag-check-fill text-info ms-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Latest Purchases Table --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">Latest Purchases</h6>
                    <a href="#" class="btn btn-danger btn-sm px-3">View All</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>SL.</th>
                                <th>Amount</th>
                                <th>Total Products</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $i => $purchase)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>${{ number_format($purchase->total_amount, 2) }}</td>
                                <td>{{ $purchase->items_count ?? 0 }}</td>
                                <td>
                                    @php
                                        $statusColor = match($purchase->status) {
                                            'received'  => 'success',
                                            'cancelled' => 'danger',
                                            default     => 'warning',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }} text-white px-3 py-1 rounded-pill">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $purchase->created_at->format('Y-m-d') }}</div>
                                    <div class="text-muted small">Created At: {{ $purchase->created_at->format('d M Y') }}</div>
                                </td>
                                <td>
                                    <a href="#" class="text-info fs-5" title="View">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No purchases found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Transactions --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Transactions</h6>
                @if(count($transactions))
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>SL.</th>
                                    <th>Amount</th>
                                    <th>Transaction ID</th>
                                    <th>Note</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $i => $txn)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>${{ number_format($txn->amount, 2) }}</td>
                                    <td>{{ $txn->transaction_id ?? '-' }}</td>
                                    <td>{{ $txn->note ?? '-' }}</td>
                                    <td>{{ $txn->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No transactions yet.</p>
                @endif
            </div>
        </div>

    </div>{{-- end left column --}}

    {{-- ════════════ RIGHT COLUMN ════════════ --}}
    <div class="col-lg-4">

        {{-- Wallet Info --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Wallet Info</h6>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted">Pay amount to supplier:</span>
                    <button class="btn btn-danger btn-sm px-3"
                            data-bs-toggle="modal"
                            data-bs-target="#payModal">
                        <i class="bi bi-send-fill me-1"></i> Pay Now
                    </button>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted">Total Transaction:</span>
                    <span class="fw-bold">{{ count($transactions) }}</span>
                </div>
            </div>
        </div>

        {{-- Current Year Statistics --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">Current Year Statistics</h6>
                <p class="text-muted small mb-3">Traffic Sources</p>
                <canvas id="supplierChart" height="220"></canvas>
                <div class="d-flex gap-3 mt-3 justify-content-center small">
                    <span><span class="badge rounded-circle bg-success me-1">&nbsp;</span> Purchases</span>
                    <span><span class="badge rounded-circle bg-dark me-1">&nbsp;</span> Total Products</span>
                </div>
            </div>
        </div>

    </div>{{-- end right column --}}

</div>{{-- end row --}}


{{-- ════════ PAY NOW MODAL ════════ --}}
<div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="payModalLabel">Pay Amount To Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form action="{{ route('admin.supplier.pay', $supplier->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Amount <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               name="amount"
                               class="form-control"
                               placeholder="Enter amount"
                               step="0.01"
                               min="0"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction ID</label>
                        <input type="text"
                               name="transaction_id"
                               class="form-control"
                               placeholder="Transaction ID">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Attachment (jpg, jpeg, png)</label>
                        <input type="file"
                               name="attachment"
                               class="form-control"
                               accept=".jpg,.jpeg,.png">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Note</label>
                        <textarea name="note"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Any message"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button"
                                class="btn btn-secondary px-4"
                                data-bs-dismiss="modal">Close</button>
                        <button type="submit"
                                class="btn btn-danger px-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ── Monthly purchase data from Laravel ──────────────────────
    const monthlyData = @json($monthlyStats ?? array_fill(0, 12, 0));
    const monthlyProducts = @json($monthlyProducts ?? array_fill(0, 12, 0));

    const labels = ['January','February','March','April','May','June',
                    'July','August','September','October','November','December'];

    new Chart(document.getElementById('supplierChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Purchases',
                    data: monthlyData,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25,135,84,0.1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: 0.3,
                },
                {
                    label: 'Total Products',
                    data: monthlyProducts,
                    borderColor: '#212529',
                    backgroundColor: 'rgba(33,37,41,0.05)',
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: 0.3,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endpush
