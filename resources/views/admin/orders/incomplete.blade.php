@extends('admin.master')

@section('content')
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

    .incomplete-page { padding: 24px; background: #f8fafc; min-height: 100vh; font-family: 'Inter', system-ui, sans-serif; }
    
    .header-section { margin-bottom: 2rem; }
    .page-title { font-size: 1.75rem; font-weight: 800; color: var(--dark); margin-bottom: 0.5rem; }
    .page-subtitle { color: var(--gray); font-size: 0.9rem; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
        gap: 1.25rem;
        border: 1px solid #f1f5f9;
        transition: transform 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-5px); }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .stat-icon.incomplete { background: #fff0f3; color: #ff4d4d; }
    .stat-icon.recovered  { background: #f0fdf4; color: #22c55e; }
    .stat-icon.contacted  { background: #eff6ff; color: #3b82f6; }
    .stat-icon.total      { background: #f8fafc; color: #64748b; }

    .stat-value { font-size: 1.5rem; font-weight: 800; color: var(--dark); line-height: 1.2; }
    .stat-label { font-size: 0.85rem; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; }

    .alert-banner {
        background: #fff5f5;
        border-left: 4px solid #ff4d4d;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        color: #991b1b;
        font-weight: 600;
    }

    .filter-pills {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }
    .pill {
        padding: 0.6rem 1.25rem;
        border-radius: 50px;
        background: #fff;
        color: var(--gray);
        font-weight: 600;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
    }
    .pill.active { background: #ff4d4d; color: white; border-color: #ff4d4d; box-shadow: 0 4px 12px rgba(255, 77, 77, 0.3); }
    .pill .count { background: rgba(0,0,0,0.08); padding: 2px 8px; border-radius: 20px; font-size: 11px; }
    .pill.active .count { background: rgba(255,255,255,0.2); }

    .action-bar {
        background: white;
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .btn-group-custom { display: flex; gap: 0.5rem; }
    .btn-custom {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        border: none;
        transition: all 0.2s;
    }
    .btn-status { background: #7c3aed; color: white; }
    .btn-delete { background: #ef4444; color: white; }
    .btn-steadfast { background: #f59e0b; color: white; }
    .btn-pathao { background: #10b981; color: white; }
    .btn-custom:hover { opacity: 0.9; transform: translateY(-1px); }

    .search-box { position: relative; width: 300px; }
    .search-box input {
        width: 100%;
        padding: 0.6rem 1rem 0.6rem 2.5rem;
        border-radius: 50px;
        border: 1px solid #e2e8f0;
        font-size: 0.9rem;
    }
    .search-box i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--gray); }

    .data-card { background: white; border-radius: 12px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: var(--shadow); }
    .leads-table th {
        background: #f8fafc;
        padding: 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 2px solid #f1f5f9;
    }
    .leads-table td { padding: 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }

    .action-icons { display: flex; flex-wrap: wrap; gap: 4px; max-width: 120px; }
    .icon-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        font-size: 12px;
        transition: all 0.2s;
    }
    .icon-view { background: #38bdf8; }
    .icon-whatsapp { background: #22c55e; }
    .icon-call { background: #3b82f6; }
    .icon-sms { background: #6366f1; }
    .icon-recovered { background: #10b981; }
    .icon-delete { background: #ef4444; }
    .icon-btn:hover { transform: scale(1.1); color: white; }

    .customer-cell { display: flex; flex-direction: column; }
    .cust-name { font-weight: 700; color: var(--dark); }
    .cust-loc { font-size: 0.75rem; color: var(--gray); }

    .phone-cell { font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 6px; }
    .phone-cell i { color: #22c55e; }

    .item-list { font-size: 0.75rem; color: #475569; list-style: none; padding: 0; margin: 0; }
    .item-list li { margin-bottom: 2px; }

    .total-cell { font-weight: 800; color: var(--dark); }
    .payment-tag {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 800;
        text-transform: uppercase;
        background: #f1f5f9;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .status-incomplete { background: #fee2e2; color: #ef4444; }
    .status-contacted  { background: #dbeafe; color: #2563eb; }
    .status-recovered  { background: #dcfce7; color: #16a34a; }

    .courier-info { display: flex; flex-direction: column; gap: 2px; }
    .courier-tag { font-size: 10px; color: #64748b; background: #f1f5f9; padding: 1px 4px; border-radius: 3px; }
    
    .btn-back {
        background: #475569;
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-back:hover { background: #334155; color: white; }
    
    .bg-soft-info { background-color: #e0f2fe; }
    .text-info { color: #0284c7 !important; }
</style>

<div class="incomplete-page">
    <div class="d-flex justify-content-between align-items-center header-section">
        <div>
            <h1 class="page-title">Incomplete Orders</h1>
            <p class="page-subtitle">যারা চেকআউট শুরু করেছেন কিন্তু অর্ডার কমপ্লিট করেননি</p>
        </div>
        <a href="{{ route('admin.orders.index', 'all') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Orders
        </a>
    </div>

    @if($totalIncomplete > 0)
    <div class="alert-banner">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span>{{ $totalIncomplete }}টি Incomplete অর্ডার আছে। এই কাস্টমারদের call করে অর্ডার complete করতে সাহায্য করুন।</span>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total"><i class="bi bi-layout-text-sidebar-reverse"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalEntry }}</div>
                <div class="stat-label">মোট এন্ট্রি</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon incomplete"><i class="bi bi-cart-x"></i></div>
            <div class="stat-info">
                <div class="stat-value text-danger">{{ $totalIncomplete }}</div>
                <div class="stat-label">মোট Incomplete</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon contacted"><i class="bi bi-telephone-outbound"></i></div>
            <div class="stat-info">
                <div class="stat-value text-primary">{{ $totalContacted }}</div>
                <div class="stat-label">Contacted</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon recovered"><i class="bi bi-check2-all"></i></div>
            <div class="stat-info">
                <div class="stat-value text-success">{{ $totalRecovered }}</div>
                <div class="stat-label">Recovered</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-pills">
        <a href="{{ route('admin.orders.incomplete', ['status' => 'all']) }}" class="pill {{ request('status') == 'all' || !request('status') ? 'active' : '' }}">
            All <span class="count">{{ $totalEntry }}</span>
        </a>
        <a href="{{ route('admin.orders.incomplete', ['status' => 'incomplete']) }}" class="pill {{ request('status') == 'incomplete' ? 'active' : '' }}">
            Incomplete <span class="count">{{ $totalIncomplete }}</span>
        </a>
        <a href="{{ route('admin.orders.incomplete', ['status' => 'contacted']) }}" class="pill {{ request('status') == 'contacted' ? 'active' : '' }}">
            Contacted <span class="count">{{ $totalContacted }}</span>
        </a>
        <a href="{{ route('admin.orders.incomplete', ['status' => 'recovered']) }}" class="pill {{ request('status') == 'recovered' ? 'active' : '' }}">
            Recovered <span class="count">{{ $totalRecovered }}</span>
        </a>
    </div>

    {{-- Action Bar --}}
    <div class="action-bar">
        <div class="btn-group-custom">
            <button class="btn-custom btn-status" onclick="bulkUpdateStatus()">
                <i class="bi bi-arrow-repeat"></i> Change Status
            </button>
            <button class="btn-custom btn-delete" onclick="bulkDeleteLeads()">
                <i class="bi bi-trash"></i> Delete Selected
            </button>
            <div class="dropdown d-inline-block">
                <button class="btn-custom btn-status dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-plus"></i> Assign Staff
                </button>
                <ul class="dropdown-menu shadow border-0">
                    @foreach(\App\Models\User::whereIn('role', ['manager', 'staff', 'employee', 'subadmin'])->get() as $staff)
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="bulkAssign({{ $staff->id }}, '{{ $staff->name }}')">{{ $staff->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <button class="btn-custom btn-steadfast" onclick="bulkSendSteadfast()">
                <i class="bi bi-truck"></i> Steadfast Send
            </button>
            <button class="btn-custom btn-pathao" onclick="bulkSendPathao()">
                <i class="bi bi-send"></i> Pathao
            </button>
        </div>
        <div class="search-box">
            <i class="bi bi-search"></i>
            <form action="{{ route('admin.orders.incomplete') }}" method="GET">
                <input type="text" name="search" placeholder="নাম/ফোন খুঁজুন..." value="{{ request('search') }}">
            </form>
        </div>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="table leads-table m-0">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" id="selectAll"></th>
                        <th width="40">SL</th>
                        <th width="120">ACTION</th>
                        <th>CUSTOMER</th>
                        <th>PHONE</th>
                        <th>STAFF</th>
                        <th>FRAUD</th>
                        <th>AREA</th>
                        <th>CART ITEMS</th>
                        <th>TOTAL</th>
                        <th>PAYMENT</th>
                        <th>STATUS</th>
                        <th>COURIER</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td><input type="checkbox" class="lead-checkbox" value="{{ $lead->id }}"></td>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="action-icons">
                                <a href="javascript:void(0)" onclick="editLead({{ json_encode($lead) }})" class="icon-btn icon-view" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" class="icon-btn icon-whatsapp" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                                <a href="tel:{{ $lead->phone }}" class="icon-btn icon-call" title="Call"><i class="bi bi-telephone"></i></a>
                                <a href="#" class="icon-btn icon-sms" title="SMS"><i class="bi bi-chat-dots"></i></a>
                                <a href="javascript:void(0)" onclick="updateStatus({{ $lead->id }}, 'recovered')" class="icon-btn icon-recovered" title="Mark Recovered"><i class="bi bi-check-lg"></i></a>
                                <a href="javascript:void(0)" onclick="deleteLead({{ $lead->id }})" class="icon-btn icon-delete" title="Delete"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                        <td>
                            <div class="customer-cell">
                                <span class="cust-name">{{ $lead->name ?? 'Guest' }}</span>
                                <span class="cust-loc">{{ $lead->address ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="phone-cell">
                                <i class="bi bi-telephone-fill"></i>
                                {{ $lead->phone }}
                            </div>
                        </td>
                            <select class="form-select form-select-sm border-0 bg-soft-info text-info fw-bold" onchange="assignStaff([{{ $lead->id }}], this.value)" style="font-size: 11px; width: 140px; cursor: pointer; border-radius: 6px;">
                                <option value="">Assign Staff</option>
                                @foreach(\App\Models\User::whereNotNull('role_id')->orWhereIn('role', ['admin', 'super_admin', 'manager', 'staff', 'employee', 'subadmin'])->get() as $staff)
                                    <option value="{{ $staff->id }}" {{ $lead->staff_id == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} ({{ $staff->roleModel ? $staff->roleModel->name : ucfirst($staff->role) }})
                                    </option>
                                @endforeach
                            </select>
                        <td class="text-center">-</td>
                        <td>
                            <span class="text-muted small">{{ $lead->area ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <ul class="item-list">
                                @if($lead->cart_items && is_array($lead->cart_items))
                                    @foreach($lead->cart_items as $item)
                                        <li class="d-flex align-items-center gap-2 mb-2">
                                            @php
                                                $imgUrl = $item['image'] ?? ($item['thumbnail'] ?? null);
                                                if ($imgUrl) {
                                                    $imgUrl = ltrim($imgUrl, '/');
                                                    if (str_starts_with($imgUrl, 'http')) {
                                                        $imgUrl = $imgUrl;
                                                    } elseif (str_starts_with($imgUrl, 'uploads/')) {
                                                        $imgUrl = asset($imgUrl);
                                                    } else {
                                                        $imgUrl = asset('uploads/product/' . $imgUrl);
                                                    }
                                                }
                                            @endphp
                                            <img src="{{ $imgUrl ?? asset('assets/admin/images/no-image.png') }}" 
                                                 style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
                                            <div>
                                                <div class="fw-bold">{{ $item['title'] ?? ($item['name'] ?? 'Product') }}</div>
                                                <div class="text-muted" style="font-size: 10px;">{{ $item['qty'] ?? 1 }} × ৳{{ number_format($item['price'] ?? 0, 0) }}</div>
                                            </div>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="text-muted italic">Empty Cart</li>
                                @endif
                            </ul>
                        </td>
                        <td>
                            <div class="total-cell">৳{{ number_format($lead->estimated_total, 0) }}</div>
                            <div class="text-muted small" style="font-size: 10px;">+ {{ $lead->delivery_charge ?? 120 }} shipping</div>
                        </td>
                        <td>
                            <span class="payment-tag">{{ $lead->payment_method ?? 'COD' }}</span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $lead->status }}">
                                <i class="bi bi-circle-fill" style="font-size: 6px;"></i>
                                {{ ucfirst($lead->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="courier-info">
                                <span class="courier-tag">SF: {{ $lead->steadfast_id ? 'Yes' : 'No' }}</span>
                                <span class="courier-tag">Pathao: {{ $lead->pathao_id ? 'Yes' : 'No' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark small fw-bold">{{ $lead->created_at->format('d-m-Y') }}</div>
                            <div class="text-muted" style="font-size: 11px;">{{ $lead->created_at->format('h:i A') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-5 text-muted">No incomplete orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 d-flex justify-content-between align-items-center">
            <div class="text-muted small">Showing {{ $leads->firstItem() ?? 0 }} to {{ $leads->lastItem() ?? 0 }} of {{ $leads->total() }} leads</div>
            {{ $leads->links() }}
        </div>
    </div>
</div>

{{-- Edit Lead Modal --}}
<div class="modal fade" id="editLeadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Customer Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editLeadForm">
                    <input type="hidden" id="edit_lead_id">
                    <div class="mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="edit_name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="edit_phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" id="edit_address" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveLeadEdit()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Select All
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.lead-checkbox').forEach(cb => cb.checked = this.checked);
    });

    function markRecovered(id) {
        updateStatus(id, 'recovered');
    }

    function updateStatus(id, status) {
        fetch(`{{ url('admin/incomplete-orders') }}/${id}/status`, {
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
                Swal.fire('Success', data.message, 'success').then(() => location.reload());
            }
        });
    }

    function deleteLead(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('admin/incomplete-orders') }}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => location.reload());
            }
        });
    }

    // Bulk actions
    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.lead-checkbox:checked')).map(cb => cb.value);
    }

    function bulkAssign(staffId, staffName) {
        const ids = getSelectedIds();
        if (ids.length === 0) return Swal.fire('Error', 'Please select at least one lead', 'error');

        Swal.fire({
            title: `Assign to ${staffName}?`,
            text: `You are assigning ${ids.length} lead(s)`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Assign'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`{{ url('admin/incomplete-orders/bulk-assign') }}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ ids, staff_id: staffId })
                }).then(res => res.json()).then(data => {
                    if(data.success) Swal.fire('Success', data.message, 'success').then(() => location.reload());
                });
            }
        });
    }

    function bulkUpdateStatus() {
        const ids = getSelectedIds();
        if (ids.length === 0) return Swal.fire('Error', 'Please select at least one lead', 'error');

        Swal.fire({
            title: 'Change Status',
            input: 'select',
            inputOptions: {
                'incomplete': 'Incomplete',
                'contacted': 'Contacted',
                'pending': 'Pending',
                'processing': 'Processing',
                'shipped': 'Shipped',
                'delivered': 'Delivered',
                'recovered': 'Recovered',
                'cancelled': 'Cancelled'
            },
            showCancelButton: true
        }).then(result => {
            if (result.value) {
                fetch(`{{ url('admin/incomplete-orders/bulk-status') }}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ ids, status: result.value })
                }).then(res => res.json()).then(data => {
                    if(data.success) Swal.fire('Success', data.message, 'success').then(() => location.reload());
                });
            }
        });
    }

    function bulkDeleteLeads() {
        const ids = getSelectedIds();
        if (ids.length === 0) return Swal.fire('Error', 'Please select at least one lead', 'error');

        Swal.fire({
            title: 'Delete Selected?',
            text: "This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`{{ url('admin/incomplete-orders/bulk-delete') }}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ ids })
                }).then(res => res.json()).then(data => {
                    if(data.success) Swal.fire('Success', data.message, 'success').then(() => location.reload());
                });
            }
        });
    }

    function bulkSendSteadfast() {
        const ids = getSelectedIds();
        if (ids.length === 0) return Swal.fire('Error', 'Please select at least one lead', 'error');
        Swal.fire('Steadfast', 'Sending lead data to Steadfast API...', 'info');
        // Implement actual API call here
    }

    function bulkSendPathao() {
        const ids = getSelectedIds();
        if (ids.length === 0) return Swal.fire('Error', 'Please select at least one lead', 'error');
        Swal.fire('Pathao', 'Sending lead data to Pathao API...', 'info');
        // Implement actual API call here
    }

    function editLead(lead) {
        document.getElementById('edit_lead_id').value = lead.id;
        document.getElementById('edit_name').value = lead.name || '';
        document.getElementById('edit_phone').value = lead.phone || '';
        document.getElementById('edit_address').value = lead.address || '';
        new bootstrap.Modal(document.getElementById('editLeadModal')).show();
    }

    function saveLeadEdit() {
        const id = document.getElementById('edit_lead_id').value;
        const data = {
            name: document.getElementById('edit_name').value,
            phone: document.getElementById('edit_phone').value,
            address: document.getElementById('edit_address').value
        };

        fetch(`{{ url('admin/incomplete-orders') }}/${id}/update`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(data)
        }).then(res => res.json()).then(data => {
            if(data.success) {
                Swal.fire('Updated!', data.message, 'success').then(() => location.reload());
            }
        });
    }
</script>
@endsection
