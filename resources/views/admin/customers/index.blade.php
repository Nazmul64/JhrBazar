@extends('admin.master')
@section('content')

<style>
    .page-card { background:#fff; border-radius:12px; padding:28px 32px; box-shadow:0 1px 8px rgba(0,0,0,.06); }
    .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
    .page-header h4 { font-size:1.25rem; font-weight:700; color:#1a1a1a; margin:0; }
    
    .status-badge { padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600; }
    .status-active { background:#e8f5e9; color:#2e7d32; }
    .status-blocked { background:#ffebee; color:#c62828; }
    
    .btn-action { padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; border:none; cursor:pointer; }
    .btn-block { background:#ffebee; color:#c62828; }
    .btn-unblock { background:#e8f5e9; color:#2e7d32; }
    
    .search-box { display:flex; gap:10px; margin-bottom:20px; }
    .search-input { padding:8px 15px; border:1px solid #ddd; border-radius:8px; width:250px; }
</style>

<div class="page-card">
    <div class="page-header">
        <h4>Customer Management (Advanced)</h4>
        <div class="d-flex gap-3">
            <span class="badge bg-success">Active: {{ $totalActive }}</span>
            <span class="badge bg-danger">Blocked: {{ $totalBlocked }}</span>
        </div>
    </div>

    <form action="{{ route('admin.customers.index') }}" method="GET" class="search-box">
        <input type="text" name="search" class="search-input" placeholder="Search name, phone, email..." value="{{ request('search') }}">
        <select name="status" class="search-input" style="width:150px;">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contact Info</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Fraud Check</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $customer->name }}</div>
                        <small class="text-muted">ID: #{{ $customer->id }}</small>
                    </td>
                    <td>
                        <div>{{ $customer->phone }}</div>
                        <div class="small text-muted">{{ $customer->email }}</div>
                        <div class="small text-muted">IP: {{ $customer->ip_address ?? 'N/A' }}</div>
                    </td>
                    <td>
                        @if($customer->is_blocked)
                            <span class="status-badge status-blocked">Blocked</span>
                        @else
                            <span class="status-badge status-active">Active</span>
                        @endif
                    </td>
                    <td>{{ $customer->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.fraud.index', ['search' => $customer->phone]) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-shield-exclamation"></i> Check Fraud
                        </a>
                    </td>
                    <td>
                        <form action="{{ route('admin.customers.toggle-block', $customer->id) }}" method="POST">
                            @csrf
                            @if($customer->is_blocked)
                                <button type="submit" class="btn-action btn-unblock">Unblock</button>
                            @else
                                <button type="submit" class="btn-action btn-block" onclick="return confirm('Are you sure you want to block this customer?')">Block</button>
                            @endif
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No customers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>

@endsection
