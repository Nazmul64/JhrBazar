@extends('admin.master')

@section('title', 'Expense & Income Categories')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ── Header ── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-tags-fill text-primary"></i> Expense & Income Categories
            </h4>
            <p class="text-muted small mb-0">Organize and classify cashbook entries into dedicated purpose buckets.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Back to Accounts Ledger
            </a>
            <button type="button" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="bi bi-plus-circle"></i> New Category
            </button>
        </div>
    </div>

    {{-- ── Categories Table ── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Category Name</th>
                        <th>Applicable Type</th>
                        <th>Color Badge</th>
                        <th>Description</th>
                        <th class="text-center">Total Entries</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark">{{ $category->name }}</span>
                            </td>
                            <td>
                                @if($category->type === 'income')
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Income Only</span>
                                @elseif($category->type === 'expense')
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Expense Only</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1">Income & Expense</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-inline-block rounded-circle border" style="width: 20px; height: 20px; background-color: {{ $category->color ?? '#3b82f6' }};"></span>
                                    <code class="small text-muted">{{ $category->color ?? '#3b82f6' }}</code>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $category->description ?? '—' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2.5 py-1">
                                    {{ $category->accounts_ledgers_count ?? 0 }} entries
                                </span>
                            </td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success-subtle text-success py-1 px-2 rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary py-1 px-2 rounded-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-2.5 py-1 me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editCategoryModal{{ $category->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.accounts.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Edit Modal for each category --}}
                        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.accounts.categories.update', $category->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                                            <h5 class="modal-title fw-bold">Edit Category: {{ $category->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">Category Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control rounded-3" value="{{ $category->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">Category Type <span class="text-danger">*</span></label>
                                                <select name="type" class="form-select rounded-3" required>
                                                    <option value="expense" {{ $category->type === 'expense' ? 'selected' : '' }}>Expense</option>
                                                    <option value="income" {{ $category->type === 'income' ? 'selected' : '' }}>Income</option>
                                                    <option value="both" {{ $category->type === 'both' ? 'selected' : '' }}>Both (Income & Expense)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">Badge Color</label>
                                                <input type="color" name="color" class="form-control form-control-color rounded-3 w-100" value="{{ $category->color ?? '#3b82f6' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">Description</label>
                                                <textarea name="description" rows="2" class="form-control rounded-3">{{ $category->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                            <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-tags fs-1 d-block mb-2 text-secondary"></i>
                                No categories defined yet. Create your first category above!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── Add Category Modal ── --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.accounts.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i> Create Purpose Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Utility Bills, Store Rent, Online Sales" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Category Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="expense" selected>Expense (Outflow)</option>
                            <option value="income">Income (Inflow)</option>
                            <option value="both">Both (Income & Expense)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Badge Color</label>
                        <input type="color" name="color" class="form-control form-control-color rounded-3 w-100" value="#3b82f6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Description</label>
                        <textarea name="description" rows="2" class="form-control rounded-3" placeholder="Optional description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
