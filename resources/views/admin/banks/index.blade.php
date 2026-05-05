@extends('admin.master')
@section('content')

<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-bank2 me-2 text-primary"></i> Bank Management
        </h4>
        <button type="button" class="btn btn-primary px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#addBankModal">
            <i class="bi bi-plus-lg me-1"></i> Add New Bank
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Bank Name</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banks as $bank)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $bank->name }}</td>
                            <td>
                                <span class="badge bg-{{ $bank->status === 'active' ? 'success' : 'danger' }} rounded-pill px-3">
                                    {{ ucfirst($bank->status) }}
                                </span>
                            </td>
                            <td>{{ $bank->created_at->format('d M, Y') }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editBankModal{{ $bank->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.banks.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editBankModal{{ $bank->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form action="{{ route('admin.banks.update', $bank->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="fw-bold">Edit Bank</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body py-4 text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Bank Name</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $bank->name }}" required>
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label fw-bold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" {{ $bank->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $bank->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary px-4">Update Bank</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No banks found. Add some to show on registration page.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addBankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.banks.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="fw-bold">Add New Bank</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-0">
                            <label class="form-label fw-bold">Bank Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter bank name" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Save Bank</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
