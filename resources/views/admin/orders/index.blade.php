@extends('admin.master')

@section('content')
@php
    $cur = $settings->default_currency ?? '৳';
@endphp
<style>
    :root {
        --primary: #4361ee;
        --secondary: #7209b7;
        --success: #4cc9f0;
        --info: #4895ef;
        --warning: #f72585;
        --danger: #e63946;
        --dark: #212529;
        --light: #f8f9fa;
        --white: #ffffff;
        --gray: #6c757d;
        --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        --radius: 12px;
    }

    .order-hub-page { padding: 24px; background: #f8fafc; min-height: 100vh; font-family: 'Inter', system-ui, sans-serif; }

    .order-hub-card {
        border: none;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        background: var(--white);
        margin-bottom: 2rem;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--white);
        padding: 1.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .bg-pending   { background: linear-gradient(135deg, #ff9f43, #ff6b6b); }
    .bg-processing{ background: linear-gradient(135deg, #4834d4, #686de0); }
    .bg-shipped   { background: linear-gradient(135deg, #22a6b3, #7ed6df); }
    .bg-delivered { background: linear-gradient(135deg, #6ab04c, #badc58); }
    .bg-all       { background: linear-gradient(135deg, #30336b, #130f40); }

    .stat-info .value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--dark);
        display: block;
    }

    .stat-info .label {
        font-size: 0.85rem;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .filter-pills {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .pill {
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        background: #f1f3f9;
        color: var(--gray);
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.3s;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pill:hover, .pill.active {
        background: var(--primary);
        color: white;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .badge-count { background: rgba(0,0,0,0.1); padding: 2px 8px; border-radius: 20px; font-size: 11px; }

    .bulk-actions {
        background: #f8f9fe;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .order-table th {
        background: #f8f9fa;
        color: #4b5563;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem;
        border: none;
    }

    .order-table td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .customer-info {
        display: flex;
        flex-direction: column;
    }

    .customer-name { font-weight: 700; color: var(--dark); }
    .customer-phone { font-size: 0.75rem; color: var(--gray); }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        border: none;
    }

    .status-pending   { background: #fff4e5; color: #ff9800; }
    .status-processing{ background: #e8f0fe; color: #1a73e8; }
    .status-shipped   { background: #e6fcf5; color: #0ca678; }
    .status-delivered { background: #f0fdf4; color: #15803d; }
    .status-cancelled { background: #fff5f5; color: #fa5252; }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 2px;
        transition: all 0.2s;
    }

    .btn-view    { background: #4dabf7; }
    .btn-fraud   { background: #7950f2; }
    .btn-edit    { background: #fab005; }
    .btn-delete  { background: #ff6b6b; }
    .btn-steadfast{ background: #0ca678; }
    .btn-pathao   { background: #fd7e14; }

    .action-btn:hover {
        transform: scale(1.1);
        color: white;
    }

    .item-meta {
        font-size: 0.7rem;
        color: #475569;
        background: #f1f3f9;
        padding: 2px 6px;
        border-radius: 4px;
        margin-right: 4px;
        font-weight: 600;
    }

    .search-box {
        position: relative;
        max-width: 300px;
    }

    .search-box input {
        border-radius: 50px;
        padding-left: 2.5rem;
        border: 1px solid #e1e4e8;
    }

    .search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray);
    }
</style>

<div class="order-hub-page">
    <div class="header-flex">
        <h1 class="page-title">{{ $title ?? 'Orders Hub' }}</h1>
        <div class="search-box">
            <i class="bi bi-search"></i>
            <form action="{{ url()->current() }}" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search orders..." value="{{ request('search') }}">
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    @if(!isset($status) || !in_array($status, ['assigned', 'activity']))
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon bg-all"><i class="bi bi-cart-fill"></i></div>
            <div class="stat-info">
                <span class="value">{{ $totalOrders ?? count($orders) }}</span>
                <span class="label">Total Orders</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-pending"><i class="bi bi-clock-history"></i></div>
            <div class="stat-info">
                <span class="value">{{ $pendingOrders ?? 0 }}</span>
                <span class="label">Pending</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-processing"><i class="bi bi-gear-fill"></i></div>
            <div class="stat-info">
                <span class="value">{{ $processingOrders ?? 0 }}</span>
                <span class="label">Processing</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-shipped"><i class="bi bi-truck"></i></div>
            <div class="stat-info">
                <span class="value">{{ $shippedOrders ?? 0 }}</span>
                <span class="label">Shipped</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-delivered"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-info">
                <span class="value">{{ $deliveredOrders ?? 0 }}</span>
                <span class="label">Delivered</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter Pills --}}
    @if(!isset($status) || !in_array($status, ['assigned', 'activity']))
    <div class="filter-pills">
        <a href="{{ route('admin.orders.index', 'all') }}" class="pill {{ ($status ?? '') == 'all' ? 'active' : '' }}">
            All Orders <span class="badge-count">{{ $totalOrders ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.orders.index', 'pending') }}" class="pill {{ ($status ?? '') == 'pending' ? 'active' : '' }}">
            Pending <span class="badge-count">{{ $pendingOrders ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.orders.index', 'processing') }}" class="pill {{ ($status ?? '') == 'processing' ? 'active' : '' }}">
            Processing <span class="badge-count">{{ $processingOrders ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.orders.index', 'shipped') }}" class="pill {{ ($status ?? '') == 'shipped' ? 'active' : '' }}">
            Shipped <span class="badge-count">{{ $shippedOrders ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.orders.index', 'delivered') }}" class="pill {{ ($status ?? '') == 'delivered' ? 'active' : '' }}">
            Delivered <span class="badge-count">{{ $deliveredOrders ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.orders.index', 'cancelled') }}" class="pill {{ ($status ?? '') == 'cancelled' ? 'active' : '' }}">
            Cancelled
        </a>
    </div>
    @endif

    <div class="order-hub-card">
        <div class="card-body p-0">
            {{-- Bulk Actions --}}
            <div class="bulk-actions px-4 py-3">
                <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label fw-bold" for="selectAll">Select All</label>
                </div>
                
                <select class="form-select form-select-sm w-auto" id="bulkStatus">
                    <option value="">Change Status</option>
                    <option value="status:pending">Pending</option>
                    <option value="status:processing">Processing</option>
                    <option value="status:shipped">Shipped</option>
                    <option value="status:delivered">Delivered</option>
                    <option value="status:cancelled">Cancelled</option>
                </select>

                <button class="btn btn-sm btn-danger" onclick="applyBulkAction('delete')">
                    <i class="bi bi-trash"></i> Delete Selected
                </button>

                <button class="btn btn-sm btn-success" onclick="applyBulkAction('steadfast')">
                    <i class="bi bi-truck"></i> Steadfast Send
                </button>

                <button class="btn btn-sm btn-warning" onclick="applyBulkAction('pathao')">
                    <i class="bi bi-send"></i> Pathao
                </button>

                <button class="btn btn-sm btn-info text-white" onclick="generateBulkInvoice()">
                    <i class="bi bi-file-earmark-pdf"></i> Generate Invoice
                </button>
            </div>

            <div class="table-responsive">
                <table class="table order-table m-0">
                    <thead>
                        <tr>
                            <th width="40"></th>
                            <th>Order ID</th>
                            <th>Items</th>
                            <th>Customer</th>
                            <th width="100">Staff</th>
                            <th width="100">Status</th>
                            <th width="120">Payment</th>
                            <th>Total</th>
                            <th>Courier</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        @php
                            $items = $order->order->items ?? [];
                            $firstItem = $items[0] ?? null;
                            $color = $firstItem['color'] ?? 'N/A';
                            $size = $firstItem['size'] ?? 'N/A';
                        @endphp
                        <tr id="order-row-{{ $order->id }}">
                            <td>
                                <input class="form-check-input order-checkbox" type="checkbox" value="{{ $order->id }}">
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-primary" style="font-size: 1.1rem;">#{{ $order->invoice_number }}</span>
                                    <span class="text-muted" style="font-size: 11px;"><i class="bi bi-calendar3 me-1"></i>{{ $order->created_at->format('d M, Y') }}</span>
                                    <span class="text-muted" style="font-size: 11px;"><i class="bi bi-clock me-1"></i>{{ $order->created_at->format('h:i A') }}</span>
                                    @if($order->seller)
                                        <span class="badge bg-soft-info text-info mt-1" style="font-size: 10px; width: fit-content;">{{ $order->seller->name }}</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger mt-1" style="font-size: 10px; width: fit-content;">Admin Order</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="items-list">
                                    @foreach($items as $item)
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            @php
                                                $img = $item['thumbnail'] ?? ($item['image'] ?? null);
                                                if ($img) {
                                                    $img = ltrim($img, '/');
                                                    if (str_starts_with($img, 'http')) {
                                                        $img = $img;
                                                    } elseif (str_starts_with($img, 'uploads/')) {
                                                        $img = asset($img);
                                                    } else {
                                                        $img = asset('uploads/product/' . $img);
                                                    }
                                                }
                                            @endphp
                                            <img src="{{ $img ?? asset('assets/admin/images/no-image.png') }}" 
                                                 style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
                                            <div style="line-height: 1.2;">
                                                <div class="fw-bold small" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    {{ $item['name'] ?? ($item['title'] ?? 'Product') }}
                                                </div>
                                                <div class="text-muted" style="font-size: 10px;">
                                                    {{ $item['qty'] }}×{{ $cur }}{{ number_format($item['price'] ?? 0, 0) }}
                                                    @if(isset($item['color']) && $item['color'] != 'N/A') <span class="badge bg-light text-dark p-0 px-1">{{ $item['color'] }}</span> @endif
                                                    @if(isset($item['size']) && $item['size'] != 'N/A') <span class="badge bg-light text-dark p-0 px-1">{{ $item['size'] }}</span> @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <span class="customer-name">{{ $order->customer?->user?->name ?? 'Guest' }}</span>
                                    <span class="customer-phone"><i class="bi bi-telephone-fill me-1"></i>{{ $order->customer?->user?->phone ?? 'N/A' }}</span>
                                    <span class="text-muted" style="font-size: 10px;">{{ $order->customer?->address ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <select class="form-select form-select-sm border-0 bg-soft-info text-info fw-bold" onchange="assignStaff({{ $order->id }}, this.value)" style="font-size: 11px;">
                                    <option value="">Not Assigned</option>
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->id }}" {{ ($order->order->staff_id ?? '') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                @php $s = $order->order->status ?? 'pending'; @endphp
                                <select class="form-select form-select-sm status-badge status-{{ $s }}" onchange="updateStatus({{ $order->id }}, this.value)" style="font-size: 11px; font-weight: 700;">
                                    <option value="pending" {{ $s == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $s == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $s == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $s == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $s == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-select form-select-sm border-0 bg-light" onchange="updatePaymentStatus({{ $order->id }}, this.value)" style="font-size: 11px;">
                                    <option value="pending" {{ ($order->order->payment_status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ ($order->order->payment_status ?? 'pending') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="partial" {{ ($order->order->payment_status ?? 'pending') == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="refunded" {{ ($order->order->payment_status ?? 'pending') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                <div class="mt-1" style="font-size: 10px; font-weight: 600; color: #666;">
                                    <i class="bi bi-wallet2 me-1"></i>{{ strtoupper($order->payment_method ?? 'COD') }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-end">
                                    <span class="fw-bold" style="font-size: 1rem;">{{ $cur }}{{ number_format($order->grand_total, 0) }}</span>
                                    <span class="text-muted" style="font-size: 11px;">Items: {{ count($items) }}</span>
                                    @if($order->delivery_charge > 0)
                                        <span class="text-muted" style="font-size: 10px;">Ship: {{ $cur }}{{ number_format($order->delivery_charge, 0) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($order->order->courier_name)
                                    <div class="badge bg-soft-success text-success p-2 w-100" style="font-size: 10px;">
                                        <div class="fw-bold"><i class="bi bi-truck me-1"></i>{{ $order->order->courier_name }}</div>
                                        <div class="mt-1 text-uppercase">{{ $order->order->courier_status }}</div>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 10px;">No Courier</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="action-btn btn-view" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.fraud.index', ['search' => $order->customer?->user?->phone]) }}" class="action-btn btn-fraud" title="Fraud Check"><i class="bi bi-shield-check"></i></a>
                                    <a href="javascript:void(0)" onclick="generateBulkInvoice([{{ $order->id }}])" class="action-btn btn-edit" title="Invoice" style="background: var(--info);"><i class="bi bi-file-earmark-pdf"></i></a>
                                    @if(!$order->order->steadfast_order_id)
                                        <a href="javascript:void(0)" onclick="sendIndividualCourier({{ $order->id }}, 'steadfast')" class="action-btn btn-steadfast" title="Steadfast"><i class="bi bi-truck"></i></a>
                                    @endif
                                    @if(!$order->order->pathao_consignment_id)
                                        <a href="javascript:void(0)" onclick="sendIndividualCourier({{ $order->id }}, 'pathao')" class="action-btn btn-pathao" title="Pathao"><i class="bi bi-send"></i></a>
                                    @endif
                                    <a href="javascript:void(0)" onclick="deleteOrder({{ $order->id }})" class="action-btn btn-delete" title="Delete"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                <span class="text-muted">No orders found matching your criteria.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Pathao Details Modal --}}
<div class="modal fade" id="pathaoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pathao Courier Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pathaoForm">
                    <div class="mb-3">
                        <label class="form-label">Store</label>
                        <select class="form-select" id="pathaoStore" required>
                            <option value="">Select Store</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <select class="form-select" id="pathaoCity" required>
                            <option value="">Select City</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Zone</label>
                        <select class="form-select" id="pathaoZone" required disabled>
                            <option value="">Select Zone</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Area</label>
                        <select class="form-select" id="pathaoArea" required disabled>
                            <option value="">Select Area</option>
                        </select>
                    </div>
                    <input type="hidden" id="pathaoOrderIds">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSubmitPathao" onclick="submitPathaoBulk()">Send to Pathao</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = this.checked);
    });

    function updateStatus(id, status) {
        fetch(`{{ url('admin/orders/status') }}/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const select = document.querySelector(`#order-row-${id} .status-badge`);
                select.className = `form-select form-select-sm status-badge status-${status}`;
                Swal.fire({ icon: 'success', title: 'Success', text: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            }
        });
    }

    function assignStaff(id, staffId) {
        fetch(`{{ url('admin/orders/assign-staff') }}/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ staff_id: staffId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire({ icon: 'success', title: 'Assigned', text: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            }
        });
    }

    function updatePaymentStatus(id, status) {
        fetch(`{{ url('admin/orders/payment-status') }}/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ payment_status: status })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire({ icon: 'success', title: 'Updated', text: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            }
        });
    }

    function sendIndividualCourier(id, courier) {
        applyBulkAction(courier, [id]);
    }

    function applyBulkAction(action, forceIds = null) {
        const selected = forceIds || Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
        if(selected.length === 0) {
            Swal.fire('Error', 'Please select at least one order.', 'error');
            return;
        }

        const finalAction = action === 'status' ? document.getElementById('bulkStatus').value : action;
        if(!finalAction) return;

        if (finalAction === 'pathao') {
            document.getElementById('pathaoOrderIds').value = selected.join(',');
            openPathaoModal();
            return;
        }

        Swal.fire({
            title: forceIds ? 'Confirm Sending?' : 'Are you sure?',
            text: forceIds ? `Send this order to ${action}?` : `Apply ${finalAction.replace('status:', 'status ')} to ${selected.length} orders?`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ route('admin.orders.bulk-action') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ action: finalAction, ids: selected })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire('Success!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            }
        });
    }

    function deleteOrder(id) {
        Swal.fire({
            title: 'Delete Order?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff6b6b',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ route('admin.orders.bulk-action') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ action: 'delete', ids: [id] })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById(`order-row-${id}`).remove();
                        Swal.fire('Deleted!', data.message, 'success');
                    }
                });
            }
        });
    }

    // Auto-trigger bulk status change when select option changes
    document.getElementById('bulkStatus').addEventListener('change', function() {
        if(this.value) applyBulkAction('status');
    });

    /* Pathao Modal Logic */
    function openPathaoModal() {
        const modal = new bootstrap.Modal(document.getElementById('pathaoModal'));
        modal.show();
        
        // Load Stores & Cities
        fetchPathaoData('stores', 'pathaoStore');
        fetchPathaoData('cities', 'pathaoCity');
    }

    function fetchPathaoData(type, targetId, parentId = null) {
        let url = `{{ url('admin/orders/pathao') }}/${type}`;
        if (parentId) url = `{{ url('admin/orders/pathao') }}/${type}/${parentId}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById(targetId);
                select.innerHTML = `<option value="">Select ${type.charAt(0).toUpperCase() + type.slice(1, -1)}</option>`;
                data.forEach(item => {
                    const id = item.city_id || item.zone_id || item.area_id || item.store_id;
                    const name = item.city_name || item.zone_name || item.area_name || item.store_name;
                    select.innerHTML += `<option value="${id}">${name}</option>`;
                });
                select.disabled = false;
            });
    }

    document.getElementById('pathaoCity').addEventListener('change', function() {
        if (this.value) fetchPathaoData('zones', 'pathaoZone', this.value);
        else document.getElementById('pathaoZone').disabled = true;
    });

    document.getElementById('pathaoZone').addEventListener('change', function() {
        if (this.value) fetchPathaoData('areas', 'pathaoArea', this.value);
        else document.getElementById('pathaoArea').disabled = true;
    });

    function submitPathaoBulk() {
        const ids = document.getElementById('pathaoOrderIds').value.split(',');
        const storeId = document.getElementById('pathaoStore').value;
        const cityId = document.getElementById('pathaoCity').value;
        const zoneId = document.getElementById('pathaoZone').value;
        const areaId = document.getElementById('pathaoArea').value;

        if (!storeId || !cityId || !zoneId || !areaId) {
            Swal.fire('Error', 'Please select all location details.', 'error');
            return;
        }

        const btn = document.getElementById('btnSubmitPathao');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';

        fetch(`{{ route('admin.orders.bulk-action') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                action: 'pathao',
                ids: ids,
                store_id: storeId,
                city_id: cityId,
                zone_id: zoneId,
                area_id: areaId
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Send to Pathao';
            if(data.success) {
                bootstrap.Modal.getInstance(document.getElementById('pathaoModal')).hide();
                Swal.fire('Success!', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        });
    }

    function generateBulkInvoice() {
        const selected = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
        if(selected.length === 0) {
            Swal.fire('Error', 'Please select at least one order.', 'error');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.orders.bulk-invoice') }}`;
        form.target = '_blank';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const idsInput = document.createElement('input');
        idsInput.type = 'hidden';
        idsInput.name = 'ids';
        idsInput.value = selected.join(',');
        form.appendChild(idsInput);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
</script>
@endsection
