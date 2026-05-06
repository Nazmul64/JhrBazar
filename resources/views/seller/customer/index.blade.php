@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">Customer Management</h4>
        <a href="{{ route('seller.customers.create') }}" class="btn btn-primary" style="background: #e7567c; border:none;">
            <i class="bi bi-plus-lg"></i> Add New Customer
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">SL</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Registered At</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $index => $customer)
                        <tr>
                            <td class="ps-4">{{ $customers->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                            </td>
                            <td>{{ $customer->user->phone ?? 'N/A' }}</td>
                            <td>{{ $customer->user->email ?? 'N/A' }}</td>
                            <td>{{ $customer->created_at->format('d M Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('seller.customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('seller.customers.destroy', $customer->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No customers found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($customers->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
